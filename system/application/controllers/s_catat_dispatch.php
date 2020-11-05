<?php
class s_catat_dispatch extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_catat_dispatch');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_catat_dispatch";
        $this->data = array();
    }
    
    function index(){
        $view="info_s_catat_dispatch";
        
        $this->data['judul_header'] = "Pencatatan Despatch Komoditi";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$this->data['jenis'] = $this->dropdownlist_jenis($this->session->userdata('DCOMPANY'));
        //$this->data['bjr_periode'] = $this->get_bjr_periode();
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
		
        echo json_encode($this->model_s_catat_dispatch->LoadData($company, $periode));   
    }
    
    function search_data(){
        $no_tiket = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $jenis = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8'));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_catat_dispatch->search_data($no_tiket,$periode,$jenis, $company));
        //$data = json_decode($this->input->post('filters'), true); 
        //echo json_encode($this->model_s_catat_dispatch->data_search($data['rules'], $company));
        
    }
    
    function CRUD_METHOD(){
        $loginid=trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_id = $data["id"];
        //echo $data_id['CRUD'];
        if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADD"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->add_new($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            } 
                      
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_data($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
                    
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_data($data_id);    
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
    
    function add_new($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //$data_post['ID_DISPATCH'] = $this->global_func->createMy_ID('s_dispatch','ID_DISPATCH',$company."DPC","TANGGAL",$company);
        $data_post['ID_DISPATCH_KIRIM'] = strtoupper(trim(htmlentities($data_id['ID_DISPATCH_KIRIM'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TANGGAL_KIRIM'] = strtoupper(trim(htmlentities($data_id['TANGGAL_KIRIM'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['ID_KOMODITAS']=strtoupper(trim(htmlentities($data_id['ID_KOMODITAS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['JENIS'] = strtoupper(trim(htmlentities($data_id['JENIS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['ID_DO'] = strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['DRIVER_NAME'] = strtoupper(trim(htmlentities($data_id['DRIVER_NAME'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')));
		
		$data_post['ID_DISPATCH'] = strtoupper(trim(htmlentities($data_id['ID_DISPATCH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TANGGALM'] = strtoupper(trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8')));
		$data_post['TANGGALK'] = strtoupper(trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8')));
		$data_post['WAKTUM'] = strtoupper(trim(htmlentities($data_id['WAKTUM'],ENT_QUOTES,'UTF-8')));
		$data_post['WAKTUK'] = strtoupper(trim(htmlentities($data_id['WAKTUK'],ENT_QUOTES,'UTF-8')));		
        $data_post['BERAT_KOSONG']=strtoupper(trim(htmlentities($data_id['BERAT_KOSONG'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BERAT_ISI'] =strtoupper(trim(htmlentities($data_id['BERAT_ISI'],ENT_QUOTES,'UTF-8')));
		
		$data_post['BROKEN'] =strtoupper(trim(htmlentities($data_id['BROKEN'],ENT_QUOTES,'UTF-8')));
		$data_post['DIRTY'] =strtoupper(trim(htmlentities($data_id['DIRTY'],ENT_QUOTES,'UTF-8')));
		$data_post['MOIST'] =strtoupper(trim(htmlentities($data_id['MOIST'],ENT_QUOTES,'UTF-8')));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        
		$berat_kosong = intval($data_post['BERAT_KOSONG']);
        $berat_isi = intval($data_post['BERAT_ISI']);
        $data_post['BERAT_BERSIH']=$berat_isi-$berat_kosong;
        
        $s_numeric = array($data_post['BERAT_KOSONG'],$data_post['BERAT_ISI'],$data_post['BROKEN'],$data_post['DIRTY'],$data_post['MOIST']);
        $validate_numeric=$this->validate_numeric($s_numeric);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai BERAT KOSONG, BERAT ISI, BROKEN/FFA, DIRTY dan MOIST harus angka";
            $return['error']=true;       
        }
        
        if (empty($data_post['ID_DISPATCH_KIRIM']) || trim($data_post['ID_DISPATCH_KIRIM'])==''){
            $return['status']="Harap isi No Tiket Dispacth Asal";
            $return['error']=true;  
        }elseif(strlen($data_post['ID_DISPATCH_KIRIM']) > 50){
            $return['status']="Panjang karakter No Tiket Dispacth Asal melebihi batas maksimal";
            $return['error']=true;
        }
                
        if (empty($data_post['ID_KOMODITAS']) || trim($data_post['ID_KOMODITAS'])==''){
            $return['status']="Harap isi JENIS BARANG";
            $return['error']=true;  
        }elseif(strlen($data_id['ID_KOMODITAS']) > 20){
            $return['status']="Panjang karakter JENIS BARANG melebihi batas maksimal";
            $return['error']=true;
        }
        
		if (empty($data_post['JENIS']) || trim($data_post['JENIS'])==''){
            $return['status']="Harap isi JENIS";
            $return['error']=true;  
        }
        
		if (empty($data_post['ID_DO']) || trim($data_post['ID_DO'])==''){
            $return['status']="Harap isi ID DO";
            $return['error']=true;  
        }
		
		if (empty($data_post['DRIVER_NAME']) || trim($data_post['DRIVER_NAME'])==''){
            $return['status']="Harap isi DRIVER NAME";
            $return['error']=true;  
        }
		
		if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $return['status']="Harap isi NO KENDARAAN";
            $return['error']=true;  
        }
		
		if (empty($data_post['ID_DISPATCH']) || trim($data_post['ID_DISPATCH'])==''){
            $return['status']="Harap isi NO TIKET";
            $return['error']=true;  
        }
		
		if (empty($data_post['TANGGALM']) || trim($data_post['TANGGALM'])==''){
            $return['status']="Harap isi TANGGALM";
            $return['error']=true;  
        }
		
		if (empty($data_post['TANGGALK']) || trim($data_post['TANGGALK'])==''){
            $return['status']="Harap isi TANGGALK";
            $return['error']=true;  
        }
		
		if (empty($data_post['WAKTUM']) || trim($data_post['WAKTUM'])==''){
            $return['status']="Harap isi WAKTUM";
            $return['error']=true;  
        }
		
		if (empty($data_post['WAKTUK']) || trim($data_post['WAKTUK'])==''){
            $return['status']="Harap isi WAKTUK";
            $return['error']=true;  
        }
		if ($data_post['BERAT_BERSIH']<= 0){
            $return['status']="berat bersih adalah berat isi dikurangi berat kosong.  berat bersih tidak boleh kurang dari sama dengan nol";
            $return['error']=true;  
        }
		
				
        if(empty($return['status']) && $return['error']===false){
                            
            $insert_id = $this->model_s_catat_dispatch->add_new($company,$data_post);
            $return =  $insert_id;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);  
        }
    }
    
    function update_data($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		
		$data_post['ID_DISPATCH_KIRIM'] = strtoupper(trim(htmlentities($data_id['ID_DISPATCH_KIRIM'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TANGGAL_KIRIM'] = strtoupper(trim(htmlentities($data_id['TANGGAL_KIRIM'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['ID_KOMODITAS']=strtoupper(trim(htmlentities($data_id['ID_KOMODITAS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['JENIS'] = strtoupper(trim(htmlentities($data_id['JENIS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['ID_DO'] = strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['DRIVER_NAME'] = strtoupper(trim(htmlentities($data_id['DRIVER_NAME'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')));
		
		$data_post['ID'] = strtoupper(trim(htmlentities($data_id['ID'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['ID_DISPATCH'] = strtoupper(trim(htmlentities($data_id['ID_DISPATCH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TANGGALM'] = strtoupper(trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8')));
		$data_post['TANGGALK'] = strtoupper(trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8')));
		$data_post['WAKTUM'] = strtoupper(trim(htmlentities($data_id['WAKTUM'],ENT_QUOTES,'UTF-8')));
		$data_post['WAKTUK'] = strtoupper(trim(htmlentities($data_id['WAKTUK'],ENT_QUOTES,'UTF-8')));		
        $data_post['BERAT_KOSONG']=strtoupper(trim(htmlentities($data_id['BERAT_KOSONG'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BERAT_ISI'] =strtoupper(trim(htmlentities($data_id['BERAT_ISI'],ENT_QUOTES,'UTF-8')));
		
		$data_post['BROKEN'] =strtoupper(trim(htmlentities($data_id['BROKEN'],ENT_QUOTES,'UTF-8')));
		$data_post['DIRTY'] =strtoupper(trim(htmlentities($data_id['DIRTY'],ENT_QUOTES,'UTF-8')));
		$data_post['MOIST'] =strtoupper(trim(htmlentities($data_id['MOIST'],ENT_QUOTES,'UTF-8')));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();
		
		$berat_kosong = intval($data_post['BERAT_KOSONG']);
        $berat_isi = intval($data_post['BERAT_ISI']);
        $data_post['BERAT_BERSIH']=$berat_isi-$berat_kosong; 
        
         $s_numeric = array($data_post['BERAT_KOSONG'],$data_post['BERAT_ISI'],$data_post['BROKEN'],$data_post['DIRTY'],$data_post['MOIST']);
        $validate_numeric=$this->validate_numeric($s_numeric);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai BERAT KOSONG, BERAT ISI, BROKEN/FFA, DIRTY dan MOIST harus angka";
            $return['error']=true;       
        }
        
        if (empty($data_post['ID_DISPATCH_KIRIM']) || trim($data_post['ID_DISPATCH_KIRIM'])==''){
            $return['status']="Harap isi No Tiket Dispacth Asal";
            $return['error']=true;  
        }elseif(strlen($data_post['ID_DISPATCH_KIRIM']) > 50){
            $return['status']="Panjang karakter No Tiket Dispacth Asal melebihi batas maksimal";
            $return['error']=true;
        }
                
        if (empty($data_post['ID_KOMODITAS']) || trim($data_post['ID_KOMODITAS'])==''){
            $return['status']="Harap isi JENIS BARANG";
            $return['error']=true;  
        }elseif(strlen($data_id['ID_KOMODITAS']) > 20){
            $return['status']="Panjang karakter JENIS BARANG melebihi batas maksimal";
            $return['error']=true;
        }
        
		if (empty($data_post['JENIS']) || trim($data_post['JENIS'])==''){
            $return['status']="Harap isi JENIS";
            $return['error']=true;  
        }
        
		if (empty($data_post['ID_DO']) || trim($data_post['ID_DO'])==''){
            $return['status']="Harap isi ID DO";
            $return['error']=true;  
        }
		
		if (empty($data_post['DRIVER_NAME']) || trim($data_post['DRIVER_NAME'])==''){
            $return['status']="Harap isi DRIVER NAME";
            $return['error']=true;  
        }
		
		if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $return['status']="Harap isi NO KENDARAAN";
            $return['error']=true;  
        }
		
		if (empty($data_post['ID_DISPATCH']) || trim($data_post['ID_DISPATCH'])==''){
            $return['status']="Harap isi NO TIKET";
            $return['error']=true;  
        }
		
		if (empty($data_post['TANGGALM']) || trim($data_post['TANGGALM'])==''){
            $return['status']="Harap isi TANGGALM";
            $return['error']=true;  
        }
		
		if (empty($data_post['TANGGALK']) || trim($data_post['TANGGALK'])==''){
            $return['status']="Harap isi TANGGALK";
            $return['error']=true;  
        }
		
		if (empty($data_post['WAKTUM']) || trim($data_post['WAKTUM'])==''){
            $return['status']="Harap isi WAKTUM";
            $return['error']=true;  
        }
		
		if (empty($data_post['WAKTUK']) || trim($data_post['WAKTUK'])==''){
            $return['status']="Harap isi WAKTUK";
            $return['error']=true;  
        }
		if ($data_post['BERAT_BERSIH']<= 0){
            $return['status']="berat bersih adalah berat isi dikurangi berat kosong.  berat bersih tidak boleh kurang dari sama dengan nol";
            $return['error']=true;  
        }
        if(empty($return['status']) && $return['error']==false){                
            $insert_id = $this->model_s_catat_dispatch->update_dispatch($data_post['ID'],$data_post,$company);
            $return =  $insert_id;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);  
        }
    }
    
    function delete_data($data_id){
        $return['status']='';
        $return['error']=false;
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $id_dispatch = strtoupper(trim(htmlentities($data_id['ID_DISPATCH'],ENT_QUOTES,'UTF-8'))) ;
        
        if (empty($id_dispatch)){
            $status = "ID_DISPATCH KOSONG !!"; 
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;  
        }elseif(strlen($id_dispatch) > 50){
            $status  ="Panjang karakter ID melebihi batas maksimal";
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_catat_dispatch->delete_dispatch($id_dispatch,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    /*
    function get_storage(){
        $q = trim(htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8')); //no kendaraan
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $data_storage = $this->model_s_catat_dispatch->get_storage($q,$company);
        //echo $q;
        $storage = array();
        foreach($data_storage as $row)
        {
            $storage[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_STORAGE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['PRODUCT_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['ID_STORAGE'],ENT_QUOTES,'UTF-8'). 
                "&nbsp;&nbsp; - &nbsp;&nbsp;".htmlentities($row['PRODUCT_CODE'],ENT_QUOTES,'UTF-8').
                "&nbsp;&nbsp; - &nbsp;&nbsp;".str_replace(chr(10),'',htmlentities($row['DESCRIPTION'],ENT_QUOTES,'UTF-8'))).'"}';
        }
        echo '['.implode(',',$storage).']'; exit;         
    }*/
    
    function get_komoditi(){
        $q = trim(htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8')); 
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $data_komoditas = $this->model_s_catat_dispatch->get_komoditi($q,$company);

        $komoditas = array();
        foreach($data_komoditas as $row)
        {
            $komoditas[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_KOMODITAS'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['JENIS'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['ID_KOMODITAS'],ENT_QUOTES,'UTF-8'). 
                "&nbsp;&nbsp; - &nbsp;&nbsp;".htmlentities($row['JENIS'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$komoditas).']'; exit;         
    }
    
	function get_no_tiket(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //no kendaraan
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $data_spb = $this->model_s_catat_dispatch->get_no_tiket($q,$company,$tanggalm);
        //echo $q;
        $spb = array();
        foreach($data_spb as $row)
        {
            $spb[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_DISPATCH'],ENT_QUOTES,'UTF-8')).
  				'",res_komoditas:"'.str_replace('"','\\"',htmlentities($row['ID_KOMODITAS'],ENT_QUOTES,'UTF-8')).
                '",res_do:"'.str_replace('"','\\"',htmlentities($row['ID_DO'],ENT_QUOTES,'UTF-8')).
				'",res_jenis:"'.str_replace('"','\\"',htmlentities($row['JENIS'],ENT_QUOTES,'UTF-8')).
                '",res_dName:"'.str_replace('"','\\"',htmlentities($row['DRIVER_NAME'],ENT_QUOTES,'UTF-8')).
				'",res_dKendaraan:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')).
				'",res_dNetto:"'.str_replace('"','\\"',htmlentities($row['BERAT_BERSIH'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['ID_DISPATCH'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
				.htmlentities($row['JENIS'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;"
                .htmlentities($row['BERAT_BERSIH'],ENT_QUOTES,'UTF-8'))." Kg".'"}';
        }
        echo '['.implode(',',$spb).']'; exit;         
    }
	
    function validate_numeric($data){
        $numeric=$data;
        $result='';
        if(is_array($data))
        {
            while(list($key,$val)=each($data))
            {
                if(trim($val)=="" || $val==null)
                {
                    $val=0;
                }
                if((! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$val)))
                {
                    $result='false';
                    break;
                }else{
                    $result='true';   
                }
            }
        }else {
            if(trim($numeric)=="" || $numeric==null)
            {
                $val=0;
            }
            
            if (! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$numeric))
            {
                $result='false';   
            }else{
                $result='true';
            }    
        }
        return $result;   
    }
	
	function dropdownlist_jenis($company){
		$q='';
		$string = "<select  name='jenis' class='select' id='jenis' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_dept = $this->model_s_catat_dispatch->get_komoditi($q,$company);
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['ID_KOMODITAS']."\"  selected>".$row['JENIS']." </option>";
			} else {
				$string = $string." <option value=\"".$row['ID_KOMODITAS']."\">".$row['JENIS']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
}
?>
