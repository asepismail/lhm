<?php
class pms_c_daftpengajuan extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
		$this->load->model('pms/pms_m_daftpengajuan');
		$this->load->model('pms/pms_m_pengajuan');
		$this->load->model('pms/pms_m_monitoring');
		$this->load->model('pms/pms_m_master_budget');
		
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->lastmenu="main_c_pms";
		$this->load->helper('file');
		require_once(APPPATH . 'libraries/fpdf_table.php');
		require_once(APPPATH . 'libraries/header_footer.inc');
    }
	
	function index(){
      $view="pms/pms_v_daftpengajuan";
      $this->data['js'] = "";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['user_dept'] = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
      $this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
	  $this->data['company'] = $this->dropdownlist("i_company","style='width:260px;'","tabindex='1'","comp","COMPANY_CODE","COMPANY_NAME");
	  $this->data['dept'] = $this->dropdownlist("i_dept","style='width:220px;'","tabindex='1'","dept","DEPT_CODE","DEPT_DESCRIPTION");
	  $this->data['afd'] = $this->dropdownlist("i_afd","style='width:140px;'","tabindex='4'","afd","AFD_CODE","AFD_DESC");
	  $this->data['satuan'] = $this->dropdownlist("i_satuan","style='width:120px;'","tabindex='16'","sat","UNIT_CODE","UNIT_DESC");
	  $this->data['dsatuan'] = $this->dropdownlist("d_satuan","style='width:120px;'","tabindex='16'","sat","UNIT_CODE","UNIT_DESC");
	  if ($this->data['login_id'] == TRUE){
            $this->load->view($view, $this->data);
      } else {
            redirect('login');
      }
   }
   
   /* grid utama */
   function read_dppj(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$dept = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
		echo json_encode($this->pms_m_daftpengajuan->read_ppj($company, $dept));
   }
   
	/* dropdown company */
   function dropdownlist($name, $style, $tab, $type, $val, $desc){ 
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='".$name."' ".$tab." class='select' id='".$name."' ".$style." >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = "";
		if($type == "dept"){
			$data = $this->pms_m_pengajuan->get_dept();
		} else if($type == "afd"){
			$data = $this->pms_m_pengajuan->get_afd($company);
		} else if($type == "sat"){
			$data = $this->pms_m_master_budget->get_satuan();
		} else if($type == "comp"){
			$data = $this->pms_m_monitoring->get_company();
		}
		foreach ( $data as $row){
			if( (isset($default))){
				$string = $string." <option value=\"".$row[$val]."\"  selected>".$row[$desc]." </option>";
			} else {
				$string = $string." <option value=\"".$row[$val]."\">".$row[$desc]." </option>";
			}
		} 
		$string =$string. "</select>";
		return $string;
	}
	
		/* fungsi chainging di dropdown */
	function getSubtipe(){
    	$tipe = $this->uri->segment(4);
		$data_subtipe = array();
		if ($tipe == "IF"){ 
			$subtype = $this->uri->segment(5);
			$data_subtipe = $this->pms_m_pengajuan->get_fixedasset($subtype);
			foreach($data_subtipe as $row){
			$array[] = array('kt' => $row['IFTYPE_NAME'], 'kt2' => $row['IFTYPE'] );
			
			}
		} else { 
			if ($tipe == "OP"){ $data_subtipe = array('TANAM KELAPA SAWIT','LAND PREPARATION'); }
			else if ($tipe == "NS"){ $data_subtipe = array('BIBITAN'); }
			else if ($tipe == "PKS"){ $data_subtipe = array('BANGUNAN PKS','FONDASI PKS','MESIN'); }
					
			for($i=0;$i < count($data_subtipe);$i++){
				$array[] = array('kt' => $data_subtipe[$i], 'kt2' => $data_subtipe[$i] );
			}
		}
        echo json_encode($array);
    }
	
	function LoadChain()
    {
        $data_key= $this->uri->segment(4);
        $sData =strtolower(trim($data_key));
        $array=array();
        
		$data_afd = $this->pms_m_pengajuan->get_ifcode($sData);
        foreach ($data_afd as $drow){
			$array[] = array('kt' => $drow['IFSUBTYPE_NAME'], 'kt2' => $drow['IFSUBTYPE'] );
		}
        echo json_encode($array);
    }
	
	function LoadChain2()
    {
        $data_key= $this->uri->segment(4);
        $sData =strtolower(trim($data_key));
        $array=array();
        
		$data_afd = $this->pms_m_pengajuan->get_ifcode($sData);
        foreach ($data_afd as $drow){
			$array[] = array('kt' => $drow['IFSUBTYPE_NAME'], 'kt2' => $drow['IFSUBTYPE'] );
		}
        echo json_encode($array);
    }
	
	function get_activity_pj()
    {
        $type = $this->uri->segment(4);
        $subtype = str_replace(" ","",$this->uri->segment(5));
		if(trim($subtype == "TANAMKELAPASAWIT")){
			$subtype = "TN";
		} else if(trim($subtype == "LANDPREPARATION")){
			$subtype = "LC";
		}else if(trim($subtype == "BIBITAN")){
			$subtype = "NS";
		}else{
			$subtype = $subtype;
		}
		$activity=array();
        
		$data_afd = $this->pms_m_pengajuan->get_activity_pj($type, $subtype);
		foreach ($data_afd as $drow){
			 //$activity[] = '{res_id:"'.str_replace('"','\\"',$drow['ACTIVITY_CODE']).'",res_name:"'.str_replace('"','\\"',$drow['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$drow['ACTIVITY_CODE']."&nbsp;&nbsp; - &nbsp;&nbsp;" .$drow['COA_DESCRIPTION']).'",}';
			$activity[] = array('kt' => $drow['COA_DESCRIPTION'], 'kt2' => $drow['ACTIVITY_CODE'] );
		}
		echo json_encode($activity);
		//echo '['.implode(',',$activity).']'; exit;
    }
	
	function get_subaktivitas(){
    
    	$sub = str_replace('"','\\"',htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
		if ($sub == "JC" || $sub == "JT" || $sub == "JU" || $sub == "JL" || $sub == "JH"){ $data_subaktivitas = array('CUT & FILL','PEMBUATAN','PENINGGIAN','PELAPISAN','PERKERASAN'); }
		else if ($sub == "TG"){ $data_subaktivitas = array('TAHAP 1','TAHAP 2','TAHAP 3','TAHAP 4'); }
		else { $data_subaktivitas = array('tidak ada subaktivitas untuk tipe ini'); }
	
	    for($i=0;$i < count($data_subaktivitas);$i++){
			$array[] = array('kt' => $data_subaktivitas[$i], 'kt2' => $data_subaktivitas[$i] );
        }
        echo json_encode($array);
    }
	
	function get_aktivitas(){
		$sub = str_replace(" ","",$this->uri->segment(4));
		$data_subaktivitas = "";
		if ($sub == "JA"){ $data_subaktivitas = array('8111000'); }
		else if ($sub == "PR"){ $data_subaktivitas = array('8121000'); }
		else if ($sub == "BA"){ $data_subaktivitas = array('8131000'); }
		else if ($sub == "JB"){ $data_subaktivitas = array('8141000'); }
		else if ($sub == "BP" || $sub == "BS" || $sub == "BN"){ $data_subaktivitas = array('8151000'); }
		else if ($sub == "BU"){ $data_subaktivitas = array('8161000'); }
		else if ($sub == "LANDPREPARATION"){ $data_subaktivitas = array('8200000'); }
		else if ($sub == "BIBITAN"){ $data_subaktivitas = array('8301000','8301001','8301002','8301003','8301004','8301005','8301006'); }
		else if ($sub == "TANAMKELAPASAWIT"){ $data_subaktivitas = array('8401000'); }
		
		for($i=0;$i < count($data_subaktivitas);$i++){
					$array[] = array('kt' => $data_subaktivitas[$i], 'kt2' => $data_subaktivitas[$i] );
		}
		echo json_encode($array);
	}
	
	function cekPPJ(){
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$ppj = trim($this->uri->segment(4));
		$dataPj = $this->pms_m_daftpengajuan->cekPPJ($company, $ppj);
		$data = "";
		foreach($dataPj as $row){
			$data .= $row['jumlah'] . "~" . $row['PROJECT_PROPNUM_NUMID'] . "~" . 
						$row['PROJECT_PROPNUM_DATE'] . "~" . $row['PROJECT_PROPNUM_PELAKSANA'] . "~" . $row['PROJECT_DEPT']
						 . "~" . $row['PROJECT_FINISH_TARGET'];
		}
		echo $data;
	}
	
	function simpan_konfirmasi(){
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) ); 
		$data_post['PROJECT_ID'] = trim($this->input->post( 'PROJECT_ID' ) ); 
		$data_post['TGL_KONFIRMASI'] = trim($this->input->post( 'TGL_KONFIRMASI' ) ); 
		$data_post['KETERANGAN'] = trim($this->input->post( 'KETERANGAN' ) ) ; 
		$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
		$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
		$this->pms_m_daftpengajuan->insert_konfimasi( $data_post );
		$this->pms_m_daftpengajuan->update_after_konfirmasi( $ppj );
		$this->sendEmailKonfirmasi($data_post);
	}
	
	public function sendEmailKonfirmasi($data_post) {
        $config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://10.88.1.61',
			'smtp_port' => 465,
			'smtp_user' => 'moh.ridhuan@provident-agro.com',
			'smtp_pass' => 'ridhu7717',
			'smtp_auth' => TRUE
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		
		$this->email->from('moh.ridhuan@provident-agro.com', 'ridhu');
		$this->email->to('moh.ridhuan@provident-agro.com');
		
		$this->email->subject('Konfirmasi kirim data.....');
		$this->email->message('konfirmasi kirim data project dengan nomor ');
	
		
		if (!$this->email->send())
			show_error($this->email->print_debugger());
		else
			echo '';   
    }
	
	/* fungsi untuk approval */
	
	function simpan_approval(){
		$type = trim($this->input->post( 'type' ) );
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) ); 
		$company = trim($this->input->post( 'company' ) );
		$data_post['PROJECT_ID'] = trim($this->input->post( 'PROJECT_ID' ) ); 
		$data_post['TGL_APPROVAL'] = trim($this->input->post( 'TGL_KONFIRMASI' ) ); 
		$data_post['KETERANGAN'] = trim($this->input->post( 'KETERANGAN' ) ) ; 
		$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
		$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
		
		if($type == "appr_kebun") {
			$this->pms_m_daftpengajuan->insert_approval_kebun( $data_post );
			$this->pms_m_daftpengajuan->update_after_approval_kebun( $ppj);
			//$this->sendEmailAppr1($data_post);
		} else if($type == "appr0") {
			$this->pms_m_daftpengajuan->insert_approval0( $data_post );
			$this->pms_m_daftpengajuan->update_after_approval0( $ppj);
			//$this->sendEmailAppr1($data_post);
		} else if($type == "appr1") {
			$this->pms_m_daftpengajuan->insert_approval1( $data_post );
			$this->pms_m_daftpengajuan->update_after_approval1( $ppj);
			//$this->sendEmailAppr1($data_post);
		} else if($type == "appr2") {
			$this->pms_m_daftpengajuan->insert_approval2( $data_post );
			$this->pms_m_daftpengajuan->update_after_approval2( $ppj);
			$this->approvetopj( $ppj , $data_post['KETERANGAN'], $company);
			//$this->sendEmailAppr2($data_post);
		}
		//$this->sendEmail();
	}
	
	public function sendEmailAppr1($data_post) {
        $config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://10.88.1.61',
			'smtp_port' => 465,
			'smtp_user' => 'moh.ridhuan@provident-agro.com',
			'smtp_pass' => 'ridhu7717',
			'smtp_auth' => TRUE
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		
		$this->email->from('moh.ridhuan@provident-agro.com', 'ridhu');
		$this->email->to('moh.ridhuan@provident-agro.com');
		
		$this->email->subject('Konfirmasi Persetujuan Project');
		$this->email->message('konfirmasi Persetujuan project 1');
		
		if (!$this->email->send())
			show_error($this->email->print_debugger());
		else
			echo ''; 
    }
	
	public function sendEmailAppr2($data_post) {
        $config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://10.88.1.61',
			'smtp_port' => 465,
			'smtp_user' => 'moh.ridhuan@provident-agro.com',
			'smtp_pass' => 'ridhu7717',
			'smtp_auth' => TRUE
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		
		$this->email->from('moh.ridhuan@provident-agro.com', 'ridhu');
		$this->email->to('moh.ridhuan@provident-agro.com');
		
		$this->email->subject('Konfirmasi Penyelesaian Pengajuan');
		$this->email->message('konfirmasi project dengan nomor ');
			
		if (!$this->email->send())
			show_error($this->email->print_debugger());
		else
			echo ''; 
    }
	
	function approvetopj($nopengajuan, $notes, $company){
		$this->load->database();
		if($company == "LIH"){
			$noperusahaan = "13";
		} else if($company == "MIA"){
			$noperusahaan = "11";
		} else if($company == "MSS"){
			$noperusahaan = "15";
		} else if($company == "SSS"){
			$noperusahaan = "14";
		} else if($company == "SAP"){
			$noperusahaan = "16";
		} else if($company == "TPAI"){
			$noperusahaan = "17";
		} else if($company == "SML"){
			$noperusahaan = "19";
		} else if($company == "GKM"){
			$noperusahaan = "18";
		} else if($company == "ASL"){
			$noperusahaan = "20";
		}
		$status = "selesai";
		
		$query = $this->db->query("UPDATE pms_project_propnum SET PROP_STATUS = 'disetujui', 
												 ISAPPROVE = 1,
												 APPROVEDATE = CURDATE()
												WHERE PROJECT_PROPNUM_NUMID = '".$nopengajuan."'  
												AND COMPANY_CODE = '".$company."'");
				
				$query2 = $this->db->query("UPDATE pms_project_proposal SET PROP_STATUS = 7, 
												 PROJECT_PROP_STATUS = 'disetujui',
												 ISAPPROVE = 1,
												 APPROVEDATE = CURDATE()
												WHERE PROJECT_PROPNUM_NUMID = '".$nopengajuan."'  
												AND PROJECT_PROP_COMPANY = '".$company."'"); 
												
				$this->db->select('PROJECT_PROPNUM_NUMID,PROJECT_ID,PROJECT_PROP_AFD,PROJECT_PROP_DESC,PROJECT_PROP_TYPE,PROJECT_PROP_SUBTYPE,
	 							PROJECT_PROP_LOCATION,PROJECT_PROP_ACTIVITY,PROJECT_PROP_SUBACTIVITY,PROJECT_PROP_PELAKSANA,PROJECT_PROP_QTY,
							  	PROJECT_PROP_UOM,PROJECT_PROP_VALUE,PROJECT_PROP_TVALUE,PROJECT_PROP_STATUS,PROJECT_PROP_COMPANY');
				$this->db->where('PROJECT_PROPNUM_NUMID', $nopengajuan);
				$this->db->from('pms_project_proposal');
				$values = "";
				
				$query3 = $this->db->get();
				foreach($query3->result() as $data){
					$pjlama = $data->PROJECT_ID;
					//$noperusahaan = '13';
					$begID = "PJ".$noperusahaan;
					$nopj = $this->global_func->create_nopj('m_project','PROJECT_ID',$begID);
					$values = "'".$data->PROJECT_PROP_AFD."','";
					$values .= $data->PROJECT_PROP_TYPE."','";
					$values .= $data->PROJECT_PROP_SUBTYPE."','";
					$values .= $data->PROJECT_PROP_DESC."','";
					$values .= $data->PROJECT_PROP_LOCATION."','";
					$values .= $data->PROJECT_PROP_ACTIVITY."','";
					$values .= $data->PROJECT_PROP_SUBACTIVITY."','";
					$values .= $data->PROJECT_PROP_PELAKSANA."','','";
					$values .= $data->PROJECT_PROP_QTY."','";
					$values .= $data->PROJECT_PROP_UOM."','";
					$values .= $data->PROJECT_PROP_VALUE."','";
					$values .= $data->PROJECT_PROP_TVALUE."',";
					$values .= "1,'";
					$values .= $company."'";
					
	$query4 = $this->db->query("INSERT INTO pms_project_prop_hist (PROJECT_ID,PROJECT_PROP_NUM, PROJECT_PROP_ID,HISTORY,ACTION) 
								VALUES ('".$nopj."','".$nopengajuan."','".$pjlama ."','".$notes."','".$status."')");
	$query5 = $this->db->query("INSERT INTO m_project 
								(PROJECT_ID, AFD, PROJECT_TYPE, PROJECT_SUBTYPE, PROJECT_DESC, PROJECT_LOCATION,
								PROJECT_ACTIVITY,PROJECT_SUB_ACTIVITY,KODE_PELAKSANA,SPK,PROJECT_QTY,
								PROJECT_UOM,PROJECT_VALUE,PROJECT_NETTVAL,
								PROJECT_STATUS,COMPANY_CODE) VALUES ('".$nopj."',".$values.")");
					} 
	}
	
	/* grid utama */
    function read_pendukung(){
		$projectid = $this->uri->segment(4);
		echo json_encode($this->pms_m_daftpengajuan->read_pendukung($projectid));
    }
	
	function insert_header_document(){
		$project = trim($this->input->post( 'PROJECT_ID' ) );
		$tgl = trim($this->input->post( 'TGL_KONFIRMASI' ) ); 
		$data_post['PROJECT_ID'] = $project;
		$data_post['TGL_KONFIRMASI'] = $tgl;
		$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
		$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
		$cekdata = $this->pms_m_daftpengajuan->cek_konfirmasi($project, $tgl);
		$cek = 0;
		foreach($cekdata as $row){
			$cek = $row['jumlah'];
		}
		if($cek > 0){
			$id = $this->pms_m_daftpengajuan->update_konfimasi( $project, $tgl, $data_post  );
			echo $id;
		} else {
			$id = $this->pms_m_daftpengajuan->insert_konfimasi( $data_post );
			echo $id;
		}
	}
	
	function insert_detail_document(){
		$idkonf = trim($this->input->post( 'ID_KONFIRMASI' ) );
		$jnsdata = trim($this->input->post( 'JNS_DATA' ) );
		$data_post['ID_KONFIRMASI'] =  $idkonf;
		$data_post['JNS_DATA'] = $jnsdata;
		$data_post['DESKRIPSI'] = $this->input->post( 'DESKRIPSI' );
		$data_post['ISVALID'] = 1;
		
		$cekdata = $this->pms_m_daftpengajuan->cek_detail_konfirmasi($idkonf, $jnsdata);
		$cek = 0;
		foreach($cekdata as $row){
			$cek = $row['jumlah'];
		}
		if($cek > 0){
			$id = $this->pms_m_daftpengajuan->update_detail_konfimasi( $idkonf, $jnsdata, $data_post  );
			echo $id;
		} else {
			$id = $this->pms_m_daftpengajuan->insert_detail_konfimasi( $data_post );
			echo $id;
		}
	}
	
	function delete_detail_document(){
		$id = $this->uri->segment(4);
		$this->pms_m_daftpengajuan->delete_detail_konfimasi( $id  );
		
	}
	
	/* flow approval */
	function read_flow_approval(){
		$projectid = $this->uri->segment(4);
		echo json_encode($this->pms_m_daftpengajuan->read_flow_appr($projectid));
    }
	/* end flow approval */
}

?>