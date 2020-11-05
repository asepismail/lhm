<?
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		$pdf->Ln(2.5);	
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 44.9;
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);
	
		for ($j=0; $j<=2; $j++)
		{
			$data = Array();
			$data[0]['TEXT'] = "";
			$data[1]['TEXT'] = "";
			$data[2]['TEXT'] = "";
			$data[3]['TEXT'] = "";
		
			if ($j == 0){
				$data[0]['TEXT'] = "Disiapkan oleh : ";
				$data[0]['T_SIZE'] = 7;
				$data[0]['T_ALIGN'] = "C";
				$data[0]['LN_SIZE'] = 3;
				$data[1]['TEXT'] = "Diperiksa oleh : ";
				$data[1]['COLSPAN'] = 2;
				$data[1]['T_SIZE'] = 7;
				$data[1]['T_ALIGN'] = "C";	
				$data[3]['TEXT'] = "Disetujui oleh : ";
				$data[3]['T_SIZE'] = 7;
				$data[3]['T_ALIGN'] = "C";				
			}
			
			if ($j == 1){
				$data[0]['T_ALIGN'] = "L";
				$data[0]['V_ALIGN'] = "B";
				$data[0]['LN_SIZE'] = 10;
				$data[1]['T_ALIGN'] = "L";
				$data[1]['T_SIZE'] = 6;
				$data[1]['LN_SIZE'] = 10;
				$data[2]['T_ALIGN'] = "L";
				$data[2]['T_SIZE'] = 6;
				$data[2]['LN_SIZE'] = 12;
				$data[3]['T_ALIGN'] = "L";
				$data[3]['T_SIZE'] = 6;
			}
			
			if ($j == 2){
				$data[0]['V_ALIGN'] = "B";
				$data[0]['TEXT'] = "[Nama] - Ka. [Bidang]";
				$data[1]['TEXT'] = "[Nama] - KTU";
				$data[2]['TEXT'] = "[Nama] - ADM / Kapro";
				$data[3]['TEXT'] = "[Nama] - Direktur Area";
				$data[3]['T_ALIGN'] = "C";
				$data[0]['T_SIZE'] = 6;
				$data[1]['T_SIZE'] = 6;
				$data[2]['T_SIZE'] = 6;
				$data[3]['T_SIZE'] = 6;
				$data[0]['LN_SIZE'] = 3.5;
				$data[1]['LN_SIZE'] = 3.5;
				$data[2]['LN_SIZE'] = 3.5;
				$data[3]['LN_SIZE'] = 3.5;
			}
						
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	


?>