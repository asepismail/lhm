<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/*
| -------------------------------------------------------------------
| EMAIL CONFIG
| -------------------------------------------------------------------
| Konfigurasi email keluar melalui mail server
| */  
$config['protocol']='smtp'; 
$config['smtp_host']='ssl://smtp.googlemail.com'; 
$config['smtp_port']='465'; 
$config['smtp_timeout']='30'; 
$config['smtp_user']='alamat_email_gmail_sobat'; 
$config['smtp_pass']='Passwor_akun_gmail_sobat'; 
$config['charset']='utf-8'; 
$config['newline']="\r\n"; 
   
/* End of file email.php */ 
/* Location: ./system/application/config/email.php */