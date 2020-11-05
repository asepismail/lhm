<?

class M_user_menu extends Controller 
{
    
	function M_user_menu ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_user_menu' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->database();
		$this->load->library('session');
	
	}

    function index()
    {
		echo anchor( 'M_user_menu/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

	function menu_list( )
	{
		
		$data_enroll = $this->model_m_user_menu->menu_parent();
		//print_r($data_enroll);
		if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	  		header("Content-type: application/xhtml+xml;charset=utf-8"); } 
		else {
	  		header("Content-type: text/xml;charset=utf-8");
		}
		$et = ">";
		echo "<?xml version='1.0' encoding='utf-8'?$et\n";
		echo "<rows>";
		echo "<page>1</page>";
		echo "<total>1</total>";
		echo "<records>1</records>";
		
		foreach( $data_enroll as $row)
		{
			echo "<row>";			
			echo "<cell></cell>";
			echo "<cell>". $row['MENU_NAME']."</cell>";
			echo "<cell>". $row['MENU_URL']."</cell>";
			echo "<cell>". $n_lvl."</cell>";
			if(!$row['MENU_PARENT']) $valp = 'NULL'; else $valp = $row['MENU_PARENT']; 
			echo "<cell><![CDATA[".$valp."]]></cell>";
			if($row['MENU_ID'] == $leafnodes[$row['MENU_ID']]) $leaf='true'; else $leaf = 'false';
			echo "<cell>".$leaf."</cell>";
			echo "<cell>false</cell>";
			echo "</row>";			
		}
		
		echo "</rows>";
	}
	
	
	function menu_list2( )
	{
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company'] = $this->session->userdata('company');
		$data['level'] = $this->session->userdata('level');
		
		if ($data['login_id'] == TRUE){
	
		
			$data_enroll = $this->model_m_user_menu->menu_parent($data['login_id']);
			//	print_r($data_enroll);
			//print_r($data_enroll);
			if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
		  		header("Content-type: application/xhtml+xml;charset=utf-8"); } 
			else {
		  		header("Content-type: text/xml;charset=utf-8");
			}
			$et = ">";
			echo "<?xml version='1.0' encoding='utf-8'?$et\n";
			echo "<rows>";
			echo "<page>1</page>";
			echo "<total>1</total>";
			echo "<records>1</records>";
			
			foreach($data_enroll as $row)
			{
				echo "<row>";			
				echo "<cell>".$row['MENU_ID']."</cell>";
				echo "<cell>". $row['MENU_NAME']."</cell>";
				echo "<cell></cell>";
				echo "<cell>0</cell>";
				echo "<cell>".$row['MENU_ID']."</cell>";
				$level = $row['MENU_ID'] + '99';
				echo "<cell>".$level."</cell>";
				echo "<cell>false</cell>";
				echo "<cell>false</cell>";
				echo "</row>";
				
				$parent = $row['MENU_ID'];
				$data_enroll2 = $this->model_m_user_menu->menu_child($data['login_id'], $parent);
				foreach($data_enroll2 as $rows)
				
				{
					//echo $rows['MENU_ID']."<br/>";
					echo "<row>";			
					echo "<cell>".$rows['MENU_ID']."</cell>";
					echo "<cell>". $rows['MENU_NAME']."</cell>";
					$url = base_url()."index.php/";
					echo "<cell>". $url.$rows['MENU_URL']."</cell>";
					echo "<cell>1</cell>";
					//if(!$row['MENU_PARENT']) $valp = 'NULL'; else $valp = $row['MENU_PARENT']; 
					echo "<cell>".$rows['MENU_ID']."</cell>";
					$level2 = $rows['MENU_ID'] + '8';
					echo "<cell>".$level2."</cell>";
					//if($row['MENU_ID'] == $leafnodes[$row['MENU_ID']]) $leaf='true'; else $leaf = 'false';
					echo "<cell>true</cell>";
					echo "<cell>false</cell>";
					echo "</row>";
				}	
					
			}
			
					echo "<row>";			
					echo "<cell>500</cell>";
					echo "<cell>Dashboard</cell>";
					echo "<cell>". base_url().'index.php/c_dashboard/'."</cell>";
					echo "<cell>0</cell>";
					echo "<cell>500</cell>";
					echo "<cell>508</cell>";
					//if($row['MENU_ID'] == $leafnodes[$row['MENU_ID']]) $leaf='true'; else $leaf = 'false';
					echo "<cell>true</cell>";
					echo "<cell>true</cell>";
					echo "</row>";
					
			echo "</rows>";
			
				
		} else {
			redirect('login');
		}
		
		
	}
	
	function cek_company() {
		$usr = $this->uri->segment(3);
		$data_enroll = $this->model_m_user_menu->cek_user_menu($usr);
		
		$data = array();
		foreach($data_enroll as $row)
			{
				$data[] = array("id"=>($row['COMPANY_CODE']) , "name"=>($row['COMPANY_NAME']));
				//echo "<option value='".$row['COMPANY_CODE']."'>";	
			}
		$storeData = json_encode($data);
		echo $storeData;

			
	}
	
	
    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
				$data['values']['MENU_NAME'] = '';
				$data['values']['MENU_PARENT'] = '';
				$data['values']['MENU_URL'] = '';
				$data['values']['MENU_TYPE'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_user_menu.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'MENU_NAME', lang('MENU_NAME'), 'required' );
				//$this->form_validation->set_rules( 'MENU_PARENT', lang('MENU_PARENT'), 'required' );
				//$this->form_validation->set_rules( 'MENU_URL', lang('MENU_URL'), 'required' );
				//$this->form_validation->set_rules( 'MENU_TYPE', lang('MENU_TYPE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
					$data['values']['MENU_NAME'] = set_value( 'MENU_NAME' );
					$data['values']['MENU_PARENT'] = set_value( 'MENU_PARENT' );
					$data['values']['MENU_URL'] = set_value( 'MENU_URL' );
					$data['values']['MENU_TYPE'] = set_value( 'MENU_TYPE' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_user_menu.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
					$data_post['MENU_NAME'] = $this->input->post( 'MENU_NAME' );
					$data_post['MENU_PARENT'] = $this->input->post( 'MENU_PARENT' );
					$data_post['MENU_URL'] = $this->input->post( 'MENU_URL' );
					$data_post['MENU_TYPE'] = $this->input->post( 'MENU_TYPE' );

    
                    $insert_id = $this->model_m_user_menu->insert_m_user_menu( $data_post );
                    
					redirect( 'M_user_menu/info/' . $insert_id );
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

    function edit( $id = '' )
    {
        
		$this->load->library('session');
        $this->load->library('form_validation'); 

        switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values'] = $this->model_m_user_menu->Info_m_user_menu( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_user_menu.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'MENU_NAME', lang('MENU_NAME'), 'required' );
$this->form_validation->set_rules( 'MENU_PARENT', lang('MENU_PARENT'), 'required' );
$this->form_validation->set_rules( 'MENU_URL', lang('MENU_URL'), 'required' );
$this->form_validation->set_rules( 'MENU_TYPE', lang('MENU_TYPE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['MENU_NAME'] = set_value( 'MENU_NAME' );
$data['values']['MENU_PARENT'] = set_value( 'MENU_PARENT' );
$data['values']['MENU_URL'] = set_value( 'MENU_URL' );
$data['values']['MENU_TYPE'] = set_value( 'MENU_TYPE' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_user_menu.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['MENU_NAME'] = $this->input->post( 'MENU_NAME' );
$data_post['MENU_PARENT'] = $this->input->post( 'MENU_PARENT' );
$data_post['MENU_URL'] = $this->input->post( 'MENU_URL' );
$data_post['MENU_TYPE'] = $this->input->post( 'MENU_TYPE' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_user_menu->update_m_user_menu( $id, $data_post );
    
					redirect( 'M_user_menu/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>