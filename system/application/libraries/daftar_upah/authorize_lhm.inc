<?
        require_once(APPPATH . 'libraries/daftar_upah/authorize_lhm.inc');
        
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
    
        for ($j=1; $j<=4; $j++)
        {
            $data = Array();
            $data[0]['TEXT'] = "";
            $data[1]['TEXT'] = "";
            $data[2]['TEXT'] = "";
            $data[3]['TEXT'] = "";
        
            if ($j == 1){
                $data[0]['TEXT'] = "Disetujui ";
                $data[1]['TEXT'] = "Diperiksa";
                $data[2]['TEXT'] = "Dibuat";
                $data[3]['TEXT'] = "";
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
                $data[0]['TEXT'] = "";
                $data[1]['TEXT'] = "";
                $data[3]['T_ALIGN'] = "C";
                $data[0]['T_SIZE'] = 8;
                $data[1]['T_SIZE'] = 8;
                $data[2]['T_SIZE'] = 8;
                $data[3]['T_SIZE'] = 8;
                $data[3]['LN_SIZE'] = 4;
            }
            
            if($j==4){
                if ( $company == 'LIH' ) {
                    $data[0]['TEXT'] = "";
                    $data[1]['TEXT'] = "";
                
                } 
            }
            
            $pdf->tbDrawData($data);
        }
        
        $pdf->tbOuputData();
        // end tabel judul    


?>
