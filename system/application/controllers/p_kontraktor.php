<?php
class p_kontraktor extends controller
{
    function __construct()
    {
        parent::Controller();    
		$this->load->model( 'model_p_kontraktor' ); 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="p_kontraktor";
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
    }
    
    function index()
    {
        $data = array();
        $view = "info_p_kontraktor";
        $data['judul_header'] = "Buku Catat Kontraktor";
        $data['js'] = "";
        $data['login_id'] = $this->session->userdata('LOGINID');
        $data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['periode'] = $this->global_func->drop_date('bulan','tahun','select','reloadGrid()');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
    
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
    
    function load_data()
    {
        $kode_kontraktor = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_kontraktor->load_data($kode_kontraktor, $periode, $company)); 
    }
    
    function load_kode_kontraktor ()
    {    
        $company = $this->session->userdata('DCOMPANY');
		$q = $_REQUEST['q'];
        $data_kontraktor=$this->model_p_kontraktor->load_kode_kontraktor($company, $q);
		$kontraktor = array();
        foreach($data_kontraktor as $row){
                $kontraktor[] = '{res_id:"'.str_replace('"','\\"',$row['KODE_KONTRAKTOR'])
                .'",res_name:"'.str_replace('"','\\"',$row['NAMA_KONTRAKTOR'])
				.'",isktbs:"'.str_replace('"','\\"',$row['IS_KONTRAKTOR_TBS'])
                .'",res_dl:"'.str_replace('"','\\"',$row['KODE_KONTRAKTOR']. "&nbsp; - &nbsp;" .$row['NAMA_KONTRAKTOR']).'"}';
        }
        echo '['.implode(',',$kontraktor).']'; 
		exit;
      	echo $data_kontraktor;    
    }
    
    function vehicle(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $inisial_kontraktor = htmlentities($this->uri->segment(3));
        $data_kendaraan = $this->model_p_kontraktor->get_vehicle($q,$inisial_kontraktor,$company);
        
        $kendaraan = array();
        foreach($data_kendaraan as $row){
                $kendaraan[] = '{res_id:"'.str_replace('"','\\"',$row['KODE_KONTRAKTOR'])
                .'",res_name:"'.str_replace('"','\\"',$row['NO_KENDARAAN'])
                .'",res_dl:"'.str_replace('"','\\"',$row['KODE_KONTRAKTOR']. "&nbsp; - &nbsp;" .$row['NO_KENDARAAN']).'"}';
        }
        echo '['.implode(',',$kendaraan).']'; exit;        
    }
     
    function activity()
    {
        $ac = $this->uri->segment(3);
        $q = $_REQUEST['q'];
		$lc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $data_enroll = $this->model_p_kontraktor->activity($ac, $q);
        
        $activity = array();
		if($ac == 'PJ') {
            $loc_ac = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');        
            $project_subtype = substr($lc,-2);
            
            $data_enroll = '';
            if($project_subtype == "TN" || $project_subtype == "LC") {
                $data_enroll = $this->model_p_kontraktor->activity_pj_lctn($loc_ac,$project_subtype);
            } else {
                $data_enroll = $this->model_p_kontraktor->activity_pj($lc, $company);
            }
            
            if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                    $activity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8')).
                    '",res_name:"'.str_replace('"','\\"',htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).
                    '",res_d:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8'). 
					'",res_sat1:"'.str_replace('"','\\"',htmlentities($row['UNIT1'],ENT_QUOTES,'UTF-8')).
					'",res_sat2:"'.str_replace('"','\\"',htmlentities($row['UNIT2'],ENT_QUOTES,'UTF-8')).
                    "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).'",}';
                }
                 echo '['.implode(',',$activity).']'; exit;    
            }
        } else {
			foreach($data_enroll as $row)
				{
					$activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).
					'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).
					'",res_sat1:"'.str_replace('"','\\"',htmlentities($row['UNIT1'],ENT_QUOTES,'UTF-8')).
					'",res_sat2:"'.str_replace('"','\\"',htmlentities($row['UNIT2'],ENT_QUOTES,'UTF-8')).
					'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
				}
			 echo '['.implode(',',$activity).']'; exit;
		}
    }
	
    function location(){
        $loc = $this->uri->segment(3);
        $company = $this->session->userdata('DCOMPANY');
        $q = $_REQUEST['q'];
		
		if($loc == 'PJ'){
        	$data_location = $this->model_p_kontraktor->location_pj($q,$company);
        } else {
        	$data_location = $this->model_p_kontraktor->location($loc, $q, $company);
        }
		 
        $data = array();
        $location = array();
        foreach($data_location as $row)
            {
                $location[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).
                '",res_name:"'.str_replace('"','\\"',$row['DESCRIPTION']).
                '",res_dl:"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
            }
          echo '['.implode(',',$location).']'; exit;
    }

    function create(){
      $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $kode='';
      $inv_id = trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8')," ") ;
		
	  $periode = substr( str_replace("-","",$this->input->post('TGL_KONTRAK')),0,6);												  	  $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
	  $close = $this->global_func->cekClosing($periode, $company);
			
	  if($close == '1'){
		echo "Periode transaksi bulan ini sudah diclose..";
	  } else {
			
		$datainv_id = $this->model_p_kontraktor->reverse_kode_kontraktor($company, $inv_id);
		foreach($datainv_id as $row){
			   $inv_id = $row['KODE_KONTRAKTOR'];
		}
			
		$data_post['ID_KONTRAK']= htmlentities($this->global_func->id_BK('p_kontraktor','ID_KONTRAK', $kode ),ENT_QUOTES,'UTF-8'); 
		$data_post['TGL_KONTRAK'] =htmlentities($this->input->post('TGL_KONTRAK'),ENT_QUOTES,'UTF-8'); 
		$data_post['NO_KENDARAAN'] =htmlentities($this->input->post('NO_KENDARAAN'),ENT_QUOTES,'UTF-8'); 
		$data_post['ID_KONTRAKTOR']= $inv_id;  //25 varchar
		$data_post['LOCATION_TYPE_CODE'] =htmlentities($this->input->post('LOCATION_TYPE_CODE'),ENT_QUOTES,'UTF-8'); 
		$data_post['LOCATION_CODE']=htmlentities($this->input->post('LOCATION_CODE'),ENT_QUOTES,'UTF-8'); 
		$data_post['LOCATION_DESC']=htmlentities($this->input->post('LOCATION_DESC'),ENT_QUOTES,'UTF-8'); 
		$data_post['ACTIVITY_CODE']=htmlentities($this->input->post('ACTIVITY_CODE'),ENT_QUOTES,'UTF-8'); 
		$data_post['ACTIVITY_DESC']=htmlentities($this->input->post('ACTIVITY_DESC'),ENT_QUOTES,'UTF-8'); 
		$data_post['HSL_SATUAN']=htmlentities($this->input->post('HSL_SATUAN'),ENT_QUOTES,'UTF-8'); 
		$data_post['HSL_VOLUME']=$this->input->post('HSL_VOLUME');
		$data_post['HSL_SATUAN2']=htmlentities($this->input->post('HSL_SATUAN2'),ENT_QUOTES,'UTF-8'); 
		$data_post['HSL_VOLUME2']=$this->input->post('HSL_VOLUME2');
		$data_post['MUATAN']=htmlentities($this->input->post('MUATAN'),ENT_QUOTES,'UTF-8'); 
		$data_post['JARAK']=htmlentities($this->input->post('JARAK'),ENT_QUOTES,'UTF-8'); 
		$data_post['TARIF_SATUAN']=htmlentities($this->input->post('TARIF_SATUAN'),ENT_QUOTES,'UTF-8');  
		$data_post['NILAI']=htmlentities($this->input->post('HSL_VOLUME'),ENT_QUOTES,'UTF-8') * htmlentities($this->input->post('TARIF_SATUAN'),ENT_QUOTES,'UTF-8');
		$data_post['COMPANY_CODE'] =$company;  
		$data_post['INPUT_BY'] = $this->session->userdata('LOGINID'); 
		$data_post['INPUT_DATE'] = date ("Y-m-d H:i:s");
			
		if(strlen($data_post['TGL_KONTRAK']) > 20){
		 	$status = "mohon tutup kotak tanggal aktivitas yang terbuka  \r\n"; 
		 	echo $status;
		} else if(strlen($data_post['NO_KENDARAAN']) > 20){
			$status = "mohon tutup kotak  NO KENDARAAN yang terbuka  \r\n"; 
		  	echo $status;
		}else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
			$status = "mohon tutup kotak  kode tipe lokasi yang terbuka  \r\n"; 
			echo $status;
		} else if(strlen($data_post['LOCATION_CODE']) > 20){
			$status = "mohon tutup kotak kode lokasi yang terbuka  \r\n"; 
			echo $status;
		} else if(strlen($data_post['ACTIVITY_CODE']) > 20){
			$status = "mohon tutup kotak  kode aktivitas yang terbuka  \r\n"; 
			echo $status;
		}  else if(strlen($data_post['HSL_SATUAN']) > 20){
			$status = "mohon tutup kotak hasil satuan yang terbuka  \r\n"; 
			echo $status;
		}  else if(strlen($data_post['HSL_VOLUME']) > 20){
			$status = "mohon tutup kotak hasil volume yang terbuka  \r\n"; 
			echo $status;
		} else if(strlen($data_post['TARIF_SATUAN']) > 20){
			$status = "mohon tutup kotak tarif satuan yang terbuka  \r\n"; 
			echo $status;
		} else if(strlen($data_post['NILAI']) > 20){
			$status="mohon tutup kotak nilai yg terbuka \r\n";
			echo $status;
		}
		$tgl = $data_post['TGL_KONTRAK'];
		$ltc = $data_post['LOCATION_TYPE_CODE'];
		$lc = $data_post['LOCATION_CODE'];
		$ac = $data_post['ACTIVITY_CODE'];
		$data_kontraktor=$this->model_p_kontraktor->load_kode_kontraktor($company, trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8')," "));
		
		$project_subtype = substr($lc,-2);
		if($ltc == "PJ"){
			if($project_subtype == "TN" || $project_subtype == "LC") {
			  $data_aktivitas = $this->model_p_kontraktor->projectlctn_activity_validate($project_subtype,$ac);
			  $data_lokasi = $this->model_p_kontraktor->lokasi_project_validate($lc, $company);    
			} else {
			  	$data_aktivitas = $this->model_p_kontraktor->project_activity_validate($lc,$ac, $company);
				$data_lokasi = $this->model_p_kontraktor->lokasi_project_validate($lc, $company);
			} 
		} else {
			$data_lokasi = $this->model_p_kontraktor->lokasi_validate($lc, $ltc, $company);    
			$data_aktivitas = $this->model_p_kontraktor->aktivitas_validate($ac, $ltc);
		} 
		
		/* validasi */
		if(count($data_kontraktor) == 0){
			$status = "Kode kontraktor tidak terdapat dalam sistem!!\r\n"; 
			echo $status;
		}	 
		
		if($tgl == "" || $ltc == "" || $lc == "" || $ac == ""){
			$status = "Tanggal, Tipe Lokasi, Lokasi, serta aktivitas tidak boleh kosong!!\r\n"; 
			echo $status;
		}
		
		if(count($data_lokasi) == 0){
			$status = "Kode lokasi salah / tidak terdapat dalam sistem!!\r\n"; 
			echo $status;
		}
		
		if(count($data_aktivitas) == 0){
			$status = "Kode aktivitas salah / tidak terdapat dalam sistem!!\r\n"; 
			echo $status;
		}
			 		   
		if(empty($status)){
			$numeric_data=array('HSL_VOLUME'=>htmlentities($this->input->post('HSL_VOLUME')),
								'HSL_VOLUME2'=>htmlentities($this->input->post('HSL_VOLUME2')),
								'TARIF_SATUAN'=>htmlentities($this->input->post('TARIF_SATUAN')),
								'NILAI'=>htmlentities($this->input->post('NILAI')));
			$validate_numeric=$this->validate_numeric($numeric_data);
			if( strtolower($validate_numeric)!='false'){
				$insert_id = $this->model_p_kontraktor->insert_new_kontraktor($data_post); 
				echo  $insert_id;
			}else{
				echo "input pada HSL_VOLUME,TARIF_SATUAN,NILAI harus angka";
		  }
		} 
	  }
    }
    
    function update()
    {
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id= htmlentities($this->input->post('ID_KONTRAK'),ENT_QUOTES,'UTF-8');
        $inv_id = htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8') ;
			
		$periode = substr( str_replace("-","",$this->input->post('TGL_KONTRAK')),0,6);
															  
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$close = $this->global_func->cekClosing($periode, $company);
			
		if($close == '1'){
			echo "Periode transaksi bulan ini sudah diclose..";
		} else {
			
			$datainv_id = $this->model_p_kontraktor->reverse_kode_kontraktor($company, $inv_id);
			foreach($datainv_id as $row){
			   $inv_id = $row['KODE_KONTRAKTOR'];
			}
			
			
            $data_post['TGL_KONTRAK'] =htmlentities($this->input->post('TGL_KONTRAK'),ENT_QUOTES,'UTF-8'); 
            $data_post['NO_KENDARAAN'] =htmlentities($this->input->post('NO_KENDARAAN'),ENT_QUOTES,'UTF-8');   
            $data_post['LOCATION_TYPE_CODE'] =htmlentities($this->input->post('LOCATION_TYPE_CODE'),ENT_QUOTES,'UTF-8');  
            $data_post['LOCATION_CODE']=htmlentities($this->input->post('LOCATION_CODE'),ENT_QUOTES,'UTF-8');  
            $data_post['LOCATION_DESC']=htmlentities($this->input->post('LOCATION_DESC'),ENT_QUOTES,'UTF-8');  
            $data_post['ACTIVITY_CODE']=htmlentities($this->input->post('ACTIVITY_CODE'),ENT_QUOTES,'UTF-8');  
            $data_post['ACTIVITY_DESC']=htmlentities($this->input->post('ACTIVITY_DESC'),ENT_QUOTES,'UTF-8');  
			$data_post['MUATAN']=htmlentities($this->input->post('MUATAN'),ENT_QUOTES,'UTF-8'); 
			$data_post['JARAK']=htmlentities($this->input->post('JARAK'),ENT_QUOTES,'UTF-8'); 
            $data_post['HSL_SATUAN']=htmlentities($this->input->post('HSL_SATUAN'),ENT_QUOTES,'UTF-8');  
            $data_post['HSL_VOLUME']=$this->input->post('HSL_VOLUME'); //10,2 decimal  
			$data_post['HSL_SATUAN2']=htmlentities($this->input->post('HSL_SATUAN2'),ENT_QUOTES,'UTF-8');  
            $data_post['HSL_VOLUME2']=$this->input->post('HSL_VOLUME2'); //10,2 decimal  
            $data_post['TARIF_SATUAN']=htmlentities($this->input->post('TARIF_SATUAN'),ENT_QUOTES,'UTF-8');  
            $data_post['NILAI']=htmlentities($this->input->post('HSL_VOLUME'),ENT_QUOTES,'UTF-8') * htmlentities($this->input->post('TARIF_SATUAN'),ENT_QUOTES,'UTF-8');  
			$data_post['UPDATE_BY'] = $this->session->userdata('LOGINID'); 
			$data_post['UPDATE_DATE'] = date ("Y-m-d H:i:s");

            if(strlen($data_post['TGL_KONTRAK']) > 20){
                $status = "mohon tutup kotak tanggal aktivitas yang terbuka  \r\n"; 
                echo $status;
            } else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
                $status = "mohon tutup kotak  kode tipe lokasi yang terbuka  \r\n"; 
                echo $status;
            } else if(strlen($data_post['LOCATION_CODE']) > 20){
                $status = "mohon tutup kotak kode lokasi yang terbuka  \r\n"; 
                echo $status;
            } else if(strlen($data_post['ACTIVITY_CODE']) > 20){
                $status = "mohon tutup kotak  kode aktivitas yang terbuka  \r\n"; 
                echo $status;
            }  else if(strlen($data_post['HSL_SATUAN']) > 20){
                $status = "mohon tutup kotak hasil satuan yang terbuka  \r\n"; 
                echo $status;
            }  else if(strlen($data_post['HSL_VOLUME']) > 20){
                $status = "mohon tutup kotak hasil volume yang terbuka  \r\n"; 
                echo $status;
            } else if(strlen($data_post['HSL_VOLUME2']) > 20){
                $status = "mohon tutup kotak hasil volume 2 yang terbuka  \r\n"; 
                echo $status;
            }  else if(strlen($data_post['TARIF_SATUAN']) > 20){
                $status = "mohon tutup kotak tarif satuan yang terbuka  \r\n"; 
                echo $status;
            } else if(strlen($data_post['NILAI']) > 20){
                $status="mohon tutup kotak nilai yg terbuka \r\n";
                echo $status;
            }
            
			$tgl = $data_post['TGL_KONTRAK'];
			$ltc = $data_post['LOCATION_TYPE_CODE'];
			$lc = $data_post['LOCATION_CODE'];
			$ac = $data_post['ACTIVITY_CODE'];
			$data_kontraktor=$this->model_p_kontraktor->load_kode_kontraktor($company, trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8')," "));
			
			$project_subtype = substr($lc,-2);
			if($ltc == "PJ"){
				if($project_subtype == "TN" || $project_subtype == "LC") {
				  $data_aktivitas = $this->model_p_kontraktor->projectlctn_activity_validate($project_subtype,$ac);
				  $data_lokasi = $this->model_p_kontraktor->lokasi_project_validate($lc, $company);    
				} else {
					$data_aktivitas = $this->model_p_kontraktor->project_activity_validate($lc,$ac, $company);
					$data_lokasi = $this->model_p_kontraktor->lokasi_project_validate($lc, $company);
				} 
			} else {
				$data_lokasi = $this->model_p_kontraktor->lokasi_validate($lc, $ltc, $company);    
				$data_aktivitas = $this->model_p_kontraktor->aktivitas_validate($ac, $ltc);
			} 
			
			/* validasi */
			if(count($data_kontraktor) == 0){
				$status = "Kode kontraktor tidak terdapat dalam sistem!!\r\n"; 
				echo $status;
			}	 
			
			if($tgl == "" || $ltc == "" || $lc == "" || $ac == ""){
				$status = "Tanggal, Tipe Lokasi, Lokasi, serta aktivitas tidak boleh kosong!!\r\n"; 
				echo $status;
			}
			
			if(count($data_lokasi) == 0){
				$status = "Kode lokasi salah / tidak terdapat dalam sistem!!\r\n"; 
				echo $status;
			}
			
			if(count($data_aktivitas) == 0){
				$status = "Kode aktivitas salah / tidak terdapat dalam sistem!!\r\n"; 
				echo $status;
			}
            
        if(empty($status)){
            $numeric_data=array('HSL_VOLUME'=>htmlentities($this->input->post('HSL_VOLUME')),
								'HSL_VOLUME2'=>htmlentities($this->input->post('HSL_VOLUME2')),
                                'TARIF_SATUAN'=>htmlentities($this->input->post('TARIF_SATUAN')),
                                'NILAI'=>htmlentities($this->input->post('NILAI')));
            $validate_numeric=$this->validate_numeric($numeric_data);
            if( strtolower($validate_numeric)!='false')
            {
                $insert_id = $this->model_p_kontraktor->update_data($data_post,$id,$inv_id,$company);
                
                echo $insert_id;
            }else{
                echo "input pada HSL_VOLUME,TARIF_SATUAN,NILAI harus angka";
            }
		}
      }
    }
    
    function delete()
    {
        $id = mysql_escape_string(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $kode = mysql_escape_string(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $company = mysql_escape_string(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $this->model_p_kontraktor->delete_data($id,$kode, $company);
    }
    
    //################# validation ########################
    function validate_numeric($data)
    {
        $numeric=$data;
        $result='';
        if(is_array($data)){
            while(list($key,$val)=each($data)){
                if(trim($val)=="" || $val==null) {
                    $val=0;
                }
                if((! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$val))){
                    $result='false';
                    break;
                }else{
                    $result='true';   
                }
            }
        } else {
            if(trim($numeric)=="" || $numeric==null){
                $val=0;
            }
            
            if (! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$numeric)){
                $result='false';   
            } else {
                $result='true';
            }    
        }
        return $result;   
    }
    
	 //satuan
    function satuan(){
        $data_satuan = $this->model_p_kontraktor->satuan();

        if(is_array($data_satuan))
        {
            $satuan = array();
            foreach($data_satuan as $row)
            {
                $satuan[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['UNIT_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['UNIT_DESC'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['UNIT_CODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['UNIT_DESC'],ENT_QUOTES,'UTF-8')).'"}';
            }
            echo '['.implode(',',$satuan).']'; exit;   
        }  
    }
	
	//jenis muatan
    function muatan(){
		$q = $_REQUEST['q'];
        $data_muatan = $this->model_p_kontraktor->muatan($q);
       
        if(is_array($data_muatan)){
            $muatan = array();
            foreach($data_muatan as $row){
                $muatan[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['KODE_MUATAN'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NAMA_MUATAN'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['KODE_MUATAN'],ENT_QUOTES,'UTF-8'). 
                "&nbsp; - &nbsp;" .htmlentities($row['NAMA_MUATAN'],ENT_QUOTES,'UTF-8')).
                '", res_sat:"'.htmlentities($row['SATUAN']).'"}';
            }
            echo '['.implode(',',$muatan).']'; exit;    
        }
         
    }
	
    function validate_activity($input)
    {
        //cek tipe data
        //cek panjang karakter
        //buang karakter yg tidak diinginkan   
    }
	
	/* ### Start Pengisian Material ### */
    function read_material()
    {
        $tdate =htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $gc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$inv_id ="";
		$datainv_id = $this->model_p_kontraktor->reverse_kode_kontraktor($company, $gc);
		foreach($datainv_id as $row){
			   $inv_id = $row['KODE_KONTRAKTOR'];
		}
       
        echo json_encode($this->model_p_kontraktor->get_material($tdate, $inv_id, $company));
    }
    
    function submit_material()
    {
		$err_status='';
        $company = htmlentities(trim($this->session->userdata('DCOMPANY')));
        $mode = htmlentities($this->input->post('MODE'));
		$activity = htmlentities($this->input->post('ACTIVITY_CODE'));
		$location = htmlentities($this->input->post('LOCATION_CODE'));
		$date = htmlentities($this->input->post('TGL_AKTIVITAS'));
		$gangcode = htmlentities($this->input->post('KODE_KONTRAKTOR'));
		$mc = htmlentities($this->input->post('MATERIAL_CODE'));
		
		$inv_id = "";
		$datainv_id = $this->model_p_kontraktor->reverse_kode_kontraktor($company, $gangcode);
		foreach($datainv_id as $row){
			   $inv_id = $row['KODE_KONTRAKTOR'];
		}
		$data_post['KODE_KONTRAKTOR']= $inv_id;
        $data_post['TGL_AKTIVITAS']= $date;
        $data_post['ACTIVITY_CODE']=$activity;
        $data_post['LOCATION_CODE']=$location;
		$data_post['MATERIAL_QTY']=htmlentities($this->input->post('MATERIAL_QUANTITY'));
        $data_post['MATERIAL_CODE']=$mc;
		$data_post['MATERIAL_SKB_NO']=htmlentities($this->input->post('MATERIAL_SKB_NO'));
		$data_post['MATERIAL_BPB_NO']=htmlentities($this->input->post('MATERIAL_BPB_NO'));
        $data_post['COMPANY_CODE']=$company;
		
		$cekAktivitas = $this->valActMat($gangcode, $date, $activity);
		if($cekAktivitas < 1)
		{
			$err_status= "Kode aktivitas tidak terdapat pada transaksi tanggal " . $date;
			echo $err_status ;
			//break;    
		}
		
		$cekLokasi = $this->valLocMat($gangcode, $date, $activity, $location);
		if($cekLokasi < 1)
		{
			$err_status= "Kode lokasi tidak terdapat pada transaksi tanggal " . $date . " dan aktivitas ". $activity;
			echo $err_status ;
			//break;    
		}
		
		if (empty($err_status) || $err_status=='')
        {
			if($mode == "POST"){
				$data_post['INPUT_BY']=htmlentities($this->session->userdata('LOGINID'));
				$data_post['INPUT_DATE']=date ("Y-m-d H:i:s");
				$data_exist = $this->model_p_kontraktor->cek_exist_data($inv_id,$date,$mc,$activity,$location,$company); 
					if($data_exist > 0) {
						echo("data material yang ke lokasi & aktivitas tersebut sudah ada di dalam database, \n
							  untuk mengubah silakan gunakan tombol ubah.."); 
					} else {
						$update_data=$this->model_p_kontraktor->insert_material($data_post);
						echo "0"; 					
					}
			} else if ( $mode == "GET"){
				$matid = htmlentities($this->input->post('BKT_MATERIAL_ID'));
				$data_post['UPDATE_BY']=htmlentities($this->session->userdata('LOGINID'));
				$data_post['UPDATE_DATE']=date ("Y-m-d H:i:s");
				$update_data=$this->model_p_kontraktor->update_material($matid, $inv_id, $date, $activity, $location, $company,$data_post);
				echo "0";  
			}
		}
    }
    
    function delete_material(){ 
        $company = htmlentities($this->session->userdata('DCOMPANY'));
       	$idp = htmlentities($this->input->post('BKT_MATERIAL_ID'));
		$act = htmlentities($this->input->post('ACTIVITY_CODE'));
		$lc = htmlentities($this->input->post('LOCATION_CODE'));
		$tgl = htmlentities($this->input->post('MAT_TGL_ACTIVITY'));
		$gc = htmlentities($this->input->post('KODE_KONTRAKTOR'));
		$mc = htmlentities($this->input->post('MATERIAL_CODE'));		
        
        $this->model_p_kontraktor->delete_material($gc, $idp, $tgl, $act, $mc, $lc, $company);
    }
	
	function getActMaterial(){
		$q = $_REQUEST["q"]; 
		$gc = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
		$data_act = $this->model_p_kontraktor->mgetActMaterial($gc,$tgl,$q);
		$aktivitas = array();
		foreach($data_act as $row) {
			$aktivitas[] = '{"res_id":"'.str_replace('"','\\"',$row['ACTIVITY_CODE']).'","res_name":"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'","res_dl":"'.str_replace('"','\\"',$row['ACTIVITY_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'"}';
		}
		echo '['.implode(',',$aktivitas).']'; exit; 
	}
	
	function getLocMaterial(){
		$q = $_REQUEST["q"]; 
		$gc = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
		$act = $this->uri->segment(5);
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data_act = $this->model_p_kontraktor->mgetLocMaterial($gc,$tgl,$act,$q);
		$aktivitas = array();
		foreach($data_act as $row) {
			$aktivitas[] = '{"res_id":"'.str_replace('"','\\"',$row['LOCATION_CODE']).'","res_name":"'.str_replace('"','\\"',$row['LOCATION_CODE']).'","res_dl":"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['LOCATION_CODE']).'"}';
		}
		echo '['.implode(',',$aktivitas).']'; exit; 
	}
	
	function getMaterial(){
		$q = $_REQUEST["q"]; 
		$data_act = $this->model_p_kontraktor->mgetMaterial($q);
		$aktivitas = array();
		foreach($data_act as $row) {
			$aktivitas[] = '{"res_id":"'.str_replace('"','\\"',$row['MATERIAL_CODE']).'","res_name":"'.str_replace('"','\\"',$row['MATERIAL_NAME']).'","res_dl":"'.str_replace('"','\\"',$row['MATERIAL_CODE']. "&nbsp; - &nbsp;" .$row['MATERIAL_NAME']).'","uom":"'.str_replace('"','\\"',$row['MATERIAL_UOM']).'"}';
		}
		echo '['.implode(',',$aktivitas).']'; exit; 
	}
	
	function valActMat($KodeKontraktor, $tgl, $q){
		
		$data_act = $this->model_p_kontraktor->valActMaterial($KodeKontraktor,$tgl,$q);
		return $data_act; 
	}
	
	function valLocMat($KodeKontraktor,$tgl,$act,$q) {
		$data_act = $this->model_p_kontraktor->valLocMaterial($KodeKontraktor,$tgl,$act,$q);
		return $data_act; 
	}    
}

?>
