<?php
class m_natura extends Controller
{
    function __construct()
    {
        parent::Controller();
        $this->load->model( 'model_m_natura' ); 
        
        $this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
    }
    
    function index()
    {
        $data = array();
        
        $view = "info_m_natura";
        $data['judul_header'] = "Natura Karyawan";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
    
    function load_data_natura()
    {
       // $natura_date=date("Y",mktime()).date("m",mktime());
       // $periode = htmlentities($natura_date,ENT_QUOTES,'UTF-8');
		$dosearch = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		
        echo json_encode($this->model_m_natura->load_data_natura($periode, $dosearch));    
    }
	
	function natura_xls(){
        $periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		$obj =& get_instance();
		$data_row = $this->model_m_natura->get_natura($company, $periode);
		$bulan = substr($periode,-2);
    	if($bulan=='01'){ $bulan = "Januari"; } 
		else if($bulan=='02'){ $bulan = "Februari"; } 
		else if($bulan=='03'){ $bulan = "Maret"; } 
		else if($bulan=='04'){ $bulan = "April"; } 
		else if($bulan=='05'){ $bulan = "Mei"; } 
		else if($bulan=='06'){ $bulan = "Juni"; } 
		else if($bulan=='07'){ $bulan = "Juli"; } 
		else if($bulan=='08'){ $bulan = "Agustus"; } 
		else if($bulan=='09'){ $bulan = "September"; } 
		else if($bulan=='10'){ $bulan = "Oktober"; } 
		else if($bulan=='11'){ $bulan = "Nopember"; } 
		else if($bulan=='12'){ $bulan = "Desember"; }
		$judul = ''; $headers = ''; $data = '';  
		$judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t  \n"; 
        $judul .= "DAFTAR NATURA KARYAWAN \t \n";
        $judul .= "PERIODE  ".$bulan." ". substr($periode,0,4) ."\t \n";
        
		$headers .= "No. \t"; $headers .= "NIK \t";  $headers .= "Nama \t";
        $headers .= "Type Karyawan \t"; $headers .= "Status \t";
        $headers .= "Natura \t";          
				
		$no = 1;
        foreach ($data_row as $row)
        {
			 $line = '';
             $line .= str_replace('"', '""',$no)."\t";
			 $line .= str_replace('"', '""',trim($row['NIK']))."\t";
			 $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
			 $line .= str_replace('"', '""',trim($row['TYPE_KARYAWAN']))."\t";
			 $line .= str_replace('"', '""',trim($row['FAMILY_STATUS']))."\t";
			 $line .= str_replace('"', '""',trim($row['NATURA']))."\t"; 
			 $no++;
             $data .= trim($line)."\n";
		}
		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=NATURA_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data\n"; 
	}
	
    function search_natura()
    {
        $nik = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'); 
           
        echo json_encode($this->model_m_natura->search_natura($nik,$periode,$company));  
    }
    
    function update_natura()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $nik = htmlentities($this->input->post('NIK'),ENT_QUOTES,'UTF-8');
        $nama  = htmlentities($this->input->post('NAMA'),ENT_QUOTES,'UTF-8');
        $natura = htmlentities($this->input->post('NATURA'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->input->post('PERIODE'),ENT_QUOTES,'UTF-8');
        
        $validate_numeric=$this->validate_numeric($natura);
        $data_emp = $this->model_m_natura->cek_natura_employee($nik,$periode,$company);
        $status='';
        
        if( strtolower($validate_numeric)=='false')
        {
            $status = "input pada NATURA harus angka";
            echo $status;    
        }
        if(sizeof($data_emp)==0)//data kosong
        {
            $status="data karyawan tidak ada";
            echo $status;
        } 
        if(empty($status) || $status=='')
        {
            $postdata['NATURA']=$natura;
            $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
            $update = $this->model_m_natura->update_natura($nik,$periode,$company,$postdata);  
            echo $update;
        }
    }
    
	function generate_natura(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(5);
		$gnat = $this->model_m_natura->generate_natura($periode,$company);
		echo $gnat. " Data berhasil di generate ";
	}
	
    function create_natura()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $nik = htmlentities($this->input->post('NIK'),ENT_QUOTES,'UTF-8');
        $nama  = htmlentities($this->input->post('NAMA'),ENT_QUOTES,'UTF-8');
        $natura = htmlentities($this->input->post('NATURA'),ENT_QUOTES,'UTF-8');
		$periode = htmlentities($this->input->post('PERIODE'),ENT_QUOTES,'UTF-8');
        $date=getdate();
       // $natura_date=$date['year'].$date['mon'];
        
        $validate_numeric=$this->validate_numeric($natura);
        $data_emp = $this->model_m_natura->cek_employee_3($company,$nik);
        $data_natura = $this->model_m_natura->cek_natura_employee($nik,$periode,$company);
         
        $status='';
        $postdata=array();
        
        if( strtolower($validate_numeric)=='false')
        {
            $status = "input pada NATURA harus angka ".$validate_numeric."-";
            echo $status;    
        }
        if(sizeof($data_emp)==0)//data kosong
        {
            $status="data karyawan tidak ada";
            echo $status;
        } 
        if($data_natura>0)//data natura sudah ada di dalam database
        {
            $status="data natura untuk karyawan dengan NIK: ".$nik." sudah ada";
            echo $status;
        }
        
        if(empty($status) || $status=='')
        {
            if(sizeof($data_emp)>1)
            {
                $postdata['NIK']=$nik;
                //$postdata['NAMA']=$nama; 
                $postdata['NATURA']=$natura;
                $postdata['PERIODE']=$$periode;
                $postdata['COMPANY_CODE']=$company; 
                $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                 
            }elseif(sizeof($data_emp)==1)
            {
                foreach($data_emp as $row)
                {
                    $postdata['NIK']=htmlentities($row['NIK'],ENT_QUOTES,'UTF-8');
                    //$postdata['NAMA']=htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8');
                }
                $postdata['PERIODE']=$periode;
                $postdata['NATURA']=$natura; 
                $postdata['COMPANY_CODE']=$company;
                $postdata['INSERT_BY']= htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');    
            }
            $insert = $this->model_m_natura->insert_natura($postdata);  
            echo $insert;
        }
    }
    
    function cek_employee()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_emp = $this->model_m_natura->cek_employee($company,$q);
         
        //echo $q;
        $employee = array();
        foreach($data_emp as $row)
            {
                $employee[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).'"}';
            }
              echo '['.implode(',',$employee).']'; exit; 
    }
    
	/* lookup employee #20111017 - ridhu */
	function lookup_employee()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_emp = $this->model_m_natura->lookup_employee($company,$q);
         
        //echo $q;
        $employee = array();
        foreach($data_emp as $row)
            {
                $employee[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).'"}';
            }
              echo '['.implode(',',$employee).']'; exit; 
    }
	/* end lookup */
	
    function cek_employee_2()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_emp = $this->model_m_natura->cek_employee_2($company,$q);
         
        //echo $q;
        $employee = array();
        foreach($data_emp as $row)
            {
                $employee[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['NIK'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')).'"}';
            }
              echo '['.implode(',',$employee).']'; exit; 
    }
    
    //################# validation ########################
    function validate_numeric($data)
    {
        $numeric=$data;
        $result='';
        if(is_array($data))
        {
            while(list($key,$val)=each($data))
            {
                if(trim($val)=="" || $val==null)
                {
                    $val=0;
                }
                if((! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$val)))
                {
                    $result='false';
                    break;
                }else{
                    $result='true';   
                }
            }
        }else {
            if(trim($numeric)=="" || $numeric==null)
            {
                $val=0;
            }
            
            if (! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$numeric))
            {
                $result='false';   
            }else{
                $result='true';
            }    
        }
        return $result;   
    }
}
?>
