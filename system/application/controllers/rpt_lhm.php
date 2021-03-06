<?
class rpt_lhm extends Controller 
{
	function rpt_lhm ()
	{
		parent::Controller();	
		$this->load->model( 'model_rpt_lhm' );
		$this->load->model( 'model_rpt_du' );
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_lhm"; 		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->load->helper('date');
	}
	
	function index(){
		$view = "rpt_lhm";
        	$data = array();
        	$data['judul_header'] = "Export Excell Data";
		$data['js'] = $this->js_rpt_lhm();    
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        	$data['d_export_data'] = $this->dropdownlist_exportdata($this->session->userdata('DCOMPANY'),$this->session->userdata('LOGINID'));
        	$data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
            		redirect('login');
        	}	
    }  
	
	function js_rpt_lhm() {
		$js = " $(function() {
				$('#RPTTO').datepicker({dateFormat:'dd-mm-yy'});
				$('#RPTFROM').datepicker({dateFormat:'dd-mm-yy'});
			});
					
			jQuery('#submitdata').click(function (){
				var jns_rpt = document.getElementById('jns_rpt').value;
				var tfrom = document.getElementById('RPTFROM').value;
				var elem = tfrom.split('-');
				from = elem[2]+elem[1]+elem[0];
						
				var tto = document.getElementById('RPTTO').value;
				var elem2 = tto.split('-');
				to = elem2[2]+elem2[1]+elem2[0];
				
				var endperiod = elem2[2]+elem2[1];
				var rpt = $('#jns_rpt').val();
					
				var period = to - from;
				if(jns_rpt == 'prj') {
					window.location = url+'rpt_lhm/generate/'+ jns_rpt + '/' + from + '/' + to;
				} else if (jns_rpt == 'prgsm') {
					var periode = $('#tahun').val() + $('#bulan').val();
					var act = 'all';			
					var afd = 'all';
                    			window.location = url+'p_progress_summary/create_excel4uat/'+endperiod+'/'+act+'/'+afd;
				} else {
					if ( period > 0 ){
						window.location = url+'rpt_lhm/generate/'+ jns_rpt + '/' + from + '/' + to;		
					} else {
						alert('rentang periode salah!!');
						return false;
					}
				}
			});";
		 return $js;
	}
	
	
	function generate () {
		$jns_rpt = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		switch($jns_rpt)
		{
			case "lhm":
				$this->generate_lhm($company, $from, $to);
			break;
			case "bk":
				$this->generate_bk($company, $from, $to);
			break;
			case "bkt":
				$this->generate_bkt($company, $from, $to);
			break;
			case "bm":
				$this->generate_bm($company, $from, $to);
			break;
			case "bw":
				$this->generate_bw($company, $from, $to);
			break;
			case "ba":
				$this->generate_ba($company, $from, $to);
			break;
			case "prg":
				$this->generate_progress($company, $from, $to);
			break;
			case "rlhm":
				$this->generate_rlhm($company, $from, $to);
			break;
			case "rekaplhm":
				$this->generate_rekaplhm($company, $from, $to);
			break;
			case "emp":
				$this->generate_emp($company, $from, $to);
			break;
			case "mtrl":
				$this->generate_mtrl($company, $from, $to);
			break;
			case "prj":
				$this->generate_prj($company);
			break;
			case "invktr":
				$this->export_adem_invoice_kontraktor($company, $from, $to);
			break;	
			case "blok":
				$this->export_blok($company);
			break;
			case "ma":
				$this->export_ma($company);
			break;
			case "ws":
				$this->export_ws($company);
			break;
			case "vh":
				$this->export_vh($company);
			break;	
			case "if":
				$this->export_if($company);
			break;
			case "ns":
				$this->export_ns($company);
			break;	
			case "sa":
				$this->export_sa($company);
			break;	
			case "ba_history":
				$this->generate_ba_history($company, $from, $to);
			break;
			case "empgang":
				$this->generate_empgang($company, $from, $to);
			break;
			case "det_invoic":
				$this->generate_invdetail($company, $to);
			break;
			case "rekumum":
				$this->generate_rekumum($company, $from);
			break;
			case "prgblok":
				$this->generate_progress_bloktanah($company, $from, $to);
			break;

		}
	
	}
	
	function generate_lhm($company, $from, $to){
		$periode = substr($to,0,6);
		$cek_closing=$this->global_func->cekClosing($periode, $company);
		$cek = $this->global_func->cekExistData($periode, 'm_gang_activity_detail', 'LHM_DATE', $company);
		//echo $cek;
		//if ($cek_closing > 0)//udah closing
		//{
		//	$query = $this->db->query("CALL sp_exp_hist_data_lhm('".$company."','".$from."','".$to."')");
		//} else {
			

			if($cek > 0){
				$query = $this->db->query("CALL sp_exp_data_lhm('".$company."','".$from."','".$to."')");
			} else {
				$query = $this->db->query("CALL sp_exp_hist_data_lhm('".$company."','".$from."','".$to."')");
			}
		//}
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'LHM_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	function generate_bm($company, $from, $to){
		$tabel = "";
		$periode = substr($to,0,6);
		$cek = $this->global_func->cekExistData($periode, 'p_machine_meter', 'TGL_AKTIVITAS', $company);
		if($cek > 0){
			$tabel = "p_machine_meter";
		} else {
			$tabel = "p_machine_meter";
		}
			
		$query = $this->db->query("CALL sp_exp_data_buku_mesin('".$company."','".$tabel."','".$from."','".$to."')");
		
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'BM_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);	
	}	
	
	function generate_bw($company, $from, $to){
		$query = $this->db->query("CALL sp_exp_data_buku_ws('".$company."','".$from."','".$to."')");
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'BW_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	/* tambah untuk eksport buku kontraktor #20110719  */
	function generate_bkt($company, $from, $to){
		
		$query = $this->db->query("CALL sp_exp_data_buku_kontraktor('".$company."','".$from."','".$to."')");
		
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'BKONTRAKTOR_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function generate_bk($company, $from, $to){
		$query = $this->db->query("CALL sp_exp_data_buku_alat('".$company."','".$from."','".$to."')");
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'BK_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	function generate_rlhm($company, $from, $to){
		
	$periode = substr($to,0,6);
	$cek_closing=$this->global_func->cekClosing($periode, $company);
		if ($cek_closing > 0){
			$query = $this->db->query("CALL sp_exp_data_detail_lhm_hist('".$company."','".$from."','".$to."','".$periode."')");
		} else {
			$query = $this->db->query("CALL sp_exp_data_detail_lhm('".$company."','".$from."','".$to."','".$periode."')");
		} 
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'DETAIL_UPAHLHM_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
function generate_rekaplhm($company, $from, $to){
	$periode = substr($to,0,6);
	$cek_closing=$this->global_func->cekClosing($periode, $company);
	if ($cek_closing > 0)//udah closing
    {
		$query = $this->db->query("SELECT DATE_FORMAT(du.ACTIVITYDATE,'%M %Y') AS PERIODE, 
			du.LOCATION_TYPE_CODE,
			du.LOCATION_CODE,
			'' AS DESCRIPTION,
			du.ACTIVITY_CODE,
			m_coa.COA_DESCRIPTION AS COA_DESCRIPTION,
			SUM(COALESCE(du.HKE_JUMLAH,0)) HK,
			 SUM(COALESCE(du.HKE_BYR,0)) +  SUM(COALESCE(du.LEMBUR_RUPIAH,0)) 
			 + SUM(COALESCE(du.PREMI,0)) - SUM(COALESCE(du.PENALTI,0)) AS BIAYA
		  FROM rpt_du_detail du
		  LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = du.ACTIVITY_CODE
		   WHERE COMPANY_CODE = '".$company."' and DATE_FORMAT(du.ACTIVITYDATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
		  GROUP BY du.LOCATION_CODE, du.ACTIVITY_CODE ");
	} else {
		$query = $this->db->query("SELECT DATE_FORMAT(c.LHM_DATE,'%M %Y') AS PERIODE, 
			c.LOCATION_TYPE_CODE,
			c.LOCATION_CODE,
			m_location.DESCRIPTION,
			c.ACTIVITY_CODE,
			m_coa.COA_DESCRIPTION,
			COALESCE(SUM(c.HKE_JUMLAH),0) HK,
			COALESCE( SUM(c.HKE_BYR) + ( SUM(c.LEMBUR_RUPIAH) + SUM(c.PREMI) - SUM(c.PENALTI) ),0) AS BIAYA
		  FROM ( SELECT lhm.GANG_CODE,lhm.LHM_DATE, lhm.EMPLOYEE_CODE, emp.NAMA, emp.DIVISION_CODE, emp.FAMILY_STATUS,
		emp.TYPE_KARYAWAN,  lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
		lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
		lhm.HK_JUMLAH AS HKE_JUMLAH,
		CASE 
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
		 END AS HKNE_JUMLAH,
		emp.GP,
		CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') IN ('BHL','KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  ROUND( (emp.GP/cnt_hk.HK) ) * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ cnt_hk.HK) )  * 1
		WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ 25))  * 1
		 END AS HKE_BYR,
		
		CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
		 END AS HKNE_BYR,
		lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
	FROM m_gang_activity_detail lhm
	LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
	LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".$periode."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
	LEFT JOIN ( 
			SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
			LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
			WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
			AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
			GROUP BY lhm2.EMPLOYEE_CODE
			ORDER BY lhm2.EMPLOYEE_CODE
		) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
	
	WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
	AND lhm.TYPE_ABSENSI <> '' ) c 
	LEFT JOIN ( SELECT LOCATION_CODE, DESCRIPTION FROM m_location 
						WHERE COMPANY_CODE = '".$company."' ) m_location ON m_location.LOCATION_CODE = c.LOCATION_CODE 
	LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = c.ACTIVITY_CODE
	GROUP BY c.LOCATION_CODE, c.ACTIVITY_CODE");
		
	}
	
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'REKAP_UPAHLHM_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function generate_emp($company, $from, $to)
	{
		/* REMARKED BY ASEP, 20131107
		$sQuery = "SELECT emp.NIK,emp.NAMA,emp.TYPE_KARYAWAN AS 'TYPE KARYAWAN',
	emp.FAMILY_STATUS AS 'STATUS KELUARGA',
	emp.DATE_JOIN AS 'TANGGAL MASUK / PENGANGKATAN',
	emp.TANGGAL_LAHIR AS 'TANGGAL LAHIR',
	emp.GP,
	REPLACE(`value`,'\\n', char(10)) emp.ALAMAT AS ALAMAT,
	ed.EDUCATION_NAME AS 'PENDIDIKAN TERAKHIR',
	emp.RELIGION AS 'AGAMA',
	emp.NO_IDENTITAS AS 'NO IDENTITAS',
	emp.NO_JAMSOSTEK AS 'NO JAMSOSTEK',
	emp.NO_NPWP AS NPWP,
	emp.SEX AS JENIS_KELAMIN,	
	lvl.EMP_LEVEL_DESC AS 'PANGKAT', 
	pos.POSITION_DESCRIPTION AS 'JABATAN',
	dept.DEPT_DESCRIPTION AS 'DEPARTEMEN',
	emp.DIVISION_CODE AS 'DIVISI',
	emp.ESTATE_CODE AS 'AFDELING',
	empgang.GANG_CODE AS 'KEMANDORAN',
	CASE WHEN emp.INACTIVE = 1 THEN 'INAKTIF' ELSE 'AKTIF' END AS 'STATUS',
	emp.INACTIVE_DATE AS 'TANGGAL STATUS',
	emp.INACTIVE_BY AS 'PERUBAHAN STATUS OLEH',
	NOTE AS CATATAN	FROM m_employee emp
					LEFT JOIN (
						SELECT m_empgang.EMPLOYEE_CODE AS NIK, m_empgang.COMPANY_CODE, m_empgang.GANG_CODE 
						FROM m_empgang 
						WHERE m_empgang.COMPANY_CODE ='".$company."'
						GROUP BY m_empgang.EMPLOYEE_CODE,m_empgang.COMPANY_CODE,m_empgang.GANG_CODE
					) empgang
					ON emp.NIK = empgang.NIK AND emp.COMPANY_CODE=empgang.COMPANY_CODE
					LEFT JOIN m_employee_dept dept ON dept.DEPT_CODE = emp.DEPT_CODE
					LEFT JOIN m_employee_level lvl ON lvl.EMP_LEVEL_ID = emp.PANGKAT
					LEFT JOIN m_employee_position pos ON pos.EMP_POSITION_ID = emp.JABATAN
					LEFT JOIN m_employee_education ed ON ed.ED_ID = emp.LAST_EDUCATION
					WHERE emp.COMPANY_CODE='".$company."' 
					GROUP BY emp.NIK";
		*/
		$sQuery = "SELECT * FROM (
	SELECT emp.NIK,emp.NAMA,emp.TYPE_KARYAWAN AS 'TYPE KARYAWAN',
	emp.FAMILY_STATUS AS 'STATUS KELUARGA',
emp.TAX_STATUS AS 'STATUS PAJAK',

	emp.DATE_JOIN AS 'TANGGAL MASUK / PENGANGKATAN',
	emp.TANGGAL_LAHIR AS 'TANGGAL LAHIR',
	emp.GP,
	REPLACE(REPLACE(REPLACE(emp.ALAMAT, '\t', ''), '\r', ''), '\n', '') AS ALAMAT,
	ed.EDUCATION_NAME AS 'PENDIDIKAN TERAKHIR',
	emp.RELIGION AS 'AGAMA',
	emp.NO_IDENTITAS AS 'NO IDENTITAS',
	emp.NO_JAMSOSTEK AS 'NO JAMSOSTEK',
	emp.NO_NPWP AS NPWP,
	emp.SEX AS JENIS_KELAMIN,	
	lvl.EMP_LEVEL_DESC AS 'PANGKAT', 
	pos.POSITION_DESCRIPTION AS 'JABATAN',
	dept.DEPT_DESCRIPTION AS 'DEPARTEMEN',
	emp.DIVISION_CODE AS 'DIVISI',
	emp.ESTATE_CODE AS 'AFDELING',
	emp.COST_CENTER AS 'COST_CENTER',

	empgang.GANG_CODE AS 'KEMANDORAN',
	CASE WHEN emp.INACTIVE = 1 THEN 'INAKTIF' ELSE 'AKTIF' END AS 'STATUS',
	emp.INACTIVE_DATE AS 'TANGGAL STATUS',
	emp.INACTIVE_BY AS 'PERUBAHAN STATUS OLEH',
	NOTE AS CATATAN	FROM m_employee emp
					LEFT JOIN (
						SELECT m_empgang.EMPLOYEE_CODE AS NIK, m_empgang.COMPANY_CODE, m_empgang.GANG_CODE 
						FROM m_empgang 
						WHERE m_empgang.COMPANY_CODE ='".$company."'
						GROUP BY m_empgang.EMPLOYEE_CODE,m_empgang.COMPANY_CODE,m_empgang.GANG_CODE
					) empgang
					ON emp.NIK = empgang.NIK AND emp.COMPANY_CODE=empgang.COMPANY_CODE
					LEFT JOIN m_employee_dept dept ON dept.DEPT_CODE = emp.DEPT_CODE
					LEFT JOIN m_employee_level lvl ON lvl.EMP_LEVEL_ID = emp.PANGKAT
					LEFT JOIN m_employee_position pos ON pos.EMP_POSITION_ID = emp.JABATAN
					LEFT JOIN m_employee_education ed ON ed.ED_ID = emp.LAST_EDUCATION
					WHERE emp.COMPANY_CODE='".$company."' 
					ORDER BY emp.NIK, emp.INACTIVE	
	) employee
	GROUP BY employee.NIK";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'KARYAWAN_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function generate_progress($company, $from, $to)
	{
	   	$tabel = "";
	   	$periode = substr($to,0,6);
		$cek = $this->global_func->cekExistData($periode, 'p_progress', 'TGL_PROGRESS', $company);
		if($cek > 0){
			$tabel = "p_progress";
		} else {
			$tabel = "hist_p_progress";
		}

		$sQuery = "SELECT COALESCE(prg.GANG_CODE,'\t') AS 'Kemandoran',
				COALESCE(prg.TGL_PROGRESS,'\t') AS 'Tgl Progres',
				COALESCE(prg.LOCATION_CODE,'\t') AS 'Kode Lokasi',
						CASE WHEN LEFT(prg.LOCATION_CODE,2) = 'PJ' 
						THEN 
							COALESCE(prj.PROJECT_DESC,'\t')		
						ELSE 
							COALESCE(loc.DESCRIPTION,'\t')
						END  AS 'Keterangan Lokasi',	
						COALESCE(prg.ACTIVITY_CODE,'\t') AS 'Kode Aktivitas', 
						COALESCE(c.COA_DESCRIPTION,'\t') AS 'Keterangan Aktivitas', 
						COALESCE(prg.SATUAN,'\t') AS Satuan,
						COALESCE(prg.HASIL_KERJA,'\t') AS 'Hasil Kerja',
						COALESCE(prg.SATUAN2,'\t') AS 'Satuan 2',
						COALESCE(prg.HASIL_KERJA2,'\t') AS 'Hasil Kerja 2',
						COALESCE(prg.REALISASI,'\t') AS Reaslisasi,
						COALESCE(prg.HK,'\t') AS HK,
						COALESCE(prg.INPUT_BY,'\t') AS 'Input oleh',
						COALESCE(prg.INPUT_DATE,'\t') AS 'Tgl Input',
						COALESCE(prg.COMPANY_CODE,'\t') AS 'Perusahaan' FROM " . $tabel . " prg
					LEFT JOIN m_coa c ON c.ACCOUNTCODE = prg.ACTIVITY_CODE
					LEFT JOIN ( SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE COMPANY_CODE = '".$company."' GROUP BY LOCATION_CODE ) loc ON loc.LOCATION_CODE = prg.LOCATION_CODE
					LEFT JOIN ( SELECT PROJECT_ID, PROJECT_DESC FROM m_project WHERE COMPANY_CODE = '".$company."' ) prj ON prj.PROJECT_ID = prg.LOCATION_CODE
					WHERE prg.COMPANY_CODE = '".$company."'
					AND DATE_FORMAT(prg.TGL_PROGRESS,'%Y%m%d') BETWEEN '".$from."' AND '".$to."'
					ORDER BY TGL_PROGRESS, GANG_CODE ASC";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'PROGRESS_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function generate_progress_bloktanah($company, $from, $to){
	   	$tabel = "";
	   	$periode = substr($to,0,6);
		
		$sQuery =  "SELECT CASE WHEN (prg.GANG_CODE) = '' THEN '\t' ELSE prg.GANG_CODE END AS 'Kemandoran',
					COALESCE(prg.TGL_PROGRESS,'\t') AS 'Tgl Progres',
					COALESCE(prg.LOCATION_CODE,'\t') AS 'Kode Lokasi',
					CASE WHEN LEFT(prg.LOCATION_CODE,2) = 'PJ' THEN COALESCE(prj.PROJECT_DESC,'\t') ELSE COALESCE(loc.DESCRIPTION,'\t') END  AS 'Keterangan Lokasi', 
					COALESCE(prg.ACTIVITY_CODE,'\t') AS 'Kode Aktivitas',
					COALESCE(c.COA_DESCRIPTION,'\t') AS 'Keterangan Aktivitas',
					COALESCE(prg.SATUAN,'\t') AS Satuan,
					COALESCE(prg.TONASE_BLOK_TANAH,'\t') AS 'Hasil Kerja',
					COALESCE(prg.SATUAN2,'\t') AS 'Satuan 2',
					COALESCE(prg.JJG_BLOK_TANAH,'\t') AS 'Hasil Kerja 2',
					COALESCE(prg.RP_BLOK_TANAH,'\t') AS Reaslisasi,
					COALESCE(prg.HK_BLOK_TANAH,'\t') AS HK,
					COALESCE(prg.INPUT_BY,'\t') AS 'Input oleh',
					COALESCE(prg.INPUT_DATE,'\t') AS 'Tgl Input',
					COALESCE(prg.COMPANY_CODE,'\t') AS 'Perusahaan' 
					FROM p_progress_panen prg
					LEFT JOIN m_coa c ON c.ACCOUNTCODE = prg.ACTIVITY_CODE
					LEFT JOIN ( SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE COMPANY_CODE = '".$company."' GROUP BY LOCATION_CODE ) loc ON loc.LOCATION_CODE = prg.LOCATION_CODE
					LEFT JOIN ( SELECT PROJECT_ID, PROJECT_DESC FROM m_project WHERE COMPANY_CODE = '".$company."') prj ON prj.PROJECT_ID = prg.LOCATION_CODE
					WHERE prg.COMPANY_CODE = '".$company."'
					AND DATE_FORMAT(prg.TGL_PROGRESS,'%Y%m%d') BETWEEN '".$from."' AND '".$to."'
					ORDER BY TGL_PROGRESS, prg.LOCATION_CODE DESC";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'PROGRESS_PANEN_BLOK_TANAH' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function generate_ba($company, $from, $to)
	{
		$periode = substr($from, 0, 6);
		$cek_closing=$this->global_func->cekClosing($periode, $company);
		if ($cek_closing > 0) 	//udah closing
    		{
			$sQuery = "CALL sp_select_ba_all_closing('".$company."','".$periode."')";
		} else {
			$sQuery = "CALL sp_select_ba_all('".$company."','".$periode."')";
		}
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name = 'BA_ALL_' . $company . "_" .  mdate($datestring);
		
		to_excel($query, $name); 	
	}

	function generate_ba_history($company, $from, $to)
	{
		$from = substr($from, 0, 6);
		$to = substr($to, 0, 6);
		$periode = $from;


		$cek_closing=$this->global_func->cekClosing($periode, $company);
		if ($cek_closing > 0)//udah closing
    		{
			$sQuery = "CALL sp_select_ba_history_block('".$company."','".$from."','".$to."')";
		} else {
			$sQuery = "CALL sp_select_ba_history_block('".$company."','".$from."','".$to."')";
		}
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m%d_%h%i%a";
		$time = time();
		$name =  $company . "_BA_ALL_MAPBLOK_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}

	
	function generate_prj($company){
		$sQuery = "SELECT * FROM m_project WHERE COMPANY_CODE = '".$company."' ORDER BY PROJECT_ID";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'PROJECT_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function export_blok($company){
		$sQuery = "SELECT FIELDCODE, BLOCKID, ESTATECODE, DESCRIPTION, HECTPLANTABLE, HECTPLANTED, CROPSSTATUS, NUMPLANTATION, 
				YEARREPLANT, INACTIVE, COMPANY_CODE
				FROM m_fieldcrop WHERE COMPANY_CODE = '".$company."' ORDER BY FIELDCODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'BLOK_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function export_ma($company){
		$sQuery = "SELECT MACHINECODE, DESCRIPTION, OWNERSHIP, SATUAN_PRESTASI, CASE WHEN INACTIVE = 0 THEN 
					'AKTIF' WHEN INACTIVE = 1 THEN 'INAKTIF' END AS STATUS_AKTIF, 
					COMPANY_CODE FROM m_machine WHERE COMPANY_CODE = '".$company."' ORDER BY MACHINECODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'MACHINE_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function export_ws($company){
		$sQuery = "SELECT WORKSHOPCODE, DESCRIPTION, CASE WHEN INACTIVE = 0 THEN 'AKTIF' WHEN INACTIVE = 1 	
			THEN 'INAKTIF' END AS STATUS_AKTIF, COMPANY_CODE FROM m_workshop WHERE 
			COMPANY_CODE = '".$company."' 
			ORDER BY WORKSHOPCODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'WORKSHOP_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	function export_vh($company){
		$sQuery = "SELECT VEHICLECODE, REGISTRATIONNO, DESCRIPTION, MAKE AS MERK, MODEL AS MODEL, 
					YEAR AS TAHUN, OWNERSHIP, ENGINENO, SATUAN_PRESTASI,
					CASE WHEN INACTIVE = 0 THEN 'AKTIF' 
					WHEN INACTIVE = 1 THEN 'INAKTIF' END AS STATUS_AKTIF, 
					COMPANY_CODE FROM m_vehicle  WHERE 
					COMPANY_CODE = '".$company."' 
					ORDER BY VEHICLECODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'VEHICLE_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	function export_if($company){
		$sQuery = "SELECT * FROM m_location WHERE LOCATION_TYPE_CODE = 'IF'
					AND COMPANY_CODE = '".$company."' 
					ORDER BY LOCATION_CODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'INFRASTRUCTURE_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}


	function export_ns($company){
		$sQuery = "SELECT NURSERYCODE, DESCRIPTION, VARIETAS, CASE WHEN INACTIVE = 0 THEN 'AKTIF' 
					WHEN INACTIVE = 1 THEN 'INAKTIF' END AS STATUS_AKTIF, 
					COMPANY_CODE FROM m_nursery WHERE 
					COMPANY_CODE = '".$company."' 
					ORDER BY NURSERYCODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'NURSERY_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	function export_sa($company){
		$sQuery = "SELECT STATION_CODE, STATION_NAME,COMPANY_CODE FROM m_station WHERE 
					COMPANY_CODE = '".$company."' 
					ORDER BY STATION_CODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'STATION_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	function generate_mtrl($company, $from, $to){
		$sQuery = "SELECT GANG_CODE, 'LHM' AS FLAG, LHM_DATE, '' AS KMHM_PENGAMBILAN ,  MATERIAL_BPB_NO, gm.MATERIAL_CODE, mat.MATERIAL_NAME, MATERIAL_QTY,
 mat.MATERIAL_UOM, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, COMPANY_CODE FROM 
m_gang_activity_detail_material gm
LEFT JOIN m_material mat ON mat.MATERIAL_CODE = gm.MATERIAL_CODE
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = gm.ACTIVITY_CODE
WHERE COMPANY_CODE = '".$company."' AND LHM_DATE BETWEEN '".$from."' AND '".$to."'

UNION 

SELECT KODE_MESIN, 'BUKU MESIN' AS FLAG, TGL_AKTIVITAS, KMHM_PENGAMBILAN, MATERIAL_BPB_NO, gm.MATERIAL_CODE, mat.MATERIAL_NAME, MATERIAL_QTY,
 mat.MATERIAL_UOM, '' AS ACTIVITY_CODE, '' AS COA_DESCRIPTION, '' AS LOCATION_CODE, COMPANY_CODE FROM 
p_machine_meter_material gm
LEFT JOIN m_material mat ON mat.MATERIAL_CODE = gm.MATERIAL_CODE
WHERE COMPANY_CODE = '".$company."' AND TGL_AKTIVITAS BETWEEN '".$from."' AND '".$to."'

UNION 

SELECT KODE_KENDARAAN, 'BUKU KENDARAAN' AS FLAG, TGL_AKTIVITAS, KMHM_PENGAMBILAN, MATERIAL_BPB_NO, gm.MATERIAL_CODE, mat.MATERIAL_NAME, MATERIAL_QTY,
 mat.MATERIAL_UOM, '' AS ACTIVITY_CODE, '' AS COA_DESCRIPTION, '' AS LOCATION_CODE, COMPANY_CODE FROM 
p_vehicle_activity_material gm
LEFT JOIN m_material mat ON mat.MATERIAL_CODE = gm.MATERIAL_CODE
WHERE COMPANY_CODE = '".$company."' AND TGL_AKTIVITAS BETWEEN '".$from."' AND '".$to."'

UNION 

SELECT KODE_WORKSHOP, 'BUKU WORKSHOP' AS FLAG, TGL_AKTIVITAS, '' AS KMHM_PENGAMBILAN, MATERIAL_BPB_NO, gm.MATERIAL_CODE, mat.MATERIAL_NAME, MATERIAL_QTY,
 mat.MATERIAL_UOM, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, COMPANY_CODE FROM 
p_workshop_activity_material gm
LEFT JOIN m_material mat ON mat.MATERIAL_CODE = gm.MATERIAL_CODE
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = gm.ACTIVITY_CODE
WHERE COMPANY_CODE = '".$company."' AND TGL_AKTIVITAS BETWEEN '".$from."' AND '".$to."'
";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'MATERIAL_' . $company . "_" .  mdate($datestring, $time);
		
		to_excel($query, $name);
	}
	
	/* fungsi generate invoice untuk kontraktor #ridhu 2012-09-18 */
	function export_adem_invoice_kontraktor($company, $from, $to){
		$periode = substr($from,0,6);
		$sQuery = "CALL sp_adem_invoice_kontraktor('".$company."','".$from."','".$to."','KTRK')";
		$query=$this->db->query($sQuery);
		$this->load->library('table');
		$this->table->set_heading(null);
		$datestring = "%Y%m";
		$time = time();
		$name = 'INVKONTRAKTOR_' . $company . "_" .  mdate($datestring, $time);
		$this->load->helper('csv');
		query_to_csv($query, TRUE, $name.'.csv');
	}
	
	function generate_invdetail($company, $to){
		$periode = substr($to,0,6);
		$sQuery = "CALL sp_new_adem_detail_invoice('".$company."','".$periode."')";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'INVDETAIL_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	/* fungsi generate invoice untuk kontraktor #ridhu 2014-02-12 */
	function generate_empgang($company, $from, $to){
		$periode = substr($from,0,6);
		$sQuery = "SELECT empg.GANG_CODE, g.DESCRIPTION, EMPLOYEE_CODE, emp.NAMA, emp.TYPE_KARYAWAN, MONTH, YEAR, 
					empg.COMPANY_CODE FROM m_empgang empg
					LEFT JOIN ( SELECT GANG_CODE, MANDORE_CODE, MANDORE1_CODE, DESCRIPTION, COMPANY_CODE 
						FROM m_gang WHERE COMPANY_CODE = '".$company."') g ON g.GANG_CODE = empg.GANG_CODE 
						AND g.COMPANY_CODE = empg.COMPANY_CODE
					LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN, COMPANY_CODE 
						FROM m_employee WHERE COMPANY_CODE = '".$company."' GROUP BY NIK
						) emp ON emp.NIK = empg.EMPLOYEE_CODE AND emp.COMPANY_CODE = empg.COMPANY_CODE
					WHERE empg.COMPANY_CODE = '".$company."' AND CONCAT(empg.YEAR, empg.MONTH) LIKE '". substr($from,0,6) ."%' AND emp.NAMA IS NOT NULL
					GROUP BY EMPLOYEE_CODE";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'KEMANDORAN_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}
	
	function  generate_rekumum($company, $from){
		$from= substr($from,0,6);
		$sQuery = "SELECT 'GAJI' AS JABATAN, '' AS POSITION_DESCRIPTION, '' AS QTY, '' AS BULAN_INI, '' AS SAMPAI_BULAN_INI
UNION 
SELECT bi.JABATAN, bi.POSITION_DESCRIPTION, SUM(COALESCE(HKE_JUMLAH,0)) AS QTY, SUM(COALESCE(HKE_BYR,0)) AS BULAN_INI, sbi.SAMPAI_BULAN_INI FROM (
SELECT PERIODE, du.COMPANY_CODE, EMPLOYEE_CODE, du.NAMA, emp.JABATAN, emp.POSITION_DESCRIPTION, ACTIVITY_CODE, du.GP, 
	COALESCE(HKE_JUMLAH,0) AS HKE_JUMLAH, COALESCE(HKE_BYR,0) AS HKE_BYR, HKNE_JUMLAH, HKNE_BYR, 
	LEMBUR_RUPIAH, PREMI, PENALTI, NATURA, ASTEK, RAPEL, THR, POTONGAN_LAIN, TUNJANGAN_JABATAN, 
	TUNJANGAN_LAIN, POTONGAN_ZAKAT, SUBSIDI_KENDARAAN, TUNJANGAN_TRANSPORT, POT_ASTEK, POT_PPH21 
FROM rpt_du_detail du
LEFT JOIN ( 

SELECT NIK, NAMA, GP, PANGKAT, lv.EMP_LEVEL_DESC, JABATAN, pos.POSITION_DESCRIPTION, COST_CENTER FROM m_employee emp
LEFT JOIN m_employee_level lv ON lv.EMP_LEVEL_ID = emp.PANGKAT
LEFT JOIN m_employee_position pos ON pos.EMP_POSITION_ID = emp.JABATAN AND pos.LEVEL_CODE = lv.EMP_LEVEL_ID
WHERE COMPANY_CODE = '".$company."' AND NIK NOT IN (
SELECT NIK FROM m_employee emp WHERE DATE_FORMAT(INACTIVE_DATE ,'%Y%m') < '".$from."' AND COMPANY_CODE = '".$company."'
) GROUP BY NIK
) emp ON emp.NIK = EMPLOYEE_CODE

WHERE PERIODE = '".$from."' AND COMPANY_CODE = '".$company."' AND emp.JABATAN IN ( '10024','10025','10026','10108','10109','10110','10116','10117'
,'10118','10119','10120','10121','10125','10123','10127','10133','10134','10135','10136','10110','10108','10144','10146','10151','10154'
,'10155','10124','10129','10126','10128','10125','10122','10138','10141','10152','10156','10145','10142','10153','10157' )
AND ACTIVITY_CODE LIKE '62%'
) bi -- untuk bulan ini ---
LEFT JOIN ( SELECT JABATAN, c.POSITION_DESCRIPTION, SUM(COALESCE(HKE_JUMLAH,0)) AS QTY, SUM(COALESCE(HKE_BYR,0)) AS SAMPAI_BULAN_INI FROM ( SELECT PERIODE, du.COMPANY_CODE, EMPLOYEE_CODE, du.NAMA, emp.JABATAN, emp.POSITION_DESCRIPTION, ACTIVITY_CODE, du.GP, 
	COALESCE(HKE_JUMLAH,0) AS HKE_JUMLAH, COALESCE(HKE_BYR,0) AS HKE_BYR, HKNE_JUMLAH, HKNE_BYR, 
	LEMBUR_RUPIAH, PREMI, PENALTI, NATURA, ASTEK, RAPEL, THR, POTONGAN_LAIN, TUNJANGAN_JABATAN, 
	TUNJANGAN_LAIN, POTONGAN_ZAKAT, SUBSIDI_KENDARAAN, TUNJANGAN_TRANSPORT, POT_ASTEK, POT_PPH21 
FROM rpt_du_detail du
LEFT JOIN ( 

SELECT NIK, NAMA, GP, PANGKAT, lv.EMP_LEVEL_DESC, JABATAN, pos.POSITION_DESCRIPTION, COST_CENTER FROM m_employee emp
LEFT JOIN m_employee_level lv ON lv.EMP_LEVEL_ID = emp.PANGKAT
LEFT JOIN m_employee_position pos ON pos.EMP_POSITION_ID = emp.JABATAN AND pos.LEVEL_CODE = lv.EMP_LEVEL_ID
WHERE COMPANY_CODE = '".$company."' AND NIK NOT IN (
SELECT NIK FROM m_employee emp WHERE DATE_FORMAT(INACTIVE_DATE ,'%Y%m') < '".$from."' AND COMPANY_CODE = '".$company."'
) GROUP BY NIK
) emp ON emp.NIK = EMPLOYEE_CODE

WHERE PERIODE BETWEEN CONCAT(SUBSTRING('".$from."',1,4),'01') AND '".$from."' AND COMPANY_CODE = '".$company."' AND emp.JABATAN IN ( '10024','10025','10026','10108','10109','10110','10116','10117'
,'10118','10119','10120','10121','10125','10123','10127','10133','10134','10135','10136','10110','10108','10144','10146','10151','10154'
,'10155','10124','10129','10126','10128','10125','10122','10138','10141','10152','10156','10145','10142','10153','10157' )
AND ACTIVITY_CODE LIKE '62%'
) c
GROUP BY JABATAN, POSITION_DESCRIPTION ) sbi ON sbi.JABATAN = bi.JABATAN
-- end bulan ini --
GROUP BY POSITION_DESCRIPTION


UNION 

SELECT 'PREMI / LEMBUR' AS JABATAN, '' AS POSITION_DESCRIPTION, '' AS QTY, '' AS BULAN_INI, '' AS SAMPAI_BULAN_INI

UNION
SELECT bi.JABATAN, bi.POSITION_DESCRIPTION, COUNT(PREMIALL) AS QTY, SUM(COALESCE(PREMIALL,0)) AS BULAN_INI, sbi.SAMPAI_BULAN_INI FROM (
SELECT PERIODE, du.COMPANY_CODE, EMPLOYEE_CODE, du.NAMA, emp.JABATAN, emp.POSITION_DESCRIPTION, ACTIVITY_CODE, du.GP, 
	COALESCE(HKE_JUMLAH,0) AS HKE_JUMLAH, COALESCE(HKE_BYR,0) AS HKE_BYR, HKNE_JUMLAH, HKNE_BYR, 
	COALESCE(LEMBUR_RUPIAH,0) + COALESCE(PREMI,0) - COALESCE(PENALTI,0) AS PREMIALL,
	COALESCE(LEMBUR_RUPIAH,0) AS LEMBUR, COALESCE(PREMI,0) AS PREMI, COALESCE(PENALTI,0) AS PENALTI, NATURA, ASTEK, RAPEL, THR, POTONGAN_LAIN, TUNJANGAN_JABATAN, 
	TUNJANGAN_LAIN, POTONGAN_ZAKAT, SUBSIDI_KENDARAAN, TUNJANGAN_TRANSPORT, POT_ASTEK, POT_PPH21 
FROM rpt_du_detail du
LEFT JOIN ( 

SELECT NIK, NAMA, GP, PANGKAT, lv.EMP_LEVEL_DESC, JABATAN, pos.POSITION_DESCRIPTION, COST_CENTER FROM m_employee emp
LEFT JOIN m_employee_level lv ON lv.EMP_LEVEL_ID = emp.PANGKAT
LEFT JOIN m_employee_position pos ON pos.EMP_POSITION_ID = emp.JABATAN AND pos.LEVEL_CODE = lv.EMP_LEVEL_ID
WHERE COMPANY_CODE = '".$company."' AND NIK NOT IN (
SELECT NIK FROM m_employee emp WHERE DATE_FORMAT(INACTIVE_DATE ,'%Y%m') < '".$from."' AND COMPANY_CODE = '".$company."'
) GROUP BY NIK
) emp ON emp.NIK = EMPLOYEE_CODE

WHERE PERIODE = '".$from."' AND COMPANY_CODE = '".$company."' AND emp.JABATAN IN ( '10024','10025','10026','10108','10109','10110','10116','10117'
,'10118','10119','10120','10121','10125','10123','10127','10133','10134','10135','10136','10110','10108','10144','10146','10151','10154'
,'10155','10124','10129','10126','10128','10125','10122','10138','10141','10152','10156','10145','10142','10153','10157' )
AND ACTIVITY_CODE LIKE '62%'
) bi -- untuk bulan ini ---
LEFT JOIN ( SELECT JABATAN, c.POSITION_DESCRIPTION, COUNT(PREMIALL) AS QTY, SUM(COALESCE(PREMIALL,0)) AS SAMPAI_BULAN_INI FROM ( SELECT PERIODE, du.COMPANY_CODE, EMPLOYEE_CODE, du.NAMA, emp.JABATAN, emp.POSITION_DESCRIPTION, ACTIVITY_CODE, du.GP, 
	COALESCE(HKE_JUMLAH,0) AS HKE_JUMLAH, COALESCE(HKE_BYR,0) AS HKE_BYR, HKNE_JUMLAH, HKNE_BYR, 
	COALESCE(LEMBUR_RUPIAH,0) + COALESCE(PREMI,0) - COALESCE(PENALTI,0) AS PREMIALL,
	COALESCE(LEMBUR_RUPIAH,0) AS LEMBUR, COALESCE(PREMI,0) AS PREMI, COALESCE(PENALTI,0) AS PENALTI, NATURA, ASTEK, RAPEL, THR, POTONGAN_LAIN, TUNJANGAN_JABATAN, 
	TUNJANGAN_LAIN, POTONGAN_ZAKAT, SUBSIDI_KENDARAAN, TUNJANGAN_TRANSPORT, POT_ASTEK, POT_PPH21 
FROM rpt_du_detail du
LEFT JOIN ( 

SELECT NIK, NAMA, GP, PANGKAT, lv.EMP_LEVEL_DESC, JABATAN, pos.POSITION_DESCRIPTION, COST_CENTER FROM m_employee emp
LEFT JOIN m_employee_level lv ON lv.EMP_LEVEL_ID = emp.PANGKAT
LEFT JOIN m_employee_position pos ON pos.EMP_POSITION_ID = emp.JABATAN AND pos.LEVEL_CODE = lv.EMP_LEVEL_ID
WHERE COMPANY_CODE = '".$company."' AND NIK NOT IN (
SELECT NIK FROM m_employee emp WHERE DATE_FORMAT(INACTIVE_DATE ,'%Y%m') < '".$from."' AND COMPANY_CODE = '".$company."'
) GROUP BY NIK
) emp ON emp.NIK = EMPLOYEE_CODE

WHERE PERIODE BETWEEN CONCAT(SUBSTRING('".$from."',1,4),'01') AND '".$from."' AND COMPANY_CODE = '".$company."' AND emp.JABATAN IN ( '10024','10025','10026','10108','10109','10110','10116','10117'
,'10118','10119','10120','10121','10125','10123','10127','10133','10134','10135','10136','10110','10108','10144','10146','10151','10154'
,'10155','10124','10129','10126','10128','10125','10122','10138','10141','10152','10156','10145','10142','10153','10157' )
AND ACTIVITY_CODE LIKE '62%'
) c
GROUP BY JABATAN, POSITION_DESCRIPTION ) sbi ON sbi.JABATAN = bi.JABATAN
-- end bulan ini --
GROUP BY POSITION_DESCRIPTION
";
		$query=$this->db->query($sQuery);
		$datestring = "%Y%m";
		$time = time();
		$name = 'KEMANDORAN_' . $company . "_" .  mdate($datestring, $time);
		to_excel($query, $name);
	}

	/* 
		$periode = substr($from,0,6);
		$sQuery = "CALL sp_adem_invoice_kontraktor('".$company."','".$periode."')";
		$query=$this->db->query($sQuery);
		$this->load->library('table');
		$this->table->set_heading(null);
		$datestring = "%Y%m";
		$time = time();
		$name = 'INVKONTRAKTOR_' . $company . "_" .  mdate($datestring, $time);
		$this->load->helper('csv');
		query_to_csv($query, TRUE, '$name.csv'); */ 
		
	function export_access_user($company,$user)
    {
        $query = $this->db->query("SELECT LOGINID FROM m_user_exportdata_access WHERE LOGINID = '".$user."' AND COMPANY_CODE = '".$company."'");
        $count=$query->num_rows();
        return $count;
    }
	
	function dropdownlist_exportdata($company, $user)
	{ 
		$cekdetail = $this->export_access_user($company, $user);
		$level = $this->session->userdata('USER_LEVEL');
		
		
		$string = "<select  name='jns_rpt' class='select' id='jns_rpt'";
		$string .= "style='width:190px;' ><option value=''> -- Pilih -- </option>";
		if($cekdetail > 0){
			 $sQuery = "SELECT EXPORT_MEID, EXPORT_ACTION,EXPORT_MENU FROM m_user_exportdata_access mu
						LEFT JOIN m_user_exportdata ON EXPORT_MEID = MUEXD_ID WHERE LOGINID = '".$user."' AND mu.INACTIVE = 0
						ORDER BY EXPORT_MEID";
		} else {
			$sQuery = "	SELECT EXPORT_MEID, EXPORT_ACTION,EXPORT_MENU FROM m_user_exportdata_access_group mu
						LEFT JOIN m_user_exportdata ON EXPORT_MEID = MUEXD_ID WHERE GROLE = '".$level."' AND mu.INACTIVE = 0
						ORDER BY EXPORT_MEID";
		}              		
		
		$temp=$this->db->query($sQuery);
        $temp = $temp->result_array();
        $this->db->close();		
						
		foreach ( $temp as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['EXPORT_ACTION']."\" selected>".$row['EXPORT_MENU']." </option>";
			} else {
				$string = $string." <option value=\"".$row['EXPORT_ACTION']."\">".$row['EXPORT_MENU']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	/* end dropdown gangcode */
	/*
		generate_procedure Added By Asep, 20130730
	*/
	function generate_procedure($company, $periode){
		
		
		//$query2 = $this->db->query("CALL sp_new_adem_hkne('".$company."','".$periode."')");

		//$query = $this->db->query("CALL sp_new_adem_premilembur('".$company."','".$periode."')");
		//$query = $this->db->query("CALL sp_new_adem_natura('".$company."','".$periode."')");
		//$query = $this->db->query("CALL sp_new_adem_astek('".$company."','".$periode."')");
		//$query = $this->db->query("CALL sp_new_adem_rapel('".$company."','".$periode."')");
		//$query = $this->db->query("CALL sp_new_adem_potastek('".$company."','".$periode."')");
		//$query = $this->db->query("CALL sp_new_adem_pph('".$company."','".$periode."')");
		
		$judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
		
		$obj =& get_instance();
		$headers .= " DOCUMENT_NO. \t";
		$headers .= " DOCUMENT_TYPE. \t";
		$headers .= " SALESTRX. \t";
		$headers .= " DATE. \t";
		$headers .= " BUSINESS_PARTNER. \t";
		$headers .= " QUANTITY. \t";
		$headers .= " SUM(UNIT_PRICE). \t";
		$headers .= " TAX_INDICATOR. \t";
		$headers .= " TAX_AMOUNT. \t";
		$headers .= " CURRENCY_ID. \t";
		$headers .= " PAYMENT_TERM. \t";
		$headers .= " TAX_ID. \t";
		$headers .= " LOCATION_TYPE_CODE. \t";
		$headers .= " BLOK. \t";
		$headers .= " TAHUN_TANAM. \t";
		$headers .= " INFRASTRUKTUR. \t";
		$headers .= " ASSET. \t";
		$headers .= " COST_CENTER. \t";
		$headers .= " CHARGE. \t";
		$headers .= " PROJECT. \t";
		$headers .= " ACTIVITY_CODE. \t";
		$headers .= " DESCRIPTIONLINE. \t";
		$headers .= " OrgValue. \t";
		
		//$datestring = "%Y%m%d_%h%i%a";
		//$time = time();
		//$name = 'LHM_' . $company . "_" .  mdate($datestring, $time);
		//to_excel($query, $name);
		
		//$query_salary = $this->db->query("CALL sp_new_adem_gaji('".$company."','".$periode."')");
		//$query_hkne = $this->db->query("CALL sp_new_adem_hkne('".$company."','".$periode."')");	
		//$query_premi = $this->db->query("CALL sp_new_adem_premilembur('".$company."','".$periode."')");
		//$query_natura = $this->db->query("CALL sp_new_adem_natura('".$company."','".$periode."')");
		//$query_astek = $this->db->query("CALL sp_new_adem_astek('".$company."','".$periode."')");
		//$query_rapel = $this->db->query("CALL sp_new_adem_rapel('".$company."','".$periode."')");
		//$query_potastek = $this->db->query("CALL sp_new_adem_potastek('".$company."','".$periode."')");
		//$query_pph = $this->db->query("CALL sp_new_adem_pph('".$company."','".$periode."')");
		
		$query_salary =$this->model_rpt_lhm->get_salary($company,$periode);
		$query_hkne=$this->model_rpt_lhm->get_hkne($company,$periode);
		$query_premi =$this->model_rpt_lhm->get_premi($company,$periode);
		$query_natura =$this->model_rpt_lhm->get_natura($company,$periode);
		$query_astek =$this->model_rpt_lhm->get_astek($company,$periode);
		$query_rapel=$this->model_rpt_lhm->get_rapel($company,$periode);
		$query_thr=$this->model_rpt_lhm->get_thr($company,$periode);
		$query_tunjangan_lain=$this->model_rpt_lhm->get_tunjangan_lain($company,$periode);
		$query_potastek =$this->model_rpt_lhm->get_potastek($company,$periode);
		$query_pph =$this->model_rpt_lhm->get_pph($company,$periode);
		$query_zakat =$this->model_rpt_lhm->get_zakat($company,$periode);
	
		if ($query_salary<>NULL){
			foreach ($query_salary as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n"; 			
			}
		}
		
		if ($query_hkne<>NULL){
			foreach ($query_hkne as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n"; 			
			}
		}
		
		if ($query_premi<>NULL){
			foreach ($query_premi as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n"; 			
			}
		}
		
		if ($query_natura<>NULL){
			foreach ($query_natura as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n";
			}
		}
		
		if ($query_astek<>NULL){
			foreach ($query_astek as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n"; 			
			}
		}
		
		if ($query_rapel<>NULL){
			foreach ($query_rapel as $row2){
				$line = '';
				$line .= str_replace('"', '""',$row2['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row2['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row2['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row2['DATE'])."\t";
				$line .= str_replace('"', '""',$row2['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row2['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row2['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row2['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row2['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row2['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row2['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row2['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row2['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row2['BLOK'])."\t";
				$line .= str_replace('"', '""',$row2['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row2['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row2['ASSET'])."\t";
				$line .= str_replace('"', '""',$row2['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row2['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row2['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row2['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row2['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row2['OrgValue'])."\t";
				$data .= trim($line)."\n"; 			
			}
		}
		
		if ($query_thr<>NULL){
			foreach ($query_thr as $row2){
				$line = '';
				$line .= str_replace('"', '""',$row2['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row2['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row2['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row2['DATE'])."\t";
				$line .= str_replace('"', '""',$row2['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row2['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row2['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row2['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row2['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row2['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row2['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row2['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row2['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row2['BLOK'])."\t";
				$line .= str_replace('"', '""',$row2['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row2['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row2['ASSET'])."\t";
				$line .= str_replace('"', '""',$row2['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row2['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row2['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row2['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row2['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row2['OrgValue'])."\t";
				$data .= trim($line)."\n"; 			
			}
		}
		
		if ($query_tunjangan_lain<>NULL){
			foreach ($query_tunjangan_lain as $row2){
				$line = '';
				$line .= str_replace('"', '""',$row2['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row2['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row2['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row2['DATE'])."\t";
				$line .= str_replace('"', '""',$row2['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row2['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row2['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row2['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row2['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row2['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row2['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row2['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row2['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row2['BLOK'])."\t";
				$line .= str_replace('"', '""',$row2['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row2['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row2['ASSET'])."\t";
				$line .= str_replace('"', '""',$row2['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row2['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row2['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row2['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row2['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row2['OrgValue'])."\t";
				$data .= trim($line)."\n"; 			
			}
		}
		
		if ($query_potastek<>NULL){
			foreach ($query_potastek as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n"; 
			}
		}
		
		if ($query_pph<>NULL){
			foreach ($query_pph as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n"; 
			}
		}
		
		if ($query_zakat<>NULL){
			foreach ($query_zakat as $row){
				$line = '';
				$line .= str_replace('"', '""',$row['DOCUMENT_NO'])."\t";
				$line .= str_replace('"', '""',$row['DOCUMENT_TYPE'])."\t";
				$line .= str_replace('"', '""',$row['SALESTRX'])."\t";
				$line .= str_replace('"', '""',$row['DATE'])."\t";
				$line .= str_replace('"', '""',$row['BUSINESS_PARTNER'])."\t";
				$line .= str_replace('"', '""',$row['QUANTITY'])."\t";			
				$line .= str_replace('"', '""',$row['SUM(UNIT_PRICE)'])."\t";
				$line .= str_replace('"', '""',$row['TAX_INDICATOR'])."\t";
				$line .= str_replace('"', '""',$row['TAX_AMOUNT'])."\t";
				$line .= str_replace('"', '""',$row['CURRENCY_ID'])."\t";
				$line .= str_replace('"', '""',$row['PAYMENT_TERM'])."\t";			
				$line .= str_replace('"', '""',$row['TAX_ID'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
				$line .= str_replace('"', '""',$row['BLOK'])."\t";
				$line .= str_replace('"', '""',$row['TAHUN_TANAM'])."\t";
				$line .= str_replace('"', '""',$row['INFRASTRUKTUR'])."\t";			
				$line .= str_replace('"', '""',$row['ASSET'])."\t";
				$line .= str_replace('"', '""',$row['COST_CENTER'])."\t";
				$line .= str_replace('"', '""',$row['CHARGE'])."\t";
				$line .= str_replace('"', '""',$row['PROJECT'])."\t";
				$line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTIONLINE'])."\t";
				$line .= str_replace('"', '""',$row['OrgValue'])."\t";
				$data .= trim($line)."\n"; 
			}
		}

		$data = str_replace("\r","",$data);
		
		 //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=UP_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
	}
}

?>
