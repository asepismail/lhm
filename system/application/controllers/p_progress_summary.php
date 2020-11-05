<?php
class p_progress_summary extends Controller{
	
    function __construct(){
        parent::__construct();
		$this->company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$this->user=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->load->model('model_p_progress_summary');
        $this->load->model('model_c_user_auth');
		$this->load->helper('form');
        $this->load->helper('language');
        $this->load->database(); 
		$this->lastmenu="p_progress_summary";
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->plugin('to_excel');
		require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
		
    }
	
	function index()
    {
        $view = "info_p_progress_summary";
        $data = array();
        $data['judul_header'] = "Summary Data Progress";
        $data['js'] = "";
		
		
		$user = htmlentities($this->session->userdata('LOGINID'));
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'));
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'));
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select',0,'gridReload()');
		$data['acttype'] = $this->dropdownlist_act();
		$data['afdeling'] = $this->global_func->dropdownlist_afdeling('proafd','gridReload()',$this->company);
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }   
	  
	function submit(){
		//$arr = array('status' => 1, 'message' => "salahhhhh");
		//return 'success';
	  $qty = 0;
	  $qty2 = 0;
	  
	  $data_post['PROGSUM_ID'] = htmlentities(trim($this->input->post( 'id' )),ENT_QUOTES,'UTF-8');
	 
	  if($this->input->post( 'QTY1_PENYESUAIAN' ) != ""){
	  	 	$data_post['QTY1_PENYESUAIAN'] = htmlentities(trim($this->input->post( 'QTY1_PENYESUAIAN' )),ENT_QUOTES,'UTF-8');
	  }
	  
	  if($this->input->post( 'QTY2_PENYESUAIAN' ) != ""){
	   		$data_post['QTY2_PENYESUAIAN'] = htmlentities(trim($this->input->post( 'QTY2_PENYESUAIAN' )),ENT_QUOTES,'UTF-8');
		  }
	  
	 	
	  $insert_id = $this->model_p_progress_summary->update_progress( $data_post['PROGSUM_ID'], $data_post );
	  echo $insert_id;
	}
	
	function cek(){
		$result = "";
		$cellname = $this->uri->segment(3);
		$id = $this->uri->segment(4);
		//$id = htmlentities(trim($this->input->post( 'id' )),ENT_QUOTES,'UTF-8');
		$value = $this->model_p_progress_summary->cekProgressValue( $id, $cellname );
		foreach ( $value as $row)
		{
			$result = $row[$cellname];
		}
		echo $result;
	}
	
	function generateData(){
		$periode = $this->uri->segment(3);
		
		$data = $this->model_p_progress_summary->generateData($this->company,$periode,$this->user);
		$result = "";
		foreach ( $data as $row)
		{
			$result .= $row['JUMLAH'];
		}
		
		echo $result . " Data progress berhasil digenerate";
	}
	/* grid utama */
    function read_grid()
    {
		$periode = $this->uri->segment(3);
		$act = $this->uri->segment(4);
		$afd = $this->uri->segment(5);
		
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_p_progress_summary->LoadData($company, $periode, $act, $afd));
    }
    
	function dropdownlist_act()
    {
    	$string = "<select name='actype' class='select'  id='actype' onchange='gridReload()' style='width:280px' >";
        $string .= "<option value='all'> -- semua -- </option>";
        $data_act = $this->model_p_progress_summary->getcoatype();
        
        foreach ( $data_act as $row){
          if( (isset($default)) && ($default==$row[$nama_isi]) ){
             $string = $string."<option value=\"".$row['COA_PARENT']."\" selected>".$row['COA_ACCOUNTTYPE']." </option>";
          } else {
             $string = $string."<option value=\"".$row['COA_PARENT']."\">".$row['COA_ACCOUNTTYPE']." </option>";
		  }
        }
        
        $string =$string. "</select>";
        return $string;
    } 
	
	//Export data
    function create_excel()
    {
        $periode = $this->uri->segment(3);
		$act = $this->uri->segment(4);
		$afd = $this->uri->segment(5);
		
        $data_progress = $this->model_p_progress_summary->gen_progress($this->company, $periode, $act, $afd);
        $judul = '';   $headers = ''; 
		$data = '';  $footer = '';
        
        $obj =& get_instance();
        			
        $judul .= htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8'). "\n";
        $judul .= "SUMMARY PROGRESS DATA"."\n";
        $judul .= "PERIODE : \t".$periode."\n";
        
        $headers .= "ID \t";
        $headers .= "PERUSAHAAN \t";
        $headers .= "PERIODE \t";
        $headers .= "KODE AKTIVITAS \t";
        $headers .= "DEKSRIPSI AKTIVITAS \t";
        $headers .= "KODE LOKASI \t";
        $headers .= "DESKRIPSI LOKASI \t";
        $headers .= "QTY1_LHM \t";
        $headers .= "QTY1_BKE \t";
        $headers .= "QTY1_BKT \t";  
        $headers .= "QTY1_TOTAL \t";
        $headers .= "QTY1_PENYESUAIAN \t";
	 $headers .= "QTY1_FINAL \t";
 	 $headers .= "UNIT1 \t";
        $headers .= "QTY2_LHM \t";
        $headers .= "QTY2_BKE \t";
        $headers .= "QTY2_BKT \t";
       
	 $headers .= "QTY2_TOTAL \t";
        $headers .= "QTY2_PENYESUAIAN \t";
	 $headers .= "QTY2_FINAL \t";
 	 $headers .= "UNIT2 \t";
	 $headers .= "BLOCKTYPE \t";
	
        foreach ( $data_progress as $row){
            $line = '';
            $line .= str_replace('"', '""',$row['PROGSUM_ID'])."\t";
            $line .= str_replace('"', '""',$row['COMPANY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['PERIODE'])."\t";
            $line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['ACTIVITY_DESC'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_DESC'])."\t";
            $line .= str_replace('"', '""',$row['QTY1_LHM'])."\t";  
            $line .= str_replace('"', '""',$row['QTY1_BKE'])."\t";
            $line .= str_replace('"', '""',$row['QTY1_BKT'])."\t";
            
	     $line .= str_replace('"', '""',$row['TOTAL1'])."\t";
            $line .= str_replace('"', '""',$row['QTY1_PENYESUAIAN'])."\t";
	     $line .= str_replace('"', '""',$row['FINAL1'])."\t";
	     $line .= str_replace('"', '""',$row['UNIT1'])."\t";
            $line .= str_replace('"', '""',$row['QTY2_LHM'])."\t";
            $line .= str_replace('"', '""',$row['QTY2_BKE'])."\t";
            $line .= str_replace('"', '""',$row['QTY2_BKT'])."\t";
			
	     $line .= str_replace('"', '""',$row['TOTAL2'])."\t";
	     $line .= str_replace('"', '""',$row['QTY2_PENYESUAIAN'])."\t";
	     $line .= str_replace('"', '""',$row['FINAL2'])."\t";
	     $line .= str_replace('"', '""',$row['UNIT2'])."\t";
	     $line .= str_replace('"', '""',$row['BLOCKTYPE'])."\t";

            $data .= trim($line)."\n";        
        }
        
        $data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=SUMPROGRESS_".$this->company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
    }

	function create_excel4uat()
    {
        $periode = $this->uri->segment(3);
		$act = $this->uri->segment(4);
		$afd = $this->uri->segment(5);
		
        $data_progress = $this->model_p_progress_summary->gen_progress($this->company, $periode, $act, $afd);
        $judul = '';   $headers = ''; 
		$data = '';  $footer = '';
        
        $obj =& get_instance();
        			
        $judul .= htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8'). "\n";
        $judul .= "SUMMARY PROGRESS DATA"."\n";
        $judul .= "PERIODE : \t".$periode."\n";
        
        $headers .= "ID \t";
        $headers .= "PERUSAHAAN \t";
        $headers .= "PERIODE \t";
        $headers .= "KODE AKTIVITAS \t";
        $headers .= "DEKSRIPSI AKTIVITAS \t";
        $headers .= "KODE LOKASI \t";
        $headers .= "DESKRIPSI LOKASI \t";
		$headers .= "HK ( ORANG ) \t";
        $headers .= "QTY1_LHM \t";
        $headers .= "QTY1_BKE \t";
        $headers .= "QTY1_BKT \t";  
        $headers .= "UNIT1 \t";
		$headers .= "QTY1_TOTAL \t";
        $headers .= "QTY1_PENYESUAIAN \t";
		$headers .= "QTY1_FINAL \t";
        $headers .= "QTY2_LHM \t";
        $headers .= "QTY2_BKE \t";
        $headers .= "QTY2_BKT \t";
        $headers .= "UNIT2 \t";
		$headers .= "QTY2_TOTAL \t";
        $headers .= "QTY2_PENYESUAIAN \t";
		$headers .= "QTY1_FINAL \t";
		
        foreach ( $data_progress as $row){
            $line = '';
            $line .= str_replace('"', '""',$row['PROGSUM_ID'])."\t";
            $line .= str_replace('"', '""',$row['COMPANY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['PERIODE'])."\t";
            $line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['ACTIVITY_DESC'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_DESC'])."\t";
			$line .= str_replace('"', '""',$row['JMLHK'])."\t";
            $line .= str_replace('"', '""',$row['QTY1_LHM'])."\t";  
            $line .= str_replace('"', '""',$row['QTY1_BKE'])."\t";
            $line .= str_replace('"', '""',$row['QTY1_BKT'])."\t";
            $line .= str_replace('"', '""',$row['UNIT1'])."\t";
			$line .= str_replace('"', '""',$row['TOTAL1'])."\t";
            $line .= str_replace('"', '""',$row['QTY1_PENYESUAIAN'])."\t";
			$line .= str_replace('"', '""',$row['FINAL1'])."\t";
            $line .= str_replace('"', '""',$row['QTY2_LHM'])."\t";
            $line .= str_replace('"', '""',$row['QTY2_BKE'])."\t";
            $line .= str_replace('"', '""',$row['QTY2_BKT'])."\t";
			$line .= str_replace('"', '""',$row['UNIT2'])."\t";
			$line .= str_replace('"', '""',$row['TOTAL2'])."\t";
			$line .= str_replace('"', '""',$row['QTY2_PENYESUAIAN'])."\t";
			$line .= str_replace('"', '""',$row['FINAL2'])."\t";
            $data .= trim($line)."\n";        
        }
        
       $data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=SUMPROGRESS_".$this->company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
    }
}

?>