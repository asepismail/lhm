<?php

class m_nursery extends Controller 
{

    function m_nursery ()
    {
        parent::Controller();
        $this->load->model('model_m_nursery');
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_nursery";
        
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->plugin('to_excel');
        
        $this->load->library('approval/appApproval');
        $this->load->library('form/formHeader');
    }
	
    function index()
    {
        $viewPath='Project/';
        $viewName ='info_PrjPengajuan';
        
        $view = "info_m_nursery";
        $data = array();
        $data['judul_header'] = "Data Master Blok Bibitan";
        $data['js'] = "";
        
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
        
        if ($data['login_id'] == TRUE && $data['user_level']){
                show($view, $data);
        } else {
                redirect('login');
        }
    }
    
    function LoadData()
    {
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_nursery->LoadData($company, $limit, $page, $sidx, $sord));
    }   
    
    function AddNew()
    {
        $this->load->library('form_validation');
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $desc=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $datapost['NURSERYCODE']=htmlentities($this->input->post('NURSERYCODE'),ENT_QUOTES,'UTF-8');
        $datapost['DESCRIPTION']=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $datapost['DATEPLANTED']=htmlentities($this->input->post('DATEPLANTED'),ENT_QUOTES,'UTF-8');
        $datapost['VARIETAS']=htmlentities($this->input->post('VARIETAS'),ENT_QUOTES,'UTF-8');
        $datapost['QTYONHAND']=htmlentities($this->input->post('QTYONHAND'),ENT_QUOTES,'UTF-8');
        $isactive=htmlentities($this->input->post('INACTIVE'),ENT_QUOTES,'UTF-8');
		$datapost['INACTIVE'] = $isactive;
		
		if($isactive == '1') {
			$datapost['INACTIVE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
			$datapost['INACTIVE_DATE'] = date ("Y-m-d H:i:s");
		}
		 
	    $datapost['COMPANY_CODE']=$company;
        $datapost['INPUT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['INPUT_DATE']= date ("Y-m-d H:i:s");
       
        
        if (!empty($datapost['NURSERYCODE']) && !empty($datapost['DESCRIPTION'])) {
            $dataexist = $this->model_m_nursery->cek_exist_data($id,$company,1);
            if ($dataexist==0) {    
                if(isset($datapost)) {
                    $insert=$this->model_m_nursery->AddNew($datapost);
                    if ($insert!=0) {
                        $dataexist = $this->model_m_nursery->cek_exist_data($id,$company,2);
                        if ($dataexist == '0') {
							$this->model_m_nursery->AddToOther($id,$desc,$isactive,$company);
						}
                    }  
                } else {
                    echo "kesalahan dalam input";
                }
            } else {
                $status="Data telah terdapat di dalam database";
                echo $status;
            }    
        } else {
            echo "input tidak komplit";
        }
        
    }
	
    function EditData()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $desc=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
       
        $datapost['NURSERYCODE']=htmlentities($this->input->post('NURSERYCODE'),ENT_QUOTES,'UTF-8');
        $datapost['DESCRIPTION']=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $datapost['DATEPLANTED']=htmlentities($this->input->post('DATEPLANTED'),ENT_QUOTES,'UTF-8');
        $datapost['VARIETAS']=htmlentities($this->input->post('VARIETAS'),ENT_QUOTES,'UTF-8');
        $datapost['QTYONHAND']=htmlentities($this->input->post('QTYONHAND'),ENT_QUOTES,'UTF-8');
        
		$isactive=htmlentities($this->input->post('INACTIVE'),ENT_QUOTES,'UTF-8');
		$datapost['INACTIVE'] = $isactive;
		
		if($isactive == '1') {
			$datapost['INACTIVE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
			$datapost['INACTIVE_DATE'] = date ("Y-m-d H:i:s");
		}
		
        $datapost['COMPANY_CODE']=$company;
        $datapost['UPDATE_BY']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['UPDATE_DATE']=date ("Y-m-d H:i:s");
       
        if (isset($id) && isset($datapost))
        {
            $this->model_m_nursery->EditData($id,$company,$datapost); 
            $this->model_m_nursery->AddToOther($id,$desc,$isactive,$company);   
        }
        else
        {
            echo "kesalahan dalam input";
        }
    }
    
    function DeleteData()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        if (isset($id) && isset($company))
        {
           $this->model_m_nursery->DeleteData($id,$company); 
           $this->model_m_nursery->DelToOther($id,$company);
        }
        
    }    
	
    function SearchData()
    {
        $code=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $desc=htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8'); 
        
        if ($code =="" and $desc=="") {
            echo json_encode($this->model_m_nursery->LoadData($company));
        } else {
            echo json_encode($this->model_m_nursery->src_data($code,$desc,$company, $limit, $page, $sidx, $sord));
        }
    }
    
    function create_excel()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
              
        $this->db->select('NURSERYCODE,DESCRIPTION,DATEPLANTED,VARIETAS,QTYORDERED,QTYONHAND,QTYONHOLD
                    ,QUANTITY,PLOT_CAPACITY,TOTAL_PLOT');
     
        $this->db->where('m_nursery.COMPANY_CODE', $company);  
          $query = $this->db->get('m_nursery');              
            
        to_excel($query,'DAFTARBIBITAN_'.$company);
        //redirect( 'm_gang_activity_detail/' );
        if ($query->num_rows() == 0) 
        {
            redirect( 'm_nursery/' );
        } 
    }
    
}

?>