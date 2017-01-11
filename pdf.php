<?php
// Export to PDF using mPDF
require_once './vendor/autoload.php';
require_once 'config.php';
require_once 'functions.php';

$data = htmlspecialchars_decode($_REQUEST['data']);
$afm = $_REQUEST['afm'];

$stylesheet = file_get_contents('vendor/twbs/bootstrap/dist/css/bootstrap.min.css');

$fname = 'pdf/es_' . $afm . '_' . rand() . '.pdf';
$footer = "$dnsiStrShort";

// mPDF initialization & pdf creation
$mpdf = new mPDF();

$mpdf->SetFooter($footer);
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($data,2);

$mpdf->Output($fname);

//log to file
if ($canLog)
  logToFile($afm, $logFile, 1);

// echo (return) file link
echo "<a href='$fname' target='_blank'><strong>Λήψη αρχείου</strong></a>";
?>
