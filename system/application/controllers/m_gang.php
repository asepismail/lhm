<?

class m_gang extends Controller 
{
    
	function m_gang ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_gang' ); 
		
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_gang";
        
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
		$view = "info_m_gang";
		$data = array();
		$data['judul_header'] = "Master Data Karyawan Kemandoran";
		$data['js'] = "";
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['speriode'] = $this->global_func->drop_date2('sbulan','stahun','select');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}
    }    
	
	function search_gang()
    {
		$gc = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_m_gang->grid_gang($gc, $company));
    }
    
    function create( )
    {    
			$data_post['GANG_CODE'] = strtoupper($this->input->post( 'GANG_CODE' ));
			$data_post['KERANI_CODE'] = $this->input->post( 'KERANI_CODE' );
			$data_post['MANDORE1_CODE'] = $this->input->post( 'MANDORE1_CODE' );
			$data_post['MANDORE_CODE'] = $this->input->post( 'MANDORE_CODE' );
			$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
			$data_post['DEPARTEMEN_CODE'] = $this->input->post( 'DEPARTEMEN_CODE' );
			$data_post['DIVISION_CODE'] = $this->input->post( 'DIVISION_CODE' );
			$data_post['COMPANY_CODE'] = $this->session->userdata('DCOMPANY');
			if($data_post['KERANI_CODE'] == 'undefined'){
				$data_post['KERANI_CODE'] = NULL;
			}
			if($data_post['DEPARTEMEN_CODE'] == '0'){
				$data_post['DEPARTEMEN_CODE'] = NULL;
			}
			if($data_post['DIVISION_CODE'] == '0'){
				$data_post['DIVISION_CODE'] = NULL;
			}
			$insert_id = $this->model_m_gang->insert_m_gang( $data_post );
    }

    function update()
    {
        	$gc = $this->uri->segment(3);
			$company = $this->session->userdata('DCOMPANY');
			$data_post['GANG_CODE'] = $this->input->post( 'GANG_CODE' );
			$data_post['KERANI_CODE'] = $this->input->post( 'KERANI_CODE' );
			$data_post['MANDORE1_CODE'] = $this->input->post( 'MANDORE1_CODE' );
			$data_post['MANDORE_CODE'] = $this->input->post( 'MANDORE_CODE' );
			$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
			$data_post['DEPARTEMEN_CODE'] = $this->input->post( 'DEPARTEMEN_CODE' );
			$data_post['DIVISION_CODE'] = $this->input->post( 'DIVISION_CODE' );
			$data_post['COMPANY_CODE'] = $this->session->userdata('DCOMPANY');
			if($data_post['KERANI_CODE'] == 'undefined'){
				$data_post['KERANI_CODE'] = NULL;
			}
			if($data_post['DEPARTEMEN_CODE'] == '0'){
				$data_post['DEPARTEMEN_CODE'] = NULL;
			}
			if($data_post['DIVISION_CODE'] == '0'){
				$data_post['DIVISION_CODE'] = NULL;
			}
		    $insert_id = $this->model_m_gang->update_m_gang( $gc,$company, $data_post );
	}
	
	/* delete */
	function delete_gang()
	{
		$gc = $this->input->post( 'GANG_CODE' );
		$company = $this->session->userdata('DCOMPANY');
		$this->model_m_gang->delete_m_gang($gc, $company);
	}
	
	/*look up*/
		
	function search_mandor(){
		$company = $this->session->userdata('DCOMPANY');
		$nik_m = $_REQUEST['q'];
		$data_gang = $this->model_m_gang->cek_mandor($nik_m,$company);

		$gangc = array();
		foreach($data_gang as $row)
			{
				$gangc[] = '{res_id:"'.str_replace('"','\\"',$row['NIK']).'",res_name:"'.str_replace('"','\\"',$row['NAMA']).'",res_dl:"'.str_replace('"','\\"',$row['NIK']. "&nbsp; - &nbsp;" .$row['NAMA']).'"}';
			}
			  echo '['.implode(',',$gangc).']'; exit; 
	}
	
	function search_kerani(){
		$company = $this->session->userdata('DCOMPANY');
		$nik_k = $_REQUEST['q'];
		$data_gang = $this->model_m_gang->cek_kerani($nik_k,$company);
		
		$gangc = array();
		foreach($data_gang as $row)
			{
				$gangc[] = '{res_id:"'.str_replace('"','\\"',$row['NIK']).'",res_name:"'.str_replace('"','\\"',$row['NAMA']).'",res_dl:"'.str_replace('"','\\"',$row['NIK']. "&nbsp; - &nbsp;" .$row['NAMA']).'"}';
			}
			  echo '['.implode(',',$gangc).']'; exit; 
	}
	
	function search_mandori(){
		$company = $this->session->userdata('DCOMPANY');
		$nik_k = $_REQUEST['q'];
		$data_gang = $this->model_m_gang->cek_mandori($nik_k,$company);
				
		$gangc = array();
		foreach($data_gang as $row)
			{
				$gangc[] = '{res_id:"'.str_replace('"','\\"',$row['NIK']).'",res_name:"'.str_replace('"','\\"',$row['NAMA']).'",res_dl:"'.str_replace('"','\\"',$row['NIK']. "&nbsp; - &nbsp;" .$row['NAMA']).'"}';
			}
			  echo '['.implode(',',$gangc).']'; exit; 
	}
	
	function search_nik_kosong(){
		$company = $this->session->userdata('DCOMPANY');
		$periode = $this->uri->segment(3);
		$nik_k = $_REQUEST['q'];
		$data_gang = $this->model_m_gang->cek_nik_kosong($nik_k,$periode,$company);
				
		$gangc = array();
		foreach($data_gang as $row)
			{
				$gangc[] = '{res_id:"'.str_replace('"','\\"',$row['NIK']).'",res_name:"'.str_replace('"','\\"',$row['NAMA']).'",res_dl:"'.str_replace('"','\\"',$row['NIK']. "&nbsp; - &nbsp;" .$row['NAMA']).'"}';
			}
			  echo '['.implode(',',$gangc).']'; exit; 
	}

}

?>
