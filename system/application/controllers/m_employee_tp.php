<?

class m_employee_tp extends Controller 
{
    
    function m_employee_tp ()
    {
        parent::Controller();    

        $this->load->model('model_m_employee_tp'); 
        $this->load->model('model_m_natura');
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_employee_tp";
        
        $this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
		$this->load->database();
    }

	function index()
    {
        $data = array();
        
        $view = "info_m_employee_tp";
        $data['judul_header'] = "Tunjangan, Pendapatan Dan Potongan";
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
	
	/* data lembur */	
	function read_grid_lembur()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);
        echo json_encode($this->model_m_employee_tp->read_employee_ot($periode,$company));
    }
	
	function glembursingle(){
		 $id = htmlentities($this->input->post('LID'),ENT_QUOTES,'UTF-8');
		 $nik = htmlentities($this->input->post('LNIK'),ENT_QUOTES,'UTF-8');
		 $gc = htmlentities($this->input->post('LGANGCODE'),ENT_QUOTES,'UTF-8');
		 $date = htmlentities($this->input->post('LTGL'),ENT_QUOTES,'UTF-8');
		 $absen = htmlentities($this->input->post('LABSEN'),ENT_QUOTES,'UTF-8');
		 $loc = htmlentities($this->input->post('LLOCATION'),ENT_QUOTES,'UTF-8'); 
		 $act = htmlentities($this->input->post('LACTIVITY'),ENT_QUOTES,'UTF-8');
		 $co = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		 $this->model_m_employee_tp->glembursingle($id, $nik, $gc, $date, $absen, $loc, $act, $co);
	}
	
	function lembur_xls(){
        $periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		$obj =& get_instance();
		$data_row = $this->model_m_employee_tp->get_lembur($company, $periode);
		$bulan = substr($periode,-2);
    	if($bulan=='01'){ $bulan = "Januari"; } 
		else if($bulan=='02'){ $bulan = "Februari"; } 
		else if($bulan=='03'){ $bulan = "Maret"; } 
		else if($bulan=='04'){ $bulan = "April"; } 
		else if($bulan=='05'){ $bulan = "Mei"; } 
		else if($bulan=='06'){ $bulan = "Juni"; } 
		else if($bulan=='07'){ $bulan = "Juli"; } 
		else if($bulan=='08'){ $bulan = "Agustus"; } 
		else if($bulan=='09'){ $bulan = "September"; } 
		else if($bulan=='10'){ $bulan = "Oktober"; } 
		else if($bulan=='11'){ $bulan = "Nopember"; } 
		else if($bulan=='12'){ $bulan = "Desember"; }
		$judul = ''; $headers = ''; $data = '';  
		$judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t  \n"; 
        $judul .= "DAFTAR LEMBUR KARYAWAN \t \n";
        $judul .= "PERIODE  ".$bulan." ". substr($periode,0,4) ."\t \n";
        
		$headers .= "No. \t";
		$headers .= "NIK \t";  
		$headers .= "Nama \t";
        $headers .= "Kemandoran \t"; 
		$headers .= "Tanggal \t";
        $headers .= "Type Absensi \t"; 
		$headers .= "Type Lokasi \t";
		$headers .= "Kode Lokasi \t";
		$headers .= "Kode Aktivitas \t";
		$headers .= "Jam Lembur \t";
		$headers .= "Rupiah Lembur \t";         
				
		$no = 1;
        foreach ($data_row as $row)
        {
			 $line = '';
             $line .= str_replace('"', '""',$no)."\t";
			 $line .= str_replace('"', '""',trim($row['EMPLOYEE_CODE']))."\t";
			 $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
			 $line .= str_replace('"', '""',trim($row['GANG_CODE']))."\t";
			 $line .= str_replace('"', '""',trim($row['LHM_DATE']))."\t";
			 $line .= str_replace('"', '""',trim($row['TYPE_ABSENSI']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['LOCATION_TYPE_CODE']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['LOCATION_CODE']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['ACTIVITY_CODE']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['LEMBUR_JAM']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['LEMBUR_RUPIAH']))."\t"; 
			 $no++;
             $data .= trim($line)."\n";
		}
		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=LEMBUR_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data\n"; 
	}
	/* end lembur */
	
	/* data kontanan */	
	function read_grid_kontanan()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);
        echo json_encode($this->model_m_employee_tp->read_employee_kontanan($periode,$company));
    }
	
	function kontanan_xls(){
        $periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		$obj =& get_instance();
		$data_row = $this->model_m_employee_tp->get_kontanan($company, $periode);
		$bulan = substr($periode,-2);
    	if($bulan=='01'){ $bulan = "Januari"; } 
		else if($bulan=='02'){ $bulan = "Februari"; } 
		else if($bulan=='03'){ $bulan = "Maret"; } 
		else if($bulan=='04'){ $bulan = "April"; } 
		else if($bulan=='05'){ $bulan = "Mei"; } 
		else if($bulan=='06'){ $bulan = "Juni"; } 
		else if($bulan=='07'){ $bulan = "Juli"; } 
		else if($bulan=='08'){ $bulan = "Agustus"; } 
		else if($bulan=='09'){ $bulan = "September"; } 
		else if($bulan=='10'){ $bulan = "Oktober"; } 
		else if($bulan=='11'){ $bulan = "Nopember"; } 
		else if($bulan=='12'){ $bulan = "Desember"; }
		$judul = ''; $headers = ''; $data = '';  
		$judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t  \n"; 
        $judul .= "DAFTAR KONTANAN KARYAWAN \t \n";
        $judul .= "PERIODE  ".$bulan." ". substr($periode,0,4) ."\t \n";
        
		$headers .= "No. \t";
		$headers .= "NIK \t";  
		$headers .= "Nama \t";
        $headers .= "Kemandoran \t"; 
		$headers .= "Tanggal \t";
        $headers .= "Type Absensi \t"; 
		$headers .= "Type Lokasi \t";
		$headers .= "Kode Lokasi \t";
		$headers .= "Kode Aktivitas \t";
		$headers .= "Rupiah Kontanan \t";         
				
		$no = 1;
        foreach ($data_row as $row)
        {
			 $line = '';
             $line .= str_replace('"', '""',$no)."\t";
			 $line .= str_replace('"', '""',trim($row['EMPLOYEE_CODE']))."\t";
			 $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
			 $line .= str_replace('"', '""',trim($row['GANG_CODE']))."\t";
			 $line .= str_replace('"', '""',trim($row['LHM_DATE']))."\t";
			 $line .= str_replace('"', '""',trim($row['TYPE_ABSENSI']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['LOCATION_TYPE_CODE']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['LOCATION_CODE']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['ACTIVITY_CODE']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['POTONGAN_KONTANAN']))."\t"; 
			 $no++;
             $data .= trim($line)."\n";
		}
		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=KONTANAN_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data\n"; 
	}
	/* end kontanan */
	
	/* BPJS */
	
	function read_grid_bpjs()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(4);
		$filt = $this->uri->segment(3);
        echo json_encode($this->model_m_employee_tp->read_employee_bpjs($periode,$company,$filt));
    }
	
	function generate_bpjs_kes(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);
		
		$persentase = $this->model_m_employee_tp->getKoefBPJS($periode);
		$persentase = explode("|",$persentase);
		$persenPotongan = $persentase[0];
		$persenTunjangan = $persentase[1];
		
		$getKaryawanBPJS = $this->model_m_employee_tp->getKaryawanBPJSKes($company);
		foreach ( $getKaryawanBPJS as $row)
		{
			//$result .= $row['result'];
			$nik = $row['NIK'];
			$cekExistTunpot = $this->model_m_employee_tp->cek_existBPJS($nik, $periode, $company);
			
			$gp = $row['GP'];
			$potonganBPJSKes = $gp * ( $persenPotongan / 100 );
			$tunjanganBPJSKes = $gp * ( $persenTunjangan / 100 );
			if($cekExistTunpot > 0){
				//update
				
				//$data_post['NIK'] = $nik;
				//$data_post['PERIODE'] = $periode;
				//$data_post['COMPANY_CODE'] = $company;
				$data_post['POTONGAN_BPJS_KES'] = $potonganBPJSKes;
				$data_post['TUNJANGAN_BPJS_KES'] = $tunjanganBPJSKes;
				$postdata['UPDATE_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                $postdata['UPDATE_DATE']=   date ("Y-m-d H:i:s");
				
				$insert_id = $this->model_m_employee_tp->updateBPJS( $nik, $periode, $data_post );
				
			} else {
				//insert
				$data_post['NIK'] = $nik;
				$data_post['PERIODE'] = $periode;
				$data_post['COMPANY_CODE'] = $company;
				$data_post['POTONGAN_BPJS_KES'] = $potonganBPJSKes;
				$data_post['TUNJANGAN_BPJS_KES'] = $tunjanganBPJSKes;
				$postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                $postdata['INSERT_DATE']=   date ("Y-m-d H:i:s");
				
				$insert_id = $this->model_m_employee_tp->insertBPJS( $data_post );
			}
		}
		
	}
	/* end BPJS */
	
	/* data tunjangan potongan */	
	function read_grid_tunpot()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);
		$nik = $this->uri->segment(4);
		$nama = $this->uri->segment(5);
        echo json_encode($this->model_m_employee_tp->read_employee_tunpot($periode,$company,$nik,$nama));
    }
	
	function create_tunpot()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = $this->uri->segment(3);
		$nik = htmlentities($this->input->post('NIK'),ENT_QUOTES,'UTF-8');
        $nama  = htmlentities($this->input->post('NAMA'),ENT_QUOTES,'UTF-8');
       
        $data_emp = $this->model_m_employee_tp->cek_employee($company,$nik);
        $data_tunpot = $this->model_m_employee_tp->cek_tp_employee($nik,$periode,$company);
         
        $status='';
        $postdata=array();
        
        if(sizeof($data_emp)==0) {
            $status="data karyawan tidak ada";
            echo $status;
        } 
        if($data_tunpot>0) {
            $status="data untuk karyawan dengan NIK: ".$nik." sudah ada";
            echo $status;
        }
        
        if(empty($status) || $status=='')
        {
            if(sizeof($data_emp)>1)
            {
                $postdata['NIK']=$nik;
                $postdata['TUNJANGAN_JABATAN']=htmlentities($this->input->post('TUNJAB'),ENT_QUOTES,'UTF-8');
				$postdata['TUNJANGAN_CUTI']=htmlentities($this->input->post('TUNJANGAN_CUTI'),ENT_QUOTES,'UTF-8');
				$postdata['KOMPENSASI_CUTI']=htmlentities($this->input->post('KOMPENSASI_CUTI'),ENT_QUOTES,'UTF-8');
                $postdata['POTONGAN_LAIN']=htmlentities($this->input->post('POTLAIN'),ENT_QUOTES,'UTF-8');
				$postdata['SUBSIDI_KENDARAAN']=htmlentities($this->input->post('SUBKEND'),ENT_QUOTES,'UTF-8');
				$postdata['TUNJ_TRANSPORT']=htmlentities($this->input->post('TUNTRANS'),ENT_QUOTES,'UTF-8');
				$postdata['RAPEL']=htmlentities($this->input->post('RAPEL'),ENT_QUOTES,'UTF-8');
				$postdata['THR']=htmlentities($this->input->post('THR'),ENT_QUOTES,'UTF-8');
				$postdata['PPH_21']=htmlentities($this->input->post('PPH'),ENT_QUOTES,'UTF-8');
				$postdata['KETERANGAN']=htmlentities($this->input->post('KETERANGAN'),ENT_QUOTES,'UTF-8');
                $postdata['COMPANY_CODE']=$company; 
                $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                $postdata['INSERT_DATE']=   date ("Y-m-d H:i:s");
            } elseif(sizeof($data_emp)==1) {
                foreach($data_emp as $row) { $postdata['NIK']=htmlentities($row['NIK'],ENT_QUOTES,'UTF-8'); }
                $postdata['PERIODE']= $periode;
                $postdata['NIK']= $nik;
                $postdata['TUNJANGAN_JABATAN']=htmlentities($this->input->post('TUNJAB'),ENT_QUOTES,'UTF-8');
				$postdata['TUNJANGAN_CUTI']=htmlentities($this->input->post('TUNJANGAN_CUTI'),ENT_QUOTES,'UTF-8');
				$postdata['KOMPENSASI_CUTI']=htmlentities($this->input->post('KOMPENSASI_CUTI'),ENT_QUOTES,'UTF-8');
                $postdata['POTONGAN_LAIN']=htmlentities($this->input->post('POTLAIN'),ENT_QUOTES,'UTF-8');
				$postdata['SUBSIDI_KENDARAAN']=htmlentities($this->input->post('SUBKEND'),ENT_QUOTES,'UTF-8');
				$postdata['TUNJ_TRANSPORT']=htmlentities($this->input->post('TUNTRANS'),ENT_QUOTES,'UTF-8');
				$postdata['RAPEL']=htmlentities($this->input->post('RAPEL'),ENT_QUOTES,'UTF-8');
				$postdata['THR']=htmlentities($this->input->post('THR'),ENT_QUOTES,'UTF-8');
				$postdata['PPH_21']=htmlentities($this->input->post('PPH'),ENT_QUOTES,'UTF-8');
				$postdata['KETERANGAN']=htmlentities($this->input->post('KETERANGAN'),ENT_QUOTES,'UTF-8');
                $postdata['COMPANY_CODE']=$company; 
                $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                $postdata['INSERT_DATE']=   date ("Y-m-d H:i:s");
            }
            $insert = $this->model_m_natura->insert_natura($postdata);  
            echo $insert;
        }
    }
	
	function update_tunpot()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $nik = htmlentities($this->input->post('NIK'),ENT_QUOTES,'UTF-8');
        $periode = $this->uri->segment(3);
        
        $data_emp = $this->model_m_employee_tp->cek_employee($company,$nik);
        $status='';
        
        if(sizeof($data_emp)==0)//data kosong
        {
            $status="data karyawan tidak ada";
            echo $status;
        } 
        if(empty($status) || $status=='')
        {
			$postdata['TUNJANGAN_JABATAN']=htmlentities($this->input->post('TUNJAB'),ENT_QUOTES,'UTF-8');
			$postdata['TUNJANGAN_CUTI']=htmlentities($this->input->post('TUNJANGAN_CUTI'),ENT_QUOTES,'UTF-8');
			$postdata['KOMPENSASI_CUTI']=htmlentities($this->input->post('KOMPENSASI_CUTI'),ENT_QUOTES,'UTF-8');
            $postdata['POTONGAN_LAIN']=htmlentities($this->input->post('POTLAIN'),ENT_QUOTES,'UTF-8');
			$postdata['SUBSIDI_KENDARAAN']=htmlentities($this->input->post('SUBKEND'),ENT_QUOTES,'UTF-8');
			$postdata['TUNJ_TRANSPORT']=htmlentities($this->input->post('TUNTRANS'),ENT_QUOTES,'UTF-8');
			$postdata['RAPEL']=htmlentities($this->input->post('RAPEL'),ENT_QUOTES,'UTF-8');
			$postdata['THR']=htmlentities($this->input->post('THR'),ENT_QUOTES,'UTF-8');
			$postdata['PPH_21']=htmlentities($this->input->post('PPH'),ENT_QUOTES,'UTF-8');
			$postdata['KETERANGAN']=htmlentities($this->input->post('KETERANGAN'),ENT_QUOTES,'UTF-8');
            $postdata['COMPANY_CODE']=$company; 
            $postdata['UPDATE_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
            $postdata['UPDATE_DATE']= date ("Y-m-d H:i:s");
            $update = $this->model_m_natura->update_natura($nik,$periode,$company,$postdata);  
            echo $update;
        }
    }
		
	/* lookup employee */
	function lookup_employee()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_emp = $this->model_m_employee_tp->lookup_employee($company,$q);
         
        //echo $q;
        $employee = array();
        foreach($data_emp as $row)
            {
                $employee[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).'"}';
            }
              echo '['.implode(',',$employee).']'; exit; 
    }
	/* end lookup */
	
	function tunpot_xls(){
        $periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		$obj =& get_instance();
		$data_row = $this->model_m_employee_tp->get_tunpot($company, $periode);
		$bulan = substr($periode,-2);
    	if($bulan=='01'){ $bulan = "Januari"; } 
		else if($bulan=='02'){ $bulan = "Februari"; } 
		else if($bulan=='03'){ $bulan = "Maret"; } 
		else if($bulan=='04'){ $bulan = "April"; } 
		else if($bulan=='05'){ $bulan = "Mei"; } 
		else if($bulan=='06'){ $bulan = "Juni"; } 
		else if($bulan=='07'){ $bulan = "Juli"; } 
		else if($bulan=='08'){ $bulan = "Agustus"; } 
		else if($bulan=='09'){ $bulan = "September"; } 
		else if($bulan=='10'){ $bulan = "Oktober"; } 
		else if($bulan=='11'){ $bulan = "Nopember"; } 
		else if($bulan=='12'){ $bulan = "Desember"; }
		$judul = ''; $headers = ''; $data = '';  
		$judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t  \n"; 
        $judul .= "DAFTAR TUNJANGAN & POTONGAN KARYAWAN \t \n";
        $judul .= "PERIODE  ".$bulan." ". substr($periode,0,4) ."\t \n";
        
		$headers .= "No. \t";
		$headers .= "NIK \t";  
		$headers .= "Nama \t";
        $headers .= "Tunjangan Jabatan \t"; 
		$headers .= "Tunjangan Cuti Tahunan\t"; 
		$headers .= "Kompensasi Cuti 5 Tahunan\t"; 
		$headers .= "Potongan Lain \t";
        $headers .= "Subsidi Kendaraan \t"; 
		$headers .= "Tunjangan Transport \t";
		$headers .= "Rapel \t";
		$headers .= "THR \t";
		$headers .= "Tunjangan BPJS Kes(4%) \t"; 
		$headers .= "Potongan BPJS Kes(4.5%/5%) \t"; 
		$headers .= "PPH 21 \t";
		$headers .= "Keterangan \t";         
				
		$no = 1;
        foreach ($data_row as $row)
        {
			 $line = '';
             $line .= str_replace('"', '""',$no)."\t";
			 $line .= str_replace('"', '""',trim($row['NIK']))."\t";
			 $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
			 $line .= str_replace('"', '""',trim($row['TUNJANGAN_JABATAN']))."\t";
			 $line .= str_replace('"', '""',trim($row['TUNJANGAN_CUTI']))."\t";
			 $line .= str_replace('"', '""',trim($row['KOMPENSASI_CUTI']))."\t";
			 $line .= str_replace('"', '""',trim($row['POTONGAN_LAIN']))."\t";
			 $line .= str_replace('"', '""',trim($row['SUBSIDI_KENDARAAN']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['TUNJ_TRANSPORT']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['RAPEL']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['THR']))."\t";
			 $line .= str_replace('"', '""',trim($row['TUNJ_BPJS_KES']))."\t";
			 $line .= str_replace('"', '""',trim($row['POT_BPJS_KES']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['PPH_21']))."\t"; 
			 $line .= str_replace('"', '""',trim($row['KETERANGAN']))."\t"; 
			 $no++;
             $data .= trim($line)."\n";
		}
		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=TUNJPOTONGAN_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data\n"; 
	}
	
	/* generate pph21 */
	function drop_temp() {
		$query = $this->db->query("DROP TEMPORARY TABLE IF EXISTS rptdu;");
	}
	
	function drop_temp_detail() {
		$query = $this->db->query("DROP TEMPORARY TABLE IF EXISTS rptdu_detail;");
	}
	
	function rpttbdu($company, $periode) {
		$query = $this->db->query("CALL sp_tb_rptdu('".$company."','".$periode."')");
	}
	
	function rpttbdu_detail($company, $periode) {
		$query = $this->db->query("CALL sp_tb_rptdu_detail('".$company."','".$periode."')");
	}
	
	function gen_pph($company, $periode) {
	
		$query = $this->db->query("CALL sp_update_pph21_new('".$company."','".$periode."')");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result; 
	}
	
	function execute() {
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);
		
		$this->drop_temp();
		$this->drop_temp_detail();
		$this->rpttbdu($company,$periode);
		$this->rpttbdu_detail($company,$periode);
		
		$data_pph = $this->gen_pph($company,$periode);
		$result = "";
		foreach ( $data_pph as $row)
		{
			$result .= $row['result'];
		}
		
		echo "data pph 21 " . $result . " orang berhasil tergenerate";

	}
	/* end generate ph21*/
}

?>