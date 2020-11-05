<?

class m_user extends Controller 
{
    
	function m_user ()
	{
		parent::Controller();	
		$this->load->model( 'model_m_user' );
		$this->load->model('model_c_user_auth');
		$this->lastmenu="m_user"; 
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
	}

    function index()
    {
		$data = array();
        $view = "info_m_user";
        $data['judul_header'] = "LHM User Management";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$data['LEVEL'] = $this->global_func->dropdownlist2("USER_LEVEL","m_user_group","USER_GROUP_NAME","USER_GROUP_ID",NULL,NULL, NULL,NULL,"select");
		$data['COMPANY_CODE'] = $this->global_func->dropdownlist2("COMPANY_CODE","m_company","COMPANY_NAME","COMPANY_CODE",NULL,NULL, NULL,NULL,"select");
	  
		$data['USER_DEPT'] = $this->dropdownlist_dept();
		
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
       
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
	}    
	
	function read_grid_user()
    {
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $level = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		
		if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->model_m_user->read_user($company, $level,$searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->model_m_user->read_user($company, $level);
	   }
	   
       echo json_encode($get);
    }
	
	function read_grid_co_role()
    {
		$user = $this->uri->segment(3);
        echo json_encode($this->model_m_user->setRole_list($user));
    }
	
	function read_grid_gc_role()
    {
		$user = $this->uri->segment(3);
        $company = $this->uri->segment(4);
		echo json_encode($this->model_m_user->getRole_gangcode($user, $company));
    }
	
   /*  function create( )
    {
        
		$this->load->library('form_validation');   
        
		            $this->form_validation->set_rules( 'LOGINID', lang('LOGINID'), 'required' );
					//$this->form_validation->set_rules( 'USER_FULLNAME', lang('USER_FULLNAME'), 'required' );
					//$this->form_validation->set_rules( 'USER_PASS', lang('USER_PASS'), 'required' );
					//$this->form_validation->set_rules( 'USER_MAIL', lang('USER_MAIL'), 'required' );
					//$this->form_validation->set_rules( 'USER_LEVEL', lang('USER_LEVEL'), 'required' );
					//$this->form_validation->set_rules( 'USER_DEPT', lang('USER_DEPT'), 'required' );
					//$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
				
    				$data['values']['LOGINID'] = set_value( 'LOGINID' );
					$data['values']['USER_FULLNAME'] = set_value( 'USER_FULLNAME' );
					$data['values']['USER_PASS'] = set_value( 'USER_PASS' );
					$data['values']['USER_MAIL'] = set_value( 'USER_MAIL' );
					$data['values']['USER_LEVEL'] = set_value( 'USER_LEVEL' );
					$data['values']['USER_DEPT'] = set_value( 'USER_DEPT' );
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'create'; 
                   
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    				$data_post['LOGINID'] = $this->input->post( 'LOGINID' );
					$data_post['USER_FULLNAME'] = $this->input->post( 'USER_FULLNAME' );
					$data_post['USER_PASS'] = md5($this->input->post( 'USER_PASS' ));
					$data_post['USER_MAIL'] = $this->input->post( 'USER_MAIL' );
					$data_post['USER_LEVEL'] = $this->input->post( 'USER_LEVEL' );
					$data_post['USER_DEPT'] = $this->input->post( 'USER_DEPT' );
					$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );
                    $insert_id = $this->model_m_user->insert_m_user( $data_post );
                }
              
    }

    function edit( $id )
    {
        	$this->load->library('form_validation'); 
				$this->form_validation->set_rules( 'USER_FULLNAME', lang('USER_FULLNAME'), 'required' );
				$this->form_validation->set_rules( 'USER_PASS', lang('USER_PASS'), 'required' );
				//$this->form_validation->set_rules( 'USER_MAIL', lang('USER_MAIL'), 'required' );
				$this->form_validation->set_rules( 'USER_LEVEL', lang('USER_LEVEL'), 'required' );
				//$this->form_validation->set_rules( 'USER_DEPT', lang('USER_DEPT'), 'required' );
				$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    
					$data['values']['USER_FULLNAME'] = set_value( 'USER_FULLNAME' );
					$data['values']['USER_PASS'] = set_value( 'USER_PASS' );
					$data['values']['USER_MAIL'] = set_value( 'USER_MAIL' );
					$data['values']['USER_LEVEL'] = set_value( 'USER_LEVEL' );
					$data['values']['USER_DEPT'] = set_value( 'USER_DEPT' );
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'edit'; 
                   
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
					$data_post['USER_FULLNAME'] = $this->input->post( 'USER_FULLNAME' );
					$data_post['USER_PASS'] = $this->input->post( 'USER_PASS' );
					$data_post['USER_MAIL'] = $this->input->post( 'USER_MAIL' );
					$data_post['USER_LEVEL'] = $this->input->post( 'USER_LEVEL' );
					$data_post['USER_DEPT'] = $this->input->post( 'USER_DEPT' );
					$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

				    $insert_id = $this->model_m_user->update_m_user( $id, $data_post );
    
                }
    
    }
	//---------------- delete -------------
	
	function delete($id)
	{
		$this->model_m_user->delete($id);
	}
		
	function company_access()
    {	
		$userid = $this->uri->segment(3);
        echo json_encode($this->model_m_user->setRole_list($userid));
    }
	
	function create_company_access( )
    {
        
		$data_post['USERID'] = $this->input->post( 'USERID' );
		$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

		$insert_id = $this->model_m_user->insert_m_user_co( $data_post );
              
    } */
	
	/* delete gangcode map */
	function delete_gc_role(){
		$id = $this->input->post( 'LOGINID' );
		$dc = $this->input->post( 'DETAIL_CODE' );
		$company = $this->input->post( 'COMPANY_CODE' );
		
		$this->model_m_user->delete_gc_role($id, $dc, $company);		
	}
	
	function insert_gc_role(){
		$id = $this->input->post( 'LOGINID' );
		$gc = $this->input->post( 'GANG_CODE' );
		$company = $this->input->post( 'COMPANY_CODE' );
		$role_gc = $this->model_m_user->cek_exist_gc_role($id,$gc,$company);
		$role_user = $this->model_m_user->cek_role($id,$company);
		$role = "";
		foreach($role_user as $row){
			$role = $row['USER_LEVEL'];
		}
		
		if($role == "SAD" || $role == "SAS"){
			echo "User ini adalah administrator site, tidak bisa dipetakan ke dalam kode kemandoran!!!";
		} else {
			if($role_gc > 0){
				echo "Data kemandoran sudah ada, mohon pilih kode kemandoran yang lain!!!";
			} else {
				$data_post['LOGINID'] = $id;
				$data_post['DETAIL_CODE'] = $gc;
				$data_post['COMPANY_CODE'] = $company;
				$data_post['DETAIL_TYPE'] = 'GANG_CODE';
				$this->model_m_user->insert_gc_role($data_post);
			}
		}
	}
	
	function dropdownlist_dept()
	{
		$string = "<select  name='USER_DEPT' class='select' id='USER_DEPT' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_dept = $this->model_m_user->get_dept();
		
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['DEPT_CODE']."\"  selected>".$row['DEPT_DESCRIPTION']." </option>";
			} else {
				$string = $string." <option value=\"".$row['DEPT_CODE']."\">".$row['DEPT_DESCRIPTION']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
}

?>