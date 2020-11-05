<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Func_table
{
	 var $obj;
	//buat table
	var $data;
	var $base_url;
	var $url_segment;
	
	var $header;
	var $header_order;
	var $nama_column;
	var $nama_header_export_coloumn;
	var $nama_export_coloumn;
	var $nama_session;
	var $table_width;
	var $table_judul;
	
	//edit
	var $using_edit;
	var $path_edit;
	var $column_edit;
	var $column_edit2;
	var $using_delete;
	var $path_delete;
	var $column_delete;
	var $column_delete2;
	var $using_view;
	var $path_view;
	var $column_view;
	var $column_view2;
	var $using_check;
	var $path_check;
	var $column_check;
	
	
	var $using_check2;
	var $path_check2;
	var $column_check2;
	var $download_excel;
	var $download_print;
	
	
	var $minimal_model;
	var $big_record_mode;
	
	function Func_table()
	{
		$this->obj =& get_instance();
		$this->reset();
		//$this->obj->load->lib('func_excel');
		
	}
	
	function reset()
	{
		
		$this->using_view=0;
		$this->using_edit=0;
		$this->using_delete=0;
		$this->using_check=0;
		$this->using_check=0;
		$this->minimal_model=false;
		$this->big_record_mode=false;
		$this->column_view=null;
		$this->column_view2=null;
		$this->download_excel= false;
		$this->download_print= false;
		$this->table_judul="";
		if(isset($this->header))
		{
			
			$this->header= array();
			
		}
		$this->nama_header_export_coloumn=array();
		$this->nama_export_coloumn=array();
		$this->nama_column  =array();
		$this->header_order=array();
		$this->data = array();
	}
	
	function set_post_config($post)
	{
		
		if(isset($post['post_'.$this->nama_session]))
		{
			if(isset($post['pageSize']))
			{
				$this->set_per_page_upadate($post['pageSize']);
			}
			if(isset($post['page_go']))
			{
				$this->set_halaman_paging($post['page_go']);
			}
			if(isset($post["ekspor_excel_".$this->nama_session]))
			{
				$this->download_excel= $post["ekspor_excel_".$this->nama_session];
			}
			if(isset($post["ekspor_print_".$this->nama_session]))
			{
				$this->download_print= $post["ekspor_print_".$this->nama_session];
			}
		}
	} 
	
	function set_judul($judul)
	{
		$this->table_judul=$judul;
	}
	
	function set_width_table()
	{
		$i=0;
		if($this->using_check)
		{
				
				$this->table_width[$i] ="2%";
				$i++;
		}
		if($this->using_check2)
		{		
				$this->table_width[$i] ="2%";
				$i++;
		}
		for( $j=0; $j < func_num_args(); $j++,$i++) {
  				$this->table_width[$i] = func_get_arg($j);
  		}
		if($this->using_view)
		{		
				$this->table_width[$i] ="2%";
				$i++;
		}
		if($this->using_edit)
		{		
				$this->table_width[$i] ="2%";
				$i++;
		}
		if($this->using_delete)
		{		
				$this->table_width[$i] ="5%";
				$i++;
		}
	}
	
	function set_using_edit($path="", $nama_column=null, $nama_column2=null)
	{
		$this->column_edit= $nama_column;
		$this->column_edit2= $nama_column2;
		$this->path_edit= $path;
		$this->using_edit = 1;
	}
	
	function set_using_delete($path= "", $nama_column=null, $nama_column2=null)
	{
		$this->column_delete = $nama_column;
		$this->column_delete2 = $nama_column2;
		$this->path_delete =$path;
		$this->using_delete= 1;
	}
	
	function set_using_view($path= "", $nama_column=null, $nama_column2=null)
	{
		$this->column_view = $nama_column;
		$this->column_view2 = $nama_column2;
		$this->path_view =$path;
		$this->using_view= 1;
	}

	function set_using_radio($path= null, $nama_column="")
	{
		$this->column_check = $nama_column;
		$this->path_check =$path;
		$this->using_check= 1;
	}
	
	function set_using_check($path= null, $nama_column="")
	{
		$this->column_check2 = $nama_column;
		$this->path_check2 =$path;
		$this->using_check2= 1;
	}
	
	
	function set_nama_session($nama)
	{
		$this->nama_session= $nama;
	}
	
	function cek_valid_order($nilai)
	{
	
		foreach($this->header_order as $row)
		{		
				if($nilai==$row)
				{
					return true;
				}
		}
		return false;
	}
	
	
	function set_order( $nilai)
	{
		//nama nya benar tidak
		if($this->cek_valid_order($nilai))
		{
			$_SESSION[$this->nama_session.'order']=$nilai;
				if(!isset($_SESSION[$this->nama_session.'order_before']))
				{
					$_SESSION[$this->nama_session.'order_before']="";
					$_SESSION[$this->nama_session.'order_type']="desc";//descending or ascending
				}
				if($_SESSION[$this->nama_session.'order']== $_SESSION[$this->nama_session.'order_before'])
				{
					if($_SESSION[$this->nama_session.'order_type']=="desc")
					{
						$_SESSION[$this->nama_session.'order_type']="asc";
					}
					else
					{
						$_SESSION[$this->nama_session.'order_type']="desc";
					}
				}
				else if($_SESSION[$this->nama_session.'order']!= $_SESSION[$this->nama_session.'order_before'])
				{
					$_SESSION[$this->nama_session.'order_type']="desc";
				}
				
				$_SESSION[$this->nama_session.'order_before']=$_SESSION[$this->nama_session.'order'];
		}
	}
	
	function get_order()
	{
		if(isset($_SESSION[$this->nama_session.'order']) )
			return $_SESSION[$this->nama_session.'order'];
		else null;
	}
	function get_order_type()
	{
		if(isset($_SESSION[$this->nama_session.'order_type']))
			return $_SESSION[$this->nama_session.'order_type'];
		else null;
	}
	
	
	//remember page 2 doesn't mean that you are in page 3 but you are in page which containning data row  3
	function set_page($page)
	{	
		if(is_numeric($page)) 
		{
		 	$_SESSION[$this->nama_session.'page']= $page;
		}
	}
	
	function get_page()
	{
		
		if(isset($_SESSION[$this->nama_session.'page']))
		{
			//watch this part, 
			if($_SESSION[$this->nama_session.'page']> $this->get_total_data())
			{
				$this->set_page("0");
				return '0';
			}
		 	return $_SESSION[$this->nama_session.'page'];
		}
		else
			{
			$this->set_page("0");
			return '0';
			}
	}
	function get_per_page()
	{
		if(isset($_SESSION[$this->nama_session.'per_page']))
		 	return $_SESSION[$this->nama_session.'per_page'];
		else
			{
				$this->set_per_page("5");
				return '5';
			}
	}
	
	function get_minimal_model()
	{	
		return $this->minimal_model;
	}
	
	function set_big_record_mode($nilai=false)
	{
		$this->big_record_mode= $nilai;
	}
	
	
	function set_halaman_paging($halaman)
	{
		//$total = count($this->data);
		$total = $this->get_total_data();
		$page=$this->get_page();
		$durasi=$this->get_per_page();
		$jumlah_halaman = ceil ($total / $durasi);
		
		$page = ($halaman-1) * $this->get_per_page();
		if(($halaman <= $jumlah_halaman)&& ($halaman>0))
		{
			$this->set_page($page);
		}
	}
	
	
	function set_total_data($value)
	{
		$_SESSION[$this->nama_session.'total'] =$value;
	}
	function get_total_data()
	{
		
		if(isset($_SESSION[$this->nama_session.'total']))
		{
			 return $_SESSION[$this->nama_session.'total'];
		}
		else
		{
			return 10000;
		}
	}
	
	
	
	function set_data($isi_data)
	{
		if(!$this->big_record_mode)
		{
			$this->set_total_data(count($isi_data));
		}
		$this->data = $isi_data;
	}
	//ini untuk base paging and order
	
	function set_url($url)
	{
		$this->base_url= $url;
	}
	function set_url_segment($segment)
	{
		$this->url_segment= $segment;
	}
	//ini jumlah record per page
	function set_per_page($page)
	{
		if(is_numeric($page)) 
		{ 
			if(!isset($_SESSION[$this->nama_session.'per_page']))
			{
				$_SESSION[$this->nama_session.'per_page']= $page;
			}
		}
	}
	function set_per_page_upadate($page)
	{
		if(is_numeric($page)) 
		{ 
			
			$_SESSION[$this->nama_session.'per_page']= $page;
			
		}
	}
	
	function set_header()
	{
		for($i = 0 ; $i < func_num_args(); $i++) {
  				$this->header[$i] = func_get_arg($i);
  		}
	}
	
	function get_header()
	{
		return $this->header;
	}
	function set_header_order()
	{
		
		for($i = 0 ; $i < func_num_args(); $i++) {
  				$this->header_order[$i] = func_get_arg($i);
  		}
	}
	
	function set_nama_header_export_coloumn()
	{
		for($i = 0 ; $i < func_num_args(); $i++) {
  				$this->nama_header_export_coloumn[$i] = func_get_arg($i);
  		}
	}
	
	function get_nama_header_export_coloumn()
	{
		return $this->nama_header_export_coloumn;
	}
	
	function set_nama_export_coloumn()
	{
		for($i = 0 ; $i < func_num_args(); $i++) {
  				$this->nama_export_coloumn[$i] = func_get_arg($i);
  		}
	}
	
	function get_nama_export_coloumn()
	{
		return $this->nama_export_coloumn;
	}
	
	
	function set_nama_column()
	{
		for($i = 0 ; $i < func_num_args(); $i++) {
  				$this->nama_column[$i] = func_get_arg($i);
  		}
	}
	
	function get_nama_coloumn()
	{
		return $this->nama_column;
	}
	
	function set_minimal_model($nilai=true)
	{
		$this->minimal_model= $nilai;
	}
	
	
	
	function generate( )
	{
	
		//jika dimaui excel
		if($this->download_excel)
		{
			$this->make_excel();
			
		}
		
		if($this->download_print)
		{
			$this->make_print();
			
		}
		//paging
		$isi_data = $this->data;
		$config['base_url'] = base_url().'index.php/'.$this->base_url.'/page/';
		if($this->big_record_mode)
		{
			$config['total_rows'] = $this->get_total_data();
		}
		else
		{
			$config['total_rows'] = count($isi_data);
		}
		$config['per_page'] =$this->get_per_page() ; 
		$config['uri_segment'] = $this->url_segment;
		$config['cur_page'] = $this->get_page();
		
		$this->obj->paging->initialize($config); 
		$paging=  $this->obj->paging->create_links();
		$this->obj->table->clear();
		$this->obj->table->set_using_footer('1');//using footer
		//style
		$this->obj->table->set_template($this->get_theme_table()) ;
		if(!$this->big_record_mode)
		{
			$this->set_total_data(count($this->data));
		}
		//cek  ada isinya atau tidak// saat ini code tidak terpakai
		/*
		$terdapat_data= 1;
		foreach($isi_data as $row)
		{
			foreach($this->nama_column as $column)
			{		
					if(isset($row[$column]))
					{
						//$terdapat_data= 1;
					}
					else 
					{
						$terdapat_data= 0;
					}
			}
		}
		*/
				
				
		if(($isi_data!= null) && (!empty($isi_data)) )
		{
			//bikin heading
			
			
				$awal_link= "<a href='".base_url().'index.php/'.$this->base_url."/order/";
				$tengah =" '><img src='".base_url()."public/images/arr.jpg'></a> ";
				$penutup_link =" ";
		
				$alt_awal_link= "<a href='".base_url().'index.php/'.$this->base_url."/";
				
			
			$link = array();
			$i=0;
		
			//if using view
			if($this->using_check)
			{
				$link[$i] =" ";
				$i++;
			}
			//if using view
			if($this->using_check2)
			{
				$link[$i] =" ";
				$i++;
			}
			$j=0;
			foreach ($this->header as $row)
			{
				if($this->minimal_model)
				{
					$link[$i]=$row;
				}
				else if($this->header_order[$j]!="")
				{
					$link[$i]=$awal_link.$this->header_order[$j].$tengah.$row.$penutup_link;
				}
				else
				{
					$link[$i]=$alt_awal_link.$this->header_order[$j].$tengah.$row.$penutup_link;
				}
				$i++;
				$j++;
			}
			//if using view
			if($this->using_view)
			{
				$link[$i] =" ";
				$i++;
			}
			//if using edit
			if($this->using_edit)
			{
				$link[$i] =" ";
				$i++;
			}
			//if using delete
			if($this->using_delete)
			{
				$link[$i] =" ";
				$i++;
			}
			
			
			$this->obj->table->set_heading($link);
			$this->obj->table->set_width_table($this->table_width);
			//bikin isian tablenya
			$i=0;
			$page=$this->get_page();
			$durasi=$this->get_per_page();
			$min = $page;
			$max = $page+$durasi;			
			foreach($isi_data as $row)
			{
				$isi=array();
				
				//using radio
				if($this->using_check)
				{
					$isi['check'] ="<input type=\"radio\" name=\"".$this->path_check."\"  id=\"".$this->path_check."\"  value=\"".$row[$this->column_check]."\" >";
				}
				//using check
				if($this->using_check2)
				{
					$isi['check2'] ="<input type=\"checkbox\" name=\"".$this->path_check2."\"  id=\"".$this->path_check2."\"  value=\"".$row[$this->column_check2]."\" >";
				}		
				foreach($this->nama_column as $column)
				{	
						
						if(isset($row[$column]))
						{	
							$isi[$column] = $row[$column];
						}
						else
						{
							$isi[$column] ="";
						}
				}
				//using view
				$path="";
				if(isset($this->column_view))
				{
					$path= $path.$row[$this->column_view];
				}
				if(isset($this->column_view2))
				{
					$path= $path."/".$row[$this->column_view2];
				}
				if($this->using_view)
				{
					$isi['view'] ="<a href=\"".$this->path_view.$path."\"><img title='lihat' src=\"".base_url()."public/images/act_view2.gif\"></a>";
				}
				//using edit
				$path="";
				if(isset($this->column_edit))
				{
					$path= $path.$row[$this->column_edit];
				}
				if(isset($this->column_edit2))
				{
					$path= $path."/".$row[$this->column_edit2];
				}
				if($this->using_edit)
				{
					$isi['edit'] ="<a href=\"".$this->path_edit.$path."\"><img width=\"16\"  title='ubah' src=\"".base_url()."public/images/edit2.png\"></a>";
				}
				//using delete
				$path="";
				if(isset($this->column_delete))
				{
					$path= $path.$row[$this->column_delete];
				}
				if(isset($this->column_delete2))
				{
					$path= $path."/".$row[$this->column_delete2];
				}
				if($this->using_delete)
				{
					$isi['delete'] ="<a href=\"".$this->path_delete.$path."\"><img width=\"16\" title='hapus' src=\"".base_url()."public/images/delete2.gif\"></a>";
				}
				
				if($this->minimal_model)
				{
					$this->obj->table->add_row($isi);
				}
				else
				{
					if($this->big_record_mode)
					{
						$this->obj->table->add_row($isi);
					}
					else
					{
						if(($i>= $min) && ($i<$max))
						{
							$this->obj->table->add_row($isi);
						}
					}
				}
				$i++;
			}
		}
		else if($this->minimal_model)
		{
			//bikin heading
			$i=0;
		
		
			$j=0;
			foreach ($this->header as $row)
			{
				$link[$i]=$row;
				$isi[$i]="";
				$i++;
				$j++;
			}
			//if using view
			if($this->using_view)
			{
				$link[$i] =" ";
				$isi[$i]="";
				$i++;
			}
			//if using edit
			if($this->using_edit)
			{
				$link[$i] =" ";
				$isi[$i]="";
				$i++;
			}
			//if using delete
			if($this->using_delete)
			{
				$link[$i] =" ";
				$isi[$i]="";
				$i++;
			}
			$isi[0]="[kosong]"; 
			$this->obj->table->set_heading($link);
			$this->obj->table->add_row($isi);
		}
		else
		{
			$this->obj->table->set_template($this->get_theme('foot')) ;
			$this->obj->table->set_heading(' ',' ','');
			$this->obj->table->add_row('[Tidak ditemukan]','','');
		}
		
		//genearte table
		$this->obj->table->set_empty("&nbsp;");
		$data_table= $this->obj->table->generate();
		$per_page= $this->information_per_page( $this->get_per_page());
		$information_page= $this->information_page();
		$page_go = $this->page_go();
		$string_perintah_excel =$this->string_perintah_excel();
		$string_perintah_print =$this->string_perintah_print();
		$result =  $this->atur_tampilan($data_table, $paging, $per_page, $information_page,$page_go,$string_perintah_excel,$string_perintah_print);
		return $result;
	}
	
	
	
	function get_theme_table()
	{
	
		$tmpl = array (
                    'table_open'          => '<table id="rounded-corner">',

                    'heading_row_start'   => '<thead>',
                    'heading_row_end'     => '</thead>',
                    'heading_cell_start'  => '<th scope="col" class="rounded-q1">',
                    'heading_cell_end'    => '</th>',
					'heading_left_corner_cell_start' => '<th scope="col" class="rounded-company">', 
					'heading_left_corner_cell_end'   => '</th>', 
					'heading_right_corner_cell_start' => '<th scope="col" class="rounded-q4">', 
					'heading_right_corner_cell_end'   => '</th>', 

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>',

                    'row_alt_start'       => '<tr>',
                    'row_alt_end'         => '</tr>',
                    'cell_alt_start'      => '<td>',
                    'cell_alt_end'        => '</td>',
					
					'cell_width_start'				=> ' <td',
					'cell_width_start_close'		=> ' > ',
					'cell_width_end'				=> '</td>',
					'cell_width_alt_start'			=> ' <td',
					'cell_width_alt_start_close'	=> ' >',
					'cell_width_alt_end'			=> '</td>',
					
					'foot_cell_start'		=> '<td>',
					'foot_cell_end'			=> '</td>',
					'foot_left_corner_cell_start' => '<td class="rounded-foot-left">', 
					'foot_left_corner_cell_end'   => '</td>', 
					'foot_right_corner_cell_start' => '<td class="rounded-foot-right">', 
					'foot_right_corner_cell_end'   => '</td>', 
					'foot_row_start'	 	=> '<tr>',
					'foot_row_end' 			=> '</tr>',
					'foot_start' 			=> '<tfoot>',
					'foot_end'	 			=> '</tfoot>',

                    'table_close'         => '</table>'
              );
			  
			  return $tmpl;
	}
	
	function get_theme($nama)
	{
		$tmpl['foot']  = array (
                   
                   'table_open'          => '<table id="rounded-corner">',

                    'heading_row_start'   => '<thead>',
                    'heading_row_end'     => '</thead>',
                    'heading_cell_start'  => '<th scope="col"  width="33%" class="rounded-q1">',
                    'heading_cell_end'    => '</th>',
					'heading_left_corner_cell_start' => '<th scope="col" width="33%" class="rounded-company">', 
					'heading_left_corner_cell_end'   => '</th>', 
					'heading_right_corner_cell_start' => '<th scope="col"  width="33%" class="rounded-q4">', 
					'heading_right_corner_cell_end'   => '</th>', 

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>',

                    'row_alt_start'       => '<tr>',
                    'row_alt_end'         => '</tr>',
                    'cell_alt_start'      => '<td>',
                    'cell_alt_end'        => '</td>',
					
					'cell_width_start'				=> ' <td',
					'cell_width_start_close'		=> ' > ',
					'cell_width_end'				=> '</td>',
					'cell_width_alt_start'			=> ' <td',
					'cell_width_alt_start_close'	=> ' >',
					'cell_width_alt_end'			=> '</td>',
					
					'foot_cell_start'		=> '<td style="style:text-align=center;">',
					'foot_cell_end'			=> '</td>',
					'foot_left_corner_cell_start' => '<td class="rounded-foot-left">', 
					'foot_left_corner_cell_end'   => '</td>', 
					'foot_right_corner_cell_start' => '<td class="rounded-foot-right">', 
					'foot_right_corner_cell_end'   => '</td>', 
					'foot_row_start'	 	=> '<tr>',
					'foot_row_end' 			=> '</tr>',
					'foot_start' 			=> '<tfoot>',
					'foot_end'	 			=> '</tfoot>',

                    'table_close'         => '</table>'
              );
			  
			  if(isset($tmpl[$nama]))
			  	return $tmpl[$nama];
			  return null;
	}
	
	
	
	function information_per_page($value="")
	{
		$string = " Informasi / Halaman ";
		$string .= "<select name=\"pageSize\" onChange=\"refresh_". $this->nama_session."()\" class=\"menu\">";
		$string .= "<option ".$this->selected("3",$value).">3</option> ";
		$string .= "<option  ".$this->selected("5",$value).">5</option> ";
		$string .= "<option  ".$this->selected("10",$value).">10</option> ";
		$string .= "<option  ".$this->selected("20",$value).">20</option> ";
		$string .= "<option  ".$this->selected("30",$value)." >30</option> ";
		$string .= "<option  ".$this->selected("100",$value).">100</option> ";
		$string .= "<option  ".$this->selected("200",$value).">200</option> ";
		$string .= "</select> ";
		
		return $string;
	}
	
	function information_page()
	{
		$total = count($this->data);
		$page=$this->get_page();
		$durasi=$this->get_per_page();
		$min = $page+1;
		$max = $page+$durasi;
		if($total<=$max)
		{
			$max= $total;
		} 
		if($total ==0)
		{
			$min= "0";
			return "";
		}
		if($this->big_record_mode)
		{
			
			
			if($total<=$max)
			{
				$max= $min+$total-1;
			}
			$string =  "informasi ". $min. "-" .$max ." dari ".$this->get_total_data();
		}
		else
		{
			$string =  "informasi ". $min. "-" .$max ." dari ".$total;
		}
		return $string;
	}
	
	function selected($value1, $value2)
	{
		if($value1== $value2)
			return "selected" ;
		else return "";
	}
	
	function page_go()
	{
		//Halaman   dari 2
	
		$string="";
		$total = count($this->data);
		$page=$this->get_page();
		$durasi=$this->get_per_page();
		$jumlah_halaman = ceil ($total / $durasi);
		$halaman_sekarang=floor($page/ $durasi)+1;
		
		if($jumlah_halaman>0)
		{
			$string = "Halaman <input type=\"text\" value=\"".$halaman_sekarang."\"size=\"2\" name=\"page_go\" >  <img src=\"".base_url()."public/images/go.jpg\" onClick=\"refresh_". $this->nama_session."()\" >  ";
			$string .= " dari ".$jumlah_halaman;
		}
		return $string;
	}
	
	function string_perintah_excel()
	{
		return "<input type=\"hidden\" name=\"ekspor_excel_".$this->nama_session."\" id=\"ekspor_excel_".$this->nama_session."\"  value=\"0\"> <a href=\"\" onclick=\"excel_".$this->nama_session."();return false;\">Ekspor ke Excel &nbsp<img src=\"".base_url()."/public/images/exportxls.gif\" ></a> &nbsp ";
	}
	
	function string_perintah_print()
	{
		return "<input type=\"hidden\" name=\"ekspor_print_".$this->nama_session."\" id=\"ekspor_print_".$this->nama_session."\"  value=\"0\"> <a href=\"\" onclick=\"print_".$this->nama_session."();return false;\">Print &nbsp<img src=\"".base_url()."/public/images/print3.jpg\" ></a> &nbsp ";
	}
	
	function atur_tampilan($table ="", $paging="", $per_page="", $information_page="", $page_go="",$string_perintah_excel="",$string_perintah_print="")
	{
		if($this->minimal_model)
		{
			return $table;
			
		}
		$string = "
		<script type=\"text/javascript\"> 
	
		
		function refresh_".$this->nama_session."() {
		var t_excel = document.getElementById(\"ekspor_excel_".$this->nama_session."\");
		var t_print = document.getElementById(\"ekspor_print_".$this->nama_session."\");
		t_excel.value=0;
		t_print.value=0;
		with (document.".$this->nama_session."inputForm) {
			submit();
		}
		}
		
		function excel_".$this->nama_session."() {
		var t_print = document.getElementById(\"ekspor_print_".$this->nama_session."\");
		t_print.value=0;
		var t_excel = document.getElementById(\"ekspor_excel_".$this->nama_session."\");
		t_excel.value=1;
		with (document.".$this->nama_session."inputForm) {
			submit();
		}
		}
		
		function print_".$this->nama_session."() {
		var t_print = document.getElementById(\"ekspor_print_".$this->nama_session."\");
		t_print.value=1;
		var t_excel = document.getElementById(\"ekspor_excel_".$this->nama_session."\");
		t_excel.value=0;
		with (document.".$this->nama_session."inputForm) {
			submit();
		}
		}
		
		</script>
		";
		$string .=  form_open($this->base_url, array('id' => $this->nama_session.'inputForm', 'name'=> $this->nama_session.'inputForm'));
		$string .=  "<input name=\"post_".$this->nama_session."\" type=\"hidden\" value=\"1\">";
		$string .= " <table width=\"100%\">";
		$string .= " <tr>";
		$string .= " <td width=\"50%\" align=\"left\" class=\"paging\">".$information_page."</td><td width=\"50%\" align=\"right\" class=\"paging\"> $string_perintah_excel $string_perintah_print". $per_page."</td>";
		$string .= " </tr>";
		$string .= " </table>";
		$string .= $table;
		$string .= " <table  width=\"100%\">";
		$string .= " <tr>";
		$string .= " <td align=\"left\"  class=\"paging\">".$page_go."</td><td align=\"right\">";
		if(!empty($paging))
		{
			$string .= "  <div class=\"paging\" >Halaman ". $paging." </div> ";
			
		}
		$string .="</td>";
		$string .= " </tr>";
		$string .= " </table>";
		
		
		$string .= form_close();
		//$string .= 'lalalala'.$this->get_page();
		return $string;
	}
	
	function make_excel()
	{
			if(isset($this->obj->func_excel))
			{
				//$data['judul'] ="Mutasi UPT";
				$param=array();
				if( (isset($this->table_judul)) && (!empty($this->table_judul))    )
				{
					$param['judul'] =$this->table_judul;
				}
				$header_excel =$this->get_header();
				if((isset($this->nama_header_export_coloumn)) && (!empty($this->nama_header_export_coloumn)) )
				{
					
					$this->obj->func_excel->set_header($this->nama_header_export_coloumn);
				}
				else
				{
					$this->obj->func_excel->set_header($header_excel);
				}	
				$nama_coloumn = $this->get_nama_coloumn();
				if((isset($this->nama_export_coloumn)) && (!empty($this->nama_export_coloumn)) )
				{
				
					$this->obj->func_excel->set_nama_column($this->nama_export_coloumn);
				}
				else
				{
					
					$this->obj->func_excel->set_nama_column($nama_coloumn);
				}
				$this->obj->func_excel->export_excel($this->data,$param);
			}
	}
	
	function make_print()
	{
		if(isset($this->obj->func_excel))
			{
				//$data['judul'] ="Mutasi UPT";
				$param=array();
				if( (isset($this->table_judul)) && (!empty($this->table_judul))    )
				{
					$param['judul'] =$this->table_judul;
				}
				$header_excel =$this->get_header();
				if((isset($this->nama_header_export_coloumn)) && (!empty($this->nama_header_export_coloumn)) )
				{
					
					$this->obj->func_excel->set_header($this->nama_header_export_coloumn);
				}
				else
				{
					$this->obj->func_excel->set_header($header_excel);
				}	
				$nama_coloumn = $this->get_nama_coloumn();
				if((isset($this->nama_export_coloumn)) && (!empty($this->nama_export_coloumn)) )
				{
				
					$this->obj->func_excel->set_nama_column($this->nama_export_coloumn);
				}
				else
				{
					
					$this->obj->func_excel->set_nama_column($nama_coloumn);
				}
				$this->obj->func_excel->export_to_printer($this->data,$param);
			}
	}
	
	function calculate_limit()
	{
		$offset=$this->get_page();
		$jumlah_row = $this->get_per_page();
		return " limit ".$offset.", ".$jumlah_row." ";
	}
}
?>