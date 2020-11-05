<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class s_analisa_panen extends Controller{
    function __construct(){
        parent::__construct();
        
        $this->load->model('model_s_analisa_panen');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_analisa_panen";
        $this->load->plugin('to_excel');

        $this->load->helper('file');
        require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
        
    }
    
    function index()
    {
        $view="info_s_analisa_panen";
        
        $data = array();
        $data['judul_header'] = "Pelaporan Panen dan Nota angkut";
        $data['js'] = "";
    
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['suppliername'] = $this->dropdownlist_supplier();
        $data['kontraktorbuah'] = $this->dropdownlist_kontraktorbuah();
        $data['afd'] = $this->dropdownlist_afd();
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
    
    function dropdownlist_afd(){
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $string = "<select  name='i_afd' class='select' id='i_afd' style='width:120px;'>";
        $string .= "<option value=''> -- pilih -- </option>";
        $string .= "<option value='all'> -- All -- </option>";
        $data_level = $this->model_s_analisa_panen->get_afd($company);
        
        foreach ( $data_level as $row) {
            if( (isset($default))) {
                $string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_CODE']." </option>";
            } else {
                $string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_CODE']." </option>";
            }
        }
        $string =$string. "</select>";
        return $string;
    }
    
    function dropdownlist_kontraktorbuah(){
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $string = "<select  name='i_kontraktor' class='select' id='i_kontraktor' style='width:120px;'>";
        $string .= "<option value=''> -- pilih -- </option>";
        $data_level = $this->model_s_analisa_panen->get_kontraktor($company);
        
        foreach ( $data_level as $row) {
            if( (isset($default))) {
                $string = $string." <option value=\"".$row['KODE_KONTRAKTOR']."\"  selected>".$row['NAMA_KONTRAKTOR']." </option>";
            } else {
                $string = $string." <option value=\"".$row['KODE_KONTRAKTOR']."\">".$row['NAMA_KONTRAKTOR']." </option>";
            }
        }
        $string =$string. "</select>";
        return $string;
    }
    
    function dropdownlist_supplier(){
        $company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $string = "<select  name='i_supplier_name' class='select' id='i_supplier_name' style='width:120px;' >";
        $string .= "<option value=''> -- pilih -- </option>";
        
        $data_afd = $this->model_s_analisa_panen->get_supplier($company);
        
        foreach ( $data_afd as $row)
        {
            if( (isset($default)))
            {
                $string = $string." <option value=\"".$row['SUPPLIERCODE']."\"  selected>".$row['SUPPLIERNAME']." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row['SUPPLIERCODE']."\">".$row['SUPPLIERNAME']." </option>";
            }
        }
        
        $string =$string. "</select>";
        return $string;
    }
    
    //## Create Report: GC - Produksi Kebun (Panen) ##
	/*
    function generate_xls_nab(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
		
		//start: Added by Asep, 20130521
		$m='';
		$y='';
		$m=date("m",strtotime($ar2));
		$y=date("Y",strtotime($ar2));
		$awal_bulan= $y.$m."01";
		if($company == 'GKM' || $company == 'SML'){ 
			$tabel1='dummy_mgangactivitydetail_gkm';
			$tabel2='dummy_pprogress_gkm';	
		}else{
			$tabel1='m_gang_activity_detail';
			$tabel2='p_progress';
		}
		//end: Added by Asep, 20130521
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_panen=$this->model_s_analisa_panen->generate_lhm_nab($ar,$ar2,$company);

        //baris 1
        $headers .= "No. \t";
        $headers .= "Tanggal \t";
        $headers .= "Kode Lokasi \t";
        //$headers .= "HK Panen \t"; // remarked by Asep, 20130521
        //$headers .= "HK Brondolan \t";  // remarked by Asep, 20130521
        $headers .= "Janjang Panen (HI) \t";
        $headers .= "Janjang Panen (SHI) \t";
        //$headers .= "LHM Janjang AFKIR \t"; // remarked by Asep, 20130521
        //$headers .= "LHM Berat Brodolan (Kg - Sebaran) \t"; // remarked by Asep, 20130521
        //$headers .= "LHM Berat Brodolan (Kg) \t"; // remarked by Asep, 20130521
		$headers .= "Berat Panen (HI) (kg) \t"; // remarked by Asep, 20130521
        $headers .= "Berat Panen (SHI) (kg) \t"; // remarked by Asep, 20130521
        //$headers .= "NAB Janjang Angkut \t"; // remarked by Asep, 20130521
        //$headers .= "NAB Janjang Angkut (SHI)\t"; // remarked by Asep, 20130521
		$headers .= "Janjang Angkut (HI) \t";
        $headers .= "Janjang Angkut (SHI) \t";
        //$headers .= "NAB Berat Angkut (Kg - Sebaran) \t"; // remarked by Asep, 20130521
		$headers .= "Berat Angkut HI (kg) \t"; // remarked by Asep, 20130521
		$headers .= "Berat Angkut SHI (kg) \t"; // remarked by Asep, 20130521
        $headers .= "Restan \t";
        
        $no = 1;
        foreach ($data_panen as $row){
			//start:  Added By Asep, 20130508				
				$tanggal=$row['TANGGAL'];
		
				$location_code = $row['LOCATION_CODE'];					
				$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $tanggal,$company,$location_code,$tabel1);
				$bjr_real = $row['BJR_REAL'];
				$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $tanggal,$company,$location_code, $tabel1);
				$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
				$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
				//end:  Added By Asep, 20130508
				
            $line = '';
            $line .= str_replace('"', '""',$no)."\t";       
            $line .= str_replace('"', '""',$row['TANGGAL'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t"; 
            //$line .= str_replace('"', '""',$row['HK_JUMLAH'])."\t"; //Remarked by Asep, 20132105
            //$line .= str_replace('"', '""',$row['HK'])."\t"; //Remarked by Asep, 20132105 
            $line .= str_replace('"', '""',$row['JANJANG_PANEN'])."\t";
            // $line .= str_replace('"', '""',$row['JJG_LHM_TOTAL'])."\t"; Remarked by Asep, 20132105
			$line .= str_replace('"', '""',$shi_janjang_panen)."\t"; 
            //$line .= str_replace('"', '""',$row['JJG_AFKIR'])."\t";//Remarked by Asep, 20132105 
            $line .= str_replace('"', '""',$row['BERAT_PANEN'])."\t";
			$line .= str_replace('"', '""',$shi_berat_panen)."\t"; //Added By Asep, 20130521
            //$line .= str_replace('"', '""',$row['HASIL_KERJA'])."\t";//Remarked by Asep, 20130521 
            $line .= str_replace('"', '""',$row['JJG_ANGKUT'])."\t";
			$line .= str_replace('"', '""',$shi_janjang_angkut)."\t"; //Added By Asep, 20130521
            //$line .= str_replace('"', '""',$row['JJG_ANGKUT_TOTAL'])."\t";  Remarked by Asep, 20132105
            $line .= str_replace('"', '""',$row['BERAT_ANGKUT'])."\t";
			$line .= str_replace('"', '""',$shi_berat_angkut)."\t"; //Added By Asep, 20130521
            $line .= str_replace('"', '""',$row['RESTAN'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=NAB_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    */
	//generate_xls_nab modifeid by Asep, 20130819
	function generate_xls_nab(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
		
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_panen=$this->model_s_analisa_panen->generate_lhm_nab($ar,$ar2,$company);
		$saldo_awal=$this->model_s_analisa_panen->saldo_awal($ar,$company);

        //baris 1
        $headers .= "No. \t";
        $headers .= "Tanggal \t";
        $headers .= "Kode Lokasi \t";
        $headers .= "Janjang Panen (HI) \t";
        $headers .= "Janjang Panen (SHI) \t";
		$headers .= "Berat Panen (HI) (kg) \t"; 
        $headers .= "Berat Panen (SHI) (kg) \t"; 
		$headers .= "Janjang Angkut (HI) \t";
        $headers .= "Janjang Angkut (SHI) \t";
		$headers .= "Berat Angkut HI (kg) \t"; 
		$headers .= "Berat Angkut SHI (kg) \t";
		$headers .= "Afkir \t";
        $headers .= "Restan \t";	
		$headers .= "Keterangan \t";
        
        $no = 1;
		if ($saldo_awal<> NULL){
			foreach ($saldo_awal as $saldo_awal){											
				$line = '';
				$line .= str_replace('"', '""',$no)."\t";       
				$line .= str_replace('"', '""',$saldo_awal['TANGGAL'])."\t";
				$line .= str_replace('"', '""',$saldo_awal['BLOCK'])."\t"; 
				$line .= "0"."\t";
				$line .= "0"."\t"; 
				$line .= "0"."\t";
				$line .= "0"."\t";
				$line .= "0"."\t";
				$line .= "0"."\t";
				$line .= "0"."\t";
				$line .= "0"."\t";
				$line .= "0"."\t";
				$line .= str_replace('"', '""',$saldo_awal['RESTAN'])."\t";
				$line .= "Restan bulan lalu"."\t";
				$no++;
				$data .= trim($line)."\n";  
			}	
		}
        foreach ($data_panen as $row){											
            $line = '';
            $line .= str_replace('"', '""',$no)."\t";       
            $line .= str_replace('"', '""',$row['DATE_TRANSACT'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t"; 
            $line .= str_replace('"', '""',$row['JANJANG_PANEN'])."\t";
			$line .= str_replace('"', '""',$row['JANJANG_PANEN_SHI'])."\t"; 
            $line .= str_replace('"', '""',$row['BERAT_PANEN'])."\t";
			$line .= str_replace('"', '""',$row['BERAT_PANEN_SHI'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG_ANGKUT'])."\t";
			$line .= str_replace('"', '""',$row['JANJANG_ANGKUT_SHI'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ANGKUT'])."\t";
			$line .= str_replace('"', '""',$row['BERAT_ANGKUT_SHI'])."\t";
			$line .= str_replace('"', '""',$row['JANJANG_AFKIR'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG_RESTAN'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=NAB_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    //## Create Report: GC - Produksi Kebun (Panen) ##
	/*
    function generate_lhm_nab(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
		//start: Added by Asep, 20130521
		$m='';
		$y='';
		$m=date("m",strtotime($ar2));
		$y=date("Y",strtotime($ar2));
		$awal_bulan= $y.$m."01";
		if($company == 'GKM' || $company == 'SML'){ 
			$tabel1='dummy_mgangactivitydetail_gkm';
			$tabel2='dummy_pprogress_gkm';	
		}else{
			$tabel1='m_gang_activity_detail';
			$tabel2='p_progress';
		}
		//end: Added by Asep, 20130521

        if(!empty($periode) && !empty($company)){
            $data_panen=$this->model_s_analisa_panen->generate_lhm_nab($ar,$ar2,$company);

            $PANEN = "";
            $i = 1;
            
            $PANEN .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PANEN .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PANEN .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
            
            
            $PANEN .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";
            $PANEN .= "<tr><td  align='left' colspan='12' class='tbl_th'> *Klik kode lokasi untuk melihat detail </td> </tr>";    
            $PANEN .= "<tr><td  align='center' rowspan='3' class='tbl_th'> NO. </td>";
            $PANEN .= "<td rowspan='3' align='center' class='tbl_th'>TANGGAL</td>"; 
            $PANEN .= "<td rowspan='3' align='center' class='tbl_th'>KODE LOKASI</td>";
            //$PANEN .= "<td rowspan='2'align='center' class='tbl_th'>ACTIVITY CODE</td>";
            $PANEN .= "<td colspan='4' align='center' class='tbl_th'>LHM</td>";
            $PANEN .= "<td colspan='4'align='center' class='tbl_th'>NOTA ANGKUT</td>";
            $PANEN .= "<td colspan='2'align='center' class='tbl_th'>RESTAN</td>";
            
            $PANEN .= "<tr><td align='center' class='tbl_th' colspan='2'>JANJANG PANEN</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>BERAT PANEN (Kg)</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>JANJANG ANGKUT</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>BERAT ANGKUT (Kg)</td>";
            $PANEN .= "<td align='center' class='tbl_th' rowspan='2'>JANJANG RESTAN</td>";
            
            $PANEN .= "<tr><td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            $total_jjg_lhm=0;
            $total_berat_lhm=0;
            $total_jjg_nab=0;
            $total_berat_nab=0;
            $total_jjg_restan=0;
            $total_berat_restan=0;

			$shi_janjang_angkut = 0; // Added By Asep, 20130508
			$shi_berat_angkut = 0; // Added By Asep, 20130508
			$bjr_real = 0; // Added By Asep, 20130508
			$location_code =''; 

            foreach($data_panen as $row){
				//start:  Added By Asep, 20130508				
				$tanggal=$row['TANGGAL'];
		
				$location_code = $row['LOCATION_CODE'];					
				$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $tanggal,$company,$location_code,$tabel1);
				$bjr_real = $row['BJR_REAL'];
				$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $tanggal,$company,$location_code, $tabel1);
				$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
				$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $tanggal,$company,$location_code);
				//end:  Added By Asep, 20130508
				
                $ar3 = preg_split('/[- :]/',trim($row['TANGGAL']));
                $ar3 = implode('',$ar3);
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['TANGGAL'].'&nbsp;</td>';
                $PANEN .= "<td width='100' class='tbl_td' ".$style."><strong>
                    <a href='".$url."get_panen_breakdown/".$row['LOCATION_CODE']."/".$ar3.
                    "' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><center>".$row['LOCATION_CODE']."</center></a></strong></td>";
                //$PANEN .= '<td width="150" class="tbl_td" align="left">&nbsp;'.$row['ACTIVITY_CODE'].'</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_PANEN']).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($shi_janjang_panen).'&nbsp;</td>'; // SHI JANJANG PANEN, Added By Asep, 20130508
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_PANEN'],2).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($shi_berat_panen,2).'&nbsp;</td>'; // SHI BERAT PANEN, Added By Asep, 20130508
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JJG_ANGKUT']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($shi_janjang_angkut).' &nbsp;</td>'; // SHI JJG_ANGKUT
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_ANGKUT'],2).'  &nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($shi_berat_angkut,2).' &nbsp;</td>'; // SHI JJG_ANGKUT 
                
                $restan_jjg = $row['JANJANG_PANEN']-$row['JJG_ANGKUT'];
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['RESTAN']).'  &nbsp;</td>';
                $PANEN .= '</tr>';
                $total_jjg_lhm+=$row['JANJANG_PANEN'];
                $total_berat_lhm+=$row['BERAT_PANEN'];
                $total_jjg_nab+=$row['JJG_ANGKUT'];
                $total_berat_nab+=$row['BERAT_ANGKUT']; 
                $total_jjg_restan+=$row['RESTAN'];
                //$total_berat_restan+=$restan_wb;   
                $i++;    
            }
            $PANEN .="<tr><td class='tbl_td' align='center' colspan='2'><strong>TOTAL</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_lhm)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_lhm,2)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_nab)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_nab,2)."&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_restan)."&nbsp;</strong></td>";
            //$PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_restan)."&nbsp;</strong></td>";
            $PANEN .= "</table>"; 
            
            echo $PANEN;			
        }
        
    }
	*/
	//generate_lhm_nab modified by asep, 20130819
	function generate_lhm_nab(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);		

        if(!empty($periode) && !empty($company)){
            $data_panen=$this->model_s_analisa_panen->generate_lhm_nab($ar,$ar2,$company);
			$saldo_awal=$this->model_s_analisa_panen->saldo_awal($ar,$company);
            $PANEN = "";
            $i = 1;
            
            $PANEN .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PANEN .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PANEN .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
            
            
            $PANEN .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";
            $PANEN .= "<tr><td  align='left' colspan='14' class='tbl_th'> *Klik kode lokasi untuk melihat detail </td> </tr>";    
            $PANEN .= "<tr><td  align='center' rowspan='3' class='tbl_th'> NO. </td>";
            $PANEN .= "<td rowspan='3' align='center' class='tbl_th'>TANGGAL</td>"; 
            $PANEN .= "<td rowspan='3' align='center' class='tbl_th'>KODE LOKASI</td>";
            $PANEN .= "<td colspan='4' align='center' class='tbl_th'>LHM</td>";
            $PANEN .= "<td colspan='4'align='center' class='tbl_th'>NOTA ANGKUT</td>";
			$PANEN .= "<td align='center' class='tbl_th'>AFKIR</td>";
            $PANEN .= "<td align='center' class='tbl_th'>RESTAN</td>";
			$PANEN .= "<td align='center' class='tbl_th' rowspan='3'>KETERANGAN</td>";
            
            $PANEN .= "<tr><td align='center' class='tbl_th' colspan='2'>JANJANG PANEN</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>BERAT PANEN (Kg)</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>JANJANG ANGKUT</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>BERAT ANGKUT (Kg)</td>";
			$PANEN .= "<td align='center' class='tbl_th' rowspan='2'>JANJANG AFKIR</td>";
            $PANEN .= "<td align='center' class='tbl_th' rowspan='2'>JANJANG RESTAN</td>";
			
            
            $PANEN .= "<tr><td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
			$location_code =''; 
			
			$total_jjg_lhm=0;
            $total_berat_lhm=0;
            $total_jjg_nab=0;
            $total_berat_nab=0;
            $total_jjg_restan=0;
			
			if ($saldo_awal<> NULL){
				foreach($saldo_awal as $saldo_awal){
					$PANEN .= '<tr id="tr_1">';
					$PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
					$PANEN .= '<td width="100" class="tbl_td" align="center">'.$saldo_awal['TANGGAL'].'&nbsp;</td>';
					$PANEN .= '<td width="100" class="tbl_td" align="center">'.$saldo_awal['BLOCK'].'&nbsp;</td>';
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>';
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>'; 
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>'; 
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>'; 
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>'; 
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>'; 
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>';
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>'; 
					$PANEN .= '<td width="100" class="tbl_td" align="right">0 &nbsp;</td>';
					$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($saldo_awal['RESTAN']).'  &nbsp;</td>';
					$PANEN .= '<td width="100" class="tbl_td" align="left">&nbsp; Restan bulan lalu</td>';
					$i++;    
				}
			}

            foreach($data_panen as $row){
				
                $ar3 = preg_split('/[- :]/',trim($row['DATE_TRANSACT']));
                $ar3 = implode('',$ar3);
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['DATE_TRANSACT'].'&nbsp;</td>';
                $PANEN .= "<td width='100' class='tbl_td' ".$style."><strong>
                    <a href='".$url."get_panen_breakdown/".$row['LOCATION_CODE']."/".$ar3.
                    "' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><center>".$row['LOCATION_CODE']."</center></a></strong></td>";                
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_PANEN']).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_PANEN_SHI']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_PANEN'],2).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_PANEN_SHI'],2).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_ANGKUT']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_ANGKUT_SHI']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_ANGKUT'],2).'  &nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_ANGKUT_SHI'],2).' &nbsp;</td>'; 
               	$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_AFKIR']).'  &nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_RESTAN']).'  &nbsp;</td>';
				$PANEN .= '<td width="100" class="tbl_td" align="right">&nbsp;</td>';
                $PANEN .= '</tr>';
                $total_jjg_lhm+=$row['JANJANG_PANEN'];
                $total_berat_lhm+=$row['BERAT_PANEN'];
                $total_jjg_nab+=$row['JANJANG_ANGKUT'];
                $total_berat_nab+=$row['BERAT_ANGKUT']; 
                if ($row['DATE_TRANSACT'] == $periode_to){					
                	//$total_jjg_restan+=$row['JANJANG_RESTAN']; 
		  		}
                $i++;    
            }
            $PANEN .="<tr><td class='tbl_td' align='center' colspan='2'><strong>TOTAL</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_lhm)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_lhm,2)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_nab)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_nab,2)."&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
     		$PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_restan)."&nbsp;</strong></td>";
			$PANEN .= "<td class='tbl_td' align='right'><strong>&nbsp;</strong></td>";
            
            $PANEN .= "</table>"; 
            
            echo $PANEN;			
        }
        
    }
	
	function generate_titip_olah(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
		$user = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);		
		
        if(!empty($periode) && !empty($company)){
            $data_panen=$this->model_s_analisa_panen->generate_titip_olah($ar,$ar2,$company, $user);

            $PANEN = "";
            $i = 1;
            
            $PANEN .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PANEN .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
			 $PANEN .= ".tbl_th2 { font-size: 14px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
			$PANEN .= ".tbl_td2 { font-size: 10px;color:#FF0000;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PANEN .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
            
            $PANEN .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>
<tr>
  <td  align='left' colspan='29' class='tbl_th'> *MONITORING TITIP OLAH</td> </tr>   
<tr><td  align='center' rowspan='3' class='tbl_th'> NO. </td>
	<td rowspan='3' align='center' class='tbl_th'>TANGGAL</td>
	<td colspan='9' align='center' class='tbl_th2'>SML</td>
    <td colspan='9' align='center' class='tbl_th2'>SSS</td> 
    <td colspan='9' align='center' class='tbl_th2'>GKM</td> 
 </tr>
            
<tr><td align='center' class='tbl_th' colspan='3'>TBS</td>
	<td align='center' class='tbl_th' colspan='4'>PRODUKSI</td>
    <td align='center' class='tbl_th' colspan='2'>STOCK</td>
    
    <td align='center' class='tbl_th' colspan='3'>TBS</td>
	<td align='center' class='tbl_th' colspan='4'>PRODUKSI</td>
    <td align='center' class='tbl_th' colspan='2'>STOCK</td>
    
    <td align='center' class='tbl_th' colspan='3'>TBS</td>
	<td align='center' class='tbl_th' colspan='4'>PRODUKSI</td>
    <td align='center' class='tbl_th' colspan='2'>STOCK</td>
    </tr>
            
<tr>
	<td align='center' class='tbl_th'>TBS Olah</td>
	<td align='center' class='tbl_th'>Dispatch CPO</td>
    <td align='center' class='tbl_th'>Dispatch KERNEL</td>
	<td align='center' class='tbl_th'>CPO</td>
	<td align='center' class='tbl_th'>OER</td>
	<td align='center' class='tbl_th'>KERNEL</td>
	<td align='center' class='tbl_th'>KER</td>
    <td align='center' class='tbl_th'>Stock CPO</td>
	<td align='center' class='tbl_th'>Stock KERNEL</td>
    
    <td align='center' class='tbl_th'>TBS Olah</td>
	<td align='center' class='tbl_th'>Dispatch CPO</td>
    <td align='center' class='tbl_th'>Dispatch KERNEL</td>
	<td align='center' class='tbl_th'>CPO</td>
	<td align='center' class='tbl_th'>OER</td>
	<td align='center' class='tbl_th'>KERNEL</td>
	<td align='center' class='tbl_th'>KER</td>
    <td align='center' class='tbl_th'>Stock CPO</td>
	<td align='center' class='tbl_th'>Stock KERNEL</td>
    
    <td align='center' class='tbl_th'>TBS Olah</td>
	<td align='center' class='tbl_th'>Dispatch CPO</td>
    <td align='center' class='tbl_th'>Dispatch KERNEL</td>
	<td align='center' class='tbl_th'>CPO</td>
	<td align='center' class='tbl_th'>OER</td>
	<td align='center' class='tbl_th'>KERNEL</td>
	<td align='center' class='tbl_th'>KER</td>
    <td align='center' class='tbl_th'>Stock CPO</td>
	<td align='center' class='tbl_th'>Stock KERNEL</td>
</tr>";
            
            $style = "";
			$stock_awal_cpo_sml = 0;
			$stock_awal_cpo_sss = 0;
			$stock_awal_cpo_gkm = 0;
				
			$stock_awal_kernel_sml = 0;
			$stock_awal_kernel_sss = 0;
			$stock_awal_kernel_gkm = 0;
			$oer_sml = 0;
			$ker_sml = 0;
			$oer_sss = 0;
			$ker_sss = 0;
			$oer_gkm = 0;
			$ker_gkm = 0;
			
			if($company=='GKM' || $company=='SML' || $company=='SSS'){
				$stock_awal_cpo_sml = $this->model_s_analisa_panen->get_stock_awal($ar,'SML', 'CPO');
				$stock_awal_cpo_sss = $this->model_s_analisa_panen->get_stock_awal($ar,'SSS', 'CPO');
				$stock_awal_cpo_gkm = $this->model_s_analisa_panen->get_stock_awal($ar,'GKM', 'CPO');
				
				$stock_awal_kernel_sml = $this->model_s_analisa_panen->get_stock_awal($ar,'SML', 'KERNEL');
				$stock_awal_kernel_sss = $this->model_s_analisa_panen->get_stock_awal($ar,'SSS', 'KERNEL');
				$stock_awal_kernel_gkm = $this->model_s_analisa_panen->get_stock_awal($ar,'GKM', 'KERNEL');
			}					
			
            foreach($data_panen as $row){
				
				if ($row['TBS_SML'] == 0 || $row['TBS_SML'] == NULL){
					$oer_sml = 0;	
					$ker_sml = 0;
				}else{
					$oer_sml =($row['CPO_SML']/$row['TBS_SML'])*100;
					$ker_sml =($row['KERNEL_SML']/$row['TBS_SML'])*100;
				}
				
				if ($row['TBS_SSS'] == 0 || $row['TBS_SSS'] == NULL){
					$oer_sss = 0;	
					$ker_sss = 0;
				}else{
					$oer_sss =($row['CPO_SSS']/$row['TBS_SSS'])*100;
					$ker_sss =($row['KERNEL_SSS']/$row['TBS_SSS'])*100;
				}
				
				if ($row['TBS_GKM'] == 0 || $row['TBS_GKM'] == NULL){
					$oer_gkm = 0;	
					$ker_gkm = 0;
				}else{
					$oer_gkm =($row['CPO_GKM']/$row['TBS_GKM'])*100;
					$ker_gkm =($row['KERNEL_GKM']/$row['TBS_GKM'])*100;
				}
				
				
				
                $ar3 = preg_split('/[- :]/',trim($row['BA_DATE']));
                $ar3 = implode('',$ar3);
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['BA_DATE'].'&nbsp;</td>';        
				
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TBS_SML']).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['DISPATCH_CPO_SML']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['DISPATCH_KERNEL_SML']).'&nbsp;</td>';
				
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['CPO_SML']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($oer_sml,2).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['KERNEL_SML']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($ker_sml,2).'  &nbsp;</td>';
				if ($stock_awal_cpo_sml+$row['CPO_SML']+$row['PURCHASE_CPO_SML']-($row['DISPATCH_CPO_SML']+$row['SALES_CPO_SML'])<0){
                $PANEN .= '<td width="100" class="tbl_td2" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_cpo_sml+$row['CPO_SML']+$row['PURCHASE_CPO_SML']-($row['DISPATCH_CPO_SML']+$row['SALES_CPO_SML'])).' &nbsp;</td>';    
				}else{
					$PANEN .= '<td width="100" class="tbl_td" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_cpo_sml+$row['CPO_SML']+$row['PURCHASE_CPO_SML']-($row['DISPATCH_CPO_SML']+$row['SALES_CPO_SML'])).' &nbsp;</td>';  	
				}
				if ($stock_awal_kernel_sml+$row['KERNEL_SML']+$row['PURCHASE_KERNEL_SML']-($row['DISPATCH_KERNEL_SML']+$row['SALES_KERNEL_SML'])<0){
					$PANEN .= '<td width="100" class="tbl_td2" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_kernel_sml+$row['KERNEL_SML']+$row['PURCHASE_KERNEL_SML']-($row['DISPATCH_KERNEL_SML']+$row['SALES_KERNEL_SML'])).'  &nbsp;</td>';	
				}else{
                	$PANEN .= '<td width="100" class="tbl_td" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_kernel_sml+$row['KERNEL_SML']+$row['PURCHASE_KERNEL_SML']-($row['DISPATCH_KERNEL_SML']+$row['SALES_KERNEL_SML'])).'  &nbsp;</td>';
				}
				
				
				//SSS
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TBS_SSS']).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['DISPATCH_CPO_SSS']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['DISPATCH_KERNEL_SSS']).'&nbsp;</td>';
				
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['CPO_SSS']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($oer_sss,2).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['KERNEL_SSS']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($ker_sss,2).'  &nbsp;</td>';
				if ($stock_awal_cpo_sss+$row['CPO_SSS']+$row['PURCHASE_CPO_SSS']-($row['DISPATCH_CPO_SSS']+$row['SALES_CPO_SSS'])<0) {
					$PANEN .= '<td width="100" class="tbl_td2" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_cpo_sss+$row['CPO_SSS']+$row['PURCHASE_CPO_SSS']-($row['DISPATCH_CPO_SSS']+$row['SALES_CPO_SSS'])).' &nbsp;</td>'; 
				}else{
                	$PANEN .= '<td width="100" class="tbl_td" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_cpo_sss+$row['CPO_SSS']+$row['PURCHASE_CPO_SSS']-($row['DISPATCH_CPO_SSS']+$row['SALES_CPO_SSS'])).' &nbsp;</td>'; 
				}
				if ($stock_awal_kernel_sss+$row['KERNEL_SSS']+$row['PURCHASE_KERNEL_SSS']-($row['DISPATCH_KERNEL_SSS']+$row['SALES_KERNEL_SSS'])<0)  {
					$PANEN .= '<td width="100" class="tbl_td2" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_kernel_sss+$row['KERNEL_SSS']+$row['PURCHASE_KERNEL_SSS']-($row['DISPATCH_KERNEL_SSS']+$row['SALES_KERNEL_SSS'])).'  &nbsp;</td>';	
				}else{
                	$PANEN .= '<td width="100" class="tbl_td" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_kernel_sss+$row['KERNEL_SSS']+$row['PURCHASE_KERNEL_SSS']-($row['DISPATCH_KERNEL_SSS']+$row['SALES_KERNEL_SSS'])).'  &nbsp;</td>';
				}
				
				//GKM
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TBS_GKM']).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['DISPATCH_CPO_GKM']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['DISPATCH_KERNEL_GKM']).'&nbsp;</td>';
				
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['CPO_GKM']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($oer_gkm,2).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['KERNEL_GKM']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($ker_gkm,2).'  &nbsp;</td>';
				if ($stock_awal_cpo_gkm+$row['CPO_GKM']+$row['PURCHASE_CPO_GKM']-($row['DISPATCH_CPO_GKM']+$row['SALES_CPO_GKM'])-$row['SLUDGE']<0){
					$PANEN .= '<td width="100" class="tbl_td2" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_cpo_gkm+$row['CPO_GKM']+$row['PURCHASE_CPO_GKM']-($row['DISPATCH_CPO_GKM']+$row['SALES_CPO_GKM'])-$row['SLUDGE']).' &nbsp;</td>'; 
				}else{
                	$PANEN .= '<td width="100" class="tbl_td" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_cpo_gkm+$row['CPO_GKM']+$row['PURCHASE_CPO_GKM']-($row['DISPATCH_CPO_GKM']+$row['SALES_CPO_GKM'])-$row['SLUDGE']).' &nbsp;</td>';   
				}
				
				if ($stock_awal_kernel_gkm+$row['KERNEL_GKM']+$row['PURCHASE_KERNEL_GKM']-($row['DISPATCH_KERNEL_GKM']+$row['SALES_KERNEL_GKM'])<0){
					$PANEN .= '<td width="100" class="tbl_td2" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_kernel_gkm+$row['KERNEL_GKM']+$row['PURCHASE_KERNEL_GKM']-($row['DISPATCH_KERNEL_GKM']+$row['SALES_KERNEL_GKM'])).'  &nbsp;</td>';	
				}else{
                	$PANEN .= '<td width="100" class="tbl_td" align="right" bgcolor="#FFFFCC">'.number_format($stock_awal_kernel_gkm+$row['KERNEL_GKM']+$row['PURCHASE_KERNEL_GKM']-($row['DISPATCH_KERNEL_GKM']+$row['SALES_KERNEL_GKM'])).'  &nbsp;</td>';
				}
				
                $PANEN .= '</tr>';
                $i++;    
				
				$stock_awal_cpo_sml = $stock_awal_cpo_sml+$row['CPO_SML']-$row['DISPATCH_CPO_SML'];
				$stock_awal_cpo_sss = $stock_awal_cpo_sss+$row['CPO_SSS']-$row['DISPATCH_CPO_SSS'];
				$stock_awal_cpo_gkm = $stock_awal_cpo_gkm+$row['CPO_GKM']-$row['DISPATCH_CPO_GKM']-$row['SLUDGE'];
				$stock_awal_kernel_sml = $stock_awal_kernel_sml+$row['KERNEL_SML']-$row['DISPATCH_KERNEL_SML'];
				$stock_awal_kernel_sss = $stock_awal_kernel_sss+$row['KERNEL_SSS']-$row['DISPATCH_KERNEL_SSS'];
				$stock_awal_kernel_gkm = $stock_awal_kernel_gkm+$row['KERNEL_GKM']-$row['DISPATCH_KERNEL_GKM'];
            }
			
			/*
            $PANEN .="<tr><td class='tbl_td' align='center' colspan='2'><strong>TOTAL</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_lhm)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_lhm,2)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_nab)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_nab,2)."&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_restan)."&nbsp;</strong></td>";
            */
			
            $PANEN .= "</table>"; 
            
            echo $PANEN;			
        }
        
    }
	
	function generate_monitor_tonase(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);		

        if(!empty($periode) && !empty($company)){
            $data_panen=$this->model_s_analisa_panen->generate_monitor_tonase($ar,$ar2,$company);
		
            $PANEN = "";
            $i = 1;
            
            $PANEN .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PANEN .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PANEN .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
            
            
            $PANEN .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";
            $PANEN .= "<tr><td  align='left' colspan='10' class='tbl_th'> *Summary TBS per tanggal </td> </tr>";    
            $PANEN .= "<tr><td  align='center' rowspan='3' class='tbl_th'> NO. </td>";
            $PANEN .= "<td rowspan='3' align='center' class='tbl_th'>PT</td>";
            
            $PANEN .= "<tr><td align='center' class='tbl_th' colspan='2'>DATA TIMBANGAN</td>";
			if ($company=='ASL' || $company=='SSS' || $company=='MSS'){
				$PANEN .= "<td align='center' class='tbl_th' colspan='2'>JJG PANEN * BJR</td>";	
			}else{
	            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>BA PRODUKSI HARIAN</td>";
			}
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>DATA DISTRIBUSI NAB</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>PROGRESS PANEN</td>";
			
            $PANEN .= "<tr><td align='center' class='tbl_th'>TANGGAL TIMBANG</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TONASE TIMBANG</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TANGGAL BA</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TONASE BA</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TANGGAL NAB</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TONASE NAB</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TANGGAL PROGRESS</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TONASE PROGRESS</td>";
            
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
			$location_code =''; 
			
			$total_tonase_tbg=0;
            $total_tonase_ba=0;
            $total_tonase_angkut=0;
            $total_tonase_progress=0;
            $total_tonase_summprogress=0;

            foreach($data_panen as $row){
				
                $ar3 = preg_split('/[- :]/',trim($row['TANGGAL']));
                $ar3 = implode('',$ar3);
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
                $PANEN .= "<td width='100' class='tbl_td' ".$style."><strong>
                    <a href='".$url."get_panen_breakdown/".$row['COMPANY_CODE']."/".$ar3.
                    "' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><center>".$row['COMPANY_CODE']."</center></a></strong></td>";                
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['TANGGAL'].'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TONASE_TIMBANG']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['TANGGAL_BA'].'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TONASE_BA']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['TANGGAL_NAB'].' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TONASE_NAB']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['TANGGAL_PROGRESS'].'  &nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TONASE_PROGRESS']).' &nbsp;</td>'; 				
                $PANEN .= '</tr>';
				$total_tonase_tbg+=$row['TONASE_TIMBANG'];
				$total_tonase_ba+=$row['TONASE_BA'];
                $total_tonase_angkut+=$row['TONASE_NAB'];
				$total_tonase_progress+=$row['TONASE_PROGRESS'];
                $i++;    
            }
            $PANEN .="<tr><td class='tbl_td' align='center' colspan='2'><strong>TOTAL</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_tonase_tbg)."&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_tonase_ba)."&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_tonase_angkut)."&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_tonase_progress,2)."&nbsp;</strong></td>";
			            
            $PANEN .= "</table>"; 
			
			//tabel2
			$i = 1;
			$PANEN .= "<br> <br>";
			$PANEN .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";
            $PANEN .= "<tr><td  align='left' colspan='6' class='tbl_th'> *Summary tonase TBS per No NAB </td> </tr>";    
            $PANEN .= "<tr><td  align='center' rowspan='3' class='tbl_th'> NO. </td>";
            $PANEN .= "<td rowspan='3' align='center' class='tbl_th'>PT</td>";
			$PANEN .= "<td rowspan='3' align='center' class='tbl_th'>NO NAB</td>";
            $PANEN .= "<td rowspan='3' align='center' class='tbl_th'>TANGGAL</td>";
			
            $PANEN .= "<tr><td align='center' class='tbl_th'>DATA TIMBANGAN</td>";
            $PANEN .= "<td align='center' class='tbl_th'>DATA DISTRIBUSI NAB</td>";
			
            $PANEN .= "<tr> <td align='center' class='tbl_th'>TONASE</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TONASE</td> </tr>";
            
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
			$location_code =''; 
			
			$total_tonase_timbang=0;
            $total_tonase_nab=0;
			
			$data_angkut=$this->model_s_analisa_panen->generate_tonase_pernab($ar,$ar2,$company);
            foreach($data_angkut as $row){
				
                $ar3 = preg_split('/[- :]/',trim($row['TANGGAL']));
                $ar3 = implode('',$ar3);
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
                $PANEN .= "<td width='100' class='tbl_td' ".$style."><strong>
                    <a href='".$url."get_panen_breakdown/".$row['COMPANY_CODE']."/".$ar3.
                    "' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><center>".$row['COMPANY_CODE']."</center></a></strong></td>";                
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['NO_SPB'].'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['TANGGAL'].'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TONASE_TIMBANG']).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['TONASE_NAB']).' &nbsp;</td>'; 
                $PANEN .= '</tr>';
				$total_tonase_timbang+=$row['TONASE_TIMBANG'];
                $total_tonase_nab+=$row['TONASE_NAB'];
                $i++;    
            }

            $PANEN .="<tr><td class='tbl_td' align='center' colspan='4'><strong>TOTAL</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_tonase_timbang)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_tonase_nab)." &nbsp;</strong></td></tr>";
            $PANEN .= "</table>"; 			
			//            
            echo $PANEN;			
        }
        
    }
	
	function generate_monitor_tonase_xls(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
		
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_panen=$this->model_s_analisa_panen->generate_monitor_tonase($ar,$ar2,$company);

        //baris 1
        $headers .= "NO. \t";
		$headers .= "PT \t";
        $headers .= "TANGGAL TIMBANG \t";
        $headers .= "TONASE TIMBANG \t";
        $headers .= "TANGGAL BA \t";
        $headers .= "TONASE BA \t";
		$headers .= "TANGGAL NAB \t"; 
        $headers .= "TONASE NAB \t"; 
		$headers .= "TANGGAL PROGRESS \t";
        $headers .= "TONASE PROGRESS \t";
        
        $no = 1;
        foreach ($data_panen as $row){											
            $line = '';
            $line .= str_replace('"', '""',$no)."\t";       
            $line .= str_replace('"', '""',$row['COMPANY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['TANGGAL'])."\t"; 
            $line .= str_replace('"', '""',$row['TONASE_TIMBANG'])."\t";
			$line .= str_replace('"', '""',$row['TANGGAL_BA'])."\t"; 
            $line .= str_replace('"', '""',$row['TONASE_BA'])."\t";
			$line .= str_replace('"', '""',$row['TANGGAL_NAB'])."\t";
            $line .= str_replace('"', '""',$row['TONASE_NAB'])."\t";
			$line .= str_replace('"', '""',$row['TANGGAL_PROGRESS'])."\t";
            $line .= str_replace('"', '""',$row['TONASE_PROGRESS'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
		//
		$data_angkut=$this->model_s_analisa_panen->generate_tonase_pernab($ar,$ar2,$company);

        //baris 1
		$data .= "\n";
		$data .= "\n";
        $data .= "NO. \t";
		$data .= "PT \t";
        $data .= "NO NAB \t";
        $data .= "TANGGAL \t";
        $data .= "TONASE TIMBANG \t";
        $data .= "TONASE ANGKUT \n";		
        
        $no = 1;
        foreach ($data_angkut as $row){											
            $line = '';
            $line .= str_replace('"', '""',$no)."\t";       
            $line .= str_replace('"', '""',$row['COMPANY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t"; 
            $line .= str_replace('"', '""',$row['TANGGAL'])."\t";
			$line .= str_replace('"', '""',$row['TONASE_TIMBANG'])."\t"; 
            $line .= str_replace('"', '""',$row['TONASE_NAB'])."\t";			
            $no++;
            $data .= trim($line)."\n";  
        }        
		//
        $data = str_replace("\r","",$data);
                 
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=MONITORING_TONASE_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
	
	//Start: Adedd By Asep, 20140220
	function regenerate_tonase($periode,$periode_to){
		
		$query ="SELECT data_nab.ID_ANON, data_nab.NO_SPB, data_nab.BLOCK, data_total.TOTAL_BERAT_EMPIRIS AS TOTAL_BERAT_EMPIRIS, 
(data_nab.JANJANG*data_bjr.VALUE) AS BERAT_EMPIRIS, data_nab.JANJANG, data_bjr.VALUE AS BJR, data_timbang.BERAT_BERSIH, 
(((data_nab.JANJANG*data_bjr.VALUE)/data_total.TOTAL_BERAT_EMPIRIS)*data_timbang.BERAT_BERSIH) AS TONASE ,data_nab.COMPANY_CODE  
FROM
(
	SELECT nabd.ID_ANON, nab.NO_SPB, nabd.BLOCK, nabd.JANJANG, nab.COMPANY_CODE
	FROM s_nota_angkutbuah nab 
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.TANGGAL BETWEEN '".$periode."' AND '".$periode_to."' AND nab.ACTIVE =1
	AND nabd.TONASE = 0 
) data_nab
INNER JOIN (
	SELECT bj.BLOCK,bj.VALUE,bj.COMPANY_CODE 
	FROM(
		SELECT AFD,BLOCK, VALUE, CONCAT(TAHUN,BULAN) AS PERIODE, COMPANY_CODE 
		FROM s_data_bjr  
		WHERE ACTIVE=1 )
bj
	JOIN (
		SELECT AFD,BLOCK,MAX(CONCAT(TAHUN,BULAN)) AS MAX_PERIODE
		FROM s_data_bjr
		WHERE CONCAT(TAHUN,BULAN) <= DATE_FORMAT('".$periode."','%Y%m') AND ACTIVE=1
		GROUP BY BLOCK 
	) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK AND bjr.MAX_PERIODE = bj.PERIODE
	
	GROUP BY bj.BLOCK,COMPANY_CODE
) data_bjr ON data_nab.BLOCK = data_bjr.BLOCK AND data_nab.COMPANY_CODE = data_bjr.COMPANY_CODE
INNER JOIN (
	SELECT data_nab.NO_SPB, SUM(data_nab.JANJANG*data_bjr.VALUE) AS TOTAL_BERAT_EMPIRIS
	FROM
	(
		SELECT nab.NO_SPB, nabd.BLOCK,nabd.JANJANG, nab.COMPANY_CODE
		FROM s_nota_angkutbuah nab 
		INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
		WHERE nab.TANGGAL BETWEEN '".$periode."' AND '".$periode_to."'
		AND nabd.TONASE = 0 
	) data_nab
	INNER JOIN (
		SELECT bj.BLOCK,bj.VALUE,bj.COMPANY_CODE 
		FROM(
			SELECT AFD,BLOCK, VALUE, CONCAT(TAHUN,BULAN) AS PERIODE, COMPANY_CODE 
			FROM s_data_bjr  
			WHERE ACTIVE=1 )
	bj
		JOIN (
			SELECT AFD,BLOCK,MAX(CONCAT(TAHUN,BULAN)) AS MAX_PERIODE
			FROM s_data_bjr
			WHERE CONCAT(TAHUN,BULAN) <= DATE_FORMAT('".$periode."','%Y%m') AND ACTIVE=1
			GROUP BY BLOCK 
		) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK AND bjr.MAX_PERIODE = bj.PERIODE
		
		GROUP BY bj.BLOCK,COMPANY_CODE
	) data_bjr ON data_nab.BLOCK = data_bjr.BLOCK AND data_nab.COMPANY_CODE = data_bjr.COMPANY_CODE
	GROUP BY data_nab.NO_SPB
) data_total ON data_nab.no_spb = data_total.no_spb
INNER JOIN(
	SELECT t.NO_SPB, t.BERAT_BERSIH FROM s_data_timbangan t
	WHERE t.ACTIVE = 1 AND t.TANGGALM BETWEEN '".$periode."' AND '".$periode_to."'
) data_timbang ON data_nab.no_spb = data_timbang.no_spb
;";
		$this->db->reconnect();
		$data_tonase = $this->db->query($query);
		if($data_tonase->num_rows() > 0){
			foreach ($data_tonase->result_array() as $row_tonase){
				$sUpdateNab="UPDATE s_nota_angkutbuah nab SET TOTAL_BERAT_EMPIRIS=". $row_tonase['TOTAL_BERAT_EMPIRIS'] ." WHERE NO_SPB='". $row_tonase['NO_SPB'] ."';";
				//var_dump('*************update sUpdateNab***************');
				//var_dump($sUpdateNab);
				$this->db->reconnect();
				$this->db->query($sUpdateNab);	
				$sUpdateDetail="UPDATE s_nota_angkutbuah_detail SET TONASE=". $row_tonase['TONASE'] .", BERAT_EMPIRIS=". $row_tonase['BERAT_EMPIRIS'] .", BJR=". $row_tonase['BJR'] .", UPDATE_BY='JOB_SCHEDULLER', UPDATE_TIME=NOW()
WHERE ID_ANON ='". $row_tonase['ID_ANON'] ."'";
				//var_dump('*************update sUpdateDetail***************');
				//var_dump($sUpdateDetail);
				$this->db->query($sUpdateDetail);	
				$this->round_tonase($row_tonase['NO_SPB']);
			}
		}
		
	}
	
	function round_all($periode, $periode_to){
		/*
		$qNab= "SELECT nab.NO_SPB FROM s_nota_angkutbuah nab
				WHERE nab.TANGGAL BETWEEN '". $periode ."' AND '". $periode_to ."' AND nab.ACTIVE = 1";	
		*/
		$qNab= "SELECT nab.NO_SPB
				FROM s_nota_angkutbuah nab 
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				INNER JOIN s_data_timbangan timbang ON nab.NO_SPB = timbang.NO_SPB 
				WHERE nab.TANGGAL BETWEEN '". $periode ."' AND '". $periode_to ."' AND nab.ACTIVE = 1
				GROUP BY nab.ID_NT_AB";		
		$dataNab =$this->db->query($qNab);
		if(!$dataNab->num_rows() == 0){
			foreach ($dataNab->result_array() as $row_nab){	
				$this->round_tonase($row_nab['NO_SPB']);
			}
		}
	}
	
	function round_tonase($id_nab){
		$i=0;		
		$sisa=0;
		$round_tonase=0;
		$qNab= "SELECT nabd.ID_ANON, nabd.BLOCK, nabd.TONASE, ROUND(nabd.TONASE) AS ROUND_TONASE, (nabd.TONASE - ROUND(nabd.TONASE)) AS SISA, timbang.BERAT_BERSIH FROM s_nota_angkutbuah nab 
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				INNER JOIN s_data_timbangan timbang ON nab.NO_SPB = timbang.NO_SPB 
				WHERE nab.NO_SPB = '". $id_nab ."' AND nab.ACTIVE=1";
		$this->db->reconnect();
		$dataNab =$this->db->query($qNab);
		$i=$dataNab->num_rows();
		if($i > 0){
			foreach ($dataNab->result_array() as $row_nab){				
				$sisa=$sisa+$row_nab['SISA'];
				if ($i == 1){				
					$round_tonase=round(($row_nab['ROUND_TONASE']+$sisa),0);
				}else{
					$round_tonase=$row_nab['ROUND_TONASE'];					
				}
				$i=$i-1;								
				$sUpdateDetail="UPDATE s_nota_angkutbuah_detail SET ROUND_TONASE = ". $round_tonase ." WHERE ID_ANON ='". $row_nab['ID_ANON'] ."'";
				$this->db->reconnect();
				$this->db->query($sUpdateDetail);
				//var_dump('*************update sUpdateDetail***************');
				//var_dump($sUpdateDetail);
			}
		}
		
	}
	
	//End: Adedd By Asep, 20140220
	function generate_closing(){
		$today = date('Ymd');
		$awal_bulan = date("Ym",strtotime($today))."01";
		$data_panen=$this->model_s_analisa_panen->generate_closing($awal_bulan, $today);
	}
	//Added By Asep, 20130731
	function generate_nab(){
		var_dump("WAIT...");
		$today = date('Ymd');
		$yesterday = strtotime('-2 day',strtotime($today)); 
		$awal_bulan = date("Ym",$yesterday)."01";
		$yesterday = date('Ymd', $yesterday);
		$awal_bulan = '20150901';
		$yesterday = '20150930';

		$array_company = array('MAG','LIH', 'NAK', 'TPAI','GKM', 'SSS', 'SML', 'ASL', 'MSS');
		//$array_company = array('LIH', 'NAK', 'TPAI','GKM', 'SSS', 'SML', 'ASL', 'MSS');
		//$array_company = array('LIH','TPAI');
		//$array_company = array('SSS');
		//$array_company = array('TPAI','SML');
		//$array_company = array('SML');
		//$array_company = array('ASL', 'MSS');
		
		$this->round_all($awal_bulan,$yesterday);
		//$this->regenerate_tonase($awal_bulan,$yesterday);
		
		foreach($array_company as $i => $company){
			
			if($company == 'GKM' || $company == 'SML'){ 
				$tabel1='dummy_mgangactivitydetail_gkm';
				$tabel2='dummy_pprogress_gkm';	
				$db_other = $this->load->database('lhm_gkm', TRUE);
			}else{
				$tabel1='m_gang_activity_detail';
				$tabel2='p_progress';
			}
	
			//if(!empty($periode) && !empty($company)){					
			$data_panen=$this->model_s_analisa_panen->runjob_nab($awal_bulan,$yesterday,$company);
			//var_dump($data_panen);				
			
			if ($data_panen[0]!=NULL){
				$restan = 0;
				$shi_janjang_angkut = 0; 
				$shi_berat_angkut = 0;
				$shi_janjang_panen = 0;
				$shi_berat_panen =0;
				$bjr_real = 0; 
				$location_code =''; 
				
				if($company == 'GKM' || $company == 'SML'){ 
					$status2=$this->model_s_analisa_panen->delete_progress_gkm($awal_bulan,$yesterday,$company);
				}else{				
					$status2=$this->model_s_analisa_panen->delete_progress($awal_bulan,$yesterday,$company);
				}
				
				$status=$this->model_s_analisa_panen->delete_rpt_nab($awal_bulan,$yesterday,$company);
				//var_dump($status." status - status2 ".$status2);
				
				$this->regenerate_realisasi($company, $awal_bulan, $yesterday);
				
				if ($status==TRUE&&$status2==TRUE){ 
					foreach($data_panen as $row){					
						$tanggal=$row['TANGGAL'];		
						$location_code = $row['LOCATION_CODE'];					
						$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi('20150901', $tanggal,$company,$location_code,$tabel1);			
						$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi('20150901', $tanggal,$company,$location_code, $tabel1);
						$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi('20150901', $tanggal,$company,$location_code);
						$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi('20150901', $tanggal,$company,$location_code);	
						$restan = $this->model_s_analisa_panen->get_restan('20150901', $tanggal,$company,$location_code,$tabel1);
							
						$sInsert ="INSERT INTO rpt_nab
									(DATE_TRANSACT, INPUT_BY, COMPANY_CODE, LOCATION_CODE, JUMLAH_POKOK,
									PLANTED_AREA, JANJANG_PANEN, JANJANG_PANEN_SHI, BERAT_PANEN, BERAT_PANEN_SHI,
									JANJANG_ANGKUT, JANJANG_ANGKUT_SHI, BERAT_ANGKUT, BERAT_ANGKUT_SHI, BJR_REAL,
									BJR_DITETAPKAN, JANJANG_RESTAN, JANJANG_AFKIR
									)
									VALUES ('". $row['TANGGAL'] ."', 'JOB_SCHEDULER', '".$company."','".$row['LOCATION_CODE']."', '', 
											'', '".$row['JANJANG_PANEN']."', '".$shi_janjang_panen."', '".$row['BERAT_PANEN']."', '".$shi_berat_panen."',
											'".$row['JJG_ANGKUT']."', '".$shi_janjang_angkut."', '".$row['BERAT_ANGKUT']."', '".$shi_berat_angkut."','".$row['BJR_REAL']."',
											'".$row['BJR']."', '".$restan."', '".$row['AFKIR']."')";	
						//var_dump('*************insert rptnab***************');
						//var_dump($sInsert);	

						$this->db->reconnect();						
						$insert=$this->db->query($sInsert);														
						
						if ($company=='MAG' || $company=='LIH' || $company=='TPAI' || $company=='MSS' || $company=='ASL'){										
							$sCheckPanen="SELECT GANG_CODE, LEFT(m_gang_activity_detail.LOCATION_CODE,2) AS AFD, m_gang_activity_detail.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM m_gang_activity_detail
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM m_gang_activity_detail g 
	WHERE g.LHM_DATE = '".$row['TANGGAL']."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = m_gang_activity_detail.LOCATION_CODE
WHERE LHM_DATE = '".$row['TANGGAL']."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND m_gang_activity_detail.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, m_gang_activity_detail.LOCATION_CODE";
							//var_dump('*************sCheckPanen***************');
							//var_dump($sCheckPanen);
							$this->db->reconnect();
							$qsCheckPanen = $this->db->query($sCheckPanen);	
							$sisaPtonase = 0;
							$sisaTonase = 0;
							$roundPtonase = 0;
							$i=0;
							$i=$qsCheckPanen->num_rows();
							if($i == 0 || $i == null){
								$sDateLHM="SELECT max(g.LHM_DATE) AS LHM_DATE
FROM m_gang_activity_detail g WHERE g.LHM_DATE <= '".$row['TANGGAL']."' AND g.COMPANY_CODE = '".$company."' AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'";
								$this->db->reconnect();
								$qDateLHM= $this->db->query($sDateLHM);
								//var_dump('*************qDateLHM***************');
								//var_dump($sDateLHM);
								if(!$qDateLHM->num_rows() == 0){
									$rows = $qDateLHM->row();									            
									$lhm_date = $rows->LHM_DATE; 
									$sCheckPanen2="SELECT GANG_CODE, LEFT(m_gang_activity_detail.LOCATION_CODE,2) AS AFD, m_gang_activity_detail.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM m_gang_activity_detail
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM m_gang_activity_detail g 
	WHERE g.LHM_DATE = '".$lhm_date."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = m_gang_activity_detail.LOCATION_CODE
WHERE LHM_DATE = '".$lhm_date."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND m_gang_activity_detail.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, m_gang_activity_detail.LOCATION_CODE";
								//var_dump('*************sCheckPanen2***************');
								//var_dump($sCheckPanen2);
									$this->db->reconnect();
									$qsCheckPanen = $this->db->query($sCheckPanen2);	
								}
								
							}
							if(!$qsCheckPanen->num_rows() == 0){
								$i=$qsCheckPanen->num_rows();								
								foreach ($qsCheckPanen->result_array() as $row_progress){									
									$pTonase=0;
									$pJanjang=0;
									//$sisaTonase=$sisaTonase+$sisaPtonase;
									if ($row_progress['TOTAL_JANJANG']!=0){
										$pTonase=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['BERAT_ANGKUT']);
										$roundPtonase = round($pTonase);
										$sisaPtonase = $pTonase-$roundPtonase;
										$sisaTonase=$sisaTonase+$sisaPtonase;
										if ($i == 1){
											$roundPtonase=$roundPtonase+$sisaTonase;	
										}else{
											$roundPtonase=$roundPtonase;		
										}
										$i=$i-1;										
										$pJanjang=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['JANJANG_PANEN']);
									}else{
										$roundPtonase=$row['BERAT_ANGKUT'];
										$pJanjang=0;
									}
									
									$sProgress = "SELECT COUNT(p.ID_PROGRESS) AS JML FROM p_progress p
		WHERE  p.TGL_PROGRESS = '". $row['TANGGAL'] ."' AND p.COMPANY_CODE = '".$company."' 
		AND p.ACTIVITY_CODE='8601003' AND p.GANG_CODE='". $row_progress['GANG_CODE'] ."' AND p.LOCATION_CODE = '". $row_progress['LOCATION_CODE'] ."';";
									$this->db->reconnect();
									$cekProgress = $this->db->query($sProgress);
									$rowJml = $cekProgress->row();
									$jmlProgress = $rowJml->JML;
									//var_dump('*************sProgress***************');
									//var_dump($sProgress);
									if ($jmlProgress==0){
										//var_dump($sProgress);
										//var_dump($cekProgress->num_rows());
									//if ($cekProgress->num_rows()==0){
										$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
	HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
	VALUES ('". $row_progress['GANG_CODE'] ."', '". $row_progress['AFD'] ."', '". $row['TANGGAL'] ."', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $roundPtonase .", 'Jjg', ". $pJanjang .", 0, 0, 0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
									}else{
										$insertProgress="UPDATE p_progress SET AFD ='". $row_progress['AFD'] ."', SATUAN = 'Kg', HASIL_KERJA=". $roundPtonase .", SATUAN2='Jjg', HASIL_KERJA2=". $pJanjang .", UPDATE_BY='JOB_SCHEDULLER', UPDATE_DATE = NOW()
		WHERE  TGL_PROGRESS = '". $row['TANGGAL'] ."' AND COMPANY_CODE = '".$company."' 
		AND ACTIVITY_CODE='8601003' AND GANG_CODE='". $row_progress['GANG_CODE'] ."' AND LOCATION_CODE = '". $row_progress['LOCATION_CODE'] ."';";	
									}
									$this->db->reconnect();
									$this->db->query($insertProgress);
									//var_dump('*************INSERT PROGRESS***************');
									//var_dump($insertProgress);
								}	
							}				
						}else if($company=='NAK'){
							$insertProgress="INSERT INTO p_progress (AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". substr($row['LOCATION_CODE'],0,2) ."', '". $row['TANGGAL'] ."', '". $row['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $row['BERAT_ANGKUT'] .", 'Jjg', ". $row['JJG_ANGKUT'] .", 0,0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
							$this->db->reconnect();
							$this->db->query($insertProgress);	
						}else if($company=='SAP' || $company=='SSS'){
							$sCheckPanen="SELECT GANG_CODE, LEFT(m_gang_activity_detail.LOCATION_CODE,2) AS AFD, m_gang_activity_detail.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM m_gang_activity_detail
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM m_gang_activity_detail g 
	WHERE g.LHM_DATE = '".$row['TANGGAL']."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = m_gang_activity_detail.LOCATION_CODE
WHERE LHM_DATE = '".$row['TANGGAL']."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND m_gang_activity_detail.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, m_gang_activity_detail.LOCATION_CODE";
							//var_dump('*************sCheckPanen***************');
							//var_dump($sCheckPanen);
							$this->db->reconnect();
							$qsCheckPanen = $this->db->query($sCheckPanen);	
							$sisaPtonase = 0;
							$sisaTonase = 0;
							$roundPtonase = 0;
							$i=0;
							//asep
							if(!$qsCheckPanen->num_rows() == 0){
								foreach ($qsCheckPanen->result_array() as $row_progress){									
									$pTonase=0;
									$pJanjang=0;
									//$sisaTonase=$sisaTonase+$sisaPtonase;
									if ($row_progress['TOTAL_JANJANG']!=0){
										$pTonase=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['BERAT_PANEN']);
										$roundPtonase = ($pTonase);
										$sisaPtonase = $pTonase-$roundPtonase;
										$sisaTonase=$sisaTonase+$sisaPtonase;

										if ($i == 1){
											$roundPtonase=$roundPtonase+$sisaTonase;	
										}else{
											$roundPtonase=$roundPtonase;		
										}
										$i=$i-1;
										$pJanjang=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['JANJANG_PANEN']);
									}else{
										$roundPtonase=$row['BERAT_PANEN'];
										$pJanjang=0;
									}									
									$sProgress = "SELECT COUNT(p.ID_PROGRESS) AS JML FROM p_progress p
		WHERE  p.TGL_PROGRESS = '". $row['TANGGAL'] ."' AND p.COMPANY_CODE = '".$company."' 
		AND p.ACTIVITY_CODE='8601003' AND p.GANG_CODE='". $row_progress['GANG_CODE'] ."' AND p.LOCATION_CODE = '". $row['LOCATION_CODE'] ."';";
									$this->db->reconnect();
									$cekProgress = $this->db->query($sProgress);
									$rowJml = $cekProgress->row();
									$jmlProgress = $rowJml->JML;
									if ($jmlProgress==0){										
										$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_TYPE_CODE, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row_progress['GANG_CODE'] ."', '". substr($row_progress['LOCATION_CODE'],0,2) ."', '". $row['TANGGAL'] ."', 'OP', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $roundPtonase .", 'Jjg', ". $pJanjang .", 0,0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
										//var_dump('*************INSERT PROGRESS***************');
									//var_dump($insertProgress);
									}else{											
										$insertProgress="UPDATE p_progress SET AFD ='". substr($row_progress['LOCATION_CODE'],0,2) ."', SATUAN = 'Kg', HASIL_KERJA=". $roundPtonase .", SATUAN2='Jjg', HASIL_KERJA2=". $pJanjang .", UPDATE_BY='JOB_SCHEDULLER', UPDATE_DATE = NOW()
		WHERE  TGL_PROGRESS = '". $row['TANGGAL'] ."' AND COMPANY_CODE = '".$company."' 
		AND ACTIVITY_CODE='8601003' AND GANG_CODE='". $row_progress['GANG_CODE'] ."' AND LOCATION_CODE = '". $row_progress['LOCATION_CODE'] ."';";
										//var_dump('*************update PROGRESS***************');
										//var_dump($insertProgress);
									}
									$this->db->reconnect();
									$this->db->query($insertProgress);
									
								}//foreach	
							}
							//asep
						}else if ($company=='GKM' || $company=='SML'){										
							$sCheckPanen="SELECT GANG_CODE, LEFT(dummy_mgangactivitydetail_gkm.LOCATION_CODE,2) AS AFD, dummy_mgangactivitydetail_gkm.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM dummy_mgangactivitydetail_gkm
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM dummy_mgangactivitydetail_gkm g 
	WHERE g.LHM_DATE = '".$row['TANGGAL']."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = dummy_mgangactivitydetail_gkm.LOCATION_CODE
WHERE LHM_DATE = '".$row['TANGGAL']."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND dummy_mgangactivitydetail_gkm.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, dummy_mgangactivitydetail_gkm.LOCATION_CODE";
							//var_dump('*************sCheckPanen***************');
							//var_dump($sCheckPanen);
							$this->db->reconnect();
							$qsCheckPanen = $this->db->query($sCheckPanen);	
							$sisaPtonase = 0;
							$sisaTonase = 0;
							$roundPtonase = 0;
							$i=0;
							$i=$qsCheckPanen->num_rows();
							
							if($i == 0 || $i == null){
								$sDateLHM="SELECT max(g.LHM_DATE) AS LHM_DATE
FROM dummy_mgangactivitydetail_gkm g WHERE g.LHM_DATE <= '".$row['TANGGAL']."' AND g.COMPANY_CODE = '".$company."' AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'";
								$this->db->reconnect();
								$qDateLHM= $this->db->query($sDateLHM);								
								$rows = $qDateLHM->row();									            
								$lhm_date = $rows->LHM_DATE;
								var_dump('*************qDateLHM***************');
								var_dump($lhm_date <> null || $lhm_date <> ''); 
								//if(!$qDateLHM->num_rows() == 0){
								if($lhm_date <> null || $lhm_date <> ''){										
									$sCheckPanen2="SELECT GANG_CODE, LEFT(dummy_mgangactivitydetail_gkm.LOCATION_CODE,2) AS AFD, dummy_mgangactivitydetail_gkm.LOCATION_CODE, SUM(HSL_KERJA_VOLUME) AS JANJANG, TOTAL.TOTAL_JANJANG, SUM(HK_JUMLAH) AS HK 
FROM dummy_mgangactivitydetail_gkm
LEFT JOIN (
	SELECT g.LOCATION_CODE, SUM(g.HSL_KERJA_VOLUME) AS TOTAL_JANJANG FROM dummy_mgangactivitydetail_gkm g 
	WHERE g.LHM_DATE = '".$lhm_date."' AND g.COMPANY_CODE = '".$company."' 
	AND g.ACTIVITY_CODE = '8601003' AND g.LOCATION_CODE = '".$row['LOCATION_CODE']."'
) TOTAL ON  TOTAL.LOCATION_CODE = dummy_mgangactivitydetail_gkm.LOCATION_CODE
WHERE LHM_DATE = '".$lhm_date."' AND COMPANY_CODE = '".$company."' 
AND ACTIVITY_CODE = '8601003' AND dummy_mgangactivitydetail_gkm.LOCATION_CODE = '".$row['LOCATION_CODE']."'
GROUP BY GANG_CODE, dummy_mgangactivitydetail_gkm.LOCATION_CODE";
								//var_dump('*************sCheckPanen2***************');
								//var_dump($sCheckPanen2);
									$this->db->reconnect();
									$qsCheckPanen = $this->db->query($sCheckPanen2);	
								}
								 //start: diremark asep 20150105 if !$qDateLHM->num_rows() == 0
								else{
									$insertProgress="INSERT INTO p_progress (AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row_progress['AFD'] ."', '". $row['TANGGAL'] ."', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $row['BERAT_ANGKUT'] .", 'Jjg', ". $row['JANJANG_PANEN'] .", 0, 0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
									$db_other->reconnect();
									$db_other->query($insertProgress);
									var_dump('*************INSERT PROGRESS GKM***************');
									var_dump($insertProgress);

								} //end: diremark asep 20150105 if !$qDateLHM->num_rows() == 0
								
							} //diremark asep 20150105
							
							if(!$qsCheckPanen->num_rows() == 0){
								$i=$qsCheckPanen->num_rows();
								foreach ($qsCheckPanen->result_array() as $row_progress){									
									$pTonase=0;
									$pJanjang=0;
									//$sisaTonase=$sisaTonase+$sisaPtonase;
									if ($row_progress['TOTAL_JANJANG']!=0){
										$pTonase=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['BERAT_ANGKUT']);
										$roundPtonase = round($pTonase);
										$sisaPtonase = $pTonase-$roundPtonase;
										$sisaTonase=$sisaTonase+$sisaPtonase;

										if ($i == 1){
											$roundPtonase=$roundPtonase+$sisaTonase;	
										}else{
											$roundPtonase=$roundPtonase;		
										}
										$i=$i-1;
										$pJanjang=(($row_progress['JANJANG']/$row_progress['TOTAL_JANJANG'])*$row['JANJANG_PANEN']);
									}else{
										$roundPtonase=$row['BERAT_ANGKUT'];
										$pJanjang=0;
									}
									$sProgress = "SELECT COUNT(p.ID_PROGRESS) AS JML FROM p_progress p
		WHERE  p.TGL_PROGRESS = '". $row['TANGGAL'] ."' AND p.COMPANY_CODE = '".$company."' 
		AND p.ACTIVITY_CODE='8601003' AND p.GANG_CODE='". $row_progress['GANG_CODE'] ."' AND p.LOCATION_CODE = '". $row_progress['LOCATION_CODE'] ."';";
									//var_dump($sProgress);
									$db_other->reconnect();
									$cekProgress = $db_other->query($sProgress);
									$rowJml = $cekProgress->row();
									$jmlProgress = $rowJml->JML;
									//var_dump($jmlProgress);
									if ($jmlProgress==0){
										$insertProgress="INSERT INTO p_progress (GANG_CODE, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, SATUAN2, 
HASIL_KERJA2, REALISASI, HK, REALISASI_HK, REALISASI_UNIT, INPUT_BY, INPUT_DATE, COMPANY_CODE)
VALUES ('". $row_progress['GANG_CODE'] ."', '". $row_progress['AFD'] ."', '". $row['TANGGAL'] ."', '". $row_progress['LOCATION_CODE'] ."', '8601003', 'PANEN', 'Kg', ". $roundPtonase .", 'Jjg', ". $pJanjang .", 0, 0,0,0,'JOB_SCHEDULLER',NOW(),'".$company."')";
									}else{
										$insertProgress="UPDATE p_progress SET AFD ='". $row_progress['AFD'] ."', SATUAN = 'Kg', HASIL_KERJA=". $roundPtonase .", SATUAN2='Jjg', HASIL_KERJA2=". $pJanjang .", UPDATE_BY='JOB_SCHEDULLER', UPDATE_DATE = NOW()
		WHERE  TGL_PROGRESS = '". $row['TANGGAL'] ."' AND COMPANY_CODE = '".$company."' 
		AND ACTIVITY_CODE='8601003' AND GANG_CODE='". $row_progress['GANG_CODE'] ."' AND LOCATION_CODE = '". $row_progress['LOCATION_CODE'] ."';";	
									}
									$db_other->reconnect();
									$db_other->query($insertProgress);
									//var_dump('*************INSERT PROGRESS GKM***************');
									//var_dump($insertProgress);
								}								
							}										
							
						}
						
					}// for each 	
					//$this->regenerate_realisasi($company,$awal_bulan,$yesterday);
				}//$status
				
			}//$data_panen[0]			
		}//foreach company		
   	}

	function regenerate_realisasi($company, $awal_bulan, $yesterday){
		$awal_bulan= date("Y-m-d", strtotime($awal_bulan));
		$yesterday= date("Y-m-d", strtotime($yesterday));
		if ($company=='GKM' || $company=='SML'){
			$db_other = $this->load->database('lhm_gkm', TRUE);
		}
		
		while (strtotime($awal_bulan) <= strtotime($yesterday)) {
			$qSP ="CALL sp_generate_progress_panen(?, ?)";
			$month = date("Ymd",strtotime($awal_bulan));	
			$result = false;
			if ($company=='GKM' || $company=='SML'){					
				$db_other->reconnect();
				$sukses=$db_other->query($qSP,array($company,$month));
			}else{
				$this->db->reconnect();	
				$sukses=$this->db->query($qSP,array($company,$month));
			}
			var_dump($month);
			$jobStatus="SELECT status FROM s_job_status";
			
			$looping = true;
			while($looping){				
				$this->db->reconnect();
				$sQuery = $this->db->query($jobStatus);
				$row = $sQuery->row();            
	            		$status = $row->status; 
				
				if ($status==true||$status=='1'){
					$looping = false;
				}else{
					//sleep(3);
				}
			}

			$awal_bulan = date ("Y-m-d", strtotime("+1 day", strtotime($awal_bulan)));
		}
		
		return $result;
	}
	
	function generate_tonase_blok_tanah(){
		$today = date('Ymd');
		$yesterday = strtotime('-2 day',strtotime($today)); 
		$awal_bulan = date("Ym",$yesterday)."01";
		$yesterday = date('Ymd', $yesterday);
		$awal_bulan = '20140901';
		$yesterday ='20140930';

		$array_company = array('LIH', 'NAK', 'TPAI', 'GKM', 'SSS', 'SML', 'ASL', 'MSS');
		//$array_company = array('MAG');
		//$array_company = array('MAG', 'LIH', 'NAK', 'TPAI', 'SSS', 'ASL', 'SAP', 'MSS');
		
		//$this->round_all($awal_bulan,$yesterday);
		//$this->regenerate_tonase($awal_bulan,$yesterday);
		
		foreach($array_company as $i => $company){	
			$this->redistribusi_tonase($company, $awal_bulan, $yesterday);
		}
	}
	
	function redistribusi_tonase($company, $awal_bulan, $yesterday){
		$awal_bulan= date("Y-m-d", strtotime($awal_bulan));
		$yesterday= date("Y-m-d", strtotime($yesterday));
		if ($company=='GKM' || $company=='SML'){
			$db_other = $this->load->database('lhm_gkm', TRUE);
		}
		while (strtotime($awal_bulan) <= strtotime($yesterday)) {
			$qSP ="CALL sp_generate_progress_panen_bloktanah(?, ?)";
			$month = date("Ymd",strtotime($awal_bulan));	
			if ($company=='GKM' || $company=='SML'){					
				$db_other->reconnect();
				$sukses=$db_other->query($qSP,array($company,$month));
			}else{
				$this->db->reconnect();	
				$sukses=$this->db->query($qSP,array($company,$month));
				//var_dump($qSP.$company.$month);
				//var_dump(" - ");
			}
			$awal_bulan = date ("Y-m-d", strtotime("+1 day", strtotime($awal_bulan)));
		}
	}
            
	//## Create Report: Summary Produksi Kebun (Panen) ##
    function generate_sum_lhm_nab(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        //$periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        //$ar2 = preg_split('/[- :]/',trim($periode_to));
        //$ar2 = implode('',$ar2);
		//start: Added by Asep, 20130521
		//$m='';
		//$y='';
		//$m=date("m",strtotime($ar2));
		//$y=date("Y",strtotime($ar2));
		//$awal_bulan= $y.$m."01";

        if(!empty($periode) && !empty($company)){
            $data_panen=$this->model_s_analisa_panen->generate_sum_lhm_nab($ar,$ar,$company);

            $PANEN = "";
            $i = 1;
            
            $PANEN .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PANEN .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PANEN .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
            
            
            $PANEN .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";
            //$PANEN .= "<tr><td  align='left' colspan='18' class='tbl_th'> *Klik kode lokasi untuk melihat detail </td> </tr>";    
            $PANEN .= "<tr><td  align='center' rowspan='3' class='tbl_th'> NO. </td>";            
			$PANEN .= "<td rowspan='3' align='center' class='tbl_th'>KODE LOKASI</td>";
			$PANEN .= "<td rowspan='3' align='center' class='tbl_th'>PLANTATION</td>";
			$PANEN .= "<td rowspan='3' align='center' class='tbl_th'>PLANTED AREA</td>";			
            //$PANEN .= "<td rowspan='2'align='center' class='tbl_th'>ACTIVITY CODE</td>";
            $PANEN .= "<td colspan='4' align='center' class='tbl_th'>LHM</td>";
            $PANEN .= "<td colspan='4'align='center' class='tbl_th'>NOTA ANGKUT</td>";
			$PANEN .= "<td align='center' class='tbl_th' rowspan='3'>BJR</td>";
			$PANEN .= "<td colspan='2' align='center' class='tbl_th' rowspan='2'>YIELD PANEN</td>";
			$PANEN .= "<td colspan='2' align='center' class='tbl_th' rowspan='2'>YIELD ANGKUT</td>";
            $PANEN .= "<td colspan='2'align='center' class='tbl_th'>RESTAN</td>";
            
            $PANEN .= "<tr><td align='center' class='tbl_th' colspan='2'>JANJANG PANEN</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>BERAT PANEN (Kg)</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>JANJANG ANGKUT</td>";
            $PANEN .= "<td align='center' class='tbl_th' colspan='2'>BERAT ANGKUT (Kg)</td>";
            $PANEN .= "<td align='center' class='tbl_th' rowspan='2'>JANJANG RESTAN</td>";
            
            $PANEN .= "<tr><td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
			$PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>HI</td>";
            $PANEN .= "<td align='center' class='tbl_th'>SHI</td>";
            
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            $total_jjg_lhm=0;
            $total_berat_lhm=0;
            $total_jjg_nab=0;
            $total_berat_nab=0;
            $total_jjg_restan=0;
            $total_berat_restan=0;
			$total_yield_panen=0;
			$total_yield_angkut=0;

			$shi_janjang_angkut = 0; // Added By Asep, 20130508
			$shi_berat_angkut = 0; // Added By Asep, 20130508
			$bjr_real = 0; // Added By Asep, 20130508
			$location_code =''; 

            foreach($data_panen as $row){
				//start:  Added By Asep, 20130508				
				//$tanggal=$row['TANGGAL'];
		
				//$location_code = $row['LOCATION_CODE'];	
				//$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $ar2,$company,$location_code,$tabel1);
				//$bjr_real = $row['BJR_REAL'];
				//$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $ar2,$company,$location_code, $tabel1);
				//$shi_yield_panen =  $this->model_s_analisa_panen->get_yield_panen_shi($awal_bulan, $ar2,$company,$location_code, $tabel1);
				//$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $ar2,$company,$location_code);
				//$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $ar2,$company,$location_code);
				//$shi_yield_angkut =  $this->model_s_analisa_panen->get_yield_angkut_shi($awal_bulan, $ar2,$company,$location_code);
				//end:  Added By Asep, 20130508
				
                //$ar3 = preg_split('/[- :]/',trim($row['TANGGAL']));
                //$ar3 = implode('',$ar3);
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';              
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['LOCATION_CODE'].'&nbsp;</td>';
                //$PANEN .= '<td width="150" class="tbl_td" align="left">&nbsp;'.$row['ACTIVITY_CODE'].'</td>';
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['NUMPLANTATION'].'&nbsp;</td>';
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['HECTPLANTED'].'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_PANEN']).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_PANEN_SHI']).'&nbsp;</td>'; // SHI JANJANG PANEN, Added By Asep, 20130508
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_PANEN'],2).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_PANEN_SHI'],2).'&nbsp;</td>'; // SHI BERAT PANEN, Added By Asep, 20130508
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JJG_ANGKUT']).' &nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JJG_ANGKUT_SHI']).' &nbsp;</td>'; // SHI JJG_ANGKUT
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_ANGKUT'],2).'  &nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_ANGKUT_SHI'],2).' &nbsp;</td>'; // SHI JJG_ANGKUT 
				
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BJR_REAL'],2).'  &nbsp;</td>';
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['YIELD_PANEN'],2).'  &nbsp;</td>';
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['YIELD_PANEN_SHI'],2).'  &nbsp;</td>';
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['YIELD_ANGKUT'],2).'  &nbsp;</td>';
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['YIELD_ANGKUT_SHI'],2).'  &nbsp;</td>';
                
				$PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['RESTAN']).'  &nbsp;</td>';
                $PANEN .= '</tr>';
				
				$restan_jjg = $row['JANJANG_PANEN']-$row['JJG_ANGKUT'];
                $total_jjg_lhm+=$row['JANJANG_PANEN'];
                $total_berat_lhm+=$row['BERAT_PANEN'];
                $total_jjg_nab+=$row['JJG_ANGKUT'];
                $total_berat_nab+=$row['BERAT_ANGKUT']; 
                $total_jjg_restan+=$row['RESTAN'];
				$total_yield_panen+=$row['YIELD_PANEN'];
				$total_yield_angkut+=$row['YIELD_ANGKUT'];
                //$total_berat_restan+=$restan_wb;   
                $i++;    
            }
            $PANEN .="<tr><td class='tbl_td' align='center' colspan='4'><strong>TOTAL</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_lhm)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_lhm,2)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_nab)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_berat_nab,2)."&nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;&nbsp;</strong></td>";
            
			$PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_yield_panen)."</strong></td>";
			$PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
			$PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_yield_angkut)."</strong></td>";
			$PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
			$PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "</table>"; 
            
            echo $PANEN;    
        }
        
    }
	
	//## Create Report: Summary Produksi Kebun (Panen) ## 
    function generate_sum_xls_nab(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
		$company_name=htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        //$periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        //$ar2 = preg_split('/[- :]/',trim($periode_to));
        //$ar2 = implode('',$ar2);
		
		//$m='';
		//$y='';
		//$m=date("m",strtotime($ar2));
		//$y=date("Y",strtotime($ar2));
		//$awal_bulan= $y.$m."01";
        
        $judul = '';
        $headers = ''; 
        $data = ''; 
        $footer = '';
        
        $obj =& get_instance();
		$w=date("d-m-Y",strtotime($ar));
        $data_panen=$this->model_s_analisa_panen->generate_sum_lhm_nab($ar,$ar,$company);
		
		$headers .= "PT. ".$company_name."\n";
		$headers .= "PRODUCTION SUMMARY REPORT \n";
		$headers .= "PERIODE: ". $w ."\n";
		$headers .= "\n";
        $headers .= "No. \t";
        $headers .= "Kode Lokasi \t";
		$headers .= "Jumlah Pokok \t";
		$headers .= "Planted Area \t";
        $headers .= "Janjang Panen (HI) \t";
        $headers .= "Janjang Panen (SHI) \t";
		$headers .= "Berat Panen (HI) (kg) \t"; 
        $headers .= "Berat Panen (SHI) (kg) \t"; 
		$headers .= "Janjang Angkut (HI) \t";
        $headers .= "Janjang Angkut (SHI) \t";
		$headers .= "Berat Angkut HI (kg) \t"; 
		$headers .= "Berat Angkut SHI (kg) \t"; 
		$headers .= "BJR \t";
		$headers .= "Yield Panen (HI) \t";
        $headers .= "Yield Panen (SHI) \t";
		$headers .= "Yield Angkut (HI) \t";
        $headers .= "Yield Angkut (SHI) \t";
        $headers .= "Restan \t";
        
        $no = 1;
        foreach ($data_panen as $row){							
				//$tanggal=$row['TANGGAL'];
		
				//$location_code = $row['LOCATION_CODE'];	
				//$shi_janjang_panen = $this->model_s_analisa_panen->get_janjang_shi($awal_bulan, $ar2,$company,$location_code,$tabel1);
				//$bjr_real = $row['BJR_REAL'];
				//$shi_berat_panen =  $this->model_s_analisa_panen->get_berat_panen_shi($awal_bulan, $ar2,$company,$location_code, $tabel1);
				//$shi_yield_panen =  $this->model_s_analisa_panen->get_yield_panen_shi($awal_bulan, $ar2,$company,$location_code, $tabel1);
				//$shi_janjang_angkut =  $this->model_s_analisa_panen->get_janjang_angkut_shi($awal_bulan, $ar2,$company,$location_code);
				//$shi_berat_angkut =  $this->model_s_analisa_panen->get_berat_angkut_shi($awal_bulan, $ar2,$company,$location_code);
				//$shi_yield_angkut =  $this->model_s_analisa_panen->get_yield_angkut_shi($awal_bulan, $ar2,$company,$location_code);
				
            $line = '';
            $line .= str_replace('"', '""',$no)."\t";       
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t"; 
			$line .= str_replace('"', '""',$row['NUMPLANTATION'])."\t";
			$line .= str_replace('"', '""',$row['HECTPLANTED'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG_PANEN'])."\t";
			$line .= str_replace('"', '""',$row['JANJANG_PANEN_SHI'])."\t"; 
            $line .= str_replace('"', '""',$row['BERAT_PANEN'])."\t";
			$line .= str_replace('"', '""',$row['BERAT_PANEN_SHI'])."\t"; 
            $line .= str_replace('"', '""',$row['JJG_ANGKUT'])."\t";
			$line .= str_replace('"', '""',$row['JJG_ANGKUT_SHI'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ANGKUT'])."\t";
			$line .= str_replace('"', '""',$row['BERAT_ANGKUT_SHI'])."\t";			
			$line .= str_replace('"', '""',$row['BJR_REAL'])."\t";
			$line .= str_replace('"', '""',$row['YIELD_PANEN'])."\t";
			$line .= str_replace('"', '""',$row['YIELD_PANEN_SHI'])."\t"; 
			$line .= str_replace('"', '""',$row['YIELD_ANGKUT'])."\t";
			$line .= str_replace('"', '""',$row['YIELD_ANGKUT_SHI'])."\t";				
            $line .= str_replace('"', '""',$row['RESTAN'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=PROD_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
	
    function get_panen_breakdown(){
        $location = trim(htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8')); 
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        
        $lhm = "<span style='font-size:14px;color:#678197'>Data Panen LHM  [ ".$location. " ]</span> <br>";
        $tbg = "<span style='font-size:14px;color:#678197'>Data Angkut [ ".$location. " ]</span> <br>";
        $lhm .= $this->panen_lhm_breakdown($location,$periode,$company);
        $tbg .= $this->panen_tbg_breakdown($location,$periode,$company);
        echo $lhm."<br>";
        echo $tbg."<br>";
    }
    
    function panen_lhm_breakdown($location,$periode,$company){ 
        if(!empty($periode) && !empty($company)){ 
            $PANEN = "";
            $i = 1;
            
            $PANEN .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PANEN .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PANEN .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
            
            $PANEN .= "<br> <table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $PANEN .= "<tr><td  align='center' class='tbl_th'> NO. </td>";
            
            $PANEN .= "<td align='center' class='tbl_th'>TANGGAL</td>";
            $PANEN .= "<td align='center' class='tbl_th'>NIK</td>";
            $PANEN .= "<td align='center' class='tbl_th'>NAMA</td>";
            //$PANEN .= "<td align='center' class='tbl_th'>KODE LOKASI</td>"; //remark by Asep, 20130508
            $PANEN .= "<td align='center' class='tbl_th'>KODE AKTIFITAS</td>";
            $PANEN .= "<td align='center' class='tbl_th'>JUMLAH JANJANG</td>";
            
            $style = "";
            $total_jjg_lhm=0;
            $result = $this->model_s_analisa_panen->get_panen_breakdown($periode,$company,$location); 
            foreach($result as $row){
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['LHM_DATE'].'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['EMPLOYEE_CODE'].'&nbsp;</td>';
                $PANEN .= '<td width="200" class="tbl_td" align="left">'.$row['NAMA'].'&nbsp;</td>';
                //$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['LOCATION_CODE'].'&nbsp;</td>'; //remark by Asep, 20130508
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['ACTIVITY_CODE'].' &nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['HSL_KERJA_VOLUME'],2).'  &nbsp;</td>';
                
                $PANEN .= '</tr>';
                $total_jjg_lhm+=$row['HSL_KERJA_VOLUME'];
                $i++;    
            }
            $PANEN .="<tr><td class='tbl_td' align='center' colspan='5'><strong>Total</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_lhm)." &nbsp;</strong></td>";
           
            $PANEN .= "</table>";
            
            return $PANEN;
        }
    }
    
    function panen_tbg_breakdown($location,$periode,$company){
         if(!empty($periode) && !empty($company)){ 
            $PANEN = "";
            $i = 1;
            $t_berat_real=0;
            $t_berat_empiris=0;
            $t_berat_bersih=0;
            
            $PANEN .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PANEN .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PANEN .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PANEN .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
            
            $PANEN .= "<br><table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $PANEN .= "<tr><td  align='center' class='tbl_th'> NO. </td>";
            
            $PANEN .= "<td align='center' class='tbl_th'>TANGGAL ANGKUT</td>";
            $PANEN .= "<td align='center' class='tbl_th'>TANGGAL PANEN LHM</td>";
            $PANEN .= "<td align='center' class='tbl_th'>No SPB</td>";
			//start: remarked by Asep, 20130508
            //$PANEN .= "<td align='center' class='tbl_th'>AFD</td>"; 
            //$PANEN .= "<td align='center' class='tbl_th'>BLOCK CODE</td>"; 
            //$PANEN .= "<td align='center' class='tbl_th'>Berat Isi</td>";
            //$PANEN .= "<td align='center' class='tbl_th'>Berat Kosong</td>";
            //$PANEN .= "<td align='center' class='tbl_th'>Berat Bersih</td>";
			//end: remarked by Asep, 20130508
            $PANEN .= "<td align='center' class='tbl_th'>JUMLAH JANJANG</td>";
            $PANEN .= "<td align='center' class='tbl_th'>BERAT EMPIRIS</td>";
            $PANEN .= "<td align='center' class='tbl_th'>BJR REAL</td>";
            $PANEN .= "<td align='center' class='tbl_th'>BERAT REAL</td>";
       
            $style = "";
            $total_jjg_tbg=0;
            $result = $this->model_s_analisa_panen->get_tbg_breakdown($periode,$company,$location); 
            foreach($result as $row){
                $PANEN .= '<tr id="tr_1">';
                $PANEN .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['TANGGALM'].'&nbsp;</td>';
                $PANEN .= '<td width="150" class="tbl_td" align="center">'.$row['TANGGAL_PANEN'].'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="center">'.$row['NO_SPB'].'&nbsp;</td>';
				//start: remarked by Asep, 20130508
                //$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['AFD'].'&nbsp;</td>';
                //$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['BLOCK'].' &nbsp;</td>';
                //$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['BERAT_ISI'].'&nbsp;</td>';
                //$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['BERAT_KOSONG'].'&nbsp;</td>';
                //$PANEN .= '<td width="100" class="tbl_td" align="right">'.$row['BERAT_BERSIH'].'&nbsp;</td>';
				//end: remarked by Asep, 20130508
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG']).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format(floatval($row['BERAT_EMPIRIS']),2).'&nbsp;</td>'; 
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format(floatval($row['BJR_REAL']),2).'&nbsp;</td>';
                $PANEN .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_REAL'],2).'&nbsp;</td>';
                
                $PANEN .= '</tr>';
                $total_jjg_tbg+=$row['JANJANG'];
                $t_berat_real+=$row['BERAT_REAL'];
                $t_berat_empiris+=$row['BERAT_EMPIRIS'];
                //$t_berat_bersih+=$row['BERAT_BERSIH']; remarked by Asep, 20130508
                $i++;    
            }
            $PANEN .="<tr><td class='tbl_td' align='center' colspan='4'><strong>Total</strong></td>";
			//start: remarked by Asep, 20130508
            //$PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            //$PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            //$PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            //$PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($t_berat_bersih)." &nbsp;</strong></td>";
			//end: remarked by Asep, 20130508
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($total_jjg_tbg)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($t_berat_empiris,2)." &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>- &nbsp;</strong></td>";
            $PANEN .= "<td class='tbl_td' align='right'><strong>".number_format($t_berat_real,2)." &nbsp;</strong></td>";
            
           
            $PANEN .= "</table>";
            
            return $PANEN;
        }    
    }
    
    //## Create Report: GC - Produksi Pabrik ##
    function generate_lhm_produksi_tbs(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
		
		//start: Added by Asep, 20130521
		$m='';
		$y='';
		$m=date("m",strtotime($ar2));
		$y=date("Y",strtotime($ar2));
		$awal_bulan= $y.$m."01";
		//end: Added by Asep, 20130521

        if(!empty($periode) && !empty($company)){
            $data_produksi=$this->model_s_analisa_panen->generate_lhm_produksi_tbs($awal_bulan,$ar2,$company,'TBS');
            $PRODUKSI = "";
            $i = 1;
            
            $PRODUKSI .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PRODUKSI .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PRODUKSI .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PRODUKSI .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $PRODUKSI .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
       
            $PRODUKSI .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $PRODUKSI .= "<tr><td align='center' class='tbl_th' ROWSPAN=2> No. </td>";
            $PRODUKSI .= "<td align='center' class='tbl_th' ROWSPAN=2>TANGGAL</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th' COLSPAN=2>TBS TERIMA (Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th' COLSPAN=2>TBS OLAH (Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th' COLSPAN=2>PRODUKSI CPO (Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th' COLSPAN=2>RENDEMEN (%)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th' COLSPAN=2>FFA (%)</td>";
            
            $PRODUKSI .= "<tr>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>TBS TERIMA HI(Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>TBS TERIMA SHI(Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>TBS OLAH HI(Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>TBS OLAH SHI(Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>PRODUKSI CPO HI(Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>PRODUKSI CPO SHI(Kg)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>RENDEMEN HI(%)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>RENDEMEN SHI(%)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>FFA HI (%)</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>FFA SHI (%)</td>";
         
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            $total_tbs_terima=0;
            $total_tbs_olah=0;
            $total_prod_cpo=0;
            $total_rendemen=0;
            $total_ffa=0;
            $shi_tbs_terima = 0;
			$shi_tbs_olah = 0;
			$shi_prod_cpo = 0;
			$shi_rendemen = 0;
			$shi_ffa = 0;
			$count_ffa = 0;
			$shi_total_ffa = 0;
            foreach($data_produksi as $row){
				//start:  Added By Asep, 20130508				
				$tanggal=$row['TANGGAL'];
				$jenis_muatan='TBS';
		
				$shi_tbs_terima = $shi_tbs_terima + $row['TBS_TERIMA']; 
				$shi_tbs_olah =  $shi_tbs_olah + $row['TBS_OLAH']; 
				$shi_prod_cpo =  $shi_prod_cpo + $row['PROD_CPO'];
				if ($shi_tbs_olah<>0.000){
					$shi_rendemen = ($shi_prod_cpo/$shi_tbs_olah)*100; 
				}else{
					$shi_rendemen =0;
				}
				$shi_ffa = $shi_ffa + $row['FFA'];

				if ($row['FFA']<>'0.000'){
                	$count_ffa=$count_ffa+1;  
					$shi_total_ffa=$shi_ffa/$count_ffa;
				}
				//end:  Added By Asep, 20130508
				if (date("Ymd",strtotime($tanggal))>=$ar){				
				
                $PRODUKSI .= '<tr id="tr_1">';
                $PRODUKSI .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $PRODUKSI .= '<td width="100" class="tbl_td" align="center">&nbsp;'.$row['TANGGAL'].'</td>';
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($row['TBS_TERIMA'],2).'&nbsp;</td>';				 
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($shi_tbs_terima,2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($row['TBS_OLAH'],2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($shi_tbs_olah,2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="120" class="tbl_td" align="right">'.number_format(round($row['PROD_CPO']),2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="120" class="tbl_td" align="right">'.number_format(round($shi_prod_cpo),2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($row['RENDEMEN'],2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($shi_rendemen,2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($row['FFA'],2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="110" class="tbl_td" align="right">'.number_format($shi_total_ffa,2).'&nbsp;</td>'; 
                
                $PRODUKSI .= '</tr>';
                $total_tbs_terima+=$row['TBS_TERIMA'];
                $total_tbs_olah+=$row['TBS_OLAH'];
                
				$total_prod_cpo+=$row['PROD_CPO']; // Added By Asep, 20130517
				$total_rendemen+=$row['RENDEMEN']; // Aded, by Asep, 20130521
                //$total_prod_cpo+=$row['JANJANG_NAB'];
                //$total_rendemen+=$row['BERAT_REAL']; 
                //$total_ffa+=$restan_jjg;				
                $i++; 
				}
            }
			
            $PRODUKSI .="<tr><td class='tbl_td' align='center' colspan='2'><strong>Total</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".number_format($total_tbs_terima,2)." &nbsp;</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".number_format($total_tbs_olah,2)." &nbsp;</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".number_format($total_prod_cpo,2)."&nbsp;</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".number_format($total_rendemen,2)."&nbsp;</strong></td>"; 
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>"; 
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>"; 

            $PRODUKSI .= "</table>"; 
               
            echo $PRODUKSI;
        }
    }
    
    //## Create Report: GC - Produksi Pabrik ##   
    function generate_xls_produksi_tbs(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
		
		//start: Added by Asep, 20130521
		$m='';
		$y='';
		$m=date("m",strtotime($ar2));
		$y=date("Y",strtotime($ar2));
		$awal_bulan= $y.$m."01";
		//end: Added by Asep, 20130521
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->generate_lhm_produksi_tbs($awal_bulan,$ar2,$company,'TBS');      

        //baris 1
		//start: Added By Asep, 20130521
		$headers .= "TANGGAL \t";
        $headers .= "TBS TERIMA HI \t";
        $headers .= "TBS TERIMA SHI \t";
        $headers .= "TBS OLAH HI \t";
        $headers .= "TBS OLAH SHI \t"; 
        $headers .= "PRODUKSI CPO HI \t";
        $headers .= "PRODUKSI CPO SHI \t";
        $headers .= "RENDEMEN HI \t";
        $headers .= "RENDEMEN SHI \t";
        $headers .= "FFA HI \t";
        $headers .= "FFA SHI \t";
		$headers .= "DISPATCH CPO HI \t";
        $headers .= "DISPATCH CPO SHI \t";
		//end: Added By Asep, 20130521
		
		//start: Remarked By Asep, 20130521
		/*
        $headers .= "Tanggal \t";
        $headers .= "TBS INTI \t";
        $headers .= " \t";
        $headers .= "TBS PLASMA \t";
        $headers .= " \t";
        $headers .= "TBS LUAR \t";
        $headers .= " \t";
        $headers .= "Total \t";
        $headers .= " \t";    
        $headers .= "TBS Olah \t";
        $headers .= " \t";
        $headers .= "Restan Pabrik \t";
        $headers .= "Produksi CPO \t";
        $headers .= " \t";
        $headers .= "Rendemen CPO \t";
        $headers .= " \t";
        $headers .= "FFA Produksi \t";
        $headers .= " \t";
        $headers .= "FFA Storage \t";
        $headers .= " \t";
        $headers .= "Pengiriman CPO \t";
        $headers .= " \t";
        $headers .= "Stock CPO \t";
        $headers .= " \t";
        $headers .= " \t";
        $headers .= "Produksi Kernel \t";
        $headers .= " \t";
        $headers .= "Rendemen Kernel \t";
        $headers .= " \t";
        $headers .= "Pengiriman Kernel \t";
        $headers .= " \t";
        $headers .= "Stock Kernel \t";
        $headers .= "Curah Hujan \t";
        $headers .= " \t";
        $headers .= "Keterangan \n";		
        
        $headers .= " \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= " \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "TANGKI 1 \t";
        $headers .= "TANGKI 2 \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "TANGKI 1 \t";
        $headers .= "TANGKI 2 \t";
        $headers .= "TOTAL \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= " \t";
        $headers .= "HI \t";
        $headers .= "S/D \t";
        $headers .= " \n";
		*/
        //end: Remarked By Asep, 20130521
		$shi_tbs_terima = 0;
		$shi_tbs_olah = 0;
		$shi_prod_cpo = 0;
		$shi_dispatch = 0;
		$shi_rendemen = 0;
		$shi_ffa = 0;
		$count_ffa = 0;
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
			//start:  Added By Asep, 20130508				
			$tanggal=$row['TANGGAL'];
			$jenis_muatan='TBS';
			
			$shi_tbs_terima = $shi_tbs_terima + $row['TBS_TERIMA']; 
			$shi_tbs_olah =  $shi_tbs_olah + $row['TBS_OLAH']; 
			$shi_prod_cpo =  $shi_prod_cpo + $row['PROD_CPO'];
			if ($shi_tbs_olah<>0.000){
				$shi_rendemen = ($shi_prod_cpo/$shi_tbs_olah)*100; 
			}else{
				$shi_rendemen =0;
			}
			$shi_ffa = $shi_ffa + $row['FFA'];			
			if ($row['FFA']<>'0.000'){
            	$count_ffa=$count_ffa+1;    
			}
			$shi_dispatch = $shi_dispatch + $row['VOL_DISPATCH1'];
			//end:  Added By Asep, 20130508
			
            
            if (date("Ymd",strtotime($tanggal))>=$ar){  
			$line = '';
            $line .= str_replace('"', '""',$row['TANGGAL'])."\t";
            $line .= str_replace('"', '""',$row['TBS_TERIMA'])."\t";
            $line .= str_replace('"', '""',$shi_tbs_terima)."\t";
            //$line .= str_replace('"', '""',$row['TBS_PLASMA'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['TBS_PLASMA_SHI'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['TBS_LUAR'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['TBS_LUAR_SHI'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['TBS_TERIMA']+$row['TBS_PLASMA']+$row['TBS_LUAR'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['TBS_TERIMA_SHI']+$row['TBS_PLASMA_SHI']+$row['TBS_LUAR_SHI'])."\t"; //Remarked By Asep, 20130521
            $line .= str_replace('"', '""',$row['TBS_OLAH'])."\t";
            $line .= str_replace('"', '""',$shi_tbs_olah)."\t";
            //$line .= str_replace('"', '""',$row['RESTAN'])."\t";
            $line .= str_replace('"', '""',$row['PROD_CPO'])."\t";
            $line .= str_replace('"', '""',$shi_prod_cpo)."\t";
            $line .= str_replace('"', '""',number_format($row['RENDEMEN'],2))."\t";
            $line .= str_replace('"', '""',number_format($shi_rendemen,2))."\t";
            $line .= str_replace('"', '""',$row['FFA'])."\t";
            $line .= str_replace('"', '""',$shi_ffa)."\t";
			$line .= str_replace('"', '""',$row['VOL_DISPATCH1'])."\t";
            $line .= str_replace('"', '""',$shi_dispatch)."\t";
            //$line .= str_replace('"', '""',$row['FFA_STRG'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""','-')."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['DISPATCH'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['DISPATCH_SHI'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['VOL_STRG1'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['VOL_STRG2'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['VOL_H']+0)."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['PROD_KERNEL'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['PROD_KERNEL_SHI'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['RENDEMEN_KERNEL'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['RENDEMEN_KERNEL_SHI'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['VOL_DISPATCH_KERNEL'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['DISPATCH_KERNEL_SHI'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row['VOL_KERNEL'])."\t"; //Remarked By Asep, 20130521
            //$line .= str_replace('"', '""',$row[''])."\t";

            //$total_netto = $total_netto + 0; //Remarked By Asep, 20130521            
            $no++;
			$data .= trim($line)."\n";
			}            
            
        } 
		
		/*
		//Remarked By Asep, 20130521
        $footer .= " Total \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= str_replace('"', '""',$total_netto)."\t";
		*/

      
        $data .= trim($footer)."\n";
        
        $data = str_replace("\r","",$data);
                         
                         
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=TBG_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    
    //## Create Report: Adem Import (Dispatch All) ##
    function generate_adem_dispatch(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_dispatch($periode,$periode_to,$company);
        //echo $this->dbutil->csv_from_result($adem_dpc, $delimiter, $newline);   
        //$data = $this->dbutil->csv_from_result($adem_dpc, $delimiter, $newline);
        $name = 'DPC_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);  
    }
	
	 //## generate_restan ##
    function generate_restan(){
        $this->load->dbutil();
        $this->load->helper('download');
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_restan($periode,$periode_to, $company);
        $name = 'RESTAN_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);    
    }
	
	 //## generate_afkir ##
    function generate_afkir(){
        $this->load->dbutil();
        $this->load->helper('download');
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_afkir($periode,$periode_to, $company);
        $name = 'AFKIR_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);    
    }
	
	//generate_xls_nab modifeid by Asep, 20130819
	function generate_afkir_xls(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
		
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_panen=$this->model_s_analisa_panen->generate_afkir_xls($periode,$periode_to, $company);

        //baris 1
        $headers .= "No. \t";
	 $headers .= "No BA\t";
        $headers .= "Tanggal Panen\t";
        $headers .= "Kode Lokasi \t";
		$headers .= "Janjang Afkir \t";
		$headers .= "Keterangan \t";
        
        $no = 1;
        foreach ($data_panen as $row){											
            $line = '';
            $line .= str_replace('"', '""',$no)."\t"; 
	     $line .= str_replace('"', '""',$row['NO_BA'])."\t";      
            $line .= str_replace('"', '""',$row['TANGGAL_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t"; 
            $line .= str_replace('"', '""',$row['JANJANG'])."\t";
			$line .= str_replace('"', '""',$row['KETERANGAN'])."\t"; 
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=AFKIR_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    
    //## Create Report: Adem Import - CPO (Produksi CPO) ##
    function generate_adem_produksi(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
		$user = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_produksi($periode,$periode_to, $user, $company);
        $name = 'PRODCPO_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);    
    }
    
    //## Create Report: Adem Import - PK (Produksi PK) ##
    function generate_adem_pkin(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
		$user = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_produksi_kernel($periode,$periode_to, $user, $company);
        $name = 'PRODPK_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);    
    }

    //## Create Report: Adem Import (TBS IN) ##
    function generate_adem_tbsin(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_tbsin($periode,$periode_to,$company);
        $name = 'TBS_IN_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);  
    }
    
    //## Generate Report: Adem Import (TBS OUT) ##
    function generate_adem_tbsout(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_tbsout($periode,$periode_to,$company);
        $name = 'TBS_OUT_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);  
    }
    
    //## Create Report: Adem Import (TBS PLASMA) ##
    function generate_adem_tbsplasma(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_tbsplasma($periode,$periode_to,$company);
        $name = 'TBS_PLASMA_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);  
    }
    
    //## Create Report: Adem Import (TBS LUAR) ##
    function generate_adem_tbsluar(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_tbsluar($periode,$periode_to,$company);
        $name = 'TBS_LUAR_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);  
    }

    //## Create Report: Adem Import (TBS AFILIASI) ##
    function generate_adem_tbsafiliasi(){
        $this->load->dbutil();
        $this->load->helper('download');
        //$delimiter = ",";
        //$newline = "\r\n";
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        $adem_dpc=$this->model_s_analisa_panen->generate_adem_tbsafiliasi($periode,$periode_to,$company);
        $name = 'TBS_AFILIASI_'.$company.'_'.$ar.'to'.$ar2.'.csv'; 
        force_download($name, $adem_dpc);  
    }
    
    //## Create Report: GC - BJR ##
    function generate_lhm_bjr(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        if(!empty($periode) && !empty($company)){
            $data_produksi=$this->model_s_analisa_panen->generate_lhm_bjr($periode,$periode_to,$company);
            $PRODUKSI = "";
            $i = 1;
            
            $PRODUKSI .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PRODUKSI .= ".tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PRODUKSI .= ".tbl_td { font-size: 14px;color:#678197;border-bottom:1px solid; border-right:1px solid } "; 
            $PRODUKSI .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
       
            $PRODUKSI .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $PRODUKSI .= "<tr><td align='center' class='tbl_th'> No. </td>";
			/*
            $PRODUKSI .= "<td align='center' class='tbl_th'>TANGGAL PANEN</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>TANGGAL ANGKUT</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>LOKASI</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>JANJANG PANEN</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>JANJANG ANGKUT</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BJR REAL</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BERAT EMPIRIS</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BERAT REAL</td>"; 
            $PRODUKSI .= "<td align='center' class='tbl_th'>BJR</td></tr>";   
			*/
			
			$PRODUKSI .= "<td align='center' class='tbl_th'>TANGGAL</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>LOKASI</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>JANJANG PANEN</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BERAT PANEN</td>";            
            $PRODUKSI .= "<td align='center' class='tbl_th'>JANJANG ANGKUT</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BERAT ANGKUT</td>"; 
			$PRODUKSI .= "<td align='center' class='tbl_th'>BJR REAL</td>";
         
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            $total_jjg_pnn=0;
            $total_jjg_angkut=0;
            
            foreach($data_produksi as $row){
                $PRODUKSI .= '<tr id="tr_1">';
                $PRODUKSI .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
				/*
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['TANGGAL_PANEN'].'</td>';
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['TANGGAL_TIMBANG'].'</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.$row['LOCATION_CODE'].'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_PANEN']).'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JJG_ANGKUT_NAB']).'&nbsp;</td>'; 
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.round($row['BJRREAL'],2).'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_EMPIRIS']).'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_REAL']).'&nbsp;</td>'; 
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.round($row['BJR_REAL'],2).'&nbsp;</td>';
                */
				$PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['DATE_TRANSACT'].'</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.$row['LOCATION_CODE'].'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_PANEN']).'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_PANEN']).'&nbsp;</td>'; 
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG_ANGKUT']).'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_ANGKUT']).'&nbsp;</td>'; 
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.round($row['BJR_REAL'],2).'&nbsp;</td>';
                $PRODUKSI .= '</tr>';
                $i++;    
            }
            /*$PRODUKSI .="<tr><td class='tbl_td' align='center' colspan='2'><strong>Total</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";*/

            $PRODUKSI .= "</table>"; 
               
            echo $PRODUKSI;
        }
    }
    
    //## Create Report: GC - BJR ## 
    function generate_xls_bjr(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->generate_lhm_bjr($periode,$periode_to,$company);

        //baris 1
		/*
        $headers .= "Tanggal Panen \t";
        $headers .= "Tanggal Timbang \t";
        $headers .= "Tanggal Panen (NAB) \t";
        $headers .= "Lokasi \t";
        $headers .= "Janjang Panen \t";
        $headers .= "Janjang Angkut (NAB) \t";
        $headers .= "Janjang Angkut (TBG) \t";
        $headers .= "BJR Real \t";  
        $headers .= "Berat Empiris \t";
        $headers .= "Berat Real \t";   
        $headers .= "BJR \t";
        $headers .= "SPB NAB \t";
        $headers .= "SPB TBG \t";
        */		
		$headers .= "TANGGAL \t";
        $headers .= "LOKASI \t";
        $headers .= "JANJANG PANEN \t";
        $headers .= "BERAT PANEN \t";
        $headers .= "JANJANG ANGKUT \t";
        $headers .= "BERAT ANGKUT\t";
        $headers .= "BJR REAL \t";  
			
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
            /*       
            $line .= str_replace('"', '""',$row['TANGGAL_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['TANGGAL_TIMBANG'])."\t"; 
            $line .= str_replace('"', '""',$row['TANGGAL_PANEN_NAB'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['JJG_ANGKUT_NAB'])."\t";
            $line .= str_replace('"', '""',$row['JJG_ANGKUT_TBG'])."\t";
            $line .= str_replace('"', '""',round($row['BJRREAL'],2))."\t";
            $line .= str_replace('"', '""',$row['BERAT_EMPIRIS'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_REAL'])."\t";
            $line .= str_replace('"', '""',$row['BJR_REAL'])."\t";
            $line .= str_replace('"', '""',$row['SPB_NAB'])."\t";
            $line .= str_replace('"', '""',$row['SPB_TBG'])."\t";
			*/
			$line .= str_replace('"', '""',$row['DATE_TRANSACT'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t"; 
            $line .= str_replace('"', '""',$row['JANJANG_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG_ANGKUT'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ANGKUT'])."\t";
            $line .= str_replace('"', '""',round($row['BJR_REAL'],2))."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=BJR_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    
    function generate_lhm_tbg(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        if(!empty($periode) && !empty($company)){
            $data_produksi=$this->model_s_analisa_panen->generate_lhm_tbg($periode,$periode_to,$company);
            $PRODUKSI = "";
            $i = 1;
            
            $PRODUKSI .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PRODUKSI .= ".tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PRODUKSI .= ".tbl_td { font-size: 14px;color:#678197;border-bottom:1px solid; border-right:1px solid } "; 
            $PRODUKSI .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
       
            $PRODUKSI .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $PRODUKSI .= "<tr><td align='center' class='tbl_th'> NO. </td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>ID</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>TANGGAL ANGKUT</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>NO TIKET</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>NO SPB</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>NO KENDARAAN</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BERAT BERSIH</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>AFD</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BLOCK</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>JANJANG</td>"; 
            $PRODUKSI .= "<td align='center' class='tbl_th'>BERAT EMPIRIS</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>BERAT REAL</td></tr>";  
         
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            $total_jjg_pnn=0;
            $total_jjg_angkut=0;
            
            foreach($data_produksi as $row){
                $PRODUKSI .= '<tr id="tr_1">';
                $PRODUKSI .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['ID_TIMBANGAN'].'</td>';
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['TANGGALM'].'</td>';
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['NO_TIKET'].'</td>';
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['NO_SPB'].'</td>';
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['NO_KENDARAAN'].'</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_BERSIH']).'&nbsp;</td>';
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['AFD'].'</td>'; 
                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['BLOCK'].'</td>';  
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['JANJANG']).'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_EMPIRIS']).'&nbsp;</td>'; 
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.number_format($row['BERAT_REAL']).'&nbsp;</td>'; 
                $PRODUKSI .= '</tr>';
                $i++;    
            }
            /*$PRODUKSI .="<tr><td class='tbl_td' align='center' colspan='2'><strong>Total</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";*/

            $PRODUKSI .= "</table>"; 
               
            echo $PRODUKSI;
        }
    }
    
    function generate_xls_tbg(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->generate_lhm_tbg($periode,$periode_to,$company);

        //baris 1
        $headers .= "ID \t";
        $headers .= "Tanggal Angkut \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
        $headers .= "No Kendaraan \t";
        $headers .= "Berat Bersih \t";
        $headers .= "AFD \t";
        $headers .= "Block \t";
        $headers .= "Janjang \t";  
        $headers .= "Berat Empiris \t";
        $headers .= "Berat Real \t";   
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['ID_TIMBANGAN'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALM'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
            $line .= str_replace('"', '""',$row['AFD'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',round($row['JANJANG'],2))."\t";
            $line .= str_replace('"', '""',$row['BERAT_EMPIRIS'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_REAL'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=TBG_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    
    //## Create Report: GC - Janjang Angkut (NAB) ##
    function get_jjg_angkut(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        if(!empty($periode) && !empty($company)){
            $data_produksi=$this->model_s_analisa_panen->get_jjg_angkut($periode,$company);
            $PRODUKSI = "";
            $i = 1;
            
            $PRODUKSI .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $PRODUKSI .= ".tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $PRODUKSI .= ".tbl_td { font-size: 14px;color:#678197;border-bottom:1px solid; border-right:1px solid } "; 
            $PRODUKSI .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
       
            $PRODUKSI .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $PRODUKSI .= "<tr><td align='center' class='tbl_th'> No. </td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>No Tiket</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>No SPB</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>Tanggal Panen</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>Block</td>";
            $PRODUKSI .= "<td align='center' class='tbl_th'>Janjang Ankut</td></tr>";
         
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            $total_jjg_pnn=0;
            $total_jjg_angkut=0;
            
            foreach($data_produksi as $row){
                $PRODUKSI .= '<tr id="tr_1">';
                $PRODUKSI .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $PRODUKSI .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['NO_TIKET'].'</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.$row['NO_SPB'].'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.$row['TANGGAL_PANEN'].'&nbsp;</td>';
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.$row['BLOCK'].'&nbsp;</td>'; 
                $PRODUKSI .= '<td width="100" class="tbl_td" align="right">'.$row['JJG_ANGKUT'].'&nbsp;</td>'; 
                
                $PRODUKSI .= '</tr>';
                $i++;    
            }
            /*$PRODUKSI .="<tr><td class='tbl_td' align='center' colspan='2'><strong>Total</strong></td>";
            $PRODUKSI .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";*/

            $PRODUKSI .= "</table>"; 
               
            echo $PRODUKSI;
        }
    }
    
    function get_xls_jjg_angkut(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_jjg_angkut($periode,$company);

        //baris 1
        $headers .= "No Tiket \t";
        $headers .= "No Spb \t";
        $headers .= "Tanggal Panen \t";
        $headers .= "Block \t";
        $headers .= "Janjang \t";    
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t";
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['TANGGAL_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',$row['JJG_ANGKUT'])."\t"; 
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=JJGAKT_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    
    //## Create Report: GC - Distribusi NAB ##
    function get_nabdist(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_nabdist($periode,$periode_to,$company);

        //baris 1
        $headers .= "Tanggal \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
        $headers .= "No Kendaraan \t";
        $headers .= "AFD \t";
        $headers .= "Block \t";
        $headers .= "Jjg \t";
        $headers .= "Berat Real \t";
        $headers .= "Berat Bersih \t";
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['TANGGAL'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['AFD'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_REAL'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
            
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=NABDIST_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";        
    }
	
	function get_scrap(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_scrap($periode,$periode_to,$company);

        //baris 1
        $headers .= "TANGGAL KIRIM \t";
        $headers .= "NO KENDARAAN \t";
        $headers .= "DRIVER NAME \t";
        $headers .= "ID DO \t";
        $headers .= "SO NUMBER \t";
        $headers .= "CUSTOMER NAME \t";
        $headers .= "NO TIKET KIRIM \t";
        $headers .= "JAM MASUK KIRIM \t";
        $headers .= "JAM KELUAR KIRIM \t";
		$headers .= "TARA KIRIM \t";
        $headers .= "BRUTO KIRIM \t";
		$headers .= "NETTO KIRIM \t";
		$headers .= "NO TIKET TERIMA \t";
		$headers .= "TANGGAL TERIMA \t";
		$headers .= "JAM MASUK TERIMA \t";
		$headers .= "JAM KELUAR_TERIMA \t";
		$headers .= "TARA TERIMA \t";
		$headers .= "BRUTO TERIMA \t";
		$headers .= "NETTO TERIMA \t";
		$headers .= "SCRAP \t";
		
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
            $line .= $no."\t";       
            $line .= str_replace('"', '""',$row['TANGGAL_KIRIM'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t"; 
            $line .= str_replace('"', '""',$row['DRIVER_NAME'])."\t";
            $line .= str_replace('"', '""',$row['ID_DO'])."\t";
            $line .= str_replace('"', '""',$row['SO_NUMBER'])."\t";
            $line .= str_replace('"', '""',$row['CUSTOMER_NAME'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET_KIRIM'])."\t";
            $line .= str_replace('"', '""',$row['JAM_MASUK_KIRIM'])."\t";
            $line .= str_replace('"', '""',$row['JAM_KELUAR_KIRIM'])."\t";
			$line .= str_replace('"', '""',$row['TARA_KIRIM'])."\t";
			$line .= str_replace('"', '""',$row['BRUTO_KIRIM'])."\t";
			$line .= str_replace('"', '""',$row['NETTO_KIRIM'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET_TERIMA'])."\t";
			$line .= str_replace('"', '""',$row['TANGGAL_TERIMA'])."\t";
			$line .= str_replace('"', '""',$row['JAM_MASUK_TERIMA'])."\t";
			$line .= str_replace('"', '""',$row['JAM_KELUAR_TERIMA'])."\t";
			$line .= str_replace('"', '""',$row['TARA_TERIMA'])."\t";
			$line .= str_replace('"', '""',$row['BRUTO_TERIMA'])."\t";
			$line .= str_replace('"', '""',$row['NETTO_TERIMA'])."\t";
			$line .= str_replace('"', '""',$row['SCRAP'])."\t";
			
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=SCRAP_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";        
    }
    
    //## Create Report: Export - NAB ##
    function export_nab(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_nab_data($periode,$periode_to,$company);

        //baris 1
        $headers .= "ID Nota \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
        $headers .= "No Kendaraan \t";
        $headers .= "AFD \t";
        $headers .= "Block \t";
        $headers .= "Jjg \t";
        $headers .= "Tanggal Panen \t";
	 $headers .= "Tanggal Angkut \t";
	 $headers .= "Kg \t";   
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['ID_NT_AB'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['AFD'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG'])."\t";
            $line .= str_replace('"', '""',$row['TANGGAL_PANEN'])."\t";
	     $line .= str_replace('"', '""',$row['TANGGAL_ANGKUT'])."\t";
	     $line .= str_replace('"', '""',$row['ROUND_TONASE'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=NAB_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";    
    }
    
    //## Create Report: Export - TBG ##
    function export_tbg(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_tbg_data($periode,$periode_to,$company);

        //baris 1
        $headers .= "ID Timbangan \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
        $headers .= "No Kendaraan \t";
        $headers .= "Tgl Masuk \t";
        $headers .= "Tgl Keluar \t";
        $headers .= "Berat Isi \t";
        $headers .= "Berat Kosong \t";
        $headers .= "Berat Bersih (Per Truk) \t";
        $headers .= "AFD \t";
        $headers .= "Block \t";
        $headers .= "Jjg \t";
        $headers .= "Berat Empiris (Sebaran) \t";  
        $headers .= "Berat Real (Sebaran) \t";
     $headers .= "Type Buah \t";
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['ID_TIMBANGAN'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALM'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALK'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ISI'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_KOSONG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
            $line .= str_replace('"', '""',$row['AFD'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_EMPIRIS'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_REAL'])."\t";
            $line .= str_replace('"', '""',$row['TYPE_BUAH'])."\t";

            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=BERAT_EMPIRIS_REAL_PKS_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data"; 
        
    }

    //## Create Report: Export - export_tbg_pks_luar##
    function export_tbg_pks_luar(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_tbg_data_pks_luar($periode,$periode_to,$company);

        //baris 1
        $headers .= "ID Timbangan \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
        $headers .= "No Kendaraan \t";
        $headers .= "Tgl Masuk \t";
        $headers .= "Tgl Keluar \t";
        $headers .= "Berat Isi \t";
        $headers .= "Berat Kosong \t";
        $headers .= "Berat Bersih (Per Truk) \t";
        $headers .= "AFD \t";
        $headers .= "Block \t";
        $headers .= "Jjg \t";
        $headers .= "Berat Empiris (Sebaran) \t";  
        $headers .= "Berat Real (Sebaran) \t";
        $headers .= "Type Buah \t";
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['ID_TIMBANGAN'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALM'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALK'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ISI'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_KOSONG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
            $line .= str_replace('"', '""',$row['AFD'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_EMPIRIS'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_REAL'])."\t";
            $line .= str_replace('"', '""',$row['TYPE_BUAH'])."\t";

            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=BERAT_EMPIRIS_REAL_PKS_LUAR".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data"; 
        
    }
    
	//## Create Report: Export - TBG ##
    function export_tbg_kebun(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_tbg_data_kebun($periode,$periode_to,$company);

        //baris 1
        $headers .= "ID Timbangan \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
        $headers .= "No Kendaraan \t";
        $headers .= "Tgl Masuk \t";
        $headers .= "Tgl Keluar \t";
        $headers .= "Berat Isi \t";
        $headers .= "Berat Kosong \t";
        $headers .= "Berat Bersih (Per Truk) \t";
        $headers .= "AFD \t";
        $headers .= "Block \t";
        $headers .= "Jjg \t";
        $headers .= "Berat Empiris (Sebaran) \t";  
        $headers .= "Berat Real (Sebaran) \t";
     $headers .= "Type Buah \t";
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['ID_TIMBANGAN'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALM'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALK'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ISI'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_KOSONG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
            $line .= str_replace('"', '""',$row['AFD'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_EMPIRIS'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_REAL'])."\t";
            $line .= str_replace('"', '""',$row['TYPE_BUAH'])."\t";

            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=BERAT_EMPIRIS_REAL_KEBUN_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data"; 
        
    }
	
    //## Create Report: Export - TBG (Buah Luar) ##
    function export_tbgluar(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_tbgluar_data($periode,$periode_to,$company);

        //baris 1
        $headers .= "ID Timbangan \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
        $headers .= "No Kendaraan \t";
        $headers .= "Tgl Masuk \t";
        $headers .= "Tgl Keluar \t";
        $headers .= "Berat Kosong \t";
        $headers .= "Tonase \t";
        $headers .= "Potongan (Kg) \t";
        $headers .= "Grading (%) \t";
        $headers .= "Tonase Dibayar (Kg) \t";
        $headers .= "Supplier \t";
    
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['ID_TIMBANGAN'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALM'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALK'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_KOSONG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ISI']-$row['BERAT_KOSONG'])."\t";
            $line .= str_replace('"', '""',$row['POTONGAN_KG'])."\t";
            $line .= str_replace('"', '""',$row['GRD_LAINNYA'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
            $line .= str_replace('"', '""',$row['SUPPLIERCODE'])."\t";

            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=TBGLUAR_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data"; 
        
    }
	
	//## Create Report: Export - TBG KEBUN ##
    function export_tbgkebun(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->get_tbgkebun_data($periode,$periode_to,$company);

        //baris 1
		$headers .= "No \t";
        $headers .= "No Tiket \t";
        $headers .= "No SPB \t";
		$headers .= "Tgl Masuk \t";
        $headers .= "Tgl Keluar \t";
		$headers .= "Jam Masuk \t";
        $headers .= "Jam Keluar \t";		
        $headers .= "No Kendaraan \t";
        $headers .= "Driver\t";
		$headers .= "Jenis Muatan\t";
		$headers .= "Berat Isi \t";
        $headers .= "Berat Kosong \t";
        $headers .= "Berat Bersih \t";
        $headers .= "Tipe Buah \t";
        $headers .= "Janjang \t";
        $headers .= "PT \t";
    
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
            $line .= str_replace('"', '""',$no)."\t";       
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t";
            $line .= str_replace('"', '""',$row['NO_SPB'])."\t"; 
            $line .= str_replace('"', '""',$row['TANGGALM'])."\t";
            $line .= str_replace('"', '""',$row['TANGGALK'])."\t";
            $line .= str_replace('"', '""',$row['WAKTUM'])."\t";
            $line .= str_replace('"', '""',$row['WAKTUK'])."\t";
            $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['DRIVER_NAME'])."\t";
            $line .= str_replace('"', '""',$row['JENIS_MUATAN'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ISI'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_KOSONG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
			$line .= str_replace('"', '""',$row['TYPE_BUAH'])."\t";
			$line .= str_replace('"', '""',$row['JJG'])."\t";
			$line .= str_replace('"', '""',$row['COMPANY_CODE'])."\t";

            $no++;
            $data .= trim($line)."\n";  
        }        

        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=TBGKEBUN_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data"; 
        
    }
    
    //## Create Report: Export - BJR Ditetapkan ##
    function export_bjrttp(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->export_bjrttp($periode,$company);

        //baris 1
        $headers .= "BULAN \t";
        $headers .= "TAHUN \t";
        $headers .= "AFD \t";
        $headers .= "BLOCK \t";
        $headers .= "BJR VALUE \t";  
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['BULAN'])."\t";
            $line .= str_replace('"', '""',$row['TAHUN'])."\t"; 
            $line .= str_replace('"', '""',$row['AFD'])."\t";
            $line .= str_replace('"', '""',$row['BLOCK'])."\t";
            $line .= str_replace('"', '""',$row['VALUE'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=BJRDTTP_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";    
    }
    
    //## Create Report: GC - Produksi Kebun (Panen) ## 
    function generate_pdf_nab(){           
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        if(!empty($periode) && !empty($company)){
            $pdf = new pdf_usage(); 
               
            $pdf->Open();
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->SetMargins(5, 13,20);
            $pdf->AddPage('L', 'LEGAL');
            $pdf->AliasNbPages(); 
                
            $pdf->SetStyle("s1","arial","",9,"");
            $pdf->SetStyle("s2","arial","",8,"");
            $pdf->SetStyle("s3","arial","",10,"");
            
            $pdf->SetTextColor(118, 0, 3);
            //$pdf->SetX(60);
            //$pdf->Ln(1);
            //$pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
            
            $pdf->Ln(2);
            
            //load the table default definitions DEFAULT!!!
            require_once(APPPATH . 'libraries/rptPDF_def.inc'); 
            $columns = 9; //number of Columns
            
            //Initialize the table class
            $pdf->tbInitialize($columns, true, true);
            
            //set the Table Type
            $pdf->tbSetTableType($table_default_table_type);
            $aSimpleHeader = array();
            
            $header = array('No','LOCATION CODE','ACTIVITY CODE', 'LHM','','NOTA ANGKUT','', 'RESTAN','');
            $header2 = array('','','','JANJANG PANEN','BERAT PANEN (Kg)','JANJANG PANEN','BERAT PANEN (Kg)','JANJANG PANEN','BERAT PANEN (Kg)');
            //Table Header
            for($i=0; $i < $columns+1; $i++) {
                $aSimpleHeader[$i] = $table_default_header_type;
                $aSimpleHeader[$i]['TEXT'] = $header[$i];
                $aSimpleHeader[0]['WIDTH'] = 7.5;
                $aSimpleHeader[1]['WIDTH'] = 30;
                $aSimpleHeader[2]['WIDTH'] = 30;
                $aSimpleHeader[3]['WIDTH'] = 30;
                $aSimpleHeader[4]['WIDTH'] = 30;
                $aSimpleHeader[5]['WIDTH'] = 30;
                $aSimpleHeader[6]['WIDTH'] = 30;
                $aSimpleHeader[7]['WIDTH'] = 30;
                $aSimpleHeader[8]['WIDTH'] = 30;
                
                $aSimpleHeader[$i]['LN_SIZE'] = 5;
                $aSimpleHeader[3]['COLSPAN'] = 2;
                $aSimpleHeader[5]['COLSPAN'] = 2;
                $aSimpleHeader[7]['COLSPAN'] = 2;
                $aSimpleHeader[0]['ROWSPAN'] = 2;
                $aSimpleHeader[1]['ROWSPAN'] = 2;
                $aSimpleHeader[2]['ROWSPAN'] = 2;
                 
                
                $aSimpleHeader2[$i] = $table_default_header_type;
                $aSimpleHeader2[$i]['TEXT'] = $header2[$i];
                $aSimpleHeader2[0]['WIDTH'] = 7.5;
                $aSimpleHeader2[1]['WIDTH'] = 30;
                $aSimpleHeader2[2]['WIDTH'] = 30;
                $aSimpleHeader2[3]['WIDTH'] = 30;
                $aSimpleHeader2[4]['WIDTH'] = 30;
                $aSimpleHeader2[5]['WIDTH'] = 30;
                $aSimpleHeader2[6]['WIDTH'] = 30;
                $aSimpleHeader2[7]['WIDTH'] = 30; 
                $aSimpleHeader2[8]['WIDTH'] = 30;
                $aSimpleHeader2[$i]['LN_SIZE'] = 5; 
            }
            
            $pdf->tbSetHeaderType($aSimpleHeader);
            $pdf->tbSetHeaderType($aSimpleHeader2);
            //Draw the Header
            $pdf->tbDrawHeader();

            //Table Data Settings
            $aDataType = Array();
            for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
            $pdf->tbSetDataType($aDataType);
                    
            $total_jjg_lhm=0;
            $total_berat_lhm=0;
            $total_jjg_nab=0;
            $total_berat_nab=0;
            $total_jjg_restan=0;
            $total_berat_restan=0;
            $data_panen=$this->model_s_analisa_panen->generate_lhm_nab($ar,$company);
            $i = 1;    
            foreach ($data_panen as $row)
            {
                $data = Array();
                $data[0]['TEXT'] = $i;
                $data[1]['TEXT'] = $row['LOCATION_CODE'];
                $data[2]['TEXT'] = $row['ACTIVITY_CODE'];
                $data[3]['TEXT'] = $row['JANJANG_PANEN'];
                $data[4]['TEXT'] = $row['BERAT_PANEN'];        
                $data[5]['TEXT'] = $row['JANJANG_NAB'];
                $data[6]['TEXT'] = $row['BERAT_REAL'];
                $restan_jjg = $row['JANJANG_PANEN']-$row['JANJANG_NAB'];
                $restan_wb = $row['BERAT_PANEN']-$row['BERAT_REAL'];
                
                $data[7]['TEXT'] = $restan_jjg;
                $data[8]['TEXT'] = $restan_wb;
                
                $total_jjg_lhm+=$row['JANJANG_PANEN'];
                $total_berat_lhm+=$row['BERAT_PANEN'];
                $total_jjg_nab+=$row['JANJANG_NAB'];
                $total_berat_nab+=$row['BERAT_REAL']; 
                $total_jjg_restan+=$restan_jjg;
                $total_berat_restan+=$restan_wb;
                $i++;
                    
                $pdf->tbDrawData($data);
            }
            $data[0]['TEXT'] = "Total";
            $data[0]['COLSPAN'] =3;            
            $data[3]['TEXT'] = number_format($total_jjg_lhm,2,'.',',');
            $data[4]['TEXT'] = number_format($total_berat_lhm,2,'.',',');
            $data[5]['TEXT'] = number_format($total_jjg_nab,2,'.',',');
            $data[6]['TEXT'] = number_format($total_berat_nab,2,'.',',');
            $data[7]['TEXT'] = number_format($total_jjg_restan,2,'.',',');
            $data[8]['TEXT'] = number_format($total_berat_restan,2,'.',',');
            $pdf->tbDrawData($data);
                       
            $pdf->tbOuputData();
            $pdf->tbDrawBorder();
            
                                
            $pdf->Ln(15.5); 
            
            //require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
            
            $pdf->Output();
        }
    }
    
    function get_adem_sales(){
        $pg_conn=$this->model_s_analisa_panen->get_adem_sales();
        $return['status'] =$pg_conn;
        $return['error']=false;
        echo json_encode($return);
    }
    
    //## Create Report: Export - Sounding ##
    function generate_lhm_sounding(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        if(!empty($periode) && !empty($company)){
            $data_sounding=$this->model_s_analisa_panen->generate_sounding($ar,$ar2,$company);
            $SOUNDING = "";
            $i = 1;
            
            $SOUNDING .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $SOUNDING .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $SOUNDING .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $SOUNDING .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $SOUNDING .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
       
            $SOUNDING .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $SOUNDING .= "<tr><td align='center' class='tbl_th'> No. </td>";
            $SOUNDING .= "<td align='center' class='tbl_th' ROWSPAN=2>TANGGAL</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>WAKTU</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>ID Storage</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>HEIGHT (M)</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>TEMPERATURE (C)</td>";
	     $SOUNDING .= "<td align='center' class='tbl_th'>HEIGHT 2 (M)</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>VOLUME</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>WEIGHT</td>";
            $SOUNDING .= "<tr>";
            
         
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            
            foreach($data_sounding as $row){
                $SOUNDING .= '<tr id="tr_1">';
                $SOUNDING .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $SOUNDING .= '<td width="150" class="tbl_td" align="center">&nbsp;'.$row['DATE'].'</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['TIME'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['ID_STORAGE'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['HEIGHT'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['TEMPERATURE'].'&nbsp;</td>';
		  $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['HEIGHT2'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['VOLUME'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['WEIGHT'].'&nbsp;</td>';
                
                $SOUNDING .= '</tr>';
                $i++;    
            }
            $SOUNDING .="<tr><td class='tbl_td' align='center' colspan='2'><strong>Total</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>"; 

            $SOUNDING .= "</table>"; 
               
            echo $SOUNDING;
        }    
    }
    
    //## Create Report: Export - Sounding ##
    function generate_xls_sounding(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_sounding=$this->model_s_analisa_panen->generate_sounding($ar,$ar2,$company);
        $SOUNDING = "";

        //baris 1
        $headers .= "TANGGAL \t";
        $headers .= "WAKTU \t";
        $headers .= "IDSTORAGE \t";
        $headers .= "HEIGHT (M) \t";
        $headers .= "TEMPERATURE (C) \t"; 
        $headers .= "VOLUME \t";
        $headers .= "WEIGHT \t"; 
        
        $no = 1;
        $total_netto=0;
        foreach ($data_sounding as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['DATE'])."\t";
            $line .= str_replace('"', '""',$row['TIME'])."\t"; 
            $line .= str_replace('"', '""',$row['ID_STORAGE'])."\t";
            $line .= str_replace('"', '""',$row['HEIGHT'])."\t";
            $line .= str_replace('"', '""',$row['TEMPERATURE'])."\t";
            $line .= str_replace('"', '""',$row['VOLUME'])."\t";
            $line .= str_replace('"', '""',$row['WEIGHT'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=SOUNDING_".$company."_".$periode."_TO_".$periode_to.".xls");
        echo "$judul\n$headers\n$data";    
    }
    
    //## Create Report: TBG - Lampiran BA Transport Panen ##
    function generate_htm_tbglampbatpanen(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $kodekontraktor = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        if(!empty($periode) && !empty($company)){
            $data_sounding=$this->model_s_analisa_panen->generate_tbglampbatpanen($kodekontraktor,$ar,$ar2,$company);
            $SOUNDING = "";
            $i = 1;
            
            $SOUNDING .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $SOUNDING .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $SOUNDING .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $SOUNDING .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $SOUNDING .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
       
            $SOUNDING .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $SOUNDING .= "<tr><td align='center' class='tbl_th'> No. </td>";
            $SOUNDING .= "<td align='center' class='tbl_th' ROWSPAN=2>No Kendaraan</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Nama Driver</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>AFD</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Berat Real</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Harga/Kg</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Total Terima (Rp)</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>PPH 23 (2%)</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Biaya SPSI (Rp 4)</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Total Biaya</td>";
            $SOUNDING .= "<tr>";
            
         
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            
            foreach($data_sounding as $row){
                $SOUNDING .= '<tr id="tr_1">';
                $SOUNDING .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['NO_KENDARAAN'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['DRIVER_NAME'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['AFD'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['BERAT_REAL'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['COST'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['C_TERIMA'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['PPH23'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['SPSI'].'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="right">'.$row['C_TOTAL_TERIMA'].'&nbsp;</td>';
                $SOUNDING .= '</tr>';
                $i++;    
            }
            $SOUNDING .="<tr><td class='tbl_td' align='center' colspan='2'><strong>Total</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".'-'."&nbsp;</strong></td>";
            
            $SOUNDING .= "</table>"; 
               
            echo $SOUNDING;
        }    
    }
    
    //## Create Report: TBG - Lampiran BA Transport Panen ##  
    function generate_pdf_tbglampbatpanen(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $kodekontraktor = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        if(!empty($periode) && !empty($company)){
            $pdf = new pdf_usage(); 
               
            $pdf->Open();
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->SetMargins(5, 13,20);
            $pdf->AddPage('L', 'LEGAL');
            $pdf->AliasNbPages(); 
                
            $pdf->SetStyle("s1","arial","",9,"");
            $pdf->SetStyle("s2","arial","",8,"");
            $pdf->SetStyle("s3","arial","",10,"");
            
             $pdf->SetTextColor(118, 0, 3);
            //$pdf->SetX(60);
            //$pdf->Ln(1);
            $pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
            $pdf->MultiCellTag(200, 5, "<s3>Lampiran Berita Acara - Transport Panen</s3>", 0);
            $pdf->MultiCellTag(200, 5, "<s1>PERIODE : ". $periode ." - ". $periode_to ." </s1>", 0);
            $nama_kontraktor = $this->model_s_analisa_panen->get_kontraktor_detail($kodekontraktor,$company) ;
            foreach($nama_kontraktor as $nmKontraktor){
                $pdf->MultiCellTag(200, 5, "<s1>NAMA KONTRAKTOR : ".$nmKontraktor['NAMA_KONTRAKTOR']." </s1>", 0);    
            }
            
            $pdf->Ln(2);
            
            //load the table default definitions DEFAULT!!!
            require_once(APPPATH . 'libraries/rptPDF_def.inc'); 
            $columns = 10; //number of Columns
            
            //Initialize the table class
            $pdf->tbInitialize($columns, true, true);
            
            //set the Table Type
            $pdf->tbSetTableType($table_default_table_type);
            $aSimpleHeader = array();
            
            $header = array('No','Nama Driver','No Polisi' ,'AFD', 'Tonase','Harga/Kg','Total Terima (Rp)','PPH 23 (2%)', 'Biaya SPSI (Rp 4)','Total Biaya');

            //Table Header
            for($i=0; $i < $columns+1; $i++) {
                $aSimpleHeader[$i] = $table_default_header_type;
                $aSimpleHeader[$i]['TEXT'] = $header[$i];
                $aSimpleHeader[0]['WIDTH'] = 7.5;
                $aSimpleHeader[1]['WIDTH'] = 30;
                $aSimpleHeader[2]['WIDTH'] = 30;
                $aSimpleHeader[3]['WIDTH'] = 30;
                $aSimpleHeader[4]['WIDTH'] = 30;
                $aSimpleHeader[5]['WIDTH'] = 30;
                $aSimpleHeader[6]['WIDTH'] = 30;
                $aSimpleHeader[7]['WIDTH'] = 30;
                $aSimpleHeader[8]['WIDTH'] = 30;
                $aSimpleHeader[9]['WIDTH'] = 30;
                
                $aSimpleHeader[$i]['LN_SIZE'] = 5;
            }
            
            $pdf->tbSetHeaderType($aSimpleHeader);
            //Draw the Header
            $pdf->tbDrawHeader();

            //Table Data Settings
            $aDataType = Array();
            for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
            $pdf->tbSetDataType($aDataType);
                    
            $data_panen=$this->model_s_analisa_panen->generate_tbglampbatpanen($kodekontraktor,$ar,$ar2,$company); 
            
            $i = 0;
            $xx=0;
            $nama_kontraktor=''; 
            $subtotal_beratreal=0;
            $subtotal_cost=0;
            $subtotal_cterima=0;
            $subtotal_pph=0;
            $subtotal_spsi=0;
            $subtotal_ctotalterima=0;
            $gtotal_beratreal=0;
            $gtotal_cost=0;
            $gtotal_cterima=0;
            $gtotal_pph=0;
            $gtotal_spsi=0;
            $gtotal_ctotalterima=0;
            $tmp_nokend='';   
            foreach ($data_panen as $row){
                $data = Array();
                $datax = array();
                if (str_replace(' ','',$row['NO_KENDARAAN'])!=$tmp_nokend && $tmp_nokend!=''){
                    $data[0]['TEXT'] = "SUB TOTAL";
                    $data[0]['COLSPAN'] = 4;
                    $data[4]['TEXT'] = number_format($subtotal_beratreal) ;        
                    //$data[5]['TEXT'] = number_format($subtotal_cost) ;
                    $data[6]['TEXT'] = number_format($subtotal_cterima) ;
                    $data[7]['TEXT'] = number_format($subtotal_pph) ;
                    $data[8]['TEXT'] = number_format($subtotal_spsi) ;
                    $data[9]['TEXT'] = number_format($subtotal_ctotalterima) ;
                    $subtotal_beratreal=0;
                    $subtotal_cost=0;
                    $subtotal_cterima=0;
                    $subtotal_pph=0;
                    $subtotal_spsi=0;
                    $subtotal_ctotalterima=0;   
                    $pdf->tbDrawData($data);
                    
                    $i++; 
                    $datax[0]['TEXT'] = $i;
                    $datax[1]['TEXT'] = $row['NO_KENDARAAN'];
                    $datax[2]['TEXT'] = $row['DRIVER_NAME'];
                    $datax[3]['TEXT'] = $row['AFD'];
                    $datax[4]['TEXT'] = number_format($row['BERAT_REAL']) ;        
                    $datax[5]['TEXT'] = number_format($row['COST']) ;
                    $datax[6]['TEXT'] = number_format($row['C_TERIMA']) ;
                    $datax[7]['TEXT'] = number_format($row['PPH23']) ;
                    $datax[8]['TEXT'] = number_format($row['SPSI']) ;
                    $datax[9]['TEXT'] = number_format($row['C_TOTAL_TERIMA']) ;
                    $subtotal_beratreal+=$row['BERAT_REAL'];
                    //$subtotal_cost+=$row['COST'];
                    $subtotal_cterima+=$row['C_TERIMA'];
                    $subtotal_pph+=$row['PPH23'];
                    $subtotal_spsi+=$row['SPSI'];
                    $subtotal_ctotalterima+=$row['C_TOTAL_TERIMA'];
                    
                    $gtotal_beratreal+=$row['BERAT_REAL'];
                    //$gtotal_cost+=$row['COST'];
                    $gtotal_cterima+=$row['C_TERIMA'];
                    $gtotal_pph+=$row['PPH23'];
                    $gtotal_spsi+=$row['SPSI'];
                    $gtotal_ctotalterima+=$row['C_TOTAL_TERIMA']; 
                    $pdf->tbDrawData($datax);   
                }else{ 
                    $i++; 
                    $data[0]['TEXT'] = $i;
                    $data[1]['TEXT'] = $row['NO_KENDARAAN'];
                    $data[2]['TEXT'] = $row['DRIVER_NAME'];
                    $data[3]['TEXT'] = $row['AFD'];
                    $data[4]['TEXT'] = number_format($row['BERAT_REAL']) ;        
                    $data[5]['TEXT'] = number_format($row['COST']) ;
                    $data[6]['TEXT'] = number_format($row['C_TERIMA']) ;
                    $data[7]['TEXT'] = number_format($row['PPH23']) ;
                    $data[8]['TEXT'] = number_format($row['SPSI']) ;
                    $data[9]['TEXT'] = number_format($row['C_TOTAL_TERIMA']) ;
                    $subtotal_beratreal+=$row['BERAT_REAL'];
                    //$subtotal_cost+=$row['COST'];
                    $subtotal_cterima+=$row['C_TERIMA'];
                    $subtotal_pph+=$row['PPH23'];
                    $subtotal_spsi+=$row['SPSI'];
                    $subtotal_ctotalterima+=$row['C_TOTAL_TERIMA'];
                    
                    $gtotal_beratreal+=$row['BERAT_REAL'];
                    //$gtotal_cost+=$row['COST'];
                    $gtotal_cterima+=$row['C_TERIMA'];
                    $gtotal_pph+=$row['PPH23'];
                    $gtotal_spsi+=$row['SPSI'];
                    $gtotal_ctotalterima+=$row['C_TOTAL_TERIMA']; 
                    $pdf->tbDrawData($data);   
                }
                $nama_kontraktor=$row['NAMA_KONTRAKTOR'];
                $tmp_nokend=str_replace(' ','',$row['NO_KENDARAAN']); 
                $xx+=1;  
            }
            $data[0]['TEXT'] = "SUB TOTAL";
            $data[0]['COLSPAN'] = 4;
            $data[4]['TEXT'] = number_format($subtotal_beratreal) ;        
            //$data[5]['TEXT'] = number_format($subtotal_cost) ;
            $data[6]['TEXT'] = number_format($subtotal_cterima) ;
            $data[7]['TEXT'] = number_format($subtotal_pph) ;
            $data[8]['TEXT'] = number_format($subtotal_spsi) ;
            $data[9]['TEXT'] = number_format($subtotal_ctotalterima) ;
            $pdf->tbDrawData($data);
            
            $data[0]['TEXT'] = "GRAND TOTAL";
            $data[0]['COLSPAN'] = 4;
            $data[4]['TEXT'] = number_format($gtotal_beratreal) ;        
            //$data[5]['TEXT'] = number_format($gtotal_cost) ;
            $data[6]['TEXT'] = number_format($gtotal_cterima) ;
            $data[7]['TEXT'] = number_format($gtotal_pph) ;
            $data[8]['TEXT'] = number_format($gtotal_spsi) ;
            $data[9]['TEXT'] = number_format($gtotal_ctotalterima) ;
            $pdf->tbDrawData($data);
                       
            $pdf->tbOuputData();
            $pdf->tbDrawBorder();
            
                                
            $pdf->Ln(15.5); 
            
            require_once(APPPATH . 'libraries/ba_pabrik/lampiran_ba_tp.inc');
            
            $pdf->Output();
        }    
    }
    
    //## Create Report: TBG - BA Transport Panen ##
    function generate_htm_tbgbatpanen(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $kodekontraktor = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 

        if(!empty($periode) && !empty($company)){
            $data_sounding=$this->model_s_analisa_panen->generate_tbgbatpanen($kodekontraktor,$ar,$ar2,$company);
            $SOUNDING = "";
            $i = 1;
            
            $SOUNDING .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $SOUNDING .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $SOUNDING .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $SOUNDING .= ".tbl_2 { font-size: 12px;color:#678197;} ";
            $SOUNDING .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
       
            $SOUNDING .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
            $SOUNDING .= "<tr><td align='center' class='tbl_th'> No. </td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Afdeling</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Item Pekerjaan</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>COST/KG</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Tonase (Kg)</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Total Rupiah</td>";
            $SOUNDING .= "<td align='center' class='tbl_th'>Keterangan</td>";
       
            $SOUNDING .= "<tr>";
            
         
            $style = "";
            $url = base_url().'index.php/s_analisa_panen/';
            $total_beratreal=0;
            $total_cterima=0;
            foreach($data_sounding as $row){
                $SOUNDING .= '<tr id="tr_1">';
                $SOUNDING .= '<td class="tbl_td" ><center>'.$i.'</center></td>';

                $SOUNDING .= '<td width="50" class="tbl_td" align="center">'.$row['AFD'].'&nbsp;</td>';
                $SOUNDING .= '<td width="150" class="tbl_td" align="left"> Angkut TBS ke PKS PT.'.$company.'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="center">'.number_format($row['COST']).'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="center">'.number_format($row['BERAT_REAL']).'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="center">'.number_format($row['C_TERIMA']).'&nbsp;</td>';
                $SOUNDING .= '<td width="100" class="tbl_td" align="left">'.''.'&nbsp;</td>';
                $SOUNDING .= '</tr>';
                $total_beratreal+= $row['BERAT_REAL'];
                $total_cterima+=$row['C_TERIMA'];
                $i++;    
            }
            $pph23 = 0.02 * $total_cterima;
            $spsi = 4 * $total_beratreal;
            $total_potongan =  $pph23 + $spsi;
            $total_terima = $total_cterima - $total_potongan;
            
            $SOUNDING .="<tr><td class='tbl_td' align='center' colspan='4'><strong>Grand Total</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".number_format($total_beratreal)." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".number_Format($total_cterima)." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right' rowspan='5'><strong>".''." &nbsp;</strong></td></tr>";
            $SOUNDING .="<tr><td class='tbl_td' align='center' colspan='4'><strong>PPH 23 (2%)</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".''." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".number_Format($pph23)." &nbsp;</strong></td></tr>";
            $SOUNDING .="<tr><td class='tbl_td' align='center' colspan='4'><strong>Biaya SPSI (Rp. 4.00,-)</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".''." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".number_Format($spsi)." &nbsp;</strong></td></tr>";
            $SOUNDING .="<tr><td class='tbl_td' align='center' colspan='4'><strong>Total Potongan</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".''." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".number_Format($total_potongan)." &nbsp;</strong></td></tr>";
            $SOUNDING .="<tr><td class='tbl_td' align='center' colspan='4'><strong>Total Terima</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".''." &nbsp;</strong></td>";
            $SOUNDING .= "<td class='tbl_td' align='right'><strong>".number_Format($total_terima)." &nbsp;</strong></td></tr>";
            
            $SOUNDING .= "</table>"; 
               
            echo $SOUNDING;
        }    
    }
    
    function generate_pdf_tbgbatpanen(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $kodekontraktor = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        if(!empty($periode) && !empty($company)){
            $pdf = new pdf_usage(); 
               
            $pdf->Open();
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->SetMargins(5, 13,20);
            $pdf->AddPage('L', 'LEGAL');
            $pdf->AliasNbPages(); 
                
            $pdf->SetStyle("s1","arial","",9,"");
            $pdf->SetStyle("s2","arial","",8,"");
            $pdf->SetStyle("s3","arial","",10,"");
            
             $pdf->SetTextColor(118, 0, 3);
            //$pdf->SetX(60);
            //$pdf->Ln(1);
            $pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
            $pdf->MultiCellTag(200, 5, "<s3>Berita Acara - Transport Panen</s3>", 0);
            $pdf->MultiCellTag(200, 5, "<s1>PERIODE : ". $periode ." - ". $periode_to ." </s1>", 0);
            $nama_kontraktor = $this->model_s_analisa_panen->get_kontraktor_detail($kodekontraktor,$company) ;
            foreach($nama_kontraktor as $nmKontraktor){
                $pdf->MultiCellTag(200, 5, "<s1>NAMA KONTRAKTOR : ".$nmKontraktor['NAMA_KONTRAKTOR']." </s1>", 0);    
            }

            $pdf->Ln(2);
            
            //load the table default definitions DEFAULT!!!
            require_once(APPPATH . 'libraries/rptPDF_def.inc'); 
            $columns = 7; //number of Columns
            
            //Initialize the table class
            $pdf->tbInitialize($columns, true, true);
            
            //set the Table Type
            $pdf->tbSetTableType($table_default_table_type);
            $aSimpleHeader = array();
            
            $header = array('No' ,'AFDELING', 'ITEM PEKERJAAN','COST/Kg','TONASE (KG)','TOTAL RUPIAH', 'KETERANGAN');

            //Table Header
            for($i=0; $i < $columns+1; $i++) {
                $aSimpleHeader[$i] = $table_default_header_type;
                $aSimpleHeader[$i]['TEXT'] = $header[$i];
                $aSimpleHeader[0]['WIDTH'] = 7.5;
                $aSimpleHeader[1]['WIDTH'] = 30;
                $aSimpleHeader[2]['WIDTH'] = 75;
                $aSimpleHeader[3]['WIDTH'] = 30;
                $aSimpleHeader[4]['WIDTH'] = 30;
                $aSimpleHeader[5]['WIDTH'] = 30;
                $aSimpleHeader[6]['WIDTH'] = 30;
                 
                $aSimpleHeader[$i]['LN_SIZE'] = 5;
            }
            
            $pdf->tbSetHeaderType($aSimpleHeader);
            //Draw the Header
            $pdf->tbDrawHeader();

            //Table Data Settings
            $aDataType = Array();
            for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
            $pdf->tbSetDataType($aDataType);
                    
            $data_panen=$this->model_s_analisa_panen->generate_tbgbatpanen($kodekontraktor,$ar,$ar2,$company); 
            
            $i = 1;
            $subtotal_beratreal = 0 ;
            $subtotal_cterima = 0 ;
            foreach ($data_panen as $row){
                $data = Array();
                
                $data[0]['TEXT'] = $i;
                $data[1]['TEXT'] = $row['AFD'];
                $data[2]['TEXT'] = 'Angkut TBS ke PKS PT.'.$company;         
                $data[3]['TEXT'] = number_format($row['COST']) ;
                $data[4]['TEXT'] = number_format($row['BERAT_REAL']) ;
                $data[5]['TEXT'] = number_format($row['C_TERIMA']) ;
                $pdf->tbDrawData($data);
                $subtotal_beratreal += $row['BERAT_REAL'];
                $subtotal_cterima += $row['C_TERIMA'];
                $i++;
            }
            $pph23 = 0.02 * $subtotal_cterima;
            $spsi = 4 * $subtotal_beratreal;
            $total_potongan =  $pph23 + $spsi;
            $total_terima = $subtotal_cterima - $total_potongan;
            
            $data1[0]['TEXT'] = "GRAND TOTAL";
            $data1[0]['COLSPAN'] = 4;
            $data1[4]['TEXT'] = number_format($subtotal_beratreal) ;        
            $data1[5]['TEXT'] = number_format($subtotal_cterima) ;
            $pdf->tbDrawData($data1);
            
            $data2[0]['TEXT'] = "PPH 23 (2%)";
            $data2[0]['COLSPAN'] = 4;        
            $data2[5]['TEXT'] = number_format($pph23) ;
            $pdf->tbDrawData($data2);
            
            $data3[0]['TEXT'] = "SPSI (Rp. 4.00,-)";
            $data3[0]['COLSPAN'] = 4;        
            $data3[5]['TEXT'] = number_format($spsi) ;
            $pdf->tbDrawData($data3);
            
            $data4[0]['TEXT'] = "TOTAL POTONGAN";
            $data4[0]['COLSPAN'] = 4;       
            $data4[5]['TEXT'] = number_format($total_potongan) ;
            $pdf->tbDrawData($data4);
            
            $data5[0]['TEXT'] = "TOTAL TERIMA";
            $data5[0]['COLSPAN'] = 4;       
            $data5[5]['TEXT'] = number_format($total_terima) ;
            $pdf->tbDrawData($data5);
                       
            $pdf->tbOuputData();
            $pdf->tbDrawBorder();
                           
            $pdf->Ln(15.5); 
            
            require_once(APPPATH . 'libraries/ba_pabrik/lampiran_ba_tp.inc');
            
            $pdf->Output();
        }    
    }
    
    //## Create Report: TBG - Detail BA Transport Panen ##
    function generate_xls_dttbgbatpanen(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_analisa_panen->generate_lhm_bjr($periode,$periode_to,$company);

        //baris 1
        $headers .= "Tanggal Panen \t";
        $headers .= "Tanggal Timbang \t";
        $headers .= "Tanggal Panen (NAB) \t";
        $headers .= "Lokasi \t";
        $headers .= "Janjang Panen \t";
        $headers .= "Janjang Angkut (NAB) \t";
        $headers .= "Janjang Angkut (TBG) \t";
        $headers .= "BJR Real \t";  
        $headers .= "Berat Empiris \t";
        $headers .= "Berat Real \t";   
        $headers .= "BJR \t";
        $headers .= "SPB NAB \t";
        $headers .= "SPB TBG \t";
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['TANGGAL_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['TANGGAL_TIMBANG'])."\t"; 
            $line .= str_replace('"', '""',$row['TANGGAL_PANEN_NAB'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
            $line .= str_replace('"', '""',$row['JANJANG_PANEN'])."\t";
            $line .= str_replace('"', '""',$row['JJG_ANGKUT_NAB'])."\t";
            $line .= str_replace('"', '""',$row['JJG_ANGKUT_TBG'])."\t";
            $line .= str_replace('"', '""',round($row['BJRREAL'],2))."\t";
            $line .= str_replace('"', '""',$row['BERAT_EMPIRIS'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_REAL'])."\t";
            $line .= str_replace('"', '""',$row['BJR_REAL'])."\t";
            $line .= str_replace('"', '""',$row['SPB_NAB'])."\t";
            $line .= str_replace('"', '""',$row['SPB_TBG'])."\t";
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=BATRANSPORT_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    
    function generate_pdf_dttbgbatpanen(){
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $afd = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8'));
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2); 
        
        if(!empty($periode) && !empty($company)){
            $pdf = new pdf_usage(); 
               
            $pdf->Open();
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->SetMargins(5, 13,20);
            $pdf->AddPage('L', 'LEGAL');
            $pdf->AliasNbPages(); 
                
            $pdf->SetStyle("s1","arial","",9,"");
            $pdf->SetStyle("s2","arial","",8,"");
            $pdf->SetStyle("s3","arial","",10,"");
            
             $pdf->SetTextColor(118, 0, 3);
            //$pdf->SetX(60);
            //$pdf->Ln(1);
            $pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
            $pdf->MultiCellTag(200, 5, "<s3>Berita Acara Per Block - Transport Panen</s3>", 0);
            $pdf->MultiCellTag(200, 5, "<s1>PERIODE : ". $periode ." - ". $periode_to ." </s1>", 0);

            $pdf->MultiCellTag(200, 5, "<s1>AFD : ".strtoupper($afd)." </s1>", 0);    


            $pdf->Ln(2);
            
            //load the table default definitions DEFAULT!!!
            require_once(APPPATH . 'libraries/rptPDF_def.inc'); 
            $columns = 11; //number of Columns
            
            //Initialize the table class
            $pdf->tbInitialize($columns, true, true);
            
            //set the Table Type
            $pdf->tbSetTableType($table_default_table_type);
            $aSimpleHeader = array();
            
            $header = array('No' ,'Tanggal', 'No Polisi','No Tiket Timbangan', 'Block', 'TONASE (KG)', 'Harga/Kg (Rp)',
                        'Jumlah','Potongan PPH 2%','TOTAL RUPIAH', 'KETERANGAN');

            //Table Header
            for($i=0; $i < $columns+1; $i++) {
                $aSimpleHeader[$i] = $table_default_header_type;
                $aSimpleHeader[$i]['TEXT'] = $header[$i];
                $aSimpleHeader[0]['WIDTH'] = 7.5;
                $aSimpleHeader[1]['WIDTH'] = 30;
                $aSimpleHeader[2]['WIDTH'] = 30;
                $aSimpleHeader[3]['WIDTH'] = 30;
                $aSimpleHeader[4]['WIDTH'] = 30;
                $aSimpleHeader[5]['WIDTH'] = 30;
                $aSimpleHeader[6]['WIDTH'] = 30;
                $aSimpleHeader[7]['WIDTH'] = 30;
                $aSimpleHeader[8]['WIDTH'] = 30;
                $aSimpleHeader[9]['WIDTH'] = 30;
                $aSimpleHeader[10]['WIDTH'] = 30;
                 
                $aSimpleHeader[$i]['LN_SIZE'] = 5;
            }
            
            $pdf->tbSetHeaderType($aSimpleHeader);
            //Draw the Header
            $pdf->tbDrawHeader();

            //Table Data Settings
            $aDataType = Array();
            for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
            $pdf->tbSetDataType($aDataType);
                    
            $data_panen=$this->model_s_analisa_panen->generate_dttbgbatpanen($afd,$ar,$ar2,$company); 
            
            $i = 1;
            $total_beratreal=0;
            $total_cost=0;
            $total_jumlah=0;
            $total_potongan=0;
            $total_jumlahakhir=0;
            foreach ($data_panen as $row){
                $data = Array();
                
                $data[0]['TEXT'] = $i;
                $data[1]['TEXT'] = $row['TANGGALM'];
                $data[2]['TEXT'] = $row['NO_KENDARAAN'];         
                $data[3]['TEXT'] = $row['NO_TIKET'] ;
                $data[4]['TEXT'] = $row['BLOCK'] ;
                $data[5]['TEXT'] = number_format($row['BERAT_REAL']) ;
                $data[6]['TEXT'] = number_format($row['COST']) ; 
                $data[7]['TEXT'] = number_format($row['JUMLAH']) ; 
                $data[8]['TEXT'] = number_format($row['POTONGAN']) ; 
                $data[9]['TEXT'] = number_format($row['JUMLAH_AKHIR']) ; 
                $data[10]['TEXT'] = '';
                $total_beratreal+=$row['BERAT_REAL'];
                $total_cost+=$row['COST']; 
                $total_jumlah+=$row['JUMLAH'];
                $total_potongan+=$row['POTONGAN'];
                $total_jumlahakhir+=$row['JUMLAH_AKHIR'];
                $pdf->tbDrawData($data);

                $i++;
            }
           
            
            $data1[0]['TEXT'] = "GRAND TOTAL";
            $data1[0]['COLSPAN'] = 5;
            $data1[5]['TEXT'] = number_format($total_beratreal) ;        
            $data1[6]['TEXT'] = number_format($total_cost) ;
            $data1[7]['TEXT'] = number_format($total_jumlah) ; 
            $data1[8]['TEXT'] = number_format($total_potongan) ; 
            $data1[9]['TEXT'] = number_format($total_jumlahakhir) ; 
            $pdf->tbDrawData($data1);
                       
            $pdf->tbOuputData();
            $pdf->tbDrawBorder();
                           
            $pdf->Ln(15.5); 
            
            require_once(APPPATH . 'libraries/ba_pabrik/lampiran_ba_tp.inc');
            
            $pdf->Output();
        }    
    }
}
?>
