<?
class open_close_period extends Controller 
{
	function open_close_period ()
	{
		parent::Controller();	

		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->database();
			
	}
	
	function index() { 
		$view = "open_close_period";
		$data = array();
		$data['judul_header'] = "Periode Closing";
		$data['js'] = $this->jsopen_close_period();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
				
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
	
	}
	
	function jsopen_close_period(){
		
		$js = "
		function cek() {
			$(function()
			{
				var modul = document.getElementById('modul').value;
				$('#afd').chainSelect('#submodul',url+'open_close_period/dropdownmod/'+ $('#modul').val(),
				{ 
					before:function (target) 
					{ 
						$('#loading').css('display','block');
					},
					after:function (target) 
					{ 
						$('#loading').css('display','none');
					}
				});
			});
		}
			jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
								
			if ( jns_laporan = 'html'){
				if(afd == ''){ 
					alert('pilih afd terlebih dahulu!!') 
				} else {
					var jns_laporan = $('#jns_laporan').val();	
					var urls = url + 'rpt_ba/ba_rawat_rekap_afd/' + afd + '/' + periode; 
					$('#frame').attr('src',urls);
				}
			}
		});	
		";
		return $js;
	}
	
}
?>