<?

class bbt_c_distribusi_kks extends Controller 
{
	private $lastmenu; 
	
    function __construct()
    {
        parent::__construct();
        $this->load->model('bbt_m_distribusi_kks'); 
		$this->load->model('bbt_m_trx_kks'); 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="bbt_c_distribusi_kks";
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
        
        $view = "bbt_v_distribusi_kks";
        $data['judul_header'] = "Distribusi Stock KKS";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$data['mvtype'] = $this->dropdownlist_ttrans();
		$data['batch'] = $this->dropdownlist_batch();
		$data['terminal'] = $this->dropdownlist_terminal();
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }  
	
	function LoadData(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
       	echo json_encode($this->bbt_m_distribusi_kks->LoadData($company));   
    }
	
	function dropdownlist_ttrans()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_tp_trans' class='select' id='i_tp_trans' style='width:180px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		
		$data_mv = $this->bbt_m_distribusi_kks->get_mvtype();
		
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
		
		$data_batch = $this->bbt_m_distribusi_kks->get_batch($company);
		
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
	
	function dropdownlist_terminal()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_terminal' class='select' id='i_terminal' style='width:220px;display:none' >";
		$string .= "<option value=''> -- pilih -- </option>";
		
		$data_batch = $this->bbt_m_distribusi_kks->get_terminal($company);
		
		foreach ( $data_batch as $r)
		{
			if( (isset($default)))
			{
				$string = $string." <option value=\"".$r['NS_LOCATION_CODE']."\"  selected>".$r['DESCRIPTION']." </option>";
			}
			else
			{
				$string = $string." <option value=\"".$r['NS_LOCATION_CODE']."\">".$r['DESCRIPTION']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
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
	
	function getgudang($company){
		$datagudang = $this->bbt_m_distribusi_kks->get_gudang($company);
		$gudang = "";
		foreach ( $datagudang as $row ){
			$gudang = $row['NS_LOCATION_CODE'];
		}
		return $gudang;
	}
	
	function gen_batch_code()
    {  
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		//$tipe = $this->uri->segment(3);
		$namaPK = 'NURSERYCODE';
        $this->db->select_max('NURSERYCODE');
		$this->db->where('COMPANY_CODE',$company);
		$this->db->like('NURSERYCODE','MN', 'after');
		$this->db->from('m_nursery');
        $temp= $this->db->get();
        $this->db->close();
        $temp = $temp->result_array();
        $hasil = '';
        if(empty($temp[0]['NURSERYCODE'])){
            $hasil = $hasil."01";
        } else {
            $str = $temp[0]['NURSERYCODE'];
            $str = substr($str,2,2);
            $str = $str+1;
            $panjangString = 2;
            $jumlahNol = $panjangString - strlen($str);
            for($i =0;$i<$jumlahNol;$i++) { $hasil .= "0"; }
            $hasil .= $str;
        }
        echo 'MN'.$hasil;
    }
	
	/* submit */
	function AddNew()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$lokasi = "";
		$terminal = htmlentities($this->input->post('i_terminal'),ENT_QUOTES,'UTF-8');
		$nursery = htmlentities($this->input->post('i_kd_lokasi'),ENT_QUOTES,'UTF-8');
		/* distribusi begining 1 */
        $datapost['TRANSACTION_ID'] = $this->global_func->create_trans_id('bbt_p_transaction','TRANSACTION_ID','3000001','3');
		$datapost['MOVEMENTTYPE_ID'] = htmlentities($this->input->post('i_tp_trans'),ENT_QUOTES,'UTF-8');
		$datapost['MOVEMENTDATE']=htmlentities($this->input->post('i_tgl_trans'),ENT_QUOTES,'UTF-8');
		$datapost['BATCH_ID']=htmlentities($this->input->post('i_batch_id'),ENT_QUOTES,'UTF-8');
        $datapost['TRANSACTION_DESC']=htmlentities($this->input->post('i_desc_trans'),ENT_QUOTES,'UTF-8');
        $datapost['LOCATION_TYPE_CODE']=htmlentities($this->input->post('i_tp_lokasi'),ENT_QUOTES,'UTF-8');
        $datapost['LOCATION_CODE']= "" ;
        $datapost['OPENING_QTY_SSTOCK']=0;
		$datapost['OPENING_QTY_DSTOCK']=0;
        $datapost['SQTY_MOVEMENT']=htmlentities($this->input->post('i_qty_single'),ENT_QUOTES,'UTF-8');
		$datapost['DQTY_MOVEMENT']=htmlentities($this->input->post('i_qty_double'),ENT_QUOTES,'UTF-8');
		$datapost['ENDING_QTY_SSTOCK']=htmlentities($this->input->post('i_qty'),ENT_QUOTES,'UTF-8');
		$datapost['ENDING_QTY_DSTOCK']=htmlentities($this->input->post('i_qty'),ENT_QUOTES,'UTF-8');
        $datapost['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $datapost['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['INPUT_DATE']=date ("Y-m-d H:i:s");
		$act = htmlentities($this->input->post('act'),ENT_QUOTES,'UTF-8');
		
		if($terminal != ""){
			$lokasi = $terminal;
		} else if($nursery != "") {
			$datapost['LOCATION_CODE'] = $nursery;
			$datapostns['NURSERYCODE'] = $nursery;
			$datapostns['BATCH_ID']=htmlentities($this->input->post('i_batch_id'),ENT_QUOTES,'UTF-8');
			$datapostns['DESCRIPTION'] = "Main Nursery Batch ". substr($nursery,2,2);
			$datapostns['DATEPLANTED']=htmlentities($this->input->post('i_tgl_trans'),ENT_QUOTES,'UTF-8');
			$datapostns['STATUS']=2;
			$datapostns['SOURCETYPE']=substr($nursery,0,2);
			$datapostns['QTYONHAND']= htmlentities($this->input->post('i_qty_single'),ENT_QUOTES,'UTF-8') +
		 							  htmlentities($this->input->post('i_qty_double'),ENT_QUOTES,'UTF-8');
			$datapostns['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
       	 	$datapostns['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        	$datapostns['INPUT_DATE']=date ("Y-m-d H:i:s");
			$datapostns['Action']="distributed";
			if(isset($act))
			{
				$cekns = $this->bbt_m_distribusi_kks->cek_exist_data_nursery($nursery,$company,1);
				if($cekns == 0) {
					$insertns = $this->bbt_m_distribusi_kks->AddNewNs($datapostns);
					if( $insertns = 1){
						$cekloc = $this->bbt_m_distribusi_kks->cek_exist_data_nursery($nursery,$company,2);
						if($cekloc == 0) {
							$datapostloc['LOCATION_CODE'] = $nursery;
							$datapostloc['LOCATION_TYPE_CODE'] = "NS";
							$datapostloc['DESCRIPTION'] = "Main Nursery Batch ". substr($nursery,2,2);
							$datapostloc['INACTIVE'] = "1";
							$datapostloc['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
							$insertloc = $this->bbt_m_distribusi_kks->AddNewLoc($datapostloc);
							if( $insertloc = 1){
								$datapostsaldo['SKKS_ID']=$this->gen_saldo_id();
								$datapostsaldo['BATCH_ID']=htmlentities($this->input->post('i_batch_id'),ENT_QUOTES,'UTF-8');
								$datapostsaldo['BATCH_TYPE']=substr($nursery,0,2);
								$datapostsaldo['NUM_TUNGGAL']=htmlentities($this->input->post('i_qty_single'),ENT_QUOTES,'UTF-8');
								$datapostsaldo['NUM_DOUBLE']=htmlentities($this->input->post('i_qty_double'),ENT_QUOTES,'UTF-8');
								$datapostsaldo['NUM_TOTAL']= htmlentities($this->input->post('i_qty_single'),ENT_QUOTES,'UTF-8') +
															 htmlentities($this->input->post('i_qty_double'),ENT_QUOTES,'UTF-8');	
								$datapostsaldo['INACTIVE']= 0;
								$datapostsaldo['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
								$datapostsaldo['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
								$datapostsaldo['INPUT_DATE']=date ("Y-m-d H:i:s");
								
								//saldo detail
								$datapostsaldod['SKKS_ID']=$this->gen_saldo_id();
								$datapostsaldod['STOCK_LOCATION']= $this->getgudang($company);
								$datapostsaldod['NUM_TUNGGAL']=htmlentities($this->input->post('i_qty_single'),ENT_QUOTES,'UTF-8');
								$datapostsaldod['NUM_DOUBLE']=htmlentities($this->input->post('i_qty_double'),ENT_QUOTES,'UTF-8');
								$datapostsaldod['NUM_TOTAL']= htmlentities($this->input->post('i_qty_single'),ENT_QUOTES,'UTF-8') +
															 htmlentities($this->input->post('i_qty_double'),ENT_QUOTES,'UTF-8');	
								$datapostsaldod['INACTIVE']= 0;
								$datapostsaldod['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
								$datapostsaldod['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
								$datapostsaldod['INPUT_DATE']=date ("Y-m-d H:i:s");
								$laststock = $this->bbt_m_distribusi_kks->check_exist_stock($nursery);
								if(count($laststock) == 0){ 
									$saldo = $this->bbt_m_distribusi_kks->AddNewSaldo($datapostsaldo);
									$saldodetail = $this->bbt_m_distribusi_kks->AddNewSaldodetail($datapostsaldod);
									if($saldo = 1 && $saldodetail = 1){
										$trans = $this->bbt_m_distribusi_kks->AddNew($datapost);
										if( $trans = 1 ){
											echo "data berhasil tersimpan";
										} else {
											echo "data gagal tersimpan";
										}
									}
								} else { echo "data gagal tersimpan";  } //buat update stock
							} else { echo "data gagal tersimpan"; /* kalau insertloc gagal */}		
						} else { echo "data gagal tersimpan"; } // buat update location 		
					} else { echo "data gagal tersimpan"; } //kalau ns gagal
				} else { echo $cekns; } //buat update ns
			} else { echo "data gagal tersimpan"; } //kalau $act kosong
		} /* end insert nursery */
		

		/* $datapostns['BATCH_ID']=htmlentities($this->input->post('i_batch_id'),ENT_QUOTES,'UTF-8');
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
		$datapostsaldo['INPUT_DATE']=date ("Y-m-d H:i:s"); */
		
    }
	
}

?>