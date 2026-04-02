<?php
// Cargamos la libreria Dompdf
require_once 'dompdf/autoload.inc.php'; 
use Dompdf\Dompdf;
use Dompdf\Options;

include 'conexion.php';

// Ajuste de zona horaria para que la hora de generacion sea real
date_default_timezone_set('America/Mexico_City');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Consulta completa para traer todos los datos del reporte
    $stmt = $pdo->prepare("SELECT t.*, a.*, c.nombre_empresa, c.am_responsable FROM tickets t 
                           JOIN activos_contratos a ON t.id_activo = a.id_activo
                           JOIN clientes c ON a.id_cliente = c.id_cliente 
                           WHERE t.id_ticket = ?");
    $stmt->execute([$id]);
    $tk = $stmt->fetch();

    if (!$tk) { die("Ticket no encontrado"); }

    // VARIABLES DE TIEMPO CON PRECISION DE SEGUNDOS
    // 1. Momento en que se genera este documento PDF
    $tiempo_generacion_pdf = date('d/m/Y H:i:s');
    
    // 2. Momento en que se registro el ticket en el sistema
    $tiempo_registro_ticket = date('d/m/Y H:i:s', strtotime($tk['fecha_apertura']));
    
    // 3. Momento del cierre (si aplica)
    $fecha_cierre = ($tk['fecha_cierre']) ? date('d/m/Y H:i:s', strtotime($tk['fecha_cierre'])) : 'Pendiente';

    // Construccion del HTML profesional
    $html = '
    <html>
    <head>
    <style>
        body { font-family: "Helvetica", sans-serif; color: #222; font-size: 11px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px solid #0e2d5c; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0e2d5c; text-transform: uppercase; font-size: 18px; }
        .folio-box { color: #d9534f; font-size: 15px; font-weight: bold; margin-top: 5px; }
        
        .time-header { text-align: right; font-size: 9px; color: #666; margin-bottom: 10px; }
        
        .section-title { background: #0e2d5c; color: #fff; padding: 5px 10px; font-weight: bold; text-transform: uppercase; margin-top: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        td { padding: 7px; border: 1px solid #eee; vertical-align: top; }
        .label { font-weight: bold; color: #444; width: 30%; background: #f9f9f9; }
        .value { width: 70%; }
        
        .falla-box { border: 1px solid #eee; padding: 15px; background: #fafafa; min-height: 60px; margin-top: 5px; font-style: italic; }
        
        .footer-auth { margin-top: 30px; border: 2px solid #5cb85c; padding: 15px; background: #f9fff9; text-align: center; }
        .footer-auth h3 { margin: 0 0 5px 0; color: #449d44; text-transform: uppercase; font-size: 12px; }
        
        .watermark { position: fixed; bottom: 0; right: 0; color: #ddd; font-size: 9px; }
    </style>
    </head>
    <body>

    <div class="time-header">
        Documento generado el: ' . $tiempo_generacion_pdf . '
    </div>

    <div class="header">
        <h1>Reporte Tecnico de Servicio</h1>
        <div class="folio-box">Folio: ' . $tk['folio_ticket'] . '</div>
    </div>

    <div class="section-title">Informacion de Registro (Apertura)</div>
    <table>
        <tr>
            <td class="label">Ticket creado el:</td>
            <td class="value">' . $tiempo_registro_ticket . '</td>
        </tr>
        <tr>
            <td class="label">Reportado por (Cuenta):</td>
            <td class="value">' . $tk['reportado_por'] . '</td>
        </tr>
        <tr>
            <td class="label">Empresa / Cliente:</td>
            <td class="value">' . $tk['nombre_empresa'] . '</td>
        </tr>
        <tr>
            <td class="label">AM Responsable:</td>
            <td class="value">' . $tk['am_responsable'] . '</td>
        </tr>
    </table>

    <div class="section-title">Detalles del Activo</div>
    <table>
        <tr>
            <td class="label">Categoria:</td>
            <td class="value">' . ($tk['categoria'] ?: 'Sin categoria') . '</td>
        </tr>
        <tr>
            <td class="label">Tecnologia:</td>
            <td class="value">' . $tk['tecnologia'] . '</td>
        </tr>
        <tr>
            <td class="label">Numero de Serie:</td>
            <td class="value">' . $tk['numeros_serie'] . '</td>
        </tr>
        <tr>
            <td class="label">Smartnet / Contrato:</td>
            <td class="value">' . ($tk['smartnet'] ?: 'N/A') . ' / ' . ($tk['no_contrato'] ?: 'N/A') . '</td>
        </tr>
    </table>

    <div class="section-title">Descripcion de la Falla</div>
    <div class="falla-box">
        ' . nl2br(htmlspecialchars($tk['descripcion_falla'])) . '
    </div>';

    // Solo mostrar la autorizacion si el ticket esta cerrado
    if ($tk['estatus'] == 'Cerrado') {
        $html .= '
        <div class="footer-auth">
            <h3>Autorizacion de cierre del reporte</h3>
            <p>Se confirma la resolucion de la incidencia bajo la supervision del administrador.</p>
            <table style="border: none; margin-top: 10px;">
                <tr>
                    <td style="border: none; text-align: center;">
                        <strong>Autorizado por:</strong><br>
                        ' . $tk['aprobado_por'] . '
                    </td>
                    <td style="border: none; text-align: center;">
                        <strong>Fecha y Hora de Cierre:</strong><br>
                        ' . $fecha_cierre . '
                    </td>
                </tr>
            </table>
        </div>';
    } else {
        $html .= '
        <div style="margin-top: 40px; text-align: center; color: #888;">
            <p>ESTATUS ACTUAL DEL REPORTE: ' . strtoupper($tk['estatus']) . '</p>
            <p>___________________________________________________</p>
            <p>Firma de Conformidad del Cliente (Apertura)</p>
        </div>';
    }

    $html .= '
    <div class="watermark">Tecnologia en Movimiento - Generado de forma automatica por el Sistema de Tickets</div>
    </body>
    </html>';

    // Configuracion de Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Descarga automatica con el nombre del folio y segundos para evitar duplicados
    $dompdf->stream("Reporte_" . $tk['folio_ticket'] . ".pdf", array("Attachment" => true));
}
?>