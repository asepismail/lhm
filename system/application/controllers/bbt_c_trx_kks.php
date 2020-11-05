<?

class bbt_c_trx_kks extends Controller 
{
	private $lastmenu; 
		
    function __construct()
    {
        parent::__construct();
        $this->load->model('bbt_m_trx_kks'); 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="bbt_c_trx_kks";
		$this->load->library('form_validation');
		$this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('global_func');
        $this->load->library('session');
    	$company = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
	}

    function index()
    {
        $data = array();
        
        $view = "bbt_v_trx_kks";
        $data['judul_header'] = "Transaksi Keluar Masuk Bibitan";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['mvtype'] = $this->dropdownlist_ttrans();
		$data['batch'] = $this->dropdownlist_batch();
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }  
	
	function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->bbt_m_trx_kks->LoadData($company));   
    }  
	
	function dropdownlist_ttrans()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_tp_trans' class='select' id='i_tp_trans' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		
		$data_mv = $this->bbt_m_trx_kks->get_mvtype();
		
		foreach ( $data_mv as $r)
		{
			if( (isset($default)))
			{
				$string = $string." <option value=\"".$r['MOVEMENTTYPE_ID']."\"  selected>".$r['MOVEMENTNAME']." </option>";
			}
			else
			{
				$string = $string." <option value=\"".$r['MOVEMENTTYPE_ID']."\">".$r['MOVEMENTNAME']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_batch()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_kd_batch_exist' class='select' id='i_kd_batch_exist' style='width:220px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		
		$data_batch = $this->bbt_m_trx_kks->get_batch($company);
		
		foreach ( $data_batch as $r)
		{
			if( (isset($default)))
			{
				$string = $string." <option value=\"".$r['BATCH_ID']."\"  selected>".$r['DESCRIPTION']." </option>";
			}
			else
			{
				$string = $string." <option value=\"".$r['BATCH_ID']."\">".$r['DESCRIPTION']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
}

?>