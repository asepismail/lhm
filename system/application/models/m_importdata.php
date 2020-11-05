<?php
class m_importdata extends Model{
    function __construct(){
        parent::__construct();
    }
    function do_import($query){
        $this->db->query($query);
    }
}  
?>
