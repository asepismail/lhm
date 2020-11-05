<?php
    if ( ! function_exists('show'))
    {
        function  show($view, $data)
        {
           global $template;
           $ci = &get_instance();
           $data['view'] = $view;
           $user_level= htmlentities($ci->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		   $user_module = $ci->session->userdata('MODULE_ACCESS');
           if(trim(strtoupper($user_module))=='PRD'){
                $ci->load->view('layout/next_layout', $data);
           }elseif(trim(strtoupper($user_module))=='PMS'){
                $ci->load->view('layout/pms_index', $data);
           }elseif(trim(strtoupper($user_module))=='LHM'){
                $ci->load->view('layout/tiga_index', $data); 
           }else{
                $ci->load->view('layout/home2', $data);        
           }                                                      
           
        }
    }
/* End of file using_template.php */
/* Location: ./system/application/helpers/using_template.php */
?>