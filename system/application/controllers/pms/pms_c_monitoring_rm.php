<?php
class pms_c_monitoring_rm extends Controller{
    function __construct(){
        parent::__construct();
        $this->load->model('pms/pms_m_monitoring_rm');
		$this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->helper('form');
        $this->load->helper('language');
		
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->lastmenu="main_c_pms";
    }
	
	function index(){
		$view="pms/pms_v_monitoring_rm";
		$this->data['js'] = "";
		$this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$this->data['user_dept'] = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
		$this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
		$this->data['fperiode'] = $this->global_func->drop_date2('fbulan','ftahun','select');
		$role = $this->pms_m_monitoring_rm->getRoleUser($this->data['login_id']);
		
		if( $role == "1000" || $role == "1002"){
			$this->data['company'] = $this->dropdownlist("i_rm_company","style='width:260px;'","tabindex='1'","COMPANY_CODE","COMPANY_NAME","gridRMReload()");
		} else {
			$this->data['company'] = "";
		}
		
		if ($this->data['login_id'] == TRUE){
			$this->load->view($view, $this->data);
		} else {
			redirect('login');
		}
    }
	
	function read_mgrid_rm(){
		$loginid = $this->session->userdata('LOGINID');
		$role = $this->pms_m_monitoring_rm->getRoleUser($loginid);
		$periode = $this->uri->segment(4);
		if( $role == "1000" || $role == "1002"){
			$company = $this->uri->segment(5);
		} else {
			$company = $this->session->userdata('DCOMPANY');
		}
        echo json_encode($this->pms_m_monitoring_rm->loadMonitoringRM($company, $periode));
	}
	
	function read_mgrid_rm_notes(){
		$noPengajuan = $this->uri->segment(4);
        echo json_encode($this->pms_m_monitoring_rm->loadPengajuanNotes($noPengajuan));
	}
	
	function getUserRole(){
		$loginid = $this->session->userdata('LOGINID');
		echo $this->pms_m_monitoring_rm->getRoleUser($loginid);
	}
	
	function approve1(){
		$ppj = trim($this->input->post( 'RM_PENGAJUAN_ID' ) ); 
		$insert_id = $this->pms_m_monitoring_rm->approve1( $ppj );
		echo $insert_id;
	}
	
	function approve2(){
		$ppj = trim($this->input->post( 'RM_PENGAJUAN_ID' ) ); 
		$insert_id = $this->pms_m_monitoring_rm->approve2( $ppj );
		echo $insert_id;
	}
	
	function addNotes(){
		$ppj = trim($this->input->post( 'RM_PENGAJUAN_ID' ) ); 
		$data_post['RM_PENGAJUAN_ID'] = $ppj;
		$data_post['DESCRIPTION'] = trim($this->input->post( 'DESCRIPTION' ) ); 
		$data_post['LOG_TYPE'] = 'notes'; 
		$data_post['CREATED'] = $this->session->userdata('LOGINID');
		$data_post['CREATEDDATE'] = date ("Y-m-d H:i:s");
		$insert_id = $this->pms_m_monitoring_rm->insert_pengajuan_notes( $data_post );
		echo $insert_id;
	}
	
	/* dropdown company */
   function dropdownlist($name, $style, $tab, $val, $desc, $onChange){ 
		
		$string = "<select  name='".$name."' ".$tab." onchange='".$onChange."' class='select' id='".$name."' ".$style." >";
		$string .= "<option value=''> -- pilih perusahaan -- </option>";
		$data = $this->pms_m_monitoring_rm->getCompany();
		
		foreach ( $data as $row){
			if( (isset($default))){
				$string = $string." <option value=\"".$row[$val]."\"  selected>".$row[$desc]." </option>";
			} else {
				$string = $string." <option value=\"".$row[$val]."\">".$row[$desc]." </option>";
			}
		} 
		$string =$string. "</select>";
		return $string;
	}
	
	function exportToExcel(){
		$this->load->library('excel');
		$loginid = $this->session->userdata('LOGINID');
		$role = $this->pms_m_monitoring_rm->getRoleUser($loginid);
		$periode = $this->uri->segment(4);
		if( $role == "1000" || $role == "1002"){
			$company = $this->uri->segment(5);
		} else {
			$company = $this->session->userdata('DCOMPANY');
		}
		
        /* $data_row = $this->pms_m_monitoring_rm->retPengajuanRMXls($company);
				
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('rptPengambilanUmum');
		
		$this->excel->getActiveSheet()->setCellValue('A1', 'PROVIDENT AGRO');
		$this->excel->getActiveSheet()->setCellValue('A2', 'DAFTAR PENGAJUAN RAWAT INFRASTRUKTUR');
		//$this->excel->getActiveSheet()->setCellValue('A3', 'PERIODE ' . strtoupper($this->retNamaBulan($bulan)) ." ".$tahun);
		$this->excel->getActiveSheet()->getStyle('A1:A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->mergeCells('A1:L1');
		$this->excel->getActiveSheet()->mergeCells('A2:L2');
		
		$this->excel->getActiveSheet()->setCellValue('A5', 'NO');
		$this->excel->getActiveSheet()->setCellValue('B5', 'NO PENGAJUAN');
		$this->excel->getActiveSheet()->setCellValue('C5', 'TGL PENGAJUAN');
		$this->excel->getActiveSheet()->setCellValue('D5', 'KODE INFRASTRUKTUR');
		$this->excel->getActiveSheet()->setCellValue('E5', 'DESKRIPSI INFRASTRUKTUR');
		$this->excel->getActiveSheet()->setCellValue('F5', 'KETERANGAN PENGAJUAN');
		$this->excel->getActiveSheet()->setCellValue('G5', 'STATUS PENGAJUAN');
		$this->excel->getActiveSheet()->setCellValue('H5', 'PERSETUJUAN KEBUN');
		$this->excel->getActiveSheet()->setCellValue('I5', 'TGL PERSETUJUAN KEBUN');
		$this->excel->getActiveSheet()->setCellValue('J5', 'PERSETUJUAN HO');
		$this->excel->getActiveSheet()->setCellValue('K5', 'TGL PERSETUJUAN HO');
		$this->excel->getActiveSheet()->setCellValue('L5', 'PERUSAHAAN');
		
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		
		$rowXls = 6;
		$no = 1;
		$endCol = "L";
		foreach ($data_row as $row){
			$this->excel->getActiveSheet()->setCellValue('A'.$rowXls, $no);
			$this->excel->getActiveSheet()->setCellValue('B'.$rowXls, $row['RM_PENGAJUAN_ID']);
			$this->excel->getActiveSheet()->setCellValue('C'.$rowXls, $row['RM_TGL_PENGAJUAN']);
			$this->excel->getActiveSheet()->setCellValue('D'.$rowXls, $row['IFCODE']);
			$this->excel->getActiveSheet()->setCellValue('E'.$rowXls, $row['IFNAME']);
			$this->excel->getActiveSheet()->setCellValue('F'.$rowXls, $row['DESCRIPTION']);
			$this->excel->getActiveSheet()->setCellValue('G'.$rowXls, $row['PENGAJUAN_STATUS']);
			$this->excel->getActiveSheet()->setCellValue('H'.$rowXls, $row['ISAPPR1']);
			$this->excel->getActiveSheet()->setCellValue('I'.$rowXls, $row['ISAPPR1_DATE']);
			$this->excel->getActiveSheet()->setCellValue('J'.$rowXls, $row['ISAPPR2']);
			$this->excel->getActiveSheet()->setCellValue('K'.$rowXls, $row['ISAPPR2_DATE']);
			$this->excel->getActiveSheet()->setCellValue('L'.$rowXls, $row['COMPANY_CODE']);
			$rowXls++;
			$no++;
		}
		
		for($i="A"; $i<="L"; $i++){
			$this->excel->getActiveSheet()->getStyle($i.'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
		}
		
		$endCol = $endCol. ($rowXls-1);
		$this->excel->getActiveSheet()->getStyle('A5:'.$endCol)->applyFromArray($styleArray); */
		
		$where = "";
		if($company != "PAG"){
			$where .= " AND rm.COMPANY_CODE = '".$company."' ";
		} 	
		
		
		$Query ="SELECT rm.RM_PENGAJUAN_ID, PERIODE, DATE_FORMAT(RM_TGL_PENGAJUAN,'%d-%m-%Y') AS RM_TGL_PENGAJUAN, 		
					rm.IFCODE AS 'KODE INFRASTRUKTUR', iif.IFNAME AS 'DESKRIPSI INFRASTRUKTUR', RM_VALID_FROM, RM_VALID_TO, 
					RM_BUDGET, DESCRIPTION AS 'KETERANGAN PENGAJUAN', ACTIVITY_CODE AS 'KODE AKTIVITAS', c.COA_DESCRIPTION AS 'DESKRIPSI AKTIVITAS', 
					QTY, det.UOM AS 'SATUAN', RPSAT AS 'RUPIAH / SATUAN', RPTTL AS 'TOTAL (RUPIAH)',
					CASE WHEN rm.PENGAJUAN_STATUS = 0 THEN 'draft' 
					WHEN rm.PENGAJUAN_STATUS = 2 THEN 'approval kebun'
					WHEN rm.PENGAJUAN_STATUS = 1 THEN 'approved' END AS 
					'STATUS PENGAJUAN', ISAPPR1 AS 'PERSETUJUAN KEBUN', DATE_FORMAT(ISAPPR1_DATE,'%d-%m-%Y') AS 'TGL PERSETUJUAN KEBUN', 
					ISAPPR2 AS 'PERSETUJUAN HO', DATE_FORMAT(ISAPPR2_DATE,'%d-%m-%Y') AS 'TGL PERSETUJUAN HO', rm.COMPANY_CODE 
					FROM pms_rm_pengajuan rm
					LEFT JOIN m_infrastructure iif ON iif.IFCODE = rm.IFCODE AND iif.COMPANY_CODE = rm.COMPANY_CODE
					LEFT JOIN ( 
						SELECT RM_PENGAJUAN_ID, ACTIVITY_CODE, QTY, UOM, RPSAT, RPTTL FROM pms_rm_pengajuan_detail
						WHERE VOIDED = 0 
						GROUP BY RM_PENGAJUAN_ID, ACTIVITY_CODE	
					 ) det ON det.RM_PENGAJUAN_ID  = rm.RM_PENGAJUAN_ID
					LEFT JOIN m_coa c ON c.ACCOUNTCODE = det.ACTIVITY_CODE
					WHERE rm.PENGAJUAN_STATUS <> 9 AND PERIODE = '".$periode."' " .$where ; 
		
		$query=$this->db->query($Query);
		$filename='DAFTAR_PENGAJUAN_RM_'.$company.'_'.$periode; //save our workbook as this file name
		/*header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		$objWriter->save('php://output'); */
		to_excel($query, $filename);	
		
	}	
}

?>