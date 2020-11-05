<?php
class model_c_user_auth extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /*  -- menu template lama */
	
	function get_menu_satu($loginid,$usr_level,$company,$lastmenu) {
        $node_tbl='';
        $query="SELECT * FROM m_user_menu_list where LOGINID='".$loginid."'";
        $sQuery = $this->db->query($query);
        
        $where =" AND 1=1 ";
        if($sQuery->num_rows() > 1){
			/*
            $node_tbl="SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, (COUNT(parent.MENU_NAME) - 1) AS depth
             FROM m_user_menu AS node
             INNER JOIN (SELECT * FROM m_user_menu_list where LOGINID='".$loginid."')mulist ON mulist.MENU_ID = node.MENU_ID
             CROSS JOIN m_user_menu AS parent
             WHERE node.LFT BETWEEN parent.LFT AND parent.RGT
             GROUP BY node.MENU_NAME
             ORDER BY node.LFT";
			 */
			 $node_tbl="SELECT menu.MENU_NAME, menu.MENU_URL, menu.MENU_PARENT, menu.MENU_ID, 
						CASE menu.menu_id 
							WHEN 001 THEN 0
							ELSE 
								CASE (menu.length_menu) WHEN 3 THEN 1 WHEN 5 THEN 2 ELSE 3 END	
							END AS depth
 						FROM(
							SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, 
							LENGTH(node.MENU_ID) AS length_menu
							FROM m_user_menu AS node 
							INNER JOIN (
											SELECT * 
											FROM m_user_menu_list 
											where LOGINID='".$loginid."'
											)mulist ON node.MENU_ID = mulist.MENU_ID
							ORDER BY node.LFT
						) menu";
                    
        }elseif($sQuery->num_rows() <=0){
			/*
            $node_tbl="SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, (COUNT(parent.MENU_NAME) - 1) AS depth
             FROM m_user_menu AS node
             INNER JOIN m_user_menu_grole ON m_user_menu_grole.MENU_ID = node.MENU_ID
             CROSS JOIN m_user_menu AS parent
             WHERE node.LFT BETWEEN parent.LFT AND parent.RGT AND user_group_id='".$usr_level."'
             GROUP BY node.MENU_NAME
             ORDER BY node.LFT"; 
			 */
			 $node_tbl="SELECT menu.MENU_NAME, menu.MENU_URL, menu.MENU_PARENT, menu.MENU_ID, 
						CASE menu.menu_id 
							WHEN 001 THEN 0
							ELSE 
								CASE (menu.length_menu) WHEN 3 THEN 1 WHEN 5 THEN 2 ELSE 3 END	
							END AS depth
						FROM(						
							SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, LENGTH(node.MENU_ID) AS length_menu
						   FROM m_user_menu AS node
						   INNER JOIN m_user_menu_grole ON  node.MENU_ID = m_user_menu_grole.MENU_ID
						   WHERE user_group_id='".$usr_level."'
						   ORDER BY node.LFT
						) menu";
        }
        
        if($node_tbl !="" || $node_tbl !=null || !empty($node_tbl)){
            $sql=$node_tbl ;
            $result = $this->db->query($sql);

            $tree = array();
            foreach ($result->result_array() as $row )
            {
                $tree[] = $row;
            }
            
            $result = '';
            $opened_li='';
            $cnt = 1;
            $currDepth = -1;  
            while (!empty($tree)) 
            {
                  $currNode = array_shift($tree);
                  if($currNode['MENU_ID'] != "001")
                  {

                      if ($currNode['depth'] > $currDepth) 
                      {
                            if($cnt>2)
                            {
                                $result .= '<ul>'."\n"; 
                            }  
                      }
                      
                      if ($currNode['depth'] < $currDepth) 
                      {
                            if ($opened_li != '' || $opened_li != null)
                            {
                                $result .= '</li>'."\n";    
                            } 
                            $result .= str_repeat('</ul>'."\n", $currDepth - $currNode['depth']);
                      }

                      if ($currNode['depth'] == 1) {
                          if($cnt>2)
                            {
                                $result .= '</li>'."\n";   
                            }
                          $result .= '<li><a href="#" class="accessible">' . $currNode['MENU_NAME'] .'</a>'."\n";
                      }else{
                          if($currNode['MENU_PARENT'] == 1)
                          {
                                $opened_li=1;
                                $result .= '<li><a href="#" class="accessible">' . $currNode['MENU_NAME'] .'</a>'."\n";    
                          }
                          else{
                           
                            $result .= '<li><a href='.base_url().$currNode['MENU_URL'].'>' . $currNode['MENU_NAME'] .'</li>'."\n";   
                          }
                          
                      } 
                      $currDepth = $currNode['depth'];

                      if (empty($tree)) {
                         $result .= '</ul>'."\n"; 
                         $result .= '</li>'."\n";
                      }    
                  }
                  $cnt++;
            }    
        }
        else{
            $result="MENU NOT AUTHORIZED";
        } 
        
         return $result;   
    }
	
	function get_menu_tiga($loginid,$usr_level,$company,$lastmenu) {
		  
        $node_tbl='';
		$template_path = base_url().$this->config->item('template_path');
        $query="SELECT * FROM m_user_menu_list where LOGINID='".$loginid."'";
        $sQuery = $this->db->query($query);
       
        $where =" AND 1=1 ";
        if($sQuery->num_rows() > 1){
			/*
            $node_tbl="SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, (COUNT(parent.MENU_NAME) - 1) AS depth
             FROM m_user_menu AS node
             INNER JOIN (SELECT * FROM m_user_menu_list where LOGINID='".$loginid."')mulist ON mulist.MENU_ID = node.MENU_ID
             CROSS JOIN m_user_menu AS parent
             WHERE node.LFT BETWEEN parent.LFT AND parent.RGT
             GROUP BY node.MENU_NAME
             ORDER BY node.LFT";
            */
			$node_tbl="SELECT menu.MENU_NAME, menu.MENU_URL, menu.MENU_PARENT, menu.MENU_ID, 
						CASE menu.menu_id 
							WHEN 001 THEN 0
							ELSE 
								CASE (menu.length_menu) WHEN 3 THEN 1 WHEN 5 THEN 2 ELSE 3 END	
							END AS depth
 						FROM(
							SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, 
							LENGTH(node.MENU_ID) AS length_menu
							FROM m_user_menu AS node 
							INNER JOIN (
											SELECT * 
											FROM m_user_menu_list 
											where LOGINID='".$loginid."'
											)mulist ON node.MENU_ID = mulist.MENU_ID
							ORDER BY node.LFT
						) menu";
        }elseif($sQuery->num_rows() <=0){
            /*$node_tbl="SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, (COUNT(parent.MENU_NAME) - 1) AS depth
             FROM m_user_menu AS node
             INNER JOIN m_user_menu_grole ON m_user_menu_grole.MENU_ID = node.MENU_ID
             CROSS JOIN m_user_menu AS parent
             WHERE node.LFT BETWEEN parent.LFT AND parent.RGT AND user_group_id='".$usr_level."'
             GROUP BY node.MENU_NAME
             ORDER BY node.LFT";  */
			 
			 $node_tbl="SELECT menu.MENU_NAME, menu.MENU_URL, menu.MENU_PARENT, menu.MENU_ID, 
						CASE menu.menu_id 
							WHEN 001 THEN 0
							ELSE 
								CASE (menu.length_menu) WHEN 3 THEN 1 WHEN 5 THEN 2 ELSE 3 END	
							END AS depth
						FROM(						
							SELECT node.MENU_NAME,node.MENU_URL,node.MENU_PARENT,node.MENU_ID, LENGTH(node.MENU_ID) AS length_menu
						   FROM m_user_menu AS node
						   INNER JOIN m_user_menu_grole ON  node.MENU_ID = m_user_menu_grole.MENU_ID
						   WHERE user_group_id='".$usr_level."'
						   ORDER BY node.LFT
						) menu;";
        }
        
        if($node_tbl !="" || $node_tbl !=null || !empty($node_tbl)){
            $sql=$node_tbl ;
            $result = $this->db->query($sql);

 			$tree = array();
            foreach ($result->result_array() as $row )
            {
                $tree[] = $row;
            }
            
            $result = '';
            $opened_li='';
            $cnt = 1;
            $currDepth = -1;  
            while (!empty($tree)) 
            {
                  $currNode = array_shift($tree);
                  if($currNode['MENU_ID'] != "001")
                  {

                      if ($currNode['depth'] > $currDepth) 
                      {
                            if($cnt>2)
                            {
                                $result .= '<div><ul>'."\n";
                            }  
                      }
                      
                      if ($currNode['depth'] < $currDepth) 
                      {
                            if ($opened_li != '' || $opened_li != null)
                            {
                                $result .= '</li>'."\n";    
                            } 
                            $result .= str_repeat('</ul></div>'."\n", $currDepth - $currNode['depth']);
                      }

                      if ($currNode['depth'] == 1) {
                          if($cnt>2)
                            {
                                $result .= '</li>'."\n";   
                            }
                          $result .= '<li><a href="#" class="parent"><img src="' . $template_path . 'themes_tiga/images/icons/topnav/tasks.png" alt="" /><span>' . $currNode['MENU_NAME'] .'</span></a>'."\n";
                      }else{
                          if($currNode['MENU_PARENT'] == 1)
                          {
                                $opened_li=1;
                                $result .= '<li><a href="#" class="parent"><span>' . $currNode['MENU_NAME'] .'</span></a>'."\n";    
                          }
                          else{
                            $result .= '<li><a href='.base_url().$currNode['MENU_URL'].'><span>' . $currNode['MENU_NAME'] .'</span></a></li>'."\n";   
                          }
                          
                      } 
                      $currDepth = $currNode['depth'];

                      if (empty($tree)) {
                         $result .= '</ul>'."\n"; 
                         $result .= '</li>'."\n";
                      }    
                  }
                  $cnt++;
            }    
        }
        else{
            $result="MENU NOT AUTHORIZED";
        } 
        
         return $result;   
    }
	
	function get_menu($loginid,$usr_level,$company,$lastmenu){
		 $ci = &get_instance();
		$module = $ci->session->userdata('MODULE_ACCESS');
		$getmenu = "";
		if(trim(strtoupper($module))=='LHM'){
           	$getmenu = $this->get_menu_tiga($loginid,$usr_level,$company,$lastmenu);
        }else{
            $getmenu = $this->get_menu_satu($loginid,$usr_level,$company,$lastmenu);        
        }
		return $getmenu;                                                  
	}
	
	function get_menu_pms($loginid) {
        $node_tbl='';
        $node_tbl="SELECT u.LOGINID, up.PMSUSERGROUP_ID, pm.PMSUSER_MENU, m.MENU_ID, m.MENU_NAME, m.MENU_PARENT, m.MENU_URL, 
						m.LFT, m.RGT, (COUNT(m.MENU_NAME) - 1) AS depth FROM m_user u
						LEFT JOIN pms_user_group_map up ON up.LOGINID = u.LOGINID
						LEFT JOIN pms_user_menu_map pm ON pm.PMSUSER_GROUP = up.PMSUSERGROUP_ID
						LEFT JOIN pms_user_menu m ON m.MENU_ID = pm.PMSUSER_MENU
						CROSS JOIN pms_user_menu AS parent
						WHERE u.LOGINID = '".$loginid."' AND m.LFT BETWEEN parent.LFT AND parent.RGT
						GROUP BY m.MENU_NAME
						ORDER BY m.LFT";
        
        if($node_tbl !="" || $node_tbl !=null || !empty($node_tbl)){
            $sql=$node_tbl ;
            $result = $this->db->query($sql);

            $tree = array();
            foreach ($result->result_array() as $row ){
                $tree[] = $row;
            }
            
            $result = '';
            $opened_li='';
            $cnt = 1;
            $currDepth = -1;  
            while (!empty($tree)) {
                  $currNode = array_shift($tree);
                  if($currNode['MENU_ID'] != "001"){
					 if ($currNode['depth'] > $currDepth){
                            if($cnt>2){
                                $result .= '<ul>';
                            }  
                      }
					  
                      if ($currNode['depth'] < $currDepth){
                            if ($opened_li != '' || $opened_li != null){
                                $result .= '</li>';    
                            } 
                            $result .= str_repeat('</ul>', $currDepth - $currNode['depth']);
                      }

                      if ($currNode['depth'] == 1) {
                          if($cnt>2){
                               $result .= '</li>';   
                          }
                          $result .= '<li><a class="menuitem">' . $currNode['MENU_NAME'] .'</a>';
                      } else {
                          if($currNode['MENU_PARENT'] == 1){
                                $opened_li=1;
                                $result .= '<li><a class="menuitem">' . $currNode['MENU_NAME'] .'</a>';    
                          } else {
                            $result .= '<li><a class="submenu" href='.base_url().$currNode['MENU_URL'].'>' . $currNode['MENU_NAME'] .'</li>';                          }
                      } 
                      $currDepth = $currNode['depth'];

                      if (empty($tree)) {
                         $result .= '</ul>'; 
                         $result .= '</li>';
                      }  
                  }
                  $cnt++;
            }   
			$result .= '</li>';
        } else {
            $result="MENU NOT AUTHORIZED";
        } 
         return $result;   
    }
   
    /*
    function get_parent_menu($userid)
    {
        $userid=$this->db->escape_str($userid);
        
        $query="SELECT m_user_menu_list.MENU_ID, m_user_menu.MENU_NAME, m_user_menu.MENU_PARENT, m_user_menu.MENU_URL, m_user_menu.RGT 
                FROM m_user_menu_list
                INNER JOIN m_user_menu ON m_user_menu.MENU_ID = m_user_menu_list.MENU_ID
                WHERE loginid='".$userid."'";
        $sQuery=$this->db->query($query);
        $numrows=$sQuery->num_rows();
        
        $temp_result = array();
        if ($numrows>0)
        { 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            } 
        }
        return $temp_result;
    }
    function get_menu($loginid,$lastmenu)
    {
        //$loginid=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $fill_menu='';
        $menus=$this->get_parent_menu($loginid);
        if (is_array($menus))
        {
            $parent = array();
            $child = array();
            
            reset($menus);
            while (list($key, $value) = each($menus))
            {
                if($value['MENU_ID']==$value['MENU_PARENT'])
                {
                    $parent[] = $value;  
                }else{
                    $child[] = $value;
                }
            }
                 
            foreach($parent as $pRow)
            {
                $rgt = $pRow['RGT'];
                $fill_menu .="<li><a href='#' class='accessible'>".$pRow['MENU_NAME']."</a>"."\n";
                if ($rgt > 0)
                {
                    $fill_menu .="<ul>"."\n";
                    foreach($child as $cRow )
                    {
                        if($cRow['MENU_PARENT'] == $pRow['MENU_ID'])
                        {
                            $cRgt = $cRow['RGT'];
                            if($cRgt > 0)// berarti child memilik child lagi di dalamnya
                            {
                                //track deep
                                $i='';
                                $fill_menu .="<li><a href='#' class='accessible'>".$cRow['MENU_NAME']."</a>";
                                $fill_menu .="<ul>"."\n";
                                foreach($child as $ccRow)
                                {
                                    if($ccRow['MENU_PARENT']==$cRow['MENU_ID'])
                                    { 
                                        $fill_menu .="<li><a href='".base_url()."index.php/".$ccRow['MENU_URL']."'>".$ccRow['MENU_NAME']."</a></li>"."\n";
                                    }      
                                }
                
                                $fill_menu .="</ul>"."\n";
                                $fill_menu .="</li>"."\n";  
                            }else{
                                $fill_menu .="<li><a href='".base_url()."index.php/".$cRow['MENU_URL']."'>".$cRow['MENU_NAME']."</a></li>"."\n";        
                            } 
                            
                        }
                          
                    }
                    $fill_menu .="</ul>"."\n";
                }
                
                $fill_menu .="</li>"."\n"; 
            }

        }else{
            $fill_menu='menu not authorized';
        }
        return $fill_menu;       
    } */
    
    /*
    function get_menu($loginid,$lastmenu)
    {
        //$loginid=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $fill_menu='';
        $menus=$this->get_parent_menu($loginid);
        if (is_array($menus))
        {
            $parent = array();
            $child = array();
            
            reset($menus);
            while (list($key, $value) = each($menus))
            {
                if($value['MENU_ID']==$value['MENU_PARENT'])
                {
                    $parent[] = $value;  
                }else{
                    $child[] = $value;
                }
            }
                 
            foreach($parent as $pRow)
            {
                $fill_menu .="<li><a href='#' class='accessible'>".$pRow['MENU_NAME']."</a>"."\n";
                $fill_menu .="<ul>"."\n";
                
                foreach($child as $cRow)
                {
                    if ($cRow['MENU_PARENT'] == $pRow['MENU_ID'])
                    {
                        if(empty($cRow['MENU_URL']) || $cRow['MENU_URL']=='' || $cRow['MENU_URL']=='#')
                        {
                            $fill_menu .="<li><a href='".base_url()."index.php/".$lastmenu."#'>".$cRow['MENU_NAME']."</a>"."\n";    
                        }else{
                            $fill_menu .="<li><a href='".base_url()."index.php/".$cRow['MENU_URL']."'>".$cRow['MENU_NAME']."</a>"."\n";
                        }
                        
                        $fill_menu .="<ul>"."\n"; 
                        foreach($child as $ccRow)
                        {
                            if($ccRow['MENU_PARENT']==$cRow['MENU_ID'])
                            { 
                                $fill_menu .="<li><a href='".base_url()."index.php/".$ccRow['MENU_URL']."'>".$ccRow['MENU_NAME']."</a></li>"."\n";
                            }      
                        }
                        $fill_menu .="</ul>"."\n";
                        $fill_menu .="</li>"."\n";
                    }
                }
                $fill_menu .="</ul>"."\n";
                $fill_menu .="</li>"."\n"; 
            }

        }else{
            $fill_menu='menu not authorized';
        }
        return $fill_menu;       
    }  
    */ 
}  
?>
