<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* Excel library for Code Igniter applications
* Author: Derek Allard, Dark Horse Consulting, www.darkhorse.to, April 2006
*/

/*
function to_excel($query, $filename='exceloutput')
{
     $headers = ''; // just creating the var for field headers to append to below
     $data = ''; // just creating the var for field data to append to below
     
     $obj =& get_instance();
     
     $fields = $query->field_data();
     if ($query->num_rows() == 0) {
          echo '<p>The table appears to have no data.</p>';
     } else {
          foreach ($fields as $field) {
             $headers .= $field->name . "\t";
          }
     
          foreach ($query->result() as $row) {
               $line = '';
               foreach($row as $value) {                                            
                    if ((!isset($value)) OR ($value == "")) {
                         $value = "\t";
                    } else {
                         $value = str_replace('"', '""', $value);
                         $value = '"' . $value . '"' . "\t";
                    }
                    $line .= $value;
               }
               $data .= trim($line)."\n";
          }
          
          $data = str_replace("\r","",$data);
                         
          header("Content-type: application/x-msdownload");
          header("Content-Disposition: attachment; filename=$filename.xls");
          echo "$headers\n$data";  
     }
} */

function to_excel($query, $filename='exceloutput')
{
    $headers = ''; // just creating the var for field headers to append to below
    $data = ''; // just creating the var for field data to append to below
     
    $obj =& get_instance();
     
    $fields = $query->field_data();
    if ($query->num_rows() == 0) {
        echo '<p>Tidak ada data untuk tabel ini.</p>';
		
    } else {
        foreach ($fields as $field) {
            $headers .= $field->name . "\t";
        }
     
        foreach ($query->result() as $row) {
            $line = '';
            foreach($row as $value) {                                            
                if ((!isset($value)) OR ($value == "")) {
                    $value = "\t";
                } else {
                    $value = str_replace('"', '""', $value);
                    // tweak
            $tmp_value = $value;
            $tmp_value = str_replace('""', '', $tmp_value);
            if (is_numeric($tmp_value))
            {
               //$value = str_replace('.', ',', $value);
               $tmp_value = '';
             }
             else
             {
               $tmp_value = '';
             }
                     // tweak
                     $value = '"' . $value . '"' . "\t";
                }
                $line .= $value;
            }
            $data .= trim($line)."\n";
        }
          
        $data = str_replace("\r","",$data);
                         
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename.xls");
        echo "$headers\n$data";  
    }
}

?> 