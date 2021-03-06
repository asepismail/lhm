<?php

	//Table Base Classs
	require_once("class.fpdf_table.php");
	
	//Class Extention for header and footer	
	require_once("header_footer.inc");
	
	$pdf = new pdf_usage();		
	$pdf->Open();
	$pdf->SetAutoPageBreak(true, 20);
    	$pdf->SetMargins(20, 20,20);
	$pdf->AddPage(L, A4);
	$pdf->AliasNbPages(); 
		
	$pdf->SetStyle("s1","arial","",12,"118,0,3");
	$pdf->SetStyle("s2","arial","",10,"0,49,159");
	
	//default text color
	$pdf->SetTextColor(118, 0, 3);
	//$pdf->SetX(60);
	$pdf->MultiCellTag(200, 5, "<s1>Edit List Journal - PT Saban Sawit Subur</s1>", 0);
	$pdf->Ln(1);
	$pdf->MultiCellTag(200, 5, "<s2>Periode :  January 2009</s2>", 0);
	$pdf->Ln(1);
	
	//load the table default definitions DEFAULT!!!
	require("GL_Journal_rptPDF_def.inc");
	$columns = 5; //number of Columns
	
	//Initialize the table class
	$pdf->tbInitialize($columns, true, true);
	
	//set the Table Type
	$pdf->tbSetTableType($table_default_table_type);
	$aSimpleHeader = array();
	
	//Table Header
	for($i=0; $i<$columns; $i++) {
		$aSimpleHeader[$i] = $table_default_header_type;
		$aSimpleHeader[$i]['TEXT'] = "Column " . ($i + 1) . " text";
	}
	$aSimpleHeader[0]['WIDTH'] = 40;
	$aSimpleHeader[1]['WIDTH'] = 50;
	$aSimpleHeader[2]['WIDTH'] = 60;
	$aSimpleHeader[3]['WIDTH'] = 70;
	$aSimpleHeader[4]['WIDTH'] = 30;
	$pdf->tbSetHeaderType($aSimpleHeader);
	
	//Draw the Header
	$pdf->tbDrawHeader();

	//Table Data Settings
	$aDataType = Array();
	for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	$pdf->tbSetDataType($aDataType);

	for ($j=1; $j<=100; $j++)
	{
		$data = Array();
		$data[0]['TEXT'] = "Line $j Text 1";
		$data[0]['T_ALIGN'] = "L";//default in the example is C
		$data[1]['TEXT'] = "Line $j Text 2";
		$data[2]['TEXT'] = "Line $j Text 3";
		$data[3]['TEXT'] = "Line $j Text 4";
		$data[4]['TEXT'] = "Line $j Text 5";
		$data[2]['T_ALIGN'] = "R";
		$pdf->tbDrawData($data);
	}
	
	//output the table data to the pdf
	$pdf->tbOuputData();
	//draw the Table Border
	$pdf->tbDrawBorder();

	$pdf->Output();

?>
