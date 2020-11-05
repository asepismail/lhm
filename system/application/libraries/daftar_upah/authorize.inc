<?
		require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
		
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 40;
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);
	
		for ($j=1; $j<=3; $j++)
		{
			$data = Array();
			$data[0]['TEXT'] = "";
			$data[1]['TEXT'] = "";
			$data[2]['TEXT'] = "";
			$data[3]['TEXT'] = "";
		
			if ($j == 1){
				$data[0]['TEXT'] = "Dibuat";
				$data[1]['TEXT'] = "Diperiksa";
				$data[2]['TEXT'] = "Diperiksa";
				$data[3]['TEXT'] = "Disetujui";
				$data[0]['T_SIZE'] = 8;
				$data[1]['T_SIZE'] = 8;
				$data[2]['T_SIZE'] = 8;
				$data[3]['T_SIZE'] = 8;
				$data[3]['T_ALIGN'] = "C";
				$data[3]['LN_SIZE'] = 4;
			}
			
			if ($j == 2){
				$data[3]['T_ALIGN'] = "L";
				$data[3]['T_SIZE'] = 8;
				$data[3]['LN_SIZE'] = 20;
			}
			
			if ($j == 3){
				$data[0]['TEXT'] = "Ka. AFD / Divisi";
				$data[1]['TEXT'] = "Kabun / Katek / Kapab / KTU";
				if ( $company == 'LIH' ) {
					$data[2]['TEXT'] = "Iskandar";
					$data[3]['TEXT'] = "Mohammad Rosyid";
				
				} else if ( $company == 'MAG' ) {
					$data[2]['TEXT'] = "R. Wahyu";
					$data[3]['TEXT'] = "I Nyoman W.";
				
				} else if ( $company == 'SSS' ) {
					$data[2]['TEXT'] = "Sholihin";
					$data[3]['TEXT'] = "Sugeng Joni S.";
				
				} else if ( $company == 'SAP' ) {
					$data[2]['TEXT'] = "M. Syawal";
					$data[3]['TEXT'] = "Wawan Sudarwanto";
				
				} else if ( $company == 'MSS' ) {
					$data[2]['TEXT'] = "Lano S.";
					$data[3]['TEXT'] = "Sugianto";
				
				} else if ( $company == 'TPAI' ) {
					$data[2]['TEXT'] = "M. Nurjantoro";
					$data[3]['TEXT'] = "David Ari Sandri";
				
				} else {
					$data[2]['TEXT'] = "KTU";
					$data[3]['TEXT'] = "Administratur";
				}
				$data[3]['T_ALIGN'] = "C";
				$data[0]['T_SIZE'] = 8;
				$data[1]['T_SIZE'] = 8;
				$data[2]['T_SIZE'] = 8;
				$data[3]['T_SIZE'] = 8;
				$data[3]['LN_SIZE'] = 4;
			}
			
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	


?>