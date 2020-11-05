<?
		$bcolumns = 4; //number of Columns
		$pdf->tbInitialize($bcolumns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		$pdf->Ln(2.5);
		
		$bSimpleHeader = array(); 
		
		for($i=0; $i<=$bcolumns; $i++) {
			$bSimpleHeader[$i] = $table_default_header_type;
			$bSimpleHeader[0]['WIDTH'] = 48.9;
			$bSimpleHeader[1]['WIDTH'] = 45.9;
			$bSimpleHeader[2]['WIDTH'] = 41;
			$bSimpleHeader[3]['WIDTH'] = 43.9;
		}
		$pdf->tbSetHeaderType($bSimpleHeader);
		
		$bDataType = Array();
		for ($i=0; $i<$bcolumns; $i++) $bDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($bDataType);
	
		for ($j=0; $j<=3; $j++)
		{
			$bdata = Array();
			$bdata[0]['TEXT'] = "";
			$bdata[1]['TEXT'] = "";
			$bdata[2]['TEXT'] = "";
			$bdata[3]['TEXT'] = "";
		
			if ($j == 0){
				$bdata[0]['TEXT'] = "Budget";
				$bdata[0]['T_SIZE'] = 7;
				$bdata[0]['T_ALIGN'] = "C";
				$bdata[1]['TEXT'] = "Kode Aktivitas";
				$bdata[2]['T_SIZE'] = 7;
				$bdata[2]['T_ALIGN'] = "C";
				$bdata[1]['T_SIZE'] = 7;
				$bdata[1]['T_ALIGN'] = "L";
				$bdata[2]['TEXT'] = "Nilai Project";
				$bdata[2]['COLSPAN'] = 2;
				$bdata[2]['T_SIZE'] = 7;
				$bdata[2]['T_ALIGN'] = "C";
				$bdata[2]['LN_SIZE'] = 4;			
			}
			
			if ($j == 1){
				if($budget = 0 || $budget = " "){
					$bdata[0]['TEXT'] = "Belum Ada Budget";
				}  else {
					if($budget > $total){
						$bdata[0]['TEXT'] = "Dalam Budget";
					} else {
						$bdata[0]['TEXT'] = "Over Budget";
					}
				}
				$bdata[0]['T_ALIGN'] = "C";
				$bdata[0]['T_SIZE'] = 9;
				$bdata[0]['V_ALIGN'] = "M";
				$bdata[0]['ROWSPAN'] = 3;
				$bdata[1]['TEXT'] = $aktivitas;
				$bdata[1]['T_ALIGN'] = "C";
				$bdata[1]['T_SIZE'] = 12;
				$bdata[1]['LN_SIZE'] = 4;
				$bdata[1]['ROWSPAN'] = 3;
				$bdata[2]['T_ALIGN'] = "L";
				$bdata[2]['T_SIZE'] = 6;
				$bdata[2]['TEXT'] = "Nilai Budget :";
				$bdata[2]['T_ALIGN'] = "R";
				$bdata[3]['T_ALIGN'] = "L";
				$bdata[3]['T_SIZE'] = 6;
				$bdata[0]['LN_SIZE'] = 3.5;
				$bdata[1]['LN_SIZE'] = 3.5;
				$bdata[2]['LN_SIZE'] = 3.5;
				$bdata[3]['LN_SIZE'] = 3.5;
				$bdata[3]['T_ALIGN'] = "R";
				$bdata[3]['T_SIZE'] = 7;
				if($budget = 0 || $budget = " "){
					$bdata[3]['TEXT'] = "0,00";
				} else {
					$bdata[3]['TEXT'] = number_format( ($budget ),2,',','.') ;
				}
			}
			
			if ($j == 2){
				$bdata[0]['V_ALIGN'] = "B";
				$bdata[3]['T_ALIGN'] = "C";
				$bdata[0]['T_SIZE'] = 6;
				$bdata[1]['T_SIZE'] = 6;
				$bdata[2]['T_SIZE'] = 6;
				$bdata[2]['TEXT'] = "Nilai Project :";
				$bdata[2]['T_ALIGN'] = "R";
				$bdata[3]['T_SIZE'] = 6;
				$bdata[0]['LN_SIZE'] = 3.5;
				$bdata[1]['LN_SIZE'] = 3.5;
				$bdata[2]['LN_SIZE'] = 3.5;
				$bdata[3]['LN_SIZE'] = 3.5;
				$bdata[3]['T_ALIGN'] = "R";
				$bdata[3]['T_SIZE'] = 7;
				$bdata[3]['TEXT'] = number_format( ($total ),2,',','.') ;
			}
			
			if ($j == 3){
				$bdata[0]['V_ALIGN'] = "B";
				$bdata[3]['T_ALIGN'] = "C";
				$bdata[0]['T_SIZE'] = 6;
				$bdata[1]['T_SIZE'] = 6;
				$bdata[2]['T_SIZE'] = 6;
				$bdata[2]['TEXT'] = "Sisa :";
				$bdata[2]['T_ALIGN'] = "R";
				$bdata[3]['T_SIZE'] = 6;
				$bdata[0]['LN_SIZE'] = 3.5;
				$bdata[1]['LN_SIZE'] = 3.5;
				$bdata[2]['LN_SIZE'] = 3.5;
				$bdata[3]['LN_SIZE'] = 3.5;
				$bdata[3]['T_ALIGN'] = "R";
				$bdata[3]['T_SIZE'] = 7;
				if($budget = 0 || $budget = " "){
					$bdata[3]['TEXT'] = "0,00";
				} else {
					$bdata[3]['TEXT'] = number_format( ($budget - $total ),2,',','.') ;
				}
			}
			$pdf->tbDrawData($bdata);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	


?>