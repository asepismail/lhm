<?php
class mcrontodo extends Model{
	
	function __construct(){
        parent::__construct();
        $this->load->database();   
    }
	
	function synchronize($company, $periode_to){
		$db_weight = $this->load->database($company, TRUE); 
		$queries ="SELECT * FROM  s_data_timbangan t
				WHERE t.COMPANY_CODE ='". $company ."' 
				AND t.TANGGALM  BETWEEN '". $periode_to. "' and '". $periode_to."'
				AND SINKRON_STATUS=0 AND JENIS_MUATAN='TBS' AND TYPE_BUAH in (1,2,3)
				AND t.TANGGALK <>'18991230' AND t.BERAT_BERSIH>0 AND t.ACTIVE=1";
				
		$query=$db_weight->query($queries);
		$idcount=0;
		$id=$this->generateID($company, $periode_to);
		$idcount=$id;
		
		$count = $query->num_rows();
		if ($count > 0){
			foreach($query->result() as $key => $val){				
				if ($id==NULL){
					$id= $company."TB".date("Ym",strtotime($periode_to))."00001"; //sample: MAGTB20130600001
					$idcount=1;
				}else{
					$idcount = $idcount+1;
					$id = $company."TB".date("Ym",strtotime($periode_to)).str_pad($idcount, 5, "0", STR_PAD_LEFT);
				}
				//$row = $query->row_array();
				$bolean_ticket=$this->check_ticket($val->NO_TIKET, $company);			
				if ($bolean_ticket==FALSE){
					$sInsert = "INSERT INTO s_data_timbangan 
								(ID_TIMBANGAN, NO_TIKET, NO_SPB,
								 TANGGALM, TANGGALK, WAKTUM,
								 WAKTUK, NO_KENDARAAN, JENIS_MUATAN,
								 BERAT_ISI, BERAT_KOSONG, BERAT_BERSIH,
								 COMPANY_CODE, INPUT_BY, INPUT_DATE,
								 TYPE_BUAH, TYPE_TIMBANG, NOTE,
								 TYPE_KENDARAAN, DRIVER_NAME, GRD_BUAHMENTAH,
								 GRD_BUAHBUSUK, GRD_BUAHKECIL, GRD_TANGKAIPANJANG, 
								 GRD_BRONDOLAN, GRD_TANDANKOSONG, GRD_LAINNYA, SINKRON_STATUS,
								 AFD, SUPPLIERCODE, JJG, ACTIVE, PO_NUMBER, KODE_KONTRAKTOR, BERAT_GRADING
								 ) 
						VALUES('".$id."','".$val->NO_TIKET."','".$val->NO_SPB."',
							   '".$val->TANGGALM."','".$val->TANGGALK."','".$val->WAKTUM."',
							   '".$val->WAKTUK."','".$val->NO_KENDARAAN."','".$val->JENIS_MUATAN."',
							   ".$val->BERAT_ISI.",".$val->BERAT_KOSONG.",".$val->BERAT_BERSIH.",
							   '".$val->COMPANY_CODE."','".$val->INPUT_BY."','".$val->INPUT_DATE."',
							   '".$val->TYPE_BUAH."','".$val->TYPE_TIMBANG."','".$val->NOTE."',
							   '".$val->TYPE_KENDARAAN."','".$val->DRIVER_NAME."','".$val->GRD_BUAHMENTAH."',
							   '".$val->GRD_BUAHBUSUK."','".$val->GRD_BUAHKECIL."','".$val->GRD_TANGKAIPANJANG."',
							   '".$val->GRD_BRONDOLAN."','".$val->GRD_TANDANKOSONG."','".$val->GRD_LAINNYA."',1,
							   '".$val->AFD."','".$val->SUPPLIERCODE."','".$val->JJG."',
							   '".$val->ACTIVE."','".$val->PO_NUMBER."','".$val->KODE_KONTRAKTOR."', (($val->BERAT_ISI-$val->BERAT_KOSONG)-$val->BERAT_BERSIH) 
							  )";				
					var_dump($sInsert);	
					$boolean_insert = $this->db->query($sInsert);					
					if ($boolean_insert==true){
						$this->update_timbangan($val->NO_TIKET, $company);
					}
				}else{
					$sUpdate ="UPDATE s_data_timbangan SET SINKRON_STATUS=1 WHERE NO_TIKET='". $val->NO_TIKET ."'";	
					$db_weight->query($sUpdate);
				}//end if
			}//end looping			
		}	
    }
	//end: function synchronize
	
	function synchronize_group($company, $periode_to){		
		$db_weight = $this->load->database($company, TRUE); 
		$queries ="SELECT * FROM  s_data_timbangan t
				WHERE t.COMPANY_CODE ='". $company ."' 
				AND t.TANGGALM  BETWEEN '". $periode_to. "' and '". $periode_to."'
				AND SINKRON_STATUS=0 AND JENIS_MUATAN='TBS' AND TYPE_BUAH = 4
				AND t.TANGGALK <>'18991230' AND t.BERAT_BERSIH>0 AND t.ACTIVE=1";
		
		$query=$db_weight->query($queries);
		$idcount=0;	
		
		$count = $query->num_rows();
		if ($count > 0){
			foreach($query->result() as $key => $val){				
				$bolean_ticket = $this->check_ticket($val->NO_TIKET, $company); 				
				$supplier='';
				$supplier=$this->get_supplier($val->SUPPLIERCODE);
				
				$id=$this->generateID($supplier, $periode_to);
				
				$idcount=$id;
				if ($bolean_ticket==false){				
					if ($id==NULL){
						$id= $supplier."TB".date("Ym",strtotime($periode_to))."00001"; //sample: MAGTB20130600001
						$idcount=1;
					}else{
						$idcount = $idcount+1;
						$id = $supplier."TB".date("Ym",strtotime($periode_to)).str_pad($idcount, 5, "0", STR_PAD_LEFT);
					}
					$sInsert = "INSERT INTO s_data_timbangan
								(ID_TIMBANGAN, NO_TIKET, NO_SPB,
								TANGGALM, TANGGALK, WAKTUM,
								WAKTUK, NO_KENDARAAN, JENIS_MUATAN,
								BERAT_ISI, BERAT_KOSONG, BERAT_BERSIH,
								COMPANY_CODE, INPUT_BY, INPUT_DATE,
								TYPE_BUAH, TYPE_TIMBANG, NOTE,
								TYPE_KENDARAAN, DRIVER_NAME, GRD_BUAHMENTAH,
								GRD_BUAHBUSUK, GRD_BUAHKECIL, GRD_TANGKAIPANJANG, 
								GRD_BRONDOLAN, GRD_TANDANKOSONG, GRD_LAINNYA, SINKRON_STATUS,
								AFD, SUPPLIERCODE, JJG, ACTIVE, PO_NUMBER, KODE_KONTRAKTOR
								) 
								VALUES('".$id."','".$val->NO_TIKET."','".$val->NO_SPB."',
								'".$val->TANGGALM."','".$val->TANGGALK."','".$val->WAKTUM."',
								'".$val->WAKTUK."','".$val->NO_KENDARAAN."','".$val->JENIS_MUATAN."',
								".$val->BERAT_ISI.",".$val->BERAT_KOSONG.",".$val->BERAT_BERSIH.",
								'".$supplier."','".$val->INPUT_BY."','".$val->INPUT_DATE."',
								'".$val->TYPE_BUAH."','".$val->TYPE_TIMBANG."','".$val->NOTE."',
								'".$val->TYPE_KENDARAAN."','".$val->DRIVER_NAME."','".$val->GRD_BUAHMENTAH."',
								'".$val->GRD_BUAHBUSUK."','".$val->GRD_BUAHKECIL."','".$val->GRD_TANGKAIPANJANG."',
								'".$val->GRD_BRONDOLAN."','".$val->GRD_TANDANKOSONG."','".$val->GRD_LAINNYA."',1,
								'".$val->AFD."','".$val->SUPPLIERCODE."','".$val->JJG."',
								'".$val->ACTIVE."','".$val->PO_NUMBER."','".$val->KODE_KONTRAKTOR."'
								)";
					$boolean_insert = $this->db->query($sInsert);
					if ($boolean_insert==true){
						$this->update_timbangan($val->NO_TIKET, $company);
					}
				}else{
					$sUpdate ="UPDATE s_data_timbangan SET SINKRON_STATUS=1 WHERE NO_TIKET='". $val->NO_TIKET ."'";	
					$db_weight->query($sUpdate);	
				}//end if
			}//end foreach looping			
		}	
    }
	//end: function sync_group
	
	function synchronize_dispatch($company, $periode_to){		
		$db_timbangan = $this->load->database($company, TRUE); 
/*
		$queries ="SELECT * FROM s_dispatch 
				   WHERE COMPANY_CODE='". $company. "' AND SINKRON_STATUS=0 
				   AND TANGGALM  BETWEEN '". $periode_to. "' and '". $periode_to."'
				   AND TANGGALK <> '18991230' AND ACTIVE=1";
*/
		if ($company=='GKM'){
			$queries ="SELECT * FROM s_dispatch 
					   WHERE COMPANY_CODE IN ('GKM','SSS','SML') AND SINKRON_STATUS=0 
					   AND TANGGALM  BETWEEN '". $periode_to. "' and '". $periode_to."'
					   AND TANGGALK <> '18991230'";	
		}else{
			$queries ="SELECT * FROM s_dispatch 
					   WHERE COMPANY_CODE='". $company. "' AND SINKRON_STATUS=0 
					   AND TANGGALM  BETWEEN '". $periode_to. "' and '". $periode_to."'
					   AND TANGGALK <> '18991230'";
		}

		$query=$db_timbangan->query($queries);
		var_dump($queries);
		$count = $query->num_rows();
		if ($count > 0){
			foreach($query->result() as $key => $val){				
				$boolean_dispatch=$this->check_dispatch($val->ID_DISPATCH, $company);
				if ($boolean_dispatch==FALSE){					
					$sInsert = "INSERT INTO s_dispatch (ID_DISPATCH, ID_STORAGE, TANGGALM, TANGGALK, 
								WAKTUM, WAKTUK, NO_KENDARAAN, ID_KOMODITAS, BERAT_ISI, BERAT_KOSONG, 
								BERAT_BERSIH, COMPANY_CODE, DRIVER_NAME, BROKEN, DIRTY, 
								MOIST, ID_DO, JENIS, MUKA, BELAKANG,
								SUHU, ALB, INPUT_BY,SINKRON_STATUS,QTY_DELIVERED_RUN,NO_SIM 
								) 
								VALUES('".$val->ID_DISPATCH."','".$val->ID_STORAGE."','".$val->TANGGALM."',
								'".$val->TANGGALK."','".$val->WAKTUM."','".$val->WAKTUK."',
								'".$val->NO_KENDARAAN."','".$val->ID_KOMODITAS."','".$val->BERAT_ISI."',
								".$val->BERAT_KOSONG.",".$val->BERAT_BERSIH.",'".$val->COMPANY_CODE."',
								'".$val->DRIVER_NAME."','".$val->BROKEN."','".$val->DIRTY."',
								'".$val->MOIST."','".$val->ID_DO."','".$val->JENIS."',
								'".$val->MUKA."','".$val->BELAKANG."','".$val->SUHU."',
								'".$val->ALB."','".$val->INPUT_BY."',1,'".$val->QTY_DELIVERED_RUN."',
								'".$val->NO_SIM."'
								)";
					$boolean_insert=$this->db->query($sInsert);	
					if ($boolean_insert==true){
						$this->update_dispatch($val->ID_DISPATCH, $company);
					}
					//$this->synchronize_dispatch_doo($val->ID_DO, $company);
				}else{
					$sUpdate ="UPDATE s_dispatch SET SINKRON_STATUS=1 WHERE ID_DISPATCH='". $val->ID_DISPATCH ."'";	
					$db_timbangan->query($sUpdate);
				}//end if
				$this->synchronize_dispatch_doo($val->ID_DO, $company);
			}//end foreach looping			
		}	
    }
	//end: function sync_group
	
	function synchronize_dispatch_doo($id_dispatch_doo, $company){		
		$db_timbangan = $this->load->database($company, TRUE); 
		$queries ="SELECT * FROM s_dispatch_do WHERE ID_DO='". $id_dispatch_doo. "'";
		$query=$db_timbangan->query($queries);
		
		$count = $query->num_rows();
		if ($count > 0){			
			$row = $query->row_array();			
			$boolean_dispatch_do=$this->check_dispatch_do($row['ID_DO'], $company);
			if ($boolean_dispatch_do==FALSE){ // if dispatch_do is not available in HO then insert the dispatch					
				$sInsert = "INSERT INTO s_dispatch_do 
							(ID_DO, C_BPARTNER_ID ,CUSTOMER_NAME, 
							CUSTOMER_ADDRESS, QTY_CONTRACT, QTY_DELIVERED, 
							COMPANY_CODE, ID_JENIS, JENIS, SO_NUMBER
							) 
							VALUES('".$row['ID_DO']."','".$row['C_BPARTNER_ID']."','".$row['CUSTOMER_NAME']."',
							'".$row['CUSTOMER_ADDRESS']."','".$row['QTY_CONTRACT']."','".$row['QTY_DELIVERED']."',
							'".$row['COMPANY_CODE']."','".$row['ID_JENIS']."','".$row['JENIS']."',
							'".$row['SO_NUMBER']."'
							)";
				$this->db->query($sInsert);	
			}else{ //if dispatch_do is available in HO then update the dispatch				
				$sUpdate = "UPDATE s_dispatch_do 
							SET QTY_CONTRACT='". $row['QTY_CONTRACT'] . "', 
							QTY_DELIVERED='". $row['QTY_DELIVERED'] . "', 
							UPDATE_TIME='".$this->global_func->gen_datetime()."'
							WHERE ID_DO='". $id_dispatch_doo. "'";
				$this->db->query($sUpdate);	
			}//end if		
		}	
    }
	//end: function sync_group
	
	function generateID($company, $periode_to){	
		$query = "SELECT SUBSTRING(MAX(ID_TIMBANGAN),-5) AS ID_TIMBANGAN 
		FROM s_data_timbangan 
		WHERE COMPANY_CODE='". $company ."' 
		AND JENIS_MUATAN ='TBS'
		AND DATE_FORMAT(TANGGALM,'%Y%m') = DATE_FORMAT('". $periode_to. "','%Y%m')";
		
		$sQuery = $this->db->query($query);
		if ($sQuery==true){
			$row = $sQuery->row();            
			$id = $row->ID_TIMBANGAN; 
		}else{
			$id = NULL;	
		}
		return $id;  
	}
	
	function generateID_emptyBunch($company, $periode_to){	
		$query = "SELECT SUBSTRING(MAX(ID_TIMBANGAN),-5) AS ID_TIMBANGAN 
		FROM s_data_timbangan 
		WHERE COMPANY_CODE='". $company ."' 
		AND JENIS_MUATAN IN ('TANKOS', 'TNK')
		AND DATE_FORMAT(TANGGALM,'%Y%m') = DATE_FORMAT('". $periode_to. "','%Y%m')";
		
		$sQuery = $this->db->query($query);
		if ($sQuery==true){
			$row = $sQuery->row();            
			$id = $row->ID_TIMBANGAN; 
		}else{
			$id = NULL;	
		}
		return $id;  
	}
	
	function update_timbangan($ticket, $company){	
		$db_weight = $this->load->database($company, TRUE); 
		$query = "UPDATE s_data_timbangan SET SINKRON_STATUS =1
		WHERE NO_TIKET='". $ticket ."'";
		$db_weight->query($query);
	}
	
	function update_dispatch($id_dispatch, $company){	
		$db_weight = $this->load->database($company, TRUE); 
		$query = "UPDATE s_dispatch SET SINKRON_STATUS =1
		WHERE ID_DISPATCH='". $id_dispatch ."'";
		$db_weight->query($query);
	}
	
	function check_ticket($ticket, $company){
		$boolean_ticket=FALSE;
		$this->db->select('NO_TIKET');
        $this->db->from('s_data_timbangan');
        $this->db->where('NO_TIKET',$ticket);
		//$this->db->where('COMPANY_CODE', $company);
		
		$sQuery = $this->db->get();
        $count = $sQuery->num_rows();	
		if ($count>0){
			$boolean_ticket = TRUE; // if ticket is available in database HO, return true
		}else{
			$boolean_ticket = FALSE;
		}
           
        return $boolean_ticket;
		
	}
	
	function check_dispatch($id_dispatch, $company){	
		$boolean_dispatch=FALSE;
		$query = "SELECT * FROM s_dispatch 
				  WHERE COMPANY_CODE='". $company ."' 
				  AND ID_DISPATCH ='". $id_dispatch ."'";
		$sQuery = $this->db->query($query);
		$row = $sQuery->row(); 
		if ($row!=NULL){
			$boolean_dispatch = TRUE; // if dispatch is available in database HO, return true
		}
		return $boolean_dispatch; 
	}
	
	function check_dispatch_do($id_do, $company){	
		$boolean_dispatch=FALSE;
		$query = "SELECT * FROM s_dispatch_do WHERE ID_DO='". $id_do. "'";
		$sQuery = $this->db->query($query);
		$row = $sQuery->row(); 
		if ($row!=NULL){
			$boolean_dispatch = TRUE; // if dispatch_doo is available in database HO, return true
		}
		return $boolean_dispatch; 
	}
	
	function get_supplier($supplier_code){	
		$boolean_ticket=FALSE;
		$query = "SELECT COMPANY_GROUP_CODE FROM m_supplier WHERE SUPPLIERCODE = '". $supplier_code ."'";
		$sQuery = $this->db->query($query);
		$row = $sQuery->row();
		$supplier = $row->COMPANY_GROUP_CODE;
		return $supplier; 
	}	
	
	function synchronize_bunchEmpty($company, $periode_to){
		$db_weight = $this->load->database($company, TRUE); 
		$queries ="SELECT * FROM  s_data_timbangan t
				WHERE t.COMPANY_CODE ='". $company ."' 
				AND t.TANGGALM ='". $periode_to ."'
				AND JENIS_MUATAN='TANKOS' 
				AND t.TANGGALK <>'18991230'
				AND t.ACTIVE = 1";	
		$query=$db_weight->query($queries);
		$idcount=0;
		$id=$this->generateID_emptyBunch($company, $periode_to);
		$idcount=$id;
		
		$count = $query->num_rows();
		if ($count > 0){
			foreach($query->result() as $key => $val){				
				if ($id==NULL){
					$id= $company."TN".date("Ym",strtotime($periode_to))."00001"; //sample: MAGTB20130600001
					$idcount=1;
				}else{
					$idcount = $idcount+1;
					$id = $company."TN".date("Ym",strtotime($periode_to)).str_pad($idcount, 5, "0", STR_PAD_LEFT);
				}
				//$row = $query->row_array();
				$bolean_ticket=$this->check_ticket($val->NO_TIKET, $company);			
				if ($bolean_ticket==FALSE){
					$sInsert = "INSERT INTO s_data_timbangan 
								(ID_TIMBANGAN, NO_TIKET, NO_SPB,
								 TANGGALM, TANGGALK, WAKTUM,
								 WAKTUK, NO_KENDARAAN, JENIS_MUATAN,
								 BERAT_ISI, BERAT_KOSONG, BERAT_BERSIH,
								 COMPANY_CODE, INPUT_BY, INPUT_DATE,
								 TYPE_BUAH, TYPE_TIMBANG, NOTE,
								 TYPE_KENDARAAN, DRIVER_NAME, GRD_BUAHMENTAH,
								 GRD_BUAHBUSUK, GRD_BUAHKECIL, GRD_TANGKAIPANJANG, 
								 GRD_BRONDOLAN, GRD_TANDANKOSONG, GRD_LAINNYA, SINKRON_STATUS,
								 AFD, SUPPLIERCODE, JJG, ACTIVE, PO_NUMBER, KODE_KONTRAKTOR
								 ) 
						VALUES('".$id."','".$val->NO_TIKET."','".$val->NO_SPB."',
							   '".$val->TANGGALM."','".$val->TANGGALK."','".$val->WAKTUM."',
							   '".$val->WAKTUK."','".$val->NO_KENDARAAN."','".$val->JENIS_MUATAN."',
							   ".$val->BERAT_ISI.",".$val->BERAT_KOSONG.",".$val->BERAT_BERSIH.",
							   '".$val->COMPANY_CODE."','".$val->INPUT_BY."','".$val->INPUT_DATE."',
							   '".$val->TYPE_BUAH."','".$val->TYPE_TIMBANG."','".$val->NOTE."',
							   '".$val->TYPE_KENDARAAN."','".$val->DRIVER_NAME."','".$val->GRD_BUAHMENTAH."',
							   '".$val->GRD_BUAHBUSUK."','".$val->GRD_BUAHKECIL."','".$val->GRD_TANGKAIPANJANG."',
							   '".$val->GRD_BRONDOLAN."','".$val->GRD_TANDANKOSONG."','".$val->GRD_LAINNYA."',1,
							   '".$val->AFD."','".$val->SUPPLIERCODE."','".$val->JJG."',
							   '".$val->ACTIVE."','".$val->PO_NUMBER."','".$val->KODE_KONTRAKTOR."'
							  )";					
					$boolean_insert = $this->db->query($sInsert);
					var_dump($sInsert);
					if ($boolean_insert==true){
						//$this->update_timbangan($val->NO_TIKET, $company);
					}
				}else{
					$sUpdate ="UPDATE s_data_timbangan SET SINKRON_STATUS=1 WHERE NO_TIKET='". $val->NO_TIKET ."'";					
					//$db_weight->query($sUpdate);
				}//end if
			}//end looping			
		}	
    }
	//end: function synchronize
}
?>