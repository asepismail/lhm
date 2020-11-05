<?php

/**
 * Class extention for Header and Footer Definitions
 *
 */
class pdf_usage extends fpdf_table
{
	
	public function Header()
	{
		$this->SetStyle("head1","arial","",7,"160,160,160");
		$this->SetStyle("head2","arial","",7,"0,119,220");
		
	    $this->SetY(7);
	    
	    $this->MultiCellTag(100, 3, "<head1>PT. Provident Agro - Plantation System</head1>");
	    
	    $this->SetY($this->tMargin);
	}	
	
	public function Footer()
	{
	    $this->SetY(-5);
	    $this->SetFont('Arial','I',7);
	    $this->SetTextColor(170, 170, 170);
	    $this->MultiCell(0, 4, "Halaman {$this->PageNo()} / {nb}", 0, 'C');
	}
} 

?>
