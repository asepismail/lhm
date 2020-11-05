<?php 
 
class jqChart { 
    private $coptions = array();
    private $conn; 
    private $dbtype; 
    private $jscode; 
    private $i_serie_index; 
    private $theme = '';
     
    function __construct($db=null) {
        $interface=''; 
        if(class_exists('jqGridDB') && $db){
            $interface = jqGridDB::getInterface();
        }else{
            $interface = 'chartarray';
            //trigger_error("Unable to load class: jqGridDB", E_USER_WARNING);
            //$interface = jqGridDB::getInterface();   
        }     
        $this->conn = $db; 
        if($interface == 'pdo') { 
            try { 
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                $this->dbtype = $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME); 
            } catch (Exception $e) { } 
        } else { 
            $this->dbtype = $interface; 
        } 
        $this->coptions['credits']['enabled'] = false; 
        $this->coptions['series'] = array(); 
        $this->i_serie_index = 0; 
        $this->jscode = false; 
    } 
    
    protected function getSQLSerie($sql, $params=null, $limit = false, $offset=0) { 
        $retarr = array(); 
        if($this->dbtype != 'chartarray' && $this->conn) { 
            try { 
                    if($limit && $limit > 0) { 
                        $sql = jqGridDB::limit($sql, $this->dbtype, $limit, $offset ); 
                    } 
                    $sersql = jqGridDB::prepare($this->conn, $sql, $params, true); 
                    jqGridDB::execute($sersql, $params); 
                    $xy = false; 
                    if(jqGridDB::columnCount($sersql) > 1) { 
                        $xy = true; 
                    } 
                    while($r = jqGridDB::fetch_num($sersql) ) { 
                        $retarr[] = $xy ? array((int)$r[0],(float)$r[1]) : $r[0]; 
                    } 
            } catch (Exception $e) { 
                echo $e->getMessage(); return false; 
            } 
        } 
        return $retarr; 
    } 
    
    public function getChartOptions() { 
        return $this->coptions; 
    } 
    
    public function setChartOptions($name, $mixvalue='') { 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['chart'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['chart'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setChartEvent($name, $jscode) { 
        $name = trim($name); 
        if($name != ''){ 
            $this->coptions['chart']['events'][$name] = "js:".$jscode; 
        } 
        return $this; 
    }
    
    public function setColors($avalue){ 
        if(is_array($avalue) && count($avalue) > 0){ 
            $this->coptions['colors'] = $avalue; 
        } 
        return $this; 
    } 
    
    public function setLabels($name, $mixvalue=''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['labels'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['labels'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    }
     
    public function setLanguage($name, $mixvalue=''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { $this->coptions['lang'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['lang'][$name] = $mixvalue; 
            } 
        } return $this; 
    } 
    
    public function setLegend($name, $mixvalue=''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['legend'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['legend'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setLoading($name, $mixvalue = ''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['loading'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['loading'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setPlotOptions($name, $avalue=''){ 
        if($avalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['plotOptions'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                if(is_array($avalue) && count($avalue) > 0){ 
                    $this->coptions['plotOptions'][$name] = $avalue; 
                } 
            } 
        } 
        return $this; 
    } 
    
    public function setSubtitle($name, $mixvalue=''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['subtitle'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['subtitle'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setTitle($name, $mixvalue=''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['title'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['title'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setTooltip($name, $mixvalue=''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    if($key=='formatter') $val = "js:".$val; $this->coptions['tooltip'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); 
            if($name != ''){ 
                if($name=='formatter') $mixvalue = "js:".$mixvalue; 
                $this->coptions['tooltip'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setxAxis($name, $mixvalue=''){ 
        if($mixvalue == '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['xAxis'][$key] = $val; 
                } 
            } 
        } 
        else { 
            $name = trim($name); 
            if($name != ''){ 
                $this->coptions['xAxis'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setyAxis($name, $mixvalue=''){ 
        if($mixvalue === '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['yAxis'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); if($name != ''){ 
                $this->coptions['yAxis'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function setExporting($name, $mixvalue=''){ 
        if($mixvalue === '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['exporting'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); if($name != ''){ 
                $this->coptions['exporting'][$name] = $mixvalue; 
            } 
        } return $this; 
    } 
    
    public function setNavigation($name, $mixvalue=''){ 
        if($mixvalue === '') { 
            if(is_array($name) && count($name) > 0 ) { 
                foreach($name as $key =>$val) { 
                    $this->coptions['navigation'][$key] = $val; 
                } 
            } 
        } else { 
            $name = trim($name); if($name != ''){ 
                $this->coptions['navigation'][$name] = $mixvalue; 
            } 
        } 
        return $this; 
    } 
    
    public function addSeries($name, $value, $params = null, $limit=false, $offset=0) { 
        $datafunc = false; 
        if($name != '') { 
            if(is_string($value)) { 
                if(strpos($value,'js:')===0) { 
                    $datafunc = true; 
                    $mixvalue = $value; 
                } else { 
                    $mixvalue = $this->getSQLSerie($value, $params, $limit, $offset); 
                } 
            } else { 
                $mixvalue = $value; 
            }
             
            if(is_array($mixvalue) || $datafunc) { 
                $f=false; 
                foreach($this->coptions['series'] as $index => $serie){ 
                    if(strtolower($serie['name']) == strtolower($name)){ 
                        $f=$index; break; 
                    } 
                } 
                if( $f!==false ){ if($datafunc) { 
                    $this->coptions['series'][$f]['data'] = $mixvalue; 
                } else { 
                    if(empty($mixvalue)) { 
                        $this->coptions['series'][$f]['data'] = $mixvalue; 
                    } else { 
                        foreach($mixvalue as $val){ 
                            $val = (is_numeric($val)) ? (float)$val : $val; 
                            $this->coptions['series'][$f]['data'][] = $val; 
                        } 
                    } 
                } 
                } else { 
                    $this->coptions['series'][$this->i_serie_index]['name'] = $name; 
                    if($datafunc) { 
                        $this->coptions['series'][$this->i_serie_index]['data'] = $mixvalue; 
                    } else { 
                        if(empty ($mixvalue)) { 
                            $this->coptions['series'][$this->i_serie_index]['data'] = $mixvalue; 
                        } else { 
                            foreach($mixvalue as $val){ 
                                $val = (is_numeric($val)) ? (float)$val : $val; 
                                $this->coptions['series'][$this->i_serie_index]['data'][] = $val; 
                            } 
                        } 
                    } $this->i_serie_index++; 
                } 
            } 
        } 
        return $this; 
    } 
    
    public function setSeriesOption($name='', $option='', $value=''){ 
        $name = trim($name); 
        if($name !== '' && $option){ 
            $f=false; 
            foreach($this->coptions['series'] as $index => $serie){ 
                if(strtolower($serie['name']) == strtolower($name)){ 
                    $f=$index; break; 
                } 
            } if( $f !== false ){ 
                if(is_array($option) && count($option)>0) { 
                    foreach($option as $key => $val) { 
                        $this->coptions['series'][$f][$key] = $val; 
                    } 
                } else { 
                    $this->coptions['series'][$f][$option] = $value; 
                } 
            } 
        } 
        return $this; 
    } 
    
    public function setJSCode($code) { 
        if(strlen($code)>0) { 
            $this->jscode = 'js:'.$code; 
        } 
        return $this; 
    } 
    
    public function setTheme($theme = '') { 
        if($theme && strlen($theme)>0) { 
            $this->theme = $theme; 
        } else { 
            $this->theme = ''; 
        } return $this; 
    } 
    
    public function renderChart($div_id='',$createlem=true, $width='800',$height='400', $chart='chart'){ 
        if($div_id == '') $div_id = 'jqchart'; 
        $this->coptions['chart']['renderTo'] = $div_id; 
        $width = is_numeric($width) ? $width.'px' : $width; 
        $height = is_numeric($height) ? $height.'px' : $height; 
        $dim = "width:".$width.";height:".$height.";margin: 0 auto;"; 
        $s = '<script type="text/javascript">'; 
        $s .= 'jQuery(document).ready(function(){'; 
        if($this->theme && strlen($this->theme)>0) { 
            $path = dirname(__FILE__).'/'; $themeFile = $path.$this->theme.".js"; 
            try { $fh = fopen($themeFile, 'r'); 
                if($fh) { 
                    $theme = fread($fh, filesize($themeFile)); fclose($fh); $s .= $theme; 
                } 
            } catch (Exception $e) { 
                
            } 
        } 
        $s .= 'var '.$chart.' = new Highcharts.Chart('.jqGridUtils::encode($this->coptions).');'; 
        if($this->jscode) { 
            $s .= jqGridUtils::encode($this->jscode); 
        } 
        $s .= '});'; 
        $s .= '</script>'; 
        if($createlem) { 
            $s .= '<div id="'.$div_id.'" style="'.$dim.'"></div>'; 
        } 
        return $s; 
    } 
} ?>