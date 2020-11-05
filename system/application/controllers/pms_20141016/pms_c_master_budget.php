<?php
class pms_c_master_budget extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('pms/pms_m_master_budget');
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->library('csvReader');
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
        $view="pms/pms_v_master_budget";
        $this->data['js'] = "";
		$this->data['judul_header'] = "Data Master Budget";
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
		$this->data['periode'] = $this->global_func->drop_year('tahun1','select');
		$this->data['periode2'] = $this->global_func->drop_year('tahun2','select');
		$this->data['dropcompany'] = $this->dropdownlist_company('company1');
		$this->data['dropcompany2'] = $this->dropdownlist_company('company2');
		
		$this->data['satuan1'] = $this->dropdownlist("i_satuan1","style='width:120px;'","tabindex='6'","sat","UNIT_CODE","UNIT_DESC");
		$this->data['satuan2'] = $this->dropdownlist("i_satuan2","style='width:120px;'","tabindex='7'","sat","UNIT_CODE","UNIT_DESC");
				
		if ($this->data['login_id'] == TRUE){
		  	$this->load->view($view, $this->data);
		} else {
			  redirect('login');
		}
    }
	
	function grid_mb_header()
    {
		$company = $this->uri->segment(4);
		$periode = $this->uri->segment(5);
        
		if($company == "" || $company == "undefined"){
			$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		}
        echo json_encode($this->pms_m_master_budget->loadDataHeader($company, $periode));
    }
	
	/* dropdown company */
   function dropdownlist_company($name){ 
		$string = "<select  name='".$name."' class='select' id='".$name."' style='width:230px;'>";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = $this->pms_m_master_budget->getCompany();
		
		foreach ( $data as $row){
		   if( (isset($default))){
			 $string = $string." <option value=\"".$row['COMPANY_CODE']."\"  selected>".$row['COMPANY_NAME']." </option>";
			} else {
			 $string = $string." <option value=\"".$row['COMPANY_CODE']."\">".$row['COMPANY_NAME']." </option>";
			}
		} 
		$string =$string. "</select>";
		return $string;
	}
		
	function dropdownlist($name, $style, $tab, $type, $val, $desc)
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='".$name."' ".$tab." class='select' id='".$name."' ".$style." >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = "";
		
		if($type = "sat"){
			$data_afd = $this->pms_m_master_budget->get_satuan();
		}
		
		foreach ( $data_afd as $row){
			if( (isset($default))){
				$string = $string." <option value=\"".$row[$val]."\"  selected>".$row[$desc]." </option>";
			} else {
				$string = $string." <option value=\"".$row[$val]."\">".$row[$desc]." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
	
	function do_import(){
        $error = "";
        $msg = "";
		$subAct = 0;
		$iftype = "";
		$ifsubtype = "";
        $fileElementName = 'myfile';
		$baris = 1;
		$ret = 0; 
		//$_FILES['myfile'] = 'C:/Users/ridhu/Documents/CEK DATA NOVEMBER 2013/upmasterbudget.csv';
		
         if(!empty($_FILES[$fileElementName]['error'])){
            
            switch($_FILES[$fileElementName]['error']){
                case '1':
                    $error = 'File yang diupload melewati batas maksimal upload yang diizinkan';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'Hanya beberapa dari file yang berhasil diupload';
                    break;
                case '4':
                    $error = 'Tidak ada file yang diupload.';
                    break;

                case '6':
                    $error = 'Folder temporer tidak ada';
                    break;
                case '7':
                    $error = 'Gagal untuk menyimpan file ke direktori';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'Tidak ada pesan error yang tersedia';
            }
        } elseif(empty($_FILES['myfile']['tmp_name']) || $_FILES['myfile']['tmp_name'] == 'none'){
            $error = 'Tidak ada file yang diimport..';
        } else {             
			$csvfile = $_FILES['myfile']['tmp_name'];
			
			if(!file_exists($csvfile)) {
				$error = "File tidak ditemukan. Pastikan anda memilih direktori yang benar.\n";
				exit;
			}
	  
			$file = fopen($csvfile,"r");
	  
			if(!$file) {
				$error = "Error dalam membuka file data.\n";
				exit;
			}
	  
			$size = filesize($csvfile);
	  
			if(!$size) {
				$error = "File kosong.\n";
				exit;
			} 
	  
			$result =   $this->csvreader->parse_file($csvfile); //path to csv file
			foreach($result as $field){
				$items = array();
				
				$isiftype = $field['IS_IF_TYPE'];
				$isifsubtype = $field['IS_IF_SUBTYPE'];
				switch($field['SUB_ACTIVITY_CODE']){
					case 'PEMBUATAN JALAN': $subAct = 1; break;
					case 'PENINGGIAN JALAN': $subAct = 2; break;
					case 'PELAPISAN JALAN': $subAct = 3; break;
					case 'PERKERASAN JALAN': $subAct = 4; break;
					case 'TAHAP I': $subAct = 5; break;
					case 'TAHAP II': $subAct = 6; break;
					case 'TAHAP III': $subAct = 7; break;
					case 'TAHAP IV': $subAct = 8; break;
					case '': default: $subAct = '';
				}
				
				if( $isiftype != "") {
					$iftype = $this->pms_m_master_budget->getIFCode($isiftype);
				}
				
				if( $isifsubtype != "") {
					$ifsubtype = $this->pms_m_master_budget->getSubIFCode($iftype, $isifsubtype);
				}
							
				$cek = $this->pms_m_master_budget->cekExist($field['COMPANY_CODE'], $field['PERIODE'],$field['MASTER_BUDGET_TYPE'], $field['ACTIVITY_CODE'], $subAct, $iftype, $ifsubtype );
				
				if ( $cek > 0 ) {
					$error = $error . " baris " . $baris . " sudah ada didalam data master budget <br />";
				} else {
					$items = array(
								'MASTER_BUDGET_TYPE' => $field['MASTER_BUDGET_TYPE'],
								'PERIODE' => $field['PERIODE'],
								'ACTIVITY_CODE' => $field['ACTIVITY_CODE'],
								'SUB_ACTIVITY_CODE' => $subAct,
								'IS_IF_TYPE' => $iftype,
								'IS_IF_SUBTYPE' => $ifsubtype,
								'SATUAN1' => $field['SATUAN1'],
								'SATUAN2' => $field['SATUAN2'],
								'QTY' => $field['QTY'],
								'ROTASI' => $field['ROTASI'],
								'RUPIAH' => $field['RUPIAH'],
								'COMPANY_CODE' => $field['COMPANY_CODE']
					);
					
					$query = $this->db->insert('pms_master_budget',$items);
					$ret += (int)$this->db->affected_rows($query);
					$msg = $ret . " Data berhasil diimport...";   
					
				}
				$baris++;
			}    
		}
        echo "{";
        echo                "error: '" . $error . "',\n";
        echo                "msg: '" . $msg . "'\n";
        echo "}"; 
    }
	
	function grid_mcoa()
    {
		$searchp = $this->uri->segment(4);
	 	echo json_encode($this->pms_m_master_budget->read_mcoa($searchp));
    }
	
	function deleteMbudget(){
		$ret = "";
		$budgetID = htmlentities(trim($this->input->post( 'MASTER_BUDGET_ID' )),ENT_QUOTES,'UTF-8');
		$cek = $this->pms_m_master_budget->cekDetailBudget($budgetID);
		if($cek > 0){
			$ret = 99999;
		} else {
			$ret = $this->pms_m_master_budget->deleteBudget($budgetID);
		}
		echo $ret;
	}
	
	function cekPTA(){
		$BudgetId = htmlentities(trim($this->input->post( 'PersediaanNo' )),ENT_QUOTES,'UTF-8'); 
		echo $this->pms_m_master_budget->cekDetailPTA($BudgetId); 
	}
	
	function addPTA(){
	  $rets = 0;
	  $data_post['MASTER_BUDGET_ID'] =  htmlentities(trim($this->input->post( 'MASTER_BUDGET_ID' )),ENT_QUOTES,'UTF-8');
	  $data_post['DOCUMENT_NUMBER'] = htmlentities(trim($this->input->post( 'DOCUMENT_NUMBER' )),ENT_QUOTES,'UTF-8');
	  $data_post['DETAIL_TYPE'] = 'PTA';
	  $data_post['ACTIVITY_CODE'] = htmlentities(trim($this->input->post( 'ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
	  $data_post['SUB_ACTIVITY_CODE'] = htmlentities(trim($this->input->post( 'SUB_ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
	  $data_post['IS_IF_TYPE'] = htmlentities($this->input->post( 'IS_IF_TYPE' ),ENT_QUOTES,'UTF-8');
	  $data_post['IS_IF_SUBTYPE'] =  htmlentities(trim($this->input->post( 'IS_IF_SUBTYPE' )),ENT_QUOTES,'UTF-8');
	  $data_post['BAL_RUPIAH'] = $this->pms_m_master_budget->getBalance($data_post['MASTER_BUDGET_ID']);
	  $data_post['PROG_RUPIAH'] = htmlentities($this->input->post( 'PROG_RUPIAH' ),ENT_QUOTES,'UTF-8');
	  $data_post['NOTES'] = htmlentities($this->input->post( 'NOTES' ),ENT_QUOTES,'UTF-8');
	  $data_post['INPUTBY'] = $this->session->userdata('LOGINID');
	  $data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
	    
	  $doInsert = $this->pms_m_master_budget->insertPTA($data_post);
	  if($doInsert > 0){
		  $rets = $this->pms_m_master_budget->updateBudgetHeader($data_post['MASTER_BUDGET_ID'], $data_post['PROG_RUPIAH']);
	  }
	  echo $rets;
	}
}

?>