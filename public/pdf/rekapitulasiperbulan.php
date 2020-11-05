<?
require('html2fpdf.php');
$pdf=new HTML2FPDF();
$pdf->HTML2FPDF('l','mm','A4');
$pdf->AddPage();
$fp = fopen("cetak_rekapitulasiperbulan.html","r");
$strContent = fread($fp, filesize("cetak_rekapitulasiperbulan.html"));
fclose($fp);
$pdf->WriteHTML($strContent);
$pdf->Output();
//echo "PDF file is generated successfully!";
?>