<?php
class model_s_recycle extends Model
{
    public $wherecondition;
    function __construct(){
        parent::__construct();
        $this->load->database();   
    }
    
    function LoadData($periode,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8'); 

        $periode = trim($this->db->escape_str($periode));
        $company = trim($this->db->escape_str($company));
        
        $queries ="SELECT *
            FROM s_dispatch_return WHERE COMPANY_CODE='".$company."' 
            AND DATE_FORMAT(TANGGAL,'%Y%m')='".$periode."'";
            
        $sql2 = $queries;
       
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sql2);
        $count = $query->num_rows(); 

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
            
        $start = $limit * $page - $limit;
        if ($start > 0 ){
            $start = $start;
        } else {
            $start = 0;
        }
        
        $sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $link = "";
        $no = 1;
        $template_path = base_url().$this->config->item('template_path');
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_RECYCLE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_DISPATCH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_KOMODITAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_KOSONG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_ISI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BROKEN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOIST,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DRIVER_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_SIM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_BA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
              
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
    function get_no_tiket($q,$company,$tanggalm){
        $company=trim($this->db->escape_str($company));
        $tanggalm=trim($this->db->escape_str($tanggalm));
        $no_tiket=strtoupper(str_replace(" ","",trim($this->db->escape_str($q))));
		
		$db_weight = $this->load->database($company, TRUE); 
		
		$query="SELECT * FROM s_data_timbangan WHERE JENIS_MUATAN = 'LAIN LAIN' AND TANGGALM = '".$tanggalm."' AND COMPANY_CODE ='".$company."' AND REPLACE(NO_TIKET,' ','') LIKE '%".$q."%' 
AND ACTIVE = 1 ORDER BY ID_TIMBANGAN ASC ";
		$sQuery=$db_weight->query($query);

        $rowcount=$sQuery->num_rows();
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
        
    }
	function get_doc($q,$company,$tanggalm){
        $company=trim($this->db->escape_str($company));
		$tanggalm=trim($this->db->escape_str($tanggalm));
        $no_tiket=strtoupper(str_replace(" ","",trim($this->db->escape_str($q))));
		
		$db_weight = $this->load->database($company, TRUE); 
		
		$query="SELECT * FROM s_dispatch WHERE REPLACE(ID_DISPATCH,' ','') LIKE '%".$q."%' AND COMPANY_CODE ='".$company."' AND ACTIVE = 1 AND DATE_FORMAT(TANGGALM, '%Y%m%d') BETWEEN DATE(DATE_FORMAT('".$tanggalm."' - INTERVAL 30 DAY,'%Y%m%d')) AND DATE_FORMAT('".$tanggalm."', '%Y%m%d')  AND JENIS IN ('KERNEL','KRN','CPO') ORDER BY ID_DISPATCH ASC ";
		$sQuery=$db_weight->query($query);
        $rowcount=$sQuery->num_rows();
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
        
    }
	 function get_jenis($company){
        $company=trim($this->db->escape_str($company));
		
		$db_weight = $this->load->database($company, TRUE); 
		
		$query="select * from s_komoditas WHERE JENIS IN ('CPO','KRN','KERNEL') AND COMPANY_CODE ='".$company."'
AND ACTIVE = 1 ORDER BY JENIS ASC ";
		$sQuery=$db_weight->query($query);

        $rowcount=$sQuery->num_rows();
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
        
    }
	
    function data_search($data_search,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8'); 

        $company = trim($this->db->escape_str($company));
        
        $where = "WHERE COMPANY_CODE = '".$company."'"; 
        $where_cnt = sizeof($data_search);
        $i=0;
        for($i==0; $i<=$where_cnt-1; $i++){
            switch(strtolower(trim($data_search[$i]['op']))){
                case "bw":
                    $operator = "LIKE";
                    break;
                case "eq":
                    $operator = "=";
                    break;
                case "ne":
                    $operator = "!=";
                    break;
                case "lt":
                    $operator = "<";
                    break;
                case "le":
                    $operator = "<=";
                    break;
                case "gt":
                    $operator = ">";
                    break;
                case "ge":
                    $operator = ">=";
                    break;
                case "ew":
                    $operator ="LIKE";
                    break;
                case "cn":
                    $operator ="LIKE";
                    break;
                default:
                    $operator ="LIKE";    
            }
            
            if(trim(strtoupper($operator))== "LIKE" && !empty($operator)){
                $where .=" AND ".trim($this->db->escape_str($data_search[$i]['field']))." $operator '%".trim($this->db->escape_like_str($data_search[$i]['data']))."%'";   
            }else{
               $where .=" AND ".trim($this->db->escape_str($data_search[$i]['field']))." $operator '".trim($this->db->escape_str($data_search[$i]['data']))."'"; 
            }    
        }
        //$where .=" GROUP BY NO_KENDARAAN";
        $this->wherecondition=$where;
        
        $queries ="SELECT *
            FROM s_dispatch_return ". $this->wherecondition;

        $sql2 = $queries;
       
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sql2);
        $count = $query->num_rows(); 

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
            
        $start = $limit * $page - $limit;
        if ($start > 0 ){
            $start = $start;
        } else {
            $start = 0;
        }
        
        $sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
		//var_dump($sql);
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $link = "";
        $no = 1;
        $template_path = base_url().$this->config->item('template_path');
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_RECYCLE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_DISPATCH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_KOMODITAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_KOSONG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_ISI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BROKEN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOIST,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DRIVER_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_SIM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_BA,ENT_QUOTES,'UTF-8'));   
			array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));         
           
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
            $exec_qSPB->free_result();
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
  
    function update_data($id,$company,$data){
		$status='';

        $id = trim($this->db->escape_str($id));
        $company = trim($this->db->escape_str($company));

		$this->db->where('ID_RECYCLE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update('s_dispatch_return', $data );
		if($this->db->trans_status() == FALSE){
			$status = $this->db->_error_message();//"Error in Transactions!!";
		}else{
			$status="Update Data ID Berhasil"."\n";
		}    
		return $status;
    }
	
	function delete_data($id_timbang,$company){
        $id_timbang = trim($this->db->escape_str($id_timbang));
        $company = trim($this->db->escape_str($company));
        $status='';
        if((!empty($id_timbang) && $id_timbang==false)){
            $status = "ID_RECYCLE CANNOT BE NULL !";
        }
        
        if((!empty($company) && $company==false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !";
        }
		
        $cek_data_exist = $this->cek_data_exist('s_dispatch_return',array('ID_RECYCLE'=>$id_timbang,'COMPANY_CODE'=>$company),'ID_RECYCLE');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==false){
            
            $this->db->where('ID_RECYCLE',$id_timbang);
            $this->db->delete('s_dispatch_return');            
                        
            if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
        
    }
	
    function add_new($company,$data_post){
        $status='';
        $company = $this->db->escape_str($company);
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_dispatch_return',
                    array('ID_RECYCLE'=>$data_post['ID_RECYCLE']),'ID_RECYCLE');
        if ($cek_data_exist > 0){
            $status='Data Input '.$data_post['ID_RECYCLE'].' telah ada di database';
        }
        
        $cek_data_exist = $this->cek_data_exist('s_dispatch_return',
                    array('ID_DISPATCH'=>$data_post['ID_DISPATCH']),'ID_DISPATCH');
        if ($cek_data_exist > 0){
            $status='Data Input '.$data_post['ID_DISPATCH'].' telah ada di database';
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 's_dispatch_return', $data_post );                        
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil";   
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
    
    function generate_tbg_xls($jns_muatan,$periode,$company){
        $jns_muatan = trim($this->db->escape_str($jns_muatan));
        $periode = trim($this->db->escape_str($periode));
        $company = trim($this->db->escape_str($company));
         
        $query ="SELECT NO_TIKET,NO_SPB,TANGGALM,TANGGALK,NO_KENDARAAN,BERAT_ISI,BERAT_KOSONG,BERAT_BERSIH
                    FROM s_recycle
                    WHERE COMPANY_CODE='".$company."' AND JENIS_MUATAN=DATE_FORMAT('".$jns_muatan."','%Y%m%d')
                        AND DATE_FORMAT(TANGGALM,'%Y%m%d')='".$periode."'" ;
        
       $sQuery = $this->db->query($query);
        
       $temp = $sQuery->row_array();
       $temp_result = array(); 
        
       foreach ( $sQuery->result_array() as $row ){
            $temp_result [] = $row;
       }

       $this->db->close();
       return $temp_result; 
    }
   
}
?>
