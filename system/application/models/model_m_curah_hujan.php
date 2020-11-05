<?php
class model_m_curah_hujan extends Model
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
		
          
        $queries = "SELECT * FROM s_data_curah_hujan WHERE ACTIVE=1 AND COMPANY_CODE='".$company."' 
                    AND DATE_FORMAT(TANGGAL,'%Y%m')= '".$tahun.$bulan."' ".$whereAFD.$whereBlock;

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
            array_push($cell, htmlentities($obj->ID_CURAH_HUJAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CURAH_HUJAN,ENT_QUOTES,'UTF-8'));
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
        
        $queries = "SELECT * FROM s_data_curah_hujan ". $where;
                    
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
            array_push($cell, htmlentities($obj->ID_CURAH_HUJAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CURAH_HUJAN,ENT_QUOTES,'UTF-8'));
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
   	
    function lokasi_validate($afd,$company){
        $company = $this->db->escape_str($company);
        $afd = $this->db->escape_str($afd);
        
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LEFT(LOCATION_CODE,2)='".$afd."' 
                AND LOCATION_TYPE_CODE='OP'"; 
        $sQuery = $this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        return $rowcount;    
    }
    
    function add_new($company, $data_post){
        $return['status']='';
        $return['error']=false;
        if( isset($company))
        {
            if(!empty($company))
            {
                $cek_data_exist = $this->cek_data_exist('s_data_curah_hujan',array('AFD'=>$data_post['AFD'],'TANGGAL'=>$data_post['TANGGAL'], 'COMPANY_CODE'=>$company,'ACTIVE'=>1),'ID_CURAH_HUJAN'); 
				 
				 
                if ($cek_data_exist <= 0){
                    $this->db->insert('s_data_curah_hujan', $data_post ); 
                    if($this->db->trans_status() === FALSE){
                        $status = $this->db->_error_message();//"Error in Transactions!!";
                    }else{
                        $status="Insert Data Berhasil";
						$return['status']=$status;
            			$return['error']=false;    
                    }    
                }else{
                    $status='Data curah hujan'.' AFD '. $data_post['AFD'].' tanggal ' . $data_post['TANGGAL'].' sudah pernah diinput';
					$return['status']=$status;
            		$return['error']=true; 
                }
                    
            }else{
                $status="data tidak lengkap";
				$return['status']=$status;
            	$return['error']=true; 
            }
        }else{
            $status="data tidak lengkap";
			$return['status']=$status;
            $return['error']=true; 
        }
        return $return;
    }
	
	function delete_all($data_post){
		$return['status']='';
        $return['error']=false;
		foreach($data_post as $keys => $vals){
			$this->db->where('ID_CURAH_HUJAN',$vals['ID_CURAH_HUJAN']);
			$this->db->update('s_data_curah_hujan',$data_post[$keys]);
			
			if($this->db->trans_status() == FALSE){
				$return['status'].="Delete data curah hujan AFD ". $vals['AFD'] ." tanggal ".$vals['TANGGAL']." gagal"."\n";  
				$return['error']=true;
            }else{
                $return['status'].="Delete data BJR AFD ". $vals['AFD'] ." tanggal ".$vals['TANGGAL']." berhasil"."\n";
				$return['error']=false;   
            }
       	}
		return $return;		 
    }
		
    function update_curah_hujan($data_post, $company){
		$company = trim($this->db->escape_str($company));
        $status='';
		$return['status']='';
        $return['error']=false;
               
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
       		
        $cek_data_exist = $this->cek_data_exist('s_data_curah_hujan',array('AFD'=>$data_post['AFD'],'TANGGAL'=>$data_post['TANGGAL'],'COMPANY_CODE'=>$company,'ACTIVE'=>1),'ID_CURAH_HUJAN');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==false){
            
            $this->db->where('AFD',$data_post['AFD']);
            $this->db->where('TANGGAL',$data_post['TANGGAL']);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_data_curah_hujan',$data_post);
            if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
				$return['status']=$status;
            	$return['error']=true; 
            }else{
                $status="Update Data curah hujan AFD ".$data_post['AFD']." Tanggal ".$data_post['TANGGAL']." berhasil"."\n";  
				$return['status']=$status;
            	$return['error']=false;  
            }
        }
        return $return;
 
    }
	
	function get_curah_hujan($company, $afd, $tgl){
        $company = $this->db->escape_str($company);
		
        $query="SELECT * FROM s_data_curah_hujan WHERE COMPANY_CODE='".$company."' AND TANGGAL = '".$tgl."' AND ACTIVE=1 AND AFD = '".$afd."'";

		$sQuery = $this->db->query($query);
        $rowcount=$sQuery->num_rows();
        return $rowcount; 
    }
    
    function delete_curah_hujan($afd,$tanggal,$company){
        $afd = $this->db->escape_str($afd);
        $tanggal = $this->db->escape_str($tanggal);
        $company = $this->db->escape_str($company);
        
        $status='';
		$return['status']='';
        $return['error']=false;
        $cek_data = $this->cek_data_exist('s_data_curah_hujan',
                    array('AFD'=>$afd,'TANGGAL'=>$tanggal,'COMPANY_CODE'=>$company),'ID_CURAH_HUJAN');
        if ($cek_data <= 0){
            $status='Data Curah Hujan tidak ada di database';
        }
        
        if(empty($status) || $status==false){
            $this->db->where('AFD',$afd);
            $this->db->where('TANGGAL',$tanggal);
            $this->db->where('COMPANY_CODE',$company);
            
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update('s_data_curah_hujan');
            
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
				$return['status']=$status;
            	$return['error']=true; 
            }else{
                $status="Delete data curah hujan AFD " .$afd. " tanggal ".$tanggal." berhasil";   
				$return['status']=$status;
            	$return['error']=false;  
            }      
        }

        return $return;  
        
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
