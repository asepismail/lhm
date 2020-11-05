<?
require('html2fpdf.php');
$pdf=new HTML2FPDF();
$pdf->AddPage();
$fp = fopen("cetak_ref_t_so.html","r");
$strContent = fread($fp, filesize("cetak_ref_t_so.html"));
fclose($fp);
$pdf->WriteHTML($strContent);
$pdf->Output();
//echo "PDF file is generated successfully!";
?>