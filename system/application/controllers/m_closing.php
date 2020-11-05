<?php
class m_closing extends controller
{
    function __construct()
    {
        parent::Controller();
        $this->load->model('model_m_closing');
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->plugin('to_excel');
        
        $this->load->library('approval/appapproval');
        $this->load->library('form/formheader');
        
    }
    function index()
    {
        //$viewPath='Project/';
        $viewName ='info_m_closing';
        $menu = new formHeader;
        $data = array();

        $data['login_id'] = $this->session->userdata('LOGINID');
        $data['company_code'] = $this->session->userdata('DCOMPANY');

        $data['CSSPath'] = $menu->LoadCSSPath(); //class formHeader
        $data['JSPath']=$menu->LoadJSPath(); //class formHeader
        $data['head']=$menu->show_head("Closing",""); //class formHeader
        $data['menu']= $menu->show_menu(); //class formHeader
        $data['CLOSING_TYPE']=$this->get_closing_type();
        if ($data['login_id'] == TRUE)
        {
            $this->load->view($viewName, $data);
        } 
        else 
        {
            redirect('login');
        }
    }
    
    function get_closing_type()
    { 
        $string = "<select  name='jns_laporan' class='select'  id='jns_laporan' style='width:130px'>";
        $string .= "<option value=''> -- choose -- </option>";
        
        $closingType = $this->model_m_closing->get_closing_type();
        
        foreach ($closingType as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row['CLOSING_TYPE_ID']."\"  selected>".$row['JENIS']." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row['CLOSING_TYPE_ID']."\">".$row['JENIS']." </option>";
            }
        }
        
        $string =$string. "</select>";
        return $string;
    }
    
    function update_closing()
    {
        $tglStart = htmlentities(mysql_escape_string($this->input->post('STARTDATE')));
        $tglEnd = htmlentities(mysql_escape_string($this->input->post('ENDDATE')));
        $closingType = (int)htmlentities($this->input->post('TIPE'));
        $company = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        $loginid = $this->session->userdata('LOGINID');   
        
        //$datapost['CLOSING_ID']="2";
        //$array = split("-",$tglStart);
        $yearmonth=substr($tglStart,0,4).substr($tglStart,4,2);  
        $datapost['CLOSING_TYPE_ID']=$closingType;
        $datapost['CLOSING_BY']= $loginid;
        $datapost['START_PERIODE']=$tglStart;
        $datapost['END_PERIODE']=$tglEnd;
        $datapost['PERIODE']= $yearmonth;
        $datapost['COMPANY_CODE']=$company;
        
        $update_closing=$this->model_m_closing->update_closing($tglStart,$tglEnd,$closingType,$company,$datapost);
       
        return $update_closing;
    }
}
?>
