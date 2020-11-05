<?

class generate_pph extends Controller 
{
    
	function generate_pph ()
	{
		parent::Controller();	

		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->model('model_c_user_auth');
        $this->lastmenu="generate_pph";
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->database();
	}

	function index()
    {
        $view = "generate_pph";
        $data = array();
        $data['judul_header'] = "Generate PPH 21 Karyawan";
        $data['js'] = $this->js_pph();
            
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
                show($view, $data);
            } 
        } else {
            redirect('login');
        }    
    }      
	
	function js_pph(){
        $js = "
        jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + $('#bulan').val();                
                
            });";
        return $js;
    }
	
	function drop_temp() {
	
		$query = $this->db->query("DROP TEMPORARY TABLE IF EXISTS rptdu;");
		
	}
	
	function rpttbdu($company, $from, $to) {
	
		$query = $this->db->query("CALL sp_tb_rptdu('".$company."','".$from."','".$to."')");
		
	
	}
	
	function gen_pph($company, $from, $to) {
	
		$query = $this->db->query("CALL sp_update_pph21('".$company."','".$from."','".$to."')");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result; 
	}
	
	function execute() {
		
		$this->drop_temp();
		$this->rpttbdu('LIH','20110701','20110731');
		$data_pph = $this->gen_pph('LIH','20110701','20110731');
		$result = "";
		foreach ( $data_pph as $row)
		{
			$result .= $row['result'];
		}
		
		echo "data pph 21 " . $result . " orang berhasil tergenerate";
		
		if($result != NULL){
			redirect('rpt_du');
		}		
	
	}
}

?>