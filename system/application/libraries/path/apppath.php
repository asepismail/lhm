<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class apppath
{
    public $CI;
    public $template_path;
    public $baseUrl;
    
    public function __construct()
    {
        $this->CI = & get_instance();
    }
    public function getTemplatePath()
    {
        $this->template_path= base_url().$this->CI->config->item('template_path');
        return $this->template_path;
    }
    public function getBaseUrl()
    {
        $this->baseUrl=base_url();
        return $this->baseUrl;
    }
}
?>
