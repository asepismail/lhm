<?

class m_employee extends Controller 
{
    
    function m_employee ()
    {
        parent::Controller();    
        $this->load->model('model_m_employee'); 
        $this->load->model('model_m_natura');
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_employee";
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
        $view = "info_m_employee";
        $data['judul_header'] = "Master Data Karyawan";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['dept'] = $this->dropdownlist_dept();
		$data['level'] = $this->dropdownlist_level();
		$data['afd'] = $this->dropdownlist_afd();
		$data['education'] = $this->dropdownlist_education();
		$data['famstat'] = $this->dropdownlist_status('i_famstat');
		$data['taxstat'] = $this->dropdownlist_status('i_taxstat');
		$data['costcenter'] = $this->dropdownlist_costcenter();
		
		
        $data['date_now'] = $this->date_now("all"); 
        $data['natura_periode'] = $this->date_now('year').$this->date_now('month');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
       
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }    
    
    function read_grid_emp()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_employee->read_employee($company));
    }
    
    function search_emp()
    {
        $nik = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $name = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $dept = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $inactive = htmlentities($this->uri->segment(6),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_employee->search_employee($nik, $name,$dept,$inactive, $company));
    }
      
	function cek_emp()
    {
		$this->obj = &get_instance();
		$reload = $this->uri->segment(4);
		if($reload == "reload"){
			$type = $this->uri->segment(3);
		} else {
			$type = $_REQUEST['_value']; 
		}
		 
		$year = date('y');
		$hasil = "";
		$companyNumber = "";
		$namaPK = "NIK";
		$companyNumberArr = $this->model_m_employee->getcompanynumber($this->session->userdata('DCOMPANY'));
		foreach ( $companyNumberArr as $row) {
			$companyNumber = $row['COMPANY_NUMBER'];
		}
		if( $type == "KDMP" || $type == "BHL") { 
			$hasil = $companyNumber . "B";
			$this->obj->db->select_max($namaPK);
			$this->obj->db->like($namaPK, $hasil, 'after'); 
			$this->obj->db->from('m_employee');
			
			$temp= $this->obj->db->get();
			$this->obj->db->close();
			$temp = $temp->result_array();
		
			if(empty($temp[0][$namaPK])){
				$hasil = $hasil."00001";
			} else {
				$str = $temp[0][$namaPK];
				$length = strlen($str) - 5;
				$str = substr($str,$length,strlen($str));
				$str = $str+1;
				
				$panjangString = 5;
				$jumlahNol = $panjangString - strlen($str);
				
				for($i =0;$i<$jumlahNol;$i++) {
					$hasil .= "0";
				}
				$hasil .= $str;
			}
		} else {
			$hasil = $companyNumber . $year;
			$this->obj->db->select_max($namaPK);
			$this->obj->db->like($namaPK, $hasil, 'after'); 
			$this->obj->db->from('m_employee');
			
			$temp= $this->obj->db->get();
			$this->obj->db->close();
			$temp = $temp->result_array();
		
			if(empty($temp[0][$namaPK])){
				$hasil = $hasil."0001";
			} else {
				$str = $temp[0][$namaPK];
				$length = strlen($str) - 4;
				$str = substr($str,$length,strlen($str));
				$str = $str+1;
				
				$panjangString = 4;
				$jumlahNol = $panjangString - strlen($str);
				
				for($i =0;$i<$jumlahNol;$i++) {
					$hasil .= "0";
				}
				$hasil .= $str;
			}
		}
		$array[] = array($hasil => $hasil);
		
		if($reload == "reload"){
			echo $hasil;
		} else {
        	echo json_encode($array);
		}
	}
	
    function create( )
    {
        $data_post['NIK'] = htmlentities($this->input->post( 'NIK' ),ENT_QUOTES,'UTF-8');
        $data_post['NAMA'] = strtoupper(htmlentities($this->input->post( 'NAMA' ),ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_KARYAWAN'] = strtoupper(htmlentities($this->input->post( 'TYPE_KARYAWAN' )));
        $data_post['GP'] = $this->input->post( 'GP' );
        $data_post['HK'] = $this->input->post( 'GP' ) / 25;
        $data_post['TANGGAL_LAHIR'] = htmlentities($this->input->post( 'TANGGAL_LAHIR' ),ENT_QUOTES,'UTF-8');
        $data_post['PANGKAT'] = strtoupper(htmlentities($this->input->post( 'PANGKAT' ),ENT_QUOTES,'UTF-8'));
		$data_post['JABATAN'] = strtoupper(htmlentities($this->input->post( 'JABATAN' ),ENT_QUOTES,'UTF-8'));
        $data_post['COST_CENTER'] = strtoupper(htmlentities($this->input->post( 'COST_CENTER' ),ENT_QUOTES,'UTF-8'));
        $data_post['DEPT_CODE'] = strtoupper(htmlentities($this->input->post( 'DEPT_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['ESTATE_CODE'] = strtoupper(htmlentities($this->input->post( 'ESTATE_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['DATE_JOIN'] = htmlentities($this->input->post( 'DATE_JOIN' ),ENT_QUOTES,'UTF-8');
		$data_post['DATE_PROMOTION'] = htmlentities($this->input->post( 'DATE_PROMOTION' ),ENT_QUOTES,'UTF-8');
        $data_post['FAMILY_STATUS'] = strtoupper(htmlentities($this->input->post( 'FAMILY_STATUS' ),ENT_QUOTES,'UTF-8'));
		$data_post['LAST_EDUCATION'] = strtoupper(htmlentities($this->input->post( 'LAST_EDUCATION' ),ENT_QUOTES,'UTF-8'));
        $data_post['TAX_STATUS'] =  strtoupper(htmlentities($this->input->post( 'TAX_STATUS' ),ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT'] = htmlentities($this->input->post( 'ALAMAT' ),ENT_QUOTES,'UTF-8');
        $data_post['PHONE'] = htmlentities($this->input->post( 'PHONE' ),ENT_QUOTES,'UTF-8');
        $data_post['NO_JAMSOSTEK'] = htmlentities($this->input->post( 'NO_JAMSOSTEK' ),ENT_QUOTES,'UTF-8');
		$data_post['NO_NPWP'] = htmlentities($this->input->post( 'NO_NPWP' ),ENT_QUOTES,'UTF-8');
		$data_post['DIVISION_CODE'] = strtoupper(htmlentities($this->input->post( 'DIVISION_CODE' ),ENT_QUOTES,'UTF-8'));
		$data_post['NO_IDENTITAS'] = htmlentities($this->input->post( 'NO_IDENTITAS' ),ENT_QUOTES,'UTF-8');
        $data_post['RELIGION'] = htmlentities($this->input->post( 'RELIGION' ),ENT_QUOTES,'UTF-8');
        $data_post['SEX'] = htmlentities($this->input->post( 'SEX' ),ENT_QUOTES,'UTF-8');
        $data_post['E_INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$data_post['E_INPUT_DATE'] = date ("Y-m-d H:i:s");
		
		$data_post['NAMA_SEKOLAH']=htmlentities($this->input->post('NAMA_SEKOLAH'),ENT_QUOTES,'UTF-8');
		$data_post['JURUSAN']=htmlentities($this->input->post('JURUSAN'),ENT_QUOTES,'UTF-8');
		$data_post['ALAMAT_SEKOLAH']=htmlentities($this->input->post('ALAMAT_SEKOLAH'),ENT_QUOTES,'UTF-8');
		$data_post['ISBPJS_KETENAGAKERJAAN']=htmlentities($this->input->post('ISBPJS_KETENAGAKERJAAN'),ENT_QUOTES,'UTF-8');
		$data_post['NO_REG_BPJS_TNG']=htmlentities($this->input->post('NO_REG_BPJS_TNG'),ENT_QUOTES,'UTF-8');
		$data_post['ISBPJS_KESEHATAN']=htmlentities($this->input->post('ISBPJS_KESEHATAN'),ENT_QUOTES,'UTF-8');
		$data_post['NO_REG_BPJS_KES']=htmlentities($this->input->post('NO_REG_BPJS_KES'),ENT_QUOTES,'UTF-8');
		
        $data_post['COMPANY_CODE'] = htmlentities($this->input->post( 'COMPANY_CODE' ),ENT_QUOTES,'UTF-8');
        
        $company = $this->input->post('COMPANY_CODE');
        $nama = $this->input->post('NAMA');
        $type_karyawan = $this->input->post('TYPE_KARYAWAN');
        $status = $this->input->post('FAMILY_STATUS');
        $nik = $this->input->post('NIK');
        $status = "";
        
        $data_karyawan = $this->model_m_employee->cek_exist_employee($company,$nik,$type_karyawan);
        if($data_karyawan > 0)
        { 
            $status = "data karyawan dengan NIK ". $nik . " dan type karyawan " . $type_karyawan . " sudah ada mohon periksa kembali!!!";
            echo $status;
        } else {
            if ($this->input->post( 'NAMA' ) == "") 
            {
                $status = "nama tidak boleh kosong";
                echo $status;
            } 
            else if ( $this->input->post( 'TYPE_KARYAWAN' ) == "") 
            {
                $status = "type karyawan tidak boleh kosong";
                echo $status;
            } 
            else if ( $this->input->post( 'FAMILY_STATUS' ) == "")
            {
                $status = "status keluarga tidak boleh kosong";
                echo $status;
            }
            else if($this->input->post( 'DATE_JOIN' )=="") 
            {
                $status="tanggal masuk tidak boleh kosong";
                echo $status;
            }
            else 
            {  
                $insert_id = $this->model_m_employee->insert_m_employee( $data_post );
                
                //############ otomatisasi perhitungan untuk naturav###########
               /*  if (strtoupper($data_post['TYPE_KARYAWAN']) =="SKU" || strtoupper($data_post['TYPE_KARYAWAN'])=="BULANAN")
                {
                    $koef_natura_val='0';
                    $koef_natura=$this->model_m_natura->get_natura_koefisien($company,$data_post['FAMILY_STATUS']);
                    
                    if(count($koef_natura)>0 && is_array($koef_natura))
                    {
                        foreach($koef_natura as $row)
                        {
                            $koef_natura_val= $row['NATURA'];
                        } 
                        
                    } 
                    
                    $data_natura = $this->model_m_natura->cek_natura_employee($nik,date("Ym",mktime()),$company);
                    if($data_natura>0)
                    {
                        $postdata['NATURA']=$koef_natura_val;
                        $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                        $update = $this->model_m_natura->update_natura($nik,date("Ym",mktime()),$company,$postdata);
                    }else{
                        $postdata['NIK']=$nik;
                        $postdata['NATURA']=$koef_natura_val;
                        $postdata['PERIODE']=date("Ym",mktime());
                        $postdata['COMPANY_CODE']=$company; 
                        $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                        $insert = $this->model_m_natura->insert_natura($postdata);
                    }    
                } */
                
                //############################################################# 
            }
        }
    }

    
    function update ()
    {
        $nik = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $act="edit";
        
        //$inputPost = $_POST;
        //$data_sanitize=$this->data_sanitize($inputPost);
          
        //$data_new=array();
        //$data_new['E_INPUT_BY'] = htmlentities($this->session->userdata('LOGINID')); 
        //$data_sanitize=array_merge($data_sanitize,$data_new);

        $isActive=strtolower(htmlentities(trim($this->input->post('INACTIVE')),ENT_QUOTES,'UTF-8')) ;
        $isActive=($isActive=='true')?1:0; 
        
        $data_post['NIK'] = htmlentities($this->input->post( 'NIK' ),ENT_QUOTES,'UTF-8');
        $data_post['NAMA'] = strtoupper(htmlentities($this->input->post( 'NAMA' ),ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_KARYAWAN'] = strtoupper(htmlentities($this->input->post( 'TYPE_KARYAWAN' ),ENT_QUOTES,'UTF-8'));
        $data_post['GP'] = $this->input->post( 'GP' );
        $data_post['HK'] = $this->input->post( 'GP' ) / 25;
        $data_post['TANGGAL_LAHIR'] = htmlentities($this->input->post( 'TANGGAL_LAHIR' ),ENT_QUOTES,'UTF-8');
        $data_post['PANGKAT'] = strtoupper(htmlentities($this->input->post( 'PANGKAT' ),ENT_QUOTES,'UTF-8'));
		$data_post['JABATAN'] = strtoupper(htmlentities($this->input->post( 'JABATAN' ),ENT_QUOTES,'UTF-8'));
        $data_post['COST_CENTER'] = strtoupper(htmlentities($this->input->post( 'COST_CENTER' ),ENT_QUOTES,'UTF-8'));
        $data_post['DEPT_CODE'] = strtoupper(htmlentities($this->input->post( 'DEPT_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['ESTATE_CODE'] = strtoupper(htmlentities($this->input->post( 'ESTATE_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['DATE_JOIN'] = htmlentities($this->input->post( 'DATE_JOIN' ),ENT_QUOTES,'UTF-8');
		$data_post['DATE_PROMOTION'] = htmlentities($this->input->post( 'DATE_PROMOTION' ),ENT_QUOTES,'UTF-8');
        $data_post['FAMILY_STATUS'] = strtoupper(htmlentities($this->input->post( 'FAMILY_STATUS' ),ENT_QUOTES,'UTF-8'));
		$data_post['LAST_EDUCATION'] = strtoupper(htmlentities($this->input->post( 'LAST_EDUCATION' ),ENT_QUOTES,'UTF-8'));
        $data_post['TAX_STATUS'] =  strtoupper(htmlentities($this->input->post( 'TAX_STATUS' ),ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT'] = htmlentities($this->input->post( 'ALAMAT' ),ENT_QUOTES,'UTF-8');
        $data_post['PHONE'] = htmlentities($this->input->post( 'PHONE' ),ENT_QUOTES,'UTF-8');
        $data_post['NO_JAMSOSTEK'] = htmlentities($this->input->post( 'NO_JAMSOSTEK' ),ENT_QUOTES,'UTF-8');
		$data_post['NO_NPWP'] = htmlentities($this->input->post( 'NO_NPWP' ),ENT_QUOTES,'UTF-8');
		$data_post['DIVISION_CODE'] = strtoupper(htmlentities($this->input->post( 'DIVISION_CODE' ),ENT_QUOTES,'UTF-8'));
		$data_post['NO_IDENTITAS'] = htmlentities($this->input->post( 'NO_IDENTITAS' ),ENT_QUOTES,'UTF-8');
        $data_post['RELIGION'] = htmlentities($this->input->post( 'RELIGION' ),ENT_QUOTES,'UTF-8');
        $data_post['SEX'] = htmlentities($this->input->post( 'SEX' ),ENT_QUOTES,'UTF-8');
        $data_post['E_UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$data_post['E_UPDATE_DATE'] = date ("Y-m-d H:i:s");
        $data_post['COMPANY_CODE'] = htmlentities($this->input->post( 'COMPANY_CODE' ),ENT_QUOTES,'UTF-8');
        $data_post['INACTIVE']=htmlentities($this->input->post('INACTIVE'),ENT_QUOTES,'UTF-8');
		
		$data_post['NAMA_SEKOLAH']=htmlentities($this->input->post('NAMA_SEKOLAH'),ENT_QUOTES,'UTF-8');
		$data_post['JURUSAN']=htmlentities($this->input->post('JURUSAN'),ENT_QUOTES,'UTF-8');
		$data_post['ALAMAT_SEKOLAH']=htmlentities($this->input->post('ALAMAT_SEKOLAH'),ENT_QUOTES,'UTF-8');
		$data_post['ISBPJS_KETENAGAKERJAAN']=htmlentities($this->input->post('ISBPJS_KETENAGAKERJAAN'),ENT_QUOTES,'UTF-8');
		$data_post['NO_REG_BPJS_TNG']=htmlentities($this->input->post('NO_REG_BPJS_TNG'),ENT_QUOTES,'UTF-8');
		$data_post['ISBPJS_KESEHATAN']=htmlentities($this->input->post('ISBPJS_KESEHATAN'),ENT_QUOTES,'UTF-8');
		$data_post['NO_REG_BPJS_KES']=htmlentities($this->input->post('NO_REG_BPJS_KES'),ENT_QUOTES,'UTF-8');
		
        $data_post['NOTE'] =htmlentities($this->input->post('NOTE'),ENT_QUOTES,'UTF-8');
             
        if ($data_post['NAMA'] == ""){
                $status = "nama tidak boleh kosong";
                echo $status;
        } else if ( $data_post['TYPE_KARYAWAN'] == ""){
            $status = "type karyawan tidak boleh kosong";
            echo $status;
        } else if ( $data_post['FAMILY_STATUS'] == ""){
            $status = "status keluarga tidak boleh kosong";
            echo $status;
        } else if($data_post['DATE_JOIN']==""){
            $status="tanggal masuk tidak boleh kosong";
            echo $status;
        }
        
        if(empty($status)) {
            //############ otomatisasi perhitungan untuk naturav###########
            /* if (strtoupper($data_post['TYPE_KARYAWAN']) =="SKU" || strtoupper($data_post['TYPE_KARYAWAN'])=="BULANAN") {
                $natura=$this->model_m_employee->get_employee($nik,$company);
                if(count($natura)>0 && is_array($natura)) {
                    $famstat='';
                    foreach($natura as $row) {
                        $famstat= $row['FAMILY_STATUS'];
                    }
                    
                    if($data_post['FAMILY_STATUS']!=$famstat) {
                        $koef_natura=$this->model_m_natura->get_natura_koefisien($company,$data_post['FAMILY_STATUS']);
                        $koef_natura_val='0';
                        if(count($koef_natura)>0 && is_array($koef_natura))
                        {
                            foreach($koef_natura as $row) {
                                $koef_natura_val= $row['NATURA'];
                            } 
                        } 
                        
                        $data_natura = $this->model_m_natura->cek_natura_employee($nik,date("Ym",mktime()),$company);
                        if($data_natura>0)
                        {
                            
                            $postdata['NATURA']=$koef_natura_val;
                            $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                            $update = $this->model_m_natura->update_natura($nik,date("Ym",mktime()),$company,$postdata);
                        } else {
                           
                            $postdata['NIK']=$nik;
                            $postdata['NATURA']=$koef_natura_val;
                            $postdata['PERIODE']=date("Ym",mktime());
                            $postdata['COMPANY_CODE']=$company; 
                            $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                            $insert = $this->model_m_natura->insert_natura($postdata);
                        }
                    }   
                }    
            } */
            
            //#############################################################
            
            $update_id = $this->model_m_employee->update_m_employee($nik,$company, $data_post );
            $data_post['NIK'] = $this->input->post( 'NIK' ); 
            $data_post['Action']=$act;
            $data_post['E_INPUT_DATE']=date ("Y-m-d H:i:s");
             
            $insert_record = $this->model_m_employee->insert_record($data_post);  
            echo $update_id;  
        }
    }
    
    function mutasi()
    {
        $this->load->library('form_validation');   
        $tmpNik = htmlentities($this->input->post('TMP_NIK'),ENT_QUOTES,'UTF-8'); //nik lama
        $company =htmlentities($this->input->post( 'COMPANY_CODE' ),ENT_QUOTES,'UTF-8');
        //$nama = strtoupper(htmlentities($this->input->post( 'NAMA' )));
        //$type_karyawan = strtoupper(htmlentities($this->input->post( 'TYPE_KARYAWAN' )));
        //$status = strtoupper(htmlentities($this->input->post( 'FAMILY_STATUS' )));
        $nik = htmlentities($this->input->post( 'NIK' ));
        $act="mutasi";
        
        /* $data_post['NIK'] = htmlentities($this->input->post( 'NIK' ),ENT_QUOTES,'UTF-8'); //nik baru
        $data_post['NAMA'] = strtoupper(htmlentities($this->input->post( 'NAMA' ),ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_KARYAWAN'] = strtoupper(htmlentities($this->input->post( 'TYPE_KARYAWAN' ),ENT_QUOTES,'UTF-8'));
        $data_post['GP'] = htmlentities($this->input->post( 'GP' ),ENT_QUOTES,'UTF-8');
        $data_post['HK'] = htmlentities($this->input->post( 'GP' ),ENT_QUOTES,'UTF-8') / 25;
        $data_post['TANGGAL_LAHIR'] = htmlentities($this->input->post( 'TANGGAL_LAHIR' ),ENT_QUOTES,'UTF-8');
        $data_post['JABATAN'] = strtoupper(htmlentities($this->input->post( 'JABATAN' ),ENT_QUOTES,'UTF-8'));
        $data_post['COST_CENTER'] = strtoupper(htmlentities($this->input->post( 'COST_CENTER' ),ENT_QUOTES,'UTF-8'));
        $data_post['DEPT_CODE'] = strtoupper(htmlentities($this->input->post( 'DEPT_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['DIVISION_CODE'] = strtoupper(htmlentities($this->input->post( 'DIVISION_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['ESTATE_CODE'] = strtoupper(htmlentities($this->input->post( 'ESTATE_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['DATE_JOIN'] = htmlentities($this->input->post( 'DATE_JOIN' ),ENT_QUOTES,'UTF-8');
        $data_post['FAMILY_STATUS'] = strtoupper(htmlentities($this->input->post( 'FAMILY_STATUS' ),ENT_QUOTES,'UTF-8'));
        $data_post['TAX_STATUS'] =  strtoupper(htmlentities($this->input->post( 'TAX_STATUS' ),ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT'] = htmlentities($this->input->post( 'ALAMAT' ),ENT_QUOTES,'UTF-8');
        $data_post['PHONE'] = htmlentities($this->input->post( 'PHONE' ),ENT_QUOTES,'UTF-8');
        $data_post['NO_JAMSOSTEK'] = htmlentities($this->input->post( 'NO_JAMSOSTEK' ),ENT_QUOTES,'UTF-8');
        $data_post['JOB_FUNCTION'] = htmlentities($this->input->post( 'JOB_FUNCTION' ),ENT_QUOTES,'UTF-8');
        $data_post['RELIGION'] = htmlentities($this->input->post( 'RELIGION' ),ENT_QUOTES,'UTF-8');
        $data_post['SEX'] = htmlentities($this->input->post( 'SEX' ),ENT_QUOTES,'UTF-8');
        $data_post['E_INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		date ("Y-m-d H:i:s");
        $data_post['COMPANY_CODE'] = htmlentities($this->input->post( 'COMPANY_CODE' ),ENT_QUOTES,'UTF-8');
        $data_post['INACTIVE']=htmlentities($this->input->post('INACTIVE'),ENT_QUOTES,'UTF-8');
        $data_post['NOTE']=htmlentities($this->input->post('NOTE'),ENT_QUOTES,'UTF-8'); */
		
		$data_post['NIK'] = htmlentities($this->input->post( 'NIK' ),ENT_QUOTES,'UTF-8');
        $data_post['NAMA'] = strtoupper(htmlentities($this->input->post( 'NAMA' ),ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_KARYAWAN'] = strtoupper(htmlentities($this->input->post( 'TYPE_KARYAWAN' ),ENT_QUOTES,'UTF-8'));
        $data_post['GP'] = $this->input->post( 'GP' );
        $data_post['HK'] = $this->input->post( 'GP' ) / 25;
        $data_post['TANGGAL_LAHIR'] = htmlentities($this->input->post( 'TANGGAL_LAHIR' ),ENT_QUOTES,'UTF-8');
        $data_post['PANGKAT'] = strtoupper(htmlentities($this->input->post( 'PANGKAT' ),ENT_QUOTES,'UTF-8'));
		$data_post['JABATAN'] = strtoupper(htmlentities($this->input->post( 'JABATAN' ),ENT_QUOTES,'UTF-8'));
        $data_post['COST_CENTER'] = strtoupper(htmlentities($this->input->post( 'COST_CENTER' ),ENT_QUOTES,'UTF-8'));
        $data_post['DEPT_CODE'] = strtoupper(htmlentities($this->input->post( 'DEPT_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['ESTATE_CODE'] = strtoupper(htmlentities($this->input->post( 'ESTATE_CODE' ),ENT_QUOTES,'UTF-8'));
        $data_post['DATE_JOIN'] = htmlentities($this->input->post( 'DATE_JOIN' ),ENT_QUOTES,'UTF-8');
		$data_post['DATE_PROMOTION'] = htmlentities($this->input->post( 'DATE_PROMOTION' ),ENT_QUOTES,'UTF-8');
        $data_post['FAMILY_STATUS'] = strtoupper(htmlentities($this->input->post( 'FAMILY_STATUS' ),ENT_QUOTES,'UTF-8'));
		$data_post['LAST_EDUCATION'] = strtoupper(htmlentities($this->input->post( 'LAST_EDUCATION' ),ENT_QUOTES,'UTF-8'));
        $data_post['TAX_STATUS'] =  strtoupper(htmlentities($this->input->post( 'TAX_STATUS' ),ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT'] = htmlentities($this->input->post( 'ALAMAT' ),ENT_QUOTES,'UTF-8');
        $data_post['PHONE'] = htmlentities($this->input->post( 'PHONE' ),ENT_QUOTES,'UTF-8');
        $data_post['NO_JAMSOSTEK'] = htmlentities($this->input->post( 'NO_JAMSOSTEK' ),ENT_QUOTES,'UTF-8');
		$data_post['NO_NPWP'] = htmlentities($this->input->post( 'NO_NPWP' ),ENT_QUOTES,'UTF-8');
		$data_post['DIVISION_CODE'] = strtoupper(htmlentities($this->input->post( 'DIVISION_CODE' ),ENT_QUOTES,'UTF-8'));
		$data_post['NO_IDENTITAS'] = htmlentities($this->input->post( 'NO_IDENTITAS' ),ENT_QUOTES,'UTF-8');
        $data_post['RELIGION'] = htmlentities($this->input->post( 'RELIGION' ),ENT_QUOTES,'UTF-8');
        $data_post['SEX'] = htmlentities($this->input->post( 'SEX' ),ENT_QUOTES,'UTF-8');
        $data_post['E_INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$data_post['E_INPUT_DATE'] = date ("Y-m-d H:i:s");
        $data_post['COMPANY_CODE'] = htmlentities($this->input->post( 'COMPANY_CODE' ),ENT_QUOTES,'UTF-8');
        $data_post['INACTIVE']=htmlentities($this->input->post('INACTIVE'),ENT_QUOTES,'UTF-8');
        $data_post['NOTE'] =htmlentities($this->input->post('NOTE'),ENT_QUOTES,'UTF-8');
		
		$data_post['NAMA_SEKOLAH']=htmlentities($this->input->post('NAMA_SEKOLAH'),ENT_QUOTES,'UTF-8');
		$data_post['JURUSAN']=htmlentities($this->input->post('JURUSAN'),ENT_QUOTES,'UTF-8');
		$data_post['ALAMAT_SEKOLAH']=htmlentities($this->input->post('ALAMAT_SEKOLAH'),ENT_QUOTES,'UTF-8');
		$data_post['ISBPJS_KETENAGAKERJAAN']=htmlentities($this->input->post('ISBPJS_KETENAGAKERJAAN'),ENT_QUOTES,'UTF-8');
		$data_post['NO_REG_BPJS_TNG']=htmlentities($this->input->post('NO_REG_BPJS_TNG'),ENT_QUOTES,'UTF-8');
		$data_post['ISBPJS_KESEHATAN']=htmlentities($this->input->post('ISBPJS_KESEHATAN'),ENT_QUOTES,'UTF-8');
		$data_post['NO_REG_BPJS_KES']=htmlentities($this->input->post('NO_REG_BPJS_KES'),ENT_QUOTES,'UTF-8');
		
        //$status = "";
        $data_karyawan = $this->model_m_employee->cek_exist_employee($company,$nik,$data_post['TYPE_KARYAWAN']);
        $type_karyawan = $this->input->post('TYPE_KARYAWAN');
        
        if($data_karyawan > 0) { 
           $status = "data karyawan dengan NIK ". $nik . " dan type karyawan " . $type_karyawan . " sudah ada mohon periksa kembali!!!";
           echo $status;
        } else  {
            if ($data_post['NAMA'] == "") {
                $status = "nama tidak boleh kosong";
                echo $status;
            } else if ($data_post['TYPE_KARYAWAN'] == "") {
                $status = "type karyawan tidak boleh kosong";
                echo $status;
            } else if ($data_post['FAMILY_STATUS'] == "") {
                $status = "status keluarga tidak boleh kosong".$status;
                echo $status;
            } else {  
    //urutan mutasi
	//pindahkan data lama ke table m_employee_history -> hapus data lama di table m_employee -> insert data baru ->update track record. 
                    $delete_tmp=$this->model_m_employee->delete_m_employee($tmpNik, $company);
                    $insert_id = $this->model_m_employee->insert_m_employee( $data_post );
                    
                    $data_post['NIK']='';
                    $data_post['NIK']=$tmpNik;
                    $data_post['NIK_BARU'] = htmlentities($this->input->post( 'NIK' ),ENT_QUOTES,'UTF-8');
                    $insert_mutasi=$this->model_m_employee->insert_history($data_post,$tmpNik,$company); //masukkan data mutasi
                    unset($data_post['NIK_BARU']); 
                    $data_post['Action']=$act;
                    $data_post['E_INPUT_DATE']= date ("Y-m-d H:i:s"); 
                    $insert_history = $this->model_m_employee->insert_record($data_post);
            }
        }   
    }
	
    /* delete */
    function delete()
    {
        $id = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $delete=$this->model_m_employee->delete_m_employee($id, $company);
        return $delete;
    }
    
    function get_field_name()
    {
        $dropdownFN = "<select name='glob_search' class='select' id='glob_search'>
                        <option value=''> -- pilih -- </option>";
        
        $getFieldName = $this->model_m_employee->get_field_name("m_employee");
        
        foreach($getFieldName as $fieldName)
        {
            $dropdownFN = $dropdownFN."<option value=\"".$fieldName."\"  selected>".$fieldName." </option>";
        }
        $dropdownFN .="</select>";
                                  
        return $dropdownFN ;                
    }
	
    function data_sanitize($data)
    {
        $postArray = $data;
        $retValues='';
        if(is_array($data))
        {
            $array=array();
            foreach ($postArray as $key => $val)
            {
                $keys=htmlentities($key,ENT_QUOTES,'UTF-8');
                $vals=htmlentities($val,ENT_QUOTES,'UTF-8');
                $array[$keys]=$vals; 
            }
            $retValues=$array;   
        }
        else
        {
            $retValues=htmlentities($data,ENT_QUOTES,'UTF-8');   
        }
        return $retValues;
    }
    
    function date_now($option)
    {
        $date=getdate();
        $option = htmlentities($option,ENT_QUOTES,'UTF-8');
        if($option !="" && !empty($option))
        {
            if($option=="year"){
                $date=$date['year'];    
            }elseif($option=="month"){
                $date=$date['mon']; 
            }elseif($option=="day"){
                $date=$date['mday'];
            }else{
                $date=$date['year']."-".$date['mon']."-".$date['mday'];
            }
        }  
        return $date;
    }
	
	/*  ############ jabatan & dept ############ */
	function dropdownlist_dept()
	{
		$string = "<select  name='i_dept' class='select' id='i_dept' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_dept = $this->model_m_employee->get_dept();
		
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['DEPT_CODE']."\"  selected>".$row['DEPT_DESCRIPTION']." </option>";
			} else {
				$string = $string." <option value=\"".$row['DEPT_CODE']."\">".$row['DEPT_DESCRIPTION']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_level()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_pangkat' class='select' id='i_pangkat' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_level = $this->model_m_employee->get_level();
		
		foreach ( $data_level as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['EMP_LEVEL_ID']."\"  selected>".$row['EMP_LEVEL_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['EMP_LEVEL_ID']."\">".$row['EMP_LEVEL_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_education()
	{
		$string = "<select  name='i_education' class='select' id='i_education' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_dept = $this->model_m_employee->get_education();
		
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['ED_ID']."\"  selected>".$row['DESCRIPTION']." </option>";
			} else {
				$string = $string." <option value=\"".$row['ED_ID']."\">".$row['DESCRIPTION']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function LoadChain()
    {
        $dept = $this->uri->segment(4);
		$level = $this->uri->segment(3);
        $array=array();
        
		$data_pos = $this->model_m_employee->get_position($dept, $level);
        foreach ($data_pos as $drow){
			$array[] = array('kt' => $drow['POSITION_DESCRIPTION'], 'kt2' => $drow['EMP_POSITION_ID'] );
		}
        echo json_encode($array);
    }
	
	function dropdownlist_afd()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_afd' class='select' id='i_afd' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_m_employee->get_afd($company);
		
		foreach ( $data_afd as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_status($name)
	{
		$string = "<select  name='".$name."' class='select' id='".$name."' style='width:180px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_m_employee->get_famstat();
		
		foreach ( $data_afd as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['EMPLOYEE_FAMSTAT_CODE']."\"  selected>".$row['EMPLOYEE_FAMSTAT_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['EMPLOYEE_FAMSTAT_CODE']."\">".$row['EMPLOYEE_FAMSTAT_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_costcenter()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_cc' class='select' id='i_cc' style='width:200px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_m_employee->get_costcenter($company);
		
		foreach ( $data_afd as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['COSTCENTERCODE']."\"  selected>".$row['DESCR']." </option>";
			} else {
				$string = $string." <option value=\"".$row['COSTCENTERCODE']."\">".$row['DESCR']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
}

?>