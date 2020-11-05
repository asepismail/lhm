<?php
class runmanual_asep extends Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('mcrontodo');
		$this->load->model('model_s_analisa_panen');
	}
 
	function sync_timbangan($date){	
		$array_company = array('MAG','GKM', 'LIH', 'NAK', 'TPAI');
		//$array_company = array('GKM'); //for request only

		foreach($array_company as $i => $value){
			$this->mcrontodo->synchronize($value, $date); //synchronize timbangan
			$this->mcrontodo->synchronize_bunchEmpty($value, $date); //synchronize bunchEmpty
			if ($value=='GKM'){
				$this->mcrontodo->synchronize_group($value, $date); //synchronize timbangan for GKM Group
			}
			$this->mcrontodo->synchronize_dispatch($value, $date); //synchronize dispatch
		}

	}
	
	function generate_nab($date){

		//$array_company = array('MAG', 'LIH', 'NAK', 'TPAI','GKM', 'SSS', 'SML', 'ASL');
		//$array_company = array('ASL', 'MSS', 'SAP');
		//$array_company = array('GKM','SML');
		$ar = preg_split('/[- :]/',trim($date));
        $ar = implode('',$ar); 
		$m=date("m",strtotime($ar));
		$y=date("Y",strtotime($ar));
		$awal_bulan= $y.$m."01";
		$array_company = array('MSS', 'SSS', 'ASL');

		foreach($array_company as $i => $company){
			
			if($company == 'GKM' || $company == 'SML'){ 
				$tabel1='dummy_mgangactivitydetail_gkm';
				$tabel2='dummy_pprogress_gkm';	
			}else{
				$tabel1='m_gang_activity_detail';
				$tabel2='p_progress';
			}
		
			$data_panen=$this->model_s_analisa_panen->runjob_nab($date,$date,$company);	
var_dump($data_panen);
//$status=$this->model_s_analisa_panen->delete_rpt_nab($date,$date,$company);	
			if ($data_panen[0]!=NULL){
				$shi_janjang_angkut = 0; 
				$shi_berat_angkut = 0;
				$shi_janjang_panen = 0;
				$shi_berat_panen =0;
				$bjr_real = 0; 
				$location_code =''; 
				
				//$status=$this->model_s_analisa_panen->delete_rpt_nab($date,$date,$company);				
				//if ($status==TRUE){ 
					foreach($data_panen as $row){							
						$tanggal=$row['TANGGAL'];		
						$location_code = $row['LOCATION_CODE'];					
						$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $tanggal,$company,$location_code,$tabel1);			
						$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $tanggal,$company,$location_code, $tabel1);
						$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
						$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $tanggal,$company,$location_code);	
							
						$sInsert ="INSERT INTO rpt_nab
									(DATE_TRANSACT, INPUT_BY, COMPANY_CODE, LOCATION_CODE, JUMLAH_POKOK,
									PLANTED_AREA, JANJANG_PANEN, JANJANG_PANEN_SHI, BERAT_PANEN, BERAT_PANEN_SHI,
									JANJANG_ANGKUT, JANJANG_ANGKUT_SHI, BERAT_ANGKUT, BERAT_ANGKUT_SHI, BJR_REAL,
									BJR_DITETAPKAN, JANJANG_AFKIR, JANJANG_RESTAN, HK, HASIL_KERJA, 
									HK_JUMLAH, BERAT_EMPIRIS
									)
									VALUES ('". $row['TANGGAL'] ."', 'JOB_SCHEDULER', '".$company."','".$row['LOCATION_CODE']."', '', 
											'', '".$row['JANJANG_PANEN']."', '".$shi_janjang_panen."', '".$row['BERAT_PANEN']."', '".$shi_berat_panen."',
											'".$row['JJG_ANGKUT']."', '".$shi_janjang_angkut."', '".$row['BERAT_ANGKUT']."', '".$shi_berat_angkut."','".$row['BJR_REAL']."',
											'','".$row['JJG_AFKIR']."', '".$row['RESTAN']."', '".$row['HK']."', '".$row['HASIL_KERJA']."',
											'".$row['HK_JUMLAH']."', '')";	
									
						
						$insert=$this->db->query($sInsert);	
					}// for each 
				//}//$status
			}//$data_panen[0]
		}//foreach
   	}
}
?>