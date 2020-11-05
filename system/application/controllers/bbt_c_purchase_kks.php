<?

class bbt_c_purchase_kks extends Controller 
{
	private $lastmenu; 
	
    function __construct()
    {
        parent::__construct();
        $this->load->model('bbt_m_purchase_kks'); 
		$this->load->model('bbt_m_trx_kks'); 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="bbt_c_purchase_kks";
		$this->load->library('form_validation');
		$this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('global_func');
        $this->load->library('session');
		$this->load->database();
    }

    function index()
    {
        $data = array();
        
        $view = "bbt_v_purchase_kks";
        $data['judul_header'] = "Pembelian Stock KKS";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }  
	
	function LoadData(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
       	echo json_encode($this->bbt_m_purchase_kks->LoadData($company));   
    }
	
	function gen_batch_id()
    {
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->db->select_max('BATCH_ID');
        $this->db->from('m_nursery');
		$this->db->WHERE('COMPANY_CODE',$company);
        $temp= $this->db->get();
        $this->db->close();
        $temp = $temp->result_array();
        
		$hasil = "";
		switch ($company) {
			case "MIA": $hasil = "113"; break;
			case "LIH": $hasil = "133"; break;
			case "SAP": $hasil = "163"; break;
			case "SSS": $hasil = "143"; break;
			case "MSS": $hasil = "153"; break;
			case "TPAI": $hasil = "173"; break;
		}
		
        if(empty($temp[0]['BATCH_ID'])) { 
			$hasil = $hasil."001"; 
		} else {
            $str = $temp[0]['BATCH_ID'];
            $str = substr($str,3,3);
            $str = $str+1;
            
            $panjangString = 3;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++) { $hasil .= "0"; }
            $hasil .= $str;
        }
        echo $hasil;
    }
	
	function gen_batch_code()
    {  
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$tipe = $this->uri->segment(3);
		$namaPK = 'NURSERYCODE';
        $this->db->select_max($namaPK);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->like('NURSERYCODE',$tipe, 'after');
		$this->db->from('m_nursery');
        $temp= $this->db->get();
        $this->db->close();
        $temp = $temp->result_array();
        $hasil = $tipe;
        if(empty($temp[0]['NURSERYCODE'])){
            $hasil = $hasil."01";
        } else {
            $str = $temp[0][$namaPK];
            $str = substr($str,2,2);
            $str = $str+1;
            $panjangString = 2;
            $jumlahNol = $panjangString - strlen($str);
            for($i =0;$i<$jumlahNol;$i++) { $hasil .= "0"; }
            $hasil .= $str;
        }
        echo $hasil;
    }
	
	function gen_saldo_id()
    {  
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$hasil = "";
		$namaPK = 'SKKS_ID';
        $this->db->select_max($namaPK);
		$this->db->from('bbt_p_stok_kks');
        $temp= $this->db->get();
        $this->db->close();
        $temp = $temp->result_array();
        
		if(empty($temp[0]['SKKS_ID'])){
            $hasil = $hasil."100001";
        } else {
            $str = $temp[0][$namaPK];
            $str = $str+1;
            $panjangString = 6;
            $jumlahNol = $panjangString - strlen($str);
            for($i =0;$i<$jumlahNol;$i++) { $hasil .= "0"; }
            $hasil .= $str;
        }
        return $hasil;
    }
	
	function AddNew()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		/* pembelian begining 1 */
        $datapost['TRANSACTION_ID'] = $this->global_func->create_trans_id('bbt_p_transaction','TRANSACTION_ID','1000001','1');
		$datapost['MOVEMENTTYPE_ID'] = '11001';
		$datapost['MOVEMENTDATE']=htmlentities($this->input->post('i_tgl_purc'),ENT_QUOTES,'UTF-8');
		$datapost['BATCH_ID']=htmlentities($this->input->post('i_batch_id'),ENT_QUOTES,'UTF-8');
        $datapost['SOURCE_TYPE']=htmlentities($this->input->post('i_tp_batch'),ENT_QUOTES,'UTF-8');
        $datapost['TRANSACTION_DESC']=htmlentities($this->input->post('i_desc_purc'),ENT_QUOTES,'UTF-8');
        $datapost['SOURCE']=htmlentities($this->input->post('i_asal_penerimaan'),ENT_QUOTES,'UTF-8');
        $datapost['NO_DOCUMENT']=htmlentities($this->input->post('i_dok_penerimaan'),ENT_QUOTES,'UTF-8');
        $datapost['OPENING_QTY_SSTOCK']=0;
        $datapost['SQTY_MOVEMENT']=htmlentities($this->input->post('i_qty'),ENT_QUOTES,'UTF-8');
		$datapost['ENDING_QTY_SSTOCK']=htmlentities($this->input->post('i_qty'),ENT_QUOTES,'UTF-8');
        $datapost['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $datapost['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['INPUT_DATE']=date ("Y-m-d H:i:s");
		$act = htmlentities($this->input->post('act'),ENT_QUOTES,'UTF-8');
        
		$datapostns['BATCH_ID']=htmlentities($this->input->post('i_batch_id'),ENT_QUOTES,'UTF-8');
		$datapostns['NURSERYCODE']=htmlentities($this->input->post('i_kd_batch'),ENT_QUOTES,'UTF-8');
		$datapostns['DESCRIPTION']=htmlentities($this->input->post('i_desc_batch'),ENT_QUOTES,'UTF-8');
		$datapostns['DATE_PURCHASED']=htmlentities($this->input->post('i_tgl_purc'),ENT_QUOTES,'UTF-8');
		$datapostns['VARIETAS']=htmlentities($this->input->post('i_varietas'),ENT_QUOTES,'UTF-8');
		$datapostns['STATUS']= 1;
		$datapostns['INACTIVE']= 0;
		$datapostns['SOURCETYPE']=htmlentities($this->input->post('i_tp_batch'),ENT_QUOTES,'UTF-8');
		$datapostns['QTYORDERED']=htmlentities($this->input->post('i_qty'),ENT_QUOTES,'UTF-8');
		$datapostns['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $datapostns['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapostns['INPUT_DATE']=date ("Y-m-d H:i:s");
		$datapostns['Action']= $act;
		
		$datapostloc['LOCATION_CODE']=htmlentities($this->input->post('i_kd_batch'),ENT_QUOTES,'UTF-8');
		$datapostloc['LOCATION_TYPE_CODE']= 'NS';
		$datapostloc['DESCRIPTION']=htmlentities($this->input->post('i_desc_batch'),ENT_QUOTES,'UTF-8');
		$datapostloc['INACTIVE']= 1;
		$datapostloc['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		
		$datapostsaldo['SKKS_ID']=$this->gen_saldo_id();
		$datapostsaldo['BATCH_ID']=htmlentities($this->input->post('i_batch_id'),ENT_QUOTES,'UTF-8');
		$datapostsaldo['BATCH_TYPE']=htmlentities($this->input->post('i_tp_batch'),ENT_QUOTES,'UTF-8');
		$datapostsaldo['NUM_TUNGGAL']=htmlentities($this->input->post('i_qty'),ENT_QUOTES,'UTF-8');
		$datapostsaldo['NUM_TOTAL']=htmlentities($this->input->post('i_qty'),ENT_QUOTES,'UTF-8');
		$datapostsaldo['INACTIVE']= 0;
		$datapostsaldo['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$datapostsaldo['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$datapostsaldo['INPUT_DATE']=date ("Y-m-d H:i:s");
		
		if(isset($act))
        {
        	$trans = $this->bbt_m_purchase_kks->AddNew($datapost);
			if( $trans = 1 ){
				$mns = $this->bbt_m_purchase_kks->AddNewNs($datapostns);
				if( $mns = 1 ){
					$mloc = $this->bbt_m_purchase_kks->AddNewLoc($datapostloc);
					if( $mloc = 1 ){
						$saldo = $this->bbt_m_purchase_kks->AddNewSaldo($datapostsaldo);
						if( $saldo = 1 ){
							echo "data berhasil tersimpan";
						} else {
							echo "data gagal tersimpan";
						}
					} else {
						echo "data gagal tersimpan";
					}
				} else {
					echo "data gagal tersimpan";
				}
			} else {
				echo "data gagal tersimpan";
			}
		}
        else
        {
            $datapost['Action'] ="";
        }
    }
	
	
}

?>