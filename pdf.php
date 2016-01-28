<?php
// Export to PDF using mPDF
require './vendor/autoload.php';
require 'config.php';

$data = htmlspecialchars_decode($_REQUEST['data']);

$stylesheet = file_get_contents('lib/bootstrap.min.css');

$fname = 'pdf/espa_' . rand() . '.pdf';
$footer = "$dnsiStrShort";

// mPDF initialization & pdf creation
$mpdf = new mPDF();

$mpdf->SetFooter($footer);
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($data,2);

$mpdf->Output($fname);

// return file link
echo "<a href='$fname' target='_blank'><strong>Λήψη αρχείου</strong></a>";
?>
