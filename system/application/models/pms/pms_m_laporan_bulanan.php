<?php
class pms_m_laporan_bulanan extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	function getCompany(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function getReport($company, $periode){
				
		$pgsql = $this->load->database('adem', TRUE);
		$pgquery = "
		
		select rv.c_project_id, kode_project, nama_project, SUM(total), kode_act, nama_act, 
		case when substr(kode_act,1,2) = '81' then c.infras
		else c.block
		end as lokasi, c.plannedamt, c.plannedqty, c.uom from rv_zcostdetailproject rv 
		left join ( 
		
		select  c_project_id, pj.value, ce.value as infras, cs.value as block, pj.isactive, plannedamt, plannedqty, cu.name as uom
		from c_project pj
		left join c_elementvalue ce on ce.c_elementvalue_id = pj.c_elementvalue_id
		left join c_salesregion cs on cs.c_salesregion_id = pj.c_salesregion_id
		left join c_uom cu on cu.c_uom_id = pj.c_uom_id
		where pj.value like '".$company."%'
		
		 )c on c.c_project_id = rv.c_project_id 
		where kode_project LIKE '".$company."%' and to_char(movementdate,'YYYYMM') = '".$periode."'
		group by rv.c_project_id,  nama_project, kode_project, kode_act, nama_act, lokasi, 
				c.plannedamt, c.plannedqty, c.uom
		
		";
		$query = $pgsql->query($pgquery);
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
}

?>