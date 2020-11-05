<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class m_machine extends Controller
{
    function m_machine()
    {
        parent::Controller();
        $this->load->model('model_m_machine');
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_machine";
        
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->plugin('to_excel');
        
        $this->load->library('approval/appApproval');
        $this->load->library('form/formheader');
    }
    
    function index()
    {
        $viewPath='Project/';
        $view = "info_m_machine";
        
        $data = array();
        $data['judul_header'] = "Data Master Alat / Mesin";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['htmlapprove']=$this->approve_html();
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
        
        if ($data['login_id'] == TRUE && $data['user_level']){
                show($view, $data);
        } else {
                redirect('login');
        }
        
        /*$viewPath='Project/';
        $viewName ='info_PrjPengajuan';
        
        $view = "info_m_machine";
        $data = array();
        $data['judul_header'] = "Data Master Alat / Mesin";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['htmlapprove']= $this->approve_html();
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$this->lastmenu);  
        
        if ($data['login_id'] == TRUE && $data['user_level']){                  
                show($view, $data);
        } else {
                redirect('login');
        }  */
    }
    function LoadData()
    {
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_machine->LoadData($company));
    }
    
    function AddNew()
    {
        $this->load->library('form_validation');
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company= htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $act="add";          
        $data_post['MACHINECODE']=htmlentities($this->input->post('MACHINECODE'),ENT_QUOTES,'UTF-8');
        $data_post['DESCRIPTION']=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $data_post['OWNERSHIP']=htmlentities($this->input->post('OWNERSHIP'),ENT_QUOTES,'UTF-8');
        $data_post['SATUAN_PRESTASI']=htmlentities($this->input->post('SATUAN_PRESTASI'),ENT_QUOTES,'UTF-8');
        $data_post['Input_By']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        //$data_post['Input_Date']=date ("Y-m-d H:i:s");
        if (isset($act))
        {$data_post['Action'] =$act;}
        else
        {$data_post['Action'] ="";}
        
        $data_post['COMPANY_CODE']=htmlentities($this->input->post('COMPANY_CODE'),ENT_QUOTES,'UTF-8');
        $desc=$this->input->post('DESCRIPTION');
        
        $data_exist = $this->model_m_machine->cek_exist_machine($id,$company);
        if($data_exist > 0)
        {
            echo("data kendaraan telah terdapat di dalam database");
        }
        else
        {
            if (isset($data_post) && !empty($data_post['MACHINECODE']))
            {
                $insert_new=$this->model_m_machine->insert_new_machine($data_post);
                if ($insert_new==0)
                {
                    $insert_ot=$this->model_m_machine->AddToOther($id,$desc,$company);
                }
                echo "1";   
            }
            else
            {
                echo ("input tidak lengkap");
            }
            
        }
    }
    
    function EditData()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $act ="edit";
        $data_post['MACHINECODE']=htmlentities($this->input->post('MACHINECODE'),ENT_QUOTES,'UTF-8');
        $data_post['DESCRIPTION']=htmlentities($this->input->post('DESCRIPTION'),ENT_QUOTES,'UTF-8');
        $data_post['OWNERSHIP']=htmlentities($this->input->post('OWNERSHIP'),ENT_QUOTES,'UTF-8');
        $data_post['SATUAN_PRESTASI']=htmlentities($this->input->post('SATUAN_PRESTASI'),ENT_QUOTES,'UTF-8');
        $data_post['COMPANY_CODE']=htmlentities($this->input->post('COMPANY_CODE'),ENT_QUOTES,'UTF-8');
        $data_post['Input_By']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data_post['Input_Date']=date ("Y-m-d H:i:s");
        if (isset($act))
        {$data_post['Action']=$act; }
        else
        {$data_post['Action']=""; }
        
        if (isset($data_post))
        {
            $update_data=$this->model_m_machine->update_machine($id,$company,$data_post); 
            $insert_ot=$this->model_m_machine->AddToOther($id,$data_post['DESCRIPTION'],$company);
            echo ("data telah terUpdate");
        }
        else
        {
            echo ("kesalahan dalam input");
        }
        
    }
    
    function DelData()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        if (isset($id) && isset($company))
        {
            $delete_data=$this->model_m_machine->DelData($id,$company);
            $delete_ot=$this->model_m_machine->DelToOther($id,$company);
        }
        else
        {
            echo ("kesalahan dalam input");
        }
    }
    
    function SearchData()
    {
        $code=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $desc=htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $company=$this->session->userdata('DCOMPANY');
        
        if ($code =="" and $desc=="")
        {
            echo json_encode($this->model_m_machine->LoadData($company));
        }
        else{
            echo json_encode($this->model_m_machine->src_data($code,$desc,$company));
        }
    }
    
    function create_excel()
    {
        $company = $this->session->userdata('DCOMPANY');
              
        $this->db->select('MACHINECODE,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,Approved,Approved_By,Approved_Date');
     
        $this->db->where('m_machine.COMPANY_CODE', $company);  
          $query = $this->db->get('m_machine');              
            
        to_excel($query,'LHM_'.$company);
        //redirect( 'm_gang_activity_detail/' );
        if ($query->num_rows() == 0) 
        {
            redirect( 'm_infras/' );
        } 
    }
    
    function cek_approve()
    {
        $approval = new appApproval;
        
        $cekapprove=$approval->cek_approved();
        return $cekapprove;
    }
     
    function approve_html()
    {
        $approval=$this->cek_approve();
        if($approval>0)
        {
            $html[0]="<li id='fragment_2' style='display:inline'><a href='#fragment-2'><span>Pengesahan</span></a></li>\n";
            $html[1]="<div id='fragment-2' style='display:inline'>
            <table width='100%' class='teks_'>            
                    <tr>
                        <td align='left' width='125'>Approve</td>
                        <td align='left'>:</td>
                        <td><input name='i_ck_approve' type='checkbox' id='i_ck_approve' tabindex='1'/></td>
                    </tr>                    
                    <!--<input tabindex='17' type='button' id='saveapproval' value='Simpan' onclick='' style='display:none'> -->
                    <tr>
                        <td colspan='5'>
                            <hr>
                            <div align='right'>
                                <input type='button' id='submitapprove' value='Simpan' onclick='save_approval()' tabindex='7'>
                            </div>
                                                                                                                             
                        </td>
                    </tr>
            </table>
        </div>\n";    
        }
        else{
            $html[0]="\n";
            $html[1]="\n";
        }
        return $html; 
    }
    
    function update_approve()
    {
        $id= htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $uName = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $companyCode = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['Approved']='1';
        $data_post['Approved_BY']=$uName;
        $data_post['Approved_DATE']=date ("Y-m-d H:i:s");
        
        $data_where['MACHINECODE'] = $id;
        $data_where['COMPANY_CODE']= $companyCode;
        
        $approval=$this->cek_approve();
        if($approval>0)
        {
            if (isset($id) && isset($companyCode) && isset($data_post) && isset($data_where))
            {
                if(!empty($id) && !empty($companyCode) && !empty($data_post) && !empty($data_where))
                {
                     $approval = new appApproval;
                     $approval->tblApprove="m_machine";
                     $approval->update_approve($data_post,$data_where);
                     $this->model_m_machine->UpdateApprMloc($id,$companyCode,"MA",1);
                     echo "1";    
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
        }else{
            echo "anda tidak memiliki hak pengesahan";
        }
        
    }
    
    function delete_approve()
    {
        $id = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $companyCode = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_where['MACHINECODE'] = $id;
        $data_where['COMPANY_CODE']= $this->session->userdata('DCOMPANY'); 
        
        $data_post['Approved']='';
        $data_post['Approved_BY']='';
        $data_post['Approved_DATE']='';
        
        if(isset($id) && isset($companyCode) && isset($data_where) && isset($data_post))
        {
            if(!empty($id) && !empty($companyCode) && !empty($data_post) && !empty($data_where))
            {
                $approval = new appApproval;
                $approval->tblApprove="m_machine";
                $cekapproval=$this->cek_approve();
                if($cekapproval>0)
                {
                    $approval->delete_approve($id,$data_where,$data_post);    
                }
                
                $this->model_m_machine->UpdateApprMloc($id,$companyCode,"MA",0);
                echo "1";    
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