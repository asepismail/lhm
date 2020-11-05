<?
require('html2fpdf.php');
$pdf=new HTML2FPDF();
$pdf->AddPage();
$fp = fopen("cetak_ref_t_user.html","r");
$strContent = fread($fp, filesize("cetak_ref_t_user.html"));
fclose($fp);
$pdf->WriteHTML($strContent);
$pdf->Output();
//echo "PDF file is generated successfully!";
?>