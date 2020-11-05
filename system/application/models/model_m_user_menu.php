<?

class Model_m_user_menu extends Model 
{

    function Model_m_user_menu()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_user_menu ( $id )
	{

		$this->db->select( 'MENU_ID,MENU_NAME,MENU_PARENT,MENU_URL,MENU_TYPE' );
		$this->db->where( 'MENU_ID', $id );
		$this->db->from('m_user_menu');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_user_menu ( $data )
	{
		$this->db->insert( 'm_user_menu', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_user_menu ( $id, $data )
	{
		$this->db->where( 'MENU_ID', $id );  
		$this->db->update( 'm_user_menu', $data );   
	}
	
	function enroll_m_user_menu ( )
	{
		$this->db->select( 'MENU_ID,MENU_NAME,MENU_PARENT,MENU_URL,MENU_TYPE');

		$this->db->from( 'm_user_menu' );

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		return $temp_result;
	}
	
	function menu_parent ($usr)
	{
		$query = $this->db->query("SELECT distinct
 				 m_user_menu.`MENU_ID`,
 				 m_user_menu.`MENU_NAME`,
 				 m_user_menu.`MENU_PARENT`,
 				 m_user_menu.`MENU_URL`,
 				 usr.`COMPANY_CODE`
				 FROM
 				 `m_user` usr
				left join `m_user_group` on (`m_user_group`.`USER_GROUP_ID` = usr.`USER_LEVEL`)
				left join `m_user_grole` on (m_user_grole.`GROUP_ID` = m_user_group.USER_GROUP_ID)
				left join `m_user_menu` on (m_user_menu.`MENU_ID` = m_user_grole.`MENU_ID`)
				where m_user_menu.`MENU_PARENT` IS NULL
				AND `usr`.`LOGINID` = '".$usr."'");

		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}

		return $temp_result;
	}
	
	function menu_child ($usr,$parent)
	{
		$query = $this->db->query("
		SELECT distinct 
 		m_user_menu.`MENU_ID`,
 	 	m_user_menu.`MENU_NAME`,
  		m_user_menu.`MENU_PARENT`,
  		m_user_menu.`MENU_URL`,
  		usr.`COMPANY_CODE`
		FROM
  		`m_user` usr
		left join `m_user_group` on (`m_user_group`.`USER_GROUP_ID` = usr.`USER_LEVEL`)
		left join `m_user_grole` on (m_user_grole.`GROUP_ID` = m_user_group.USER_GROUP_ID)
		left join `m_user_menu` on (m_user_menu.`MENU_ID` = m_user_grole.`MENU_ID`)
		where
		m_user_menu.`MENU_PARENT` IS NOT NULL
		AND m_user_menu.`MENU_PARENT` = '".$parent."'
		AND `usr`.`LOGINID` = '".$usr."'");

		$temp_result = array();
		
		$leafnodes = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			$leafnodes [$row['MENU_ID']] = $row['MENU_ID'];
			
		}

		return $temp_result;
		return $leafnodes;
	}
	
	 function cek_user_menu($usr){
		$query = $this->db->query("SELECT `m_user_co`.`COMPANY_CODE`, m_company.COMPANY_NAME
		FROM `m_user_co` 
		left join m_user on (m_user.`LOGINID` = m_user_co.`USERID`) 
		left join m_company on (m_company.`COMPANY_CODE` = m_user_co.`COMPANY_CODE`)
		where m_user.`LOGINID` = '".$usr."'");
		
		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}

		return $temp_result;
	}   


}   

?>
