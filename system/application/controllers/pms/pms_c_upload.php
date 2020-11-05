<?php
class pms_c_upload extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('session');
		$this->load->database();
	}
	
	
	/*
	*	Display upload form
	*/
	public function index()
	{
		$this->load->view('pms/pms_v_upload');
	}
	
	public function do_upload()
    {
        /* $this->load->library('upload');

        $image_upload_folder = FCPATH . '/uploads';

        if (!file_exists($image_upload_folder)) {
            mkdir($image_upload_folder, DIR_WRITE_MODE, true);
        }

        $this->upload_config = array(
            'upload_path'   => $image_upload_folder,
            'allowed_types' => 'png|jpg|jpeg|bmp|tiff',
            'max_size'      => 2048,
            'remove_space'  => TRUE,
            'encrypt_name'  => TRUE,
        );

        $this->upload->initialize($this->upload_config);
		
		var_dump($this->upload->do_upload());
        if (!$this->upload->do_upload()) {
            $upload_error = $this->upload->display_errors();
            echo json_encode($upload_error);
        } else {
            $file_info = $this->upload->data();
            echo json_encode($file_info);
        } */
		//$this->output->enable_profiler(TRUE);
		
    	if (!empty($_FILES)) {
		$tempFile = $_FILES['Filedata']['tmp_name'];
		//get codeigniter's root directory in the form
		//windows: c:\etc\etc
		//linux: home/etc/etc/etc
		//$root = getcwd();
		$root = FCPATH . '/uploads';
		$targetFile = $root . "YOUR_ABSOLUTE_PATH" . $_FILES['Filedata']['name'];
		 
		$filename = $_FILES['Filedata']['name'];
		$y_category = $_POST['category']; //get the category that we've set
		//do what ever you want with the category and you can perform some sql here
		 
		move_uploaded_file($tempFile,$targetFile);
		echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
		
		}
	}

	/*
	*	Handles JSON returned from /js/uploadify/upload.php
	*/
	public function uploadify()
	{
		
		//Decode JSON returned by /js/uploadify/upload.php
		$file = $this->input->post('filearray');
		$data['json'] = json_decode($file);
		
		$this->load->view('pms/pms_v_upload',$data);
	}
	
	/* baru */
	function set_filename($path, $filename, $file_ext, $encrypt_name = FALSE)
	{
		if ($encrypt_name == TRUE)
		{		
			mt_srand();
			$filename = md5(uniqid(mt_rand())).$file_ext;	
		}
	
		if ( ! file_exists($path.$filename))
		{
			return $filename;
		}
	
		$filename = str_replace($file_ext, '', $filename);
		
		$new_filename = '';
		for ($i = 1; $i < 100; $i++)
		{			
			if ( ! file_exists($path.$filename.$i.$file_ext))
			{
				$new_filename = $filename.$i.$file_ext;
				break;
			}
		}

		if ($new_filename == '')
		{
			return FALSE;
		}
		else
		{
			return $new_filename;
		}
	}
	
	function prep_filename($filename) {
	   if (strpos($filename, '.') === FALSE) {
		  return $filename;
	   }
	   $parts = explode('.', $filename);
	   $ext = array_pop($parts);
	   $filename    = array_shift($parts);
	   foreach ($parts as $part) {
		  $filename .= '.'.$part;
	   }
	   $filename .= '.'.$ext;
	   return $filename;
	}
	
	function get_extension($filename) {
	   $x = explode('.', $filename);
	   return '.'.end($x);
	} 

	
	function upload2(){
		//echo $_FILES;
		if (!empty($_FILES)) {
				  $path = '../uploads/';
				 //$client_id = $_GET['client_id'];
				 $file_temp = $_FILES['Filedata']['tmp_name'];
				 $file_name = date("Y-m-d_H-i-s").$this->prep_filename($_FILES['Filedata']['name']);
				 $file_ext = $this->get_extension($_FILES['Filedata']['name']);
				 $real_name = $file_name;
				 $newf_name = $this->set_filename($path, $file_name, $file_ext);
				 $file_size = round($_FILES['Filedata']['size']/1024, 2);
				 $file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES['Filedata']['type']);
				 $file_type = strtolower($file_type);
				 $targetFile =  str_replace('//','/',$path) . $newf_name;
				 if ( ! is_dir($path)) {
					 mkdir($path);
				 }
				  move_uploaded_file($file_temp, 'uploads/'.$_FILES['Filedata']['name']);
			  
				 $filearray = array();
				 $filearray['file_name'] = $newf_name;
				 $filearray['real_name'] = $real_name;
				 $filearray['file_ext'] = $file_ext;
				 $filearray['file_size'] = $file_size;
				 $filearray['file_path'] = $targetFile;
				 $filearray['file_temp'] = $file_temp;
				 //$filearray['client_id'] = $client_id;
		
		   		 $json_array = json_encode($filearray);
			     echo $json_array;
		}else{
				echo "1";	
		}
	}
}
/* End of File /application/controllers/upload.php */