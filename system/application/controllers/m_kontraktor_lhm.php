<?php
class m_kontraktor_lhm extends Controller
{
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_kontraktor');
        $this->load->model('model_c_user_auth');  
        $this->load->library('form_validation');
        $this->lastmenu="m_kontraktor_lhm";
    }
    
    function index(){
        $view="info_m_kontraktor_lhm";
        
        $data = array();
        $data['judul_header'] = "Master Data Kontraktor";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$data['propinsi'] =$this->dropdownlist_propinsi();
        $data['grid_name'] = "list_kontraktor";
        $data['grid_pager'] ="pager_kontraktor";

		$data['grid_adem_name'] = "list_adem_kontraktor";
        $data['grid_adem_pager'] = "pager_adem_kontraktor";
		
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_kontraktor->LoadData($company));
    }
    
	function LoadDataAdem(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company = $this->model_m_kontraktor->getCompanyIDAdem($company);
		$nama = $this->uri->segment(3);
        echo json_encode($this->model_m_kontraktor->LoadDataAdem($company, $nama));
    }
	
    function LoadData_Kendaraan(){
        $kode_kontraktor = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_kontraktor->LoadData_Kendaraan($kode_kontraktor,$company));
    }
       
    function create_kontraktor(){
       	$status = "";
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_post['KODE_KONTRAKTOR'] = strtoupper(trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8')));
		//$this->global_func->createMy_ID('m_kontraktor','KODE_KONTRAKTOR',$company."KTK","INPUT_DATE",$company);
        $data_post['KODE_INISIAL'] = strtoupper(trim(htmlentities($this->input->post('KODE_INISIAL'),ENT_QUOTES,'UTF-8'))) ;
		$data_post['IS_KONTRAKTOR_TBS'] = strtoupper(trim(htmlentities($this->input->post('IS_KONTRAKTOR_TBS'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_KONTRAKTOR'] = strtoupper(trim(htmlentities($this->input->post('NAMA_KONTRAKTOR'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_CONTACT']=strtoupper(trim(htmlentities($this->input->post('NAMA_CONTACT'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NO_CONTACT'] =trim(htmlentities($this->input->post('NO_CONTACT'),ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT']  =strtoupper(trim(htmlentities($this->input->post('ALAMAT'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['KOTA'] = strtoupper(trim(htmlentities($this->input->post('KOTA'),ENT_QUOTES,'UTF-8')));
		$data_post['KECAMATAN'] = strtoupper(trim(htmlentities($this->input->post('KECAMATAN'),ENT_QUOTES,'UTF-8'))); 
        $data_post['KODE_POS'] = trim(htmlentities($this->input->post('KODE_POS'),ENT_QUOTES,'UTF-8')); 
        $data_post['PROPINSI'] = strtoupper(trim(htmlentities($this->input->post('PROPINSI'),ENT_QUOTES,'UTF-8'))); 
        $data_post['TELEPON'] = trim(htmlentities($this->input->post('TELEPON'),ENT_QUOTES,'UTF-8')); 
        $data_post['EMAIL'] = trim(htmlentities($this->input->post('EMAIL'),ENT_QUOTES,'UTF-8')); 
        $data_post['BANK'] = strtoupper(htmlentities($this->input->post('BANK'),ENT_QUOTES,'UTF-8')); 
        $data_post['NO_REKENING'] = trim(htmlentities($this->input->post('NO_REKENING'),ENT_QUOTES,'UTF-8')); 
        $data_post['NPWP'] = trim(htmlentities($this->input->post('NPWP'),ENT_QUOTES,'UTF-8'));
        $data_post['INPUT_BY'] = trim(htmlentities($this->input->post('INPUT_BY'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company;  
            
        $status='';
        if (empty($data_post['KODE_KONTRAKTOR']) || trim($data_post['KODE_KONTRAKTOR'])==''){
            $status = "Harap isi Kode Kontraktor";
        }
        
        if (empty($data_post['KODE_INISIAL']) || trim($data_post['KODE_INISIAL'])==''){
            $status = "Harap isi Kode Inisial Kontraktor";
        }
        
        if (empty($data_post['NAMA_KONTRAKTOR']) || trim($data_post['NAMA_KONTRAKTOR'])==''){
            $status = "Harap isi Nama Kontraktor";
        }elseif(strlen($data_post['NAMA_KONTRAKTOR']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
        }
        
		$cek_data_exist = $this->model_m_kontraktor->cek_data_exist('m_kontraktor',array('KODE_KONTRAKTOR'=>$data_post['KODE_KONTRAKTOR'],'COMPANY_CODE'=>$company),'KODE_KONTRAKTOR');
		
        if ($cek_data_exist > 0){
            $status='Data sudah ada di database';
        } else {
			if(empty($status)){     
				$insert_id = $this->model_m_kontraktor->add_new($data_post['KODE_KONTRAKTOR'],$company,$data_post);
				if($insert_id > 0 ){
					$status = 1;
				}
			}
		}
		
		echo $status;
    }
    
	 function delete_kontraktor(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_kontraktor = htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8');
		$status = "";

        if (empty($id_kontraktor) || trim($id_kontraktor)==='' || $id_kontraktor===false){
            $status = "KODE_KONTRAKTOR KOSONG !!";  
        }elseif(strlen($id_kontraktor) > 50){
            $status="Panjang karakter KODE_KONTRAKTOR melebihi batas maksimal";
        }
        
        if(empty($status)){     
            $delete_kontraktor = $this->model_m_kontraktor->delete_kontraktor($id_kontraktor,$company);
            echo $status;
        }else{
            echo $status;
        }   
    }
	
    function update_kontraktor($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		
        $data_post['KODE_INISIAL'] = strtoupper(trim(htmlentities($this->input->post('KODE_INISIAL'),ENT_QUOTES,'UTF-8'))) ;
		$data_post['IS_KONTRAKTOR_TBS'] = strtoupper(trim(htmlentities($this->input->post('IS_KONTRAKTOR_TBS'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_KONTRAKTOR'] = strtoupper(trim(htmlentities($this->input->post('NAMA_KONTRAKTOR'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_CONTACT']=strtoupper(trim(htmlentities($this->input->post('NAMA_CONTACT'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NO_CONTACT'] =trim(htmlentities($this->input->post('NO_CONTACT'),ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT']  =strtoupper(trim($this->input->post('ALAMAT'))) ;
        $data_post['KOTA'] = strtoupper(trim(htmlentities($this->input->post('KOTA'),ENT_QUOTES,'UTF-8')));
        $data_post['KODE_POS'] = trim(htmlentities($this->input->post('KODE_POS'),ENT_QUOTES,'UTF-8')); 
        $data_post['PROPINSI'] = strtoupper(trim(htmlentities($this->input->post('PROPINSI'),ENT_QUOTES,'UTF-8'))); 
		$data_post['KECAMATAN'] = strtoupper(trim(htmlentities($this->input->post('KECAMATAN'),ENT_QUOTES,'UTF-8'))); 
        $data_post['TELEPON'] = trim(htmlentities($this->input->post('TELEPON'),ENT_QUOTES,'UTF-8')); 
        $data_post['EMAIL'] = trim(htmlentities($this->input->post('EMAIL'),ENT_QUOTES,'UTF-8')); 
        $data_post['BANK'] = strtoupper(trim(htmlentities($this->input->post('BANK'),ENT_QUOTES,'UTF-8'))); 
        $data_post['NO_REKENING'] = trim(htmlentities($this->input->post('NO_REKENING'),ENT_QUOTES,'UTF-8')); 
        $data_post['NPWP'] = trim(htmlentities($this->input->post('NPWP'),ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();
        //$data_post['COMPANY_CODE'] = $company;  
            
        $status='';
        $id=strtoupper(trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8')));
        if (empty($id) || $id==''){
            $status = "Harap isi Kode Kontraktor";   
        }
        
        if (empty($data_post['KODE_INISIAL']) || trim($data_post['KODE_INISIAL'])==''){
            $status = "Harap isi Kode Inisial Kontraktor";
        }
        
        if (empty($data_post['NAMA_KONTRAKTOR']) || trim($data_post['NAMA_KONTRAKTOR'])==''){
            $status = "Harap isi Nama Kontraktor";
        }elseif(strlen($this->input->post('NAMA_KONTRAKTOR')) > 150){
            $status  ="Panjang karakter melebihi batas maksimal";
        }
		
		$cek_data_exist = $this->model_m_kontraktor->cek_data_exist('m_kontraktor',array('KODE_KONTRAKTOR'=>$id,'COMPANY_CODE'=>$company),'*');
        if ($cek_data_exist <= 0){
            $status='Data Input ID tidak ada di database';
        } else {
			if(empty($status)){
           		$update_id = $this->model_m_kontraktor->update_data($id,$company,$data_post);
				if( $update_id > 0 ){
					$status = 1;
				}
        	} 
		}
		echo $status;
            
    }
    
    function create_kendaraan(){
        $status='';
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($this->input->post('NO_KENDARAAN'),ENT_QUOTES,'UTF-8')));
        $data_post['KODE_KONTRAKTOR']=strtoupper(trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['DESKRIPSI']=strtoupper(trim(htmlentities($this->input->post('DESKRIPSI'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NOTE'] =trim(htmlentities($this->input->post('NOTE'),ENT_QUOTES,'UTF-8'));
        $data_post['INPUT_BY']  =trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company; 
        
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $status = "Harap isi No Kendaraan";
        }elseif(strlen($data_id['NO_KENDARAAN']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
        }
        
        if(empty($status)){
                    
        $create_id = $this->model_m_kontraktor->add_new_kendaraan($data_post['KODE_KONTRAKTOR'],$data_post['NO_KENDARAAN'],$company,$data_post);
                      
        }else{
            echo $status;
        }    
    }
    
    function update_kendaraan(){
        $status='';
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_post['ID_KENDARAAN_KONTRAKTOR'] = strtoupper(trim(htmlentities($this->input->post('ID_KENDARAAN_KONTRAKTOR'),ENT_QUOTES,'UTF-8')));
        $data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($this->input->post('NO_KENDARAAN'),ENT_QUOTES,'UTF-8')));
        $data_post['KODE_KONTRAKTOR']=strtoupper(trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['DESKRIPSI']=strtoupper(trim(htmlentities($this->input->post('DESKRIPSI'),ENT_QUOTES,'UTF-8'))) ;
        $data_post['NOTE'] =trim(htmlentities($this->input->post('NOTE'),ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_BY']  =trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
        $data_post['COMPANY_CODE'] = $company; 
        
        $status='';
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $status = "Harap isi No Kendaraan";
        }elseif(strlen($data_post['NO_KENDARAAN']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
        }
        
        if (empty($data_post['ID_KENDARAAN_KONTRAKTOR']) || trim($data_post['ID_KENDARAAN_KONTRAKTOR'])==''){
            $status = "Harap isi ID Kendaraan";
        }elseif(strlen($data_post['ID_KENDARAAN_KONTRAKTOR']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
        }
        
        if(empty($status)){
            $create_id = $this->model_m_kontraktor->update_kendaraan($data_post['KODE_KONTRAKTOR'],$data_post['ID_KENDARAAN_KONTRAKTOR'],$data_post['NO_KENDARAAN'],$company,$data_post);
        }else{
            echo $status;
        }
    }
    
    function delete_kendaraan(){
        $status='';
        
        $no_kend = trim(htmlentities($this->input->post('NO_KENDARAAN'),ENT_QUOTES,'UTF-8'));
        $kode_kontraktor = trim(htmlentities($this->input->post('KODE_KONTRAKTOR'),ENT_QUOTES,'UTF-8'));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
 
        if (empty($no_kend) || trim($no_kend)==='' || $no_kend===false){
            $status = "NO_KENDARAAN KOSONG !!"; 
        }elseif(strlen($no_kend) > 50){
        }
        
        if (empty($kode_kontraktor) || trim($kode_kontraktor)==='' || $kode_kontraktor===false){
            $status = "KODE_KONTRAKTOR KOSONG !!"; 
        }elseif(strlen($kode_kontraktor) > 50){
            $status="Panjang karakter KODE_KONTRAKTOR melebihi batas maksimal";
        }
        
        if(empty($status)){     
            $delete_id = $this->model_m_kontraktor->delete_kendaraan($no_kend,$kode_kontraktor,$company);
        }else{
            echo $status;
        }
    }
    
    function search_data(){
        $nama = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //echo json_encode($this->model_m_kontraktor->search_data($nama,$company)); 
        //$data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_m_kontraktor->search_data($nama, $company));      
    }
	
	/* Data Tambahan */ 
	function dropdownlist_propinsi()
	{
		$string = "<select tabindex='8'  name='txt_provinsi' class='select' id='txt_provinsi' style='width:190px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		
		$data_afd = $this->model_m_kontraktor->get_propinsi();
		
		foreach ( $data_afd as $row){
			if( (isset($default)) && ($default==$row[$nama_isi]) ){
				$string = $string." <option value=\"".$row['id_prov']."\"  selected>".$row['nama_prov']." </option>";
			} else {
				$string = $string." <option value=\"".$row['id_prov']."\">".$row['nama_prov']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
	
	function LoadChain()
    {
        $data_key= $this->uri->segment(3);
        $sData =strtolower(trim($data_key));
        $array=array();
        
		$data_afd = $this->model_m_kontraktor->get_kabkot($sData);
        foreach ($data_afd as $drow){
			$array[] = array('kt' => $drow['nama_kabkot'], 'kt2' => $drow['id_kabkot'] );
		}
        echo json_encode($array);
    }
	
	function LoadChain2()
    {
        $propinsi= $this->uri->segment(3);
		$kabkot= $this->uri->segment(4);
        
        $array=array();
        
		$data_afd = $this->model_m_kontraktor->get_kecamatan($propinsi, $kabkot);
        foreach ($data_afd as $drow){
			$array[] = array('kt' => $drow['nama_kec'], 'kt2' => $drow['id_kec'] );
		}
        echo json_encode($array);
    }
}
?>
