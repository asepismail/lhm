<?php
class syst_c_menu extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
		$this->load->model('system/syst_m_menu');
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
		$this->lastmenu="syst_c_menu";
		$this->load->helper('file');
    }
	
	function index(){
      $view="system/syst_v_menu";
      $this->data['js'] = "";
	  $this->data['judul_header'] = "Master Data Menu System";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['parent']=$this->dropdownlist("i_parent","style='width:190px;'","tabindex='2'","parent","MENU_ID","MENU_NAME");
      $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
	  
	  if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
      } else {
            redirect('login');
      }
   }
   
   function search_menu(){
	   $company = $this->session->userdata('DCOMPANY');
	   $get = "";
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->syst_m_menu->read_menu($searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->syst_m_menu->read_menu();
	   }

	   echo json_encode($get);
   }
   
   function dropdownlist($name, $style, $tab, $type, $val, $desc){
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='".$name."' ".$tab." class='select' id='".$name."' ".$style." >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = "";
		if($type == "parent"){
			$data = $this->syst_m_menu->get_parent();
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
   
   function ext_genID(){
		$parent = $this->uri->segment(4);
		$hasil = "";
		if($parent != ""){
			$this->db->select_max('MENU_ID');
			$this->db->from('m_user_menu');
			$this->db->like('MENU_ID', $parent, 'after'); 
			$temp= $this->db->get();
       		$this->db->close();
       		$temp = $temp->result_array();
			
			if(empty($temp[0]['MENU_ID'])){
				$hasil = $parent."01";
			} else {
				$hasil = $parent;
				$str = $temp[0]['MENU_ID'];
				$str = $str+1;
				$hasil = $str;
			}
		} else {
			$this->db->select_max('MENU_ID');
        	$this->db->from('m_user_menu');
			$this->db->where('MENU_PARENT', '1'); 
			$temp= $this->db->get();
       		$this->db->close();
       		$temp = $temp->result_array();
			if(empty($temp[0]['MENU_ID'])){
				$hasil = "100";
			} else {
				$str = $temp[0]['MENU_ID'];
				$strRight = substr($str,2,1);
				$str = substr($str,0,2);
				$str = $str+1;
				$panjangString = 3;
				$jumlahNol = $panjangString - strlen($str);
				for($i=0;$i<$jumlahNol;$i++){
					$hasil .= "0";
				}
				$hasil = $str.$strRight;
			}
		}
       echo $hasil;
	}
	
	function getLeft(){
		$parent = substr($this->uri->segment(4),0,2);
		$this->db->select_max('LFT');
		$this->db->from('m_user_menu');
		$this->db->like('MENU_ID', $parent, 'after'); 
		$temp= $this->db->get();
       	$this->db->close();
       	$temp = $temp->result_array();
		
		$str = $temp[0]['LFT'];
		echo $str+2;		
	}
	
	function getRight(){
		$parent = substr($this->uri->segment(4),0,2);
		$this->db->select_max('RGT');
		$this->db->from('m_user_menu');
		$this->db->like('MENU_ID', $parent, 'after'); 
		$temp= $this->db->get();
       	$this->db->close();
       	$temp = $temp->result_array();
		
		$str = $temp[0]['RGT'];
		if($parent != ""){
			echo $str+1;		
		} else {
			echo $str;
		}
	}
	
	function insertData(){
		$data_post['MENU_ID'] = str_replace(" ","",$this->input->post( 'MENU_ID' ));
        $data_post['MENU_NAME'] = $this->input->post( 'MENU_NAME' );
        $data_post['MENU_URL'] = $this->input->post( 'MENU_URL' );
        $data_post['MENU_PARENT'] = $this->input->post( 'MENU_PARENT' );
        $data_post['LFT'] =  str_replace(" ","",$this->input->post( 'LFT' ));
		$data_post['RGT'] =  str_replace(" ","",$this->input->post( 'RGT' ));
		$this->syst_m_menu->updatelevel_left( str_replace(" ","",$this->input->post( 'LFT' )) );
        $this->syst_m_menu->updatelevel_right(  str_replace(" ","",$this->input->post( 'LFT' )) - 1 );
		$data_menu = $this->syst_m_menu->cek_exist(str_replace(" ","",$this->input->post( 'MENU_ID' )));
		if($data_menu > 0) { 
           $status = "Menu dengan id ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
        } else  {
		   $insert_id = $this->syst_m_menu->insert_menu( $data_post );
		}
		//$this->syst_m_menu->updatelevel_left( $data_post['LFT'] );
		
	}
	
	function updateData(){
		$data_post['MENU_ID'] = str_replace(" ","",$this->input->post( 'MENU_ID' ));
        $data_post['MENU_NAME'] = $this->input->post( 'MENU_NAME' );
        $data_post['MENU_URL'] = $this->input->post( 'MENU_URL' );
        $data_post['MENU_PARENT'] = $this->input->post( 'MENU_PARENT' );
        $data_post['LFT'] =  str_replace(" ","",$this->input->post( 'LFT' ));
		$data_post['RGT'] =  str_replace(" ","",$this->input->post( 'RGT' ));
		$insert_id = $this->syst_m_menu->update_menu( $data_post['MENU_ID'], $data_post );
	}
	
	function deleteData(){
		$data_post['MENU_ID'] = str_replace(" ","",$this->input->post( 'MENU_ID' ));
		$data_post['LFT'] = str_replace(" ","",$this->input->post( 'LFT' ));
		$data_post['RGT'] = str_replace(" ","",$this->input->post( 'RGT' ));
		
		$this->syst_m_menu->deletelevel_left( str_replace(" ","",$this->input->post( 'LFT' )) );
        $this->syst_m_menu->deletelevel_right(  str_replace(" ","",$this->input->post( 'LFT' )) - 1 );
		
		$insert_id = $this->syst_m_menu->delete_menu( $data_post['MENU_ID'], $data_post );
	}
}
?>