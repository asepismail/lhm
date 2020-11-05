<?
class rpt_rekonbadu extends Controller 
{
	function rpt_rekonbadu ()
	{
		parent::Controller();	
		$this->load->model( 'model_rpt_rekon_badu' ); 
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->load->helper('file');
		require_once(APPPATH . 'libraries/fpdf_table.php');
		require_once(APPPATH . 'libraries/header_footer.inc');
	    require_once(APPPATH . 'libraries/table_def.inc');
		
	}
	
	function index()
    {
		$view = "rpt_rekonbadu";
		$data = array();
		$data['judul_header'] = "Rekonsiliasi BA & DU";
		$data['js'] = $this->js_badu();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		
		if ($data['login_id'] == TRUE){
			//if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
			show($view, $data);
			//} 
		} else {
			redirect('login');
		}		
    } 
	
	function js_ba_global(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var nw = $('#newwindow').is(':checked');
			urls = url + 'rpt_rekonbadu/gen_du/'+periode;
				if(nw != false) {	
						$('#frame').attr('src','');
							
						$('.button').popupWindow({ 
							windowURL:urls,
							windowName:'Rekap Biaya Gaji Rawat AFD : '+ afd,
							width:800 
						}); 
				} else {
						$('#frame').attr('src',urls);
				}
			 
				
			
		});";
		return $js;
	}
}

?>
