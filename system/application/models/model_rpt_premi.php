<?php
class model_rpt_premi extends Model
{
    function __construct()
    {
        parent::Model();
        $this->load->database();
    }
    function generate_premi($company, $gc, $periode)
    {
        if(strtoupper($gc)=="ALL")
        {
            $query ="SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
                  GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',c.PREMI ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN
                FROM m_empgang emp 
                LEFT JOIN 
                (
                    SELECT gad2.EMPLOYEE_CODE, gad2.LHM_DATE, 
                        SUM(gad2.PREMI) AS PREMI, gad2.COMPANY_CODE 
                    FROM m_gang_activity_detail gad2
                    WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."'
                    GROUP BY gad2.EMPLOYEE_CODE, gad2.LHM_DATE 
                ) c 
                ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
                LEFT JOIN 
                (
                    SELECT emp1.NIK,emp1.NAMA,emp1.TYPE_KARYAWAN
                        FROM m_employee emp1
                        WHERE emp1.COMPANY_CODE = '".$company."' AND emp1.INACTIVE = 0
                ) b
                ON emp.EMPLOYEE_CODE = b.NIK
                WHERE CONCAT(emp.YEAR,emp.MONTH) = '".$periode."'
                AND emp.COMPANY_CODE = '".$company."'
                GROUP BY emp.EMPLOYEE_CODE
                ORDER BY c.LHM_DATE ASC, emp.EMPLOYEE_CODE ASC";    
        }
        else
        {
            $where=" AND emp.GANG_CODE = '".$gc."' ";
            $query ="SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
                  GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',c.PREMI ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN
                FROM m_empgang emp 
                LEFT JOIN 
                (
                    SELECT gad2.EMPLOYEE_CODE, gad2.LHM_DATE, 
                        gad2.PREMI, gad2.COMPANY_CODE 
                    FROM m_gang_activity_detail gad2
                    WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."'
                    GROUP BY gad2.LHM_DATE, gad2.EMPLOYEE_CODE 
                ) c 
                ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
                LEFT JOIN 
                (
                    SELECT emp1.NIK,emp1.NAMA,emp1.TYPE_KARYAWAN
                        FROM m_employee emp1
                        WHERE emp1.COMPANY_CODE = '".$company."'  AND emp1.INACTIVE = 0
                ) b
                ON emp.EMPLOYEE_CODE = b.NIK
                WHERE CONCAT(emp.YEAR,emp.MONTH) = '".$periode."'
                AND emp.COMPANY_CODE = '".$company."' $where
                GROUP BY emp.EMPLOYEE_CODE
                ORDER BY c.LHM_DATE ASC, emp.EMPLOYEE_CODE ASC";
        }
        
        $sQuery = $this->db->query($query);
        
        $temp_result = array();
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
     
    function cek_hari($tgl,$company)
    {
        $query = $this->db->query("SELECT * FROM m_calendar WHERE COMPANY_CODE = '".$company."' AND DATE_FORMAT(CAL_TGL,'%Y%m%e') = '".$tgl."'");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row; 
        }
        return $temp_result;
    }  
}
?>
