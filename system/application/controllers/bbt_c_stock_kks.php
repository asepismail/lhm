<?

class bbt_c_stock_kks extends Controller 
{
	private $lastmenu; 
    function __construct()
    {
        parent::__construct();
        $this->load->model('bbt_m_stock_kks'); 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="bbt_c_stock_kks";
		$this->load->library('form_validation');
		$this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('global_func');
        $this->load->library('session');
    }

    function index()
    {
        $data = array();
        
        $view = "bbt_v_stock_kks";
        $data['judul_header'] = "Daftar Stock KKS";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }  
	
	function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->bbt_m_stock_kks->LoadData($company));   
    }  
}

?>