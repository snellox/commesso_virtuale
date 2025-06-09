<?php
error_reporting(E_ERROR | E_PARSE | E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/tcpdf/tcpdf.php');
include_once './api/public/conn.php';

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);
if(!isset($data['order_id']) || !isset($data['negozio'])){
    die(false);
}

$sql = '
    SELECT `o`.*, `s`.`indirizzo`, `u`.`name`, `u`.`surname`, `u`.`email`
    FROM `order` `o` 
    INNER JOIN `shop` `s` ON `s`.`id` = '.$data['negozio'].' 
    INNER JOIN `users` `u` ON `u`.`id` = `o`.`id_cliente` 
    WHERE `o`.`id` = '.$data['order_id'];

$ds = $db->query($sql);
$order = $ds->fetch_assoc();

$sql = '
    SELECT `o`.`qta`, `d`.*  FROM `order_detail` `o` INNER JOIN `model` `d` ON `d`.`id` = `o`.`snowboard_id` WHERE `o`.`order_id` = '.$data['order_id'];
$ds = $db->query($sql);
$order_rows = [];
while($row = $ds->fetch_assoc()){
    $order_rows[] = $row;
}

$filename = 'odl_'.$order['surname'].'_'.date('d_m_Y', strtotime($order['data_consegna'])).'.pdf';
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('dejavusans', '', 12, '', true);
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);       // Colore del testo (nero)
$pdf->SetFont('dejavusans', '', 10);
//PAGINA 210mm - 20mm = 190 utilizzabiliß
$pdf->MultiCell(190, 20, '<img src="https://andrew02.it/assets/images/procreate-logo.png" width="50" height="50">', 0, 'L', 0, 0, '', '', true, 0, true, true, 10, 'M');
$pdf->Ln(27);
// HEADER
$pdf->SetFillColor(204, 204, 204);
$pdf->MultiCell(110, 10, 'CLIENTE', 1, 'C', 1, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(80, 10, 'ORDINE DI LAVORO', 1, 'C', 1, 1, '', '', true, 0, false, true, 10, 'M');
$pdf->setCellPadding( 5 );
$pdf->MultiCell(110, 30, '<b>'.$order['name'].' '.$order['surname'].'</b><br>'.$order['email'], 1, 'L', 0, 0, '', '', true, 0, true, true, 10, 'M');
$pdf->setCellPaddings( 0, 3, 0, 0 );
$pdf->MultiCell(80, 20, 'ARTICOLI IN FASE DI PRODUZIONE<br>CONSEGNA SUBORDINATA AL COMPLETAMENTO DEL CICLO PRODUTTIVO.', 1, 'C', 1, 1, '', '', true, 0, true, true, 20, 'M');
$pdf->SetX($pdf->GetX() + 110);
$pdf->MultiCell(80, 10, '<b>DATA</b> '.date('d-m-Y', strtotime($order['data_pagamento'])).' &nbsp;&nbsp;&nbsp;&nbsp;<b>N.</b> '.$order['id'], 1, 'C', 0, 1, '', '', true, 0, true, true, 10, 'M');

//DATI NEGOZIO
$pdf->setCellPaddings( 0,15, 5, 15);
$pdf->MultiCell(190, 40, '
    Spett.le<br><b>Sede produttiva</b><br>'.$order['indirizzo'], 0, 'R', 0, 1, '', '', true, 0, true);
$pdf->setCellPadding( 0);

//TESTA RIGHE DA PRODURRE
$pdf->setCellPaddings( 2, 3, 2,0);
$pdf->SetFillColor(204, 204, 204);
$pdf->MultiCell(120, 10, '<b>Prodotto</b>', 1, 'L', 1, 0, '', '', true, 0, true, true, 10, 'M');
$pdf->MultiCell(70, 10, '<b>Quantità</b>', 1, 'R', 1, 1, '', '', true, 0, true, true, 10, 'M');
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());

$pdf->SetFillColor(255, 255, 255);
$pdf->setCellPaddings( 2, 3, 2,0);
foreach($order_rows as $row){
    $pdf->MultiCell(120, 10, '<b>'.$row['name'].'</b> - '.$row['size'].'mm / '.$row['weight_min'].'-'.$row['weight_max'].'kg', 1, 'L', 0, 0, '', '', true, 0, true, true, 10, 'M');
    $pdf->MultiCell(70, 10, 'x <b>'.$row['qta'].'</b>', 1, 'R', 0, 1, '', '', true, 0, true, true, 10, 'M');
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());
}
$pdf->SetFillColor(204, 204, 204);
$pdf->setCellPaddings( 2, 3, 2,0);
$pdf->MultiCell(120, 10, 'CONSEGNA PREVISTA PER IL:', 1, 'L', 1, 0, '', '', true, 0, true, true, 10, 'M');
$pdf->MultiCell(70, 10, '<b>'.date('d-m-Y', strtotime($order['data_consegna'])).'</b>', 1, 'R', 1, 1, '', '', true, 0, true, true, 10, 'M');
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());

$pdf->Ln(20);
//FIRMA
$pdf->setCellPaddings( 2, 3, 2,0);
$pdf->MultiCell(150, 22, 'FIRMA DEL PRODUTTORE <br><br><hr>', 1, 'L', 0, 0, '', '', true, 0, true, true, 10, 'M');
$pdf->setCellPaddings( 3, 2, 0, 0);
$pdf->MultiCell(40, 22, '<b>PRODOTTI<br>DESTINATI<br>ALLA<br>VENDITA</b>', 1, 'L', 0, 1, '', '', true, 0, true, true, 10, 'M');

$pdf->Output($_SERVER['DOCUMENT_ROOT'].'export/'.$filename, 'F');
die(json_encode(['url'=>$filename]));