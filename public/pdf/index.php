<?
require('html2fpdf.php');
$pdf=new HTML2FPDF();
$pdf->AddPage();
$fp = fopen("cetak_kinerjaperbulan.html","r");
$strContent = fread($fp, filesize("cetak_kinerjaperbulan.html"));
fclose($fp);
$pdf->WriteHTML($strContent);
$pdf->Output();
//echo "PDF file is generated successfully!";
?>