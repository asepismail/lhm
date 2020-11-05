<?php 
class SimpleXMLExtended extends SimpleXMLElement { 
    public function addCData($cdata_text) { 
        $node= dom_import_simplexml($this); 
        $no = $node->ownerDocument; 
        $node->appendChild($no->createCDATASection($cdata_text)); 
    } 
} 

class jqGridUtils { 
    public static function toXml($data, $rootNodeName = 'root', $xml=null, $encoding='utf-8', $cdata=false) { 
        if (ini_get('zend.ze1_compatibility_mode') == 1) { 
            ini_set ('zend.ze1_compatibility_mode', 0); 
        } 
        if ($xml == null) { 
            $xml = new SimpleXMLExtended("<?xml version='1.0' encoding='".$encoding."'?><$rootNodeName />"); 
        } 
        foreach($data as $key => $value) { 
            if (is_numeric($key)) { 
                $key = "row"; 
            } 
            if (is_array($value) || is_object($value)) { 
                $node = $xml->addChild($key); 
                self::toXml($value, $rootNodeName, $node, $encoding, $cdata); 
            } else { 
                $value = htmlspecialchars($value); 
                if($cdata===true) { 
                    $node = $xml->addChild($key); 
                    $node->addCData($value); 
                } else { 
                    $xml->addChild($key,$value); 
                } 
            } 
        } 
        return $xml->asXML(); 
    }
    
    public static function quote($js,$forUrl=false) { 
        if($forUrl) return strtr($js,array('%'=>'%25',"\t"=>'\t',"\n"=>'\n',"\r"=>'\r','"'=>'\"','\''=>'\\\'','\\'=>'\\\\')); 
        else return strtr($js,array("\t"=>'\t',"\n"=>'\n',"\r"=>'\r','"'=>'\"','\''=>'\\\'','\\'=>'\\\\',"'"=>'\'')); 
    } 
    
    public static function encode($value) { 
        if(is_string($value)) { 
            if(strpos($value,'js:')===0) return substr($value,3); 
            else return '"'.self::quote($value).'"'; 
        } else if($value===null) return "null"; 
        else if(is_bool($value)) return $value?"true":"false"; 
        else if(is_integer($value)) return "$value"; 
        else if(is_float($value)) { 
            if($value===-INF) return 'Number.NEGATIVE_INFINITY'; 
            else if($value===INF) return 'Number.POSITIVE_INFINITY'; 
            else return "$value"; 
        } else if(is_object($value)) return self::encode(get_object_vars($value)); 
        else if(is_array($value)) { 
            $es=array(); 
            if(($n=count($value))>0 && array_keys($value)!==range(0,$n-1)) { 
                foreach($value as $k=>$v) $es[]='"'.self::quote($k).'":'.self::encode($v); 
                return "{".implode(',',$es)."}"; 
            } else { 
                foreach($value as $v) $es[]=self::encode($v); 
                return "[".implode(',',$es)."]"; } } 
                else return ""; 
    } 
    
    public static function decode($json) { 
        $comment = false; 
        $out = '$x='; 
        for ($i=0; $i<strlen($json); $i++) { 
            if (!$comment) { 
                if ($json[$i] == '{') $out .= ' array('; 
                else if ($json[$i] == '}') $out .= ')'; 
                else if ($json[$i] == '[') $out .= ' array('; 
                else if ($json[$i] == ']') $out .= ')'; 
                else if ($json[$i] == ':') $out .= '=>'; 
                else $out .= $json[$i]; 
            } 
            else $out .= $json[$i]; 
            if ($json[$i] == '"') $comment = !$comment; 
        } 
        eval($out . ';'); 
        return $x; 
    } 
    
    public static function Strip($value) { 
        if(get_magic_quotes_gpc() != 0) { 
            if(is_array($value)) 
            if ( 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ) { 
                foreach( $value as $k=>$v){ 
                    $tmp_val[$k] = stripslashes($v); 
                    $value = $tmp_val;
                } 
            } else { 
                for($j = 0; $j < sizeof($value); $j++) $value[$j] = stripslashes($value[$j]); 
            } else {
                $value = stripslashes($value);
            } 
        } 
        return $value; 
    } 
    
    public static function parseDate($format, $date, $newformat = '') { 
        $m = 1; 
        $d = 1; 
        $y = 1970; 
        $h = 0; 
        $i = 0; 
        $s = 0; 
        $format = trim(strtolower($format)); 
        $date = trim($date); 
        $sep = '([\\\/:_;.\s-]{1})'; 
        $date = preg_split($sep, $date); 
        $format = preg_split($sep, $format); 
        foreach($format as $key => $formatDate) { 
            if(isset ($date[$key])) { 
                if(!preg_match('`^([0-9]{1,4})$`', $date[$key])) { 
                    return FALSE; 
                } 
                $$formatDate = $date[$key]; 
            } 
        } 
        $timestamp = mktime($h, $i, $s, $m, $d, $y); 
        if($newformat) return date($newformat, $timestamp); 
        return (integer)$timestamp; 
    } 
    
    public static function GetParam($parameter_name, $default_value = "") { 
        $parameter_value = ""; 
        if(isset($_POST[$parameter_name])) 
        $parameter_value = self::Strip($_POST[$parameter_name]); 
        else if(isset($_GET[$parameter_name])) $parameter_value = self::Strip($_GET[$parameter_name]); 
        else $parameter_value = $default_value; 
        return $parameter_value; 
    } 
    
    public static function array_extend($a, $b) { 
        foreach($b as $k=>$v) { 
            if( is_array($v) ) { 
                if( !isset($a[$k]) ) { 
                    $a[$k] = $v; 
                } 
                else { 
                    $a[$k] = self::array_extend($a[$k], $v); 
                } 
            } 
            else { 
                $a[$k] = $v; 
            } 
        } 
        return $a; 
    } 
    
    public static function phpTojsDate ($phpdate) { 
        str_replace('j', 'd', $phpdate); 
        str_replace('d', 'dd', $phpdate); 
        str_replace('z', 'o', $phpdate); 
        str_replace('l', 'DD', $phpdate); 
        str_replace('m', 'mm', $phpdate); 
        str_replace('n', 'm', $phpdate); 
        str_replace('F', 'MM', $phpdate); 
        str_replace('Y', 'yy', $phpdate); 
    } 
} 
?>
