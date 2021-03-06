<?php
class runmanual extends Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('mcrontodo');
		$this->load->model('model_s_analisa_panen');
		$this->load->database();		
	}
 	
	function send_email(){
			$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => '10.88.1.61',
			'smtp_port' => 25
			//'smtp_user' => 'asep.ismail@provident-agro.com',
			//'smtp_pass' => 'balicamp',
			//'smtp_auth' => TRUE
			//'smtp_user' => 'asep.ismail@provident-agro.com',
			//'smtp_pass' => 'balicamp',
			//'smtp_auth' => TRUE
		);
			
		$this->load->library("email", $config);
		$this->email->from('ismail.asep@gmail.com','Asep Ismail'); 
		$this->email->to("asep.ismail@provident-agro.com");  //diisi dengan alamat tujuan
		$this->email->subject('A test email from CodeIgniter using Gmail'); 
		$this->email->message("I can now email from CodeIgniter using Gmail as my server!"); 
		echo $this->email->send();	
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
	
	function round_all($company, $periode, $periode_to){
		/*
		$qNab= "SELECT nab.NO_SPB FROM s_nota_angkutbuah nab
				WHERE nab.TANGGAL BETWEEN '". $periode ."' AND '". $periode_to ."' AND nab.ACTIVE = 1";	
		*/
		$qNab= "SELECT nab.NO_SPB
				FROM s_nota_angkutbuah nab 
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				INNER JOIN s_data_timbangan timbang ON nab.NO_SPB = timbang.NO_SPB 
				WHERE nab.TANGGAL BETWEEN '". $periode ."' AND '". $periode_to ."' AND nab.ACTIVE = 1
				GROUP BY nab.ID_NT_AB";	
		$dataNab =$this->db->query($qNab);
		if(!$dataNab->num_rows() == 0){
			foreach ($dataNab->result_array() as $row_nab){	
				$this->round_tonase($row_nab['NO_SPB']);
			}
		}
	}
	
	function regenerate_tonase($company, $periode,$periode_to){
		
		$query ="SELECT data_nab.ID_ANON, data_nab.NO_SPB, data_nab.BLOCK, data_total.TOTAL_BERAT_EMPIRIS AS TOTAL_BERAT_EMPIRIS, 
(data_nab.JANJANG*data_bjr.VALUE) AS BERAT_EMPIRIS, data_nab.JANJANG, data_bjr.VALUE AS BJR, data_timbang.BERAT_BERSIH, 
(((data_nab.JANJANG*data_bjr.VALUE)/data_total.TOTAL_BERAT_EMPIRIS)*data_timbang.BERAT_BERSIH) AS TONASE ,data_nab.COMPANY_CODE  
FROM
(
	SELECT nabd.ID_ANON, nab.NO_SPB, nabd.BLOCK, nabd.JANJANG, nab.COMPANY_CODE
	FROM s_nota_angkutbuah nab 
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.TANGGAL BETWEEN '".$periode."' AND '".$periode_to."' AND nab.ACTIVE =1
	AND nabd.TONASE = 0 AND nab.COMPANY_CODE = '".$company."'
) data_nab
INNER JOIN (
	SELECT bj.BLOCK,bj.VALUE,bj.COMPANY_CODE 
	FROM(
		SELECT AFD,BLOCK, VALUE, CONCAT(TAHUN,BULAN) AS PERIODE, COMPANY_CODE 
		FROM s_data_bjr  
		WHERE ACTIVE=1 )
bj
	JOIN (
		SELECT AFD,BLOCK,MAX(CONCAT(TAHUN,BULAN)) AS MAX_PERIODE
		FROM s_data_bjr
		WHERE CONCAT(TAHUN,BULAN) <= DATE_FORMAT('".$periode."','%Y%m') AND ACTIVE=1
		GROUP BY BLOCK 
	) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK AND bjr.MAX_PERIODE = bj.PERIODE
	
	GROUP BY bj.BLOCK,COMPANY_CODE
) data_bjr ON data_nab.BLOCK = data_bjr.BLOCK AND data_nab.COMPANY_CODE = data_bjr.COMPANY_CODE
INNER JOIN (
	SELECT data_nab.NO_SPB, SUM(data_nab.JANJANG*data_bjr.VALUE) AS TOTAL_BERAT_EMPIRIS
	FROM
	(
		SELECT nab.NO_SPB, nabd.BLOCK,nabd.JANJANG, nab.COMPANY_CODE
		FROM s_nota_angkutbuah nab 
		INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
		WHERE nab.TANGGAL BETWEEN '".$periode."' AND '".$periode_to."'
		AND nabd.TONASE = 0 
	) data_nab
	INNER JOIN (
		SELECT bj.BLOCK,bj.VALUE,bj.COMPANY_CODE 
		FROM(
			SELECT AFD,BLOCK, VALUE, CONCAT(TAHUN,BULAN) AS PERIODE, COMPANY_CODE 
			FROM s_data_bjr  
			WHERE ACTIVE=1 )
	bj
		JOIN (
			SELECT AFD,BLOCK,MAX(CONCAT(TAHUN,BULAN)) AS MAX_PERIODE
			FROM s_data_bjr
			WHERE CONCAT(TAHUN,BULAN) <= DATE_FORMAT('".$periode."','%Y%m') AND ACTIVE=1
			GROUP BY BLOCK 
		) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK AND bjr.MAX_PERIODE = bj.PERIODE
		
		GROUP BY bj.BLOCK,COMPANY_CODE
	) data_bjr ON data_nab.BLOCK = data_bjr.BLOCK AND data_nab.COMPANY_CODE = data_bjr.COMPANY_CODE
	GROUP BY data_nab.NO_SPB
) data_total ON data_nab.no_spb = data_total.no_spb
INNER JOIN(
	SELECT t.NO_SPB, t.BERAT_BERSIH FROM s_data_timbangan t
	WHERE t.ACTIVE = 1 AND t.TANGGALM BETWEEN '".$periode."' AND '".$periode_to."'
) data_timbang ON data_nab.no_spb = data_timbang.no_spb
;";
		$data_tonase = $this->db->query($query);
		if($data_tonase->num_rows() > 0){
			foreach ($data_tonase->result_array() as $row_tonase){
				$sUpdateNab="UPDATE s_nota_angkutbuah nab SET TOTAL_BERAT_EMPIRIS=". $row_tonase['TOTAL_BERAT_EMPIRIS'] ." WHERE NO_SPB='". $row_tonase['NO_SPB'] ."';";
				//var_dump('*************update sUpdateNab***************');
				//var_dump($sUpdateNab);
				$this->db->query($sUpdateNab);	
				$sUpdateDetail="UPDATE s_nota_angkutbuah_detail SET TONASE=". $row_tonase['TONASE'] .", BERAT_EMPIRIS=". $row_tonase['BERAT_EMPIRIS'] .", BJR=". $row_tonase['BJR'] .", UPDATE_BY='JOB_SCHEDULLER', UPDATE_TIME=NOW()
WHERE ID_ANON ='". $row_tonase['ID_ANON'] ."'";
				//var_dump('*************update sUpdateDetail***************');
				//var_dump($sUpdateDetail);
				$this->db->query($sUpdateDetail);	
				$this->round_tonase($row_tonase['NO_SPB']);
			}
		}
		
	}
	
	function round_tonase($id_nab){
		$i=0;		
		$sisa=0;
		$round_tonase=0;
		$qNab= "SELECT nabd.ID_ANON, nabd.BLOCK, nabd.TONASE, ROUND(nabd.TONASE) AS ROUND_TONASE, (nabd.TONASE - ROUND(nabd.TONASE)) AS SISA, timbang.BERAT_BERSIH FROM s_nota_angkutbuah nab 
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				INNER JOIN s_data_timbangan timbang ON nab.NO_SPB = timbang.NO_SPB 
				WHERE nab.NO_SPB = '". $id_nab ."' AND nab.ACTIVE=1";
		$dataNab =$this->db->query($qNab);
		$i=$dataNab->num_rows();
		if($i > 0){
			foreach ($dataNab->result_array() as $row_nab){				
				$sisa=$sisa+$row_nab['SISA'];
				if ($i == 1){				
					$round_tonase=round(($row_nab['ROUND_TONASE']+$sisa),0);
				}else{
					$round_tonase=$row_nab['ROUND_TONASE'];					
				}
				$i=$i-1;								
				$sUpdateDetail="UPDATE s_nota_angkutbuah_detail SET ROUND_TONASE = ". $round_tonase ." WHERE ID_ANON ='". $row_nab['ID_ANON'] ."'";
				var_dump($sUpdateDetail);
				$this->db->query($sUpdateDetail);
				//var_dump('*************update sUpdateDetail***************');
				//var_dump($sUpdateDetail);
			}
		}
		
	}
	
	function generate_progress(){
		
		$awal_bulan = '20140301';
		$yesterday = '20140301';
		$array_company = array('LIH');
				
		foreach($array_company as $i => $company){
			$this->round_all($company, $awal_bulan,$yesterday);
			$this->regenerate_tonase($company, $awal_bulan,$yesterday);
			
			if($company == 'GKM' || $company == 'SML'){ 
				$tabel1='dummy_mgangactivitydetail_gkm';
				$tabel2='dummy_pprogress_gkm';
				$db_other = $this->load->database('lhm_gkm', TRUE);	
			}else{
				$tabel1='m_gang_activity_detail';
				$tabel2='p_progress';
			}
	
			//if(!empty($periode) && !empty($company)){
			//$awal_bulan = '20130701';
			//$yesterday ='20130701';		
			$data_panen=$this->model_s_analisa_panen->runjob_nab($awal_bulan,$yesterday,$company);				
			
			if ($data_panen[0]!=NULL){
				$shi_janjang_angkut = 0; 
				$shi_berat_angkut = 0;
				$shi_janjang_panen = 0;
				$shi_berat_panen =0;
				$bjr_real = 0; 
				$location_code =''; 
				
				if($company == 'GKM' || $company == 'SML'){ 
					$status2=$this->model_s_analisa_panen->delete_progress_gkm($awal_bulan,$yesterday,$company);
				}else{				
					$status2=$this->model_s_analisa_panen->delete_progress($awal_bulan,$yesterday,$company);
				}
				$status=$this->model_s_analisa_panen->delete_rpt_nab($awal_bulan,$yesterday,$company);
				var_dump($status);
				if ($status==TRUE&&$status2==TRUE){ 
					foreach($data_panen as $row){					
						$tanggal=$row['TANGGAL'];		
						$location_code = $row['LOCATION_CODE'];					
						$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $tanggal,$company,$location_code,$tabel1);			
						$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $tanggal,$company,$location_code, $tabel1);
						$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
						$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $tanggal,$company,$location_code);	
							
						$sInsert ="INSERT INTO rpt_nab
									(DATE_TRANSACT, LHM_DATE, INPUT_BY, COMPANY_CODE, LOCATION_CODE, JUMLAH_POKOK,
									PLANTED_AREA, JANJANG_PANEN, JANJANG_PANEN_SHI, BERAT_PANEN, BERAT_PANEN_SHI,
									JANJANG_ANGKUT, JANJANG_ANGKUT_SHI, BERAT_ANGKUT, BERAT_ANGKUT_SHI, BJR_REAL,
									BJR_DITETAPKAN, JANJANG_AFKIR, JANJANG_RESTAN, HK, HASIL_KERJA, 
									HK_JUMLAH, BERAT_EMPIRIS, GANG_CODE
									)
									VALUES ('". $row['TANGGAL'] ."', '". $row['TANGGAL_PANEN_TBG'] ."', 'JOB_SCHEDULER', '".$company."','".$row['LOCATION_CODE']."', '', 
											'', '".$row['JANJANG_PANEN']."', '".$shi_janjang_panen."', '".$row['BERAT_PANEN']."', '".$shi_berat_panen."',
											'".$row['JJG_ANGKUT']."', '".$shi_janjang_angkut."', '".$row['BERAT_ANGKUT']."', '".$shi_berat_angkut."','".$row['BJR_REAL']."',
											'','".$row['JJG_AFKIR']."', '".$row['RESTAN']."', '".$row['HK']."', '".$row['HASIL_KERJA']."',
											'".$row['HK_JUMLAH']."', '', '".$row['GANG_CODE']."')";	
						
						$insert=$this->db->query($sInsert);	
						var_dump('*************insert rptnab***************');
						var_dump($sInsert);											
						
						if ($company=='MAG' || $company=='LIH' || $company=='TPAI'){										
							$sCheckPanen="SELECT GANG_CODE, LEFT(m_gang_activity_detail.LOCATION_CODE,2) AS AFD, m_gang_activity_detail.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM m_gang_activity_detail
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM m_gang_activity_detail g 
	WHERE g.LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = m_gang_activity_detail.LOCATION_CODE
WHERE LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND m_gang_activity_detail.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, m_gang_activity_detail.LOCATION_CODE";
							//var_dump('*************sCheckPanen***************');
							//var_dump($sCheckPanen);
							$qsCheckPanen = $this->db->query($sCheckPanen);	
							$sisaPtonase = 0;
							$sisaTonase = 0;
							$roundPtonase = 0;
							$i=0;
							$i=$qsCheckPanen->num_rows();
							if($i > 0){
								foreach ($qsCheckPanen->result_array() as $row_progress){									
									$pTonase=0;
									$pJanjang=0;
									//$sisaTonase=$sisaTonase+$sisaPtonase;
									if ($row_progress['TOTAL_JANJANG']!=0){
										$pTonase=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['BERAT_ANGKUT']);
										$roundPtonase = round($pTonase);
										$sisaPtonase = $pTonase-$roundPtonase;
										$sisaTonase=$sisaTonase+$sisaPtonase;

										if ($i == 1){
											$roundPtonase=$roundPtonase+$sisaTonase;	
										}else{
											$roundPtonase=$roundPtonase;		
										}
										$i=$i-1;
										$pJanjang=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['JJG_ANGKUT']);
									}else{
										$roundPtonase=0;
										$pJanjang=0;
									}
									$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row_progress['GANG_CODE'] ."', '". $row_progress['AFD'] ."', '". $row['TANGGAL'] ."', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $roundPtonase .", 'Jjg', ". $pJanjang .", 0, ". $row_progress['HK'] .",0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
									$this->db->query($insertProgress);
									var_dump('*************INSERT PROGRESS***************');
									var_dump($insertProgress);
								}								
							}
						}else if($company=='NAK'){
							$insertProgress="INSERT INTO p_progress (AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". substr($row['LOCATION_CODE'],0,2) ."', '". $row['TANGGAL'] ."', '". $row['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $row['BERAT_ANGKUT'] .", 'Jjg', ". $row['JJG_ANGKUT'] .", 0,0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
							$this->db->query($insertProgress);	
							var_dump('*************insertProgress***************');
							var_dump($insertProgress);
						}else if($company=='SAP' || $company=='MSS' || $company=='ASL' || $company=='SSS'){
							/*
							$insertProgress="INSERT INTO p_progress (AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". substr($row['LOCATION_CODE'],0,2) ."', '". $row['TANGGAL'] ."', '". $row['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $row['BERAT_PANEN'] .", 'Jjg', ". $row['JANJANG_PANEN'] .", 0,0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
							*/
							$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_TYPE_CODE, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row['GANG_CODE'] ."', '". substr($row['LOCATION_CODE'],0,2) ."', '". $row['TANGGAL'] ."', 'OP', '". $row['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $row['BERAT_PANEN'] .", 'Jjg', ". $row['JANJANG_PANEN'] .", 0,0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
							$this->db->query($insertProgress);	
							var_dump('*************insertProgress***************');
							var_dump($insertProgress);
						}else if ($company=='GKM' || $company=='SML'){										
							$sCheckPanen="SELECT GANG_CODE, LEFT(dummy_mgangactivitydetail_gkm.LOCATION_CODE,2) AS AFD, dummy_mgangactivitydetail_gkm.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM dummy_mgangactivitydetail_gkm
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM dummy_mgangactivitydetail_gkm g 
	WHERE g.LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = dummy_mgangactivitydetail_gkm.LOCATION_CODE
WHERE LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND dummy_mgangactivitydetail_gkm.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, dummy_mgangactivitydetail_gkm.LOCATION_CODE";
							//var_dump('*************sCheckPanen***************');
							//var_dump($sCheckPanen);
							$qsCheckPanen = $this->db->query($sCheckPanen);	
							$sisaPtonase = 0;
							$sisaTonase = 0;
							$roundPtonase = 0;
							$i=0;
							$i=$qsCheckPanen->num_rows();
							if($i > 0){
								foreach ($qsCheckPanen->result_array() as $row_progress){									
									$pTonase=0;
									$pJanjang=0;
									//$sisaTonase=$sisaTonase+$sisaPtonase;
									if ($row_progress['TOTAL_JANJANG']!=0){
										$pTonase=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['BERAT_ANGKUT']);
										$roundPtonase = round($pTonase);
										$sisaPtonase = $pTonase-$roundPtonase;
										$sisaTonase=$sisaTonase+$sisaPtonase;

										if ($i == 1){
											$roundPtonase=$roundPtonase+$sisaTonase;	
										}else{
											$roundPtonase=$roundPtonase;		
										}
										$i=$i-1;
										$pJanjang=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['JJG_ANGKUT']);
									}else{
										$roundPtonase=0;
										$pJanjang=0;
									}
									$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row_progress['GANG_CODE'] ."', '". $row_progress['AFD'] ."', '". $row['TANGGAL'] ."', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $roundPtonase .", 'Jjg', ". $pJanjang .", 0, ". $row_progress['HK'] .",0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
									$db_other->query($insertProgress);
									var_dump('*************INSERT PROGRESS***************');
									var_dump($insertProgress);
								}								
							}
						}
						
					}// for each 	
					//$this->regenerate_realisasi($company,$awal_bulan,$yesterday);
				}//$status
			}//$data_panen[0]			
		}//foreach company		
   	}
	
	function generate_progress_perdate($date){
		$awal_bulan = '20140201';		
		$array_company = array('TPAI');
				
		foreach($array_company as $i => $company){
			$this->round_all($company, $date,$date);
			$this->regenerate_tonase($company, $date,$date);
			
			if($company == 'GKM' || $company == 'SML'){ 
				$tabel1='dummy_mgangactivitydetail_gkm';
				$tabel2='dummy_pprogress_gkm';	
			}else{
				$tabel1='m_gang_activity_detail';
				$tabel2='p_progress';
			}
	
			//if(!empty($periode) && !empty($company)){
			//$awal_bulan = '20130701';
			//$yesterday ='20130701';		
			$data_panen=$this->model_s_analisa_panen->runjob_nab($date,$date,$company);				
			
			if ($data_panen[0]!=NULL){
				$shi_janjang_angkut = 0; 
				$shi_berat_angkut = 0;
				$shi_janjang_panen = 0;
				$shi_berat_panen =0;
				$bjr_real = 0; 
				$location_code =''; 
				
				$status=$this->model_s_analisa_panen->delete_rpt_nab($date,$date,$company);
				$status2=$this->model_s_analisa_panen->delete_progress($date,$date,$company);
				if ($status==TRUE&&$status2==TRUE){ 
					foreach($data_panen as $row){					
						$tanggal=$row['TANGGAL'];		
						$location_code = $row['LOCATION_CODE'];					
						$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $tanggal,$company,$location_code,$tabel1);			
						$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $tanggal,$company,$location_code, $tabel1);
						$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
						$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $tanggal,$company,$location_code);	
							
						$sInsert ="INSERT INTO rpt_nab
									(DATE_TRANSACT, LHM_DATE, INPUT_BY, COMPANY_CODE, LOCATION_CODE, JUMLAH_POKOK,
									PLANTED_AREA, JANJANG_PANEN, JANJANG_PANEN_SHI, BERAT_PANEN, BERAT_PANEN_SHI,
									JANJANG_ANGKUT, JANJANG_ANGKUT_SHI, BERAT_ANGKUT, BERAT_ANGKUT_SHI, BJR_REAL,
									BJR_DITETAPKAN, JANJANG_AFKIR, JANJANG_RESTAN, HK, HASIL_KERJA, 
									HK_JUMLAH, BERAT_EMPIRIS, GANG_CODE
									)
									VALUES ('". $row['TANGGAL'] ."', '". $row['TANGGAL_PANEN_TBG'] ."', 'JOB_SCHEDULER', '".$company."','".$row['LOCATION_CODE']."', '', 
											'', '".$row['JANJANG_PANEN']."', '".$shi_janjang_panen."', '".$row['BERAT_PANEN']."', '".$shi_berat_panen."',
											'".$row['JJG_ANGKUT']."', '".$shi_janjang_angkut."', '".$row['BERAT_ANGKUT']."', '".$shi_berat_angkut."','".$row['BJR_REAL']."',
											'','".$row['JJG_AFKIR']."', '".$row['RESTAN']."', '".$row['HK']."', '".$row['HASIL_KERJA']."',
											'".$row['HK_JUMLAH']."', '', '".$row['GANG_CODE']."')";	
						
						$insert=$this->db->query($sInsert);	
						var_dump('*************insert rptnab***************');
						var_dump($sInsert);											
						
						if ($company=='MAG' || $company=='LIH' || $company=='SML' || $company=='TPAI'){										
							$sCheckPanen="SELECT GANG_CODE, LEFT(m_gang_activity_detail.LOCATION_CODE,2) AS AFD, m_gang_activity_detail.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM m_gang_activity_detail
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM m_gang_activity_detail g 
	WHERE g.LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = m_gang_activity_detail.LOCATION_CODE
WHERE LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND m_gang_activity_detail.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, m_gang_activity_detail.LOCATION_CODE";
							//var_dump('*************sCheckPanen***************');
							//var_dump($sCheckPanen);
							$qsCheckPanen = $this->db->query($sCheckPanen);	
							$sisaPtonase = 0;
							$sisaTonase = 0;
							$roundPtonase = 0;
							$i=0;
							$i=$qsCheckPanen->num_rows();
							if($i > 0){
								foreach ($qsCheckPanen->result_array() as $row_progress){									
									$pTonase=0;
									$pJanjang=0;
									//$sisaTonase=$sisaTonase+$sisaPtonase;
									if ($row_progress['TOTAL_JANJANG']!=0){
										$pTonase=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['BERAT_ANGKUT']);
										$roundPtonase = round($pTonase);
										$sisaPtonase = $pTonase-$roundPtonase;
										$sisaTonase=$sisaTonase+$sisaPtonase;

										if ($i == 1){
											$roundPtonase=$roundPtonase+$sisaTonase;	
										}else{
											$roundPtonase=$roundPtonase;		
										}
										$i=$i-1;
										$pJanjang=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['JJG_ANGKUT']);
									}else{
										$roundPtonase=0;
										$pJanjang=0;
									}
									$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row_progress['GANG_CODE'] ."', '". $row_progress['AFD'] ."', '". $row['TANGGAL'] ."', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $roundPtonase .", 'Jjg', ". $pJanjang .", 0, ". $row_progress['HK'] .",0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
									$this->db->query($insertProgress);
									var_dump('*************INSERT PROGRESS***************');
									var_dump($insertProgress);
								}								
							}
						}else if($company=='NAK'){
							$insertProgress="INSERT INTO p_progress (AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". substr($row['LOCATION_CODE'],0,2) ."', '". $row['TANGGAL'] ."', '". $row['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $row['BERAT_ANGKUT'] .", 'Jjg', ". $row['JJG_ANGKUT'] .", 0,0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
							$this->db->query($insertProgress);	
							var_dump('*************insertProgress***************');
							var_dump($insertProgress);
						}else if($company=='SAP' || $company=='MSS' || $company=='ASL' || $company=='SSS'){
							$insertProgress="INSERT INTO p_progress (AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". substr($row['LOCATION_CODE'],0,2) ."', '". $row['TANGGAL'] ."', '". $row['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $row['BERAT_PANEN'] .", 'Jjg', ". $row['JANJANG_PANEN'] .", 0,0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
							$this->db->query($insertProgress);	
							var_dump('*************insertProgress***************');
							var_dump($insertProgress);
						}else if ($company=='GKM'){										
							$sCheckPanen="SELECT GANG_CODE, LEFT(dummy_mgangactivitydetail_gkm.LOCATION_CODE,2) AS AFD, dummy_mgangactivitydetail_gkm.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM dummy_mgangactivitydetail_gkm
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM dummy_mgangactivitydetail_gkm g 
	WHERE g.LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = dummy_mgangactivitydetail_gkm.LOCATION_CODE
WHERE LHM_DATE = '".$row['TANGGAL_PANEN_TBG']."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND dummy_mgangactivitydetail_gkm.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, dummy_mgangactivitydetail_gkm.LOCATION_CODE";
							//var_dump('*************sCheckPanen***************');
							//var_dump($sCheckPanen);
							$qsCheckPanen = $this->db->query($sCheckPanen);	
							$sisaPtonase = 0;
							$sisaTonase = 0;
							$roundPtonase = 0;
							$i=0;
							$i=$qsCheckPanen->num_rows();
							if($i > 0){
								foreach ($qsCheckPanen->result_array() as $row_progress){									
									$pTonase=0;
									$pJanjang=0;
									//$sisaTonase=$sisaTonase+$sisaPtonase;
									if ($row_progress['TOTAL_JANJANG']!=0){
										$pTonase=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['BERAT_ANGKUT']);
										$roundPtonase = round($pTonase);
										$sisaPtonase = $pTonase-$roundPtonase;
										$sisaTonase=$sisaTonase+$sisaPtonase;

										if ($i == 1){
											$roundPtonase=$roundPtonase+$sisaTonase;	
										}else{
											$roundPtonase=$roundPtonase;		
										}
										$i=$i-1;
										$pJanjang=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['JJG_ANGKUT']);
									}else{
										$roundPtonase=0;
										$pJanjang=0;
									}
									$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row_progress['GANG_CODE'] ."', '". $row_progress['AFD'] ."', '". $row['TANGGAL'] ."', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $roundPtonase .", 'Jjg', ". $pJanjang .", 0, ". $row_progress['HK'] .",0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
									$this->db->query($insertProgress);
									var_dump('*************INSERT PROGRESS***************');
									var_dump($insertProgress);
								}								
							}
						}
						
					}// for each 	
					//$this->regenerate_realisasi($company,$awal_bulan,$yesterday);
				}//$status
			}//$data_panen[0]			
		}//foreach company		
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
		$array_company = array('LIH');

		foreach($array_company as $i => $company){
			
			if($company == 'GKM' || $company == 'SML'){ 
				$tabel1='dummy_mgangactivitydetail_gkm';
				$tabel2='dummy_pprogress_gkm';	
			}else{
				$tabel1='m_gang_activity_detail';
				$tabel2='p_progress';
			}
		
			$data_panen=$this->model_s_analisa_panen->runjob_nab($date,$date,$company);		
			if ($data_panen[0]!=NULL){
				$shi_janjang_angkut = 0; 
				$shi_berat_angkut = 0;
				$shi_janjang_panen = 0;
				$shi_berat_panen =0;
				$bjr_real = 0; 
				$location_code =''; 
				
				$status=$this->model_s_analisa_panen->delete_rpt_nab($date,$date,$company);
				if ($status==TRUE){ 
					foreach($data_panen as $row){							
						$tanggal=$row['TANGGAL'];		
						$location_code = $row['LOCATION_CODE'];					
						$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $tanggal,$company,$location_code,$tabel1);			
						$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $tanggal,$company,$location_code, $tabel1);
						$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
						$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $tanggal,$company,$location_code);	
												
						$sInsert ="INSERT INTO rpt_nab
									(DATE_TRANSACT, LHM_DATE, INPUT_BY, COMPANY_CODE, LOCATION_CODE, JUMLAH_POKOK,
									PLANTED_AREA, JANJANG_PANEN, JANJANG_PANEN_SHI, BERAT_PANEN, BERAT_PANEN_SHI,
									JANJANG_ANGKUT, JANJANG_ANGKUT_SHI, BERAT_ANGKUT, BERAT_ANGKUT_SHI, BJR_REAL,
									BJR_DITETAPKAN, JANJANG_AFKIR, JANJANG_RESTAN, HK, HASIL_KERJA, 
									HK_JUMLAH, BERAT_EMPIRIS, GANG_CODE
									)
									VALUES ('". $row['TANGGAL'] ."', '". $row['TANGGAL_PANEN_TBG'] ."', 'JOB_SCHEDULER', '".$company."','".$row['LOCATION_CODE']."', '', 
											'', '".$row['JANJANG_PANEN']."', '".$shi_janjang_panen."', '".$row['BERAT_PANEN']."', '".$shi_berat_panen."',
											'".$row['JJG_ANGKUT']."', '".$shi_janjang_angkut."', '".$row['BERAT_ANGKUT']."', '".$shi_berat_angkut."','".$row['BJR_REAL']."',
											'','".$row['JJG_AFKIR']."', '".$row['RESTAN']."', '".$row['HK']."', '".$row['HASIL_KERJA']."',
											'".$row['HK_JUMLAH']."', '', '".$row['GANG_CODE']."')";	
									
						var_dump($sInsert);
						var_dump("----------------------");
						$insert=$this->db->query($sInsert);	
						
						//start: asep, 20140211 progress
						$query ="SELECT * FROM p_progress p WHERE p.LOCATION_CODE = '".$row['LOCATION_CODE']."' AND p.COMPANY_CODE='".$company."' AND p.TGL_PROGRESS = '". $row['TANGGAL'] ."' AND p.ACTIVITY_CODE = '8601003'";
						$sCheck = $this->db->query($query);
        				$count = $sCheck->num_rows(); 
						if($count==1){
							 if ($company=='MAG' || $company=='LIH' || $company=='GKM' || $company=='SML' || $company=='NAK' || $company=='TPAI'){
								$sUpdate="UPDATE p_progress SET HASIL_KERJA=".$row['BERAT_ANGKUT']." ,HASIL_KERJA2=".$row['JANJANG_PANEN'].", SATUAN2='Jjg', UPDATE_BY='JOB_SCHEDULLER', UPDATE_DATE =NOW() WHERE LOCATION_CODE = '".$row['LOCATION_CODE']."' AND COMPANY_CODE='".$company."' AND TGL_PROGRESS = '". $row['TANGGAL'] ."' AND ACTIVITY_CODE = '8601003'";
							}else if ($company=='SAP' || $company=='MSS' || $company=='SSS' || $company=='ASL'){
								$sUpdate="UPDATE p_progress SET HASIL_KERJA=".$row['BERAT_PANEN']." ,HASIL_KERJA2=".$row['JANJANG_PANEN'].", SATUAN2='Jjg', UPDATE_BY='JOB_SCHEDULLER', UPDATE_DATE =NOW() WHERE LOCATION_CODE = '".$row['LOCATION_CODE']."' AND COMPANY_CODE='".$company."' AND TGL_PROGRESS = '". $row['TANGGAL'] ."' AND ACTIVITY_CODE = '8601003'";
							 }
							 var_dump($sUpdate);
							 var_dump("----------------------");
							 $update=$this->db->query($sUpdate);	
						}
						//end: asep, 20140211 progress
						
					}// for each 
				}//$status
			}//$data_panen[0]
		}//foreach
   	}
}
?>