<?php
class model_m_bjr extends Model
{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    function LoadData($bulan,$tahun,$company,$afd='-',$block='-'){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $bulan=$this->db->escape_str($bulan);
        $tahun=$this->db->escape_str($tahun);
        $company=$this->db->escape_str($company);
        
        $whereAFD='';
        $whereBlock='';
        
        if(trim($afd)!='-'){
            $whereAFD =" AND AFD='".$afd."' ";   
        }
        
        if(trim($block)!='-'){
            $whereBlock =" AND BLOCK='".$block."' ";   
        }
          
        $queries = "SELECT *, CASE WHEN APPROVED = 1 THEN 'APPROVED' ELSE 'WAITING APPROVAL' END AS APPROVED FROM s_data_bjr WHERE ACTIVE=1 AND COMPANY_CODE='".$company."' 
                    AND BULAN='".$bulan."' AND TAHUN='".$tahun."' ".$whereAFD.$whereBlock;

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
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_BJR,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->VALUE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->APPROVED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
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
    
    function data_search($data_search, $company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));
        
        $where = "WHERE ACTIVE=1 AND COMPANY_CODE = '".$company."' "; 
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
        
        $queries = "SELECT * FROM s_data_bjr ". $where;
                    
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
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_BJR,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->VALUE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
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
    
    /*########################## PENETAPAN PERIODE BJR ###############################
    ##################################################################################*/
    function set_bjr_periode($periode,$company){
        $periode = $this->db->escape_str($periode);
        $company = $this->db->escape_str($company);
        
        $query = "SELECT PERIODE FROM s_bjr_periode WHERE COMPANY_CODE='".$company."'";
        $sQuery = $this->db->query($query);
        $status ='';
        
        if($sQuery->num_rows() > 0){
            //$this->db->set('PERIODE',$periode);
            $data = array('PERIODE'=>$periode);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_bjr_periode',$data);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data Berhasil";   
            }    
        }elseif($sQuery->num_rows() <=0){
             $this->db->set('PERIODE',$periode);
             $this->db->set('COMPANY_CODE',$company);
             $this->db->insert('s_bjr_periode');
             if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil";   
            }
        }
        return $status; 
    }
	
	function getCompany(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
    
    function get_bjr_periode($company){
        $company = $this->db->escape_str($company);
        $query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();
            
            if(!empty($row->PERIODE_USED) || $row->PERIODE_USED!=null){
                $value = $row->PERIODE_USED;    
            }else{
                $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
            }
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
    /*########################## END PENETAPAN PERIODE BJR ###############################
    ##################################################################################*/
    
    function get_afdeling($company,$q){
        $company=$this->db->escape_str($company);
        $q=$this->db->escape_str($q);
        $query="SELECT LEFT(LOCATION_CODE,2) AS AFD 
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LOCATION_TYPE_CODE='OP'
                    AND LOCATION_CODE LIKE '%".$q."%'
                GROUP BY AFD,COMPANY_CODE";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if(!empty($rowcount) || $rowcount!=0)
        {
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
    }
    
    function get_block($company,$location_left,$location){
        $company=$this->db->escape_str($company);
        $location_left=$this->db->escape_str($location_left);
        $location=$this->db->escape_str($location); 
        
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LEFT(LOCATION_CODE,2)='".$location_left."' 
                AND LOCATION_CODE LIKE '%".$location."%' AND LOCATION_TYPE_CODE='OP'";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
         $temp_result = array();
        if(!empty($rowcount) || $rowcount!=0)
        {
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
        
    }
    
    function lokasi_validate($afd,$location,$company){
        $company = $this->db->escape_str($company);
        $afd = $this->db->escape_str($afd);
        $location = $this->db->escape_str($location);
        
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LEFT(LOCATION_CODE,2)='".$afd."' 
                AND LOCATION_CODE = '".$location."' AND LOCATION_TYPE_CODE='OP'"; 
        $sQuery = $this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        return $rowcount;    
    }
    
    function add_new($company, $data_post){
        $status='';
        if( isset($company))
        {
            if(!empty($company))
            {
                $cek_data_exist = $this->cek_data_exist('s_data_bjr',array('AFD'=>$data_post['AFD'],'BLOCK'=>$data_post['BLOCK'], 'BULAN'=>$data_post['BULAN'],'TAHUN'=>$data_post['TAHUN'], 'COMPANY_CODE'=>$company,'ACTIVE'=>1),'ID_BJR'); 
				 
				 
                if ($cek_data_exist <= 0){
                    $this->db->insert( 's_data_bjr', $data_post ); 
                    //$status=$this->db->insert_id();
                    if($this->db->trans_status() === FALSE){
                        $status = $this->db->_error_message();//"Error in Transactions!!";
                    }else{
                        $status="Insert Data Berhasil";   
                    }    
                }else{
                    $status='Data Input telah ada di database';
                }
                    
            }else{
                $status="data tidak lengkap";
            }
        }else{
            $status="data tidak lengkap";
        }
        return $status;
    }

	function approve_bjr($data_post){
		$return['status']='';
		foreach($data_post as $keys => $vals){
			$this->db->where('ID_BJR',$vals['ID_BJR']);
			$this->db->where('AFD',$vals['AFD']);
            $this->db->where('BLOCK',$vals['BLOCK']);
            $this->db->where('BULAN',$vals['BULAN']);
            $this->db->where('TAHUN',$vals['TAHUN']);
            $this->db->where('COMPANY_CODE',$vals['COMPANY_CODE']);
			$this->db->update('s_data_bjr',$data_post[$keys]);
			
			if($this->db->trans_status() == FALSE){
				$return['status'].="Approve data BJR Blok ". $vals['BLOCK'] ." gagal"."\n";  
            }else{
                $return['status'].="Approve data BJR Blok ". $vals['BLOCK'] ." berhasil"."\n";   
            }
       	}
		return $return['status'];		 
    }
	
    function update_bjr($data_post, $company){
		$company = trim($this->db->escape_str($company));
        $status=FALSE;
               
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_data_bjr',array('AFD'=>$data_post['AFD'],'BLOCK'=>$data_post['BLOCK'],'BULAN'=>$data_post['BULAN'],
										'TAHUN'=>$data_post['TAHUN'],'COMPANY_CODE'=>$company),'ID_BJR');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==false){
            
            $this->db->where('AFD',$data_post['AFD']);
            $this->db->where('BLOCK',$data_post['BLOCK']);
            $this->db->where('BULAN',$data_post['BULAN']);
            $this->db->where('TAHUN',$data_post['TAHUN']);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_data_bjr',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
 
    }
	
	/*
		get_active_bjr added by Asep, 20130507		
	*/
	function get_active_bjr($company, $block, $tgl){
        $company = $this->db->escape_str($company);
		
		//todo: Asep, tambahkan parameter tanggal (yyyymmdd)
        $query="SELECT bj.AFD,bj.BLOCK,bj.VALUE,
							PERIODE,bj.COMPANY_CODE 
				FROM(
						SELECT AFD,BLOCK,VALUE,
									CONCAT(TAHUN,BULAN) AS PERIODE,
									COMPANY_CODE 
						FROM s_data_bjr 
						WHERE COMPANY_CODE='".$company."' 
								ORDER BY AFD ASC, BLOCK ASC
				)bj
				JOIN (
						SELECT AFD,BLOCK,MAX(CONCAT(TAHUN,BULAN)) AS MAX_PERIODE
						FROM s_data_bjr
						WHERE COMPANY_CODE='".$company."' AND CONCAT(TAHUN,BULAN) <= DATE_FORMAT('".$tgl."','%Y%m') AND ACTIVE=1
						GROUP BY BLOCK 
				) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK 
								AND bjr.MAX_PERIODE = bj.PERIODE
WHERE bj.BLOCK = '".$block."'";

		//$query="SELECT bjr.VALUE FROM s_data_bjr bjr WHERE bjr.BLOCK = '".$block."'  AND bjr.ACTIVE = 1 AND bjr.COMPANY_CODE ='".$company."'";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
            $value = $row->VALUE;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	
	function get_bjr_blok($company, $block, $tgl){
        $company = $this->db->escape_str($company);
		
        $query="SELECT * FROM s_data_bjr WHERE COMPANY_CODE='".$company."' AND CONCAT(TAHUN,BULAN) = DATE_FORMAT('".$tgl."','%Y%m') AND ACTIVE=1 AND BLOCK = '".$block."'";

		$sQuery = $this->db->query($query);
        $rowcount=$sQuery->num_rows();
        return $rowcount; 
    }
    
    function delete_bjr($afd,$block,$bulan,$tahun,$company){
        $afd = $this->db->escape_str($afd);
        $block = $this->db->escape_str($block);
        $bulan = $this->db->escape_str($bulan);
        $tahun = $this->db->escape_str($tahun);
        $company = $this->db->escape_str($company);
        
        $status=FALSE;
        $cek_data = $this->cek_data_exist('s_data_bjr',
                    array('AFD'=>$afd,'BLOCK'=>$block,'BULAN'=>$bulan,'TAHUN'=>$tahun,
                            'COMPANY_CODE'=>$company),
                    'ID_BJR');
        if ($cek_data <= 0){
            $status='Data BJR tidak ada di database';
        }
        
        if(empty($status) || $status==false){
            $this->db->where('AFD',$afd);
            $this->db->where('BLOCK',$block);
            $this->db->where('BULAN',$bulan);
            $this->db->where('TAHUN',$tahun);
            $this->db->where('COMPANY_CODE',$company);
            
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_data_bjr');
            
            //$this->db->delete('s_data_bjr');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data Berhasil";   
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
}
?>
