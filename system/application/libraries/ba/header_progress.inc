<? 
		require_once(APPPATH . 'libraries/ba/table_noborder.inc');
		$pdf->SetTextColor(118, 0, 3);
	    
        if ($jns == 'PNN') {
            $jenis='Panen';
        } else if ($jns == 'RWT') {
             $jenis='Rawat';
        } else if ($jns == 'TP') {
             $jenis='Transport Panen';
        } else if ($jns == 'BBT') {
             $jenis='Bibitan';
        } else if ($jns == 'SSP') {
             $jenis='Sisip';
        }else if ($jns == 'TNM') {
             $jenis='Tanam';
        }else if ($jns == 'RWTIF') {
             $jenis='Rawat Infrastruktur';
        }else if ($jns == 'PJINF') {
             $jenis='Project Infrastruktur';
        }else if ($jns == 'PJBBT') {
             $jenis='Project Bibitan';
        }
		$columns = 5; //number of Columns
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
        
		for ($j=1; $j<=5; $j++)
		{
			$data = Array();
			$data[0]['TEXT'] = "";
			$data[1]['TEXT'] = "";
			$data[2]['TEXT'] = "";
				
			if ($j == 1)
			{
				
				$data[0]['TEXT'] = "							LAPORAN RINCIAN PEKERJAAN HARIAN - ". strtoupper($jenis);
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 10;
				$data[0]['T_TYPE'] = "B";
			}
			
			if ($j == 2){
				$data[0]['TEXT'] = "							NO :          / PRG / ". $company ."/ ". strtoupper(substr($tgl,4,2)) ." / " . substr($tgl,0,4) ;
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 10;
				$data[0]['T_TYPE'] = "B";
			}
			
			if ($j == 3){
				$data[0]['TEXT'] = "							PERIODE  : " .strtoupper(date("d-m-Y",strtotime($tgl)));
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 9;
				$data[0]['T_TYPE'] = "B";
				$data[0]['LN_SIZE'] = 5;	
			}
			
			if ($j == 4){
				$data[0]['TEXT'] = "PT.     " . strtoupper($company_name);
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 3;
				$data[0]['T_SIZE'] = 9;
				$data[0]['LN_SIZE'] = 4;
               
			}
	        if ($j == 5){
                $data[0]['TEXT'] = "AFD : " . strtoupper($afd);
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