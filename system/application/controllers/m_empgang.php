<?

class m_empgang extends Controller 
{
    
	function m_empgang ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_empgang' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
	}

    function index()
    {
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		
		if ($data['login_id'] == TRUE){
			$this->load->view('info_m_empgang', $data);
		} else {
			redirect('login');
		}
    }    
	
	function search_empgang()
    {
		$gc = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
		$periode = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_empgang->search_empgang($gc, $periode, $company));
    }
    
    function search_empgang_detail()
    {
        $nik=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $name=htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $gc = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment(6),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        //echo "nik: ".$nik."- name: ".$name."- GC: ".$gc."- Periode: ".$periode."- COMP: ".$company;
        echo json_encode($this->model_m_empgang->search_empgang_detail($name,$nik,$gc, $periode, $company));
    }
	
    function create_empgang( )
    {  
		$gc = $this->input->post( 'GANG_CODE' );
		$nik = $this->input->post( 'EMPLOYEE_CODE' );
		$bulan = $this->input->post( 'MONTH' );
		$tahun = $this->input->post( 'YEAR' );
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
			
		$data_post['GANG_CODE'] = $gc;
		$data_post['EMPLOYEE_CODE'] = $nik; 
		$data_post['MONTH'] = $bulan;
		$data_post['YEAR'] = $tahun;
		$data_post['COMPANY_CODE'] = $company;
    				
		$data_karyawan = $this->model_m_empgang->cek_exist_employee($company,$nik);
        if($data_karyawan > 0)
        {
			$data_karyawan = $this->model_m_empgang->cek_exist_empgang($company,$nik,$tahun.$bulan);
			if(count($data_karyawan) > 0 ) {
				foreach($data_karyawan as $row)
				{
					if( $nik != $row['EMPLOYEE_CODE'] && $gc != $row['GANG_CODE']){
						$this->model_m_empgang->insert_m_empgang( $data_post );
					} else {
						$status = "Data karyawan " . $row['EMPLOYEE_CODE'] . " : " . $row['NAMA'];
						$status .= " sudah terdaftar di kemandoran ".$row['GANG_CODE']."..\n";
						echo  $status;
					} 
				}
			} else {
				$this->model_m_empgang->insert_m_empgang( $data_post );
			}
		} else {
			echo "data karyawan tidak ada / belum terdaftar";
		}                                     
    }

    function edit_empgang( )
    {
    		$company = $this->session->userdata('DCOMPANY');
			$gc = $this->input->post( 'GANG_CODE' );
			$nik = $this->input->post( 'EMPLOYEE_CODE' );
			$bulan = $this->input->post( 'MONTH' );
			$tahun = $this->input->post( 'YEAR' );
			
			$data_post['GANG_CODE'] = $gc;
			$data_post['EMPLOYEE_CODE'] = $nik; 
			$data_post['MONTH'] = $bulan;
			$data_post['YEAR'] = $tahun;
			$data_post['COMPANY_CODE'] = $this->session->userdata('DCOMPANY');
			
			$this->model_m_empgang->delete_m_empgang($gc,$nik,$bulan,$tahun,$company);
	      	$insert_id = $this->model_m_empgang->insert_m_empgang( $data_post );
	}   
	
	/* delete */
	function delete_empgang()
	{
		$gc = $this->input->post( 'GANG_CODE' );
		$nik = $this->input->post( 'EMPLOYEE_CODE' );
		$bulan = $this->input->post( 'MONTH' );
		$tahun = $this->input->post( 'YEAR' );
		
		$company = $this->session->userdata('DCOMPANY');
		$this->model_m_empgang->delete_m_empgang($gc,$nik,$bulan,$tahun,$company);
	}
}

?>