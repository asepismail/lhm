<?php
class s_nota_angkut_kebun extends Controller
{
    private $lastmenu; 
    function __construct()
    {
        parent::__construct();
        $this->load->model('model_s_nota_angkut_kebun');
		$this->load->model('model_m_bjr');
		$this->load->model('Model_m_company');
        $this->load->model('model_c_user_auth');   
        
        $this->load->library('form_validation');
        require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
        
        $this->lastmenu="s_nota_angkut_kebun";   
    }
    
    function index()
    {
        //$this->output->cache(3);
        $view="info_s_nota_angkut_kebun";
        
        $data = array();
        $data['judul_header'] = "Nota Angkut Buah";
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
    
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_nota_angkut_kebun->data_search($data['rules'], $company));    
    }
    
    function LoadData(){
        //$opt= htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        //$no_tiket = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_nota_angkut_kebun->LoadData($company,$periode));   
    }
    
    function LoadData_Detail(){
        //$no_tiket = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $id_nota = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_nota_angkut_kebun->LoadData_Detail($company,$id_nota));   
    }
    
    function get_afdeling(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_s_nota_angkut_kebun->get_afdeling($company,$q);
         
        //echo $q;
        $afdeling = array();
        foreach($data_afd as $row)
        {
            $afdeling[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).
            '",res_name:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).
            '",res_dl:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$afdeling).']'; exit;     
    }
    
    function get_block(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); 
        $location_left = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_s_nota_angkut_kebun->get_block($company,$location_left,$q);
         
        //echo $q;
        $block = array();
        foreach($data_afd as $row)
        {
            $block[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['DESCRIPTION'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$block).']'; exit;     
    }
    
    function CRUD_METHOD(){
        $loginid=trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_id = $data["id"];
        
        if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADD"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->create_nota();    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_nota();    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
                    
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_nota($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "PRINT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"PRINT",$loginid);
            if($is_auth_user_command['0']['ROLE_REPORT']=='1'){
                $print_type = $this->uri->segment('3');
                $this->print_nota($data_id,$print_type);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DELD"){
			$data_detail=array(); //Added By Asep, 20130524
	        $data_detail = $data["detail"]; //Added By Asep, 20130524
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_notadetail($data_detail);  //Modified by Asep: data_id to data_detail, 20130524 
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDITD"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_nota_detail($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }else{
            $return['status'] ="Operation Unknown !!";
            $return['error']=true;
            echo json_encode($return);
        }      
    }
    
    /*############################# START PRINTING FUNCTION ##################################
    ####################################################################################*/
    function print_nota($data_id,$print_type){
        $return['status']='';
        $return['error']=false;
        
        $id_nota = trim(htmlentities($data_id['ID_NT_AB'],ENT_QUOTES,'UTF-8'));
        $plat_no = trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'));
        $no_spb = trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
 
        if (empty($id_nota) || trim($id_nota)=='' || $id_nota==false){
            $status = "ID_NOTA KOSONG !!"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($id_nota) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']==false){     
            if (trim(strtoupper($print_type))=="PDF"){
                $prints=$this->print_pdf($data_id);
                $return['status'] =$prints;
                $return['error']=false;
                        
            }elseif(trim(strtoupper($print_type))=="XLS"){
                $return['error']=false; 
                $this->print_xls($data_id);
                    
            }else{
                $return['status'] ="Operation Unknown !!";
                $return['error']=true;    
            }
            echo json_encode($return);              
        }else{
            echo json_encode($return);
        }    
    }
    
    function print_pdf($data_id){

        //$pdffile=basename(tempnam(getcwd(),'tmp'));
        $pdf = new pdf_usage();       
        $pdf->Open();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(5, 13,20);
        $pdf->AddPage('L', 'LEGAL');
        $pdf->AliasNbPages(); 
            
        $pdf->SetStyle("s1","arial","",9,"");
        $pdf->SetStyle("s2","arial","",8,"");
        $pdf->SetStyle("s3","arial","",10,"");
        
        $pdf->SetTextColor(118, 0, 3);
        //$pdf->SetX(60);
        //$pdf->Ln(1);
        //$pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
        
        $pdf->Ln(2);
        
        //load the table default definitions DEFAULT!!!
        require_once(APPPATH . 'libraries/rptPDF_def.inc'); 
        $columns = 9; //number of Columns
        
        //Initialize the table class
        $pdf->tbInitialize($columns, true, true);
        
        //set the Table Type
        $pdf->tbSetTableType($table_default_table_type);
        $aSimpleHeader = array();
        
        $header = array('No','LOCATION CODE','ACTIVITY CODE', 'LHM','','NOTA ANGKUT','', 'RESTAN','');
        $header2 = array('','','','JANJANG PANEN','BERAT PANEN (Kg)','JANJANG PANEN','BERAT PANEN (Kg)','JANJANG PANEN','BERAT PANEN (Kg)');
        //Table Header
        for($i=0; $i < $columns; $i++) {
            $aSimpleHeader[$i] = $table_default_header_type;
            $aSimpleHeader[$i]['TEXT'] = $header[$i];
            $aSimpleHeader[0]['WIDTH'] = 7.5;
            $aSimpleHeader[1]['WIDTH'] = 30;
            $aSimpleHeader[2]['WIDTH'] = 30;
            $aSimpleHeader[3]['WIDTH'] = 30;
            $aSimpleHeader[4]['WIDTH'] = 30;
            $aSimpleHeader[5]['WIDTH'] = 30;
            $aSimpleHeader[6]['WIDTH'] = 30;
            $aSimpleHeader[7]['WIDTH'] = 30;
            $aSimpleHeader[8]['WIDTH'] = 30;
            
            $aSimpleHeader[$i]['LN_SIZE'] = 5;
            $aSimpleHeader[3]['COLSPAN'] = 2;
            $aSimpleHeader[5]['COLSPAN'] = 2;
            $aSimpleHeader[7]['COLSPAN'] = 2;
            $aSimpleHeader[0]['ROWSPAN'] = 2;
            $aSimpleHeader[1]['ROWSPAN'] = 2;
            $aSimpleHeader[2]['ROWSPAN'] = 2;
             
            
            $aSimpleHeader2[$i] = $table_default_header_type;
            $aSimpleHeader2[$i]['TEXT'] = $header2[$i];
            $aSimpleHeader2[0]['WIDTH'] = 7.5;
            $aSimpleHeader2[1]['WIDTH'] = 30;
            $aSimpleHeader2[2]['WIDTH'] = 30;
            $aSimpleHeader2[3]['WIDTH'] = 30;
            $aSimpleHeader2[4]['WIDTH'] = 30;
            $aSimpleHeader2[5]['WIDTH'] = 30;
            $aSimpleHeader2[6]['WIDTH'] = 30;
            $aSimpleHeader2[7]['WIDTH'] = 30; 
            $aSimpleHeader2[8]['WIDTH'] = 30;
            $aSimpleHeader2[$i]['LN_SIZE'] = 5; 
        }
        
        $pdf->tbSetHeaderType($aSimpleHeader);
        $pdf->tbSetHeaderType($aSimpleHeader2);
        //Draw the Header
        $pdf->tbDrawHeader();

        /*//Table Data Settings
        $aDataType = Array();
        for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
        $pdf->tbSetDataType($aDataType);
                
        $total_jjg_lhm=0;
        $total_berat_lhm=0;
        $total_jjg_nab=0;
        $total_berat_nab=0;
        $total_jjg_restan=0;
        $total_berat_restan=0;
        $data_panen=$this->model_s_analisa_panen->generate_lhm_nab($ar,$company);
        $i = 1;    
        foreach ($data_panen as $row)
        {
            $data = Array();
            $data[0]['TEXT'] = $i;
            $data[1]['TEXT'] = $row['LOCATION_CODE'];
            $data[2]['TEXT'] = $row['ACTIVITY_CODE'];
            $data[3]['TEXT'] = $row['JANJANG_PANEN'];
            $data[4]['TEXT'] = $row['BERAT_PANEN'];        
            $data[5]['TEXT'] = $row['JANJANG_NAB'];
            $data[6]['TEXT'] = $row['BERAT_REAL'];
            $restan_jjg = $row['JANJANG_PANEN']-$row['JANJANG_NAB'];
            $restan_wb = $row['BERAT_PANEN']-$row['BERAT_REAL'];
            
            $data[7]['TEXT'] = $restan_jjg;
            $data[8]['TEXT'] = $restan_wb;
            
            $total_jjg_lhm+=$row['JANJANG_PANEN'];
            $total_berat_lhm+=$row['BERAT_PANEN'];
            $total_jjg_nab+=$row['JANJANG_NAB'];
            $total_berat_nab+=$row['BERAT_REAL']; 
            $total_jjg_restan+=$restan_jjg;
            $total_berat_restan+=$restan_wb;
            $i++;
                
            $pdf->tbDrawData($data);
        }
        $data[0]['TEXT'] = "Total";
        $data[0]['COLSPAN'] =3;            
        $data[3]['TEXT'] = number_format($total_jjg_lhm,2,'.',',');
        $data[4]['TEXT'] = number_format($total_berat_lhm,2,'.',',');
        $data[5]['TEXT'] = number_format($total_jjg_nab,2,'.',',');
        $data[6]['TEXT'] = number_format($total_berat_nab,2,'.',',');
        $data[7]['TEXT'] = number_format($total_jjg_restan,2,'.',',');
        $data[8]['TEXT'] = number_format($total_berat_restan,2,'.',',');
        $pdf->tbDrawData($data);
        */            
        $pdf->tbOuputData();
        $pdf->tbDrawBorder();
        
                            
        $pdf->Ln(15.5); 
        
        //require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
        //rename($pdffile,$pdffile.'.pdf');
        return $pdf->Output('', 'I');
         
    }
    function print_xls($data_id){
        
    }
    /*############################# END PRINTING FUNCTION ##################################
    ####################################################################################*/
    
    function create_nota(){
		$total_janjang = 0; //Added By Asep, 20130506
		$janjang = 0 ; //Added by Asep
		$total_empiris = 0; //added by Asep
        $return['status']='';
        $return['error']=false;
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_detail = array();
        $tmp_afd='';
        $tmp_identifier='';
		$data_post_d = array();
		
        $data_detail = $data["detail"]; 
        $data_id = $data["id"];		
		//--- Added by Asep, 20130522
        $tgl=preg_split('/[- :]/',trim($data_id['TANGGAL']));
		$tgl=implode('',$tgl);
		//-----	        
        $plat_no = str_replace(" ","",trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
		$netto = trim(htmlentities($data_id['BERAT_BERSIH'],ENT_QUOTES,'UTF-8')); // Added by Asep, 20130506
		$netto_kebun = $this->model_s_nota_angkut_kebun->get_netto(trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8')));

        $data_post['ID_NT_AB'] = $company.$this->global_func->createID_Nota('s_nota_angkutbuah','ID_NT_AB',"NAB",$company,"TANGGAL",$data_id['TANGGAL']);
        //$data_post['NO_TIKET'] = trim(htmlentities($data_id['NO_TIKET'],ENT_QUOTES,'UTF-8'));
        $data_post['NO_SPB'] = str_replace(" ","",strtoupper(trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['NO_KENDARAAN']=strtoupper($plat_no);
        $data_post['NAMA_SUPIR']=strtoupper(trim(htmlentities($data_id['NAMA_SUPIR'],ENT_QUOTES,'UTF-8')));
        $data_post['TANGGAL'] =trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8')) ;
        $data_post['COMPANY_CODE']  = trim($company);
        $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
		$no_spb = str_replace(" ","",strtoupper(trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'))));
		
		$flag_spb = $data_id['FLAG_SPB'];
		if ($flag_spb == "1"){
			$data_post['FLAG_TIMBANGAN']  = trim($flag_spb);
		}		
		$flag_wp = $this->Model_m_company->info_weighbridge_sta($company);
		if (($flag_wp == "1") &&($flag_spb)){
			$return['status']="Centang hanya untuk perusahaan non timbangan ";
            $return['error']=true;
		}
        
        $TGL_AKTIVITAS=strval($data_post['TANGGAL']);
        if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==false){
            $return['status']="Tanggal Aktifitas tidak boleh kosong";
            $return['error']=true;
        } else{ 
            if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
                $return['status']="format datetime tidak benar";
                $return['error']=true;
            }
			
			// validasi closing
			$isClose = $this->model_s_nota_angkut_kebun->get_status_close($company, $TGL_AKTIVITAS);
			if ($isClose == 1){
				$return['status']="NAB dengan tanggal " .  date('d-m-Y', strtotime($tgl)). " tidak diijinkan diinput kedalam sistem, dikarenakan tanggal tersebut sudah closing. Silakan reopen tanggal jika ingin bertransaksi di tanggal tersebut
				";
                $return['error']=true;	
			}		
        }
        
		if(strlen($data_post['NO_KENDARAAN']) > 20 || strlen($data_post['NO_KENDARAAN'])<=0){
			$return['status']="input NO_KENDARAAN melebihi batas karakter yang di tetapkan dan tidak boleh kosong  \r\n";
			$return['error']=true;
		}
			
		if(strlen($data_post['NO_SPB']) > 50 || strlen($data_post['NO_SPB'])<=0){
			$return['status']="input NO_SPB melebihi batas karakter yang di tetapkan dan tidak boleh kosong  \r\n";
			$return['error']=true;
		}

		
		//start: Added by Asep, 20130506
		foreach($data_detail as $key => $val){
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $tmp_identifier=$int; 
	
			//if(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='JANJANG'){
			if (preg_match('/JJG_TOTAL/',$key)){
				$total_janjang = $total_janjang + $val;			
			//}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='TONASE'){						
			}elseif (preg_match('/TONASE/',$key)){
				$empiris = $this->model_m_bjr->get_active_bjr($company,$block,$tgl);
				$val = $val * $empiris;       	
				$total_empiris = $total_empiris + $val;
				$block = '';
				$empiris = 0;								
			//}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='BLOCK') {
			}elseif (preg_match('/BLOCK/',$key)){
				$block =$val;
			}
		}
			
		$data_post['TOTAL_JANJANG']  = $total_janjang;
		$data_post['TOTAL_BERAT_EMPIRIS']  = $total_empiris;
        $block ='';
		$int=0;
		$tmp_identifier='';
		//end: Added by Asep, 20130506  
        foreach($data_detail as $key => $val)
        {
            $int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $tmp_identifier=$int; 
			//if(substr($key,-1)!=$tmp_identifier){
                //$tmp_identifier=substr($key,-1); 
            //}
            
            //if(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='AFD'){
			if (preg_match('/AFD/',$key)){
                if(strlen(trim($val))>2)   {
                    $return['status']="input AFD pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
                    $return['error']=true;    
                }elseif(strlen(trim($val))<=0 || empty($val)){
                    $return['status']="AFD pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
                    $tmp_afd=trim($val);
                    $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('AFD'=>$val));
                }
			//}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='TONASE'){
			}elseif (preg_match('/TONASE/',$key)){
				//start: Added by Asep, 20130506
				if($netto==0 || $total_empiris==0 || $netto_kebun==0){	
						$return['status']="Berat Bersih atau berat empiris Timbangan tidak terdeteksi. Silakan klik ulang tombol simpan \r\n";
						$return['error']=true;
				}else{
					$bjr = 0;
					$bjr = $this->model_m_bjr->get_active_bjr($company,$block,$tgl);	
					if($bjr<=0 || empty($bjr)){
						$return['status']="BJR blok " . $block ." pada  baris ke ".$int." tidak boleh nol. Mohon input BJR terlebih dahulu di Menu master BJR \r\n";
						$return['error']=true;   
					}else{
						$empiris = $val * $bjr;
						if ($total_empiris<>0) {
							$val = ($empiris/$total_empiris)*$netto;
							$val_kebun = ($empiris/$total_empiris)*$netto_kebun;
						}
						//else{
						//	$val =0;
						//}
						//if ($flag_spb){
							//$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE'=>$empiris));
							//$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE'=>$val));
						//}else{
							$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE'=>$val));	
							$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE_KEBUN'=>$val_kebun));
						//}
						$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BJR'=>$bjr));
						$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BERAT_EMPIRIS'=>$empiris));
						$block = '';
						$empiris = 0;
					}
				}
				//end: Added by Asep, 20130506 					
			//}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='BLOCK') {
			}elseif (preg_match('/BLOCK/',$key)){
                if(strlen(trim($val))>20){
                    $return['status']="input BLOCK pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
                    $return['error']=true;    
                }elseif(strlen(trim($val))<=0 || empty($val)){
                    $return['status']="BLOCK pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{ 
                    // no error, then validate location
                    $lc = trim($val);  
                    $afd = $tmp_afd;
                    $data_lokasi = $this->model_s_nota_angkut_kebun->lokasi_validate($afd,$lc,$company);   
                    if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){  
                        $return['status']="kode lokasi : ".$lc.", pada baris ".$int." SALAH!! \r\n";
                        $return['error']=true;
                    }else{
                        $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BLOCK'=>$val));						
						$block =$val; //Added by Asep, 20130506
						$block_panen =$val; //Added by Asep, 20130506
                    }    
                }    
            //}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='JANJANG') {
			}elseif (preg_match('/JANJANG/',$key)){
                if(strlen(trim($val))>10){
                    $return['status']="input JANJANG pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
                    $return['error']=true;    
                }elseif(strlen(trim($val))<=0 || intval($val)==0){
                    $return['status']="input JANJANG pada baris ".$int." tidak boleh nol atau kosong  \r\n";
                    $return['error']=true;   
                } else{
                    $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('JANJANG'=>$val));
                }
            //}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='TANGGAL_PANEN') {
			}elseif (preg_match('/OVERRIPE/',$key)){
                if(strlen(trim($val))>10){
                    $return['status']="input JANJANG OVERRIPE pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
                    $return['error']=true;    
                //}elseif(strlen(trim($val))<=0){
                    //$return['status']="input JANJANG OVERRIPE pada baris ".$int." tidak boleh nol atau kosong  \r\n";
                    //$return['error']=true;   
                } else{
                    $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('OVERRIPE'=>$val));
                }
			}elseif (preg_match('/AFKIR/',$key)){
                if(strlen(trim($val))>10){
                    $return['status']="input JANJANG AFKIR pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
                    $return['error']=true;    
                //}elseif(strlen(trim($val))<=0){
                   // $return['status']="input JANJANG AFKIR pada baris ".$int." tidak boleh nol atau kosong  \r\n";
                    //$return['error']=true;   
                } else{
                    $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('AFKIR'=>$val));
                }
			}elseif (preg_match('/BRONDOLAN/',$key)){
                if(strlen(trim($val))>10){
                    $return['status']="input BRONDOLAN pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
                    $return['error']=true;    
                //}elseif(strlen(trim($val))<=0){
                    //$return['status']="input BRONDOLAN pada baris ".$int." tidak boleh nol atau kosong  \r\n";
                    //$return['error']=true;   
                } else{
                    $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BRONDOLAN'=>$val));
                }
			}elseif (preg_match('/TANGGAL_PANEN/',$key)){
            	if(strlen(trim($val))<=0 || intval($val)==0){					
					$return['status']="Mohon tutup kotak tanggal panen yang masih terbuka pada baris ".$int." dan tidak boleh null \r\n";
					$return['error']=true;   
        		}else{                    
                    $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TANGGAL_PANEN'=>$val));
               	}                    
            //}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='NO_SPB') {
				$date =$val;
				$block_panen = trim($block_panen);	
				if($company<>'NAK'){
					$tgl_lokasi = $this->model_s_nota_angkut_kebun->lokasitgl_validate($date,$block_panen,$company); 
					 
					if($tgl_lokasi=0 || $tgl_lokasi='0' || $tgl_lokasi==null){ 
						$return['status']="Tidak ada aktivitas panen di blok ".$block_panen." pada tanggal : ".$date.", Mohon koreksi baris ".$int." \r\n";
						$return['error']=true;
					}
					
					$boolCompare = $this->compare_two_date($TGL_AKTIVITAS,$date);
					if($boolCompare==false){
						$return['status']="Tanggal Angkut ".$TGL_AKTIVITAS." pada blok ".$block_panen." lebih dulu dari tanggal panen ".$date.". Mohon koreksi baris ".$int." \r\n";
						$return['error']=true;					
					}
				}
			}elseif (preg_match('/NO_SPB/',$key)){
                $data_post_d[$tmp_identifier]=array('ID_NT_AB'=>$data_post['ID_NT_AB']); //array('NO_SPB'=>$val);    
                $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('ID_NT_AB'=>$data_post['ID_NT_AB'])); 
            }elseif (preg_match('/DESCRIPTION/',$key)){				               
                $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('DESCRIPTION'=>$val));
            }
			
        }
        
		if ($data_post_d == null){
			$return['status']="Mohon isi detail nota buah \r\n";
			$return['error']=true;		
		}
        if(empty($return['status']) && $return['error'] == false){   
            $id=trim($data_post['ID_NT_AB']);        
            $insert_id = $this->model_s_nota_angkut_kebun->add_new($id, $company, $data_post, $flag_wp );
			
			if($insert_id['error'] == false){ 
            	$insert_detail = $this->model_s_nota_angkut_kebun->add_new_detail($data_post['ID_NT_AB'], $no_spb, $company, $data_post_d) ;
				$return = $insert_detail;
			}else{
				$return = $insert_id;	
			}
			//$return['status']=  "detail: ".$insert_detail."id: ".$insert_id;
            //$return['error']=false;
            echo json_encode($return);         
        }else{
            echo json_encode($return);
        }
    }
	
	function compare_two_date($date_1,$date_2) {
		list($year, $month, $day) = explode('-', $date_1);
	  	$new_date_1 = sprintf('%04d%02d%02d', $year, $month, $day);
	  	list($year, $month, $day) = explode('-', $date_2);
	  	$new_date_2 = sprintf('%04d%02d%02d', $year, $month, $day);
		
		if ($date_2 > $date_1) {
			return false;
		}else{
			return true;
		}
	}
    
    function update_nota(){
		$total_janjang = 0; //Added By Asep, 20130506
		$janjang = 0 ; //Added by Asep, 20130506
		$total_empiris = 0; //added by Asep, 20130506
        $return['status']='';
        $return['error']=false;
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_detail = array();
        $tmp_afd='';
        $tmp_identifier='';
        $data_post_d = array();
        $data_detail = $data["detail"]; 
        $data_id = $data["id"];
		
        //--- Added by Asep, 20130522
        $tgl=preg_split('/[- :]/',trim($data_id['TANGGAL']));
		$tgl=implode('',$tgl);
		//-----	
        $plat_no = trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $netto = trim(htmlentities($data_id['BERAT_BERSIH'],ENT_QUOTES,'UTF-8')); // Added by Asep, 20130506
		$netto_kebun = $this->model_s_nota_angkut_kebun->get_netto(trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'))); 
		
        $id_nota = trim(htmlentities($data_id['ID_NT_AB'],ENT_QUOTES,'UTF-8'));
        //$no_tiket = trim(htmlentities($data_id['NO_TIKET'],ENT_QUOTES,'UTF-8'));
        $no_spb = trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'));
        $no_tiket = $no_spb;
        $cek_status = $this->model_s_nota_angkut_kebun->cek_nota_status($no_tiket,$id_nota);
        if($cek_status == 1 || $cek_status =='1') 
        {
            $return['status']=  "Data sudah ditutup, tidak dapat di update !";
            $return['error']=true;
            echo json_encode($return);
        }else{
            $data_post['NO_KENDARAAN']=strtoupper($plat_no);
            $data_post['NAMA_SUPIR']=strtoupper(trim(htmlentities($data_id['NAMA_SUPIR'],ENT_QUOTES,'UTF-8')));
            $data_post['NO_SPB']=strtoupper(trim($no_spb));
            $data_post['TANGGAL'] =trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8')) ;
            $data_post['COMPANY_CODE']  = trim($company);
            $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();
			$flag_spb = $data_id['FLAG_SPB'];
			if ($flag_spb == "1"){
				$data_post['FLAG_TIMBANGAN']  = trim($flag_spb);
			}
			$flag_wp = $this->Model_m_company->info_weighbridge_sta($company);
			if (($flag_wp == "1") &&($flag_spb)){
				$return['status']="Centang hanya untuk perusahaan non timbangan ";
				$return['error']=true;
			}			
			//start: Added by Asep, 20130506	
			
			foreach($data_detail as $key => $val){

				$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                $tmp_identifier=$int; 

				//if(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='JANJANG'){
				if (preg_match('/JJG_TOTAL/',$key)){
					$total_janjang = $total_janjang + $val;			
				//}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='TONASE'){	
				}elseif (preg_match('/TONASE/',$key)){
					$empiris = $this->model_m_bjr->get_active_bjr($company,$block,$tgl);
					$val = $val * $empiris;       	
					$total_empiris = $total_empiris + $val;
					$block = '';
					$empiris = 0;								
				//}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='BLOCK') {
				}elseif (preg_match('/BLOCK/',$key)){
					$block =$val;
				}
			}
			$data_post['TOTAL_JANJANG']  = $total_janjang;
			$data_post['TOTAL_BERAT_EMPIRIS']  = $total_empiris;
	        $block ='';
			//end: Added by Asep, 20130506 
            
            $TGL_AKTIVITAS=strval($data_post['TANGGAL']);
            if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==''){
                $return['status']="Tanggal Aktifitas tidak boleh kosong";
                $return['error']=true;
            }else{ 
                if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
                    $return['status']="format datetime tidak benar";
                    $return['error']=true;
                }
				
				// validasi closing
				$isClose = $this->model_s_nota_angkut_kebun->get_status_close($company, $TGL_AKTIVITAS);
				if ($isClose == 1){
					$return['status']="NAB dengan tanggal ".  date('d-m-Y', strtotime($tgl)). " tidak diijinkan diupdate, dikarenakan tanggal tersebut sudah closing. Silakan reopen tanggal jika ingin bertransaksi di tanggal tersebut
					
					";
					$return['error']=true;	
				}
            }

			if(strlen($data_post['NO_KENDARAAN']) > 20 || strlen($data_post['NO_KENDARAAN'])<=0){
				$status = "input NO_KENDARAAN melebihi batas karakter yang di tetapkan dan tidak boleh kosong  \r\n"; 
				$return['status']=$status;
				$return['error']=true;
			}
				
			if(strlen($data_post['NO_SPB']) > 50 || strlen($data_post['NO_SPB'])<=0){
				$status = "input NO_SPB melebihi batas karakter yang di tetapkan dan tidak boleh kosong  \r\n"; 
				$return['status']=$status;
				$return['error']=true;
			}

				
            $block = '';
			$int=0;
			$tmp_identifier='';			
            foreach($data_detail as $key => $val)
            {
                /*if(substr($key,-1)!=$tmp_identifier)
                {
                    $tmp_identifier=substr($key,-1); 
                }*/
				$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT); // Added By Asep, 20130531
                $tmp_identifier=$int; // Modified By Asep, 20130531 
                //if(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='AFD')
				if (preg_match('/AFD/',$key)){
                    if(strlen(trim($val))>2)   
                    {
                        $status = "input AFD pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n"; 
                        $return['status']=$status;
                        $return['error']=true;    
                    }elseif(strlen(trim($val))<=0 || empty($val)){
                        $status="AFD pada baris ".$int." tidak boleh kosong   \r\n";
                        $return['status']=$status;
                        $return['error']=true;   
                    }else{
                        $tmp_afd=trim($val);
                        $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('AFD'=>$val));
                    }
                //}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='TONASE'){ 					
				}elseif (preg_match('/TONASE/',$key)){
					//start: Added by Asep, 20130506
					if($netto==0 || $total_empiris==0 || $netto_kebun==0){	
						$return['status']="Berat Bersih atau berat empiris Timbangan tidak terdeteksi. Silakan klik ulang tombol simpan \r\n";
						$return['error']=true;
					}else{
						$bjr = 0;
						$bjr = $this->model_m_bjr->get_active_bjr($company,$block,$tgl);	
						if($bjr<=0 || empty($bjr)){
							$return['status']="BJR blok " . $block ." pada  baris ke ".$int." tidak boleh nol. Mohon input BJR terlebih dahulu di Menu master BJR \r\n";
							$return['error']=true;   
						}else{
							$empiris = $val * $bjr;
							if ($total_empiris<>0) {
								$val = ($empiris/$total_empiris)*$netto;
								$val_kebun = ($empiris/$total_empiris)*$netto_kebun;
							}
							//else{
								//$val =0;
							//}
							//if ($flag_spb){
								//$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE'=>$empiris));
								//$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE'=>$val));
							//}else{
								$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE'=>$val));	
								$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TONASE_KEBUN'=>$val_kebun));	
							//}
							$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BJR'=>$bjr));
							$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BERAT_EMPIRIS'=>$empiris));
							$block = '';
							$empiris = 0;
						}
					}
					//end: Added by Asep, 20130506 						
				//}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='BLOCK') {
				}elseif (preg_match('/BLOCK/',$key)){
                    if(strlen(trim($val))>20)
                    {
                        $status = "input BLOCK pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n"; 
                        $return['status']=$status;
                        $return['error']=true;    
                    }elseif(strlen(trim($val))<=0 || empty($val)){
                        $status="BLOCK pada baris ".$int." tidak boleh kosong   \r\n";
                        $return['status']=$status;
                        $return['error']=true;   
                    }else{ // no error, then validate location
                        $lc = trim($val);  
                        $afd = $tmp_afd;
                        $data_lokasi = $this->model_s_nota_angkut_kebun->lokasi_validate($afd,$lc,$company);   
                        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null)
                        { 
                            $status = "kode lokasi : ".$lc.", pada baris ".$int." SALAH!! \r\n"; 
                            $return['status']=$status;
                            $return['error']=true;
                        }else{
							$block =$val; //Added by Asep, 20130506
                            $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BLOCK'=>$val));
							$block_panen =$val; //Added by Asep, 20130506
                        }    
                    }    
                //}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='JANJANG') {
				}elseif (preg_match('/JANJANG/',$key)){
                    if(strlen(trim($val))>10){
                        $status = "input JANJANG pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n"; 
                        $return['status']=$status;
                        $return['error']=true;    
                    }elseif(strlen(trim($val))<=0 || intval($val)==0){
                        $return['status']="input JANJANG pada baris ".$int." tidak boleh nol atau kosong  \r\n";
                        $return['error']=true;   
                    } else{
                        $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('JANJANG'=>$val));
                    }
                //}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='TANGGAL_PANEN') {
				}elseif (preg_match('/OVERRIPE/',$key)){
					if(strlen(trim($val))>10){
						$return['status']="input JANJANG OVERRIPE pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
						$return['error']=true;    
					//}elseif(strlen(trim($val))<=0){
						//$return['status']="input JANJANG OVERRIPE pada baris ".$int." tidak boleh nol atau kosong  \r\n";
						//$return['error']=true;   
					} else{
						$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('OVERRIPE'=>$val));
					}
				}elseif (preg_match('/AFKIR/',$key)){
					if(strlen(trim($val))>10){
						$return['status']="input JANJANG AFKIR pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
						$return['error']=true;    
					//}elseif(strlen(trim($val))<=0){
						//$return['status']="input JANJANG AFKIR pada baris ".$int." tidak boleh nol atau kosong  \r\n";
						//$return['error']=true;   
					} else{
						$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('AFKIR'=>$val));
					}
				}elseif (preg_match('/BRONDOLAN/',$key)){
					if(strlen(trim($val))>10){
						$return['status']="input BRONDOLAN pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n";
						$return['error']=true;    
					//}elseif(strlen(trim($val))<=0){
						//$return['status']="input BRONDOLAN pada baris ".$int." tidak boleh nol atau kosong  \r\n";
						//$return['error']=true;   
					} else{
						$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('BRONDOLAN'=>$val));
					}
				}elseif (preg_match('/TANGGAL_PANEN/',$key)){
					if(strlen(trim($val))<=0 || intval($val)==0){
						//$return['status']="input Tanggal Panen pada baris ".substr($key,-1)." tidak boleh nol atau kosong  \r\n";
						$return['status']="Mohon tutup kotak tanggal panen yang masih terbuka pada baris ".$int." dan tidak boleh null \r\n";
						$return['error']=true;   
					}else{						
						$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('TANGGAL_PANEN'=>$val));
					}  
					
					$date =$val;
					$block_panen = trim($block_panen);	
					if($company<>'NAK'){

						$tgl_lokasi = $this->model_s_nota_angkut_kebun->lokasitgl_validate($date,$block_panen,$company); 
						 
						if($tgl_lokasi=0 || $tgl_lokasi='0' || $tgl_lokasi==null){ 
							$return['status']="Tidak ada aktivitas panen di blok ".$block_panen." pada tanggal : ".$date.", Mohon koreksi baris ".$int." \r\n";
							$return['error']=true;
						}

						$boolCompare = $this->compare_two_date($TGL_AKTIVITAS,$date);
						if($boolCompare==false){
							$return['status']="Tanggal Angkut ".$TGL_AKTIVITAS." pada blok ".$block_panen." lebih dulu dari tanggal panen ".$date.". Mohon koreksi baris ".$int." \r\n";
							$return['error']=true;					
						}
					}
					              
                //}elseif(strtoupper(trim(substr($key,0,strlen(trim($key))-1)))=='NO_SPB') {
				}elseif (preg_match('/NO_SPB/',$key)){
                    $data_post_d[$tmp_identifier]=array('ID_NT_AB'=>$id_nota); //array('NO_SPB'=>$val);    
                    $data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('ID_NT_AB'=>$id_nota)); 
                }elseif (preg_match('/DESCRIPTION/',$key)){	
					if($val=='&lt;input role="textbox" name="DESCRIPTION" id="1_DESCRIPTION" style="width: 98%;" type="text"&gt;'){
						$return['status']="Mohon tutup kotak KETERANGAN yang masih terbuka pada baris ".$int." \r\n";
						$return['error']=true;
					}
                	$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('DESCRIPTION'=>$val));
            }
			
				//start: Added By Asep, 20130524
				//$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))));
				//$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('UPDATE_TIME'=>$this->global_func->gen_datetime()));
				//end: Added By Asep, 20130524
            }
            
            if ($data_post_d == null){
				$return['status']="Mohon isi detail nota buah \r\n";
				$return['error']=true;		
			}	
					
            if(empty($return['status']) && $return['error']==false){ 
                $update_id = $this->model_s_nota_angkut_kebun->update_data($id_nota,$no_tiket, $company, $data_post );				
                if($update_id['error'] == false){ 
					$update_detail = $this->model_s_nota_angkut_kebun->update_detail($id_nota,$no_tiket, $company, $data_post_d) ;
					$return = $update_detail;
				}else{
					$return = $update_id;	
				}
					//$return['status']=  "Detail: ".$update_detail." - ID: ".$update_id;
                //$return['error']=false;
                echo json_encode($return);           
            }else{
                echo json_encode($return);
            }     
        }
        
    }
    
    function update_nota_detail($data_id){
        $return['status']='';
        $return['error']=FALSE;
        try{
            $id_anon=trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8')) ;
            $id_nt_ab=trim(htmlentities($data_id['ID_NT_AB'],ENT_QUOTES,'UTF-8')) ;
            $data_post['AFD'] = trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8')) ;
            $data_post['BLOCK'] = trim(htmlentities($data_id['BLOCK'],ENT_QUOTES,'UTF-8')) ;
            $data_post['JANJANG'] = trim(htmlentities($data_id['JANJANG'],ENT_QUOTES,'UTF-8')) ;
            $data_post['TANGGAL_PANEN'] = trim(htmlentities($data_id['TANGGAL_PANEN'],ENT_QUOTES,'UTF-8')) ;
            $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();

            if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
                throw new Exception("AFD tidak boleh null!!");              
            }elseif(strlen($data_post['AFD']) > 25){
                throw new Exception("Panjang karakter AFD melebihi batas maksimal");
            }
            
            if (empty($data_post['BLOCK']) || trim($data_post['BLOCK'])==''){
                throw new Exception("BLOCK tidak boleh null!!");              
            }elseif(strlen($data_post['BLOCK']) > 25){
                throw new Exception("Panjang karakter BLOCK melebihi batas maksimal");
            }
            
            if (empty($data_post['JANJANG']) || trim($data_post['JANJANG'])==''){
                throw new Exception("JANJANG tidak boleh null!!");              
            }elseif(strlen($data_post['JANJANG']) > 10){
                throw new Exception("Panjang karakter JANJANG melebihi batas maksimal");
            }
            
            if(empty($return['status']) && $return['error']==false){     
                $insert_id = $this->model_s_nota_angkut_kebun->update_nota_detail($id_anon,$id_nt_ab,$data_post);
                $return['status']=  $insert_id;
                $return['error']=false;
                echo json_encode($return);          
            }else{
                echo json_encode($return);
            } 
            
               
        }catch(Exception $e){
            $return['status'] = $e->getMessage();
            $return['error']=true;
            echo json_encode($return);    
        }    
    }
    
    function delete_nota($data_id){
        $return['status']='';
        $return['error']=false;
        //$data = json_decode($this->input->post('myJson'), true);
        //$data_id=array();
        //$data_detail = array();
        
        //$data_detail = $data["detail"]; 
       // $data_id = $data["id"];
        
        $id_nota = trim(htmlentities($data_id['ID_NT_AB'],ENT_QUOTES,'UTF-8'));
        $plat_no = trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'));
        $no_spb = trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
 
        if (empty($id_nota) || trim($id_nota)=='' || $id_nota==false){
            $status = "ID_NOTA KOSONG !!"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($id_nota) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_nota_angkut_kebun->delete_nota($id_nota,$plat_no,$no_spb,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }    
    }
    
    function delete_notadetail($data_id){
        $return['status']='';
        $return['error']=false;

        $id_anon = trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'));
        $id_nt_ab = trim(htmlentities($data_id['ID_NT_AB'],ENT_QUOTES,'UTF-8'));
 
        if (empty($id_anon) || trim($id_anon)=='' || $id_anon==false){
            //$status = "ID_NOTA KOSONG !!"; 
            //$return['status']=$status;
            //$return['error']=true;  
        }elseif(strlen($id_anon) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_nota_angkut_kebun->delete_notadetail($id_anon,$id_nt_ab);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }    
    }

    function generate_tiket(){
        $no_tiket = $this->global_func->id_GAD('s_nota_angkutbuah','NO_TIKET',str_replace(" ","","NAB"));
        echo $no_tiket;   
    }
    
    function generate_spb(){
        $no_tiket = $this->global_func->createMy_ID('s_nota_angkutbuah','NO_SPB',"SPB") ;
        
        echo $no_tiket;   
    }
    
    function get_no_kendaraan(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //no kendaraan
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_kendaraan = $this->model_s_nota_angkut_kebun->get_no_kendaraan($q,$company);
        //echo $q;
        $kendaraan = array();
        foreach($data_kendaraan as $row)
        {
            $kendaraan[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NAMA_KONTRAKTOR'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['NAMA_KONTRAKTOR'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$kendaraan).']'; exit;         
    }
    
	function get_supplier(){
        $supplier = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //no kendaraan
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_supplier = $this->model_s_nota_angkut_kebun->get_supplier($supplier,$company);
        //echo $q;
        $supplier = array();
        foreach($data_supplier as $row)
        {
            $supplier[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['SUPPLIERCODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['SUPPLIERNAME'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['SUPPLIERNAME'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$supplier).']'; exit;         
    }
	
    function get_no_spb(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //no kendaraan
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $data_spb = $this->model_s_nota_angkut_kebun->get_no_spb($q,$company,$tanggalm);
        //echo $q;
        $spb = array();
        foreach($data_spb as $row)
        {
            $spb[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['NO_SPB'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')).
                '",res_dName:"'.str_replace('"','\\"',htmlentities($row['DRIVER_NAME'],ENT_QUOTES,'UTF-8')).
				'",res_dNetto:"'.str_replace('"','\\"',htmlentities($row['BERAT_BERSIH'],ENT_QUOTES,'UTF-8')).
				'",res_dFlag:"'.str_replace('"','\\"',htmlentities($row['FLAG_TIMBANGAN'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['NO_SPB'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['DRIVER_NAME'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$spb).']'; exit;         
    }
   
   function search_spb(){
        $spb = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $no_kend = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $periode = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8') ;
        
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_nota_angkut_kebun->search_spb($spb, $no_kend,$periode, $company));
    } 
}
?>
