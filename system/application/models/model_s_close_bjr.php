<?php
class model_s_close_bjr extends Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    function close_bjr($periode, $periode_to,$jns_transaksi ,$company){
        $periode=trim($this->db->escape_str($periode));
        $periode_to=trim($this->db->escape_str($periode_to));
        $company=trim($this->db->escape_str($company));
        $jns_transaksi=trim($this->db->escape_str($jns_transaksi));
        $status=FALSE; 

        $whBeetween='TANGGAL BETWEEN "'. date('Y-m-d', strtotime($periode)). '" and "'. date('Y-m-d', strtotime($periode_to)).'"';
		/*
        $cek_data_exist = $this->cek_data_exist('s_nota_angkutbuah',array('TANGGAL >='=>$periode,'TANGGAL <='=>$periode_to,'COMPANY_CODE'=>$company),'ID_NT_AB');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        */

        if(empty($status) || $status===FALSE){
            if (strtoupper($jns_transaksi) == 'CLS'){ // untuk close BJR
                $this->db->where('COMPANY_CODE',$company);
                $this->db->where($whBeetween);
                $set = array('CLOSING_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                        'CLOSING_DATE' =>  $this->global_func->gen_datetime(),
                        'CLOSING_STATUS'=>0
                        );
                $this->db->set($set);
                $this->db->update('s_nota_angkutbuah') ;
                if($this->db->trans_status() === FALSE){
                    $status = $this->db->_error_message();//"Error in Transactions!!";
                }else{
                    $qSP ="CALL sp_tbg_close_bjr(?, ?, ?)";
                    $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to));
                    
                    $status="Closing Berhasil"."\n";   
                } 
			//SYNC Added By Asep, 20130528
			}elseif(strtoupper($jns_transaksi) == 'SYNC'){				
				if($company == 'GKM' || $company == 'SML'){ 
                    $this->sync_data($company,$periode,$periode_to);                            
                    $status="Synchronize Berhasil"."\n";
                }	
            }elseif(strtoupper($jns_transaksi) == 'GEN'){ // untuk generate BJR
				//Remarked By Asep, 20130528
				/*
                if($company == 'GKM' || $company == 'SML'){
                    $this->sync_data($company,$periode,$periode_to);
					//todo: asep
					$opt=1; //Added by Asep, 20130506
                    $qSP ="CALL sp_tbg_calc_timbangan_auto(?, ?, ?, ?)";					
                    //$sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$opt));                            
                    $status="Update Data ID Berhasil lho"."\n";
                }else{
                    $qSP ="CALL sp_tbg_calc_timbangan_auto(?, ?, ?, ?)";
					$opt=1; //Added by Asep, 20130506
                    $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$opt));
                        
                        $status="Update Data ID Berhasil"."\n";
                }  */
				
				//Added By Asep: 20130528
				$qSP ="CALL sp_tbg_calc_timbangan_auto(?, ?, ?, ?)";
				$opt=1; //Added by Asep, 20130506
                $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$opt));        
                $status="Generate Berhasil"."\n";
                          
            }else{
                $status="Transaction Unknown!";    
            }
            
        }
        return $status;
    }
    
    function cek_data_exist($tableName,$where_condition,$select_condition){
        $this->db->select($select_condition);
        $this->db->from($tableName);
        $this->db->where($where_condition);
        
        $sQuery = $this->db->get();
        $count = $sQuery->num_rows();
           
        return $count;
    }
    
    function sync_data($company, $periode, $periode_to){
        $status=FALSE;
        $company = trim($this->db->escape_str($company));
		//Remarked by Asep, 20130524
        /*
        $config['hostname'] = 'localhost';
        $config['username'] = "root";
        $config['password'] = 'app5224878';
        $config['database'] = "lhm_online_gkm";
        $config['dbdriver'] = "mysqli";
        $config['dbprefix'] = "";
        $config['pconnect'] = TRUE;
        $config['db_debug'] = FALSE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";
        */
		
        try{
           //$mysql_wb=$this->load->database($config, TRUE); 
		   $dbgkm = $this->load->database('lhm_gkm', TRUE); 
           $queries = "SELECT * FROM m_gang_activity_detail WHERE ACTIVITY_CODE ='8601003' and 
                LHM_DATE BETWEEN '". $periode. "' and '". $periode_to."'
                and COMPANY_CODE='". $company ."'";			
		   $query=$dbgkm->query($queries);
           //$query = $this->db->query($queries);
           $count = $query->num_rows();
		   
           if ($count > 0){	
		   
			   $this->db->where("LHM_DATE BETWEEN '". $periode . "' AND '" . $periode_to . "'");
			   //$this->db->where('sell_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
			   $this->db->where('ACTIVITY_CODE','8601003');
			   //$this->db->where('age BETWEEN ' . $age1 . ' AND ' . $age2);
			   $this->db->where('COMPANY_CODE',$company);
			   $this->db->delete('dummy_mgangactivitydetail_gkm');
			   
               //$this->db->truncate('dummy_mgangactivitydetail_gkm'); 
               
               //$objects = $mysql_wb->query($queries,FALSE)->result();
			   //$objects = $mysql_wb->query($queries);
			   //$count = $objects->num_rows();
			   
               foreach($query->result() as $obj){
                    $this->db->insert('dummy_mgangactivitydetail_gkm',$obj);                  
               }  
               $status="Temp mgang has moved";   
           } 
           
  
           $queries = "SELECT * FROM p_progress WHERE ACTIVITY_CODE='8601003' and 
                TGL_PROGRESS BETWEEN '". $periode. "' and '". $periode_to."'";
           //$query = $this->db->query($queries);

		   $query=$dbgkm->query($queries);
           $count = $query->num_rows();
           
           if ($count > 0){
/*
			   $this->db->where("TGL_PROGRESS '". $periode . "' AND '" . $periode_to . "'");
			   $this->db->where('ACTIVITY_CODE','8601003');
			   $this->db->where('COMPANY_CODE',$company);
			   $this->db->delete('dummy_pprogress_gkm');
*/
$sql_delete = "DELETE FROM dummy_pprogress_gkm WHERE ACTIVITY_CODE='8601003' and 
                TGL_PROGRESS BETWEEN '". $periode. "' and '". $periode_to."' AND COMPANY_CODE='". $company."'";
				$this->db->query($sql_delete);
               
               //$objects = $mysql_wb->query($queries,FALSE)->result();
               //foreach($objects as $obj){
				foreach($query->result() as $obj){	   
                    $this->db->insert('dummy_pprogress_gkm',$obj);                  
               }  
               $status="Temp pprogres has moved";   
           }       
        }catch(Exception $e){
            $status = $e->getMessage();  
        }
        //$mysql_wb->close();
        return $status; 
       
    }
}
?>