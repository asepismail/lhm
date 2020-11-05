<?php
class m_bloktanam extends Controller
{
    function m_bloktanam()
    {
        parent::Controller();
        $this->load->model('model_m_bloktanam');
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_bloktanam";
        
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
        $view = "info_m_bloktanam";
        $data = array();
        $data['judul_header'] = "Master Data Blok";
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
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_bloktanam->LoadData($company));
    }
    
    function AddNew()
    {
        $this->load->library('form_validation');
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $afd = substr(trim(htmlentities($this->input->post('BLOCKID'),ENT_QUOTES,'UTF-8')),0,2); 
        $act="add";
        
        $datapost['FIELDCODE'] = htmlentities($this->input->post('FIELDCODE'),ENT_QUOTES,'UTF-8');
        $datapost['FIELDCODECONV']=htmlentities($this->input->post('FIELDCODECONV'),ENT_QUOTES,'UTF-8');
        $datapost['BLOCKID']=htmlentities($this->input->post('BLOCKID'),ENT_QUOTES,'UTF-8');
        $datapost['ESTATECODE']=$afd;
        $datapost['DESCRIPTION']=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $datapost['HECTPLANTED']=htmlentities($this->input->post('HECTPLANTED'),ENT_QUOTES,'UTF-8');
        $datapost['HECTPLANTABLE']=htmlentities($this->input->post('HECTPLANTABLE'),ENT_QUOTES,'UTF-8');
        $datapost['CROPSSTATUS']=htmlentities($this->input->post('CROPSSTATUS'),ENT_QUOTES,'UTF-8');
        $datapost['NUMPLANTATION']=htmlentities($this->input->post('NUMPLANTATION'),ENT_QUOTES,'UTF-8');
        $datapost['YEARREPLANT']=htmlentities($this->input->post('YEARREPLANT'),ENT_QUOTES,'UTF-8');
        $datapost['COMPANY_CODE']=$company;
        $datapost['Input_By']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['Input_Date']=date ("Y-m-d H:i:s");
        if(isset($act))
        {$datapost['Action'] =$act;}
        else
        {$datapost['Action'] ="";}
        
        $fieldcode = htmlentities($this->input->post('FIELDCODE'),ENT_QUOTES,'UTF-8');
        $desc = htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        
        if(isset($id) && !empty($id))
        {
            $data_exist=$this->model_m_bloktanam->cek_exist_data($id,$company);
            if ($data_exist ==0)
            {
                if (isset($datapost))
                {
                    if (!empty($datapost['FIELDCODE']))
                    {
                        $insert=$this->model_m_bloktanam->AddNew($datapost);
                        if ($insert==0)
                        {
                            if (isset($fieldcode))
                            {
                               $insert_ot=$this->model_m_bloktanam->AddToOther($fieldcode,$desc,$company); 
                            }
                            
                        }
                    }
                    else
                    {
                        echo "Input tidak lengkap";
                    }
                }
                else
                {
                    echo ("kesalahan dalam input");
                }
                
            }
            elseif($data_exist>0)
            {
                $status="Data telah terdapat di dalam database";
                echo $status;
            }   
        }
        
        
    }
    
    function EditData()
    {
        $this->load->library('form_validation');
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8') ;
        $afd = substr(trim(htmlentities($this->input->post('BLOCKID'),ENT_QUOTES,'UTF-8')),0,2); 
        $act="edit";
        
        $datapost['FIELDCODE'] = htmlentities($this->input->post('FIELDCODE'),ENT_QUOTES,'UTF-8');
        $datapost['FIELDCODECONV']=htmlentities($this->input->post('FIELDCODECONV'),ENT_QUOTES,'UTF-8');
        $datapost['BLOCKID']=htmlentities($this->input->post('BLOCKID'),ENT_QUOTES,'UTF-8');
        $datapost['ESTATECODE']=$afd;
        $datapost['DESCRIPTION']=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $datapost['HECTPLANTED']=htmlentities($this->input->post('HECTPLANTED'),ENT_QUOTES,'UTF-8');
        $datapost['HECTPLANTABLE']=htmlentities($this->input->post('HECTPLANTABLE'),ENT_QUOTES,'UTF-8');
        $datapost['CROPSSTATUS']=htmlentities($this->input->post('CROPSSTATUS',ENT_QUOTES,'UTF-8'));
        $datapost['NUMPLANTATION']=htmlentities($this->input->post('NUMPLANTATION'),ENT_QUOTES,'UTF-8');
        $datapost['YEARREPLANT']=htmlentities($this->input->post('YEARREPLANT'),ENT_QUOTES,'UTF-8');
        $datapost['COMPANY_CODE']=$company;
        $datapost['Input_By']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['Input_Date']=date ("Y-m-d H:i:s");
        if (isset($act))
        {
           $datapost['Action'] =$act; 
        }
        else
        {
            $datapost['Action'] ="";
        }

        if (isset($id) && isset($datapost))
        {
            $update=$this->model_m_bloktanam->EditData($id,$company,$datapost);
            $insert_ot=$this->model_m_bloktanam->AddToOther($id,$datapost['DESCRIPTION'],$company);  //insert ke dalam table m_location
            echo ("data telah terUpdate");   
        }
        else
        {
            echo ("kesalahan dalam input");
        }
        
    }
    
    function DelData()
    {
        $id=htmlentities($this->uri->segment(3));
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        if ($id !="" & isset($id))
        {
            $delete=$this->model_m_bloktanam->DelData($id,$company);
            $delete_ot=$this->model_m_bloktanam->DelToOther($id,$company);
        }
    }
	
	function SyncData(){
		$block=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $afd=htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
		$all=htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
		
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company_adem = $this->model_m_bloktanam->getCompanyIDAdem($company);

		$return_sync_fieldCrop =$this->model_m_bloktanam->SyncFieldCrop($company_adem, $company, $afd, $block, $all);
		$return_sync_location =$this->model_m_bloktanam->SyncLocation($company_adem, $company, $afd, $block, $all);
    }
	
    function SearchData()
    {
        $code=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $desc=htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        if ($code =="" and $desc=="")
        {
            echo json_encode($this->model_m_bloktanam->LoadView($company));
        }
        else{
            echo json_encode($this->model_m_bloktanam->src_data(strtoupper($code),$desc,$company));
        }
        
    }
    
    function create_excel()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
              
        $this->db->select('FIELDCODE,FIELDCODECONV,BLOCKID,ESTATECODE,DESCRIPTION,HECTPLANTED,HECTPLANTABLE
                    ,CROPSSTATUS,NUMPLANTATION,YEARREPLANT AS TAHUN_TANAM, CASE WHEN INACTIVE = 0 THEN "AKTIF" ELSE "INAKTIF" END AS STATUS, COMPANY_CODE');
     
        $this->db->where('m_fieldcrop.COMPANY_CODE', $company);  
          $query = $this->db->get('m_fieldcrop');              
            
        to_excel($query,'BLOK_'.$company);
        //redirect( 'm_gang_activity_detail/' );
        if ($query->num_rows() == 0) 
        {
            redirect( 'm_bloktanam/' );
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
        $id= htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $uName = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $companyCode = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['Approved']='1';
        $data_post['Approved_BY']=$uName;
        $data_post['Approved_DATE']=date ("Y-m-d H:i:s");
        
        $data_where['FIELDCODE'] = $id;
        $data_where['COMPANY_CODE']= $companyCode;
        
        $approval = new appApproval;
        $approval->tblApprove="m_fieldcrop";
        if (isset($data_post) && isset($data_where))
        {
           $approval->update_approve($data_post,$data_where);
           $this->model_m_bloktanam->UpdateApprMloc($id,$companyCode,"OP",1); 
        }
        else
        {
            echo "kesalahan dalam input";
        }
    }
    function delete_approve()
    {
        $id = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $companyCode = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_where['FIELDCODE'] = $id;
        $data_where['COMPANY_CODE']= $companyCode;
        
        $data_post['Approved']='';
        $data_post['Approved_BY']='';
        $data_post['Approved_DATE']='';
        
        $approval = new appApproval;
        $approval->tblApprove="m_fieldcrop";
        $approval->delete_approve($id,$data_where,$data_post);
        $this->model_m_bloktanam->UpdateApprMloc($id,$companyCode,"OP",0);
    }
}
?>