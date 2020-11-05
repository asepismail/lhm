<?php
function parseArrayToObject($array) {
   $object = new stdClass();
   if (is_array($array) && count($array) > 0) {
      foreach ($array as $name=>$value) {
         $name = strtolower(trim($name));
         if (!empty($name)) {
            $object->$name = $value;
         }
      }
   }
   return $object;
}

function parseObjectToArray($object) {
   $array = array();
   if (is_object($object)) {
      $array = get_object_vars($object);
   }
   return $array;
}
?>
