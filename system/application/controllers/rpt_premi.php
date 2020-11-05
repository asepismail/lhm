<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class rpt_premi extends Controller
{
    function __construct()
    {
        parent::Controller();
        $this->load->model('model_rpt_premi');
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_premi";
        
   		$this->load->helper('form');
		$this->load->helper('language');
		$this->load->database(); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->plugin('to_excel');
    }
	
    function index()
    {
		$view = "info_rpt_premi";
		$data = array();
		$data['judul_header'] = "Laporan Premi Karyawan";
		$data['js'] = $this->js_premi();
        
        $data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['GANG_CODE'] = $this->global_func->dropdownlist2("GANG_CODE","m_gang","GANG_CODE","GANG_CODE",
                            "COMPANY_CODE = '".$this->session->userdata('DCOMPANY')."'",NULL, NULL,'',"select", TRUE);
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
 	
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}  
    }
    
    function js_premi()
    {  
        $url=htmlentities($_SERVER['PHP_SELF']);
        $js = "jQuery('#submitdata').click(function (){
            var periode = $('#tahun').val() + $('#bulan').val();
            var gc = $('#GANG_CODE').val();
            var jns_laporan = $('#jns_laporan').val();                
            if ( jns_laporan == 'html')
            {
                urls = 'rpt_premi/premi_to_html/' + gc + '/' + periode; 
                $('#frame').attr('src',urls); 
            } 
            else if ( jns_laporan == 'excell')
            {
                urls = 'rpt+premi/premi_to_xls/' + gc + '/' + periode;
                $.download(urls,'');
            }
        });";
        return $js; 
    }
    
    function premi_to_html()
    {
        $gc = htmlentities($this->uri->segment(3));
        $periode = htmlentities($this->uri->segment(4));
        $company = htmlentities($this->session->userdata('DCOMPANY'));
        //echo $gc."-".$periode;
        $data_premi = $this->model_rpt_premi->generate_premi($company, $gc, $periode);
        
        $absen = "";
        $array = array();
        
        $libur = "";                
                
        $bulan = substr($periode,-2);
        $tahun = substr($periode,0,4);
        if($bulan == '01'){ $bulan = "Januari"; $hari = 31; } 
        else if($bulan == '02'){ $bulan = "Februari"; $hari = 28;  } 
        else if($bulan == '03'){ $bulan = "Maret";  $hari = 31; } 
        else if($bulan == '04'){ $bulan = "April";  $hari = 30; } 
        else if($bulan == '05'){ $bulan = "Mei";  $hari = 31; } 
        else if($bulan == '06'){ $bulan = "Juni";  $hari = 30; } 
        else if($bulan == '07'){ $bulan = "Juli";  $hari = 31; } 
        else if($bulan == '08'){ $bulan = "Agustus";  $hari = 31; } 
        else if($bulan == '09'){ $bulan = "September";  $hari = 30; } 
        else if($bulan == '10'){ $bulan = "Oktober";  $hari = 31; } 
        else if($bulan == '11'){ $bulan = "Nopember";  $hari = 30; } 
        else if($bulan == '12'){ $bulan = "Desember";  $hari = 31; }
        
        $bln = strtotime($periode.$hari);
        //$bulan = date("F Y", $bln);
            
        $table = "<table cellpadding='0' cellspacing='0' style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' width='100%'>";
        
        $table .= "<tr>
        <td width='25px' rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>No.</td>
        <td width='50px' rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Kemandoran</td>
        <td width='65px' rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid';>NIK</td>
        <td width='180px' rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Nama</td>
        <td width='30px' rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Status</td>";
        //$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> KJ </td>";
        $table .= "<td colspan = '".$hari."' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'><center>".strtoupper($bulan)." ".$tahun. 
                "</center></td></tr>";           
        $table .= "<tr>";
        for($i=1; $i<=$hari; $i++)
        {
            $table .= "<td width='50px' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$i." </td>";
        }   
        $table .= "</tr>";
          
        $no = 1;
        foreach($data_premi as $row)
        {
            $table .= " <tr>
            <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$no." </td> ";
            $table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['GANG_CODE']." </td> ";
            $table .= " <td  align='left' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid; padding-left:2px; padding-right:6px;' > ".$row['EMPLOYEE_CODE']." </td> ";
            $table .= '<td  align="left" style="font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid"> &nbsp;'.$row['NAMA'].'</td> ';
            $table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['TYPE_KARYAWAN']." </td> ";
            
            $array = split(",",$row['ABSEN']);
            for($i=1; $i<=$hari; $i++)
            {
                $table .= "<td align='right' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid;padding-right:2px; padding-left:6px;'>";
                $a = ""; 
                for($j=0; $j<count($array); $j++)
                {              
                    $premi = explode(":", $array[$j] );                      
                    $tgl = $premi[0]; 
                    $ta=(isset($premi[1]))?round($premi[1]):"-";   
                    $a .=($tgl == $i)?$ta:"";
                        
                }
               // $nPremi = explode(":", $array[$i] );
                if ($a != "")
                {
                    $table .=number_format($a);
                }
                else
                {
                    $table .="-";
                }
                //$table .=($a != "")?number_format($a):"-"; 
                $table .= "</td>";         
            }
            $table .= "</tr>";
            $no++;      
        } 
        $datatotal='';
        
        $table .= "<tr>";
        $table .= "<td  align = 'center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='5'><strong>TOTAL</strong></td>";
        for($i=1; $i<=$hari; $i++)
        {
            $table .= "<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> "."-"." </td>";
        } 
        $table .= "</tr>";
        
        $table .= "</table><br />";
        $table .= $libur;
        echo $table;
    }
    
    function premi_to_xls()
    {
        $gc = $this->uri->segment(3);
        $periode = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        
        $obj =& get_instance();
        
        $data_premi = $this->model_rpt_premi->generate_premi($company, $gc, $periode);
       
        $absen = "";
        $array = array();
                
        $hari = 31;
        $bln = strtotime($periode.$hari);
        $bulan = date("F Y", $bln);
        
        $headers .= "No \t";
        $headers .= "Kemandoran \t";
        $headers .= "NIK \t";
        $headers .= "Nama \t";
        $headers .= "Type Karyawan \t";
        
        for($i=1; $i<=$hari; $i++)
        {
            $headers .= $i."\t";
        }
        $no = 1;   
        foreach($data_premi as $row)
        {
            $line = '';            
            $line .= str_replace('"', '""',$no)."\t";
            $line .= str_replace('"', '""',$row['GANG_CODE'])."\t";
            $line .= str_replace('"', '""',$row['EMPLOYEE_CODE'])."\t";
            $line .= str_replace('"', '""',$row['NAMA'])."\t";
            $line .= str_replace('"', '""',$row['TYPE_KARYAWAN'])."\t";
            $array = split(",",$row['ABSEN']);
            
            for($i=1; $i<=31; $i++)
            {
                $a = "";
                for($j=0; $j<count($array); $j++)
                {             
                    $absennya = explode(":", $array[$j] );
                    $tgl = $absennya[0];
                    $ta =(isset($absennya[1]))?$absennya[1]:"";
                    $a .=($tgl == $i)?$ta:"";
                    
                }
                $line .=($a != "")?$a."\t":"- \t";
                        
            }
                    
            $no++;
            $data .= trim($line)."\n";
        }
        $data = str_replace("\r","",$data);
                         
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=premi_".$gc."_".$periode.".xls");
        echo "$headers\n$data";  
            
    }
}

?>
