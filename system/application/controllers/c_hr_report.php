<?
class c_hr_report extends Controller 
{
	function c_hr_report ()
	{
		parent::Controller();	
		$this->load->model( 'm_hr_report' );
		 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="c_hr_report";
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
	
	function index()
    {
		$view = "v_hr_report";
        $data = array();
        $data['judul_header'] = "Summary Data Karyawan";
		$data['js'] = $this->js_hr();    
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['dropcompany'] =  $this->dropdownlist_company();
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
            redirect('login');
        }	
    }  
	
	function js_hr(){
        $js = " jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + '-' + $('#bulan').val();
				var company = $('#cmb_company').val();
                var jns_laporan = $('#jns_laporan').val();
				if ( periode != '' ){
					if ( jns_laporan == 'html'){
							urls = url + 'c_hr_report/showAllpreview/' + company + '/' + periode, 
							$('#frame').attr('src',urls); 
						} else if ( jns_laporan == 'pdf'){
							urls = url+'c_hr_report/showAllpreview/' + company + '/' + periode,  
							$('#frame').attr('src',urls);                  
						} else if ( jns_laporan == 'excell'){
							urls = url + 'c_hr_report/xlsAllpreview/' + company + '/' + periode,
							$.download(urls,'');
					}
				} else {
                    alert('rentang periode salah!!');
                    return false;
                }
            });";
        return $js;
    }
	
	/* dropdown company */
   function dropdownlist_company(){ 
		$string = "<select  name='cmb_company' class='select' id='cmb_company' style='width:230px;'>";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = $this->m_hr_report->getCompany();
		
		foreach ( $data as $row){
		   if( (isset($default))){
			 $string = $string." <option value=\"".$row['COMPANY_CODE']."\"  selected>".$row['COMPANY_NAME']." </option>";
			} else {
			 $string = $string." <option value=\"".$row['COMPANY_CODE']."\">".$row['COMPANY_NAME']." </option>";
			}
		} 
		$string =$string. "</select>";
		return $string;
	}
	
	function tableStyle($company){
		$company_desc = "";
		foreach ($this->m_hr_report->getCompanyDesc($company) as $row){
			$company_desc = $row['COMPANY_NAME'];
		}
		$style = "<style>
			.tsc_table_s8 { background-color:#fff; border-collapse:collapse; width:100%;}
			.tsc_table_s8 caption { 
				font-size:0.9	em; color:#1ba6b2; font-weight:bold; 
				text-align:left; padding:10px; margin-bottom:2px;
				font-family: Arial, Helvetica, sans-serif;
			}
			.tsc_table_s8 thead th { 
				font-family: Arial, Helvetica, sans-serif;
				border:1px solid #f0f0f0; color:#fff; font-weight:bold; text-align:center; padding:2px; 	
				text-transform:uppercase; height:25px; background-color:#a3c159; 
				font-weight:normal; font-size:0.7em
			}
			.tsc_table_s8 tfoot { 
				font-family: Arial, Helvetica, sans-serif;
				color:#1ba6b2; padding:2px; text-transform:uppercase; font-size:1em; 
				font-weigth:bold; margin-top:6px; border:6px solid #e9f7f6;
			}
			.tsc_table_s8 tbody tr { font-family: Arial, Helvetica, sans-serif;
									background-color:#fff; border:1px solid #f0f0f0;}
			.tsc_table_s8 tbody td { border:1px solid #f0f0f0;font-family: Arial, Helvetica, sans-serif; font-weight:normal; 
									 font-size:0.7em; color:#414141; padding:5px; }
			.tsc_table_s8 tbody th { text-align:left; padding:2px; padding-left:5px; font-size:0.7em;}
			.tsc_table_s8 tbody td a,
			.tsc_table_s8 tbody th a { 
					color:#6C8C37; text-decoration:none; font-weight:normal; 
					display:block; padding-left:15px;
					font-family: Arial, Helvetica, sans-serif;
			}
			.tsc_table_s8 tbody td a:hover,
			.tsc_table_s8 tbody th a:hover { color:#009193; text-decoration:none;} 
			</style>";
		if($company_desc == "PAG"){ 
			$company_desc == "ALL PT"; 
		} else { 
			$company_desc = "PT. ".$company_desc; 
		}
		$style .= " <table class='tsc_table_s8' summary='Sample Table' style='width:80%;'>
				<caption>
				Summary Data Karyawan 
				</caption>
				<thead>
				  <tr>
					<th colspan='2' rowspan='2' scope='col'>".$company_desc."</th>
					<th colspan='2' scope='col'>Bulan Lalu</th>
					<th colspan='2' scope='col'>Masuk</th>
					<th colspan='2' scope='col'>Keluar</th>
					<th colspan='2' scope='col'>Sampai Dengan Bulan Ini</th>
				  </tr>
				   <tr>
					<th scope='col'>Jumlah</th>
					<th scope='col'>Persentase</th>
					<th scope='col'>Jumlah</th>
					<th scope='col'>Persentase</th>
					<th scope='col'>Jumlah</th>
					<th scope='col'>Persentase</th>
					<th scope='col'>Jumlah</th>
					<th scope='col'>Persentase</th>
				  </tr>
				</thead>";
   
		return $style;
	}
	
	function showAllpreview(){
		$company = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		
		$ret = $this->showTypeKaryawan($company, $periode);
		$ret .= $this->showJenisKelamin($company, $periode);
		$ret .= $this->showStatusKawin($company, $periode);
		$ret .= $this->showAgama($company, $periode);
		$ret .= $this->showUmur($company, $periode);
		$ret .= $this->showJabatan($company, $periode);
				
		$table = "<tbody>";
		echo $ret;
	}
	
	/* mulai function tampilkan data */
	function showTypeKaryawan($company, $periode){
		$string = $this->tableStyle($company);
		$data = $this->m_hr_report->rpt_hr_type($company, $periode);
		$baris = count($data);
		$rowspan = 0;
		
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		
		foreach ($data as $row){
			$flag = $row['FLAG'];
			$rowspan = $rowspan + 1;
			$string .= "<tr >";
			if($rowspan==1){
				$string .= "<th rowspan='".$baris."' scope='row'>".$row['FLAG']."</th>";
			}
			$string .= "<td>".$row['DESKRIPSI']."</td>
				<td style='text-align:right'>".$row['JUMLAH']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KARY']."% </td>
				<td style='text-align:right'>".$row['TOTAL_MASUK']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_MASUK']."% </td>
				<td style='text-align:right'>".$row['TOTAL_KELUAR']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KELUAR']."%</td>
				<td style='text-align:right'>".$row['TTL_AWAL_BERIKUT']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_NEXT']."%</td>
			  </tr>";
	  		$ttlJumlah = $ttlJumlah + $row['JUMLAH'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT'];
		}
		
		$string .= "<tr style='background-color:#CCC'>
        <th scope='row' colspan='2'>Total ".$flag."</th>
        <td style='text-align:right'><b>".$ttlJumlah."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlah." %</b></td>
		<td style='text-align:right'><b>".$ttlMasuk."</b></td>
		<td style='text-align:right'><b>".$ttlPersenMasuk." %</b></td>
		<td style='text-align:right'><b>".$ttlKeluar."</b></td>
		<td style='text-align:right'><b>".$ttlPersenKeluar." %</b></td>
		<td style='text-align:right'><b>".$ttlJumlahAkhir."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlahAkhir." %</b></td></tr>";
		return $string;
	}
	
	function showJenisKelamin($company, $periode){
		$data = $this->m_hr_report->rpt_hr_jk($company, $periode);
		$string = "";
		$baris = count($data);
		$rowspan = 0;
		
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		foreach ($data as $row)
        {
			$flag = $row['FLAG'];
			$rowspan = $rowspan + 1;
			$string .= "<tr >";
			if($rowspan==1){
				$string .= "<th rowspan='".$baris."' scope='row'>".$row['FLAG']."</th>";
			}
			$string .= "<td>".$row['DESKRIPSI']."</td>
				<td style='text-align:right'>".$row['JUMLAH_JK']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KARY_JK']."% </td>
				<td style='text-align:right'>".$row['TOTAL_MASUK_JK']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_MASUK_JK']."% </td>
				<td style='text-align:right'>".$row['TOTAL_KELUAR_JK']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KELUAR_JK']."%</td>
				<td style='text-align:right'>".$row['TTL_AWAL_BERIKUT_JK']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_NEXT_JK']."%</td>
			  </tr>";
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_JK'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_JK'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_JK'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_JK'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_JK'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_JK'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT_JK'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_JK'];
		}
		$string .= "<tr style='background-color:#CCC'>
        <th scope='row' colspan='2'>Total ".$flag."</th>
        <td style='text-align:right'><b>".$ttlJumlah."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlah." %</b></td>
		<td style='text-align:right'><b>".$ttlMasuk."</b></td>
		<td style='text-align:right'><b>".$ttlPersenMasuk." %</b></td>
		<td style='text-align:right'><b>".$ttlKeluar."</b></td>
		<td style='text-align:right'><b>".$ttlPersenKeluar." %</b></td>
		<td style='text-align:right'><b>".$ttlJumlahAkhir."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlahAkhir." %</b></td></tr>";
		return $string;
	}
	
	function showStatusKawin($company, $periode){
		$data = $this->m_hr_report->rpt_hr_status($company, $periode);
		$string = "";
		$baris = count($data);
		$rowspan = 0;
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		foreach ($data as $row)
        {
			$flag = $row['FLAG'];
			$rowspan = $rowspan + 1;
			$string .= "<tr >";
			if($rowspan==1){
				$string .= "<th rowspan='".$baris."' scope='row'>".$row['FLAG']."</th>";
			}
			$string .= "<td>".$row['FAMILY_STATUS']."</td>
				<td style='text-align:right'>".$row['JUMLAH_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KARY_STATUS']."% </td>
				<td style='text-align:right'>".$row['TOTAL_MASUK_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_MASUK_STATUS']."% </td>
				<td style='text-align:right'>".$row['TOTAL_KELUAR_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KELUAR_STATUS']."%</td>
				<td style='text-align:right'>".$row['TTL_AWAL_BERIKUT_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_NEXT_STATUS']."%</td>
			  </tr>";
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_STATUS'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_STATUS'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_STATUS'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_STATUS'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_STATUS'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_STATUS'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT_STATUS'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_STATUS'];
		}
		$string .= "<tr style='background-color:#CCC'>
        <th scope='row' colspan='2'>Total ".$flag."</th>
        <td style='text-align:right'><b>".$ttlJumlah."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlah." %</b></td>
		<td style='text-align:right'><b>".$ttlMasuk."</b></td>
		<td style='text-align:right'><b>".$ttlPersenMasuk." %</b></td>
		<td style='text-align:right'><b>".$ttlKeluar."</b></td>
		<td style='text-align:right'><b>".$ttlPersenKeluar." %</b></td>
		<td style='text-align:right'><b>".$ttlJumlahAkhir."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlahAkhir." %</b></td></tr>";
		return $string;
	}
	
	function showAgama($company, $periode){
		$data = $this->m_hr_report->rpt_hr_agama($company, $periode);
		$string = "";
		$baris = count($data);
		$rowspan = 0;
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		foreach ($data as $row)
        {
			$flag = $row['FLAG'];
			$rowspan = $rowspan + 1;
			$string .= "<tr >";
			if($rowspan==1){
				$string .= "<th rowspan='".$baris."' scope='row'>".$row['FLAG']."</th>";
			}
			$string .= "<td>".$row['DESKRIPSI_AG']."</td>
				<td style='text-align:right'>".$row['JUMLAH_AG']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KARY_AG']."% </td>
				<td style='text-align:right'>".$row['TOTAL_MASUK_AG']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_MASUK_AG']."% </td>
				<td style='text-align:right'>".$row['TOTAL_KELUAR_AG']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KELUAR_AG']."%</td>
				<td style='text-align:right'>".$row['TTL_AWAL_BERIKUT_AG']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_NEXT_AG']."%</td>
			  </tr>";
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_AG'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_AG'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_AG'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_AG'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_AG'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_AG'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT_AG'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_AG'];
		}
		$string .= "<tr style='background-color:#CCC'>
        <th scope='row' colspan='2'>Total ".$flag."</th>
        <td style='text-align:right'><b>".$ttlJumlah."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlah." %</b></td>
		<td style='text-align:right'><b>".$ttlMasuk."</b></td>
		<td style='text-align:right'><b>".$ttlPersenMasuk." %</b></td>
		<td style='text-align:right'><b>".$ttlKeluar."</b></td>
		<td style='text-align:right'><b>".$ttlPersenKeluar." %</b></td>
		<td style='text-align:right'><b>".$ttlJumlahAkhir."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlahAkhir." %</b></td></tr>";
		return $string;
	}
	
	function showUmur($company, $periode){
		$data = $this->m_hr_report->rpt_hr_umur($company, $periode);
		$string = "";
		$baris = count($data);
		$rowspan = 0;
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		foreach ($data as $row)
        {
			$flag = $row['FLAG'];
			$rowspan = $rowspan + 1;
			$string .= "<tr >";
			if($rowspan==1){
				$string .= "<th rowspan='".$baris."' scope='row'>".$row['FLAG']."</th>";
			}
			$string .= "<td>".$row['KATEGORI_UMUR']."</td>
				<td style='text-align:right'>".$row['JUMLAH_UMUR']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KARY_UMUR']."% </td>
				<td style='text-align:right'>".$row['TOTAL_MASUK_UMUR']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_MASUK_UMUR']."% </td>
				<td style='text-align:right'>".$row['TOTAL_KELUAR_UMUR']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KELUAR_UMUR']."%</td>
				<td style='text-align:right'>".$row['TTL_AWAL_BERIKUT_UMUR']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_NEXT_UMUR']."%</td>
			  </tr>";
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_UMUR'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_UMUR'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_UMUR'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_UMUR'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_UMUR'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_UMUR'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT_UMUR'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_UMUR'];
	  
		}
		$string .= "<tr style='background-color:#CCC'>
        <th scope='row' colspan='2'>Total ".$flag."</th>
        <td style='text-align:right'><b>".$ttlJumlah."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlah." %</b></td>
		<td style='text-align:right'><b>".$ttlMasuk."</b></td>
		<td style='text-align:right'><b>".$ttlPersenMasuk." %</b></td>
		<td style='text-align:right'><b>".$ttlKeluar."</b></td>
		<td style='text-align:right'><b>".$ttlPersenKeluar." %</b></td>
		<td style='text-align:right'><b>".$ttlJumlahAkhir."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlahAkhir." %</b></td></tr>";
		return $string;
	}
	
	function showJabatan($company, $periode){
		$data = $this->m_hr_report->rpt_hr_pangkat($company, $periode);
		$string = "";
		$baris = count($data);
		$rowspan = 0;
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		foreach ($data as $row)
        {
			$flag = $row['FLAG'];
			$rowspan = $rowspan + 1;
			$string .= "<tr >";
			if($rowspan==1){
				$string .= "<th rowspan='".$baris."' scope='row'>".$row['FLAG']."</th>";
			}
			$string .= "<td>".$row['EMP_LEVEL_DESC']."</td>
				<td style='text-align:right'>".$row['JUMLAH_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KARY_STATUS']."% </td>
				<td style='text-align:right'>".$row['TOTAL_MASUK_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_MASUK_STATUS']."% </td>
				<td style='text-align:right'>".$row['TOTAL_KELUAR_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_KELUAR_STATUS']."%</td>
				<td style='text-align:right'>".$row['TTL_AWAL_BERIKUT_STATUS']."</td>
				<td style='text-align:right'>".$row['PERSENTASE_NEXT_STATUS']."%</td>
			  </tr>";
	  		$ttlJumlah = $ttlJumlah + $row['JUMLAH_STATUS'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_STATUS'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_STATUS'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_STATUS'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_STATUS'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_STATUS'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT_STATUS'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_STATUS'];
	  
		}
		$string .= "<tr style='background-color:#CCC'>
        <th scope='row' colspan='2'>Total ".$flag."</th>
        <td style='text-align:right'><b>".$ttlJumlah."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlah." %</b></td>
		<td style='text-align:right'><b>".$ttlMasuk."</b></td>
		<td style='text-align:right'><b>".$ttlPersenMasuk." %</b></td>
		<td style='text-align:right'><b>".$ttlKeluar."</b></td>
		<td style='text-align:right'><b>".$ttlPersenKeluar." %</b></td>
		<td style='text-align:right'><b>".$ttlJumlahAkhir."</b></td>
		<td style='text-align:right'><b>".$ttlPersenJumlahAkhir." %</b></td></tr>";
		return $string;
	}
	
	/* -------------------- fungsi export ke excell ------------------------- */
	function xlsHeader($company, $periode){
		$company_desc = "";
		$judul = '';
        $header = ''; 
		foreach ($this->m_hr_report->getCompanyDesc($company) as $row){
			$company_desc = $row['COMPANY_NAME'];
		}
		$judul .= "Summary Data Karyawan ".$periode."\n";
		
		if($company_desc == "PAG"){ 
			$company_desc == "ALL PT"; 
		} else { 
			$company_desc = "PT. ".$company_desc; 
		}
						
		$header .= $company_desc ."\t";
		$header .= ""."\t";
		$header .= "Jumlah Bulan Lalu\t";
		$header .= "Persentase Bulan Lalu\t";
		$header .= "Jumlah Masuk Bulan Ini\t";
		$header .= "Persentase Masuk Bulan Ini\t";
		$header .= "Jumlah Keluar Bulan Ini\t";
		$header .= "Persentase Keluar Bulan Ini\t";
		$header .= "Jumlah Akhir Bulan Ini\t";
		$header .= "Persentase Akhir Bulan Ini\t";
        $header .= " \n";
   
		return $judul . $header;
	}
	
	function xlsAllpreview(){
		$company = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		
		$ret = $this->xlsTypeKaryawan($company, $periode);
		$ret .= $this->xlsJenisKelamin($company, $periode);
		$ret .= $this->xlsStatusKawin($company, $periode);
		$ret .= $this->xlsAgama($company, $periode);
		$ret .= $this->xlsUmur($company, $periode);
		$ret .= $this->xlsJabatan($company, $periode);
		 
		$data = str_replace("\r","",$ret);
                         
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=SKARYAWAN".$company."_".$periode.".xls");
    
        echo "$data"; 
	}
	
	/* mulai function tampilkan data */
	function xlsTypeKaryawan($company, $periode){
		$string = $this->xlsHeader($company, $periode);
		$data = $this->m_hr_report->rpt_hr_type($company, $periode);
				
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		
		foreach ($data as $row){
			$flag = $row['FLAG'];
			$string .= str_replace('"', '""',$row['FLAG'])."\t";
			$string .= str_replace('"', '""',$row['DESKRIPSI'])."\t";
			$string .= str_replace('"', '""',$row['JUMLAH'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KARY'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_MASUK'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_MASUK'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_KELUAR'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KELUAR'])."% \t";
			$string .= str_replace('"', '""',$row['TTL_AWAL_BERIKUT'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_NEXT'])."% \t \n";
			
			$ttlJumlah = $ttlJumlah + $row['JUMLAH'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT'];
		}

		$string .= str_replace('"', '""',$flag)."\t";
		$string .= "-"."\t";
        $string .= str_replace('"', '""',$ttlJumlah)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlah)."% \t";
		$string .= str_replace('"', '""',$ttlMasuk)."\t";
		$string .= str_replace('"', '""',$ttlPersenMasuk)."% \t";
		$string .= str_replace('"', '""',$ttlKeluar)."\t";
		$string .= str_replace('"', '""',$ttlPersenKeluar)."% \t";
		$string .= str_replace('"', '""',$ttlJumlahAkhir)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlahAkhir)."% \t \n";
		return $string;
	}
	
	function xlsJenisKelamin($company, $periode){
		
		$data = $this->m_hr_report->rpt_hr_jk($company, $periode);
				
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		$string = "";
		foreach ($data as $row){
			$flag = $row['FLAG'];
			$string .= str_replace('"', '""',$row['FLAG'])."\t";
			$string .= str_replace('"', '""',$row['DESKRIPSI'])."\t";
			$string .= str_replace('"', '""',$row['JUMLAH_JK'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KARY_JK'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_MASUK_JK'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_MASUK_JK'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_KELUAR_JK'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KELUAR_JK'])."% \t";
			$string .= str_replace('"', '""',$row['TTL_AWAL_BERIKUT_JK'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_NEXT_JK'])."% \t \n";
			
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_JK'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_JK'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_JK'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_JK'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_JK'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_JK'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['PERSENTASE_KELUAR_JK'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_JK'];
		}

		$string .= str_replace('"', '""',$flag)."\t";
		$string .= "-"."\t";
        $string .= str_replace('"', '""',$ttlJumlah)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlah)."% \t";
		$string .= str_replace('"', '""',$ttlMasuk)."\t";
		$string .= str_replace('"', '""',$ttlPersenMasuk)."% \t";
		$string .= str_replace('"', '""',$ttlKeluar)."\t";
		$string .= str_replace('"', '""',$ttlPersenKeluar)."% \t";
		$string .= str_replace('"', '""',$ttlJumlahAkhir)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlahAkhir)."% \t \n";
		return $string;
	}
	
	function xlsStatusKawin($company, $periode){
		
		$data = $this->m_hr_report->rpt_hr_status($company, $periode);
				
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		$string = "";
		foreach ($data as $row){
			$flag = $row['FLAG'];
			
			$string .= str_replace('"', '""',$row['FLAG'])."\t";
			$string .= str_replace('"', '""',$row['FAMILY_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['JUMLAH_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KARY_STATUS'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_MASUK_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_MASUK_STATUS'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_KELUAR_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KELUAR_STATUS'])."% \t";
			$string .= str_replace('"', '""',$row['TTL_AWAL_BERIKUT_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_NEXT_STATUS'])."% \t \n";
			
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_STATUS'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_STATUS'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_STATUS'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_STATUS'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_STATUS'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_STATUS'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['PERSENTASE_KELUAR_STATUS'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_STATUS'];
		}

		$string .= str_replace('"', '""',$flag)."\t";
		$string .= "-"."\t";
        $string .= str_replace('"', '""',$ttlJumlah)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlah)."% \t";
		$string .= str_replace('"', '""',$ttlMasuk)."\t";
		$string .= str_replace('"', '""',$ttlPersenMasuk)."% \t";
		$string .= str_replace('"', '""',$ttlKeluar)."\t";
		$string .= str_replace('"', '""',$ttlPersenKeluar)."% \t";
		$string .= str_replace('"', '""',$ttlJumlahAkhir)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlahAkhir)."% \t \n";
		return $string;
	}
	
	function xlsAgama($company, $periode){
		
		$data = $this->m_hr_report->rpt_hr_agama($company, $periode);
				
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		$string = "";
		
		foreach ($data as $row){
			$flag = $row['FLAG'];
			$string .= str_replace('"', '""',$row['FLAG'])."\t";
			$string .= str_replace('"', '""',$row['DESKRIPSI_AG'])."\t";
			$string .= str_replace('"', '""',$row['JUMLAH_AG'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KARY_AG'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_MASUK_AG'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_MASUK_AG'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_KELUAR_AG'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KELUAR_AG'])."% \t";
			$string .= str_replace('"', '""',$row['TTL_AWAL_BERIKUT_AG'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_NEXT_AG'])."% \t \n";
			
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_AG'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_AG'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_AG'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_AG'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_AG'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_AG'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['PERSENTASE_KELUAR_AG'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_AG'];
		}

		$string .= str_replace('"', '""',$flag)."\t";
		$string .= "-"."\t";
        $string .= str_replace('"', '""',$ttlJumlah)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlah)."% \t";
		$string .= str_replace('"', '""',$ttlMasuk)."\t";
		$string .= str_replace('"', '""',$ttlPersenMasuk)."% \t";
		$string .= str_replace('"', '""',$ttlKeluar)."\t";
		$string .= str_replace('"', '""',$ttlPersenKeluar)."% \t";
		$string .= str_replace('"', '""',$ttlJumlahAkhir)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlahAkhir)."% \t \n";
		return $string;
	}
	
	function xlsUmur($company, $periode){
		
		$data = $this->m_hr_report->rpt_hr_umur($company, $periode);
				
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		$string = "";
		foreach ($data as $row){
			$flag = $row['FLAG'];
			$string .= str_replace('"', '""',$row['FLAG'])."\t";
			$string .= str_replace('"', '""',$row['KATEGORI_UMUR'])."\t";
			$string .= str_replace('"', '""',$row['JUMLAH_UMUR'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KARY_UMUR'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_MASUK_UMUR'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_MASUK_UMUR'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_KELUAR_UMUR'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KELUAR_UMUR'])."% \t";
			$string .= str_replace('"', '""',$row['TTL_AWAL_BERIKUT_UMUR'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_NEXT_UMUR'])."% \t \n";
			
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_UMUR'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_UMUR'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_UMUR'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_UMUR'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_UMUR'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_UMUR'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT_UMUR'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_UMUR'];
		}

		$string .= str_replace('"', '""',$flag)."\t";
		$string .= "-"."\t";
        $string .= str_replace('"', '""',$ttlJumlah)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlah)."% \t";
		$string .= str_replace('"', '""',$ttlMasuk)."\t";
		$string .= str_replace('"', '""',$ttlPersenMasuk)."% \t";
		$string .= str_replace('"', '""',$ttlKeluar)."\t";
		$string .= str_replace('"', '""',$ttlPersenKeluar)."% \t";
		$string .= str_replace('"', '""',$ttlJumlahAkhir)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlahAkhir)."% \t \n";
		return $string;
	}
	
	function xlsJabatan($company, $periode){
		
		$data = $this->m_hr_report->rpt_hr_pangkat($company, $periode);
				
		$ttlJumlah = 0;
		$ttlPersenJumlah = 0;
		$ttlMasuk = 0;
		$ttlPersenMasuk = 0;
		$ttlKeluar = 0;
		$ttlPersenKeluar = 0;
		$ttlJumlahAkhir = 0;
		$ttlPersenJumlahAkhir = 0;
		$string = "";
		foreach ($data as $row){
			$flag = $row['FLAG'];
			
			$string .= str_replace('"', '""',$row['FLAG'])."\t";
			$string .= str_replace('"', '""',$row['EMP_LEVEL_DESC'])."\t";
			$string .= str_replace('"', '""',$row['JUMLAH_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KARY_STATUS'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_MASUK_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_MASUK_STATUS'])."% \t";
			$string .= str_replace('"', '""',$row['TOTAL_KELUAR_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_KELUAR_STATUS'])."% \t";
			$string .= str_replace('"', '""',$row['TTL_AWAL_BERIKUT_STATUS'])."\t";
			$string .= str_replace('"', '""',$row['PERSENTASE_NEXT_STATUS'])."% \t \n";
			
			$ttlJumlah = $ttlJumlah + $row['JUMLAH_STATUS'];
			$ttlPersenJumlah = $ttlPersenJumlah + $row['PERSENTASE_KARY_STATUS'];
			$ttlMasuk = $ttlMasuk + $row['TOTAL_MASUK_STATUS'];
			$ttlPersenMasuk = $ttlPersenMasuk + $row['PERSENTASE_MASUK_STATUS'];
			$ttlKeluar = $ttlKeluar + $row['TOTAL_KELUAR_STATUS'];
			$ttlPersenKeluar = $ttlPersenKeluar + $row['PERSENTASE_KELUAR_STATUS'];
			$ttlJumlahAkhir = $ttlJumlahAkhir + $row['TTL_AWAL_BERIKUT_STATUS'];
			$ttlPersenJumlahAkhir = $ttlPersenJumlahAkhir + $row['PERSENTASE_NEXT_STATUS'];
		}

		$string .= str_replace('"', '""',$flag)."\t";
		$string .= "-"."\t";
        $string .= str_replace('"', '""',$ttlJumlah)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlah)."% \t";
		$string .= str_replace('"', '""',$ttlMasuk)."\t";
		$string .= str_replace('"', '""',$ttlPersenMasuk)."% \t";
		$string .= str_replace('"', '""',$ttlKeluar)."\t";
		$string .= str_replace('"', '""',$ttlPersenKeluar)."% \t";
		$string .= str_replace('"', '""',$ttlJumlahAkhir)."\t";
		$string .= str_replace('"', '""',$ttlPersenJumlahAkhir)."% \t \n";
		return $string;
	}
}

?>