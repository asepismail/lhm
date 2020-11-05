<?php
class pms_c_pengajuan extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('pms/pms_m_pengajuan');
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
    }
	
	function index(){
      $view="pms/pms_v_pengajuan";
      $this->data['js'] = "";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['user_dept'] = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
      $this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
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
	
	function dropdownlist($name, $style, $tab, $type, $val, $desc)
	{
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
		
	function cekNotComplete(){
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$dataPj = $this->pms_m_pengajuan->cekNotComplete($company);
		$data = "";
		foreach($dataPj as $row){
			$data .= $row['jumlah'] . "~" . $row['PROJECT_PROPNUM_NUMID'] . "~" . 
						$row['PROJECT_PROPNUM_DATE'] . "~" . $row['PROJECT_PROPNUM_PELAKSANA'] . "~" . $row['PROJECT_DEPT']
						 . "~" . $row['PROJECT_FINISH_TARGET'];
		}
		echo $data;
	}
	
	/* grid utama */
    function read_ppj()
    {
		$ppj= $this->uri->segment(4);
        echo json_encode($this->pms_m_pengajuan->read_ppj($ppj));
    }
	
	/* fungsi controller CRUD Header*/
	function insert_header(){
		$company = $this->session->userdata('DCOMPANY');
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) ); 
        $data_post['PROJECT_PROPNUM_NUMID'] = $ppj;
		$data_post['PROJECT_DEPT'] = trim($this->input->post( 'PROJECT_DEPT' ) ); 
		$data_post['PROJECT_PROPNUM_DATE'] = trim($this->input->post( 'PROJECT_PROPNUM_DATE' ) ) ; 
		//$data_post['PROJECT_FINISH_TARGET'] = trim($this->input->post( 'PROJECT_FINISH_TARGET' ) ); 
		$data_post['PROJECT_PROPNUM_PELAKSANA'] = trim($this->input->post( 'PROJECT_PROPNUM_PELAKSANA' ) ); 
		$data_post['COMPANY_CODE'] = trim($company); 
		
		$dataHeader = $this->pms_m_pengajuan->cek_header($company, $ppj);
		$cekdata = count($dataHeader);
		if($cekdata > 0){
			$data_post['UPDATEBY'] = $this->session->userdata('LOGINID');
			$data_post['UPDATEDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->update_header( $ppj, $data_post );
		} else {
			$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
			$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->insert_header( $data_post );
		}
	}
	
	function cancel_header(){
		$company = $this->session->userdata('DCOMPANY');
		$user = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) ); 
		$insert_id = $this->pms_m_pengajuan->cancel_header( $ppj, $user );
	}
	/* end fungsi controller CRUD Header*/
	
	/* fungsi controller CRUD Detail Header*/
	function insert_detail(){
		$company = $this->session->userdata('DCOMPANY');
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) ); 
		$pjs = trim($this->input->post( 'PROJECT_ID' ) ); 
        $data_post['PROJECT_PROPNUM_NUMID'] = $ppj;
		$data_post['PROJECT_ID'] = $pjs; 
		$data_post['PROJECT_PROP_AFD'] = trim($this->input->post( 'PROJECT_PROP_AFD' ) ) ; 
		$data_post['PROJECT_PROP_TYPE'] = trim($this->input->post( 'PROJECT_PROP_TYPE' ) ); 
		$data_post['PROJECT_PROP_SUBTYPE'] = trim($this->input->post( 'PROJECT_PROP_SUBTYPE' ) ); 
	 	$data_post['PROJECT_PROP_IFTYPE'] = trim($this->input->post( 'PROJECT_PROP_IFTYPE' ) ); 
		$data_post['PROJECT_PROP_LOCATION'] = $this->input->post( 'PROJECT_PROP_LOCATION' ) ; 
		$data_post['PROJECT_PROP_ACTIVITY'] = trim($this->input->post( 'PROJECT_PROP_ACTIVITY' ) ) ; 
		$data_post['PROJECT_PROP_SUBACTIVITY'] = $this->input->post( 'PROJECT_PROP_SUBACTIVITY' ); 
		$data_post['PROJECT_PROP_DESC'] = $this->input->post( 'PROJECT_PROP_DESC' ); 
		$data_post['PROJECT_PROP_START'] = trim($this->input->post( 'PROJECT_PROP_START' ) ); 
		$data_post['PROJECT_PROP_END'] = trim($this->input->post( 'PROJECT_PROP_END' ) ); 
		$data_post['PROJECT_PROP_QTY'] = trim($this->input->post( 'PROJECT_PROP_QTY' ) ) ; 
		$data_post['PROJECT_PROP_UOM'] = trim($this->input->post( 'PROJECT_PROP_UOM' ) ); 
		$data_post['PROJECT_PROP_VALUE'] = trim($this->input->post( 'PROJECT_PROP_VALUE' ) );
		$data_post['PROJECT_PROP_TVALUE'] = trim($this->input->post( 'PROJECT_PROP_TVALUE' ) );
		$data_post['ISDETAIL'] = trim($this->input->post( 'ISDETAIL' ) );
		$data_post['PROP_TYPE'] = 'baru';
			
		$dataDetail = $this->pms_m_pengajuan->cek_detail($company, $ppj, $pjs);
		$cekdata = count($dataDetail);
		if($cekdata > 0){
			$data_post['UPDATEBY'] = $this->session->userdata('LOGINID');
			$data_post['UPDATEDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->update_detail( $ppj, $pjs,  $data_post );
		} else {
			$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
			$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->insert_detail( $data_post );
			/* if ($res) {
				echo "1";
			} else {
				echo "0";
			} */
		}
	}
	
	function cancel_detail(){
		$company = $this->session->userdata('DCOMPANY');
		$user = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) );
		$pjs = trim($this->input->post( 'PROJECT_ID' ) ); 
		$insert_id = $this->pms_m_pengajuan->cancel_detail( $ppj, $pjs, $user );
	}
	/* end fungsi controller CRUD Detail Header*/
	
	/* fungsi controller CRUD Detail Activity*/
	function insert_detail_act(){
		$company = $this->session->userdata('DCOMPANY');
		$pjs = trim($this->input->post( 'DPROJECT_ID' ) ); 
		$activity =  trim($this->input->post( 'DPROJECT_PROP_ACTIVITY' ) );
       	$data_post['DPROJECT_ID'] = $pjs; 
		$data_post['DPROJECT_PROP_ACTIVITY'] = $activity;
		$data_post['DPROJECT_PROP_QTY'] = trim($this->input->post( 'DPROJECT_PROP_QTY' ) ); 
		$data_post['DPROJECT_PROP_UOM'] = trim($this->input->post( 'DPROJECT_PROP_UOM' ) ); 
	 	$data_post['DPROJECT_PROP_VALUE'] = trim($this->input->post( 'DPROJECT_PROP_VALUE' ) ); 
		$data_post['DPROJECT_PROP_TVALUE'] = $this->input->post( 'DPROJECT_PROP_TVALUE' ) ; 
			
		$dataDetail = $this->pms_m_pengajuan->cek_detail_act($pjs, $activity);
		$cekdata = count($dataDetail);
		if($cekdata > 0){
			$data_post['UPDATEBY'] = $this->session->userdata('LOGINID');
			$data_post['UPDATEDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->update_detail_act( $pjs, $activity,  $data_post );
			echo $insert_id;
		} else {
			$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
			$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->insert_detail_act( $pjs, $data_post );
			echo $insert_id;
			/* if ($res) {
				echo "1";
			} else {
				echo "0";
			} */
		}
	}
	
	function delete_detail_act(){
		$id = trim( $this->uri->segment(4) );
		$pjs = trim( $this->uri->segment(5) );
		$delete_id = $this->pms_m_pengajuan->delete_detail( $id, $pjs );
		echo $delete_id;
	}
	
	function cancel_detail_act(){
		$company = $this->session->userdata('DCOMPANY');
		$user = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) );
		$pjs = trim($this->input->post( 'PROJECT_ID' ) ); 
		$insert_id = $this->pms_m_pengajuan->cancel_detail( $ppj, $pjs, $user );
	}
	/* end fungsi controller CRUD Detail Activity */
	
	function read_detail_ppj()
    {
		$pjs= $this->uri->segment(4);
        echo json_encode($this->pms_m_pengajuan->read_detail_ppj($pjs));
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
		else if ($sub == "BIBITAN"){ $data_subaktivitas = array('8301000','8301001','8301002','8301003','8301004','8301005','8301006'); 		
		} else if ($sub == "TANAMKELAPASAWIT"){ $data_subaktivitas = array('8401000'); 
		} else if ($sub == "BANGUNANPKS"){ $data_subaktivitas = array('8199100'); 
		} else if ($sub == "FONDASIPKS"){ $data_subaktivitas = array('8199200'); 
		} else if ($sub == "MESIN"){ $data_subaktivitas = array('8199300'); }
		
		for($i=0;$i < count($data_subaktivitas);$i++){
					$array[] = array('kt' => $data_subaktivitas[$i], 'kt2' => $data_subaktivitas[$i] );
		}
		echo json_encode($array);
	}
	
	/* ########### generate no pengajuan & project sementara*/
	function ext_genNoPengajuan(){
		$company = $this->session->userdata('DCOMPANY');
		$noperusahaan = '';
	    $coid = $this->pms_m_pengajuan->get_company_id($company);
        foreach ($coid as $drow){
			$noperusahaan = $drow['COMPANY_NUMBER'];
		}
		$begID = "PPJ-".$noperusahaan.substr(date('Y'),2,2);
		$nopengajuan = $this->global_func->id_GAD('pms_project_propnum','PROJECT_PROPNUM_NUMID',$begID);
		echo trim($nopengajuan);   
    }
	
	function ext_genPJS(){
		$company = $this->session->userdata('DCOMPANY');
		$noperusahaan = '';
	    $coid = $this->pms_m_pengajuan->get_company_id($company);
        foreach ($coid as $drow){
			$noperusahaan = $drow['COMPANY_NUMBER'];
		}
		$begID = "PJ".$noperusahaan.substr(date('Y'),2,2);
		$noproject = $this->global_func->id_GAD('pms_project_proposal','PROJECT_ID',$begID);
		echo trim($noproject);  
	}
	
	function selesai(){
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) ); 
		$insert_id = $this->pms_m_pengajuan->selesai( $ppj );
	}
	
	/* ######### validasi ############ */
	function validasi_lokasi($type, $afd, $loc){
		$status = "";
		if($type == "OP"){
			$block = substr($loc, 0, 2);
			if($afd != $block){
				$status .= "lokasi block tidak sama dengan afdeling..";
			}
		} else {
			
		}
		return $status;
	}
	/* ######### end validasi ######## */
	
	/* ######### ambil blok ########## */
	function read_blok()
    {
		$company = $this->session->userdata('DCOMPANY');
		$afd= $this->uri->segment(4);
		$q = $this->uri->segment(5);
		echo json_encode($this->pms_m_pengajuan->getblok($company, $afd, $q));
    }
	
	/* ################ insert log pengajuan ###################*/
	function insert_log_koreksi(){
		$company = $this->session->userdata('DCOMPANY');
		$sLog = trim($this->input->post( 'PROJECT_PROP_PELAKSANA' ) );
		$sLog .= "~".trim($this->input->post( 'PROJECT_PROP_AFD' ) )."~".trim($this->input->post( 'PROJECT_PROP_TYPE' ) );
		$sLog .= "~".trim($this->input->post( 'PROJECT_PROP_SUBTYPE' ) )."~".trim($this->input->post( 'PROJECT_PROP_IFTYPE' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_LOCATION'))."~".trim($this->input->post('PROJECT_PROP_ACTIVITY'));
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_SUBACTIVITY'))."~".trim($this->input->post( 'PROJECT_PROP_DESC' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_START'))."~".trim($this->input->post( 'PROJECT_PROP_END' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_QTY'))."~".trim($this->input->post( 'PROJECT_PROP_UOM' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_VALUE'))."~".trim($this->input->post( 'PROJECT_PROP_TVALUE' ) );
		$sLog .= "~".trim($this->input->post('ISDETAIL'));
		$data_post['PROJECT_PROP_NUM'] = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) );
		$data_post['PROJECT_PROP_ID'] = trim($this->input->post( 'PROJECT_ID' ) ); 
		$data_post['LOG_BEFORE'] = $sLog; 
	 	$data_post['ACTION'] = "revisi"; 	
		$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
		$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
		$insert_id = $this->pms_m_pengajuan->insert_log_pengajuan( $data_post );
		echo $insert_id;
	}
	
	function update_log_koreksi(){
		$company = $this->session->userdata('DCOMPANY');
		$logID = trim($this->input->post( 'IDLOG' ) );
		$sLog = trim($this->input->post( 'PROJECT_PROP_PELAKSANA' ) );
		$sLog .= "~".trim($this->input->post( 'PROJECT_PROP_AFD' ) )."~".trim($this->input->post( 'PROJECT_PROP_TYPE' ) );
		$sLog .= "~".trim($this->input->post( 'PROJECT_PROP_SUBTYPE' ) )."~".trim($this->input->post( 'PROJECT_PROP_IFTYPE' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_LOCATION'))."~".trim($this->input->post('PROJECT_PROP_ACTIVITY'));
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_SUBACTIVITY'))."~".trim($this->input->post( 'PROJECT_PROP_DESC' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_START'))."~".trim($this->input->post( 'PROJECT_PROP_END' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_QTY'))."~".trim($this->input->post( 'PROJECT_PROP_UOM' ) );
		$sLog .= "~".trim($this->input->post('PROJECT_PROP_VALUE'))."~".trim($this->input->post( 'PROJECT_PROP_TVALUE' ) );
		$sLog .= "~".trim($this->input->post('ISDETAIL'));
		$data_post['LOG_AFTER'] = $sLog; 	
		$insert_id = $this->pms_m_pengajuan->update_log_pengajuan( $logID, $data_post['LOG_AFTER'] );
	}
	
	function delete_log_koreksi(){
		$logID = trim($this->input->post( 'IDLOG' ) );
		$insert_id = $this->pms_m_pengajuan->delete_log_pengajuan( $logID );
	}
	
	/* ########### end inserting log ####################### */
	
	/* grid utama */
    function read_attachment()
    {
		$ppj= $this->uri->segment(4);
        echo json_encode($this->pms_m_pengajuan->read_detail_attacment($ppj));
    }
}

?>