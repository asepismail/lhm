<? 
		require_once(APPPATH . 'libraries/slipgaji/table_noborder.inc');
		
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 44.5;
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);
	
		for ($j=1; $j<=6; $j++)
		{
			$data = Array();
			$data[0]['TEXT'] = "";
			$data[1]['TEXT'] = "";
			$data[2]['TEXT'] = "";
			$data[3]['TEXT'] = "";
		
			if ($j == 1){
				$data[0]['TEXT'] = "Gaji Diterima : ";
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
			}
			
			if ($j == 2){
				$data[0]['TEXT'] = "Terbilang : ";
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
			}
			
			if ($j == 3){
				if($company == "LIH") {
					$data[2]['TEXT'] = "KEMANG, ";
				} else if($company == "SAP"){
					$data[2]['TEXT'] = "";
				} else if($company == "TPAI") {
					$data[2]['TEXT'] = "";
				} else if($company == "SSS") {
					$data[2]['TEXT'] = "";
				}
				$data[2]['T_ALIGN'] = "C";
				$data[2]['COLSPAN'] = 2;
				$data[2]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
				$data[1]['BRD_TYPE'] = 0;
				$data[2]['BRD_TYPE'] = 0;
				$data[2]['LN_SIZE'] = 4;
				
			}
			
			if ($j == 4){
				$data[2]['TEXT'] = "TANDA TANGAN";
				$data[2]['T_ALIGN'] = "C";
				$data[2]['COLSPAN'] = 2;
				$data[2]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
				$data[1]['BRD_TYPE'] = 0;
				$data[2]['BRD_TYPE'] = 0;
				$data[2]['LN_SIZE'] = 4;
			}
	
			if ($j == 5){
				$data[0]['TEXT'] = "";
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['BRD_TYPE'] = 0;
				$data[0]['LN_SIZE'] = 10;	
			}
			
			if ($j == 6){
				$data[2]['TEXT'] = "( NAMA )";
				$data[2]['T_ALIGN'] = "C";
				$data[2]['COLSPAN'] = 2;
				$data[2]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
				$data[1]['BRD_TYPE'] = 0;
				$data[2]['BRD_TYPE'] = 0;
				$data[2]['LN_SIZE'] = 4;
			}
			
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	
?>