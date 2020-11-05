<?

class model_kpivar extends Model 
{
    function model_kpivar()
    {
        parent::Model(); 

        $this->load->database();
    }
	
	function get_kpi ($company, $periode, $tipe) {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        
        $sidx = 'KPIP_ID';
        $sord = 'ASC';    
        $sql2 = "SELECT KPIT_ID,PERIODE,KPIV_ID,kpi_parameter.KPIP_DESC AS KPIV_DESC,kpi_parameter.KPIP_UOM AS KPIV_UOM,KPIV_VALUE,
KETERANGAN,COMPANY_CODE, kpi_parameter.KPIP_PARENT
					FROM kpi_variable
					LEFT JOIN kpi_parameter ON ( kpi_parameter.KPIP_ID = kpi_variable.KPIV_ID )  
					WHERE PERIODE = '".$periode."' AND COMPANY_CODE = '".$company."' AND KPIP_PARENT = '".$tipe."'
					UNION
				SELECT '' AS KPIT_ID, '' AS PERIODE, KPIP_ID AS KPIV_ID, KPIP_DESC AS KPIV_DESC, 
				KPIP_UOM AS KPIV_UOM, '' AS KPIV_VALUE, '' AS KETERANGAN, '' AS COMPANY_CODE, KPIP_PARENT FROM kpi_parameter
				WHERE KPIP_ID NOT IN ( SELECT KPIV_ID FROM kpi_variable ) AND KPIP_ID <> ''
				AND KPIP_PARENT = '".$tipe."' 
				";   
       
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sql2);
        $count = $query->num_rows(); 

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        
$sql = "SELECT KPIT_ID,PERIODE,KPIV_ID,kpi_parameter.KPIP_DESC AS KPIV_DESC,kpi_parameter.KPIP_UOM AS KPIV_UOM,KPIV_VALUE,
KETERANGAN,COMPANY_CODE, kpi_parameter.KPIP_PARENT
					FROM kpi_variable
					LEFT JOIN kpi_parameter ON ( kpi_parameter.KPIP_ID = kpi_variable.KPIV_ID )  
					WHERE PERIODE = '".$periode."' AND COMPANY_CODE = '".$company."' AND KPIP_PARENT = '".$tipe."'
					UNION
				SELECT '' AS KPIT_ID, '' AS PERIODE, KPIP_ID AS KPIV_ID, KPIP_DESC AS KPIV_DESC, 
				KPIP_UOM AS KPIV_UOM, '' AS KPIV_VALUE, '' AS KETERANGAN, '' AS COMPANY_CODE, KPIP_PARENT FROM kpi_parameter
				WHERE KPIP_ID NOT IN ( SELECT KPIV_ID FROM kpi_variable ) AND KPIP_ID <> ''
				AND KPIP_PARENT = '".$tipe."' 
				ORDER BY KPIV_ID ".$sord."";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
                        
        $no = 1;            
        
        $id = "";
                            
                                    
        foreach($objects as $obj)
        {
            $cell = array();
					if ($obj->KPIT_ID == '') { $id = $no; } else { $id = $obj->KPIT_ID; }
                    array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->KPIV_ID,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->KPIV_DESC,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->KPIV_UOM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->KPIV_VALUE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->KETERANGAN,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    
                    $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            
            array_push($rows, $row);
            
            $no++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
    
    function insert_kpi ( $data )
    {
        $this->db->insert( 'kpi_variable', $data );
        return $this->db->insert_id();   
    }
    
    function update_kpi ( $company, $tipe, $periode, $data )
    {
      
        $periode=htmlentities($this->db->escape_str($periode),ENT_QUOTES,'UTF-8'); 
        $tipe =htmlentities($this->db->escape_str($tipe),ENT_QUOTES,'UTF-8');  
        $company =htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
        $this->db->where('KPIV_ID',$tipe);
        $this->db->where('PERIODE',$periode);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->update( 'kpi_variable', $data );   
    }
    
    function delete_kpi ( $company, $tipe, $periode )
    {
        $periode=htmlentities($this->db->escape_str($periode),ENT_QUOTES,'UTF-8'); 
        $tipe =htmlentities($this->db->escape_str($tipe),ENT_QUOTES,'UTF-8');  
        $company =htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
        $this->db->where('KPIV_ID',$tipe);
        $this->db->where('PERIODE',$periode);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->delete('kpi_variable');   
    }
}

?>
