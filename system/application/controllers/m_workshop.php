<?php
class m_workshop extends Controller
{
	function m_workshop()
	{
		parent::Controller();
		$this->load->model('model_m_workshop');
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_workshop";
        
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
       
	    $view = "info_m_workshop";
		$data = array();
		$data['judul_header'] = "Master Data Workshop";
		$data['js'] = "";
        
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);  
		
		if ($data['login_id'] == TRUE && $data['user_level']){
				show($view, $data);
		} else {
				redirect('login');
		}
	}
	
	function LoadData()
	{
		$company=$this->session->userdata('DCOMPANY');
		echo json_encode($this->model_m_workshop->LoadData($company));
	}
	function AddNew()
	{
		$this->load->library('form_validation');
		$act="add";
        
		$company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
		$dt_post['WORKSHOPCODE']=htmlentities(mysql_escape_string($this->input->post('WORKSHOPCODE')));
		$dt_post['DESCRIPTION']=htmlentities($this->input->post('DESCRIPTION'));
		$dt_post['COMPANY_CODE']=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
		$id=htmlentities(mysql_escape_string($this->input->post('WORKSHOPCODE')));
		$desc=htmlentities(mysql_escape_string($this->input->post('DESCRIPTION')));
        $dt_post['Input_By']=htmlentities($this->session->userdata('LOGINID'));
        $dt_post['Input_Date']=date ("Y-m-d H:i:s");
        if (isset($act))
        {$dt_post['Action'] =$act;}
        else
        {$dt_post['Action'] ="";}
		
        if (!empty($dt_post['WORKSHOPCODE']))
        {
            $isData_exist=$this->model_m_workshop->cek_exist_data($dt_post['WORKSHOPCODE'],$company);
            if ($isData_exist == 0 )
            {
                if(isset($dt_post))
                {
                    $insert = $this->model_m_workshop->AddNew($dt_post);
                    if ($insert==0)
                    {
                        $insert_ot=$this->model_m_workshop->AddToOther($id,$desc,$company);
                    }  
                }
                else
                {
                    echo "kesalahan dalam input";
                } 
            }
            elseif($isData_exist > 0)
            {
                echo "Data telah terdapat di dalam database";
            }    
        }
        else
        {
            echo "input tidak komplit";
        }
		
	}
	function EditData()
	{
		$company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
		$id=htmlentities(mysql_escape_string($this->input->post('WORKSHOPCODE')));
        $act="edit";
        
		$dt_post['WORKSHOPCODE']=htmlentities(mysql_escape_string($this->input->post('WORKSHOPCODE')));
		$dt_post['DESCRIPTION']=htmlentities(mysql_escape_string($this->input->post('DESCRIPTION')));
		$dt_post['COMPANY_CODE']=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
		$dt_post['Input_By']=htmlentities($this->session->userdata('LOGINID'));
        $dt_post['Input_Date']=date ("Y-m-d H:i:s");
        if (isset($act))
        {$dt_post['Action']=$act; }
        else
        {$dt_post['Action']=""; }
        
        if (isset($id) && isset($dt_post))
        {
            $update_data=$this->model_m_workshop->EditData($id,$company,$dt_post);
            $insert_ot=$this->model_m_workshop->AddToOther($id,$dt_post['DESCRIPTION'],$company);
            echo "data telah terupdate";    
        }
        else
        {
            echo "kesalahan dalam input";
        }
		
	}
	
	function DeleteData()
	{
		$company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
		$id=htmlentities(mysql_escape_string($this->uri->segment(3)));
		
        if (isset($id) && isset($company))
        {
           $delete_data = $this->model_m_workshop->DeleteData($id,$company);
           $delete_ot=$this->model_m_workshop->DelToOther($id,$company); 
        }
		else
        {
            echo "kesalahan dalam input";
        }
	}
	function SearchData()
	{
		$code=htmlentities(mysql_escape_string($this->uri->segment(3)));
		$desc=htmlentities(mysql_escape_string($this->uri->segment(4)));
		$company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
		
		if ($code =="" and $desc=="")
		{
			echo json_encode($this->model_m_workshop->LoadData($company));
		}
		else{
			echo json_encode($this->model_m_workshop->src_data($code,$desc,$company));
		}
		
	}
	
	function create_excel()
	{
		$company = $this->session->userdata('DCOMPANY');
              
        $this->db->select('WORKSHOPCODE, DESCRIPTION');
     
        $this->db->where('m_workshop.COMPANY_CODE', $company);  
	  	$query = $this->db->get('m_workshop');              
	        
        to_excel($query,'LHM_'.$company);
		//redirect( 'm_gang_activity_detail/' );
		if ($query->num_rows() == 0) 
		{
        	redirect( 'm_workshop/' );
    	} 
	}
    
    
    function cek_approve()
    {
        $approval = new appApproval;
        
        $cekapprove=$approval->cek_approved();
        echo $cekapprove;
    }
    function update_approve()
    {
        $id= htmlentities(mysql_escape_string($this->uri->segment(3)));
        $uName = htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        $companyCode = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        
        $data_post['Approved']='1';
        $data_post['Approved_BY']=$uName;
        $data_post['Approved_DATE']=date ("Y-m-d H:i:s");
        
        $data_where['WORKSHOPCODE'] = $id;
        $data_where['COMPANY_CODE']= $companyCode;
        
        $approval = new appApproval;
        $approval->tblApprove="m_workshop";
        $approval->update_approve($data_post,$data_where);
        $this->model_m_workshop->UpdateApprMloc($id,$companyCode,"WS",1);
    }
    function delete_approve()
    {
        $id= htmlentities(mysql_escape_string($this->uri->segment(3)));
        $companyCode = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        $data_where['WORKSHOPCODE'] = $id;
        $data_where['COMPANY_CODE']= $this->session->userdata('DCOMPANY'); 
        
        $data_post['Approved']='';
        $data_post['Approved_BY']='';
        $data_post['Approved_DATE']='';
        
        $approval = new appApproval;
        $approval->tblApprove="m_workshop";
        $approval->delete_approve($id,$data_where,$data_post);
        $this->model_m_workshop->UpdateApprMloc($id,$companyCode,"WS",0);
    }
}
?>