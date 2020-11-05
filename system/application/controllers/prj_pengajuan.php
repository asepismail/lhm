<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class prj_Pengajuan extends Controller
{
    function __construct()
    {
        parent::Controller();
        $this->load->model( 'model_project_pengajuan' ); 
		$this->load->model('model_c_user_auth');
		$this->lastmenu="prj_pengajuan";
        $this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->library('form/formheader');
		$this->load->database();
		$this->load->plugin('to_excel');
    }
	
    function index()
    {
		$view = "info_prjpengajuan";
		$data = array();
		$data['judul_header'] = "Pengajuan Project";
		$data['js'] = "";
        	$data['login_id'] = $this->session->userdata('LOGINID');
      		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['afd'] = $this->dropdownlist_afd();
		$data['safd'] = $this->dropdownlist_safd();
        
		$data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
		if ($data['login_id'] == TRUE && $data['user_level'] == 'SAD' || $data['user_level'] == 'ADMHO' || $data['user_level'] == 'MTBUDGET'){
			show($view, $data);
		} else {
			redirect('login');
		}
    }
    
    function AddNew()
    {
        $act ="add";
        $company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY'))); 
    
        $data_post['PROJECT_ID']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
		$pjid = htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
        $data_post['AFD']=htmlentities(mysql_escape_string($this->input->post('AFD')));
        $data_post['PROJECT_TYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_TYPE')));
        $data_post['PROJECT_SUBTYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_SUBTYPE')));
		$data_post['PROJECT_SUB_ACTIVITY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_SUB_ACTIVITY')));
        $data_post['PROJECT_DESC']=htmlentities(mysql_escape_string($this->input->post('PROJECT_DESC')));
		$data_post['PROJECT_LOCATION']=htmlentities(mysql_escape_string($this->input->post('PROJECT_LOCATION')));
		$data_post['PROJECT_ACTIVITY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ACTIVITY')));
		$act = htmlentities(mysql_escape_string($this->input->post('PROJECT_ACTIVITY')));
		$data_post['KODE_PELAKSANA']=htmlentities(mysql_escape_string($this->input->post('KODE_PELAKSANA')));
		$data_post['SPK']=htmlentities(mysql_escape_string($this->input->post('SPK')));
        $data_post['PROJECT_START']=htmlentities(mysql_escape_string($this->input->post('PROJECT_START')));
        $data_post['PROJECT_END']=htmlentities(mysql_escape_string($this->input->post('PROJECT_END')));
        $data_post['PROJECT_STATUS']=htmlentities(mysql_escape_string($this->input->post('PROJECT_STATUS')));
		$data_post['PROJECT_QTY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_QTY')));
		$data_post['PROJECT_UOM']=htmlentities(mysql_escape_string($this->input->post('PROJECT_UOM')));
		$data_post['PROJECT_VALUE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_VALUE')));
		$data_post['PROJECT_PPN']=htmlentities(mysql_escape_string($this->input->post('PROJECT_PPN')));
		$data_post['PROJECT_NETTVAL']=htmlentities(mysql_escape_string($this->input->post('PROJECT_NETTVAL')));
		$data_post['PROJECT_STATUS']=htmlentities(mysql_escape_string($this->input->post('PROJECT_STATUS')));
		$data_post['TGL_TERBIT']=htmlentities(mysql_escape_string($this->input->post('TGL_TERBIT')));
        $data_post['COMPANY_CODE']=$company;
		$data_post['INPUT_BY']=htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        $data_post['INPUT_DATE']=date ("Y-m-d H:i:s");
        if(isset($act)) {
            $data_post['Action'] =$act;    
        } else {
            $data_post['Action'] ="";
        }
		
		$data_post_detail['MASTER_PROJECT_ID']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
		$data_post_detail['PROJECT_ACTIVITY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ACTIVITY')));
		$data_post_detail['COMPANY_CODE']=$company;
		
        $id=htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
        $desc=htmlentities(mysql_escape_string($this->input->post('PROJECT_DESC')));
        
        if(isset($data_post) && isset($id) && isset($desc) && isset($company)) {
            if (!empty($data_post['PROJECT_ID'])) { 
                $data_exist = $this->model_project_pengajuan->cek_exist_data($id,"m_project",$company); //cek data project, mencegah duplikasi
                if($data_exist > 0) {
                    echo("data project telah terdapat di dalam database");
                } else {  
                    $insert_new=$this->model_project_pengajuan->insert_new_data($data_post,"m_project"); //insert baru ke database
                    if($insert_new != "" && $data_post['PROJECT_TYPE'] != "OP"){
						  $data_exist_detail = $this->model_project_pengajuan->cek_exist_data_detail($id,$act,$company);
						  if($data_exist_detail > 0) {
							  	$this->model_project_pengajuan->update_data_detail($id,$company,$data_post_detail,$act);
						  } else {  
						  		$this->model_project_pengajuan->insert_new_data_detail($data_post_detail,"m_project_detail");
						  }
					}
					//echo $insert_new;          
                }    
            } else {
                echo "input tidak lengkap";
            }       
        } else {
            echo "variable not define";
        }
    }
	
    function EditData()
    {
        $act="edit";
        $company = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        
        //$data_post['PROJECT_ID']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
        $data_post['AFD']=htmlentities(mysql_escape_string($this->input->post('AFD')));
        $data_post['PROJECT_TYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_TYPE')));
        $data_post['PROJECT_SUBTYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_SUBTYPE')));
		$data_post['PROJECT_SUB_ACTIVITY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_SUB_ACTIVITY')));
        $data_post['PROJECT_DESC']=htmlentities(mysql_escape_string($this->input->post('PROJECT_DESC')));
		$data_post['PROJECT_LOCATION']=htmlentities(mysql_escape_string($this->input->post('PROJECT_LOCATION')));
		$data_post['PROJECT_ACTIVITY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ACTIVITY')));
		$data_post['KODE_PELAKSANA']=htmlentities(mysql_escape_string($this->input->post('KODE_PELAKSANA')));
		$data_post['SPK']=htmlentities(mysql_escape_string($this->input->post('SPK')));
        $data_post['PROJECT_START']=htmlentities(mysql_escape_string($this->input->post('PROJECT_START')));
        $data_post['PROJECT_END']=htmlentities(mysql_escape_string($this->input->post('PROJECT_END')));
        $data_post['PROJECT_STATUS']=htmlentities(mysql_escape_string($this->input->post('PROJECT_STATUS')));
		$data_post['PROJECT_QTY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_QTY')));
		$data_post['PROJECT_UOM']=htmlentities(mysql_escape_string($this->input->post('PROJECT_UOM')));
		$data_post['PROJECT_VALUE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_VALUE')));
		$data_post['PROJECT_PPN']=htmlentities(mysql_escape_string($this->input->post('PROJECT_PPN')));
		$data_post['PROJECT_NETTVAL']=htmlentities(mysql_escape_string($this->input->post('PROJECT_NETTVAL')));
		$data_post['PROJECT_STATUS']=htmlentities(mysql_escape_string($this->input->post('PROJECT_STATUS')));
		$data_post['TGL_TERBIT']=htmlentities(mysql_escape_string($this->input->post('TGL_TERBIT')));
        $data_post['COMPANY_CODE']=$company;
        $data_post['UPDATE_BY']=htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        $data_post['UPDATE_DATE']=date ("Y-m-d H:i:s");
        if (isset($act)) {
            $data_post['Action']=$act;    
        } else {
            $data_post['Action']="";
        }
        $desc=htmlentities(mysql_escape_string($this->input->post('DESCRIPTION')));
        $id=mysql_escape_string(htmlentities($this->uri->segment(3)));
        
        if (isset($id) && isset($data_post) && isset($desc)) {
            if (!empty($id)) {
                $update_data=$this->model_project_pengajuan->update_data($id,$company,$data_post,"m_project");
                echo "0";   
            } else {
                echo "input tidak lengkap";
            }  
        } else {
            echo "variable not define";
        }
    }
    
    function DelData() 
	{
        $id=htmlentities(mysql_escape_string($this->uri->segment(3)));
        $company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        $act = "-";
        if (isset($id) && isset($company)) {
            if(!empty($id)) {
                $delete_data=$this->model_project_pengajuan->delete_data($id,$company,"m_project"); 
				$this->model_project_pengajuan->delete_data_detail($id, $act, $company,"m_project_detail"); 
				//echo "0";   
            } else {
                echo "input tidak lengkap";
            }
        } else {
            echo "variable not define";
        }
    }
	
	/* detail aktivitas */
	function AddNewDetail()
    {
        $company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY'))); 
    
        $data_post['MASTER_PROJECT_ID']=htmlentities(mysql_escape_string($this->input->post('MASTER_PROJECT_ID')));
		$data_post['PROJECT_ACTIVITY']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ACTIVITY')));
		$data_post['COMPANY_CODE']=$company;
		$id =  htmlentities(mysql_escape_string($this->input->post('MASTER_PROJECT_ID')));
		$act = htmlentities(mysql_escape_string($this->input->post('PROJECT_ACTIVITY'))); 
        if(isset($data_post) && isset($company)) {
            if (!empty($data_post['MASTER_PROJECT_ID'])) { 
 				$data_exist_detail = $this->model_project_pengajuan->cek_exist_data_detail($id,$act,$company);
				if($data_exist_detail > 0) {
					$this->model_project_pengajuan->update_data_detail($id,$company,$data_post,$act);
				} else {  
					$this->model_project_pengajuan->insert_new_data_detail($data_post,"m_project_detail");
				}
            } else {
                echo "input tidak lengkap";
            }       
        } else {
            echo "variable not define";
        }
    }
	
	function DelDataDetail() 
	{
        $id=htmlentities(mysql_escape_string($this->uri->segment(3)));
		$act=htmlentities(mysql_escape_string($this->uri->segment(4)));
        $company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        
        if (isset($id) && isset($company)) {
            if(!empty($id)) {
				$this->model_project_pengajuan->delete_data_detail($id, $act, $company,"m_project_detail"); 
            } else {
                echo "input tidak lengkap";
            }
        } else {
            echo "variable not define";
        }
    }
	/* end detail aktivitas */
    
    function LoadData() 
	{
        $company=$this->session->userdata('DCOMPANY');
        echo json_encode($this->model_project_pengajuan->LoadData($company));
    }
    
    function SearchData() 
	{
        $getID =htmlentities(mysql_escape_string($this->uri->segment(3))); 
        $getAfd=htmlentities(mysql_escape_string($this->uri->segment(4)));
        $getType=htmlentities(mysql_escape_string($this->uri->segment(5)));
        $getDesc=htmlentities(mysql_escape_string($this->uri->segment(6)));
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $company=$this->session->userdata('DCOMPANY');
        if ($getID =="" && $getAfd=="" && $getDesc=="" && $getType=="") {
            echo json_encode($this->model_project_pengajuan->LoadData($company));
        } else {
            echo json_encode($this->model_project_pengajuan->search_prj($getID,$getAfd,$getType,$getDesc, $limit, $page, $sidx, $sord));
        }
    }
    
	function dropdownlist_afd()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_afd' class='select' id='i_afd' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_project_pengajuan->get_afd($company);
		
		foreach ( $data_afd as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_safd()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='search_afd' class='select' id='search_afd' onchange='doSearch(arguments[0]||event)' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_project_pengajuan->get_afd($company);
		
		foreach ( $data_afd as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function LoadChain()
	{
		$data_key= $this->uri->segment(3);
		$array=array();
		
		if ($data_key=="IF") {
			$array[] = array('kt' => 'PEKERJAAN BANGUNAN - BANGUNAN PERMANEN', 'kt2' => '8151000' );
			$array[] = array('kt' => 'PEKERJAAN BANGUNAN - BANGUNAN SEMI PERMANEN', 'kt2' => '8151000' );
			$array[] = array('kt' => 'PEKERJAAN BANGUNAN - BANGUNAN NON PERMANEN', 'kt2' => '8151000' );
			$array[] = array('kt' => 'PEKERJAAN BANGUNAN - BANGUNAN AIR', 'kt2' => '8131000' );
			$array[] = array('kt' => 'PEKERJAAN BANGUNAN - BANGUNAN UTILITAS', 'kt2' => '8161000' );
			$array[] = array('kt' => 'PEMBUATAN JALAN', 'kt2' => '8111000' );
			$array[] = array('kt' => 'PEMBUATAN JEMBATAN, GORONG GORONG dan TITI PANEN BETON', 'kt2' => '8141000' );
			$array[] = array('kt' => 'PEMBUATAN PARIT', 'kt2' => '8121000' );
		} elseif($data_key=="OP") {
			$array[] = array('kt' => 'LAND PREPARATION', 'kt2' => '8200000' );
			$array[] = array('kt' => 'TANAM KELAPA SAWIT', 'kt2' => '8401000' );		
		} elseif($data_key=="NS") {
			$array[] = array('kt' => 'PEMBUKAAN LAHAN BIBITAN', 'kt2' => '8301001' );
			$array[] = array('kt' => 'PEMBUATAN BANGUNAN', 'kt2' => '8301002' );
			$array[] = array('kt' => 'PEMBUATAN JALAN DAN JEMBATAN', 'kt2' => '8301003' );
			$array[] = array('kt' => 'PEMBUATAN DRAINASE DAN WADUK ( RESERVOIR )', 'kt2' => '8301004' );
			$array[] = array('kt' => 'PEMBUATAN INSTALASI LISTRIK', 'kt2' => '8301005' );
			$array[] = array('kt' => 'PEMBUATAN INSTALASI AIR', 'kt2' => '8301006' );
			$array[] = array('kt' => 'TRANSPORT MATERIAL', 'kt2' => '8301007' );
			$array[] = array('kt' => 'TRANSPORT KARYAWAN', 'kt2' => '8301008' );
		} elseif($data_key=="PB") {
			$array[] = array('kt' => 'BANGUNAN PKS', 'kt2' => '8199100' );
			$array[] = array('kt' => 'FONDASI PKS', 'kt2' => '8199200' );
			$array[] = array('kt' => 'MESIN-MESIN PKS', 'kt2' => '8199300' );
		}
		echo json_encode($array);
	}
	
	function LoadChain2()
	{
		$data_key= $this->uri->segment(3);
		$array=array();
		
		if ($data_key=="8111000") {
			$array[] = array('kt' => 'PEMBUATAN JALAN', 'kt2' => 'PEMBUATAN JALAN' );
			$array[] = array('kt' => 'PERKERASAN JALAN', 'kt2' => 'PERKERASAN JALAN' );
			$array[] = array('kt' => 'PENINGGIAN JALAN', 'kt2' => 'PENINGGIAN JALAN' );
		} else if ( $data_key=="8131000" ) {
			$array[] = array('kt' => 'TAHAP I', 'kt2' => 'TAHAP I' );
			$array[] = array('kt' => 'TAHAP II', 'kt2' => 'TAHAP II' );
			$array[] = array('kt' => 'TAHAP III', 'kt2' => 'TAHAP III' );
			$array[] = array('kt' => 'TAHAP IV', 'kt2' => 'TAHAP IV' );
		}  
		echo json_encode($array);
	}
	
	function pjtoexcell()
	{
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$sQuery = "SELECT * FROM m_project WHERE COMPANY_CODE = '".$company."' ORDER BY PROJECT_ID";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'PROJECT_' . $company . "_" .  date($datestring, $time);
		to_excel($query, $name);
	}
	
	/* detail project */
	function LoadDetail() 
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$id = $this->uri->segment(3);
        $company=$this->session->userdata('DCOMPANY');
		echo json_encode($this->model_project_pengajuan->getdetailact($id, $company, $limit, $page, $sidx, $sord));
	}
	
	function getProjectNum(){
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$id=$this->uri->segment(3);
		$pj = $this->model_project_pengajuan->get_project_num($company, $id);
		$data = array();
		foreach ( $pj as $row) {
			$data[] = array("code"=>($row['PROJECT_ID']),"type"=>($row['PROJECT_TYPE']) );
		}
		$storeData = json_encode($data);
        echo $storeData;
	}
	
	//autocomplete
	function getactivity(){
		$q = $_REQUEST["q"]; 
		$data_act = $this->model_project_pengajuan->get_project_act($q);
		$aktivitas = array();
		foreach($data_act as $row) {
			$aktivitas[] = '{"res_id":"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'","res_name":"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'","res_dl":"'.str_replace('"','\\"',$row['ACCOUNTCODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'"}';
		}
		echo '['.implode(',',$aktivitas).']'; exit; 
	}
}

?>