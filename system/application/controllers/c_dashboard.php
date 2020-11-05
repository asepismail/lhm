<?php
class c_dashboard extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('m_dashboard');
        $this->load->model('model_c_user_auth');
        $this->load->model('model_s_analisa_panen');  
        
        $this->load->library('form_validation');
        $this->load->library('global_func');

        $this->load->plugin('jqsuit');
         
        $this->lastmenu="c_dashboard";
        $this->data = array(); 
    }
    
    function index(){
        $view="dashboard";

        $this->data['judul_header'] = "DASHBOARD";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        //$this->data['graph_tangki_1'] = $this->load_graph_tangki_1();
        //$this->data['graph_tangki_2'] = $this->load_graph_tangki_2();
        $this->data['graph_produksi'] = $this->load_graph_produksi();
		$this->data['graph_produksi_all'] = $this->load_graph_produksi_all();
		$this->data['graph_produksi_forday'] = $this->load_graph_produksi_forday();
        //$this->data['graph_despatch'] = $this->load_graph_despatch();
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData_UnmatchNAB(){
        $periode = date('Ym'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_s_analisa_panen->LoadData_UnmatchNAB('201111',$company));   
    }
    
    function get_total_day(){
        $total_d = date('t'); //get total day for a month
        $arr_d=array();
        for($i=1; $i<=$total_d; $i++){
            $arr_d[]=$i;    
        }
        return $arr_d;        
    }
    
    function load_graph_tangki_1(){
        //$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
        //$conn = new MYSQLI('localhost','root','','lhm_online');
        $volume=array();
        $tgl=array();
        $temperature=array();
        $ffa=array();
        $periode_full=array();
        $strg_code='';
        $periode_now=date('Ym');
        
        if ($this->data['company_code']=='MIA'){
            //Get Company Code data Then
            //Set Storage Code
            //This still hardcode :D
            $strg_code='TCMIA-01';
        }elseif($this->data['company_code']=='LIH'){
            $strg_code='TCLIH-01'; 
        }
        $temp_volume=$this->model_s_analisa_panen->get_vol_tanki($this->data['company_code'],$strg_code,$periode_now);
        foreach ($temp_volume as $key){
            $volume[]=$key['VOLUME'];
            $tgl[]=date('d',strtotime($key['DATE']));
            $periode_full[]=$key['DATE'];
            $temperature[]=$key['TEMPERATURE'];
            $ffa[]=$key['FFA'];    
        }
        
        $chart = new jqChart('');
        $chart->setChartOptions(array("zoomType"=>"xy"))
        ->setTitle(array('text'=>'Daily Volume Storage-1'))
        ->setSubtitle(array("text"=>"Periode: ".date('F Y').""))//date('F Y', strtotime("2011-11-01")).""))
        ->setxAxis( //Horizontal
                array("categories"=>$tgl,
                        "labels"=>array(
                            "rotation"=> -45,
                            "align"=>"right",
                            "style"=>array("font"=>"normal 10px Verdana, sans-serif")
                        )  
            ))
        ->setyAxis(array(
            array( 
                "labels"=>array(
                    "formatter"=>"js:function(){return this.value +'C';}",
                    "style"=>array("color"=>'#89A54E')
                ),
                "title"=>array(
                    'text'=> 'Temperature',
                    "style"=>array("color"=>'#89A54E')
                ),
                "opposite"=> true
            ),
            array( 
                "title"=>array(
                    'text'=> 'Volume',
                    "style"=>array("color"=>'#4572A7')
                ),
                "labels"=>array(
                    "formatter"=>"js:function(){return this.value +'mm';}",
                    "style"=>array("color"=>'#4572A7')
                ),
            ),
            array(
                "labels"=>array(
                    "formatter"=>"js:function(){return this.value +'%';}",
                    "style"=>array("color"=>'#AA4643')
                ),
                "title"=>array(
                    'text'=> 'FFA',
                    "style"=>array("color"=>'#AA4643')
                ),
                "opposite"=> true
            ),
        ))
        ->setTooltip(array("formatter"=>"function(){
            var unit = {
                'Volume': 'mm',
                'Temperature': 'C',
                'FFA': '%'
            }[this.series.name];
            if (this.series.name=='Volume'){
                return 'Tgl:'+ this.x +', Vol:'+ this.y +' '+ unit;
            }else if(this.series.name=='Temperature'){
                return 'Tgl:'+ this.x +', Suhu:'+ this.y +' '+ unit;
            }else if(this.series.name=='FFA'){
                return 'Tgl:'+ this.x +', ffa:'+ this.y +' '+ unit;
            }
            
            }"
        ))
        ->setLegend(array(
            "layout"=> 'vertical',
            "align"=> 'left',
            "x"=> 50,
            "y"=> -10,
            "verticalAlign"=> 'top',
            "floating"=> TRUE,
            "backgroundColor"=> '#FFFFFF'
        ))
        ->setPlotOptions(array(
            "series"=>array(
                "cursor"=>"pointer",
                "point"=>array(
                    "events"=>array(
                        "click"=>"js:function(event){
                            // this refers to entry object again with data
                            jQuery.jgrid.info_dialog(this.series.name, Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+ this.y +' visits', 'Close',
                                    {buttonalign:'right',top:this.pageY, left:this.pageX, modal:false, overlay:0});
                            
                        }"
                    )
                ),
                "marker"=>array("lineWidth"=>1)
            )
        ))
        
        //ADD GRAPH DATA 
        //BEGIN HERE
        ->addSeries('Volume', $volume)
        ->setSeriesOption('Volume',array('type'=>'column', "color"=>'#4572A7',"yAxis"=>1))
        
        ->addSeries('FFA', $ffa)
        ->setSeriesOption('FFA',array(
            'type'=>'spline',
            "color"=>'#AA4643',
            "yAxis"=>2,
            "dashStyle"=> 'shortdot',
            "marker"=>array("enabled" =>false)
        ))
        ->addSeries('Temperature', $temperature)
        ->setSeriesOption('Temperature',array('type'=>'spline', "color"=>'#89A54E'));
        return $chart->renderChart('vol1', true, 675, 275);   
    }
    
    function load_graph_tangki_2(){
        //$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
        //$conn = new MYSQLI('localhost','root','','lhm_online');
        $volume=array();
        $tgl=array();
        $temperature=array();
        $ffa=array();
        $strg_code='';
        $periode_now=date('Ym');
        
        if ($this->data['company_code']=='MIA'){
            //Get Company Code data Then
            //Set Storage Code
            //This still hardcode :D
            $strg_code='TCMIA-02';
        }elseif($this->data['company_code']=='LIH'){
            $strg_code='TCLIH-02'; 
        }
        $temp_volume=$this->model_s_analisa_panen->get_vol_tanki($this->data['company_code'],$strg_code,$periode_now);
        foreach ($temp_volume as $key){
            $volume[]=$key['VOLUME'];
            $tgl[]=date('d',strtotime($key['DATE']));
            $temperature[]=$key['TEMPERATURE'];
            $ffa[]=$key['FFA'];    
        }
        
        $chart = new jqChart('');
        $chart->setChartOptions(array("zoomType"=>"xy"))
        ->setTitle(array('text'=>'Daily Volume Storage-2'))
        ->setSubtitle(array("text"=>"Periode: ".date('F Y').""))//date('F Y', strtotime("2011-11-01")).""))
        ->setxAxis( //Horizontal
                array("categories"=>$tgl,
                        "labels"=>array(
                            "rotation"=> -45,
                            "align"=>"right",
                            "style"=>array("font"=>"normal 10px Verdana, sans-serif")
                        )   
            ))
        ->setyAxis(array(
            array( 
                "labels"=>array(
                    "formatter"=>"js:function(){return this.value +'C';}",
                    "style"=>array("color"=>'#89A54E')
                ),
                "title"=>array(
                    'text'=> 'Temperature',
                    "style"=>array("color"=>'#89A54E')
                ),
                "opposite"=> true
            ),
            array( 
                "title"=>array(
                    'text'=> 'Volume',
                    "style"=>array("color"=>'#4572A7')
                ),
                "labels"=>array(
                    "formatter"=>"js:function(){return this.value +'mm';}",
                    "style"=>array("color"=>'#4572A7')
                ),
            ),
            array(
                "labels"=>array(
                    "formatter"=>"js:function(){return this.value +'%';}",
                    "style"=>array("color"=>'#AA4643')
                ),
                "title"=>array(
                    'text'=> 'FFA',
                    "style"=>array("color"=>'#AA4643')
                ),
                "opposite"=> true
            ),
        ))
        ->setTooltip(array("formatter"=>"function(){
            var unit = {
                'Volume': 'mm',
                'Temperature': 'C',
                'FFA': '%'
            }[this.series.name];
            if (this.series.name=='Volume'){
                return 'Tgl:'+ this.x +', Vol:'+ this.y +' '+ unit;
            }else if(this.series.name=='Temperature'){
                return 'Tgl:'+ this.x +', Suhu:'+ this.y +' '+ unit;
            }else if(this.series.name=='FFA'){
                return 'Tgl:'+ this.x +', ffa:'+ this.y +' '+ unit;
            }
            
            }"
        ))
        ->setLegend(array(
            "layout"=> 'vertical',
            "align"=> 'left',
            "x"=> 50,
            "y"=> -10,
            "verticalAlign"=> 'top',
            "floating"=> TRUE,
            "backgroundColor"=> '#FFFFFF'
        ))
        ->setPlotOptions(array(
            "series"=>array(
                "cursor"=>"pointer",
                "point"=>array(
                    "events"=>array(
                        "click"=>"js:function(event){
                            // this refers to entry object again with data
                            jQuery.jgrid.info_dialog(this.series.name, Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+ this.y +' visits', 'Close',
                                    {buttonalign:'right',top:this.pageY, left:this.pageX, modal:false, overlay:0});
                            
                        }"
                    )
                ),
                "marker"=>array("lineWidth"=>1)
            )
        ))
        
        //ADD GRAPH DATA 
        //BEGIN HERE
        ->addSeries('Volume', $volume)
        ->setSeriesOption('Volume',array('type'=>'column', "color"=>'#4572A7',"yAxis"=>1))
        
        ->addSeries('FFA', $ffa)
        ->setSeriesOption('FFA',array(
            'type'=>'spline',
            "color"=>'#AA4643',
            "yAxis"=>2,
            "dashStyle"=> 'shortdot',
            "marker"=>array("enabled" =>false)
        ))
        ->addSeries('Temperature', $temperature)
        ->setSeriesOption('Temperature',array('type'=>'spline', "color"=>'#89A54E'));
        return $chart->renderChart('vol2', true, 675, 275); 
    }
    
    function load_graph_produksi(){
        $periode_awal = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'))."<br>";
        $periode_akhir =date("Y-m-d", strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
        
        $prod_cpo = array();
        $prod_pk = array();
        $tbs_terima = array();
        $tbs_olah = array();
        $rendemen = array();
		$oer_kernel = array();
        $restan = array();
        $periode_full=array();
        
        $temp_produksi=$this->model_s_analisa_panen->get_produksi($this->data['company_code'],$periode_awal,$periode_akhir);
        foreach ($temp_produksi as $key){
            $prod_cpo[] = $key['PROD_CPO'];
            $prod_pk[] = $key['PROD_KERNEL'];
            $tbs_terima[] = $key['TBS_TERIMA'];
            $tbs_olah[] = $key['TBS_OLAH'];
            $rendemen[] = round($key['RENDEMEN_CPO'],2) ;
			$oer_kernel[] = round($key['RENDEMEN_KERNEL'],2) ;
            $restan[] = $key['RESTAN']; 
            $periode_full[]=$key['TANGGAL'];   
        }
        
        $chart = new jqChart();
        $chart->setChartOptions(array(
            //"defaultSeriesType"=>"column"
            "zoomType"=>"xy"
        ))
        ->setTitle(array('text'=>'Daily Production PT. '. $this->data['company_dest'] ))
        ->setSubtitle(array("text"=>"Period: ".date('F Y').""))//date('F Y', strtotime("2011-11-01")).""))
        ->setxAxis(array("categories"=>$periode_full,
                        "labels"=>array(
                            "rotation"=> -75,
                            "align"=>"right",
                            "style"=>array("font"=>"normal 10px Verdana, sans-serif")
                        )   ))
        ->setyAxis(array(
                array( 
                    "labels"=>array(
                        "formatter"=>"js:function(){return this.value +' Kg';}"
                    ),
                    "title"=>array(
                        'text'=> 'Volume'
                    )
                ),
                array( 
                    "title"=>array(
                        'text'=> 'OER CPO/Kernel'
                    ),
                    "labels"=>array(
                        "formatter"=>"js:function(){return this.value +' %';}"
                    ),
                    "opposite"=> true
                )
            )) 
        /*->setLegend(array( 
            "layout"=>"vertical",
            "backgroundColor"=>'#FFFFFF',
            "align"=>"left",
            "verticalAlign"=>'bottom',
            "x"=>100,
            "y"=>5,
            "floating"=>true,
            "shadow"=>true
        ))*/
        //->setTooltip(array("formatter"=>"function(){return this.series.name +': '+ this.y +' Ton';}"))
        ->setTooltip(array("formatter"=>"function(){
            var unit = {
                'OER KERNEL': '%'
            }[this.series.name];
            if (this.series.name=='OER KERNEL' || this.series.name=='OER CPO'){
                return this.series.name +': '+ this.y +'%';
            }else {
                return this.series.name +': '+ this.y +' Kg';  
            }
            
            }"
        ))
        ->setPlotOptions(array(
            "column"=> array(
                "pointPadding"=> 0.2,
                "borderWidth"=> 0
            )
        ))
        
        ->addSeries('CPO', $prod_cpo)
        ->setSeriesOption('CPO',array('type'=>'column',"yAxis"=>0))
        ->addSeries('KERNEL', $prod_pk)
        ->setSeriesOption('PK',array('type'=>'column'))
        ->addSeries('FFB RECEIVED', $tbs_terima)
        ->setSeriesOption('FFB RECEIVED',array('type'=>'column'))
        ->addSeries('FFB PROCESSED', $tbs_olah)
        ->setSeriesOption('FFB PROCESSED',array('type'=>'column'))
        ->addSeries('BALANCE YESTERDAY', $restan)
        ->setSeriesOption('BALANCE YESTERDAY',array('type'=>'column'))		
        ->addSeries('OER CPO', $rendemen)
        ->setSeriesOption('OER CPO',array('type'=>'spline',"yAxis"=>1))
		->addSeries('OER KERNEL', $oer_kernel)
        ->setSeriesOption('OER KERNEL',array('type'=>'spline',"yAxis"=>1));
        return $chart->renderChart('prod', true, 1500, 375);  
    }
	
	function load_graph_produksi_all(){
        $periode_awal = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'))."<br>";
        $periode_akhir =date("Y-m-d", strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
        
        $prod_cpo = array();
        $prod_pk = array();
        $tbs_terima = array();
        $tbs_olah = array();
        $rendemen = array();
		$oer_kernel = array();
        $company_code=array();
        
        $temp_produksi=$this->model_s_analisa_panen->get_produksi_all($periode_awal,$periode_akhir);
        foreach ($temp_produksi as $key){
            $prod_cpo[] = $key['PROD_CPO'];
            $prod_pk[] = $key['PROD_KERNEL'];
            $tbs_terima[] = $key['TBS_TERIMA'];
            $tbs_olah[] = $key['TBS_OLAH'];
            $rendemen[] = round($key['RENDEMEN_CPO'],2);
			$oer_kernel[] = round($key['RENDEMEN_KERNEL'],2) ;
			$company[] = $key['COMPANY_NAME'];
        }
        
        $chart = new jqChart();
        $chart->setChartOptions(array(
            //"defaultSeriesType"=>"column"
            "zoomType"=>"xy"
        ))
        ->setTitle(array('text'=>'Daily Production To Date'))
        ->setSubtitle(array("text"=>"Period: ".date('d F Y').""))//date('F Y', strtotime("2011-11-01")).""))
        ->setxAxis(array("categories"=>$company,
                        "labels"=>array(
                            "rotation"=> 0,
                            "align"=>"center",
                            "style"=>array("font"=>"normal 11px Verdana, sans-serif")
                        )   ))
        ->setyAxis(array(
                array( 
                    "labels"=>array(
                        "formatter"=>"js:function(){return this.value +' Kg';}"
                    ),
                    "title"=>array(
                        'text'=> 'Volume'
                    )
                ),
                array( 
                    "title"=>array(
                        'text'=> 'OER CPO/Kernel'
                    ),
                    "labels"=>array(
                        "formatter"=>"js:function(){return this.value +' %';}"
                    ),
                    "opposite"=> true
                )
            )) 
        /*
		->setLegend(array( 
            "layout"=>"vertical",
            "backgroundColor"=>'#FFFFFF',
            "align"=>"left",
            "verticalAlign"=>'top',
            "x"=>130,
            "y"=>5,
            "floating"=>true,
            "shadow"=>true
        ))
		*/
        //->setTooltip(array("formatter"=>"function(){return this.series.name +': '+ this.y +' Ton';}"))
        ->setTooltip(array("formatter"=>"function(){
            var unit = {
                'OER CPO': '%'
            }[this.series.name];
            if (this.series.name=='OER CPO' || this.series.name=='OER KERNEL'){
                return this.series.name +': '+ this.y +'%';
            }else {
                return this.series.name +': '+ this.y +' Kg';  
            }
            
            }"
        ))
        ->setPlotOptions(array(
            "column"=> array(
                "pointPadding"=> 0.2,
                "borderWidth"=> 0
            )
        ))
        
        ->addSeries('CPO', $prod_cpo)
        ->setSeriesOption('CPO',array('type'=>'column',"yAxis"=>0))        
        ->addSeries('FFB RECEIVED', $tbs_terima)
        ->setSeriesOption('FFB RECEIVED',array('type'=>'column'))
        ->addSeries('FFB PROCESSED', $tbs_olah)
        ->setSeriesOption('FFB PROCESSED',array('type'=>'column'))
		->addSeries('KERNEL', $prod_pk)
        ->setSeriesOption('PK',array('type'=>'column'))
        ->addSeries('OER CPO', $rendemen)
        ->setSeriesOption('OER CPO',array('type'=>'spline',"yAxis"=>1))
		->addSeries('OER KERNEL', $oer_kernel)
        ->setSeriesOption('OER KERNEL',array('type'=>'spline',"yAxis"=>1));
        return $chart->renderChart('prod_all', true, 600, 375);  
    }
	
	function load_graph_produksi_forday(){
		$today = date('Ymd');
		$today = strtotime('-1 day',strtotime($today));
		$today = date('Ymd', $today);
        
        $prod_cpo = array();
        $prod_pk = array();
        $tbs_terima = array();
        $tbs_olah = array();
        $rendemen = array();
		$oer_kernel = array();
        $company_code=array();
        
        $temp_produksi=$this->model_s_analisa_panen->get_produksi_all($today,$today);
        foreach ($temp_produksi as $key){
            $prod_cpo[] = $key['PROD_CPO'];
            $prod_pk[] = $key['PROD_KERNEL'];
            $tbs_terima[] = $key['TBS_TERIMA'];
            $tbs_olah[] = $key['TBS_OLAH'];
            $rendemen[] = round($key['RENDEMEN_CPO'],2);
			$oer_kernel[] = round($key['RENDEMEN_KERNEL'],2) ;
			$company[] = $key['COMPANY_NAME'];
        }
        
        $chart = new jqChart();
        $chart->setChartOptions(array(
            //"defaultSeriesType"=>"column"
            "zoomType"=>"xy"
        ))
        ->setTitle(array('text'=>'Daily Production Today'))
        ->setSubtitle(array("text"=>"Period: ".date('d F Y').""))//date('F Y', strtotime("2011-11-01")).""))
        ->setxAxis(array("categories"=>$company,
                        "labels"=>array(
                            "rotation"=> 0,
                            "align"=>"center",
                            "style"=>array("font"=>"normal 11px Verdana, sans-serif")
                        )   ))
        ->setyAxis(array(
                array( 
                    "labels"=>array(
                        "formatter"=>"js:function(){return this.value +' Kg';}"
                    ),
                    "title"=>array(
                        'text'=> 'Volume'
                    )
                ),
                array( 
                    "title"=>array(
                        'text'=> 'OER CPO/Kernel'
                    ),
                    "labels"=>array(
                        "formatter"=>"js:function(){return this.value +' %';}"
                    ),
                    "opposite"=> true
                )
            )) 
        /*->setLegend(array( 
            "layout"=>"horizontal",
            "backgroundColor"=>'#FFFFFF',
            "align"=>"left",
            "verticalAlign"=>'bottom',
            "x"=>130,
            "y"=>5,
            "floating"=>true,
            "shadow"=>true
        ))*/
        //->setTooltip(array("formatter"=>"function(){return this.series.name +': '+ this.y +' Ton';}"))
        ->setTooltip(array("formatter"=>"function(){
            var unit = {
                'OER CPO': '%'
            }[this.series.name];
            if (this.series.name=='OER CPO'||this.series.name=='OER KERNEL'){
                return this.series.name +': '+ this.y +'%';
            }else {
                return this.series.name +': '+ this.y +' Kg';  
            }
            
            }"
        ))
        ->setPlotOptions(array(
            "column"=> array(
                "pointPadding"=> 0.2,
                "borderWidth"=> 0
            )
        ))
        
        ->addSeries('CPO', $prod_cpo)
        ->setSeriesOption('CPO',array('type'=>'column',"yAxis"=>0))        
        ->addSeries('FFB RECEIVED', $tbs_terima)
        ->setSeriesOption('FFB RECEIVED',array('type'=>'column'))
        ->addSeries('FFB PROCESSED', $tbs_olah)
        ->setSeriesOption('FFB PROCESSED',array('type'=>'column'))
		->addSeries('KERNEL', $prod_pk)
        ->setSeriesOption('PK',array('type'=>'column'))
        ->addSeries('OER CPO', $rendemen)
        ->setSeriesOption('OER CPO',array('type'=>'spline',"yAxis"=>1))
		->addSeries('OER KERNEL', $oer_kernel)
        ->setSeriesOption('OER KERNEL',array('type'=>'spline',"yAxis"=>1));
        return $chart->renderChart('prod_forday', true, 600, 375);  
    }
    
    function load_graph_despatch(){
        $dpc_cpo = array();
        $dpc_pk = array();
        $dpc_ckg = array();
        $dpc_periode = array();
        $id_komoditas_cpo='';
        $id_komoditas_pk='';
        $id_komoditas_ckg='';
        
        if ($this->data['company_code']=='MIA'){
            $id_komoditas_cpo='KOMIA0002';
            $id_komoditas_pk='KOMIA0003'; 
            $id_komoditas_ckg='KOMIA0008';  
        }elseif($this->data['company_code']=='LIH'){
            $id_komoditas_cpo='KOLIH0002';
            $id_komoditas_pk='KOLIH0003'; 
            $id_komoditas_ckg='KOLIH0008';  
        }
        
        $temp_despatch_cpo=$this->model_s_analisa_panen->get_vol_dispatch($this->data['company_code'],date('Y'),$id_komoditas_cpo);
        foreach ($temp_despatch_cpo as $key){
            $dpc_cpo[] = $key['VOL_DESPATCH'];
            $dpc_periode[] = date('F Y', strtotime($key['TANGGALM']));  
        }
        $temp_despatch_pk=$this->model_s_analisa_panen->get_vol_dispatch($this->data['company_code'],date('Y'),$id_komoditas_pk);
        foreach ($temp_despatch_pk as $key){
            $dpc_pk[] = $key['VOL_DESPATCH'];  
        }
        $temp_despatch_ckg=$this->model_s_analisa_panen->get_vol_dispatch($this->data['company_code'],date('Y'),$id_komoditas_ckg);
        foreach ($temp_despatch_ckg as $key){
            $dpc_ckg[] = $key['VOL_DESPATCH'];  
        }
        
        $chart = new jqChart();
        $chart->setChartOptions(array(
            "defaultSeriesType"=>"area"
        ))
        ->setTitle(array('text'=>'Data Penjualan CPO , PK & CKG - '.$this->data['company_code']))
        ->setSubtitle(array("text"=>"Periode: ".date('Y').""))
        ->setxAxis(array("categories"=>$dpc_periode,
                        "labels"=>array(
                            "rotation"=> -45,
                            "align"=>"right",
                            "style"=>array("font"=>"normal 10px Verdana, sans-serif")
                        )   ))
        ->setyAxis(array(
            "title"=>array("text"=>'Volume'),
            "labels"=>array("formatter"=>"js:function(){return this.value +'Kg';}")
        ))
        ->setTooltip(array(
            "formatter"=>"function(){return this.series.name +' Terjual <b>'+Highcharts.numberFormat(this.y, 0) +' Kg'+'</b><br/>Selama '+ this.x;}"
        ))
        ->setPlotOptions(array(
            "area"=>array(
                
                "marker"=>array(
                    "enabled"=> false,
                    "symbol"=>"circle",
                    "radius"=>3,
                    "states"=>array("hover"=>  array ("enabled"=>true))
                )
            )
        ))
        
        ->addSeries('CPO', $dpc_cpo)
        ->addSeries('PK (Palm Kernel)', $dpc_pk)
        ->addSeries('CKG (Cangkang)', $dpc_ckg);

        return $chart->renderChart('dpc', true, 600, 300); 
    }
}
?>
