<?php
class c_importdata extends Controller{
    private $data;
    
    function __construct(){
        parent::__construct() ;
        $this->load->model('model_importdata');
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        
        $this->load->plugin('to_excel');
        $this->load->helper('file');
        $this->lastmenu="c_importdata";         
    }
    
    function index(){
        $view="info_importdata";
        
        //$data = array();
        $this->data['judul_header'] = "Import Data";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
	function LoadData()
    {
        $company=$this->session->userdata('DCOMPANY');
        echo json_encode($this->model_importdata->LoadData($this->session->userdata('DCOMPANY')));
    }
	
    function do_import(){
        $error = "";
        $msg = "";
        $fileElementName = 'fileToImport';
        if(!empty($_FILES[$fileElementName]['error'])){
            
            switch($_FILES[$fileElementName]['error']){
                case '1':
                    $error = 'File yang diupload melewati batas maksimal upload yang diizinkan';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'Hanya beberapa dari file yang berhasil diupload';
                    break;
                case '4':
                    $error = 'Tidak ada file yang diupload.';
                    break;

                case '6':
                    $error = 'Folder temporer tidak ada';
                    break;
                case '7':
                    $error = 'Gagal untuk menyimpan file ke direktori';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'Tidak ada pesan error yang tersedia';
            }
        } elseif(empty($_FILES['fileToImport']['tmp_name']) || $_FILES['fileToImport']['tmp_name'] == 'none'){
            $error = 'Tidak ada file yang diimport..';
        } else {   
            $fieldseparator = ",";
            $lineseparator = "\n";
            $databasetable = "dummy_table";
            $csvfile = $_FILES['fileToImport']['tmp_name'];
            if(!file_exists($csvfile)) {
                echo "File tidak ditemukan. Pastikan anda memilih direktori yang benar.\n";
                exit;
            }

            $file = fopen($csvfile,"r");

            if(!$file) {
                echo "Error dalam membuka file data.\n";
                exit;
            }

            $size = filesize($csvfile);

            if(!$size) {
                echo "File kosong.\n";
                exit;
            }

            $csvcontent = fread($file,$size);

            fclose($file);

            $lines = 0;
            $queries = "";
            $linearray = array();
			
            foreach(explode($lineseparator,$csvcontent) as $line) {
                $lines++;

                $line = trim($line," \t");
                
                $line = str_replace("\r","",$line);
                
                $line = str_replace("'","\'",$line);
                
                $linearray = explode($fieldseparator,$line);
                
                $linemysql = implode("','",$linearray);
                
               
                $query = "insert into $databasetable values('$linemysql');";
                
                //$queries .= $query . "\n";
                $this->model_importdata->do_import($query); 
				//$imported = $this->db->affected_rows($query);   
            }
            $msg= $lines-1 . " Data berhasil diimport...";           
        }        
        echo "{";
        echo                "error: '" . $error . "',\n";
        echo                "msg: '" . $msg . "'\n";
        echo "}";
    }
	
	function doDeleteImport(){
		$error = "";
        $msg = "";	
		
		$this->db->where('company_code',$this->session->userdata('DCOMPANY'));
		$this->db->delete('dummy_table');
		
		$affct = $this->db->affected_rows();  
		
		$msg= " Data berhasil dihapus...";
		
		echo "{";
        echo                "error: '" . $error . "',\n";
        echo                "msg: '" . $msg . "'\n";
        echo "}";
	}
	
	/*  create excel  */
    function create_excel(){
      
        $company = $this->session->userdata('DCOMPANY');
              
        $this->db->select('transactid, kode,cost,periode,company_code');
        $this->db->where('company_code',$company);
               
        $query = $this->db->get('dummy_table');              
            
        to_excel($query,'SumberBiaya_'.$company);
        
        if ($query->num_rows() == 0) {
            redirect( 'c_importdata/' );
        } 
    }
}  
?>
