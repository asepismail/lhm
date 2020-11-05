<?php
class m_infras extends Controller
{
    function m_infras ()
    {
        parent::Controller();
        $this->load->model('model_m_infras');
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_infras";
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->plugin('to_excel');      
        $this->load->library('./approval/appApproval');
    }
	
    function index()
    {
        $viewPath='./Project/';
        $viewName ='info_PrjPengajuan';
        
        $view = "info_m_infras";
        $data = array();
        $data['judul_header'] = "Data Master Infrastruktur";
        $data['js'] = "";
        
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['jenisaktiva'] = $this->dropdownlist_jenisaktiva();
		$data['afd'] = $this->dropdownlist_afd();
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
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
        echo json_encode($this->model_m_infras->LoadData($company));
    }
    
    function AddNew()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $desc=$this->input->post('IFNAME');
        $act="add";
        
        $datapost['IFCODE']=htmlentities($this->input->post('IFCODE'),ENT_QUOTES,'UTF-8');
        $datapost['FIXEDASSETCODE']=htmlentities($this->input->post('FIXEDASSETCODE'),ENT_QUOTES,'UTF-8');
        $datapost['IFTYPE']=htmlentities($this->input->post('IFTYPE'),ENT_QUOTES,'UTF-8');
        $datapost['IFSUBTYPE']=htmlentities($this->input->post('IFSUBTYPE'),ENT_QUOTES,'UTF-8');
        $datapost['IFNAME']=htmlentities($this->input->post('IFNAME'),ENT_QUOTES,'UTF-8');
        $datapost['ESTATE']=htmlentities($this->input->post('ESTATE'),ENT_QUOTES,'UTF-8');
        $datapost['IF_LOCATION']=htmlentities($this->input->post('IF_LOCATION'),ENT_QUOTES,'UTF-8');
		$datapost['INACTIVE']=htmlentities($this->input->post('INACTIVE'),ENT_QUOTES,'UTF-8');
        $datapost['ISAPPR_RM']=htmlentities($this->input->post('ISAPPR_RM'),ENT_QUOTES,'UTF-8');
        $datapost['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $datapost['Input_By']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['Input_Date']=date ("Y-m-d H:i:s");
        if(isset($act))
        {
            $datapost['Action'] =$act;    
        }
        else
        {
            $datapost['Action'] ="";
        }
        
        if (!empty($datapost['IFCODE']))
        {
            if(isset($id) && !empty($id))
            {
                $dataexist=$this->model_m_infras->cek_exist_data($id,$company,1);
                if ($dataexist==0)
                {
                    if (isset($datapost))
                    {
                        $insert=$this->model_m_infras->AddNew($datapost);
                        if ($insert==0)
                        {
                            $dataexist=$this->model_m_infras->cek_exist_data($id,$company,2);
                            if ($dataexist==0)
                            {$insertother=$this->model_m_infras->AddToOther($id,$desc,$company);}
                        } 
                    }
                    else
                    {
                        echo "kesalahan dalam input";
                    }
                }
                else
                {
                    echo "data telah terdapat di dalam database";
                }  
            }
            
        }
        else
        {
            echo "input tidak lengkap";
        }
    }
	
    function EditData()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $act ="edit";
        
        //$datapost['IFCODE']=htmlentities($this->input->post('IFCODE'),ENT_QUOTES,'UTF-8');
        $datapost['FIXEDASSETCODE']=htmlentities($this->input->post('FIXEDASSETCODE'),ENT_QUOTES,'UTF-8');
        $datapost['IFTYPE']=htmlentities($this->input->post('IFTYPE'),ENT_QUOTES,'UTF-8');
        $datapost['IFSUBTYPE']=htmlentities($this->input->post('IFSUBTYPE'),ENT_QUOTES,'UTF-8');
        $datapost['IFNAME']=htmlentities($this->input->post('IFNAME'),ENT_QUOTES,'UTF-8');
        $datapost['ESTATE']=htmlentities($this->input->post('ESTATE'),ENT_QUOTES,'UTF-8');
        $datapost['IF_LOCATION']=htmlentities($this->input->post('IF_LOCATION'),ENT_QUOTES,'UTF-8');
		$datapost['INACTIVE']=htmlentities($this->input->post('INACTIVE'),ENT_QUOTES,'UTF-8');
		$datapost['ISAPPR_RM']=htmlentities($this->input->post('ISAPPR_RM'),ENT_QUOTES,'UTF-8');
		echo $datapost['ISAPPR_RM'];
        $datapost['Input_By']=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $datapost['Input_Date']=date ("Y-m-d H:i:s");
        if(isset($act))
        {
            $datapost['Action'] =$act;    
        }
        else
        {
            $datapost['Action'] ="";
        }
        
        $datapost['COMPANY_CODE']=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        if (!empty($id) && isset($datapost))
        {
            $editdata=$this->model_m_infras->EditData($id,$company,$datapost);  
            $insertother=$this->model_m_infras->AddToOther($id,$datapost['IFNAME'],$company); 
        }
        
    }
    function DeleteData()
    {
        $id=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        if (!empty($id) && isset($id))
        {
            $deldata=$this->model_m_infras->DeleteData($id,$company);  
            $delete_ot=$this->model_m_vehicle->DelToOther($id,$company); 
        }
        
    }
	
	function dropdownlist_jenisaktiva()
	{
	
		$string = "<select  name='i_if_facode' class='select' id='i_if_facode' style='width:190px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		
		$data_afd = $this->model_m_infras->get_fixedasset();
		
		foreach ( $data_afd as $row)
		{
			if( (isset($default)) && ($default==$row[$nama_isi]) )
			{
				$string = $string." <option value=\"".$row['IFTYPE']."\"  selected>".$row['IFTYPE_NAME']." </option>";
			}
			else
			{
				$string = $string." <option value=\"".$row['IFTYPE']."\">".$row['IFTYPE_NAME']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_afd()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='i_if_estate' class='select' id='i_if_estate' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		
		$data_afd = $this->model_m_infras->get_afd($company);
		
		foreach ( $data_afd as $row)
		{
			if( (isset($default)))
			{
				$string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_DESC']." </option>";
			}
			else
			{
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
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
        
		$data_afd = $this->model_m_infras->get_ifcode($sData);
        foreach ($data_afd as $drow){
			$array[] = array('kt' => $drow['IFSUBTYPE_NAME'], 'kt2' => $drow['IFSUBTYPE'] );
		}
        echo json_encode($array);
    }
        
    function SearchData()
    {
        $code=htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $desc=htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        if ($code =="" and $desc=="")
        {
            echo json_encode($this->model_m_infras->LoadData($company));
        }
        else{
            echo json_encode($this->model_m_infras->src_data($code,$desc,$company));
        }    
    }
    
    function create_excel()
    {
        $company = $this->session->userdata('DCOMPANY');
              
        $sQuery ="SELECT DISTINCT IFCODE, IFNAME, mi.IFTYPE, mit.IFTYPE_NAME, mi.IFSUBTYPE, mis.IFSUBTYPE_NAME, ESTATE,IF_LOCATION, 
					IFLENGTH, IFWIDTH, UOM, INSTALLDATE, VOLUME, DEVELOPMENT_COST, mi.COMPANY_CODE, 
					CASE WHEN mi.INACTIVE = 0 THEN 1 ELSE 0 END AS ACTIVE, 
					CASE WHEN mloc.INACTIVE = 1 THEN 0 ELSE 1 END AS ISAPPR_RM 
					FROM m_infrastructure mi 
					LEFT JOIN m_infrastructure_type mit ON mit.IFTYPE = mi.IFTYPE 
					LEFT JOIN m_infrastructure_subtype mis ON mis.IFSUBTYPE = mi.IFSUBTYPE 
					LEFT JOIN ( SELECT LOCATION_CODE, INACTIVE, COMPANY_CODE FROM m_location WHERE COMPANY_CODE = '".$company."'
							AND LOCATION_TYPE_CODE = 'IF' ) mloc ON mloc.LOCATION_CODE = mi.IFCODE
					WHERE mi.COMPANY_CODE='".$company."'"; 
     
        $query = $this->db->query($sQuery);           
            
        to_excel($query,'LHM_'.$company);
        //redirect( 'm_gang_activity_detail/' );
        if ($query->num_rows() == 0) 
        {
            redirect( 'm_infras/' );
        } 
    }
    
  	/* detail pengajuan RM */
  	function LoadDataRM()
    {
        $company=$this->session->userdata('DCOMPANY');
        $periode= $this->uri->segment(3);
        echo json_encode($this->model_m_infras->LoadDataRM($company, $periode));
    }
  	/* end detail pengajuan RM */ 
  	
  	function SyncDataInfras(){
		$user = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company_adem = $this->model_m_infras->getCompanyIDAdem($company);
		$cek = $this->model_m_infras->doSyncInfras($company, $user);
		echo $cek;
		/* $json = array();
		foreach($cek as $c){
			$json[] = $c;
		}
		echo $json_data=json_encode($json); */
		
		//$return_sync_fieldCrop =$this->model_m_infras->SyncFieldCrop($company_adem, $company, $afd, $block, $all);
		//$return_sync_location =$this->model_m_infras->SyncLocation($company_adem, $company, $afd, $block, $all);
    }
}
?>
