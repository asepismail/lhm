<?php
class model_m_closing extends Model
{
    function __construct()
    {
        parent::Model();
        $this->load->database();
    }
    
    function cek_exist_data($ccode,$tableName)
    {
        $where ="DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tglStart."','%Y%m%01') AND DATE_FORMAT('".$tglEnd."','%Y%m%d') AND COMPANY_CODE='".$company."'";
        $sQuery = "SELECT * FROM ".$tableName." WHERE VEHICLECODE='".$vhccode."'";
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        return $count;
    }
    
    function update_closing($tglStart,$tglEnd,$closingType,$company,$datapost)
    {
        $tglStart = $tglStart;
        $tglEnd = $tglEnd;    
        $closingType = htmlentities($closingType);
        $company = htmlentities(mysql_escape_string($company));
        
        $where ="DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tglStart."','%Y%m%01') AND DATE_FORMAT('".$tglEnd."','%Y%m%d') AND COMPANY_CODE='".$company."'";
        $this->db->where($where);
        $this->db->set('CLOSING_STATUS',"1");
        $this->db->update('m_gang_activity_detail'); //update closing status menjadi=1 yg berarti data sudah di closing 
        
        $this->db->insert('closing',$datapost); //setelah data pada m_gang_activity_detail di closing, buat flag closing pada table 'closing' 
        //$execsp=$this->exec_sp(substr($tglStart,0,6),$company); 
        
        return $this->db->insert_id();     
    }
    
    function exec_sp($periode,$company)
    {
        $query = "CALL sp_generate_du_detail('".$periode."', '".$company."')";
        $exec=$this->db->query($query);
    }
    
    function get_closing_type()
    {
        $sQuery ="SELECT * FROM closing_type";
        $query = $this->db->query($sQuery);
        
        if ($query->num_rows() > 0)
        {   
            foreach ($query->result_array() as $row )
            {
                $temp_result [] = $row;
            }
            $query->free_result();    
            return $temp_result;
        }        
    }
}
?>
