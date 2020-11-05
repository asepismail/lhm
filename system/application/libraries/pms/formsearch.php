<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


require_once(APPPATH . 'libraries/path/appPath.php');

class formSearch extends appPath
{
    public $colNameSearch;   //teks pada HTML
    public $dbcolNameSearch; //kolom pada database
    //protected $cntColSearch;   //jumlah kolom pencarian
    
    function __construct()
    {
        $this->CI= & get_instance();
    }
    
    
    function setColName($colNameSearch)
    {
        $this->colNameSearch=$colNameSearch;
        $this->addSrcSession($colNameSearch);
    }
    function setdbColName($dbcolNameSearch)
    {
        $this->dbcolNameSearch=$dbcolNameSearch;
        //$this->cntColSearch=count($dbcolNameSearch);
        //$this->addSrcSession();  //masukkan nilai count colum ke dalam session
    }
    function addSrcSession($data)
    {
          
        $oldSession = $this->CI->session->userdata('jsSearchCol'); //cek session sebelumnya
        if (!empty($oldSession)) //jika tidak kosong nilai session sebelumnya
        {
            $this->CI->session->unset_userdata('jsSearchCol');  //maka delete session
        }
        else
        {
            /*$arrCol = $this->colNameSearch;
            $cntColSearch=$this->cntColSearch;
            $data = array('searchCount'=>$cntColSearch);  //jika kosong
            $this->CI->session->set_userdata($data);    //isi dengan nilai baru  */
            
        } 
        
    }
    
    
    function jsclearSearchValue()
    {
        $arrCol = $this->colNameSearch;

        $showJS = "\n"."<script type='text/javascript'>"."\n";
        $showJS .="jQuery(document).ready(function(){ "."\n";
        foreach($arrCol as $colSearch )
        {
            $ids = "search_".$colSearch;
            $showJS .="     document.getElementById('$ids').value = '';"."\n";
        }
        $showJS .="});";
        $showJS .="\n"."</script>"."\n";
        
        return $showJS;
    }
    
    function loadSearchHTML()
    {
        $arrCol = $this->colNameSearch;
        $showHTML = "<table border='0' class='teks_' cellpadding='2' cellspacing='4'>";
        $showHTML .="<tr>";
        foreach($arrCol as $colSearch )
        {
            $ids = "search_".$colSearch;
            $showHTML .="<td>$colSearch </td>";
            $showHTML .="<td>:</td>";
            $showHTML .="<td >
                        <input type='text' class='input' id=$ids onkeydown='doSearch(arguments[0]||event)'/>
                        </td>";
        }
        $showHTML .="</tr>";
        $showHTML .="</form>";
        return $showHTML;
    }
    
    function doSearchs ($srcArg)
    {
        $srcArg=$srcArg;
        return $srcArg;
    }
    
}
?>
