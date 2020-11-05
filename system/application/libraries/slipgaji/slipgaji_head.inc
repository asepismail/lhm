<? 
		require_once(APPPATH . 'libraries/slipgaji/table_noborder.inc');
		
		$pdf->SetTextColor(118, 0, 3);
		$pdf->MultiCellTag(205, 2, "<s1> ----------------------------------------------------------------------------------------------------------------------------------------------- </s1>", 0);


		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 50;
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
				$data[0]['TEXT'] = "PT.". strtoupper($company_name);
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 9;
			}
			
			if ($j == 2){
				$data[0]['TEXT'] = "SLIP GAJI";
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 11;
				$data[0]['T_TYPE'] = "B";
			}
			
			if ($j == 3){
				$data[0]['TEXT'] = "Bulan : " .$bulan;
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['LN_SIZE'] = 5;	
			}
			
			if ($j == 4){
				$data[0]['TEXT'] = "Jabatan : ";
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['LN_SIZE'] = 4;
			}
	
			if ($j == 5){
				$data[3]['TEXT'] = "Status : ";
				$data[3]['T_ALIGN'] = "L";
				$data[3]['T_SIZE'] = 10;
				$data[3]['LN_SIZE'] = 4;
			}
			
			if ($j == 6){
				$data[0]['TEXT'] = "Nama : ";
				$data[0]['T_ALIGN'] = "C";
				$data[0]['T_SIZE'] = 10;
				$data[0]['LN_SIZE'] = 4;
				
				$data[3]['TEXT'] = "NIK : ";
				$data[3]['T_ALIGN'] = "L";
				$data[3]['T_SIZE'] = 10;
				$data[3]['LN_SIZE'] = 4;
			}
			
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	
?>