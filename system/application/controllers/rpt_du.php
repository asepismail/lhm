<?
class rpt_du extends Controller 
{
    function rpt_du ()
    {
        parent::Controller();    

        $this->load->model( 'model_rpt_du' ); 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_du";
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->database();
        $this->load->plugin('to_excel');
        $this->load->library('cezpdf');
        $this->load->helper('file');
        require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
    }
    
    function js_du(){
        $js = "paceOptions = {
				  //elements: true
				  ajax: false, // disabled
				  document: false, // disabled
				  eventLag: true, // disabled
				  elements: {
					selectors: ['.frame']
				  }
				};
				
				jQuery(document).ready(function(){
					$('#form_progress').hide();		
					
					$('#FROM').datepicker({dateFormat:'yy-mm-dd'});
               		$('#TO').datepicker({dateFormat:'yy-mm-dd'});				
				});
				
				$('#progressbar').dialog({
							bgiframe: true, autoOpen: false,
							resizable: true, draggable: true,
							closeOnEscape:false, height: 160,
							width: 220, modal: true
				});
				
				function load(url){
					var x = new XMLHttpRequest()
					x.open('GET', url, true);
					x.send();
				};
        		
				jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + $('#bulan').val();
                var gc = $('#GANG_CODE').val();
                var jns_laporan = $('#jns_laporan').val();
				
				var tfrom = document.getElementById('FROM').value;
                var elem = tfrom.split('-');
                from = elem[0]+elem[1]+elem[2];
                            
                var tto = document.getElementById('TO').value;
                var elem2 = tto.split('-');
                to = elem2[0]+elem2[1]+elem2[2];
                        
                var period = to - from;
				
				if ( period > 0 ){
					var postdata = {}; 
					$.post( url+'rpt_du/cekGeneratePPh/'+from, postdata,function(status) 		  { 
						  var status = new String(status);
						  if(status.replace(/\s/g,'') != '') {
							  if(status.replace(/\s/g,'') == '0'){
								  if ( jns_laporan == 'html'){
										urls = url + 'rpt_du/preview/' + gc + '/' + from  + '/' + to, 
										$('#frame').attr('src',urls); 
										load(urls);
									} else if ( jns_laporan == 'pdf'){
										urls = url+'rpt_du/generate/' + gc + '/' + from  + '/' + to,  
										$('#frame').attr('src',urls);
										load(urls);                
									} else if ( jns_laporan == 'excell'){
										urls = url + 'rpt_du/du_xls/' + gc + '/' + from  + '/' + to,
										$.download(urls,'');
										load(urls);
									}
							  } else {
							  		alert(status);
							  }
						  }
					});
				} else {
                    alert('rentang periode salah!!');
                    return false;
                }
            }); 
				
			jQuery('#regenerategc').click(function (){
					var answer = confirm ('regenerate ulang data kemandoran?');
					if (answer){
				      var postdata = {}; 
					  var tfrom = document.getElementById('FROM').value;
					  var elem = tfrom.split('-');
					  var periode = elem[0]+elem[1];
					
					  $('#load').show();
					  document.getElementById('msg').innerHTML= 'Mohon menunggu...regenerate sedang diproses...';
					  $('#progressbar').dialog('open');
					  $.post( url+'rpt_du/generateKemandoran/'+periode, postdata,function(status) 		  { 
						var status = new String(status);
						   if(status.replace(/\s/g,'') != '') { 
								$('#load').hide();
								document.getElementById('msg').innerHTML= status; 
						  };  
					  }); 
				    }
			});
				
			jQuery('#regeneratehk').click(function (){
					var answer = confirm ('regenerate ulang GP Karyawan BHL & SKU?');
					if (answer){
				      var postdata = {}; 
					  var tfrom = document.getElementById('FROM').value;
					  var elem = tfrom.split('-');
					  var tahun = elem[0];
					
					  $('#load').show();
					  document.getElementById('msg').innerHTML= 'Mohon menunggu...regenerate sedang diproses...';
					  $('#progressbar').dialog('open');
					  $.post( url+'rpt_du/generateGP/'+tahun, postdata,function(status) 		  { 
						var status = new String(status);
						   if(status.replace(/\s/g,'') != '') { 
								$('#load').hide();
								document.getElementById('msg').innerHTML= status; 
						  };  
					  }); 
				    }
			});";
        return $js;
    }

    function js_du_afd(){
        $js = " paceOptions = {
				  ajax: false, // disabled
				  document: false, // disabled
				  eventLag: true, // disabled
				  elements: {
					selectors: ['.frame']
				  }
				};
				
				$(function() {
                    $('#FROM').datepicker({dateFormat:'yy-mm-dd'});
                    $('#TO').datepicker({dateFormat:'yy-mm-dd'});
                });
				
				function load(url){
					var x = new XMLHttpRequest()
					x.open('GET', url, true);
					x.send();
				};
				
                jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + $('#bulan').val();
                var gc = $('#division').val();
                var jns_laporan = $('#jns_laporan').val();
                
                var tfrom = document.getElementById('FROM').value;
                var elem = tfrom.split('-');
                from = elem[0]+elem[1]+elem[2];
                            
                var tto = document.getElementById('TO').value;
                var elem2 = tto.split('-');
                to = elem2[0]+elem2[1]+elem2[2];
                        
                var period = to - from;
                
                if ( period > 0 ){
                	var postdata = {}; 
					$.post( url+'rpt_du/cekGeneratePPh/'+from, postdata,function(status){ 
						  var status = new String(status);
						  if(status.replace(/\s/g,'') != '') {
							  if(status.replace(/\s/g,'') == '0'){
									if ( jns_laporan == 'html'){
											urls = url+'rpt_du/preview_afd/' + gc  + '/' + from  + '/' + to, 
												$('#frame').attr('src',urls); 
												load(urls);
										} else if ( jns_laporan == 'pdf'){
											urls = url+'rpt_du/gen_rpt_du_afd/' + gc  + '/' + from  + '/' + to, 
											$('#frame').attr('src',urls); 
											load(urls);                
										} else if ( jns_laporan == 'excell'){
												urls = url + 'rpt_du/du_xls/' + gc + '/' + from + '/' + to + '/div';
												$.download(urls,'');
												load(urls);
										}
							  } else {
							  		alert(status);
							  }
						  }
					});	  
                } else {
                    alert('rentang periode salah!!');
                    return false;
                }    
                
            });";
        return $js;
    }
    
	function js_du_bulanan(){
        $js = " paceOptions = {
				  ajax: false, // disabled
				  document: false, // disabled
				  eventLag: true, // disabled
				  elements: {
					selectors: ['.frame']
				  }
				};
				
				$(function() {
                    $('#FROM').datepicker({dateFormat:'yy-mm-dd'});
                    $('#TO').datepicker({dateFormat:'yy-mm-dd'});
                });
				
				function load(url){
					var x = new XMLHttpRequest()
					x.open('GET', url, true);
					x.send();
				};
				
                jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + $('#bulan').val();
                var gc = $('#i_dept').val();
                var jns_laporan = $('#jns_laporan').val();
                
                var tfrom = document.getElementById('FROM').value;
                var elem = tfrom.split('-');
                from = elem[0]+elem[1]+elem[2];
                            
                var tto = document.getElementById('TO').value;
                var elem2 = tto.split('-');
                to = elem2[0]+elem2[1]+elem2[2];
                        
                var period = to - from;
                
                if ( period > 0 ){
                	var postdata = {}; 
					$.post( url+'rpt_du/cekGeneratePPh/'+from, postdata,function(status){ 
						var status = new String(status);
						if(status.replace(/\s/g,'') != '') {
						    if(status.replace(/\s/g,'') == '0'){
								  
								if ( jns_laporan == 'html'){
										urls = url+'rpt_du/preview_bulanan/' + gc  + '/' + from  + '/' + to, 
											$('#frame').attr('src',urls); 
											load(urls);
									} else if ( jns_laporan == 'pdf'){
										urls = url+'rpt_du/gen_rpt_du_bulanan/' + gc  + '/' + from  + '/' + to, 
										$('#frame').attr('src',urls);
										load(urls);                 
									} else if ( jns_laporan == 'excell'){
											urls = url + 'rpt_du/du_bulanan_xls/' + gc + '/' + from + '/' + to + '/div';
											$.download(urls,'');
											load(urls);
									}
							 } else {
									alert(status);
							 }
						 }
					});	  
                } else {
                    alert('rentang periode salah!!');
                    return false;
                }    
                
            });";
        return $js;
    }
    /* $('.button').popupWindow({ 
        windowURL:url+'rpt_du/gen_rpt_du_afd/' + gc  + '/' + from  + '/' + to, 
        windowName:'report DU Divisi / Bagian : '+ gc,
        width:800 }); */ 
    
    function index()
    {
        $view = "rpt_du";
        $data = array();
        $data['judul_header'] = "Daftar Upah Per Kemandoran";
        $data['js'] = $this->js_du();
            
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['GANG_CODE'] = $this->dropdownlist_gc();
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS' || $data['user_level'] == 'ADMSITE' || $data['user_level'] == 'KABUN'){
                show($view, $data);
            } 
        } else {
            redirect('login');
        }    
    }      
    
    function du_afd()
    {
        $view = "rpt_du_afd";
        $data = array();
        $data['judul_header'] = "Daftar Upah Per Divisi";
        $data['js'] = $this->js_du_afd();
            
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['DIVISION'] = $this->dropdownlist_division();
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
        
        if ($data['login_id'] == TRUE){
            if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
                show($view, $data);
            } 
        } else {
            redirect('login');
        }    
    }
    
    function du_bulanan()
    {
        $view = "rpt_du_bulanan";
        $data = array();
        $data['judul_header'] = "Daftar Upah Karyawan Bulanan";
        $data['js'] = $this->js_du_bulanan();
            
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['dept'] = $this->dropdownlist_dept();;
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
        
        if ($data['login_id'] == TRUE){
            if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
                show($view, $data);
            } 
        } else {
            redirect('login');
        }    
    }
	
    function dropdownlist_gc()
    {
    
        $string = "<select  name='GANG_CODE' class='select'  id='GANG_CODE' >";
        $string .= "<option value=''> -- choose -- </option><option value='ALL'> -- SEMUA -- </option>";
        
        $data_afd = $this->model_rpt_du->get_gc($this->session->userdata('DCOMPANY'));
        
        foreach ( $data_afd as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row['GANG_CODE']."\"  selected>".$row['GANG_CODE']." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row['GANG_CODE']."\">".$row['GANG_CODE']." </option>";
            }
        }
        
        $string =$string. "</select>";
        return $string;
    }
    
    function du_act()
    {
        $view = "rpt_du_act";
        $data = array();
        $data['judul_header'] = "Daftar Upah per Aktivitas";
        $data['js'] = $this->js_du();
            
        $data['login_id'] = $this->session->userdata('LOGINID');
        $data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['DIVISION'] = $this->dropdownlist_division();
        
        if ($data['login_id'] == TRUE){
            if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
                show($view, $data);
            } 
        } else {
            redirect('login');
        }    
    }    
        
    function dropdownlist_division()
    {
    
        $string = "<select  name='division' class='select'  id='division' style='width:200px'>";
        $string .= "<option value=''> -- Pilih -- </option><option value='ALL'> -- Semua -- </option>";
        
        $data_afd = $this->model_rpt_du->get_division($this->session->userdata('DCOMPANY'));
        
        foreach ( $data_afd as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row['DIVISION_CODE']."\"  selected>".$row['DIVISION_CODE']." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row['DIVISION_CODE']."\">".$row['DIVISION_CODE']." </option>";
            }
        }
        
        $string =$string. "</select>";
        return $string;
    }
    
    function gen_rpt_du($gc, $from, $to){
            
    if ($this->session->userdata('logged_in') != TRUE)
    {
       redirect('login');
    }
    
    $company = $this->session->userdata('DCOMPANY');
    $pdf = new pdf_usage();        
    $pdf->Open();
    $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(5, 13,20);
    $pdf->AddPage('L', 'LEGAL');
    $pdf->AliasNbPages(); 
        
    $pdf->SetStyle("s1","arial","",9,"");
    $pdf->SetStyle("s2","arial","",8,"");
    $pdf->SetStyle("s3","arial","",10,"");
    
    $data_gc = $this->model_rpt_du->header_du($gc,$company);
    $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
    $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
    $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
    $ttl_potongan = 0;  $ttl_upah = 0;
    
    $gangcode1 = ""; $gangcode2 = "";
    $gangcode3 = ""; $gangcode4 = "";    
    
    foreach ($data_gc as $row)
    {
        $gangcode1 .= $gc;
        $gangcode2 .= $row['DESCRIPTION'];
        $gangcode3 .= $row['MANDORE_CODE'];
        $gangcode4 .= $row['NAMA'];
    }
    //default text color
    $pdf->SetTextColor(118, 0, 3);
    //$pdf->SetX(60);
    //$pdf->Ln(1);
    $pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
    $pdf->MultiCellTag(200, 5, "<s3>DAFTAR UPAH</s3>", 0);
    //$periode = substr($to,4,2);
	
	$bulan = substr($to,4,2);
    if($bulan=="01"){ $bulan = "Januari"; } 
    else if($bulan=="02"){ $bulan = "Februari"; } 
    else if($bulan=="03"){ $bulan = "Maret"; } 
    else if($bulan=="04"){ $bulan = "April"; } 
    else if($bulan=="05"){ $bulan = "Mei"; } 
    else if($bulan=="06"){ $bulan = "Juni"; } 
    else if($bulan=="07"){ $bulan = "Juli"; } 
    else if($bulan=="08"){ $bulan = "Agustus"; } 
    else if($bulan=="09"){ $bulan = "September"; } 
    else if($bulan=="10"){ $bulan = "Oktober"; } 
    else if($bulan=="11"){ $bulan = "Nopember"; } 
    else if($bulan=="12"){ $bulan = "Desember"; }

    $pdf->MultiCellTag(200, 5, "<s1>PERIODE : ". strtoupper($bulan) ." ". substr($periode,0,4) ." </s1>", 0);
    $pdf->MultiCellTag(200, 5, "<s1>KEMANDORAN : ".strtoupper($gangcode1)." - ". strtoupper($gangcode2) ." </s1>", 0);
    $pdf->MultiCellTag(200, 5, "<s1>MANDOR : ".strtoupper($gangcode3)." - ". strtoupper($gangcode4) ." </s1>", 0);
    $pdf->Ln(2);
    
    
    //load the table default definitions DEFAULT!!!
    require_once(APPPATH . 'libraries/rptPDF_def.inc');
    $columns = 25; //number of Columns
    
    //Initialize the table class
    $pdf->tbInitialize($columns, true, true);
    
    //set the Table Type
    $pdf->tbSetTableType($table_default_table_type);
    $aSimpleHeader = array();
    
    $header = array('No','NIK','Nama','Status','Type  Karyawan','HK Dibayar','','','Nilai (Rp) Dibayar','','','GP', 'Tunj. Jab','Premi / Lembur','Natura','Rapel / THR / Bonus','Astek   (4,54%)','Tunj. Lebih Hari','Gaji Bruto','Pot. Astek   (6,54%)','Pot.Lain','PPh 21','    Pot. Kurang Hari','TTL Pot.','Upah Diterima');
    $header2 = array('','','','','','HKE','HKNE','TTL','HKE','HKNE','TTL','','','','','','','','','','','','','','','');
    //Table Header
    for($i=0; $i < $columns+1; $i++) {
        $aSimpleHeader[$i] = $table_default_header_type;
        $aSimpleHeader[$i]['TEXT'] = $header[$i];
        $aSimpleHeader[0]['WIDTH'] = 5.5;
        $aSimpleHeader[1]['WIDTH'] = 14;
        $aSimpleHeader[2]['WIDTH'] = 34;
        $aSimpleHeader[3]['WIDTH'] = 10;
        $aSimpleHeader[4]['WIDTH'] = 14;
        $aSimpleHeader[5]['WIDTH'] = 30;
        $aSimpleHeader[6]['WIDTH'] = 0;
        $aSimpleHeader[7]['WIDTH'] = 0;
        $aSimpleHeader[8]['WIDTH'] = 15;
        $aSimpleHeader[10]['WIDTH'] = 15;
        $aSimpleHeader[18]['WIDTH'] = 15;
        $aSimpleHeader[21]['WIDTH'] = 12;
        $aSimpleHeader[23]['WIDTH'] = 15;
        $aSimpleHeader[24]['WIDTH'] = 15;
        $aSimpleHeader[$i]['WIDTH'] = 13.3;
        $aSimpleHeader2[$i]['LN_SIZE'] = 2;
        
        if ($i == '5') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
        else if ($i == '8') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
        else if ($i >= '0' && $i < '6'){ $aSimpleHeader[$i]['ROWSPAN'] = 2; } 
        else if ($i >= '11' && $i < '26'){ $aSimpleHeader[$i]['ROWSPAN'] = 2; } 
        
        $aSimpleHeader2[$i] = $table_default_header_type;
        $aSimpleHeader2[$i]['TEXT'] = $header2[$i];
        $aSimpleHeader2[0]['WIDTH'] = 5.5;
        $aSimpleHeader2[1]['WIDTH'] = 14;
        $aSimpleHeader2[2]['WIDTH'] = 34;
        $aSimpleHeader2[3]['WIDTH'] = 10;
        $aSimpleHeader2[4]['WIDTH'] = 14;
        $aSimpleHeader2[8]['WIDTH'] = 15;
        $aSimpleHeader2[10]['WIDTH'] = 15;
        /* hke kolom */
        //$aSimpleHeader2[2]['WIDTH'] = 18;
        $aSimpleHeader2[5]['WIDTH'] = 10;
        $aSimpleHeader2[6]['WIDTH'] = 10;
        $aSimpleHeader2[7]['WIDTH'] = 10;
        $aSimpleHeader2[18]['WIDTH'] = 15;
        $aSimpleHeader2[21]['WIDTH'] = 12;
        $aSimpleHeader2[23]['WIDTH'] = 15;
        $aSimpleHeader2[24]['WIDTH'] = 15;
        
        $aSimpleHeader2[$i]['WIDTH'] = 13.3;
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
            
    $data_row = $this->model_rpt_du->generate_du2($gc, $from, $to, $company);
    $i = 1;    
    foreach ($data_row as $row)
    {
        $data = Array();
            $data[0]['TEXT'] = $i;
            $data[1]['TEXT'] = $row['EMPLOYEE_CODE'];
            $data[2]['TEXT'] = $row['NAMA'];
            $data[2]['T_ALIGN'] = 'L';
            $data[3]['TEXT'] = $row['FAMILY_STATUS'];
            $data[4]['TEXT'] = $row['TYPE_KARYAWAN'];        
            $data[5]['TEXT'] = $row['HK'];
            $data[6]['TEXT'] = $row['HKNE'];
            $data[7]['TEXT'] = $row['TTL'];
            $data[8]['TEXT'] = number_format($row['HKE_BYR']);
            $data[8]['T_ALIGN'] = 'R';
            $data[9]['TEXT'] = number_format($row['HKNE_BYR']);
            $data[9]['T_ALIGN'] = 'R';
            $data[10]['TEXT'] = number_format($row['TTL_BYR']);
            $data[10]['T_ALIGN'] = 'R';
            
            if ($row['TYPE_KARYAWAN'] == "BHL"){
                $gp = $row['TTL_BYR'];
            } else if ($row['TYPE_KARYAWAN'] == "KDMP"){
                if($this->session->userdata('DCOMPANY') == 'MAG'){
                	$gp = $row['GP'];
				} else {
					$gp = $row['TTL_BYR'];
				}
            } else {
                $gp = $row['GP'];
            }
            $data[11]['TEXT'] = number_format($gp);
            $data[11]['T_ALIGN'] = 'R';
            
            $data[12]['TEXT'] = number_format($row['TUNJANGAN_JABATAN']);
            $data[12]['T_ALIGN'] = 'R';
            $data[13]['TEXT'] = number_format($row['PREMI_LEMBUR']);
            $data[13]['T_ALIGN'] = 'R';
            $data[14]['TEXT'] = number_format($row['NATURA']);
            $data[14]['T_ALIGN'] = 'R';
	     $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }
            $rtb = $row['RAPEL'] + $thr + $row['BONUS'];
            $data[15]['TEXT'] = number_format($rtb);
            $data[15]['T_ALIGN'] = 'R';
            $data[16]['TEXT'] = number_format($row['ASTEK']);
            $data[16]['T_ALIGN'] = 'R';            
            if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
                $tunj_lhari = $row['TTL_BYR'] - $gp;
                $data[17]['TEXT'] =    number_format($tunj_lhari);
                $data[17]['T_ALIGN'] = 'R';
            } else if ($row['TYPE_KARYAWAN'] == "KDMP" && $gp < $row['TTL_BYR']) {
                $tunj_lhari = $row['TTL_BYR'] - $gp;
                $data[17]['TEXT'] =    number_format($tunj_lhari);
                $data[17]['T_ALIGN'] = 'R';
            } else {
                $tunj_lhari = 0;
                $data[17]['TEXT'] = number_format($tunj_lhari);
                $data[17]['T_ALIGN'] = 'R';
            }
            
            $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;
            
            $data[18]['TEXT'] = number_format($gaji_bruto);
            $data[18]['T_ALIGN'] = 'R';
            $data[19]['TEXT'] = number_format($row['POT_ASTEK']);
            $data[19]['T_ALIGN'] = 'R';
            $data[20]['TEXT'] = number_format($row['POTONGAN_LAIN']);
            $data[20]['T_ALIGN'] = 'R';
            $data[21]['TEXT'] = number_format($row['PPH_21']);            
            $data[21]['T_ALIGN'] = 'R';
            if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR'] ){
                $pot_khari = -($row['TTL_BYR'] - $gp);
                $data[22]['TEXT'] = number_format($pot_khari);
                $data[22]['T_ALIGN'] = 'R';
            } else if($row['TYPE_KARYAWAN'] == "KDMP" && $gp > $row['TTL_BYR'] ){
                $pot_khari = -($row['TTL_BYR'] - $gp);
                $data[22]['TEXT'] = number_format($pot_khari);
                $data[22]['T_ALIGN'] = 'R';
            } else {
                $pot_khari = 0;
                $data[22]['TEXT'] = number_format($pot_khari);
                $data[22]['T_ALIGN'] = 'R';
            }
            
            $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
            $data[23]['TEXT'] = number_format($total_potongan);
            $data[23]['T_ALIGN'] = 'R';
            $data[24]['TEXT'] = number_format($gaji_bruto - $total_potongan);
            $data[24]['T_ALIGN'] = 'R';
            
            $ttl_hke = $ttl_hke + $row['HK'];
            $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
            $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
            
            $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
            $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
            $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
            
            $ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
            $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
            $ttl_natura = $ttl_natura + $row['NATURA'];
            $ttl_rtb = $ttl_rtb + $rtb;
            
            $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
            $pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
            $ttl_potongan = $ttl_potongan + $total_potongan;
            $ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
            
            $i++;
            
        $pdf->tbDrawData($data);
    }
            $data[0]['TEXT'] = "Total";
            $data[0]['COLSPAN'] = 5;            
            $data[5]['TEXT'] = $ttl_hke;
            $data[6]['TEXT'] = $ttl_hkne;
            $data[7]['TEXT'] = $ttl_hke_ne;
            $data[8]['TEXT'] = number_format($ttlbyr_hke);
            $data[8]['T_ALIGN'] = 'R';
            $data[9]['TEXT'] = number_format($ttlbyr_hkne);
            $data[9]['T_ALIGN'] = 'R';
            $data[10]['TEXT'] = number_format($ttlbyr_hke_ne);
            $data[10]['T_ALIGN'] = 'R';
            $data[11]['TEXT'] = '-';
            $data[11]['T_ALIGN'] = 'C';
            $data[12]['TEXT'] = number_format($ttl_tunjab);
            $data[12]['T_ALIGN'] = 'R';
            $data[13]['TEXT'] = number_format($ttl_premi);
            $data[13]['T_ALIGN'] = 'R';
            $data[14]['TEXT'] = number_format($ttl_natura);
            $data[14]['T_ALIGN'] = 'R';
            $data[15]['TEXT'] = number_format($ttl_rtb);
            $data[15]['T_ALIGN'] = 'R';
            $data[16]['TEXT'] = '-';
            $data[16]['T_ALIGN'] = 'C';
            $data[16]['COLSPAN'] = '2';
            $data[18]['TEXT'] = number_format($ttl_gaji_bruto);
            $data[18]['T_ALIGN'] = 'R';
            $data[19]['TEXT'] = '-';
            $data[19]['T_ALIGN'] = 'C';
            $data[20]['TEXT'] = number_format($pot_lain);
            $data[20]['T_ALIGN'] = 'R';
            $data[21]['TEXT'] = '-';
            $data[21]['T_ALIGN'] = 'C';
            $data[21]['COLSPAN'] = 2;
            
            $data[23]['TEXT'] = number_format($ttl_potongan);
            $data[23]['T_ALIGN'] = 'R';
            $data[24]['TEXT'] = number_format($ttl_upah);
            $data[24]['T_ALIGN'] = 'R';
    $pdf->tbDrawData($data);
    
    
    //output the table data to the pdf
    $pdf->tbOuputData();
    //draw the Table Border
    $pdf->tbDrawBorder();
    
                        
    $pdf->Ln(15.5);
    
    require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
        
    $pdf->Output();
		
    
    }
    
    function rpt_du_breakdown()
    {
        $nik = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $gc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'); 
        $periode = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $company_name=htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        
        $data_du_breakdown=$this->model_rpt_du->generate_du2_breakdown($nik,$gc,$periode);
		
		$bulan = substr($periode,-2);
    
			if($bulan==01){ $bulan = "Januari"; } 
			else if($bulan==02){ $bulan = "Februari"; } 
			else if($bulan==03){ $bulan = "Maret"; } 
			else if($bulan==04){ $bulan = "April"; } 
			else if($bulan==05){ $bulan = "Mei"; } 
			else if($bulan==06){ $bulan = "Juni"; } 
			else if($bulan==07){ $bulan = "Juli"; } 
			else if($bulan==08){ $bulan = "Agustus"; } 
			else if($bulan==09){ $bulan = "September"; } 
			else if($bulan==10){ $bulan = "Oktober"; } 
			else if($bulan==11){ $bulan = "Nopember"; } 
			else if($bulan==12){ $bulan = "Desember"; }
			
        $tabel = "";
        $tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
                    .tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
                    .tbl_td { font-size: 12px;color:#678197; padding:1px; padding-left:5px; padding-right:5px; border-bottom:1px solid; border-right:1px solid }
                    .tbl_2 { font-size: 12px;color:#678197;}
                    .content { font-size: 12px;color:#678197; }
                    </style>";
        $tabel .= "<table class='tbl_2' border='0'><tr><td colspan='3'>PT. ".$company_name."</td>
                    </tr><tr><td colspan='3'>DAFTAR UPAH PERKEMANDORAN DETAIL : </td>
                    </tr><tr><td colspan='3'>GANG CODE : <strong>".$gc."</strong></td> 
                    </tr><tr><td colspan='3'>NIK : <strong>".$nik."</strong></td> 
                    </tr><tr><td colspan='3'>PERIODE : <strong>". strtoupper($bulan) ." ". substr($periode,0,4) ."</strong></td></tr></table>";
        $tabel .= "<table width='100%' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2'>No</th>
                        
                        <th class='tbl_th' rowspan='2' width='5%'>TGL TRANSAKSI</th>
						<th class='tbl_th' rowspan='2'>NAMA</th>
                        <th class='tbl_th' colspan='2'>AKTIVITAS</th>
                        <th class='tbl_th' colspan='2'>LOKASI</th>
                        <th class='tbl_th' rowspan='2'>HKE</th>
                        <th class='tbl_th' rowspan='2'>HKNE</th>
                        <th class='tbl_th' rowspan='2'>HKE BAYAR (Rp)</th>
                        <th class='tbl_th' rowspan='2'>HKNE BAYAR (Rp)</th>
                        <th class='tbl_th' rowspan='2'>LEMBUR JAM (Rp)</th>
                        <th class='tbl_th' rowspan='2'>LEMBUR (Rp)</th>
                        <th class='tbl_th' rowspan='2'>PREMI (Rp)</th>
                        <th class='tbl_th' rowspan='2'>PENALTI (Rp)</th>
						<th class='tbl_th' rowspan='2'>TGL INPUT</th>
                    </tr>";
        $tabel .= "<tr><th class='tbl_th'>KODE</th>
                        <th class='tbl_th'>DESKRIPSI</th>
                        <th class='tbl_th'>TIPE</th>
                        <th class='tbl_th'>KODE</th>
                    </tr>";
        $total_hke_jumlah=0 ;
        $total_hkne_jumlah=0 ;
        $total_hke_byr=0 ;
        $total_hkne_byr=0;
        $total_lembur_jam=0;
        $total_lembur_rupiah=0;
        $total_premi=0;
        $total_penalti=0;
        
        $i=1;
        foreach ($data_du_breakdown as $row)
        { 
           $hke_jumlah=htmlentities((double)$row['HKE_JUMLAH'],ENT_QUOTES,'UTF-8') ;
           $hkne_jumlah=htmlentities((double)$row['HKNE_JUMLAH'],ENT_QUOTES,'UTF-8') ;
           $hke_byr=htmlentities((double)$row['HKE_BYR'],ENT_QUOTES,'UTF-8') ;
           $hkne_byr=htmlentities((double)$row['HKNE_BYR'],ENT_QUOTES,'UTF-8');
           $lembur_jam=htmlentities((double)$row['LEMBUR_JAM'],ENT_QUOTES,'UTF-8');
           $lembur_rupiah=htmlentities((double)$row['LEMBUR_RUPIAH'],ENT_QUOTES,'UTF-8');
           $premi=(double)$row['PREMI']; //htmlentities($row['PREMI'],ENT_QUOTES,'UTF-8');
           $penalti=htmlentities((double)$row['PENALTI'],ENT_QUOTES,'UTF-8');
           
           $total_hke_jumlah += $hke_jumlah ;
           $total_hkne_jumlah += $hkne_jumlah;
           $total_hke_byr += $hke_byr;
           $total_hkne_byr += $hkne_byr;
           $total_lembur_jam += $lembur_jam;
           $total_lembur_rupiah += $lembur_rupiah;
           $total_premi += $premi;
           $total_penalti += $penalti;
           
           $bgcolor='';
           $nb_vh='';
           if (strtoupper(trim($row['LOCATION_TYPE_CODE'])) =='VH' && ($row['VH']==NULL || empty($row['VH'])))
           {
                   $bgcolor=" bgcolor='#00FF66' ";
                $nb_vh="<em>*Kolom yang berwarna hijau, adalah aktivitas VH yang belum memiliki Buku Kendaraan</em>";
           }
           
           $tabel .= "<tr ".$bgcolor.">
                        <td class='tbl_td' align = 'center' width='2%'>".htmlentities($i,ENT_QUOTES,'UTF-8')."</td>
						<td class='tbl_td' align = 'center'>".htmlentities($row['LHM_DATE'],ENT_QUOTES,'UTF-8')."</td>
                        <td class='tbl_td' align = 'left'>".htmlentities($row['NAMA'],ENT_QUOTES,'UTF-8')."</td>
                        <td class='tbl_td' align = 'center'>".htmlentities($row['ACTIVITY_CODE'],ENT_QUOTES,'UTF-8')."</td>
						<td class='tbl_td' align = 'center'>".htmlentities($row['AKTIVITAS'],ENT_QUOTES,'UTF-8')."</td>
						<td class='tbl_td' align = 'center'>".htmlentities($row['LOCATION_TYPE_CODE'],ENT_QUOTES,'UTF-8')."</td>
                        <td class='tbl_td' align = 'center'>".htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8')."</td>
                        <td class='tbl_td' align = 'right'>".number_format($hke_jumlah,2,',','.')."&nbsp;&nbsp;</td>
                    ";
                    
        $tabel .= "<td class='tbl_td' align = 'right'>".number_format($hkne_jumlah,2,',','.')."&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'>".number_format($hke_byr,2,',','.')."&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'>".number_format($hkne_byr,2,',','.')."&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'>".number_format($lembur_jam,2,',','.')."&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'>".number_format($lembur_rupiah,2,',','.')."&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'>".number_format($premi,2,',','.')."&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'>".number_format($penalti,2,',','.')."&nbsp;&nbsp;</td>";
			$tabel .= "<td class='tbl_td' align = 'center'>".htmlentities($row['INPUT_DATE'],ENT_QUOTES,'UTF-8')."</td>";
            $tabel .= "</tr>";
            
        $i++;
        }
        
                    
        $tabel .= "<tr>
                    <td class='tbl_td' colspan='7' align = 'center'>TOTAL</td>
                    <td class='tbl_td' align = 'right' colspan=''><strong>".number_format($total_hke_jumlah,2,',','.')."</strong>&nbsp;&nbsp;</td>
                    <td class='tbl_td' align = 'right'><strong>".number_format($total_hkne_jumlah,2,',','.')."</strong>&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_hke_byr,2,',','.')."</strong>&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_hkne_byr,2,',','.')."</strong>&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_lembur_jam,2,',','.')."</strong>&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_lembur_rupiah,2,',','.')."</strong>&nbsp;&nbsp;</td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_premi,2,',','.')."</strong>&nbsp;&nbsp;</td>"; 
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_penalti,2,',','.')."</strong>&nbsp;&nbsp;</td>"; 
			$tabel .= "<td class='tbl_td' align = 'center'><strong>-</strong>&nbsp;&nbsp;</td>"; 
            $tabel .= "</tr>";            
        $tabel .= "<tr>".$nb_vh."</tr>";
        $tabel .= "</table>";
        echo $tabel;         
    }
            
    function preview(){
        $gc = $this->uri->segment(3);
        $from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
        $company = $this->session->userdata('DCOMPANY');
        $data_row = $this->model_rpt_du->generate_du2($gc,$from, $to, $company);
        
        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
        $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
        $ttl_potongan = 0; $ttl_upah = 0; $gp = 0; $tunj_lhari = 0; $pot_khari = 0;
        $ttl_gp = 0; $ttl_astek = 0;  $ttl_lhari = 0; $ttl_potongan = 0; $ttl_pastek = 0;
        $ttl_plhari = 0; $ttl_pph21 = 0;    
        
        $i = 1;
        $DU = "";
        $template_path = base_url().$this->config->item('template_path');
        
        $DU .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
        $DU .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_2 { font-size: 12px;color:#678197;} ";
        $DU .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
        
        if ($gc == "ALL") {
            $DU .= "<span class='content2'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
            $DU .= "<span class='content2'>DAFTAR UPAH</span><br />";
            $DU .= "<span class='content2'>KEMANDORAN    :  ".$gc ."</span><br />";
        } else {
            $data_gc = $this->model_rpt_du->header_du($gc,$company);
            foreach ($data_gc as $row)
            {
                $DU .= "<span class='content2'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
                $DU .= "<span class='content2'>DAFTAR UPAH</span><br />";
                $DU .= "<span class='content2'>KODE KEMANDORAN     :  ".$gc ."</span><br />";
                $DU .= "<span class='content2'>NAMA KEMANDORAN    :  ".strtoupper($row['DESCRIPTION']) ."</span><br />";
                $DU .= "<span class='content2'>NIK MANDOR                :  ".$row['MANDORE_CODE'] ."</span><br />";
                $DU .= "<span class='content2'>NAMA MANDOR             :  ".$row['NAMA'] ."</span><br />";
            }
        }
        if( $gc != "ALL") {
        $DU .= "<div class='content2' style='float:right;margin-right:1%;'><a href='".base_url()."index.php/rpt_tandaterima/gen_tt/".$gc."/".$from."/-/".$to."' target='_blank'>CETAK TANDA TERIMA GAJI</a>
        &nbsp;<a href='".base_url()."index.php/rpt_slip_gaji/create_slipgaji/".$gc."/".$from."/-/".$to."' target='_blank'>CETAK SLIP GAJI</a></div><br/>";
        }
        $DU .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
        $DU .= "<tr><td  align='center' rowspan='2' class='tbl_th'> No. </td>";
        if ($gc == "ALL") {
            $DU .= "<td rowspan='2' align='center' class='tbl_th'>Kemandoran</td>";
        }
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>NIK</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Nama</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Status</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Type Karyawan</td>";
        $DU .= "<td colspan='3' align='center' class='tbl_th'>HK dibayar</td>";
        $DU .= "<td align='center' colspan='3' class='tbl_th'>Nilai (Rp) dibayar</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>GP</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Jabatan / Tunj. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Premi / Lembur</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Natura</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Rapel / Thr / Bonus</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Lebih Hari</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Gaji Bruto</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Kurang Hari</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>PPh 21</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Total Potongan</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Upah Diterima</td></tr>";
        $DU .= "<tr><td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td>";
        $DU .= "<td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td></tr>";
        $style = "";
        $url = base_url().'index.php/rpt_du/'; 
        foreach ($data_row as $row)
        { 
            if($row['GANG_CODE'] == "") {
                $style = 'style="background-color:#CF0"';
            } else { $style = ""; }
            
            $DU .= '<tr id="tr_1">';
              $DU .= '<td class="tbl_td" '.$style.'><center>'.$i.'</center></td>';
                                
            if ($gc == "ALL") {    
                $DU .= '<td width="50" class="tbl_td" '.$style.' ><center>'.$row['GANG_CODE'].'</center></td>';
            }
            $DU .= "<td width='50' class='tbl_td' ".$style."><strong><a href='".$url."rpt_du_breakdown/".$row['EMPLOYEE_CODE']."/".$gc."/".substr($to,0,6).
            "' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><center>".$row['EMPLOYEE_CODE']."</center></a></strong></td>";
            $DU .= '<td width="150" class="tbl_td" '.$style.' align="left">&nbsp;'.$row['NAMA'].'</td>';
            $DU .= '<td width="75" class="tbl_td" '.$style.'><center>'.$row['FAMILY_STATUS'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.'><center>'.$row['TYPE_KARYAWAN'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.$row['HK'].' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.$row['HKNE'].'  &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.$row['TTL'].'</strong> &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['HKE_BYR']) .' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['HKNE_BYR']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($row['TTL_BYR']).'</strong>&nbsp;</td>';
            
            if ($row['TYPE_KARYAWAN'] == "BHL"){
                $gp = $row['TTL_BYR'];
            } else if ($row['TYPE_KARYAWAN'] == "KDMP"){
				if($this->session->userdata('DCOMPANY') == 'MAG'){
                	$gp = $row['GP'];
				} else {
					$gp = $row['TTL_BYR'];
				}
            } else {
                $gp = $row['GP'];
            }
            
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($gp).'</strong>&nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['TUNJANGAN_JABATAN']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['PREMI_LEMBUR']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['NATURA']).' &nbsp;</td>';
		 $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }

            $rtb = $row['RAPEL'] + $thr + $row['BONUS'];
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($rtb).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['ASTEK']).' &nbsp;</td>';
            if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
                    $tunj_lhari = $row['TTL_BYR'] - $gp;
                    if($tunj_lhari < 100) { $tunj_lhari = 0; } 
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            } else if ($row['TYPE_KARYAWAN'] == "KDMP" && $gp < $row['TTL_BYR']) {
                    $tunj_lhari = $row['TTL_BYR'] - $gp;
                    if($tunj_lhari < 100) { $tunj_lhari = 0; } 
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            } else {
                $tunj_lhari = 0;
                $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            }
            
        $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($gaji_bruto).'</strong>&nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['POT_ASTEK']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['POTONGAN_LAIN']).' &nbsp;</td>';
            if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR']){
                    $pot_khari = -($row['TTL_BYR'] - $gp);
                    if($pot_khari < 100) { $pot_khari = 0; }
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($pot_khari).' &nbsp;</td>';
            } else if($row['TYPE_KARYAWAN'] == "KDMP" && $gp > $row['TTL_BYR']){
                    $pot_khari = -($row['TTL_BYR'] - $gp);
                    if($pot_khari < 100) { $pot_khari = 0; }
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($pot_khari).' &nbsp;</td>';
            } else {
                $pot_khari = 0;
                $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"> 0 &nbsp;</td>';
            }    
             
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['PPH_21']).' &nbsp;</td>';
            $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($total_potongan).' &nbsp;</strong></td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($gaji_bruto - $total_potongan).' &nbsp;</strong></td>';
          $DU .= '</tr>';    
        
        $ttl_hke = $ttl_hke + $row['HK'];
        $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
        $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
        
        $ttl_gp = $ttl_gp + $gp; 
        $ttl_astek = $ttl_astek + $row['ASTEK'];  
        $ttl_lhari = $ttl_lhari + $tunj_lhari ; 
        $ttl_pastek = $ttl_pastek + $row['POT_ASTEK'];
        $ttl_plhari = $ttl_plhari + $pot_khari; 
        $ttl_pph21 = $ttl_pph21 + $row['PPH_21'];    
        
        $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
        $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
        $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
        
        $ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
        $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
        $ttl_natura = $ttl_natura + $row['NATURA'];
        $ttl_rtb = $ttl_rtb + $rtb;
        
        $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
        $pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
        $ttl_potongan = $ttl_potongan + $total_potongan;
        $ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
        
            $i++;
        }
        if ($gc == "ALL") {
            $DU .= "<tr><td class='tbl_td' align='center' colspan='6'><strong>Total</strong></td>";
        } else {
            $DU .= "<tr><td class='tbl_td' align='center' colspan='5'><strong>Total</strong></td>";
        }
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hkne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke_ne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hkne)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke_ne)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_gp)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_tunjab)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_premi)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_natura)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_rtb)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_astek)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_lhari)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_gaji_bruto)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_pastek)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($pot_lain)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_plhari)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_pph21)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_potongan)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_upah)." &nbsp;</strong></td></tr>";
        $DU .= "</table>"; 
        
        echo $DU;
    
    }
    
    function preview_afd(){
        //$periode = $this->uri->segment(3);
        $afd = $this->uri->segment(3);
        $from = $this->uri->segment(4);
        $to = $this->uri->segment(5);
        $company = $this->session->userdata('DCOMPANY');
        $data_row = $this->model_rpt_du->get_du_perafd($company,$from,$to,$afd);
        
        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
        $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
        $ttl_potongan = 0; $ttl_upah = 0; $gp = 0; $tunj_lhari = 0; $pot_khari = 0;
        
        $i = 1;
        $DU = "";
        
        $DU .= "<span style='font-size: 11px;color:#678197'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
        $DU .= "<span style='font-size: 11px;color:#678197'>DAFTAR UPAH</span><br />";
        $DU .= "<span style='font-size: 11px;color:#678197'>DIVISI / BAGIAN     :  ".$afd ."</span><br />";
        
        $DU .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
        $DU .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_2 { font-size: 12px;color:#678197;} ";
        $DU .= ".content { font-size: 12px;color:#678197; }</style>";
        if( $afd != "ALL") {
        $DU .= "<div class='content' style='float:right;margin-right:1%;'><a href='".base_url()."index.php/rpt_tandaterima/gen_tt/".$afd."/".$from."/afd/".$to."' target='_blank'>CETAK TANDA TERIMA GAJI</a>
        &nbsp;<a href='".base_url()."index.php/rpt_slip_gaji/create_slipgaji/".$afd."/".$from."/afd/".$to."' target='_blank'>CETAK SLIP GAJI</a></div><br/>";
        }
        $DU .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
        $DU .= "<tr><td  align='center' rowspan='2' class='tbl_th'> No. </td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>NIK</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Nama</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Status</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Type Karyawan</td>";
        $DU .= "<td colspan='3' align='center' class='tbl_th'>HK dibayar</td>";
        $DU .= "<td align='center' colspan='3' class='tbl_th'>Nilai (Rp) dibayar</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>GP</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Jabatan / Tunj. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Premi / Lembur</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Natura</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Rapel / Thr / Bonus</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Lebih Hari</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Gaji Bruto</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Kurang Hari</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>PPh 21</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Total Potongan</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Upah Diterima</td></tr>";
        $DU .= "<tr><td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td>";
        $DU .= "<td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td></tr>";

        foreach ($data_row as $row)
        {
            $DU .= '<tr id="tr_1">';
              $DU .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
            $DU .= '<td width="50" class="tbl_td" ><center>'.$row['EMPLOYEE_CODE'].'</center></td>';
            $DU .= '<td width="150" class="tbl_td" align="left">&nbsp;'.$row['NAMA'].'</td>';
            $DU .= '<td width="75" class="tbl_td"><center>'.$row['FAMILY_STATUS'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td"><center>'.$row['TYPE_KARYAWAN'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.$row['HK'].' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.$row['HKNE'].'  &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.$row['TTL'].'</strong> &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['HKE_BYR']) .' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['HKNE_BYR']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($row['TTL_BYR']).'</strong>&nbsp;</td>';
            
            if ($row['TYPE_KARYAWAN'] == "BHL"){
                $gp = $row['TTL_BYR'];
            } else if ($row['TYPE_KARYAWAN'] == "KDMP"){
                if($this->session->userdata('DCOMPANY') == 'MAG'){
                	$gp = $row['GP'];
				} else {
					$gp = $row['TTL_BYR'];
				}
            } else {
                $gp = $row['GP'];
            }
            
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($gp).'</strong>&nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['TUNJANGAN_JABATAN']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['PREMI_LEMBUR']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['NATURA']).' &nbsp;</td>';
		 $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }

            $rtb = $row['RAPEL'] + $thr + $row['BONUS'];
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($rtb).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['ASTEK']).' &nbsp;</td>';
            if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
                    $tunj_lhari = $row['TTL_BYR'] - $gp;
                    if($tunj_lhari < 100) { $tunj_lhari = 0; } 
                    $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            } else if ($row['TYPE_KARYAWAN'] == "KDMP" && $gp < $row['TTL_BYR']) {
                    $tunj_lhari = $row['TTL_BYR'] - $gp;
                    if($tunj_lhari < 100) { $tunj_lhari = 0; } 
                    $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            } else {
                $tunj_lhari = 0;
                $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            }
            
        $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($gaji_bruto).'</strong>&nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['POT_ASTEK']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['POTONGAN_LAIN']).' &nbsp;</td>';
            if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR']){
                    $pot_khari = -($row['TTL_BYR'] - $gp);
                    if($pot_khari < 100) { $pot_khari = 0; }
                    $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($pot_khari).' &nbsp;</td>';
            } else if($row['TYPE_KARYAWAN'] == "KDMP" && $gp > $row['TTL_BYR']){
                    $pot_khari = -($row['TTL_BYR'] - $gp);
                    if($pot_khari < 100) { $pot_khari = 0; }
                    $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($pot_khari).' &nbsp;</td>';
            } else {
                $pot_khari = 0;
                $DU .= '<td width="78" class="tbl_td" align="right"> 0 &nbsp;</td>';
            }    
             
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['PPH_21']).' &nbsp;</td>';
            $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($total_potongan).' &nbsp;</strong></td>';
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($gaji_bruto - $total_potongan).' &nbsp;</strong></td>';
          $DU .= '</tr>';    
        
        $ttl_hke = $ttl_hke + $row['HK'];
        $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
        $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
        
        $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
        $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
        $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
        
        $ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
        $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
        $ttl_natura = $ttl_natura + $row['NATURA'];
        $ttl_rtb = $ttl_rtb + $rtb;
        
        $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
        $pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
        $ttl_potongan = $ttl_potongan + $total_potongan;
        $ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
        
            $i++;
        }
        
        $DU .= "<tr><td class='tbl_td' align='center' colspan='5'><strong>Total</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hkne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke_ne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hkne)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke_ne)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_tunjab)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_premi)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_natura)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_rtb)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        $DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_gaji_bruto)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($pot_lain)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        $DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_potongan)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_upah)." &nbsp;</strong></td></tr>";
        $DU .= "</table>"; 
        
        echo $DU;
    
    }
    
            
    function generate () {
        //$periode = $this->uri->segment(3);
        $gc = $this->uri->segment(3);
        $from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		
		//echo $gc.$from.$to;
        $data= array();
        if(isset($gc) && isset($from) && isset($to)){
            $this->gen_rpt_du($gc, $from, $to);
        }               
    }
    
    function du_xls(){
        $typerpt = $this->uri->segment(6);
        if($typerpt != '') {
            $gc = $this->uri->segment(3);
            $from = $this->uri->segment(4);
            $to = $this->uri->segment(5);
            $periode = $from."_".$to;
        } else {
            $gc = $this->uri->segment(3);
            $from = $this->uri->segment(4);
            $to = $this->uri->segment(5);
            $periode = $from."_".$to;
        }
        
        $company = $this->session->userdata('DCOMPANY');
        
        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0;
        $ttl_premi = 0; $ttl_natura = 0; $ttl_rtb = 0;
        $ttl_gaji_bruto = 0; $pot_lain = 0; $ttl_potongan = 0; $ttl_upah = 0;
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();
                
        if($gc == 'all') {
            $data_row = $this->model_rpt_du->generate_du2($gc, $from, $to, $company);            
            $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
            $judul .= "Daftar Upah ".$periode."\n";
        } else if($typerpt != '') { 
            $data_row = $this->model_rpt_du->get_du_perafd($company,$from,$to,$gc);
                $judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t";
                $judul .= " \n";
                $judul .= "DAFTAR UPAH \t";
                $judul .= " \n";
                $judul .= "DIVISI / BAGIAN \t";
                $judul .= strtoupper($gc)." \n";
        } else {    
            $data_row = $this->model_rpt_du->generate_du2($gc,$from, $to,$company);
            $data_gc = $this->model_rpt_du->header_du($gc,$company);        
            foreach ($data_gc as $row)
            {
                $judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t";
                $judul .= " \n";
                $judul .= "DAFTAR UPAH \t";
                $judul .= " \n";
                $judul .= "KODE KEMANDORAN \t";
                $judul .= $gc." \n";
                $judul .= "NAMA KEMANDORAN \t";
                $judul .= strtoupper($row['DESCRIPTION'])."\n";
                $judul .= "NIK MANDOR \t";
                $judul .= $row['MANDORE_CODE'] ."\n";
                $judul .= "NAMA MANDOR  \t";
                $judul .= $row['NAMA'] ."\n";
            }    
        }
        
        $array = array();
        //baris 1
        $headers .= "No \t";
        if($gc == 'ALL') {
            $headers .= "Kemandoran \t";
        }
        $headers .= "NIK \t";
        $headers .= "Nama \t";
        $headers .= "Status \t";
        $headers .= "Type Karyawan \t";    
        $headers .= "Division \t";
        $headers .= "HKE \t";
        $headers .= "HKNE \t";
        $headers .= "Total \t";
        $headers .= "HKE (Rp) dibayar \t";
        $headers .= "HKNE (Rp) dibayar \t";
        $headers .= "Total (Rp) dibayar \t";
        $headers .= "GP \t";
        $headers .= "Tunj. Jabatan \t";
        $headers .= "Premi / Lembur \t";
        $headers .= "Natura \t";
        $headers .= "Rapel / Thr / Bonus \t";
        $headers .= "Astek \t";
        $headers .= "Tunj. Lebih Hari \t";
        $headers .= "Gaji Bruto \t";
        $headers .= "Pot. Astek \t";
        $headers .= "Pot. Lain \t";
        $headers .= "Pot. Kurang Hari \t";
        $headers .= "PPh 21 \t";
        $headers .= "Total Potongan \t";
        $headers .= "Upah Diterima \n";
        
        $no = 1;
        
        foreach ($data_row as $row)
        {
                $line = '';
                    $line .= str_replace('"', '""',$no)."\t";
                    if($gc == 'ALL') {
                        $line .= str_replace('"', '""',$row['GANG_CODE'])."\t";
                    }
                    $line .= str_replace('"', '""',trim($row['EMPLOYEE_CODE']))."\t";
                    $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
                    $line .= str_replace('"', '""',trim($row['FAMILY_STATUS']))."\t";
                    $line .= str_replace('"', '""',trim($row['TYPE_KARYAWAN']))."\t";
                    $line .= str_replace('"', '""',trim($row['DIVISION_CODE']))."\t";
                    $line .= str_replace('"', '""',trim($row['HK']))."\t";
                    $line .= str_replace('"', '""',trim($row['HKNE']))."\t";
                    $line .= str_replace('"', '""',trim($row['TTL']))."\t";
                    $line .= str_replace('"', '""',trim($row['HKE_BYR']))."\t";
                    $line .= str_replace('"', '""',trim($row['HKNE_BYR']))."\t";
                    $line .= str_replace('"', '""',trim($row['TTL_BYR']))."\t";
                    if (trim(strtoupper($row['TYPE_KARYAWAN'])) == "BHL"){
                        $gp = trim($row['TTL_BYR']);
                    }  else if ($row['TYPE_KARYAWAN'] == "KDMP"){
                		if($this->session->userdata('DCOMPANY') == 'MAG'){
							$gp = $row['GP'];
						} else {
							$gp = $row['TTL_BYR'];
						}
            		} else {
                        $gp = trim($row['GP']);
                    }
                    $line .= str_replace('"', '""',$gp)."\t";
                    $line .= str_replace('"', '""',trim($row['TUNJANGAN_JABATAN']))."\t";
    $line .= str_replace('"', '""',trim($row['PREMI_LEMBUR']))."\t";
                    $line .= str_replace('"', '""',trim($row['NATURA']))."\t";
			 $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }

                    $rtb = $row['RAPEL'] + $thr + $row['BONUS'];
                    $line .= str_replace('"', '""',trim($rtb))."\t";
                    $line .= str_replace('"', '""',trim($row['ASTEK']))."\t";
                    
                    if (trim(strtoupper($row['TYPE_KARYAWAN'])) == "SKU" && $gp < $row['TTL_BYR']) {
                        $tunj_lhari = $row['TTL_BYR'] - $gp;            
                    } else if (trim(strtoupper($row['TYPE_KARYAWAN'])) == "KDMP" && $gp < $row['TTL_BYR']) {
                        $tunj_lhari = $row['TTL_BYR'] - $gp;            
                    } else {
                        $tunj_lhari = 0;
                    }
                    
                    $line .= str_replace('"', '""', trim($tunj_lhari) )."\t";
                    
                    $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;

                    $line .= str_replace('"', '""',trim($gaji_bruto))."\t";
                    $line .= str_replace('"', '""',trim($row['POT_ASTEK']))."\t";
                    $line .= str_replace('"', '""',trim($row['POTONGAN_LAIN']))."\t";
                    
                    if(trim(strtoupper($row['TYPE_KARYAWAN'])) == "SKU" && $gp > $row['TTL_BYR']){
                        $pot_khari = -($row['TTL_BYR'] - $gp);
                    } else if(trim(strtoupper($row['TYPE_KARYAWAN'])) == "KDMP" && $gp > $row['TTL_BYR']){
                        $pot_khari = -($row['TTL_BYR'] - $gp);
                    } else {
                    	$pot_khari = 0;
            		}
                    $line .= str_replace('"', '""', trim($pot_khari) )."\t";
                    $line .= str_replace('"', '""',trim($row['PPH_21']))."\t";
                $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
                //$total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $row['POT_LBH_HR'] + $row['PPH_21'];
                    $line .= str_replace('"', '""',trim($total_potongan))."\t";
                    $line .= str_replace('"', '""',trim($gaji_bruto) - trim($total_potongan))."\t";
                    
                            $ttl_hke = $ttl_hke + $row['HK'];
                    $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
                    $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
                    
                    $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
                    $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
                    $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
                    
                    $ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
                    $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
                    $ttl_natura = $ttl_natura + $row['NATURA'];
                    $ttl_rtb = $ttl_rtb + $rtb;
                    
                    $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
                    $pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
                    $ttl_potongan = $ttl_potongan + $total_potongan;
                    $ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
                    
            $no++;
        
            $data .= trim($line)."\n";
            
        }        
                    $footer .= " - \t";
                    if($gc == 'ALL') {
                        $footer .= " - \t";
                    }
                    $footer .= " - \t";
                    $footer .= " Total \t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_hke))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_hkne))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_hke_ne))."\t";
                    $footer .= str_replace('"', '""',trim($ttlbyr_hke))."\t";
                    $footer .= str_replace('"', '""',trim($ttlbyr_hkne))."\t";
                    $footer .= str_replace('"', '""',trim($ttlbyr_hke_ne))."\t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_tunjab))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_premi))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_natura))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_rtb))."\t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_gaji_bruto))."\t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($pot_lain))."\t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_potongan))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_upah)    )."\t";
            $data .= trim($footer)."\n";
        
        $data = str_replace("\r","",$data);
                         
        header("Content-type: application/x-msdownload");
           header("Content-Disposition: attachment; filename=DU_".$gc."_".$periode.".xls");
    
        echo "$judul\n$headers\n$data";  
    }
    
    //per afd / divisi
    function gen_rpt_du_afd(){
            
    if ($this->session->userdata('logged_in') != TRUE)
    {
       redirect('login');
    }
    
    $afd = $this->uri->segment(3);
    $from = $this->uri->segment(4);
    $to = $this->uri->segment(5);
    $company = $this->session->userdata('DCOMPANY');
    
    $pdf = new pdf_usage();        
    $pdf->Open();
    $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(5, 13,20);
    $pdf->AddPage('L', 'LEGAL');
    $pdf->AliasNbPages(); 
        
    $pdf->SetStyle("s1","arial","",9,"");
    $pdf->SetStyle("s2","arial","",8,"");
    $pdf->SetStyle("s3","arial","",10,"");
    
    $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
    $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
    $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
    $ttl_potongan = 0;  $ttl_upah = 0;
    
    $pdf->SetTextColor(118, 0, 3);
    //$pdf->SetX(60);
    //$pdf->Ln(1);
    $pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
    $pdf->MultiCellTag(200, 5, "<s3>DAFTAR UPAH</s3>", 0);
    
    $bulan = substr($from,4,2);
    
    if($bulan==01){ $bulan = "Januari"; } 
    else if($bulan==02){ $bulan = "Februari"; } 
    else if($bulan==03){ $bulan = "Maret"; } 
    else if($bulan==04){ $bulan = "April"; } 
    else if($bulan==05){ $bulan = "Mei"; } 
    else if($bulan==06){ $bulan = "Juni"; } 
    else if($bulan==07){ $bulan = "Juli"; } 
    else if($bulan==08){ $bulan = "Agustus"; } 
    else if($bulan==09){ $bulan = "September"; } 
    else if($bulan==10){ $bulan = "Oktober"; } 
    else if($bulan==11){ $bulan = "Nopember"; } 
    else if($bulan==12){ $bulan = "Desember"; }

    $pdf->MultiCellTag(200, 5, "<s1>PERIODE : ". strtoupper($bulan) ." ". substr($from,0,4) ." </s1>", 0);
    $pdf->MultiCellTag(200, 5, "<s1>DIVISI : ".strtoupper($afd)."</s1>", 0);
    $pdf->Ln(2);
    
    //load the table default definitions DEFAULT!!!
    require_once(APPPATH . 'libraries/rptPDF_def.inc');
    $columns = 25; //number of Columns
    
    //Initialize the table class
    $pdf->tbInitialize($columns, true, true);
    
    //set the Table Type
    $pdf->tbSetTableType($table_default_table_type);
    $aSimpleHeader = array();
    
    $header = array('No','NIK','Nama','Status','Type  Karyawan','HK Dibayar','','','Nilai (Rp) Dibayar','','','GP', 'Tunj. Jab','Premi / Lembur','Natura','Rapel / THR / Bonus','Astek   (4,54%)','Tunj. Lebih Hari','Gaji Bruto','Pot. Astek   (6,54%)','Pot.Lain','PPh 21','    Pot. Kurang Hari','TTL Pot.','Upah Diterima');
    $header2 = array('','','','','','HKE','HKNE','TTL','HKE','HKNE','TTL','','','','','','','','','','','','','','','');
    //Table Header
    for($i=0; $i < $columns+1; $i++) {
        $aSimpleHeader[$i] = $table_default_header_type;
        $aSimpleHeader[$i]['TEXT'] = $header[$i];
        $aSimpleHeader[0]['WIDTH'] = 7.5;
        $aSimpleHeader[1]['WIDTH'] = 14;
        $aSimpleHeader[2]['WIDTH'] = 34;
        $aSimpleHeader[3]['WIDTH'] = 10;
        $aSimpleHeader[4]['WIDTH'] = 14;
        $aSimpleHeader[5]['WIDTH'] = 30;
        $aSimpleHeader[6]['WIDTH'] = 0;
        $aSimpleHeader[7]['WIDTH'] = 0;
        $aSimpleHeader[8]['WIDTH'] = 15;
        $aSimpleHeader[10]['WIDTH'] = 15;
        $aSimpleHeader[18]['WIDTH'] = 15;
        $aSimpleHeader[21]['WIDTH'] = 12;
        $aSimpleHeader[23]['WIDTH'] = 15;
        $aSimpleHeader[24]['WIDTH'] = 15;
        $aSimpleHeader[$i]['WIDTH'] = 13.3;
        $aSimpleHeader2[$i]['LN_SIZE'] = 2;
        
        if ($i == '5') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
        else if ($i == '8') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
        else if ($i >= '0' && $i < '6'){ $aSimpleHeader[$i]['ROWSPAN'] = 2; } 
        else if ($i >= '11' && $i < '26'){ $aSimpleHeader[$i]['ROWSPAN'] = 2; } 
        
        $aSimpleHeader2[$i] = $table_default_header_type;
        $aSimpleHeader2[$i]['TEXT'] = $header2[$i];
        $aSimpleHeader2[0]['WIDTH'] = 7.5;
        $aSimpleHeader2[1]['WIDTH'] = 14;
        $aSimpleHeader2[2]['WIDTH'] = 34;
        $aSimpleHeader2[3]['WIDTH'] = 10;
        $aSimpleHeader2[4]['WIDTH'] = 14;
        $aSimpleHeader2[8]['WIDTH'] = 15;
        $aSimpleHeader2[10]['WIDTH'] = 15;
        /* hke kolom */
        //$aSimpleHeader2[2]['WIDTH'] = 18;
        $aSimpleHeader2[5]['WIDTH'] = 10;
        $aSimpleHeader2[6]['WIDTH'] = 10;
        $aSimpleHeader2[7]['WIDTH'] = 10;
        $aSimpleHeader2[18]['WIDTH'] = 15;
        $aSimpleHeader2[21]['WIDTH'] = 12;
        $aSimpleHeader2[23]['WIDTH'] = 15;
        $aSimpleHeader2[24]['WIDTH'] = 15;
        
        $aSimpleHeader2[$i]['WIDTH'] = 13.3;
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
            
    $data_row = $this->model_rpt_du->get_du_perafd($company,$from,$to,$afd);
    $i = 1;    
    foreach ($data_row as $row)
    {
        $data = Array();
            $data[0]['TEXT'] = $i;
            $data[1]['TEXT'] = $row['EMPLOYEE_CODE'];
            $data[2]['TEXT'] = $row['NAMA'];
            $data[2]['T_ALIGN'] = 'L';
            $data[3]['TEXT'] = $row['FAMILY_STATUS'];
            $data[4]['TEXT'] = $row['TYPE_KARYAWAN'];        
            $data[5]['TEXT'] = $row['HK'];
            $data[6]['TEXT'] = number_format($row['HKNE'],2,'.',',');
            $data[7]['TEXT'] = $row['TTL'];
            $data[8]['TEXT'] = number_format($row['HKE_BYR']);
            $data[8]['T_ALIGN'] = 'R';
            $data[9]['TEXT'] = number_format($row['HKNE_BYR']);
            $data[9]['T_ALIGN'] = 'R';
            $data[10]['TEXT'] = number_format($row['TTL_BYR']);
            $data[10]['T_ALIGN'] = 'R';
            
            if ($row['TYPE_KARYAWAN'] == "BHL"){
                $gp = $row['TTL_BYR'];
            } else if ($row['TYPE_KARYAWAN'] == "KDMP"){
                if($this->session->userdata('DCOMPANY') == 'MAG'){
                	$gp = $row['GP'];
				} else {
					$gp = $row['TTL_BYR'];
				}
            } else {
                $gp = $row['GP'];
            }
            $data[11]['TEXT'] = number_format($gp);
            $data[11]['T_ALIGN'] = 'R';
            
            $data[12]['TEXT'] = number_format($row['TUNJANGAN_JABATAN']);
            $data[12]['T_ALIGN'] = 'R';
            $data[13]['TEXT'] = number_format($row['PREMI_LEMBUR']);
            $data[13]['T_ALIGN'] = 'R';
            $data[14]['TEXT'] = number_format($row['NATURA']);
            $data[14]['T_ALIGN'] = 'R';
		 $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }

            $rtb = $row['RAPEL'] + $thr + $row['BONUS'];
            $data[15]['TEXT'] = number_format($rtb);
            $data[15]['T_ALIGN'] = 'R';
            $data[16]['TEXT'] = number_format($row['ASTEK']);
            $data[16]['T_ALIGN'] = 'R';            
            if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
                $tunj_lhari = $row['TTL_BYR'] - $gp;
                $data[17]['TEXT'] =    number_format($tunj_lhari);
                $data[17]['T_ALIGN'] = 'R';
            } else if ($row['TYPE_KARYAWAN'] == "KDMP" && $gp < $row['TTL_BYR']) {
                $tunj_lhari = $row['TTL_BYR'] - $gp;
                $data[17]['TEXT'] =    number_format($tunj_lhari);
                $data[17]['T_ALIGN'] = 'R';
            } else {
                $tunj_lhari = 0;
                $data[17]['TEXT'] = number_format($tunj_lhari);
                $data[17]['T_ALIGN'] = 'R';
            }
            
            $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;
            
            $data[18]['TEXT'] = number_format($gaji_bruto);
            $data[18]['T_ALIGN'] = 'R';
            $data[19]['TEXT'] = number_format($row['POT_ASTEK']);
            $data[19]['T_ALIGN'] = 'R';
            $data[20]['TEXT'] = number_format($row['POTONGAN_LAIN']);
            $data[20]['T_ALIGN'] = 'R';
            $data[21]['TEXT'] = number_format($row['PPH_21']);            
            $data[21]['T_ALIGN'] = 'R';
            if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR']){
                $pot_khari = -($row['TTL_BYR'] - $gp);
                $data[22]['TEXT'] = number_format($pot_khari);
                $data[22]['T_ALIGN'] = 'R';
            } else if($row['TYPE_KARYAWAN'] == "KDMP" && $gp > $row['TTL_BYR']){
                $pot_khari = -($row['TTL_BYR'] - $gp);
                $data[22]['TEXT'] = number_format($pot_khari);
                $data[22]['T_ALIGN'] = 'R';
            } else {
                $pot_khari = 0;
                $data[22]['TEXT'] = number_format($pot_khari);
                $data[22]['T_ALIGN'] = 'R';
            }
            
            $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
            $data[23]['TEXT'] = number_format($total_potongan);
            $data[23]['T_ALIGN'] = 'R';
            $data[24]['TEXT'] = number_format($gaji_bruto - $total_potongan);
            $data[24]['T_ALIGN'] = 'R';
            
            $ttl_hke = $ttl_hke + $row['HK'];
            $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
            $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
            
            $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];

            $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
            $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
            
            $ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
            $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
            $ttl_natura = $ttl_natura + $row['NATURA'];
            $ttl_rtb = $ttl_rtb + $rtb;
            
            $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
            $pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
            $ttl_potongan = $ttl_potongan + $total_potongan;
            $ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
            
            $i++;
            
        $pdf->tbDrawData($data);
    }
            $data[0]['TEXT'] = "Total";
            $data[0]['COLSPAN'] = 5;            
            $data[5]['TEXT'] = number_format($ttl_hke,2,'.',',');
            $data[6]['TEXT'] = number_format($ttl_hkne,2,'.',',');
            $data[7]['TEXT'] = number_format($ttl_hke_ne,2,'.',',');
            $data[8]['TEXT'] = number_format($ttlbyr_hke);
            $data[8]['T_ALIGN'] = 'R';
            $data[9]['TEXT'] = number_format($ttlbyr_hkne);
            $data[9]['T_ALIGN'] = 'R';
            $data[10]['TEXT'] = number_format($ttlbyr_hke_ne);
            $data[10]['T_ALIGN'] = 'R';
            $data[11]['TEXT'] = '-';
            $data[11]['T_ALIGN'] = 'C';
            $data[12]['TEXT'] = number_format($ttl_tunjab);
            $data[12]['T_ALIGN'] = 'R';
            $data[13]['TEXT'] = number_format($ttl_premi);
            $data[13]['T_ALIGN'] = 'R';
            $data[14]['TEXT'] = number_format($ttl_natura);
            $data[14]['T_ALIGN'] = 'R';
            $data[15]['TEXT'] = number_format($ttl_rtb);
            $data[15]['T_ALIGN'] = 'R';
            $data[16]['TEXT'] = '-';
            $data[16]['T_ALIGN'] = 'C';
            $data[16]['COLSPAN'] = '2';
            $data[18]['TEXT'] = number_format($ttl_gaji_bruto);
            $data[18]['T_ALIGN'] = 'R';
            $data[19]['TEXT'] = '-';
            $data[19]['T_ALIGN'] = 'C';
            $data[20]['TEXT'] = number_format($pot_lain);
            $data[20]['T_ALIGN'] = 'R';
            $data[21]['TEXT'] = '-';
            $data[21]['T_ALIGN'] = 'C';
            $data[21]['COLSPAN'] = 2;
            
            $data[23]['TEXT'] = number_format($ttl_potongan);
            $data[23]['T_ALIGN'] = 'R';
            $data[24]['TEXT'] = number_format($ttl_upah);
            $data[24]['T_ALIGN'] = 'R';
    $pdf->tbDrawData($data);    
    $pdf->tbOuputData();
    $pdf->tbDrawBorder();
    
                        
    $pdf->Ln(15.5);
    
    require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
        
    $pdf->Output();
    
    }
    
	
	/* 	report untuk du bulanan
		author 		: ridhuz
		modified 	: 2012-01-16 */
	function dropdownlist_dept()
	{
		$company = $this->session->userdata('DCOMPANY');
		$string = "<select  name='i_dept' class='select' id='i_dept' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$string .= "<option value='ALL'> * </option>";
		$data_dept = $this->model_rpt_du->get_gangcode($company);
		
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['GANG_CODE']."\"  selected>".$row['GANG_CODE']." </option>";
			} else {
				$string = $string." <option value=\"".$row['GANG_CODE']."\">".$row['GANG_CODE']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function preview_bulanan(){
        $dept = $this->uri->segment(3);
        $from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
        $company = $this->session->userdata('DCOMPANY');
        $data_row = $this->model_rpt_du->generate_du_bulanan($dept, $from, $to, $company);
        
        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
        $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
        $ttl_potongan = 0; $ttl_upah = 0; $gp = 0; $tunj_lhari = 0; $pot_khari = 0;
        $ttl_gp = 0; $ttl_astek = 0;  $ttl_lhari = 0; $ttl_potongan = 0; $ttl_pastek = 0;
        $ttl_plhari = 0; $ttl_pph21 = 0;    
        
        $i = 1;
        $DU = "";
        
        
        $DU .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
        $DU .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_2 { font-size: 12px;color:#678197;} ";
        $DU .= ".content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>";
        
        if ($dept == "ALL") {
            $DU .= "<span class='content2'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
            $DU .= "<span class='content2'>DAFTAR UPAH KARYAWAN BULANAN</span><br />";
        } else {
            $data_gc = $this->model_rpt_du->header_du($dept, $company);
            foreach ($data_gc as $row)
            {
                $DU .= "<span class='content2'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
                $DU .= "<span class='content2'>DAFTAR UPAH KARYAWAN BULANAN</span><br />";
                $DU .= "<span class='content2'>KEMANDORAN     :  ".strtoupper($row['GANG_CODE']) ." - ".strtoupper($row['DESCRIPTION']) ." </span><br />";
				$DU .= "<span class='content2'>MANDOR         :  ".strtoupper($row['MANDORE_CODE']) ." - ".strtoupper($row['NAMA']) ." </span><br />";
            }
        }
        
        $DU .= "<div class='content2' style='float:right;margin-right:1%;'><a href='".base_url()."index.php/rpt_tandaterima/gen_tt/".$dept."/".$from."/bln/".$to."' target='_blank'>CETAK TANDA TERIMA GAJI</a>
        &nbsp;<a href='".base_url()."index.php/rpt_slip_gaji/create_slipgaji/".$dept."/".$from."/bln/".$to."' target='_blank'>CETAK SLIP GAJI</a></div><br/>";
        $DU .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
        $DU .= "<tr><td  align='center' rowspan='2' class='tbl_th'> No. </td>";
        if ($dept == "ALL") {
            $DU .= "<td rowspan='2' align='center' class='tbl_th'>Kemandoran</td>";
        }
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>NIK</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Nama</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Status</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Type Karyawan</td>";
        $DU .= "<td colspan='3' align='center' class='tbl_th'>HK dibayar</td>";
        $DU .= "<td align='center' colspan='3' class='tbl_th'>Nilai (Rp) dibayar</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>GP</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Jabatan / Tunj. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Premi / Lembur</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Natura</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Rapel / Thr / Bonus</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Lebih Hari</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Gaji Bruto</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Kurang Hari</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>PPh 21</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Total Potongan</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Upah Diterima</td></tr>";
        $DU .= "<tr><td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td>";
        $DU .= "<td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td></tr>";
        $style = "";
        $url = base_url().'index.php/rpt_du/'; 
        foreach ($data_row as $row)
        { 
            if($row['GANG_CODE'] == "") {
                $style = 'style="background-color:#CF0"';
            } else { $style = ""; }
            
            $DU .= '<tr id="tr_1">';
              $DU .= '<td class="tbl_td" '.$style.'><center>'.$i.'</center></td>';
                                
            if ($dept == "ALL") {    
                $DU .= '<td width="50" class="tbl_td" '.$style.' ><center>'.$row['GANG_CODE'].'</center></td>';
            }
            $DU .= "<td width='50' class='tbl_td' ".$style."><strong><a href='".$url."rpt_du_breakdown/".$row['EMPLOYEE_CODE']."/".$dept."/".substr($to,0,6).
            "' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><center>".$row['EMPLOYEE_CODE']."</center></a></strong></td>";
            $DU .= '<td width="150" class="tbl_td" '.$style.' align="left">&nbsp;'.$row['NAMA'].'</td>';
            $DU .= '<td width="75" class="tbl_td" '.$style.'><center>'.$row['FAMILY_STATUS'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.'><center>'.$row['TYPE_KARYAWAN'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.$row['HK'].' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.$row['HKNE'].'  &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.$row['TTL'].'</strong> &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['HKE_BYR']) .' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['HKNE_BYR']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($row['TTL_BYR']).'</strong>&nbsp;</td>';
            
            if ($row['TYPE_KARYAWAN'] == "BHL"){
                $gp = $row['TTL_BYR'];
            } else if ($row['TYPE_KARYAWAN'] == "KDMP"){
				if($this->session->userdata('DCOMPANY') == 'MAG'){
                	$gp = $row['GP'];
				} else {
					$gp = $row['TTL_BYR'];
				}
            } else {
                $gp = $row['GP'];
            }
            
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($gp).'</strong>&nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['TUNJANGAN_JABATAN']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['PREMI_LEMBUR']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['NATURA']).' &nbsp;</td>';
 $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }

            $rtb = $row['RAPEL'] + $thr + $row['BONUS'];
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($rtb).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['ASTEK']).' &nbsp;</td>';
            if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
                    $tunj_lhari = $row['TTL_BYR'] - $gp;
                    if($tunj_lhari < 100) { $tunj_lhari = 0; } 
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            } else if ($row['TYPE_KARYAWAN'] == "KDMP" && $gp < $row['TTL_BYR']) {
                    $tunj_lhari = $row['TTL_BYR'] - $gp;
                    if($tunj_lhari < 100) { $tunj_lhari = 0; } 
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            } else {
                $tunj_lhari = 0;
                $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            }
            
        $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($gaji_bruto).'</strong>&nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['POT_ASTEK']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['POTONGAN_LAIN']).' &nbsp;</td>';
            if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR']){
                    $pot_khari = -($row['TTL_BYR'] - $gp);
                    if($pot_khari < 100) { $pot_khari = 0; }
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($pot_khari).' &nbsp;</td>';
            } else if($row['TYPE_KARYAWAN'] == "KDMP" && $gp > $row['TTL_BYR']){
                    $pot_khari = -($row['TTL_BYR'] - $gp);
                    if($pot_khari < 100) { $pot_khari = 0; }
                    $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($pot_khari).' &nbsp;</td>';
            } else {
                $pot_khari = 0;
                $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"> 0 &nbsp;</td>';
            }    
             
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right">'.number_format($row['PPH_21']).' &nbsp;</td>';
            $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($total_potongan).' &nbsp;</strong></td>';
            $DU .= '<td width="78" class="tbl_td" '.$style.' align="right"><strong>'.number_format($gaji_bruto - $total_potongan).' &nbsp;</strong></td>';
          $DU .= '</tr>';    
        
        $ttl_hke = $ttl_hke + $row['HK'];
        $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
        $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
        
        $ttl_gp = $ttl_gp + $gp; 
        $ttl_astek = $ttl_astek + $row['ASTEK'];  
        $ttl_lhari = $ttl_lhari + $tunj_lhari ; 
        $ttl_pastek = $ttl_pastek + $row['POT_ASTEK'];
        $ttl_plhari = $ttl_plhari + $pot_khari; 
        $ttl_pph21 = $ttl_pph21 + $row['PPH_21'];    
        
        $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
        $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
        $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
        
        $ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
        $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
        $ttl_natura = $ttl_natura + $row['NATURA'];
        $ttl_rtb = $ttl_rtb + $rtb;
        
        $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
        $pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
        $ttl_potongan = $ttl_potongan + $total_potongan;
        $ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
        
            $i++;
        }
        if ($dept == "ALL") {
            $DU .= "<tr><td class='tbl_td' align='center' colspan='6'><strong>Total</strong></td>";
        } else {
            $DU .= "<tr><td class='tbl_td' align='center' colspan='5'><strong>Total</strong></td>";
        }
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hkne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke_ne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hkne)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke_ne)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_gp)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_tunjab)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_premi)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_natura)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_rtb)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_astek)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_lhari)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_gaji_bruto)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_pastek)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($pot_lain)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_plhari)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_pph21)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_potongan)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_upah)." &nbsp;</strong></td></tr>";
        $DU .= "</table>"; 
        echo $DU;
    }
	
	function gen_rpt_du_bulanan($gc, $from, $to){
            
		if ($this->session->userdata('logged_in') != TRUE){
		   redirect('login');
		}
		
		$company = $this->session->userdata('DCOMPANY');
		$pdf = new pdf_usage();        
		$pdf->Open();
		$pdf->SetAutoPageBreak(true, 10);
			$pdf->SetMargins(5, 13,20);
		$pdf->AddPage('L', 'LEGAL');
		$pdf->AliasNbPages(); 
			
		$pdf->SetStyle("s1","arial","",9,"");
		$pdf->SetStyle("s2","arial","",8,"");
		$pdf->SetStyle("s3","arial","",10,"");
		
		$data_gc = $this->model_rpt_du->header_du($gc, $company);
		$ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
		$ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
		$ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
		$ttl_potongan = 0;  $ttl_upah = 0;
		
		$gangcode1 = ""; $gangcode2 = "";
		$gangcode3 = ""; $gangcode4 = "";    
		
		foreach ($data_gc as $row)
		{
			$gangcode2 .= $row['GANG_CODE'] . " - " . $row['DESCRIPTION'] ;
			$gangcode3 .= $row['MANDORE_CODE'] . " - " . $row['NAMA'] ;
		}
		//default text color
		$pdf->SetTextColor(118, 0, 3);
		$pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
		$pdf->MultiCellTag(200, 5, "<s3>DAFTAR UPAH KARYAWAN BULANAN</s3>", 0);
		//$periode = substr($to,4,2);
		
		$bulan = substr($to,4,2);
		$bulan = $this->bln_to_periode($bulan);
		if ($dept == "ALL") {
			$pdf->MultiCellTag(200, 5, "<s1>PERIODE : ". strtoupper($bulan) ." ". substr($periode,0,4) ." </s1>", 0);
		} else {
			$pdf->MultiCellTag(200, 5, "<s1>PERIODE : ". strtoupper($bulan) ." ". substr($periode,0,4) ." </s1>", 0);
			$pdf->MultiCellTag(200, 5, "<s1>KEMANDORAN : ". $gangcode2 ." </s1>", 0);
			$pdf->MultiCellTag(200, 5, "<s1>MANDOR : ". $gangcode3 ." </s1>", 0);
		}
		$pdf->Ln(2);
		
		
		//load the table default definitions DEFAULT!!!
		require_once(APPPATH . 'libraries/rptPDF_def.inc');
		$columns = 25; //number of Columns
		
		//Initialize the table class
		$pdf->tbInitialize($columns, true, true);
		
		//set the Table Type
		$pdf->tbSetTableType($table_default_table_type);
		$aSimpleHeader = array();
		
		$header = array('No','NIK','Nama','Status','Type  Karyawan','HK Dibayar','','','Nilai (Rp) Dibayar','','','GP', 'Tunj. Jab','Premi / Lembur','Natura','Rapel / THR / Bonus','Astek   (4,54%)','Tunj. Lebih Hari','Gaji Bruto','Pot. Astek   (6,54%)','Pot.Lain','PPh 21','    Pot. Kurang Hari','TTL Pot.','Upah Diterima');
		$header2 = array('','','','','','HKE','HKNE','TTL','HKE','HKNE','TTL','','','','','','','','','','','','','','','');
		//Table Header
		for($i=0; $i < $columns+1; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['TEXT'] = $header[$i];
			$aSimpleHeader[0]['WIDTH'] = 5.5;
			$aSimpleHeader[1]['WIDTH'] = 14;
			$aSimpleHeader[2]['WIDTH'] = 34;
			$aSimpleHeader[3]['WIDTH'] = 10;
			$aSimpleHeader[4]['WIDTH'] = 14;
			$aSimpleHeader[5]['WIDTH'] = 30;
			$aSimpleHeader[6]['WIDTH'] = 0;
			$aSimpleHeader[7]['WIDTH'] = 0;
			$aSimpleHeader[8]['WIDTH'] = 15;
			$aSimpleHeader[10]['WIDTH'] = 15;
			$aSimpleHeader[18]['WIDTH'] = 15;
			$aSimpleHeader[21]['WIDTH'] = 12;
			$aSimpleHeader[23]['WIDTH'] = 15;
			$aSimpleHeader[24]['WIDTH'] = 15;
			$aSimpleHeader[$i]['WIDTH'] = 13.3;
			$aSimpleHeader2[$i]['LN_SIZE'] = 2;
			
			if ($i == '5') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
			else if ($i == '8') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
			else if ($i >= '0' && $i < '6'){ $aSimpleHeader[$i]['ROWSPAN'] = 2; } 
			else if ($i >= '11' && $i < '26'){ $aSimpleHeader[$i]['ROWSPAN'] = 2; } 
			
			$aSimpleHeader2[$i] = $table_default_header_type;
			$aSimpleHeader2[$i]['TEXT'] = $header2[$i];
			$aSimpleHeader2[0]['WIDTH'] = 5.5;
			$aSimpleHeader2[1]['WIDTH'] = 14;
			$aSimpleHeader2[2]['WIDTH'] = 34;
			$aSimpleHeader2[3]['WIDTH'] = 10;
			$aSimpleHeader2[4]['WIDTH'] = 14;
			$aSimpleHeader2[8]['WIDTH'] = 15;
			$aSimpleHeader2[10]['WIDTH'] = 15;
			/* hke kolom */
			//$aSimpleHeader2[2]['WIDTH'] = 18;
			$aSimpleHeader2[5]['WIDTH'] = 10;
			$aSimpleHeader2[6]['WIDTH'] = 10;
			$aSimpleHeader2[7]['WIDTH'] = 10;
			$aSimpleHeader2[18]['WIDTH'] = 15;
			$aSimpleHeader2[21]['WIDTH'] = 12;
			$aSimpleHeader2[23]['WIDTH'] = 15;
			$aSimpleHeader2[24]['WIDTH'] = 15;
			
			$aSimpleHeader2[$i]['WIDTH'] = 13.3;
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
				
		$data_row = $this->model_rpt_du->generate_du_bulanan($gc, $from, $to, $company);
		$i = 1;    
		foreach ($data_row as $row)
		{
			$data = Array();
				$data[0]['TEXT'] = $i;
				$data[1]['TEXT'] = $row['EMPLOYEE_CODE'];
				$data[2]['TEXT'] = $row['NAMA'];
				$data[2]['T_ALIGN'] = 'L';
				$data[3]['TEXT'] = $row['FAMILY_STATUS'];
				$data[4]['TEXT'] = $row['TYPE_KARYAWAN'];        
				$data[5]['TEXT'] = $row['HK'];
				$data[6]['TEXT'] = $row['HKNE'];
				$data[7]['TEXT'] = $row['TTL'];
				$data[8]['TEXT'] = number_format($row['HKE_BYR']);
				$data[8]['T_ALIGN'] = 'R';
				$data[9]['TEXT'] = number_format($row['HKNE_BYR']);
				$data[9]['T_ALIGN'] = 'R';
				$data[10]['TEXT'] = number_format($row['TTL_BYR']);
				$data[10]['T_ALIGN'] = 'R';
				
				if ($row['TYPE_KARYAWAN'] == "BHL"){
					$gp = $row['TTL_BYR'];
				} else if ($row['TYPE_KARYAWAN'] == "KDMP"){
					if($this->session->userdata('DCOMPANY') == 'MAG'){
						$gp = $row['GP'];
					} else {
						$gp = $row['TTL_BYR'];
					}
				} else {
					$gp = $row['GP'];
				}
				$data[11]['TEXT'] = number_format($gp);
				$data[11]['T_ALIGN'] = 'R';
				
				$data[12]['TEXT'] = number_format($row['TUNJANGAN_JABATAN']);
				$data[12]['T_ALIGN'] = 'R';
				$data[13]['TEXT'] = number_format($row['PREMI_LEMBUR']);
				$data[13]['T_ALIGN'] = 'R';
				$data[14]['TEXT'] = number_format($row['NATURA']);
				$data[14]['T_ALIGN'] = 'R';
 $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }

				$rtb = $row['RAPEL'] + $thr + $row['BONUS'];
				$data[15]['TEXT'] = number_format($rtb);
				$data[15]['T_ALIGN'] = 'R';
				$data[16]['TEXT'] = number_format($row['ASTEK']);
				$data[16]['T_ALIGN'] = 'R';            
				if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
					$tunj_lhari = $row['TTL_BYR'] - $gp;
					$data[17]['TEXT'] =    number_format($tunj_lhari);
					$data[17]['T_ALIGN'] = 'R';
				} else if ($row['TYPE_KARYAWAN'] == "KDMP" && $gp < $row['TTL_BYR']) {
					$tunj_lhari = $row['TTL_BYR'] - $gp;
					$data[17]['TEXT'] =    number_format($tunj_lhari);
					$data[17]['T_ALIGN'] = 'R';
				} else {
					$tunj_lhari = 0;
					$data[17]['TEXT'] = number_format($tunj_lhari);
					$data[17]['T_ALIGN'] = 'R';
				}
				
				$gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;
				
				$data[18]['TEXT'] = number_format($gaji_bruto);
				$data[18]['T_ALIGN'] = 'R';
				$data[19]['TEXT'] = number_format($row['POT_ASTEK']);
				$data[19]['T_ALIGN'] = 'R';
				$data[20]['TEXT'] = number_format($row['POTONGAN_LAIN']);
				$data[20]['T_ALIGN'] = 'R';
				$data[21]['TEXT'] = number_format($row['PPH_21']);            
				$data[21]['T_ALIGN'] = 'R';
				if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR'] ){
					$pot_khari = -($row['TTL_BYR'] - $gp);
					$data[22]['TEXT'] = number_format($pot_khari);
					$data[22]['T_ALIGN'] = 'R';
				} else if($row['TYPE_KARYAWAN'] == "KDMP" && $gp > $row['TTL_BYR'] ){
					$pot_khari = -($row['TTL_BYR'] - $gp);
					$data[22]['TEXT'] = number_format($pot_khari);
					$data[22]['T_ALIGN'] = 'R';
				} else {
					$pot_khari = 0;
					$data[22]['TEXT'] = number_format($pot_khari);
					$data[22]['T_ALIGN'] = 'R';
				}
				
				$total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
				$data[23]['TEXT'] = number_format($total_potongan);
				$data[23]['T_ALIGN'] = 'R';
				$data[24]['TEXT'] = number_format($gaji_bruto - $total_potongan);
				$data[24]['T_ALIGN'] = 'R';
				
				$ttl_hke = $ttl_hke + $row['HK'];
				$ttl_hkne = $ttl_hkne + $row['HKNE'] ;
				$ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
				
				$ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
				$ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
				$ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
				
				$ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
				$ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
				$ttl_natura = $ttl_natura + $row['NATURA'];
				$ttl_rtb = $ttl_rtb + $rtb;
				
				$ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
				$pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
				$ttl_potongan = $ttl_potongan + $total_potongan;
				$ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
				
				$i++;
				
			$pdf->tbDrawData($data);
		}
				$data[0]['TEXT'] = "Total";
				$data[0]['COLSPAN'] = 5;            
				$data[5]['TEXT'] = $ttl_hke;
				$data[6]['TEXT'] = $ttl_hkne;
				$data[7]['TEXT'] = $ttl_hke_ne;
				$data[8]['TEXT'] = number_format($ttlbyr_hke);
				$data[8]['T_ALIGN'] = 'R';
				$data[9]['TEXT'] = number_format($ttlbyr_hkne);
				$data[9]['T_ALIGN'] = 'R';
				$data[10]['TEXT'] = number_format($ttlbyr_hke_ne);
				$data[10]['T_ALIGN'] = 'R';
				$data[11]['TEXT'] = '-';
				$data[11]['T_ALIGN'] = 'C';
				$data[12]['TEXT'] = number_format($ttl_tunjab);
				$data[12]['T_ALIGN'] = 'R';
				$data[13]['TEXT'] = number_format($ttl_premi);
				$data[13]['T_ALIGN'] = 'R';
				$data[14]['TEXT'] = number_format($ttl_natura);
				$data[14]['T_ALIGN'] = 'R';
				$data[15]['TEXT'] = number_format($ttl_rtb);
				$data[15]['T_ALIGN'] = 'R';
				$data[16]['TEXT'] = '-';
				$data[16]['T_ALIGN'] = 'C';
				$data[16]['COLSPAN'] = '2';
				$data[18]['TEXT'] = number_format($ttl_gaji_bruto);
				$data[18]['T_ALIGN'] = 'R';
				$data[19]['TEXT'] = '-';
				$data[19]['T_ALIGN'] = 'C';
				$data[20]['TEXT'] = number_format($pot_lain);
				$data[20]['T_ALIGN'] = 'R';
				$data[21]['TEXT'] = '-';
				$data[21]['T_ALIGN'] = 'C';
				$data[21]['COLSPAN'] = 2;
				
				$data[23]['TEXT'] = number_format($ttl_potongan);
				$data[23]['T_ALIGN'] = 'R';
				$data[24]['TEXT'] = number_format($ttl_upah);
				$data[24]['T_ALIGN'] = 'R';
		$pdf->tbDrawData($data);
		//output the table data to the pdf
		$pdf->tbOuputData();
		//draw the Table Border
		$pdf->tbDrawBorder();
		$pdf->Ln(15.5);
    
    require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
    $pdf->Output();
    }
	
	function du_bulanan_xls(){
        $typerpt = $this->uri->segment(6);
        $gc = $this->uri->segment(3);
        $from = $this->uri->segment(4);
        $to = $this->uri->segment(5);
        $periode = $from."_".$to;
        $company = $this->session->userdata('DCOMPANY');
        
        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0;
        $ttl_premi = 0; $ttl_natura = 0; $ttl_rtb = 0;
        $ttl_gaji_bruto = 0; $pot_lain = 0; $ttl_potongan = 0; $ttl_upah = 0;
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();
                
        if($gc == 'ALL') {
            $data_row = $this->model_rpt_du->generate_du_bulanan($gc, $from, $to, $company);            
            $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
            $judul .= "DAFTAR UPAH KARYAWAN BULANAN".$periode."\n";
        } else {    
            $data_row = $this->model_rpt_du->generate_du_bulanan($gc,$from, $to,$company);
            $data_gc = $this->model_rpt_du->header_du($gc,$company);        
            foreach ($data_gc as $row)
            {
                $judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t";
                $judul .= " \n";
                $judul .= "DAFTAR UPAH KARYAWAN BULANAN\t";
                $judul .= " \n";
                $judul .= "KEMANDORAN \t";
                $judul .= strtoupper($row['GANG_CODE'])."-".strtoupper($row['DESCRIPTION'])."\n";
				$judul .= " \n";
                $judul .= "MANDOR \t";
                $judul .= strtoupper($row['MANDORE_CODE'])."-".strtoupper($row['NAMA'])."\n";

            }    
        }
        
        $array = array();
        //baris 1
        $headers .= "No \t";
        if($gc == 'ALL') {
            $headers .= "Kemandoran \t";
        }
        $headers .= "NIK \t";
        $headers .= "Nama \t";
        $headers .= "Status \t";
        $headers .= "Type Karyawan \t";    
        $headers .= "Division \t";
        $headers .= "HKE \t";
        $headers .= "HKNE \t";
        $headers .= "Total \t";
        $headers .= "HKE (Rp) dibayar \t";
        $headers .= "HKNE (Rp) dibayar \t";
        $headers .= "Total (Rp) dibayar \t";
        $headers .= "GP \t";
        $headers .= "Tunj. Jabatan \t";
        $headers .= "Premi / Lembur \t";
        $headers .= "Natura \t";
        $headers .= "Rapel / Thr / Bonus \t";
        $headers .= "Astek \t";
        $headers .= "Tunj. Lebih Hari \t";
        $headers .= "Gaji Bruto \t";
        $headers .= "Pot. Astek \t";
        $headers .= "Pot. Lain \t";
        $headers .= "Pot. Kurang Hari \t";
        $headers .= "PPh 21 \t";
        $headers .= "Total Potongan \t";
        $headers .= "Upah Diterima \n";
        
        $no = 1;
        
        foreach ($data_row as $row)
        {
                $line = '';
                    $line .= str_replace('"', '""',$no)."\t";
                    if($gc == 'ALL') {
                        $line .= str_replace('"', '""',$row['GANG_CODE'])."\t";
                    }
                    $line .= str_replace('"', '""',trim($row['EMPLOYEE_CODE']))."\t";
                    $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
                    $line .= str_replace('"', '""',trim($row['FAMILY_STATUS']))."\t";
                    $line .= str_replace('"', '""',trim($row['TYPE_KARYAWAN']))."\t";
                    $line .= str_replace('"', '""',trim($row['DIVISION_CODE']))."\t";
                    $line .= str_replace('"', '""',trim($row['HK']))."\t";
                    $line .= str_replace('"', '""',trim($row['HKNE']))."\t";
                    $line .= str_replace('"', '""',trim($row['TTL']))."\t";
                    $line .= str_replace('"', '""',trim($row['HKE_BYR']))."\t";
                    $line .= str_replace('"', '""',trim($row['HKNE_BYR']))."\t";
                    $line .= str_replace('"', '""',trim($row['TTL_BYR']))."\t";
                    if (trim(strtoupper($row['TYPE_KARYAWAN'])) == "BHL"){
                        $gp = trim($row['TTL_BYR']);
                    }  else if ($row['TYPE_KARYAWAN'] == "KDMP"){
                		if($this->session->userdata('DCOMPANY') == 'MAG'){
							$gp = $row['GP'];
						} else {
							$gp = $row['TTL_BYR'];
						}
            		} else {
                        $gp = trim($row['GP']);
                    }
                    $line .= str_replace('"', '""',$gp)."\t";
                    $line .= str_replace('"', '""',trim($row['TUNJANGAN_JABATAN']))."\t";
    $line .= str_replace('"', '""',trim($row['PREMI_LEMBUR']))."\t";
                    $line .= str_replace('"', '""',trim($row['NATURA']))."\t";
 $thr = 0;
	     if($this->session->userdata('LOGINID') == 'ridhuz' || $this->session->userdata('LOGINID') == 'aseps'){
	     	$thr = $row['THR'];
	     }

                    $rtb = $row['RAPEL'] + $thr + $row['BONUS'];
                    $line .= str_replace('"', '""',trim($rtb))."\t";
                    $line .= str_replace('"', '""',trim($row['ASTEK']))."\t";
                    
                    if (trim(strtoupper($row['TYPE_KARYAWAN'])) == "SKU" && $gp < $row['TTL_BYR']) {
                        $tunj_lhari = $row['TTL_BYR'] - $gp;            
                    } else if (trim(strtoupper($row['TYPE_KARYAWAN'])) == "KDMP" && $gp < $row['TTL_BYR']) {
                        $tunj_lhari = $row['TTL_BYR'] - $gp;            
                    } else {
                        $tunj_lhari = 0;
                    }
                    
                    $line .= str_replace('"', '""', trim($tunj_lhari) )."\t";
                    
                    $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;

                    $line .= str_replace('"', '""',trim($gaji_bruto))."\t";
                    $line .= str_replace('"', '""',trim($row['POT_ASTEK']))."\t";
                    $line .= str_replace('"', '""',trim($row['POTONGAN_LAIN']))."\t";
                    
                    if(trim(strtoupper($row['TYPE_KARYAWAN'])) == "SKU" && $gp > $row['TTL_BYR']){
                        $pot_khari = -($row['TTL_BYR'] - $gp);
                    } else if(trim(strtoupper($row['TYPE_KARYAWAN'])) == "KDMP" && $gp > $row['TTL_BYR']){
                        $pot_khari = -($row['TTL_BYR'] - $gp);
                    } else {
                    	$pot_khari = 0;
            		}
                    $line .= str_replace('"', '""', trim($pot_khari) )."\t";
                    $line .= str_replace('"', '""',trim($row['PPH_21']))."\t";
                $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
                //$total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $row['POT_LBH_HR'] + $row['PPH_21'];
                    $line .= str_replace('"', '""',trim($total_potongan))."\t";
                    $line .= str_replace('"', '""',trim($gaji_bruto) - trim($total_potongan))."\t";
                    
                            $ttl_hke = $ttl_hke + $row['HK'];
                    $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
                    $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
                    
                    $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
                    $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
                    $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
                    
                    $ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
                    $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
                    $ttl_natura = $ttl_natura + $row['NATURA'];
                    $ttl_rtb = $ttl_rtb + $rtb;
                    
                    $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
                    $pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
                    $ttl_potongan = $ttl_potongan + $total_potongan;
                    $ttl_upah = $ttl_upah + ( $gaji_bruto - $total_potongan );
                    
            $no++;
        
            $data .= trim($line)."\n";
            
        }        
                    $footer .= " - \t";
                    if($gc == 'ALL') {
                        $footer .= " - \t";
                    }
                    $footer .= " - \t";
                    $footer .= " Total \t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_hke))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_hkne))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_hke_ne))."\t";
                    $footer .= str_replace('"', '""',trim($ttlbyr_hke))."\t";
                    $footer .= str_replace('"', '""',trim($ttlbyr_hkne))."\t";
                    $footer .= str_replace('"', '""',trim($ttlbyr_hke_ne))."\t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_tunjab))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_premi))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_natura))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_rtb))."\t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_gaji_bruto))."\t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($pot_lain))."\t";
                    $footer .= " - \t";
                    $footer .= " - \t";
                    $footer .= str_replace('"', '""',trim($ttl_potongan))."\t";
                    $footer .= str_replace('"', '""',trim($ttl_upah)    )."\t";
            $data .= trim($footer)."\n";
        
        $data = str_replace("\r","",$data);
                         
        header("Content-type: application/x-msdownload");
           header("Content-Disposition: attachment; filename=".$company."_DU_".$gc."_".$periode.".xls");
    
        echo "$judul\n$headers\n$data";  
    }
	
	function generateKemandoran(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);

		$data_kemandoran = $this->model_rpt_du->regenerateKemandoran($company,$periode);
		$result = "";
		foreach ( $data_kemandoran as $row){
			$result .= $row['result'];
		}
		echo $result . " data berhasil tergenerate";
	}
	
	function generateGP(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$tahun = $this->uri->segment(3);

		$data_gp = $this->model_rpt_du->regenerateGP($company,$tahun);
		$result = "";
		foreach ( $data_gp as $row){
			$result .= $row['TOTAL'];
		}
		echo $result . " data berhasil tergenerate";
	}
	
	function cekGeneratePPh(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);
		$periode = substr(str_replace("-","",$periode),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		
		if($close == "0"){
			//$tglGenerate = $this->uri->segment(4);
			//$tglGenerate = substr($tglGenerate,0,4).'-'.substr($tglGenerate,4,2).'-'.substr($tglGenerate,6,2);
			$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
			$retDate = $this->model_rpt_du->cekGeneratePPh($periode, $company);
			
			if($retDate == ''){
				echo 'Mohon generate data pph terlebih dahulu pada Master Data -> Tunjangan & Potongan';
			} else {
				$date2 = new DateTime($retDate);
				$date1 = new DateTime(date("Y-m-d"));
				$diffence = $date1->diff($date2)->days;
				if($diffence >= 3){
					echo 'Data PPh digenerate terakhir tanggal : ' . $retDate . ', Mohon generate ulang pph!!';
				} else {
					echo '0';
				}
			}
			//
			//
			//echo $diffence;
			//
			//	
			//}
		} else {
			echo '0';
		}
	}
}

?>
