<?php
class m_produksi_kebun extends Controller
{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_produksi_kebun');
		$this->load->model('model_s_close_bjr');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="m_produksi_kebun";
		$this->load->library('csvReader');
        $this->data = array();    
    }
    
    function index(){
        $view="info_m_produksi_kebun";
		
        $this->data['judul_header'] = "Monitoring Hama Penyakit Tanaman";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $this->data['periode_produksi_kebun'] = $this->global_func->drop_date2('bulan_produksi_kebun','tahun_produksi_kebun','select');
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        
        $bulan = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $tahun = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8'); 
		//$this->model_s_close_bjr->sync_data_gkm($tahun."-".$bulan);        
        echo json_encode($this->model_m_produksi_kebun->LoadData($bulan,$tahun));   
    }
	
	function LoadDataGKM(){
        
        $bulan = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $tahun = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8');      
        echo json_encode($this->model_m_produksi_kebun->LoadDataGKM($bulan,$tahun));   
    }
	
	function LoadData_Adem(){        
        $bulan = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $tahun = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8'); 
        
        echo json_encode($this->model_m_produksi_kebun->LoadData_Adem($bulan,$tahun));   
    }
	
    function set_produksi_kebun_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'));
        
        $set_produksi_kebun_periode = $this->model_m_produksi_kebun->set_produksi_kebun_periode($periode,$company);
        echo $set_produksi_kebun_periode;
    }
    function get_produksi_kebun_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $produksi_kebun_periode = $this->model_m_produksi_kebun->get_produksi_kebun_periode($company);
        
        return $produksi_kebun_periode; 
    }
   	
	function sinkron(){	
		$data_post = array();
		$data = json_decode($this->input->post('myJson'), true);
		$data_detail = $data["id"];
		$periode = $data_detail['TAHUN'].'-'.$data_detail['BULAN'];
		$ret = 0;
		
		$data_produksi_kebun=$this->model_m_produksi_kebun->get_produksi_kebun($periode);		

		if ($data_produksi_kebun[0]!=NULL){	
			foreach($data_produksi_kebun as $row){	
				$company=$this->model_m_produksi_kebun->getCompanyIDAdem($row['COMPANY_CODE']);
				if ($row['AFDELING']=='INTI'){
					$CBPatner=0;
				}else{
					$CBPatner=$this->model_m_produksi_kebun->getCBPatnerAdem($row['AFDELING']);					
				}
				
				$status_proses = $this->model_m_produksi_kebun->getStatusProsesAdem($company, $row['dateacct'], $row['AFDELING']);
				
				if ($status_proses==''){
					//var_dump('INSERT');
					$sql ="INSERT INTO z_produksi_kebun (ad_client_id, ad_org_id, dateacct, afdeling, produksi_tm, produksi_tbm, c_bpartner_id) 
							VALUES (1000000,". $company .", '". $row['dateacct'] ."', '". $row['AFDELING'] ."', ". $row['PRODUKSI_TM'] .", ". $row['PRODUKSI_TBM'] .", ". $CBPatner ." )";					
				}elseif($status_proses=='N'){
					//var_dump('UPDATE');	
					$sql ="UPDATE z_produksi_kebun SET produksi_tm=". $row['PRODUKSI_TM'] .", produksi_tbm=". $row['PRODUKSI_TBM'] ."
							WHERE ad_org_id = ". $company ." AND dateacct='". $row['dateacct'] ."' AND afdeling='". $row['AFDELING'] ."'";				
				}elseif($status_proses=='Y'){
					//var_dump('NO INSERT UPDATE');					
				}
				//var_dump($sql);
				$pgsql = $this->load->database('adem', TRUE);
				$pgsql->query($sql);
				
				$ret +=$pgsql->affected_rows($sql);					
			}//foreach		
		} // data_produksi_kebun lhm not empty
		
		$data_produksi_kebun_gkm=$this->model_m_produksi_kebun->get_produksi_kebun_gkm($periode);		

		if ($data_produksi_kebun_gkm[0]!=NULL){	
			foreach($data_produksi_kebun_gkm as $row){	
				$company=$this->model_m_produksi_kebun->getCompanyIDAdem($row['COMPANY_CODE']);
				if ($row['AFDELING']=='INTI'){
					$CBPatner=0;
				}else{
					$CBPatner=$this->model_m_produksi_kebun->getCBPatnerAdem($row['AFDELING']);					
				}
				$status_proses = $this->model_m_produksi_kebun->getStatusProsesAdem($company, $row['dateacct'], $row['AFDELING']);
				
				if ($status_proses==''){
					//var_dump('INSERT');
					$sql ="INSERT INTO z_produksi_kebun (ad_client_id, ad_org_id, dateacct, afdeling, produksi_tm, produksi_tbm, c_bpartner_id) 
							VALUES (1000000,". $company .", '". $row['dateacct'] ."', '". $row['AFDELING'] ."', ". $row['PRODUKSI_TM'] .", ". $row['PRODUKSI_TBM'] .", ". $CBPatner ." )";					
				}elseif($status_proses=='N'){
					//var_dump('UPDATE');	
					$sql ="UPDATE z_produksi_kebun SET produksi_tm=". $row['PRODUKSI_TM'] .", produksi_tbm=". $row['PRODUKSI_TBM'] ."
							WHERE ad_org_id = ". $company ." AND dateacct='". $row['dateacct'] ."' AND afdeling='". $row['AFDELING'] ."'";				
				}elseif($status_proses=='Y'){
					//var_dump('NO INSERT UPDATE');					
				}
				//var_dump($sql);
				//$pgsql = $this->load->database('adem', TRUE);
				$pgsql->query($sql);
				
				$ret +=$pgsql->affected_rows($sql);
						
			}//foreach		
		} // data_produksi_kebun not empty
		$return['status']=  $ret." berhasil disinkron";	
    	echo json_encode($return);	
    }
	
	function sinkron_percompany(){	
		$data_post = array();
		$data = json_decode($this->input->post('myJson'), true);
		$data_detail = $data["id"];
		$periode = $data_detail['TAHUN'].'-'.$data_detail['BULAN'];
		$pt = $data_detail['COMPANY'];
		$ret = 0;
		//var_dump((!($pt=="GKM"||$pt=="SML")));
		if (!($pt=="GKM"||$pt=="SML")){
			$data_produksi_kebun=$this->model_m_produksi_kebun->get_produksi_kebun_percompany($periode, $pt);		
	
			if ($data_produksi_kebun[0]!=NULL){	
				foreach($data_produksi_kebun as $row){	
					$company=$this->model_m_produksi_kebun->getCompanyIDAdem($row['COMPANY_CODE']);
					if ($row['AFDELING']=='INTI'){
						$CBPatner=0;
					}else{
						$CBPatner=$this->model_m_produksi_kebun->getCBPatnerAdem($row['AFDELING']);					
					}
					
					$status_proses = $this->model_m_produksi_kebun->getStatusProsesAdem($company, $row['dateacct'], $row['AFDELING']);
					
					if ($status_proses==''){
						$sql ="INSERT INTO z_produksi_kebun (ad_client_id, ad_org_id, dateacct, afdeling, produksi_tm, produksi_tbm, c_bpartner_id) 
								VALUES (1000000,". $company .", '". $row['dateacct'] ."', '". $row['AFDELING'] ."', ". $row['PRODUKSI_TM'] .", ". $row['PRODUKSI_TBM'] .", ". $CBPatner ." )";					
					}elseif($status_proses=='N'){
	
						$sql ="UPDATES z_produksi_kebun SET produksi_tm=". $row['PRODUKSI_TM'] .", produksi_tbm=". $row['PRODUKSI_TBM'] ."
								WHERE ad_org_id = ". $company ." AND dateacct='". $row['dateacct'] ."' AND afdeling='". $row['AFDELING'] ."'";				
					}elseif($status_proses=='Y'){
				
					}
	
					$pgsql = $this->load->database('adem', TRUE);
					$pgsql->query($sql);
					
					$ret +=$pgsql->affected_rows($sql);					
				}//foreach		
			} // data_produksi_kebun lhm not empty
		}else{
			
			$data_produksi_kebun_gkm=$this->model_m_produksi_kebun->get_produksi_kebun_percompany_gkm($periode, $pt);		
	
			if ($data_produksi_kebun_gkm[0]!=NULL){	
				foreach($data_produksi_kebun_gkm as $row){	
					$company=$this->model_m_produksi_kebun->getCompanyIDAdem($row['COMPANY_CODE']);
					if ($row['AFDELING']=='INTI'){
						$CBPatner=0;
					}else{
						$CBPatner=$this->model_m_produksi_kebun->getCBPatnerAdem($row['AFDELING']);					
					}
					$status_proses = $this->model_m_produksi_kebun->getStatusProsesAdem($company, $row['dateacct'], $row['AFDELING']);
					
					if ($status_proses==''){
						$sql ="INSERT INTO z_produksi_kebun (ad_client_id, ad_org_id, dateacct, afdeling, produksi_tm, produksi_tbm, c_bpartner_id) 
								VALUES (1000000,". $company .", '". $row['dateacct'] ."', '". $row['AFDELING'] ."', ". $row['PRODUKSI_TM'] .", ". $row['PRODUKSI_TBM'] .", ". $CBPatner ." )";					
					}elseif($status_proses=='N'){
						$sql ="UPDATE z_produksi_kebun SET produksi_tm=". $row['PRODUKSI_TM'] .", produksi_tbm=". $row['PRODUKSI_TBM'] ."
								WHERE ad_org_id = ". $company ." AND dateacct='". $row['dateacct'] ."' AND afdeling='". $row['AFDELING'] ."'";				
					}elseif($status_proses=='Y'){
											
					}
					$pgsql = $this->load->database('adem', TRUE);
					$pgsql->query($sql);
					
					$ret +=$pgsql->affected_rows($sql);
							
				}//foreach		
			} // data_produksi_kebun not empty
		} // GKM
		//var_dump($sql);
		$return['status']=  $ret." berhasil disinkron";	
    	echo json_encode($return);	
    }
    
	function xls_month(){
		$company_code = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');	
		$bulan = substr($periode,4,2);
		$tahun = substr($periode,0,4);
		$judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();
		$headers .= "PT. ".$company."\n";
		$headers .= "MONITORING HAMA PENYAKIT TANAMAN \n";
		$headers .= "PERIODE: ". $periode ."\n";
		$headers .= "\n";
		$headers .= "NO. \t";
		$headers .= "BLOK TANAH \t";
		$headers .= "PLANTED (HA) \t";
		$headers .= "POKOK \t";	
		$headers .= "SPH \t";	
		$headers .= "TGL DETEKSI \t";
		$headers .= "TIPE produksi_kebun \t";
		$headers .= "PKK SAMPLE \t";
		$headers .= "PKK TRS \t";
		$headers .= "TIPE KATAGORI \t";		
		$headers .= "IS(%) \t";
		$headers .= "LS \t";
		
		$no = 1;
		$is=0;
		$ls=0;
		$tipe_katagori='';
		$data=$this->model_m_produksi_kebun->get_xls($company_code, $bulan, $tahun);
		if($data!=NULL){
			foreach ($data as $row){
				//var_dump($row['BLOCKID']." ". $row['PKK_SAMPLE']);
				if ($row['PKK_SAMPLE']==0 || $row['PKK_SAMPLE']=='' || $row['PKK_SAMPLE']==null){
					$is=0;
					$ls=0;
				}else{
					$is=($row['PKK_TRS']/$row['PKK_SAMPLE'])*100;
					$ls=($is*$row['HECTPLANTED'])/100;
				}
				
				if($row['TIPE_KATAGORI']=='N'){
					$tipe_katagori='NIHIL';
				}else if($row['TIPE_KATAGORI']=='R'){
					$tipe_katagori='RINGAN';
				}else if($row['TIPE_KATAGORI']=='S'){
					$tipe_katagori='SEDANG';
				}else if($row['TIPE_KATAGORI']=='B'){
					$tipe_katagori='BERAT';
				}else{
					$tipe_katagori='';
				}
				
				$line = '';
				$line .= str_replace('"', '""',$no)."\t"; 
				$line .= str_replace('"', '""',$row['BLOCKID'])."\t";
				$line .= str_replace('"', '""',$row['HECTPLANTED'])."\t";
				$line .= str_replace('"', '""',$row['NUMPLANTATION'])."\t";
				$line .= str_replace('"', '""',$row['SPH'])."\t";
				$line .= str_replace('"', '""',$row['TGL_DETEKSI'])."\t";
				$line .= str_replace('"', '""',$row['TIPE_produksi_kebun'])."\t";
				$line .= str_replace('"', '""',$row['PKK_SAMPLE'])."\t";
				$line .= str_replace('"', '""',$row['PKK_TRS'])."\t";
				$line .= str_replace('"', '""',$tipe_katagori)."\t";
				$line .= str_replace('"', '""',$is)."\t";
				$line .= str_replace('"', '""',$ls)."\t";
				$no++;
				$data .= trim($line)."\n"; 
			
			}
			$data = str_replace("\r","",$data);
			$data = str_replace("Array","",$data);
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=produksi_kebun_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
		}
		
	}
}
?>
