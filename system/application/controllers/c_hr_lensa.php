<?
class c_hr_lensa extends Controller 
{
	function c_hr_lensa ()
	{
		parent::Controller();	
		$this->load->model( 'm_hr_report' );
		 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="c_hr_report";
         	 		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->load->helper('date');
		
	}
	
	function index()
    {
		$view = "v_hr_lensa";
        $data = array();
        $data['judul_header'] = "Export Data Karyawan & Gaji";
		$data['js'] = $this->js_hr();    
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['dropcompany'] = $this->dropdownlist_company();
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
            redirect('login');
        }
    }  
	
	function js_hr(){
        $js = " 
				jQuery('#submitdata').click(function (){
			
                var periode = $('#tahun').val() + $('#bulan').val();
				
				
				var company = $('#company').val();
                var jns_laporan = $('#jns_kriteria').val();
				if ( periode != '' ){
					
					if ( jns_laporan == '1'){
						window.location = url+'c_hr_lensa/expEmployeeData/'	 + company
					} else if ( jns_laporan == '2'){
						window.location = url+'c_hr_lensa/expSalary/' + company + '/' + periode             
					} else if ( jns_laporan == '3'){
						window.location = url+'c_hr_lensa/expSptBulanan/' + company + '/' + periode             
					} else if ( jns_laporan == '4'){
						window.location = url+'c_hr_lensa/expSptBHL/' + company + '/' + periode             
					} else if ( jns_laporan == '5'){
						window.location = url+'c_hr_lensa/expBonus/' + company + '/' + periode             
					} else if ( jns_laporan == '6'){
						window.location = url+'c_hr_lensa/expSptBulananBonus/' + company + '/' + periode             
					}
 
				}  else {
					alert(periode);
				}
            });";
        return $js;
    }
	
	function dropdownlist_company(){ 
		$string = "<select  name='company' class='select' id='company'";
		$string .= "style='width:190px;' ><option value=''> -- Pilih -- </option>";
		
		$sQuery = "	SELECT COMPANY_CODE, COMPANY_NAME from m_company WHERE COMPANY_FLAG = 1";
		             		
		$temp=$this->db->query($sQuery);
        $temp = $temp->result_array();
        $this->db->close();		
						
		foreach ( $temp as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\" selected>".$row['COMPANY_NAME']." </option>";
			} else {
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\">".$row['COMPANY_NAME']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	
	function expEmployeeData(){
		$company = $this->uri->segment(3);
		$sQuery = "CALL sp_hr_lensa_employee('".$company."')";
		$query=$this->db->query($sQuery);
		
		//$this->table->set_heading(null);
		$datestring = "%Y%m";
		$time = time();
		$name = 'EMPLOYEE_' . $company . "_" .  mdate($datestring, $time);
				
		to_excel($query, $name);
		
		//query_to_csv($query, TRUE, $name.'.csv');
		
	}
	
	function expSalary(){
		$company = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$sQuery = "CALL sp_hr_lensa_salary('".$company."','".$periode."')";
		$query=$this->db->query($sQuery);
		
		$datestring = "%Y%m";
		$time = time();
		$name = 'SALARY_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
		
	}
	
	function expSptBulanan(){
		$company = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$sQuery = "CALL sp_espt_bulanan('".$periode."')";
		$query=$this->db->query($sQuery);
		
		$datestring = "%Y%m";
		$time = time();
		$name = 'SPTBULANAN_' . $periode . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
		
	}
	
	function expSptBulananBonus(){
		$company = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$sQuery = "CALL sp_espt_bulanan_bonus('".$periode."')";
		$query=$this->db->query($sQuery);
		
		$datestring = "%Y%m";
		$time = time();
		$name = 'SPTBULANANBONUS_' . $periode . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
		
	}

	function expSptBHL(){
		$company = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$sQuery = "CALL sp_espt_bhl('".$periode."')";
		$query=$this->db->query($sQuery);
		
		$datestring = "%Y%m";
		$time = time();
		$name = 'SPTBHL_' . $periode . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
		
	}

	function expBonus(){
		$company = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$sQuery = "CALL sp_hr_lensa_bonus('".$periode."')";
		$query=$this->db->query($sQuery);
		
		$datestring = "%Y%m";
		$time = time();
		$name = 'BONUS_KARYAWAN_' . $periode . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
		
	}

}

?>