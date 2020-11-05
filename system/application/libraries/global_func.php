<?php

class global_func
{
    
    var $obj;
  
    function global_func(){
           $this->obj = &get_instance();
    }
    
	/* ############ cek closing periode : auth : ridhu #####*/
	function cekClosing($periode,$company){
		$this->obj->db->select('ISCLOSE');
        $this->obj->db->from('m_periode');
		$this->obj->db->where('PERIODE_NAME',$periode);
		$this->obj->db->where('COMPANY_CODE',$company);
        $temp=$this->obj->db->get();
        $temp = $temp->result_array();
        $this->obj->db->close();
		$ret = 0;
		foreach ( $temp as $row)
        {
			$ret = $row['ISCLOSE'];
		}
		return $ret;
	}
	
	function cekClosingTransaksi($modul,$date,$company){
		$periode = substr(str_replace("-","",$date),0,6);
		
		$qry = "SELECT pdc.ISCLOSE as ret FROM m_periode p
				LEFT JOIN m_periode_control pc ON pc.PERIODE_ID = p.PERIODE_ID
				LEFT JOIN m_periode_control_detail pdc ON pdc.PERIODE_CONTROL_ID = pc.PERIODE_CONTROL_ID
				WHERE pc.MODULE = '".$modul."' AND pc.COMPANY_CODE = '".$company."' AND pdc.PERIODE_DATE = '".$date."'
				AND p.PERIODE_NAME = '".$periode."'";
       $query=$this->obj->db->query($qry);
       
		$data = array_shift($query->result_array());
		$temp = $data['ret'];
		$this->obj->db->close();
		return $temp;
		
		//$ret = 0;
		/* foreach ( $temp as $row)
        {
			$ret = $row['ISCLOSE'];
		} */
		return $ret;
	}
	/* ############ end cek closing ########################*/
	
	/* ############ cek jumlah record tabel transaksi : auth : ridhu #####*/
	function cekExistData($periode, $tabel, $fieldtgl, $company){
		/* $this->obj->db->select("COUNT(".$fieldtgl.") AS JUMLAH");
        	$this->obj->db->from($tabel);
		$this->obj->db->where('COMPANY_CODE',$company);
		$this->obj->db->like($fieldtgl,$periode,'after');
        	$temp=$this->obj->db->get();
        	$temp = $temp->result_array();
        	$this->obj->db->close();
		$ret = 0;
		foreach ( $temp as $row){
			$ret = $row['JUMLAH'];
		}
		return $ret; */

		$query = $this->obj->db->query("SELECT ".$fieldtgl." FROM ".$tabel." WHERE DATE_FORMAT(".$fieldtgl.",'%Y%m') LIKE '".$periode."%' AND COMPANY_CODE = '".$company."'");
        	$count=$query->num_rows();
      
        	return $count;
	}
	/* ############ end cek closing ########################*/
	
	/* ############ dropdown afdeling ###################### */
	function dropdownlist_afdeling($id, $onchange, $company)
	{ 
		$string = "<select  name=\"".$id."\" class='select' id=\"".$id."\" ";
		if(!empty($onchange)){
            $string = $string." onchange=\"". $onchange."\" ";
        }
		$string .= "style='width:130px;' ><option value='all'> -- semua -- </option>";
				
		$this->obj->db->select('AFD_CODE, AFD_DESC');
		$this->obj->db->from('m_afdeling');
		$this->obj->db->where('COMPANY_CODE', $company);
		$this->obj->db->order_by("AFD_CODE", "ASC");
		      		
		$temp=$this->obj->db->get();
        $temp = $temp->result_array();
        $this->obj->db->close();
						
		foreach ( $temp as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['AFD_CODE']."\" selected>".$row['AFD_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	/* ############ end dropdown afdeling ################## */

	/* 	dropdown function gangcode 
		membatasi user untuk membuka kemandoran mana saja
		author : ridhu,  modified :	2011-12-26
	*/
	function cekdetiluser($company,$user)
    {
        $query = $this->obj->db->query("SELECT LOGINID FROM m_user_per_detail WHERE LOGINID = '".$user."' AND COMPANY_CODE = '".$company."'");
        $count=$query->num_rows();
      
        return $count;
    }
	
	function dropdownlist_gangcode($id, $onchange, $company, $user, $isdu='')
	{ 
		$cekdetail = $this->cekdetiluser($company, $user);
		
		$string = "<select  name=\"".$id."\" class='select' id=\"".$id."\" ";
		if(!empty($onchange)){
            $string = $string." onchange=\"". $onchange."\" ";
        }
		$string .= "style='width:130px;' ><option value=''> -- Pilih -- </option>";
				
		if($cekdetail > 0){
			$this->obj->db->select('DETAIL_CODE AS GANG_CODE');
			$this->obj->db->from('m_user_per_detail');
			$this->obj->db->where('LOGINID', $user);
			$this->obj->db->where('COMPANY_CODE', $company);
			$this->obj->db->order_by("DETAIL_CODE", "ASC"); 
		} else {
			$this->obj->db->select('GANG_CODE');
			$this->obj->db->from('m_gang');
			$this->obj->db->where('COMPANY_CODE', $company);
			$this->obj->db->order_by("GANG_CODE", "ASC");
			if(!empty($isdu)){
				$string .= "<option value='ALL'> -- Pilih Semua -- </option>";
			}
		}              		
				
		$temp=$this->obj->db->get();
        $temp = $temp->result_array();
        $this->obj->db->close();
						
		foreach ( $temp as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['GANG_CODE']."\" selected>".$row['GANG_CODE']." </option>";
			} else {
				$string = $string." <option value=\"".$row['GANG_CODE']."\">".$row['GANG_CODE']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	/* end dropdown gangcode */
	
    //################ 31 Jan 2011 #######################
    //####################################################
    function cek_user_authentification($u_level,$act,$loginid){
        $u_level = trim($this->obj->db->escape_str($u_level));
        $loginid = trim($this->obj->db->escape_str($loginid));
        $act = strtoupper(trim($act));
        
        $query = "SELECT ROLE_ADD, ROLE_EDIT, ROLE_DELETE, ROLE_REPORT, ROLE_APPROVE, ROLE_REOPEN
                    FROM m_user_list_grole 
                    WHERE LOGINID='".$loginid."'";
        
        $sQuery = $this->obj->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }else{
            $sQuery->free_result();
            $query = "SELECT ROLE_ADD, ROLE_EDIT, ROLE_DELETE, ROLE_REPORT, ROLE_APPROVE
                    FROM m_user_grole 
                    WHERE USER_GROUP_ID='".$u_level."'"; 
             $sQuery = $this->obj->db->query($query);
             $rowcount=$sQuery->num_rows();
            
             $temp_result = array();
             if(!empty($rowcount)){
                foreach ( $sQuery->result_array() as $row )
                {
                    $temp_result[] = $row;
                }
             }   
        }            
        return $temp_result;    
    }
    
    function createID_Nota($namaTable,$namaPK,$begID,$company,$dateCols,$trans_date)
    { 
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $date = $year.$month.$day;
        
        $this->obj->db->select_max($namaPK);
        $this->obj->db->from($namaTable);
        $wArray = array("COMPANY_CODE" => $company, "DATE_FORMAT(".$dateCols.",'%Y%m')" => date('Y',strtotime($trans_date)).date('m',strtotime($trans_date)),
                    "LEFT(".$namaPK.",CHAR_LENGTH('".$company."'))" => $company);
        $this->obj->db->where($wArray);
        
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
        $hasil = $begID.$date;
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."0001";
        }
        
        else
        {
            $str = $temp[0][$namaPK];
            $str = substr($str,strlen($company)+9,strlen($company)+12);
            $str = $str+1;
            
            $panjangString = 4;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .= "0";
            }
            
            $hasil .= $str;
        }
        $final_fixID = $this->cek_id_exist($namaTable,$namaPK,$hasil,strlen($begID.$date),$company);
        return $final_fixID;
       // return $hasil;
    }
    
    function createMy_ID($namaTable,$namaPK,$begID,$dateCols,$company){
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $date = $year.$month.$day;

        $this->obj->db->select_max($namaPK);
        $this->obj->db->from($namaTable);
        $wArray = array("COMPANY_CODE" => $company, "DATE_FORMAT(".$dateCols.",'%Y%m')" => date('Y').date('m'),
                    "LEFT(".$namaPK.",CHAR_LENGTH('".$company."'))"=> $company);
        $this->obj->db->where($wArray);
        
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
        $hasil = $begID.$date;
        //$len_awalan='';
        $len_awalan = strlen($begID.$date);
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."0001";
        }
        
        else
        {
            //$len_awalan = strlen($begID.$date);
            $str = $temp[0][$namaPK];
            $str = substr($str,$len_awalan,4); 
            $str = $str+1;

            $panjangString = 4;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .= "0";
            }
            
            $hasil .= $str;
        }
       $final_fixID = $this->cek_id_exist($namaTable,$namaPK,$hasil,$len_awalan,$company);
       return $final_fixID;//substr($temp[0][$namaPK],$len_awalan,$len_awalan+3);
    }
    
    private function cek_id_exist($namaTable,$namaPK,$ID,$len_awalan,$company){
        if (trim($namaTable)=='s_nota_angkutbuah') {
            $query = "SELECT ".trim($namaPK)." FROM ".$namaTable." WHERE ".trim($namaPK)."='".$company.$ID."' ";    
        }else{
            $query = "SELECT ".trim($namaPK)." FROM ".$namaTable." WHERE ".trim($namaPK)."='".$ID."' ";        
        }
        
        $sQuery = $this->obj->db->query($query);
        $row = $sQuery->row_array();
        //$tmp_id=$row[trim($namaPK)]; //LIHTBG1102010001
        
        $count = $sQuery->num_rows();
        $fix_id ='';
        $pattern=substr($ID,0,$len_awalan);

        if($count>0){// if id exist, then regenerate it
            if ($namaTable=='s_nota_angkutbuah'){  
                $str = $ID;
                $patternnab=substr($ID,0,$len_awalan); 
                
                while ($fix_id==''){
                    $hasil=$patternnab;
                    $str = substr($str,strlen($company)+9,4);//substr($str,$len_awalan,$len_awalan+3); 
                    $str = $str+20;
                    $panjangString = 4;
                    $jumlahNol = $panjangString - strlen($str);
                    
                    for($i =0;$i<$jumlahNol;$i++)
                    {
                        $hasil .= "0";
                    } 
                    $hasil .= $str;
                    
                    $query = "SELECT ".$namaPK." FROM ".$namaTable." WHERE ".$namaPK."='".$company.$hasil."' ";
                    $sQuery = $this->obj->db->query($query);
                    $row = $sQuery->row_array();
                    
                    $count2 = $sQuery->num_rows();
                    if($count2<=0){
                        $fix_id =$hasil; 
                    }//else{
                       // $fix_id =$hasil;
                    //} 
                    $str = $hasil;
                }
                   
            }else{       
                $str = $ID;//LIHTBG1102010001
                while ($fix_id==''){
                    $hasil=$pattern;
                    $str = substr($str,$len_awalan,4); 
                    $str = $str+20;
                    $panjangString = 4;
                    $jumlahNol = $panjangString - strlen($str);
                    
                    for($i =0;$i<$jumlahNol;$i++)
                    {
                        $hasil .= "0";
                    } 
                    $hasil .= $str;
                    
                    $query = "SELECT ".$namaPK." FROM ".$namaTable." WHERE ".$namaPK."='".$hasil."' ";
                    $sQuery = $this->obj->db->query($query);
                    $row = $sQuery->row_array();
                    
                    $count = $sQuery->num_rows();
                    if($count<=0){
                        $fix_id =$hasil; 
                    } 
                    $str = $hasil;
                }    
            }
               
        }else{
            $fix_id=$ID;    
        }
        return $fix_id;     
    }
    
    function gen_datetime(){
        $time = 'DATETIME';
        $time = str_replace(
                array('DATETIME','DATE','TIME','YEAR'),
                array('DATE TIME','Y-m-d','H:i:s','Y'),
                $time
            );
        return date($time,time());
    }
    //####################################################
    //####################################################
    
    function createID($namaTable,$namaPK,$begID)
    {
        
        
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $date = $year.$month.$day;
        
        
        
        $this->obj->db->select_max($namaPK);
        $this->obj->db->from($namaTable);
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
        $hasil = $begID.$date;
        
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."0001";
        }
        
        else
        {
            $str = $temp[0][$namaPK];
            $str = substr($str,9,12);
            $str = $str+1;
            
            $panjangString = 4;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .= "0";
            }
            
            $hasil .= $str;
        }
        
        return $hasil;
    }
    
    
    function id_GAD($namaTable,$namaPK,$begID)
    {
        
        $hasil = $begID;
        
        $this->obj->db->select_max($namaPK);
        $this->obj->db->like($namaPK, $hasil, 'after'); 
        $this->obj->db->from($namaTable);
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."0001";
        }
        
        else
        {
            $str = $temp[0][$namaPK];
            $length = strlen($str) - 4;
            $str = substr($str,$length,strlen($str));
            $str = $str+1;
            
            
            $panjangString = 4;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .= "0";
            }
            
            $hasil .= $str;
        }
        
        return $hasil;
    }
        
    function id_BK($namaTable,$namaPK,$begID)
    {
        
        
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $date = $year.$month.$day;
        
        $hasil = $begID.$date;
        
        $this->obj->db->select_max($namaPK);
        $this->obj->db->like($namaPK, $hasil, 'after'); 
        $this->obj->db->from($namaTable);
        
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
    
        
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."0001";
        }
        
        else
        {
            $str = $temp[0][$namaPK];
            $length = strlen($str) - 4;
            $str = substr($str,$length,strlen($str));
            $str = $str+1;
            
            $panjangString = 4;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .= "0";
            }
            
            $hasil .= $str;
        }
        
        return $hasil;
    }
    
    function createID_vehicle($namaTable,$namaPK,$begID)
    {
        
        
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $date = $year.$month.$day;
        
        
        
        $this->obj->db->select_max($namaPK);
        $this->obj->db->from($namaTable);
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
        $hasil = $begID.$date;
        
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."0001";
        }
        
        else
        {
            $str = $temp[0][$namaPK];
            $str = substr($str,9,12);
            $str = $str+1;
            
            $panjangString = 4;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .= "0";
            }
            
            $hasil .= $str;
        }
        
        return $hasil;
    }

    function createTicketID($namaTable,$namaPK,$begID)
    {        
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $date = $year.$month;
        
        $this->obj->db->select_max($namaPK);
        $this->obj->db->from($namaTable);
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
        $hasil = $begID.$date;
        
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."0001";
        }
        
        else
        {
            $str_c = $temp[0][$namaPK];
            $str = substr($str_c,9,12);
            $str = $str+1;
            
            $panjangString = 4; 
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .=  "0";
            }
            
            $hasil .= $str;
        }
        
        return $hasil;
    }
    
    function createProjID($namaTable,$namaPK,$begID)
    {
        
        
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $date = $day.$month.$year;
        
        
        $this->obj->db->select_max($namaPK);
        $this->obj->db->from($namaTable);
        $temp= $this->obj->db->get();
        $this->obj->db->close();
        $temp = $temp->result_array();
        $hasil = $begID.$date;
        
        if(empty($temp[0][$namaPK]))
        {
            $hasil = $hasil."01";
        }
        
        else
        {
            $str = $temp[0][$namaPK];
            $str = substr($str,5,14);
            $str = $str+1;
            
            $panjangString = 5;
            $jumlahNol = $panjangString - strlen($str);
            
            for($i =0;$i<$jumlahNol;$i++)
            {
                $hasil .= "0";
            }
            
            $hasil = $begID.$str;
        }
        
        return $hasil;
    }
    
    function dropdownlist($nama_dropdown=null, $nama_tabel=null, $nama_tampil=null, $nama_isi=null, $column=null, $isi_column=null, $class=null, $default= null, $width=null, $onchange=null, $kolomOrder=null, $tabindex=null, $usingNull=null)
    {
    

        $this->obj->db->select('*');
        $this->obj->db->from($nama_tabel);
        if(!empty($column))
        {
            $this->obj->db->where($column, $isi_column);
        }
        if(!empty($kolomOrder))
        {
            $this->obj->db->order_by($kolomOrder, 'asc');
        }
        else
        {
            
            
            $this->obj->db->order_by($nama_tampil, 'asc');
        }
         
        $temp=$this->obj->db->get();
        $temp = $temp->result_array();
        $this->obj->db->close();

        $string = "<select  name=\"".$nama_dropdown."\" class=\"".$class."\"  id=\"".$nama_dropdown."\" tabindex='".$tabindex."' ";
        $string .= "<option value=''> -- choose -- </option>";
        if(!empty($onchange))
        {
            $string = $string." onchange=\"". $onchange."\" ";
        }
        if(isset($width))
        {
            $string = $string." style=\"width:". $width."px\" ";
        }
        $string = $string." >";
        if(isset($usingNull) && !(isset($default)))
        {
            $string = $string."<option selected></option>";
        }
        else if(isset($usingNull) && (isset($default)))
        {
            $string = $string."<option></option>";
        }
        foreach ( $temp as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row[$nama_isi]."\"  selected>".$row[$nama_tampil]." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row[$nama_isi]."\">".$row[$nama_tampil]." </option>";
            }
        }
                $string =$string. "</select>";
        return $string;
    }
    
    
    function dropdownlist2($nama_dropdown=null, $nama_tabel=null, $nama_tampil=null, $nama_isi=null, $where=null, $default= null, $width=null, $onchange=null,$class=null, $allcoll=null, $kolomOrder=null, $tabindex=null, $usingNull=null)
    {
        $this->obj->db->select('*');
        $this->obj->db->from($nama_tabel);
        if(!empty($where))
        {
            $this->obj->db->where($where);
        }
        if(!empty($kolomOrder))
        {
            $this->obj->db->order_by($kolomOrder, 'asc');
        }
        else
        {
            
            
            $this->obj->db->order_by($nama_tampil, 'asc');
        }
         
        $temp=$this->obj->db->get();
        $temp = $temp->result_array();
        $this->obj->db->close();

        $string = "<select  name=\"".$nama_dropdown."\"  id=\"".$nama_dropdown."\" class='".$class."' tabindex='".$tabindex."' ";
        
        if(!empty($onchange))
        {
            $string = $string." onchange=\"". $onchange."\" ";
        }
        if(isset($width))
        {
            $string = $string." style=\"width:". $width."px\" ";
        }
        $string = $string." >";
        $string .= "<option value=''> -- pilih -- </option>";
        if(isset($allcoll)){
            if ($allcoll = true){
                $string .= "<option value='all'> SEMUA </option>";
            }
        }
        
        if(isset($usingNull) && !(isset($default)))
        {
            $string = $string."<option selected></option>";
        }
        else if(isset($usingNull) && (isset($default)))
        {
            $string = $string."<option></option>";
        }
        foreach ( $temp as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row[$nama_isi]."\"  selected>".$row[$nama_tampil]." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row[$nama_isi]."\">".$row[$nama_tampil]." </option>";
            }
        }
                $string =$string. "</select>";
        return $string;
    }
    
    function drop_date2($nmonth, $nyear,$class, $useDate=0, $onchange="")
    {
            $monthName = array(1=> "Januari", "Februari", "Maret", 
            "April", "Mei", "Juni", "Juli", "Agustus", 
            "September", "Oktober", "November", "Desember"); 

        if($useDate == 0) { 
            $useDate = time(); 
        } 
         
        $bulan = "";
        $tahun = "";
        
        $bulan .= "<SELECT NAME=" . $nmonth . " style='width:100px;' onchange='".$onchange."' class = ". $class ." id=" . $nmonth . ">\n"; 
        for($currentMonth = 1; $currentMonth <= 12; $currentMonth++) 
        { 
            
            $bulan .= "<OPTION VALUE=\""; 
            if (strlen($currentMonth) < '2')
            {
            $currentMonths = "0".$currentMonth;
            } else {
            $currentMonths = $currentMonth;    
            }
            $bulan .= $currentMonths; 
            $bulan .= "\""; 
            IF(intval(DATE( "m", $useDate))==$currentMonth) 
            { 
                $bulan .= " SELECTED"; 
            } 
            $bulan .= ">" . $monthName[$currentMonth] . "\n"; 
        } 
        $bulan .= "</SELECT>"; 
 
        $tahun .= "<SELECT NAME=" . $nyear . " style='width:70px;' class=".$class." id=" . $nyear . ">\n"; 
        $startYear = date( "Y", $useDate); 
        for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++) 
        { 
            $tahun .= "<OPTION VALUE=\"$currentYear\""; 
            if(date( "Y", $useDate)==$currentYear) 
            { 
                $tahun .= " SELECTED"; 
            } 
            $tahun .= ">$currentYear\n"; 
        } 
        $tahun .= "</SELECT>"; 
        
        return $bulan."&nbsp;".$tahun;

    }    
    
	/* #### update 28 April 2012 
			author : ridhu
			fungsi : tambah dropdown tahun 
	#### */
	function drop_year($nyear,$class, $useDate=0)
    {
        if($useDate == 0){ 
            $useDate = time(); 
        } 
         
        $tahun = "";
        $tahun .= "<SELECT NAME=" . $nyear . " style='width:70px; min-height:25px;' class=".$class." id=" . $nyear . ">\n"; 
        $startYear = date( "Y", $useDate); 
        for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++){ 
            $tahun .= "<OPTION VALUE=\"$currentYear\""; 
            if(date( "Y", $useDate)==$currentYear){ 
                $tahun .= " SELECTED"; 
            } 
            $tahun .= ">$currentYear\n"; 
        } 
        $tahun .= "</SELECT>"; 
        return $tahun;
    }  
	
    function drop_date($nmonth, $nyear, $class, $onchange, $useDate=0)
    {
        $monthName = array(1=> "Januari", "Februari", "Maret", 
            "April", "Mei", "Juni", "Juli", "Agustus", 
            "September", "Oktober", "November", "Desember"); 
			
		$event = "";
		
        if($useDate == 0) {
        	$useDate = time(); 
        } 
        
		if($onchange == 0) { 
			/* dihilangkan saja biar bisa jalan onchangenya */
			/* ridhu 2013-04-18 */
            //$onchange = ""; 
        }
		
		//echo $onchange;
		
        $bulan = "";
        $tahun = "";
        
  		$bulan .= "<SELECT NAME='" . $nmonth . "' style='width:100px;' class = '". $class ."' onChange = '" .$onchange. "' id='" . $nmonth . "'>\n"; 
        for($currentMonth = 1; $currentMonth <= 12; $currentMonth++) 
        { 
            
            $bulan .= "<OPTION VALUE=\""; 
            if (strlen($currentMonth) < '2')
            {
            $currentMonths = "0".$currentMonth;
            } else {
            $currentMonths = $currentMonth;    
            }
            $bulan .= $currentMonths; 
            $bulan .= "\""; 
            IF(intval(DATE( "m", $useDate))==$currentMonth) 
            { 
                $bulan .= " SELECTED"; 
            } 
            $bulan .= ">" . $monthName[$currentMonth] . "\n"; 
        } 
        $bulan .= "</SELECT>"; 
 
        $tahun .= "<SELECT NAME='" . $nyear . "' style='width:70px;' class='".$class."' onChange = '" .$onchange. "' id='" . $nyear . "'>\n"; 
        $startYear = date( "Y", $useDate); 
        for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++) 
        { 
            $tahun .= "<OPTION VALUE=\"$currentYear\""; 
            if(date( "Y", $useDate)==$currentYear) 
            { 
                $tahun .= " SELECTED"; 
            } 
            $tahun .= ">$currentYear\n"; 
        } 
        $tahun .= "</SELECT>"; 
        
        return $bulan."&nbsp;".$tahun;

    } 
    
    
    function dateDiff($interval,$dateTimeBegin,$dateTimeEnd) {
        //Parse about any English textual datetime
        //$dateTimeBegin, $dateTimeEnd
        
        $dateTimeBegin=strtotime($dateTimeBegin);
        if($dateTimeBegin === -1) {
        return("..begin date Invalid");
        }
        
        $dateTimeEnd=strtotime($dateTimeEnd);
        if($dateTimeEnd === -1) {
        return("..end date Invalid");
        }
        
        $dif=$dateTimeEnd - $dateTimeBegin;
        
        switch($interval) {
        case "s"://seconds
        return($dif);
        
        case "n"://minutes
        return(floor($dif/60)); //60s=1m
        
        case "h"://hours
        return(floor($dif/3600)); //3600s=1h
        
        case "d"://days
        return(floor($dif/86400)); //86400s=1d
        
        case "ww"://Week
        return(floor($dif/604800)); //604800s=1week=1semana
        
        case "m": //similar result "m" dateDiff Microsoft
        $monthBegin=(date("Y",$dateTimeBegin)*12)+
        date("n",$dateTimeBegin);
        $monthEnd=(date("Y",$dateTimeEnd)*12)+
        date("n",$dateTimeEnd);
        $monthDiff=$monthEnd-$monthBegin;
        return($monthDiff);
        
        case "yyyy": //similar result "yyyy" dateDiff Microsoft
        return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));
        
        default:
        return(floor($dif/86400)); //86400s=1d
        }
        }
		
	/* function global PMS */
	function create_nopengajuan($namaTable,$namaPK,$begID)
	{
		$hasil = $begID;
		$this->obj->db->select_max($namaPK);
		$this->obj->db->like($namaPK, $hasil, 'after'); 
		$this->obj->db->from($namaTable);
		$temp= $this->obj->db->get();
		$this->obj->db->close();
		$temp = $temp->result_array();
		
		if(empty($temp[0][$namaPK]))
		{
			$hasil = $hasil."001";
		} else {
			$str = $temp[0][$namaPK];
			$length = strlen($str) - 3;
			$str = substr($str,$length,strlen($str));
			$str = $str+1;
			
			$panjangString = 3;
			$jumlahNol = $panjangString - strlen($str);
			
			for($i =0;$i<$jumlahNol;$i++)
			{
				$hasil .= "0";
			}
			$hasil .= $str;
		}
		
		return $hasil;
	}
	
	function create_nopj($namaTable,$namaPK,$begID)
	{
		$hasil = $begID;
		$this->obj->db->select_max($namaPK);
		$this->obj->db->like($namaPK, $hasil, 'after'); 
		$this->obj->db->from($namaTable);
		$temp= $this->obj->db->get();
		$this->obj->db->close();
		$temp = $temp->result_array();
		
		if(empty($temp[0][$namaPK]))
		{
			$hasil = $hasil."00001";
		} else {
			$str = $temp[0][$namaPK];
			$length = strlen($str) - 5;
			$str = substr($str,$length,strlen($str));
			$str = $str+1;
			
			$panjangString = 5;
			$jumlahNol = $panjangString - strlen($str);
			
			for($i =0;$i<$jumlahNol;$i++)
			{
				$hasil .= "0";
			}
			$hasil .= $str;
		}
		
		return $hasil;
	}
}


?>