<?
        require_once(APPPATH . 'libraries/daftar_upah/authorize_ba2.inc');
        
        $columns = 5; //number of Columns
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
            $data[2]['TEXT'] = "";
            $data[3]['TEXT'] = "";
        
            if ($j == 1){
                $data[0]['TEXT'] = "Prepared by";
                $data[0]['COLSPAN'] = 2;
                $data[2]['TEXT'] = "Checked by";
                $data[2]['COLSPAN'] = 2;
				$data[4]['TEXT'] = "Approved By";
                $data[0]['T_SIZE'] = 8;
                $data[1]['T_SIZE'] = 8;
                $data[2]['T_SIZE'] = 8;
                $data[3]['T_SIZE'] = 8;
                $data[4]['T_SIZE'] = 8;
                $data[3]['T_ALIGN'] = "C";
                $data[3]['LN_SIZE'] = 4;
            }
            
            if ($j == 2){
                $data[3]['T_ALIGN'] = "L";
                $data[3]['T_SIZE'] = 8;
                $data[3]['LN_SIZE'] = 20;
            }
            
            if ($j == 3){
                $data[0]['TEXT'] = $qc;
                $data[1]['TEXT'] = $labor;
				$data[2]['TEXT'] = $mill_manager;
				$data[3]['TEXT'] = $ktu;
				$data[4]['TEXT'] = $administratur;
				
                $data[3]['T_ALIGN'] = "C";
                $data[0]['T_SIZE'] = 8;
                $data[1]['T_SIZE'] = 8;
                $data[2]['T_SIZE'] = 8;
                $data[3]['T_SIZE'] = 8;
				$data[4]['T_SIZE'] = 8;
                $data[3]['LN_SIZE'] = 4;
            }
            
            if ($j == 4){
                $data[0]['TEXT'] = "Quality Control";
				$data[1]['TEXT'] = "Ast. Labor";
				$data[2]['TEXT'] = "Mill Manager";
				$data[3]['TEXT'] = "KTU";
				$data[4]['TEXT'] = "Administratur";
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
