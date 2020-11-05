<?
		require_once(APPPATH . 'libraries/daftar_upah/authorize_prg.inc');
		
		$columns = 2; //number of Columns
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
	
		for ($j=1; $j<=4; $j++)
        {
            $data = Array();
            $data[0]['TEXT'] = "";
            $data[1]['TEXT'] = "";

        
            if ($j == 1){
                $data[0]['TEXT'] = "Dibuat";
                $data[1]['TEXT'] = "Diperiksa / Disetujui";
                
                $data[0]['T_SIZE'] = 10;
                $data[1]['T_SIZE'] = 10;
                $data[0]['LN_SIZE'] = 5;
                $data[1]['LN_SIZE'] = 5;
            }
            if ($j == 2)
            {
                $data[0]['ROWSPAN'] = 3;
                $data[1]['ROWSPAN'] = 3;
                $data[0]['LN_SIZE'] = 20;
                $data[1]['LN_SIZE'] = 20;
            }
            $pdf->tbDrawData($data);
        }
		
		$pdf->tbOuputData();
		// end tabel judul	
?>