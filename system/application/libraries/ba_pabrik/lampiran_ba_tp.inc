<?
        require_once(APPPATH . 'libraries/ba_pabrik/lampiran_ba_tp.inc');
        
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
                $data[0]['TEXT'] = "Diterima";
                $data[1]['TEXT'] = "Diketahui";
                $data[3]['TEXT'] = "Diperiksa";
                $data[4]['TEXT'] = "Dibuat";
                $data[0]['T_SIZE'] = 8;
                $data[1]['T_SIZE'] = 8;
                $data[1]['COLSPAN'] = 2;
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
            
            if ($j==3){
                if ( $company == 'LIH' ) {
                    $data[0]['TEXT'] = $nama_kontraktor;
                    $data[1]['TEXT'] = "Supriyambono";
                    $data[2]['TEXT'] = "M. Rosyid";
                    $data[4]['TEXT'] = "Antoni Syatibi";
                
                } else if ( $company == 'MIA' ) {
                    $data[0]['TEXT'] = $nama_kontraktor;
                    $data[1]['TEXT'] = "R. Wahyu";
                    $data[2]['TEXT'] = "Farid Kurniawan";
                    $data[4]['TEXT'] = "Yusman Yusuf";
                
                } else if ( $company == 'GKM' ) {
                    $data[0]['TEXT'] = $nama_kontraktor;
                    $data[1]['TEXT'] = "Permadi W.";
                    $data[2]['TEXT'] = "Vidarta R.";
                
                } else {
                    $data[0]['TEXT'] = $nama_kontraktor;
                    $data[1]['TEXT'] = "KTU";
                    $data[2]['TEXT'] = "Administratur";
                }
            }
            
            if ($j == 4){
                $data[0]['TEXT'] = "Ka.Kerja";
                $data[1]['TEXT'] = "KTU";
                $data[2]['TEXT'] = "Ka. Kebun";
                $data[3]['TEXT'] = "Ka. AFD";
                $data[4]['TEXT'] = "Mandor Transport";
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
