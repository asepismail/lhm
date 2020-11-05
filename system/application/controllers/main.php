<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main extends Controller {

	function Main()
	{
		parent::Controller();
		
		$this->load->model( 'model_dashboard' );
		$this->load->model( 'model_rpt' ); 
		$this->load->helper('form');
	
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->library('Fusioncharts') ;
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
 
		$this->swfCharts  = base_url().'public/flash/Pie2D.swf' ;
		$this->swf2DBarCharts  = base_url().'public/flash/Column2D.swf' ;
		$this->swfColumn3DChartsLine  = base_url().'public/flash/MSColumnLine3D.swf' ;
		$this->swfMSArea  = base_url().'public/flash/MSLine.swf' ;
		$this->swfLine  = base_url().'public/flash/Line.swf' ;
		$this->swfColumn3DCharts  = base_url().'public/flash/MSColumn3D.swf';
		$this->swfpie3D  = base_url().'public/flash/Pie3D.swf';
		$this->MSColumn3DLineDY  = base_url().'public/flash/MSColumn3DLineDY.swf';
		$this->MS2DDY  = base_url().'public/flash/MSCombiDY2D.swf';	
		
				
	}
	
	function index()
	{	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company'] = $this->session->userdata('company');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['dest'] = $this->session->userdata('co_dest');
		$data['level'] = $this->session->userdata('level');
		
		
		if ($data['login_id'] == TRUE){
			//$data['COMPANY'] =$this->global_func->dropdownlist("COMPANY","m_company","COMPANY_NAME","COMPANY_CODE",null,null,null,null,"");
			$this->load->view('main.php', $data);
		} else {
			redirect('login');
		}

	}
	
	/* function index()
    {
		$data['COMPANY'] =$this->global_func->dropdownlist("COMPANY","m_company","COMPANY_NAME","COMPANY_CODE",null,null,null,null,"");
		//$data_row = $this->model_dashboard->tbs_chart('MIA');
		$data['table'] = $this->tampil_semua();
		$data['table_cpo'] = $this->cpo_tampil_semua();
		$data['graph'] = $this->chart_cpo();
		$data['graph2'] = $this->chart_kernel();
		$data['graph3'] = $this->chart_tbs();
		//$data['graph2'] = $this->chart_FFB_Receive();
		/*
		$i=0;
		$category="";
		$quantity="";
		foreach( $data_row as $row)
		{
		   $category .= "<category label='".$row['DATE']."' />";
		   $quantity .= "<set value='". $row['TONASE_TIMBANG']."' />";
		
		   $arrData[$i][1] = $row['DATE'];
		   $arrData[$i][2] = $row['TONASE_TIMBANG'];
		   
		   
		   $i++;
		} 	
		
		$strColumn3DXML ="<chart caption='TBS ".$row['COMPANY']." Blok ".$row['BLOK_TBS']."' labelDisplay='Rotate' slantLabels='1' shownames='1' showvalues='1' decimals='2' xAxisName='Tanggal' yAxisName='Tonase (Kg)' >";
		$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= "<dataset tooltext='TBS&lt;BR&gt;tanggal: ".$row['DATE']."&lt;BR&gt;Tonase:".$row['TONASE_TIMBANG']."' seriesName='Tonase TBS' color='AFD222' showValues='0'>" . $quantity . "</dataset>";
		*/

		/* $strColumn3DXML .= "<styles>";
		$strColumn3DXML .= "<definition>";
		$strColumn3DXML .= "<style name='myHTMLFont' type='font' isHTML='1' />";
		$strColumn3DXML .= "</definition>";
		$strColumn3DXML .= "<application>";
		$strColumn3DXML .= "<apply toObject='TOOLTIP' styles='myHTMLFont' />";
		$strColumn3DXML .= "</application>";
		$strColumn3DXML .= "</styles>";*/
		/* $strColumn3DXML .= "</chart>"; 

		$data['graph'] = $this->fusioncharts->renderChart($this->swfColumn3DCharts,'',$strColumn3DXML,"Progres", 550, 330,false, false); */

		//$this->load->view('v_dashboard', $data['graph2']);
		//$this->load->view('v_dashboard', $data['graph']);
		//$this->load->view('v_dashboard', $data);
   // }    
	
	function tampil_semua( )
	{
		
		$data_row = $this->model_rpt->tbs_inti_all();
		
		$header = "";
		$baris = "";
		$company = "";
		foreach( $data_row as $row)
		{
			
		   $baris .= "<tr>";
		   $baris .= "<td align='center'>".$row['COMPANY_CODE']."</td>";
		   $baris .= "<td align='center'>".$row['JANJANG_KEBUN']."</td>";
		   $baris .= "<td align='center'>".$row['JK_SHI']."</td>";
		   $baris .= "<td align='center'>".$row['HK']."</td>";
		   $baris .= "<td align='center'>".$row['HK_SHI']."</td>";
		   $baris .= "<td align='center'>".$row['JJG_ANGKUT']."</td>";
		   $baris .= "<td align='center'>".$row['JJG_ANGKUT_SHI']."</td>";
		   $baris .= "<td align='center'>".$row['TONASE_ANGKUT']."</td>";
		   $baris .= "<td align='center'>".$row['TONASE_ANGKUT_SHI']."</td>";
		   	 
			$baris .= "</tr>";		
		}
		
		$table = "Produksi TBS Inti <br />";
		//$table .= "Tanggal ". $tgl ."<br /><br />";
		$table .= "<table align='center' class='fancy' width='100%' cellpadding='2' cellspacing='0' border='1'>";
		$table .= "<tr>
					<th align='center' rowspan='2'>Perusahaan</th>
					<th align='center' colspan='2'>Janjang Panen</th>
					<th align='center' colspan='2'>HK</th>
					<th align='center' colspan='2'>Janjang Angkut</th>
					<th align='center' colspan='2'>Tonase Angkut</th>
					</tr>";
		$table .= "<tr>
				 	<th align='center'>HI</th><th align='center'>SHI</th>
					<th align='center'>HI</th><th align='center'>SHI</th>
					<th align='center'>HI</th><th align='center'>SHI</th>
					<th align='center'>HI</th><th align='center'>SHI</th>
					</tr>";
 		$table .= $baris;
		$table .= "</table>"; 	
		return $table;
	}
	
	
	function cpo_tampil_semua( )
	{
		
		$data_row = $this->model_rpt->cpo_all();
		
		$header = "";
		$baris = "";
		$company = "";
		foreach( $data_row as $row)
		{
			
		   $baris .= "<tr>";
		   $baris .= "<td align='center'>".$row['company']."</td>";
		   $baris .= "<td align='center'>".$row['TONASE_CPO']."</td>";
		   $baris .= "<td align='center'>".$row['TON_CPO_SHI']."</td>";
		   $baris .= "<td align='center'>".$row['RENDEMEN_CPO']."</td>";
		   $baris .= "<td align='center'>".$row['FFA_PROD_CPO']."</td>";
		   $baris .= "<td align='center'>".$row['TONASE_KERNEL']."</td>";
		   $baris .= "<td align='center'>".$row['TON_KERNELL_SHI']."</td>";
		   $baris .= "<td align='center'>".$row['RENDEMEN_KERNEL']."</td>";
		  
		   	 
			$baris .= "</tr>";		
		}
		
		$table_cpo = "Produksi CPO <br />";
		//$table .= "Tanggal ". $tgl ."<br /><br />";
		$table_cpo .= "<table align='center' class='fancy' width='100%' cellpadding='2' cellspacing='0' border='1'>";
		$table_cpo .= "<tr>
					<th align='center' rowspan='2'>Perusahaan</th>
					<th align='center' colspan='2'>Produksi CPO</th>
					<th align='center' rowspan='2'>Rendemen CPO(%)</th>
					<th align='center' rowspan='2'>FFA (%)</th>
					<th align='center' colspan='2'>Produksi Kernel</th>
					<th align='center' rowspan='2'>Rendemen Kernel(%)</th>
					</tr>";
		$table_cpo .= "<tr>
				 	<th align='center'>HI</th><th align='center'>SHI</th>
					<th align='center'>HI</th><th align='center'>SHI</th>
					</tr>";
 		$table_cpo .= $baris;
		$table_cpo .= "</table>"; 	
		return $table_cpo;
	}
	
		
	function chart_tbs_2d()
	{
		$data_row = $this->model_dashboard->generate_stock();

		$i=0;
		$category="";
		$quantity="";
		$bar="";
		foreach( $data_row as $row)
		{
		   $category .= "<category label='".$row['DATE']."' />";
		   $quantity .= "<set value='". $row['TONASE_TIMBANG']."' />";
		   $bar .= "<set label='".$row['DATE']."' value='".$row['TONASE_TIMBANG']."' />";
		   $arrData[$i][1] = $row['DATE'];
		   $arrData[$i][2] = $row['TONASE_TIMBANG'];
		   
		   
		   $i++;
		} 	
		
		$strColumn3DXML ="<chart palette='2' showBorder='0' yAxisMaxValue='1200' canvasBorderColor='FFFFFF' bgColor='FFFFFF' caption='TBS Blok ".$row['BLOK_TBS']."' labelDisplay='Rotate' slantLabels='1' shownames='1' showvalues='1' decimals='2' xAxisName='Tanggal' yAxisName='Tonase (Kg)' >";
		//$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= $bar;
		/* $strColumn3DXML .= "<styles>";
		$strColumn3DXML .= "<definition>";
		$strColumn3DXML .= "<style name='myHTMLFont' type='font' isHTML='1' />";
		$strColumn3DXML .= "</definition>";
		$strColumn3DXML .= "<application>";
		$strColumn3DXML .= "<apply toObject='TOOLTIP' styles='myHTMLFont' />";
		$strColumn3DXML .= "</application>";
		$strColumn3DXML .= "</styles>";*/
		$strColumn3DXML .= "</chart>"; 

		$data['graph'] = $this->fusioncharts->renderChart($this->swf2DBarCharts,'',$strColumn3DXML,"Progres", 750, 400,false, false);
		echo $data['graph'];
		//$this->load->view('v_dashboard.php',$data);

	}
	
	function chart_tbs()
	{
		$data_row = $this->model_dashboard->produksi_tbs('MIA');

		$i=0;
		$category="";
		$quantity="";
		foreach( $data_row as $row)
		{
		   $category .= "<category label='".$row['DATE']."' />";
		   $quantity .= "<set value='". $row['JK']."' />";
		} 	
		
		$strColumn3DXML ="<chart palette='2' caption='TBS Blok ".$row['company']."' labelDisplay='Rotate' slantLabels='1' shownames='1' showvalues='1' decimals='2' xAxisName='Tanggal' yAxisName='Tonase (Kg)' >";
		$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= "<dataset seriesName='Janjang Panen' color='AFD222' showValues='1'>" . $quantity . "</dataset>";
		
		$strColumn3DXML .= "</chart>"; 

		$data['graph_tbs'] = $this->fusioncharts->renderChart($this->swfColumn3DCharts,'',$strColumn3DXML,"tbs", 750, 250,false, false);
		return $data['graph_tbs'];
		//$this->load->view('v_dashboard.php',$data);

	}
	
	//grading buah
	function chart_grading_buah()
	{
		$comp = $this->uri->segment(3);
		$data_row = $this->model_dashboard->grading_mtd($comp);

		$i=0;
		$quantity="";
		
		foreach( $data_row as $row)
		{
		   $quantity .= "<set label='Janjang Kebun' value='". $row['JANJANG_KEBUN']."' />";
		   $quantity .= "<set label='Buah Mentah' value='". $row['MENTAH']."' />";
		   $quantity .= "<set label='Buah Busuk' value='". $row['BUSUK']."' />";
		   $quantity .= "<set label='Tangkai Panjang' value='". $row['PANJANG']."' />";
		   	   
		   $i++;
		} 	
		//line nya
		$strColumn3DXML ="<chart palette='4' decimals='0' enableSmartLabels='1' enableRotation='0' bgColor='99CCFF,FFFFFF' bgAlpha='40,100' bgRatio='0,100' bgAngle='360' showBorder='1' startingAngle='70'>";

		$strColumn3DXML .= $quantity;
		$strColumn3DXML .= "</chart>"; 

		//$data['graph'] = $this->fusioncharts->renderChart($this->swfColumn3DCharts,'',$strColumn3DXML,"Progres", 750, 400,false, false);
		
		$data['graph'] = $this->fusioncharts->renderChart($this->swfpie3D,'',$strColumn3DXML,"Progres", 750, 400,false, TRUE);
		
		$this->load->view('v_dashboard.php',$data);

	}
	
	
	//tbs multi series
	function chart_tbs_all()
	{
		$data_row = $this->model_dashboard->generate_stock();

		$i=0;
		$category="";
		$quantity="";
		$quantity2="";
		foreach( $data_row as $row)
		{
		   $category .= "<category label='".$row['DATE']."' />";
		   $quantity .= "<set value='". $row['JANJANG_KEBUN']."' />";
		   $quantity2 .= "<set value='". $row['JANJANG_TIMBANG']."' />";
		   
		   $arrData[$i][1] = $row['DATE'];
		   $arrData[$i][2] = $row['JANJANG_KEBUN'];
		   $arrData[$i][2] = $row['JANJANG_TIMBANG'];
		   
		   
		   $i++;
		} 	
		
		//barnya
		//$strColumn3DXML ="<chart palette='5' canvasPadding='20' caption='TBS Blok ".$row['BLOK_TBS']."' labelDisplay='Rotate' slantLabels='1' shownames='1' showvalues='1' decimals='2' xAxisName='Tanggal' yAxisName='Janjang' >";

		//line nya
		$strColumn3DXML ="<chart palette='5' subcaption='TBS Blok ".$row['BLOK_TBS']."' lineThickness='2' canvasPadding='20' caption='PT. Minang Agro' labelDisplay='Rotate' slantLabels='1' shownames='1' showvalues='1' decimals='2' xAxisName='Tanggal' yAxisName='Janjang' anchorRadius='2' divLineAlpha='20' divLineColor='CC3300' divLineIsDashed='1' showAlternateHGridColor='1' alternateHGridColor='CC3300' shadowAlpha='60' labelStep='2' numvdivlines='5' bgColor='FFFFFF,CC3300' bgAngle='270' bgAlpha='10,10' >";

		$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= "<dataset tooltext='Janjang Kebun&lt;BR&gt;tanggal: ".$row['DATE']."&lt;BR&gt;Janjang:".$row['JANJANG_KEBUN']."' seriesName='Janjang Kebun' color='F6BD0F' showValues='0'>" . $quantity . "</dataset>";
		$strColumn3DXML .= "<dataset tooltext='Janjang Timbang&lt;BR&gt;tanggal: ".$row['DATE']."&lt;BR&gt;Janjang:".$row['JANJANG_TIMBANG']."' seriesName='Janjang Timbang' color='AFD8F8' showValues='0'>" . $quantity2 . "</dataset>";
		$strColumn3DXML .= "<styles>";
		$strColumn3DXML .= "<definition>";
		$strColumn3DXML .= "<style name='CaptionFont' type='font' size='12'/>";
		$strColumn3DXML .= "</definition>";
		$strColumn3DXML .= "<application>";
		$strColumn3DXML .= "<apply toObject='CAPTION' styles='CaptionFont'/>";
		$strColumn3DXML .= "<apply toObject='SUBCAPTION' styles='CaptionFont'/>";
		$strColumn3DXML .= "</application>";
		$strColumn3DXML .= "</styles>";
		$strColumn3DXML .= "</chart>"; 

		//$data['graph'] = $this->fusioncharts->renderChart($this->swfColumn3DCharts,'',$strColumn3DXML,"Progres", 750, 400,false, false);
		
		$data['graph'] = $this->fusioncharts->renderChart($this->swfMSArea,'',$strColumn3DXML,"Progres", 750, 400,false, TRUE);
		
		$this->load->view('v_dashboard.php',$data);

	}
	
	
	//------------------------------------------single line
	function chart_tbs_single()
	{
		$data_row = $this->model_dashboard->generate_stock();

		$i=0;
		$category="";
		$quantity="";
		$quantity2="";
		foreach( $data_row as $row)
		{
		   $category .= "<category label='".$row['DATE']."' />";
		   $quantity .= "<set label='".$row['DATE']."' value='". $row['JANJANG_KEBUN']."' />";
		   //$quantity2 .= "<set value='". $row['JANJANG_TIMBANG']."' />";
		   
		   $arrData[$i][1] = $row['DATE'];
		   $arrData[$i][2] = $row['JANJANG_KEBUN'];
		   //$arrData[$i][2] = $row['JANJANG_TIMBANG'];
		   
		   
		   $i++;
		} 	
		
		//line nya
		$strColumn3DXML ="<chart palette='5' subcaption='TBS Blok ".$row['BLOK_TBS']."' lineThickness='2' canvasPadding='20' caption='PT. Minang Agro' labelDisplay='Rotate' slantLabels='1' shownames='1' showvalues='1' decimals='2' xAxisName='Tanggal' yAxisName='Janjang' anchorRadius='2' divLineAlpha='20' divLineColor='CC3300' divLineIsDashed='1' showAlternateHGridColor='1' alternateHGridColor='CC3300' shadowAlpha='60' labelStep='2' numvdivlines='5' bgColor='FFFFFF,CC3300' bgAngle='270' bgAlpha='10,10' >";

		$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= $quantity;
		$strColumn3DXML .= "<styles>";
		$strColumn3DXML .= "<definition>";
		$strColumn3DXML .= "<style name='CaptionFont' type='font' size='12'/>";
		$strColumn3DXML .= "</definition>";
		$strColumn3DXML .= "<application>";
		$strColumn3DXML .= "<apply toObject='CAPTION' styles='CaptionFont'/>";
		$strColumn3DXML .= "<apply toObject='SUBCAPTION' styles='CaptionFont'/>";
		$strColumn3DXML .= "</application>";
		$strColumn3DXML .= "</styles>";
		$strColumn3DXML .= "</chart>"; 

		//$data['graph'] = $this->fusioncharts->renderChart($this->swfColumn3DCharts,'',$strColumn3DXML,"Progres", 750, 400,false, false);
		
		$data['graph'] = $this->fusioncharts->renderChart($this->swfLine,'',$strColumn3DXML,"Progres", 750, 400,false, TRUE);
		
		$this->load->view('v_dashboard.php',$data);

	}
	
	
	function chart_FFB_Receive ()
	{
		$data_row = $this->model_dashboard->cpo_chart();

		$i=0;
		$category="";
		
		$ds_quantity="";
		$ds_olah="";
		foreach( $data_row as $row)
		{
		   $kbn = $row['TBS_TRM_KEBUN'];
		   $luar = $row['TBS_TRM_LUAR'];
		   $plasma = $row['TBS_TRM_PLASMA'];
		   $all = $kbn + $luar + $plasma;
		   $category .= "<category label='".$row['DATE']."' />";
		   $ds_quantity .= "<set value='". $all ."' />";
		   $ds_olah .= "<set value='". $row['TBS_OLAH']."' />";
		   
		   //$arrData[$i][1] = $row['DATE'];
		   //$arrData[$i][2] = $row['TONASE_TIMBANG'];
		   
		   
		   $i++;
		} 	
		
		$strColumn3DXML ="<chart palette='5' yAxisMinValue='0' SYAxisMaxValue='600' setAdaptiveYMin='0' labelDisplay='Rotate' slantLabels='1' shownames='1' showValues='0' sYAxisValuesDecimals='0' connectNullData='0' PYAxisName='TBS Terima' SYAxisName='TBS Olah' numDivLines='0' formatNumberScale='0' >";
		$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= "<dataset seriesName='TBS Terima' color='AFD8F8' showValues='0'>" . $ds_quantity . "</dataset>";
		$strColumn3DXML .= "<dataset seriesName='TBS Olah' showValues='0' color='8BBA00' showValues='0' parentYAxis='S'>" . $ds_olah . "</dataset>";
		$strColumn3DXML .= "</chart>"; 

		$data['graph2'] = $this->fusioncharts->renderChart($this->MSColumn3DLineDY,'',$strColumn3DXML,"Progres", 550, 300,false, false);
		return $data['graph2'];
		//$this->load->view('v_dashboard.php',$data);

	}
	
	function chart_kernel ()
	{
		$data_row = $this->model_dashboard->produksi_cpo('MIA');

		$i=0;
		$category="";
		
		$ds_quantity="";
		$ds_kernel="";
		foreach( $data_row as $row)
		{
		  
		   $category .= "<category label='".$row['DATE']."' />";
		   $ds_quantity .= "<set value='". $row['TONASE_KERNEL'] ."' />";
		   $ds_kernel .= "<set value='". $row['RENDEMEN_KERNELL']."' />";
		   
		   //$arrData[$i][1] = $row['DATE'];
		   //$arrData[$i][2] = $row['TONASE_TIMBANG'];
		   
		   
		   $i++;
		} 	
		
		$strColumn3DXML ="<chart palette='5' caption='Produksi Kernel PT Minang Agro' subCaption='Oktober 2009' yAxisMinValue='0' SYAxisMaxValue='6' setAdaptiveYMin='0' labelDisplay='Rotate' slantLabels='1' shownames='1' showValues='0' sYAxisValuesDecimals='0' connectNullData='0' PYAxisName='Produksi' SYAxisName='Rendemen' formatNumberScale='0' >";
		$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= "<dataset seriesName='Tonase' color='AFD8F8' showValues='0'>" . $ds_quantity . "</dataset>";
		$strColumn3DXML .= "<dataset seriesName='Rendemen' showValues='0' color='8BBA00' showValues='0' parentYAxis='S'>" . $ds_kernel . "</dataset>";
		$strColumn3DXML .= "</chart>"; 

		$data['graph_kernell'] = $this->fusioncharts->renderChart($this->MSColumn3DLineDY,'',$strColumn3DXML,"Kernel", 480, 250,false, false);
		
		return $data['graph_kernell'];
		//$this->load->view('v_dashboard.php',$data);

	}
	
	function chart_cpo ()
	{
		$data_row = $this->model_dashboard->produksi_cpo('MIA');

		$i=0;
		$category="";
		
		$ds_quantity="";
		$ds_cpo="";
		$plan ="";
		foreach( $data_row as $row)
		{
		  
		   $category .= "<category label='".$row['DATE']."' />";
		   $ds_quantity .= "<set value='". $row['TONASE_CPO'] ."' />";
		   $plan .= "<set value='". $row['PLAN_TONASE_CPO'] ."' />";
		   $ds_cpo .= "<set value='". $row['RENDEMEN']."' />";
		   
		   //$arrData[$i][1] = $row['DATE'];
		   //$arrData[$i][2] = $row['TONASE_TIMBANG'];
		   
		   
		   $i++;
		} 	
		
		$strColumn3DXML ="<chart palette='2' labelDisplay='Rotate' SYAxisMaxValue='35' slantLabels='1' caption='Produksi CPO PT Minang Agro' subCaption='Oktober 2009' showValues='0' divLineDecimalPrecision='1' limitsDecimalPrecision='1' PYAxisName='Produksi' SYAxisName='Rendemen (%)' SYAxisSuffix='%' formatNumberScale='0' >";
		$strColumn3DXML .= "<categories>" . $category  ."</categories>";
		$strColumn3DXML .= "<dataset seriesName='Produksi Actual' color='AFD8F8' showValues='0'>" . $ds_quantity . "</dataset>";
		$strColumn3DXML .= "<dataset seriesName='Plan' renderAs='Area' parentYAxis='P'>" . $plan . "</dataset>";
		$strColumn3DXML .= "<dataset seriesName='Rendemen' lineThickness='3' parentYAxis='S'>" . $ds_cpo . "</dataset>";
		$strColumn3DXML .= "</chart>"; 

		$data['graph'] = $this->fusioncharts->renderChart($this->MS2DDY,'',$strColumn3DXML,"produksi", 750, 230,false, false);
		
		//$this->load->view('v_dashboard.php',$data);
		return $data['graph'];
	}
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
?>