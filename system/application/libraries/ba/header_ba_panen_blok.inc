<? 
		require_once(APPPATH . 'libraries/ba/table_noborder.inc');
		
		$pdf->SetTextColor(118, 0, 3);
	
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 110;
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);
	
		for ($j=1; $j<=4; $j++)
		{
			$data = Array();
			$data[0]['TEXT'] = "";
			$data[1]['TEXT'] = "";
			$data[2]['TEXT'] = "";
				
			if ($j == 1){
				$data[0]['TEXT'] = "PT. " . strtoupper($company_name);;
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 11;
				$data[0]['LN_SIZE'] = 4;
			}
			
			if ($j == 2){
				$data[0]['TEXT'] = "REALISASI KERJA PANEN";
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 11;
				$data[0]['LN_SIZE'] = 4;
			}
			if ($j == 3){
				$data[0]['TEXT'] = "PERIODE   : " . strtoupper($bulan)  ."  ". $tahun;
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 11;
			}
			if ($j == 4){
				$data[0]['TEXT'] = "AFD            : " . strtoupper($afd);
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 11;
			}
			
				
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	
?>