<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class appapproval
{
    public $tblApprove;
    
    private $CI;
    function __construct()
    {
        $this->CI=& get_instance();
        $this->CI->load->database();
    }
    
    public function cek_approved()
    {
           $SaveuName = $this->CI->db->escape_str($this->CI->session->userdata('LOGINID'));
           $SaveuPass = $this->CI->db->escape_str($this->CI->session->userdata('USER_PASS'));
           $cekapprove = $this->cek_approve($SaveuName,$SaveuPass);
           return $cekapprove;
    }
    
    private function cek_approve($uName,$uPass)
    {
        
        if (!(isset($uName)))
        {
            $uName="";
        }
        else
        {
            $uName=$this->CI->db->escape_str($uName) ;
        }
        
        if (!(isset($uPass)))
        {
            $uPass="";
        }else
        {
            $uPass=$this->CI->db->escape_str($uPass);
        }
        
        $sQuery="SELECT m_user_co.USERID,m_user_co.COMPANY_CODE,m_user.LOGINID,m_user.USER_FULLNAME,m_user.USER_PASS,
                m_user.USER_MAIL,m_user.USER_LEVEL,m_user.USER_DEPT,m_user.ROLE_APPROVE 
                FROM m_user_co
                INNER JOIN m_user ON m_user_co.USERID = m_user.LOGINID
                WHERE  m_user_co.USERID='".$uName."' AND m_user.USER_PASS='".$uPass."'
                        AND m_user.ROLE_APPROVE='1'
                GROUP BY m_user_co.USERID, m_user_co.COMPANY_CODE";
        $query=$this->CI->db->query($sQuery);
        $count=$query->num_rows();
        if ($count>0)
        {
            return $count;
        }
        else{
            return $count;
        }
    }
    function update_approve($data,$where,$whereValues='')
    {
        $tblApprove=$this->CI->db->escape_str($this->tblApprove);
        
        if (!isset($tblApprove))
        {
            $tblApprove='';
        }
        
        if (is_array($where))
        {
             foreach($where as $keyWhere => $valWhere)
             {
                 $this->CI->db->where(htmlentities($this->CI->db->escape_str($keyWhere),ENT_QUOTES,'UTF-8'),htmlentities($this->CI->db->escape_str($valWhere),ENT_QUOTES,'UTF-8'));
             }
        }
        else
        {
            $where =  htmlentities($this->CI->db->escape_str($where),ENT_QUOTES,'UTF-8');
            $whereValues =  $this->CI->db->escape_str($whereValues);
            $this->CI->db->where($where,$whereValues);
        }
        $this->CI->db->update($tblApprove,$data);
    }
    function delete_approve($id,$where,$data)
    {
         $tblApprove=$this->CI->db->escape_str($this->tblApprove); 
         
          if (is_array($where))
          {
                foreach($where as $keyWhere => $valWhere)
                {
                    $this->CI->db->where(htmlentities($this->CI->db->escape_str($keyWhere),ENT_QUOTES,'UTF-8'),htmlentities($this->CI->db->escape_str($valWhere),ENT_QUOTES,'UTF-8'));
                }
          }
          else
          {
                $where =  htmlentities($this->CI->db->escape_str($where),ENT_QUOTES,'UTF-8');
                $whereValues =  htmlentities($this->CI->db->escape_str($whereValues),ENT_QUOTES,'UTF-8');
                $this->CI->db->where($where,$whereValues);
          }
         $this->CI->db->update($tblApprove,$data);
    }
}   
?>
