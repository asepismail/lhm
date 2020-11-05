<?php
class adem_c_running_account extends Controller{
    private $data;
	 function __construct(){
        parent::__construct() ;
        $this->load->model('model_importdata');
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        
        $this->load->plugin('to_excel');
        $this->load->helper('file');
        $this->lastmenu="c_importdata";         
    }
	
	 function index(){
        $view="adem_v_running_account";
        
        //$data = array();
        $this->data['judul_header'] = "Generate Data Running Gaji";
        $this->data['js'] = $this->js_rpt();
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$this->data['inisial'] = $this->dropdownlist_inisial();
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
	
	function js_rpt() {
		
			$js = " jQuery('#submitdata').click(function (){
								var jns_rpt = document.getElementById('jns_rpt').value;
								var inisial = document.getElementById('inisial').value;
								var periode = document.getElementById('tahun').value + document.getElementById('bulan').value;
								
								if ( periode != '' ){
										window.location = url+'/adem_c_running_account/generate/'+ jns_rpt + '/' + periode + '/' + inisial;		
									} else {
										alert('mohon pilih periode yang akan digenerate!!');
										return false;
									}
								});
						";
				 return $js;
	}
	
	function dropdownlist_inisial()
	{
	
		$string = "<select  name='inisial' class='select'  id='inisial' style='width:230px' >";
		
		$data_inisial = $this->model_importdata->get_inisial($this->session->userdata('DCOMPANY'));
		
		foreach ( $data_inisial as $row)
		{
			if( (isset($default)) && ($default==$row[$nama_isi]) )
			{
				$string = $string." <option value=\"".$row['INISIAL']."\"  selected>".$row['COMPANY_NAME']." </option>";
			}
			else
			{
				$string = $string." <option value=\"".$row['INISIAL']."\">".$row['COMPANY_NAME']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
	
	function generate () {
		$jns_rpt = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$inisial = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		switch($jns_rpt)
		{
			case "ws":
				$this->generate_ws($inisial, $periode,$company);
			break;
			case "vh1":
				$this->generate_vh1($inisial, $periode,$company);
			break;
			case "vh2":
				$this->generate_vh2($inisial, $periode,$company);
			break;
			case "ma":
				$this->generate_ma($inisial, $periode,$company);
			break;
			case "pay":
				$this->generate_pay($inisial, $periode,$company);
			break;
			case "hkn":
				$this->generate_hkn($inisial, $periode,$company);
			break;
			case "ast":
				$this->generate_ast($inisial, $periode,$company);
			break;
			case "pph":
				$this->generate_pph($inisial, $periode,$company);
			break;
			case "zis":
				$this->generate_zis($inisial, $periode,$company);
			break;
		}
	
	}
	
	function generate_ws($company, $periode, $inisial){
		$query = $this->db->query("CALL sp_adem_workshop('".$company."','".$periode."','".$inisial."')");
		$name = 'RA_WS_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_vh1($inisial, $periode, $company){
		$query = $this->db->query("CALL sp_adem_prevehicle('".$inisial."','".$periode."','".$company."')");
		$name = 'RA_VH1_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_vh2($inisial, $periode, $company){
		$query = $this->db->query("CALL sp_adem_vehicle('".$inisial."','".$periode."','".$company."')");
		$name = 'RA_VH2_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_ma($company, $periode, $inisial){
		$query = $this->db->query("CALL sp_adem_machine('".$company."','".$periode."','".$inisial."')");
		$name = 'RA_MA_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_pay($company, $periode, $inisial){
		$query = $this->db->query("CALL sp_adem_lhm_gaji('".$company."','".$periode."','".$inisial."')");
		$name = 'RA_PAY_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_hkn($company, $periode, $inisial){
		$query = $this->db->query("CALL sp_adem_lhm_hkne('".$company."','".$periode."','".$inisial."')");
		$name = 'RA_HKNR_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_ast($company, $periode, $inisial){
		$query = $this->db->query("CALL sp_adem_lhm_astek('".$company."','".$periode."','".$inisial."')");
		$name = 'RA_ASTEK_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_pph($company, $periode, $inisial){
		$query = $this->db->query("CALL sp_adem_lhm_pph('".$company."','".$periode."','".$inisial."')");
		$name = 'RA_PPH_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
	
	function generate_zis($company, $periode, $inisial){
		$query = $this->db->query("CALL sp_adem_lhm_astek('".$company."','".$periode."','".$inisial."')");
		$name = 'RA_ZIS_' . $company . "_" .  $periode;
		
		to_excel($query, $name);
	}
}