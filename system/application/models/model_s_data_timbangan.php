<?php
class model_s_data_timbangan extends Model
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
        
        //$queries ="SELECT ID_TIMBANGAN,TANGGALM,NO_TIKET,NO_KENDARAAN,BERAT_ISI,BERAT_KOSONG,BERAT_BERSIH,NO_SPB,TYPE_BUAH,SUPPLIERCODE FROM s_data_timbangan WHERE COMPANY_CODE='".$company."' AND TANGGALM='".$periode."' AND FLAG_TIMBANGAN = 1 GROUP BY NO_KENDARAAN ,NO_TIKET";
		$queries ="SELECT *
            FROM s_data_timbangan WHERE COMPANY_CODE='".$company."' 
            AND DATE_FORMAT(TANGGALM,'%Y%m')='".$periode."' AND FLAG_TIMBANGAN = 1 GROUP BY NO_KENDARAAN ,NO_TIKET";
            
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
            array_push($cell, htmlentities($obj->ID_TIMBANGAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_ISI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_KOSONG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TYPE_BUAH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SUPPLIERCODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
			//start: Added By Asep, 20130829
			array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JENIS_MUATAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TYPE_TIMBANG,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NOTE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TYPE_KENDARAAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DRIVER_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BUAHMENTAH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BUAHBUSUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BUAHKECIL,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_TANGKAIPANJANG,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BRONDOLAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_LAINNYA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BERAT_GRADING,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JJG,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FLAG_TIMBANGAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
			//end: Added By Asep, 20130829
            
            //$acts=trim(htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
            ///*$qSPB = "SELECT NO_SPB 
            //         FROM s_nota_angkutbuah 
            //            WHERE NO_SPB ='".$acts."' AND COMPANY_CODE='".$company."'";*/
            //$qSPB="SELECT nab.NO_SPB 
            //        FROM s_nota_angkutbuah nab
            //         INNER JOIN s_data_timbangan_detail tbgd ON tbgd.NO_SPB = nab.NO_SPB
            //    AND tbgd.COMPANY_CODE = nab.COMPANY_CODE
            //            WHERE nab.NO_SPB ='".$acts."' AND nab.COMPANY_CODE='".$company."'";
            // $exec_qSPB = $this->db->query($qSPB);
            //$exec_numrows = $exec_qSPB->num_rows();
            ////$act=$acts; 
           //$link=(($acts==='-' || empty($acts)) || empty($exec_numrows))?
            //        //"<img src='".$template_path."themes/base/images/search.png' width='12px' height='15px' onclick=\"linknab();\"/>":$acts;
             //       "0":$acts;  
            //array_push($cell, $link);
            //array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));                
            $row = new stdClass();

            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
            //$exec_qSPB->free_result();
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
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
        $where .=" GROUP BY NO_KENDARAAN";
        $this->wherecondition=$where;
        
        $queries ="SELECT *
            FROM s_data_timbangan ". $this->wherecondition;

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
            array_push($cell, htmlentities($obj->ID_TIMBANGAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_ISI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_KOSONG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TYPE_BUAH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SUPPLIERCODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));	
			
			array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JENIS_MUATAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TYPE_TIMBANG,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NOTE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TYPE_KENDARAAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DRIVER_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BUAHMENTAH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BUAHBUSUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BUAHKECIL,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_TANGKAIPANJANG,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_BRONDOLAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->GRD_LAINNYA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BERAT_GRADING,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JJG,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FLAG_TIMBANGAN,ENT_QUOTES,'UTF-8'));
            
            $acts=trim(htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
            $qSPB = "SELECT NO_SPB 
                     FROM s_nota_angkutbuah 
                        WHERE NO_SPB ='".$acts."' AND COMPANY_CODE='".$company."'";
            $exec_qSPB = $this->db->query($qSPB);
            $exec_numrows = $exec_qSPB->num_rows();
            $link=(($acts==='-' || empty($acts)) || empty($exec_numrows))?
                    //"<img src='".$template_path."themes/base/images/search.png' width='12px' height='15px' onclick=\"linknab();\"/>":$acts;
                    "0":$acts; 
            array_push($cell, $link);
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
    
    function load_grid_timbangan($vc, $no_tiket, $company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $vc = strtoupper(str_replace(" ","",trim($this->db->escape_str($vc))));
        $no_tiket = trim($this->db->escape_str($no_tiket));
        $company = trim($this->db->escape_str($company));
        
        $queries="SELECT tbgd.ID_TIMBANGAN,tbgd.AFD,tbgd.BLOCK,tbgd.JANJANG,bjr.VALUE AS BJR,
                        ROUND(tbgd.BERAT_EMPIRIS,2) AS BERAT_EMPIRIS,
                        ROUND(tbgd.BERAT_REAL,2) AS BERAT_REAL,
                        tbgd.COMPANY_CODE,MONTH(tbg.TANGGALM) AS BULANT
                    FROM s_data_timbangan_detail tbgd
                    LEFT JOIN s_data_timbangan tbg ON tbg.ID_TIMBANGAN = tbgd.ID_TIMBANGAN
                    LEFT JOIN (
                        SELECT bj.AFD,bj.BLOCK,bj.VALUE,
                            bj.TAHUN,bj.BULAN,bj.COMPANY_CODE FROM s_data_bjr bj
                        INNER JOIN (
                            SELECT AFD,BLOCK,MAX(TAHUN) AS TAHUN,MAX(BULAN) AS BULAN,COMPANY_CODE
                            FROM s_data_bjr
                            WHERE COMPANY_CODE='".$company."'
                            GROUP BY BLOCK -- ,AFD,BULAN,TAHUN
                            -- HAVING bulan <=3
                        ) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK 
                            AND bjr.TAHUN = bj.TAHUN AND bjr.BULAN = bj.BULAN
                            AND bjr.COMPANY_CODE = bj.COMPANY_CODE
                        ORDER BY bj.AFD ASC, bj.BLOCK ASC
                    ) bjr ON bjr.AFD = tbgd.AFD AND bjr.BLOCK = tbgd.BLOCK
                                AND bjr.COMPANY_CODE = tbgd.COMPANY_CODE   
                    WHERE tbgd.NO_TIKET='".$no_tiket."' 
                    AND REPLACE(TRIM(tbgd.NO_KENDARAAN),' ','')='".$vc."' AND tbgd.COMPANY_CODE='".$company."'
                    GROUP BY tbgd.BLOCK,tbgd.AFD";
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

        //$act = "";
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($obj->ID_TIMBANGAN,ENT_QUOTES,'UTF-8'));
           // array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JANJANG,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->BJR,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities($obj->ID_TIMBANGAN,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities($obj->JENIS_MUATAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_EMPIRIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_REAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));               
            $row = new stdClass();

            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
    
    function LoadData_WB($filename,$periode,$periode_to,$company){
        # open dbf file for reading and writing
        $db_file =  'uploads/'.$filename;
        $id = @ dbase_open ($db_file, READ_WRITE)
           or die ("Could not open dbf file <i>$db_file</i>."); 
        # find the number of fields (columns) and rows in the dbf file
        //$num_fields = dbase_numfields ($id);
        //$num_rows   = dbase_numrecords($id);
        //$record_numbers = dbase_numrecords($id);
        //$column_info = dbase_get_header_info($id);
        /* memanggil file DBF untuk kita Buka */
        //$insert=dbase_open(“D:\DBF\kota.dbf???,0);
        if ($id){
            $jum_record=dbase_numrecords($id);
            if($jum_record > 0){
                $this->db->empty_table('dummy_wb');// empty the dummy table, in every new capture
                
                for ($ind=1;$ind<=$jum_record;$ind++){
                    $record=dbase_get_record($id,$ind);     
                    $wb_input = array(
                        'NOSERI'=>trim(htmlentities($record[0],ENT_QUOTES,'UTF-8')),
                        'NOPOLISI' =>trim(htmlentities($record[1],ENT_QUOTES,'UTF-8')),
                        'NMRELASI'=>trim(htmlentities($record[2],ENT_QUOTES,'UTF-8')),
                        'NMBARANG'=>trim(htmlentities($record[3],ENT_QUOTES,'UTF-8')),
                        'REFERENSI'=>trim(htmlentities($record[4],ENT_QUOTES,'UTF-8')),
                        'TIMBANG1'=>trim(htmlentities($record[5],ENT_QUOTES,'UTF-8')),
                        'TIMBANG2'=>trim(htmlentities($record[6],ENT_QUOTES,'UTF-8')),
                        'TANGGALM'=>trim(htmlentities($record[7],ENT_QUOTES,'UTF-8')),
                        'TANGGALK'=>trim(htmlentities($record[8],ENT_QUOTES,'UTF-8')),
                        'WAKTU1'=>trim(htmlentities($record[9],ENT_QUOTES,'UTF-8')),
                        'WAKTU2'=>trim(htmlentities($record[10],ENT_QUOTES,'UTF-8')),
                        'PENIMBANG'=>trim(htmlentities($record[11],ENT_QUOTES,'UTF-8'))
                        );
                    
                    $this->db->set($wb_input);
                    $this->db->insert('dummy_wb') ;
                    //echo $periode."-".$company;  
                }
                
                if($this->db->trans_status() === FALSE){
                    $status = $this->db->_error_message();//"Error in Transactions!!";
                    echo $status;
                }else{                        
                    /*
                    $sql = 'CALL sp_filter_dummy_wb(?,?,?,?)';
                    $query = $this->db->query($sql,array($periode,$periode_to,$jns,$company));
                    */ 
                    $qSP ="CALL sp_tbg_filter_dummy_wb(?,?,?,?)";
                    $stmt = mysqli_prepare($this->db->conn_id, $qSP);
                    $jns='TBS';
                    $stmt->bind_param('ssss',$periode,$periode_to,$jns,$company);
                    $stmt->execute(); 
                    
                      
                }
            }
        }
        dbase_close($id);
        return 1;
    }

    function get_no_mesin($q,$company,$periode){
        $company = trim($this->db->escape_str($company));
        $periode = trim($this->db->escape_str($periode));
        
        $q = str_replace(" ","",trim($this->db->escape_str($q))) ; 
        $query ="SELECT * FROM s_nota_angkutbuah WHERE COMPANY_CODE ='".$company."' AND STATUS =0 
            AND REPLACE(NO_KENDARAAN,' ','') LIKE '%".$q."%' AND TANGGAL='".$periode."' AND ACTIVE=1 GROUP BY NO_KENDARAAN ASC";
        $sQuery = $this->db->query($query);
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
    
    function load_nota_info($company,$periode,$no_kendaraan){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $periode=trim($this->db->escape_str($periode));
        $company=trim($this->db->escape_str($company)); 
        $no_kendaraan=strtoupper(str_replace(" ","",trim($this->db->escape_str($no_kendaraan))));
         
        $queries = "SELECT * FROM s_nota_angkutbuah WHERE COMPANY_CODE='".$company."' 
                    AND DATE_FORMAT(TANGGAL,'%Y%m%d')=DATE_FORMAT('".$periode."','%Y%m%d') AND 
                        REPLACE(TRIM(NO_KENDARAAN),' ','')='".$no_kendaraan."' AND STATUS=0 AND ACTIVE=1";

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

        $this->db->limit($limit, $start);
        //$this->db->order_by("$sidx", "$sord");
        // ORDER BY 1 LIMIT ".$start.",".$limit."
        $sql =  $queries;

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_NT_AB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
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
    
    function search_spb($spb, $no_kend,$periode, $company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        if (isset($spb)){
            $spb = htmlentities($spb,ENT_QUOTES,'UTF-8');
        } else {
            $spb = "";
        }
        
        if (isset($no_kend)){
            $no_kend = strtoupper(str_replace(" ","",$this->db->escape_str(htmlentities($no_kend,ENT_QUOTES,'UTF-8')))) ;
        } else {
            $no_kend = "";
        }
        
        $where = "WHERE 1=1 AND ACTIVE=1 "; 
        if($spb!='' && $spb!='-') $where.= " AND NO_SPB LIKE '%$spb%'"; 
        if($no_kend!='' && $no_kend!='-') $where.= " AND REPLACE(TRIM(NO_KENDARAAN),' ','') LIKE '%$no_kend%'";       
        $where .= " AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(TANGGAL,'%Y%m%d')='".$periode."'";
        
        $queries = "SELECT * FROM s_nota_angkutbuah ". $where;
                    
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
        
        if( $count >0 ) {
            $sql =  $queries." ORDER BY ID_NT_AB ASC LIMIT ".$start.",".$limit."";
        } else {
            $sql =  $queries;     
        }
        //$sql = "select * FROM m_employee ".$where." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act = "";
        $no = 1;                           
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_NT_AB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));                
                                
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            
            array_push($rows, $row);
            
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    } 
    
    function update_spb_timbangan($id_timbangan,$id_nt_ab,$company,$data_post){
        $id_timbangan = trim($this->db->escape_str($id_timbangan));
        $company = trim($this->db->escape_str($company));
        $id_nt_ab = trim($this->db->escape_str($id_nt_ab));
        $no_tiket = '';//trim($this->db->escape_str($no_tiket));
        
        $status=FALSE;
        if(empty($id_timbangan)){
            $status = "id_timbangan CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_data_timbangan',array('ID_TIMBANGAN'=>$id_timbangan),'ID_TIMBANGAN');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_TIMBANGAN',$id_timbangan);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_data_timbangan',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $this->db->where('ID_NT_AB',$id_nt_ab);
                $this->db->where('COMPANY_CODE',$company);
                $this->db->set('STATUS','1');
                $this->db->update('s_nota_angkutbuah',$data_post);

                $qSP ="CALL sp_tbg_calc_timbangan(?,?,?, ?,?)";
                $stmt = mysqli_prepare($this->db->conn_id, $qSP);
                $opt=1;
                $stmt->bind_param('ssssi',$company,$no_tiket,$data_post['NO_SPB'],$data_post['NO_KENDARAAN'],$opt);
                $stmt->execute();
                //$exec_sp = $this->db->query($qSP,array($company,$no_tiket,$data_post['NO_SPB'],$data_post['NO_KENDARAAN'],'1'),TRUE);
                
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
	
    function update_data($id,$company,$data){
		$return['status']='';
        $return['error']=false;
        $id = trim($this->db->escape_str($id));
        $company = trim($this->db->escape_str($company));

		$this->db->where('ID_TIMBANGAN',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update('s_data_timbangan', $data );
		if($this->db->trans_status() == FALSE){
			$status = $this->db->_error_message();//"Error in Transactions!!";
			$return['status']=$status;
			$return['error']=true;
		}else{
			$status="Update Data ID Berhasil"."\n";
			$return['status']=$status;
			$return['error']=false;
		}    
		return $return;
    }
	
	function delete_data($id_timbang,$company){
        $id_timbang = trim($this->db->escape_str($id_timbang));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if((!empty($id_timbang) && $id_timbang==false)){
            $status = "ID_NOTA CANNOT BE NULL !!";
        }
        
        if((!empty($company) && $company==false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
		
        $cek_data_exist = $this->cek_data_exist('s_data_timbangan',array('ID_TIMBANGAN'=>$id_timbang,'COMPANY_CODE'=>$company),'ID_TIMBANGAN');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==false){
            
            $this->db->where('ID_TIMBANGAN',$id_timbang);
            $this->db->delete('s_data_timbangan');            
                        
            if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
        
    }
	
    function add_new($company,$data_post){
        $status=FALSE;
        $company = $this->db->escape_str($company);
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_data_timbangan',
                    array('ID_TIMBANGAN'=>$data_post['ID_TIMBANGAN']),'ID_TIMBANGAN');
        if ($cek_data_exist > 0){
            $status='Data Input ID telah ada di database';
        }
        
        $cek_data_exist = $this->cek_data_exist('s_data_timbangan',
                    array('NO_SPB'=>$data_post['NO_SPB']),'ID_TIMBANGAN');
        if ($cek_data_exist > 0){
            $status='Data Input NO SPB telah ada di database';
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 's_data_timbangan', $data_post );
            
            //$qSP ="CALL sp_tbg_calc_timbangan(?,?,?, ?,?)";
            //$stmt = mysqli_prepare($this->db->conn_id, $qSP);
            //$opt=1;
            //$no_tiket = '';
            //$stmt->bind_param('ssssi',$company,$no_tiket,$data_post['NO_SPB'],$data_post['NO_KENDARAAN'],$opt);
            //$stmt->execute();
                        
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
                    FROM s_data_timbangan
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
    
    function generate_xls_tbg(){
        $where = $this->wherecondition;
        $query = "SELECT TANGGALM, NO_TIKET, NO_KENDARAAN, BERAT_ISI, BERAT_KOSONG, BERAT_BERSIH, SUPPLIERCODE
                    FROM s_data_timbangan ". $where;
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
