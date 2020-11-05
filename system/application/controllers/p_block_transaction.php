<?php
class p_block_transaction extends Controller{
    function __construct(){
        parent::__construct();
		$this->load->model( 'model_p_block_transaction' );
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
		$this->lastmenu="syst_m_user";
		$this->load->helper('file');
    }
	
	function index(){
      $view="info_p_block_transaction";
      $this->data['js'] = "";
	  $this->data['judul_header'] = "Transaksi Pokok Mati";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');       
	  $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
	  
	  $this->data['afd'] = $this->dropdownlist_afd();
	  	  
	  if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
      } else {
            redirect('login');
      }
   }
   
   function search_pokok_mati(){
	   $company = $this->session->userdata('DCOMPANY');
	   $periode = $this->uri->segment(3);
	   
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
		   $get = $this->model_p_block_transaction->read_pokok_mati($company,$periode,$searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->model_p_block_transaction->read_pokok_mati($company,$periode);
	   }
	   echo json_encode($get);
   }
   
   function dropdownlist_afd(){
	    $company = $this->session->userdata('DCOMPANY');
		$string = "<select  name='i_afd' class='select' id='i_afd' onchange='chainBlok()' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_p_block_transaction->get_afd($company);
		foreach ( $data_afd as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
			}
		}
		$string = $string. "</select>";
		return $string;
	}
	
	function get_block(){
		$afd = strtoupper(trim($this->uri->segment(3)));
		$company = $this->session->userdata('DCOMPANY');
		$array=array();
        $data_afd = $this->model_p_block_transaction->get_block($afd, $company);
        foreach ($data_afd as $drow){
			$array[] = array('kt' => $drow['BLOCKID'], 'kt2' => $drow['BLOCKID'] );
		}
        echo json_encode($array);
	}
	
	function get_block_tanam(){
		$afd = strtoupper(trim($this->uri->segment(3)));
		$block = strtoupper(trim($this->uri->segment(4)));
		$company = $this->session->userdata('DCOMPANY');
		$array=array();
        $data_afd = $this->model_p_block_transaction->get_block($afd, $company, $block);
        foreach ($data_afd as $drow){
			$array[] = array('kt' => $drow['DESCRIPTION'], 'kt2' => $drow['FIELDCODE'] );
		}
        echo json_encode($array);
	}
	
	function insertData(){
	  $data_post['TRANS_DATE'] = str_replace(" ","",$this->input->post( 'TRANS_DATE' ));
	  $data_post['TRANS_TYPE'] = 'Pokok Mati';
      $data_post['AFD'] = str_replace(" ","",$this->input->post( 'AFD' ));
      $data_post['BLOCK'] = str_replace(" ","",$this->input->post( 'BLOCK' ));
      $data_post['BLOCK_TANAM'] = str_replace(" ","",$this->input->post( 'BLOCK_TANAM' ));
      $data_post['QTY'] =  str_replace(" ","",$this->input->post( 'QTY' ));
	  $data_post['DOCUMENT_NUMBER'] = str_replace(" ","",$this->input->post( 'DOCUMENT_NUMBER' ));
	  $data_post['NOTE'] =  $this->input->post( 'NOTE' );
	  $data_post['COMPANY_CODE'] =  htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
	  $data_post['INPUTBY'] =  htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
	  $data_post['INPUTDATE'] =  date ("Y-m-d H:i:s");
	  $data_trans = $this->model_p_block_transaction->cek_exist_trans($data_post['TRANS_DATE'], $data_post['BLOCK_TANAM']);
	 
	  if( str_replace(" ","",$this->input->post( 'TRANS_DATE' )) == "" || 
					str_replace(" ","",$this->input->post( 'AFD' )) == "" || 
						str_replace(" ","",$this->input->post( 'BLOCK' )) == "" || 
							str_replace(" ","",$this->input->post( 'BLOCK_TANAM' )) == "" ||
							   str_replace(" ","",$this->input->post( 'QTY' )) == "" ||
							   		str_replace(" ","",$this->input->post( 'DOCUMENT_NUMBER' )) == ""){
		 		 echo "Mohon lengkapi data tanggal, afdeling, blok, qty, dan no dokumen!!";
		 		 echo $data_post['TRANS_DATE'];
	  } else {
		  if (!is_numeric($data_post['QTY'])) {
				echo "Data jumlah pokok mati harus angka !!";
		  } else {
			  if($data_trans > 0) { 
				  $status = "Transaksi pokok mati ini sudah ada mohon periksa kembali!!!"; 
				  echo $status;
			  } else  {
				  $insert_id = $this->model_p_block_transaction->insert_trans( $data_post );
			  }
		  }
	   }
	}
	
	function updateData(){
		$data_post['TRANS_ID'] = str_replace(" ","",$this->input->post( 'TRANS_ID' ));
		$data_post['TRANS_DATE'] = str_replace(" ","",$this->input->post( 'TRANS_DATE' ));
		$data_post['TRANS_TYPE'] = 'Pokok Mati';
        $data_post['AFD'] = str_replace(" ","",$this->input->post( 'AFD' ));
        $data_post['BLOCK'] = str_replace(" ","",$this->input->post( 'BLOCK' ));
        $data_post['BLOCK_TANAM'] = str_replace(" ","",$this->input->post( 'BLOCK_TANAM' ));
        $data_post['QTY'] =  str_replace(" ","",$this->input->post( 'QTY' ));
		$data_post['DOCUMENT_NUMBER'] =  str_replace(" ","",$this->input->post( 'DOCUMENT_NUMBER' ));
		$data_post['COMPANY_CODE'] =  htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data_post['NOTE'] =  $this->input->post( 'NOTE' );
		$data_post['UPDATEBY'] =  htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$data_post['UPDATEDATE'] =  date ("Y-m-d H:i:s");
		$selectlog = $this->model_p_block_transaction->data_for_log( $data_post['TRANS_ID']);
		
		if( str_replace(" ","",$this->input->post( 'TRANS_DATE' )) == "" || 
				str_replace(" ","",$this->input->post( 'AFD' )) == "" || 
					str_replace(" ","",$this->input->post( 'BLOCK' )) == "" || 
						str_replace(" ","",$this->input->post( 'BLOCK_TANAM' )) == "" ||
							str_replace(" ","",$this->input->post( 'QTY' )) == "" ||
							   	str_replace(" ","",$this->input->post( 'DOCUMENT_NUMBER' )) == ""){
		 		 echo "Mohon lengkapi data tanggal, afdeling, blok, qty, dan no dokumen!!";
		 		 echo $data_post['TRANS_DATE'];
	  } else {
		 	if (!is_numeric($data_post['QTY'])) {
					echo "Data jumlah pokok mati harus angka !!";
		  	} else {
					$logafter = $data_post['TRANS_ID'] . ";" . $data_post['TRANS_DATE'] . ";"; 
					$logafter .= $data_post['TRANS_TYPE'] . ";" . $data_post['AFD'] . ";"; 
					$logafter .= $data_post['BLOCK'] . ";" . $data_post['BLOCK_TANAM'] . ";"; 
					$logafter .= $data_post['QTY'] . ";" . $data_post['DOCUMENT_NUMBER'] . ";"; 
					$logafter .= $data_post['COMPANY_CODE'] . ";" . $data_post['NOTE'] . ";"; 
					$logafter .= $data_post['UPDATEBY'] . ";" . $data_post['UPDATEDATE']; 
		
					foreach($selectlog as $row){
						$data_log['LOG_DATE'] = $row['TRANS_DATE'];
						$data_log['LOGINID'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
						$data_log['MODULE'] = 'Pokok Mati';
						$data_log['LOG_BEFORE'] = $row['LOG_BEFORE'];
						$data_log['LOG_AFTER'] = $logafter;
						$data_log['CREATED_DATE'] = 'Pokok Mati';
					}
		
				$insert_log = $this->model_p_block_transaction->insert_log( $data_log );
				if( $insert_log > 0) {
					$insert_log = $this->model_p_block_transaction->update_trans( $data_post['TRANS_ID'], $data_post );
				} 
	  		} 
	    }
	}
}

?>