<? 
		require_once(APPPATH . 'libraries/ba/table_noborder.inc');
		$pdf->SetTextColor(118, 0, 3);
	
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 82;
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
				
			if ($j == 1)
			{
				
				$data[0]['TEXT'] = "							BERITA ACARA PEMBAYARAN PROGRESS KONTRAKTOR";
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 10;
				$data[0]['T_TYPE'] = "B";
			}
			
			if ($j == 2){
				$data[0]['TEXT'] = "							NO :          / ". $company ." / KONTRAKTOR / ". strtoupper($bulanr) ." / " . $tahun ;
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 10;
				$data[0]['T_TYPE'] = "B";
			}
			
			if ($j == 3){
				$data[0]['TEXT'] = "							PERIODE  : " .substr($from,6,2)."-". substr($from,4,2)."-". substr($from,0,4) . "  s/d  " . substr($to,6,2)."/". substr($to,4,2)."-". substr($to,0,4);
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 9;
				$data[0]['T_TYPE'] = "B";
				$data[0]['LN_SIZE'] = 5;	
			}
			
			if ($j == 4){
				$data[0]['TEXT'] = "PERUSAHAAN   :   PT. " . strtoupper($this->session->userdata('DCOMPANY_NAME'));
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 9;
				$data[0]['LN_SIZE'] = 4;
			}
			
			if ($j == 5){
				$data[0]['TEXT'] = "KONTRAKTOR   :   " . strtoupper($nmkontraktor);
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 9;
				$data[0]['LN_SIZE'] = 4;
			}
			
			if ($j == 6){
				$data[0]['TEXT'] = "NO SPK              : ";
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 9;
				$data[0]['LN_SIZE'] = 4;
			}
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	
?>