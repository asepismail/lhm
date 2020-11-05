<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class prj_Pengajuan extends Controller
{
    function __construct()
    {
        parent::Controller();
        $this->load->model( 'model_project_pengajuan' ); 
        
        $this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->model('model_c_user_auth');
        $this->lastmenu="project/prj_Pengajuan";
        $this->load->library('form/formheader');
        $this->load->library('approval/appapproval');
    }
    function index()
    {
       // $viewPath='project/';
        //$viewName ='info_prjpengajuan';
        //$menu = new formHeader;
		
		$view = "project/info_prjpengajuan";
		$data = array();
		$data['judul_header'] = "Master Data Project";
		$data['js'] = "";
        $data['login_id'] = $this->session->userdata('LOGINID');
      	$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
		
		if ($data['login_id'] == TRUE && $data['user_level'] == 'SAD'){
			show($view, $data);
		} else {
			redirect('login');
		}
    }
    
    function AddNew()
    {
        $act ="add";
        $company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY'))); 
    
        $data_post['PROJECT_ID']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
        $data_post['AFD']=htmlentities(mysql_escape_string($this->input->post('AFD')));
        $data_post['PROJECT_TYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_TYPE')));
        $data_post['PROJECT_SUBTYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_SUBTYPE')));
        $data_post['PROJECT_DESC']=htmlentities(mysql_escape_string($this->input->post('PROJECT_DESC')));
        $data_post['PROJECT_START']=htmlentities(mysql_escape_string($this->input->post('PROJECT_START')));
        $data_post['PROJECT_END']=htmlentities(mysql_escape_string($this->input->post('PROJECT_END')));
        $data_post['PROJECT_STATUS']=htmlentities(mysql_escape_string($this->input->post('PROJECT_STATUS')));
        $data_post['COMPANY_CODE']=$company;
        $data_post['Input_By']=htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        $data_post['Input_Date']=date ("Y-m-d H:i:s");
        if(isset($act))
        {
            $data_post['Action'] =$act;    
        }
        else
        {
            $data_post['Action'] ="";
        }
        $id=htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
        $desc=htmlentities(mysql_escape_string($this->input->post('PROJECT_DESC')));
        
        if(isset($data_post) && isset($id) && isset($desc) && isset($company))
        {
            if (!empty($data_post['PROJECT_ID']))
            { 
                $data_exist = $this->model_project_pengajuan->cek_exist_data($id,"m_project",$company); //cek data project, mencegah duplikasi
                if($data_exist > 0)
                {
                    echo("data project telah terdapat di dalam database");
                }
                else
                {  
                    $insert_new=$this->model_project_pengajuan->insert_new_data($data_post,"m_project"); //insert baru ke database
                    echo $insert_new;          
                }    
            }
            else
            {
                echo "input tidak lengkap";
            }       
        }
        else
        {
            echo "variable not define";
        }
    }
    function EditData()
    {
        $act="edit";
        $company = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        
        //$data_post['PROJECT_ID']=htmlentities(mysql_escape_string($this->input->post('PROJECT_ID')));
        $data_post['AFD']=htmlentities(mysql_escape_string($this->input->post('AFD')));
        $data_post['PROJECT_TYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_TYPE')));
        $data_post['PROJECT_SUBTYPE']=htmlentities(mysql_escape_string($this->input->post('PROJECT_SUBTYPE')));
        $data_post['PROJECT_DESC']=htmlentities(mysql_escape_string($this->input->post('PROJECT_DESC')));
        $data_post['PROJECT_START']=htmlentities(mysql_escape_string($this->input->post('PROJECT_START')));
        $data_post['PROJECT_END']=htmlentities(mysql_escape_string($this->input->post('PROJECT_END')));
        $data_post['PROJECT_STATUS']=htmlentities(mysql_escape_string($this->input->post('PROJECT_STATUS')));
        $data_post['COMPANY_CODE']=$company;
        $data_post['Input_By']=htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        $data_post['Input_Date']=date ("Y-m-d H:i:s");
        if (isset($act))
        {
            $data_post['Action']=$act;    
        }
        else
        {
            $data_post['Action']="";
        }
        $desc=htmlentities(mysql_escape_string($this->input->post('DESCRIPTION')));
        $id=mysql_escape_string(htmlentities($this->uri->segment(4)));
        
        if (isset($id) && isset($data_post) && isset($desc))
        {
            if (!empty($id))
            {
                $update_data=$this->model_project_pengajuan->update_data($id,$company,$data_post,"m_project");
                echo "0";   
            }
            else
            {
                 echo "input tidak lengkap";
            }  
        }
        else
        {
            echo "variable not define";
        }
    }
    
    function DelData()
    {
        $id=htmlentities(mysql_escape_string($this->uri->segment(4)));
        $company=htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        
        if (isset($id) && isset($company))
        {
            if(!empty($id))
            {
                $delete_data=$this->model_project_pengajuan->delete_data($id,$company,"m_project"); 
                echo "0";   
            }
            else
            {
                echo "input tidak lengkap";
            }
              
        }
        else
        {
            echo "variable not define";
        }
    }
    
    function LoadData()
    {
        $company=$this->session->userdata('DCOMPANY');
        echo json_encode($this->model_project_pengajuan->LoadData($company));
    }
    
    function SearchData()
    {
        $getID =htmlentities(mysql_escape_string($this->uri->segment(4))); 
        $getAfd=htmlentities(mysql_escape_string($this->uri->segment(5)));
        $getType=htmlentities(mysql_escape_string($this->uri->segment(6)));
        $getDesc=htmlentities(mysql_escape_string($this->uri->segment(7)));
        
        $company=$this->session->userdata('DCOMPANY');
        
        if ($getID =="" && $getAfd=="" && $getDesc=="" && $getType=="")
        {
            echo json_encode($this->model_project_pengajuan->LoadData($company));
        }
        else{
            echo json_encode($this->model_project_pengajuan->search_prj($getID,$getAfd,$getType,$getDesc));
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
        $id= htmlentities(mysql_escape_string($this->uri->segment(4)));
        $uName = htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        $companyCode = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        
        $data_post['Approved']='1';
        $data_post['Approved_BY']=$uName;
        $data_post['Approved_DATE']=date ("Y-m-d H:i:s");
        
        $data_where['PROJECT_ID'] = $id;
        $data_where['COMPANY_CODE']= $companyCode;
        
       
        if (isset($id) && isset($companyCode) && isset($data_post) && isset($data_where))
        {
            if(!empty($id) && !empty($companyCode) && !empty($data_post) && !empty($data_where))
            {
                 $approval = new appApproval;
                 $approval->tblApprove="m_project";
                 $approval->update_approve($data_post,$data_where);   
            }
            else
            {
                die("variable cannot be null");    
            }     
        }
        else
        {
            die("variable not define");
        }
    }
    
    function delete_approve()
    {
        $id = htmlentities(mysql_escape_string($this->uri->segment(4)));
        $companyCode = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        $data_where['PROJECT_ID'] = $id;
        $data_where['COMPANY_CODE']= $this->session->userdata('DCOMPANY'); 
        
        $data_post['Approved']='';
        $data_post['Approved_BY']='';
        $data_post['Approved_DATE']='';
        
        if(isset($id) && isset($companyCode) && isset($data_where) && isset($data_post))
        {
            if(!empty($id) && !empty($companyCode) && !empty($data_post) && !empty($data_where))
            {
                $approval = new appApproval;
                $approval->tblApprove="m_project";
                $approval->delete_approve($id,$data_where,$data_post);
            }
            else
            {
                die("variable cannot be null");    
            }    
        }
        else
        {
            die("variable not define");
        }
    }
}

?>