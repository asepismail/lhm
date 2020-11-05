<script type="text/javascript">
        
		//--------------------------------------------------------- url global js
		var url = "<?= base_url().'index.php/' ?>";
		// ----------------------------------------------------------------grid
        var jGrid = null;
        var colNamesT = new Array();
        var colModelT = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';

        colNamesT.push('Infrastructure ID');
        colModelT.push({name:'IFTYPE',index:'IFTYPE', editable: false, width: 150, align:'center'});
        colNamesT.push('Infrastructure Name');
        colModelT.push({name:'IFTYPE_NAME',index:'IFTYPE_NAME', editable: false, width: 230, align:'center'});
        colNamesT.push('Control Job');
        colModelT.push({name:'CONTROL_JOB',index:'CONTROL_JOB', editable: false, width: 230, align:'center'});
      
        var loadView = function()
        {
            jGrid = jQuery("#list_iftype").jqGrid(
            {
                url:url+'M_infrastructure_type/read_json_format',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT ,
                rowNum: 20,
                height: 350,
                rowList:[10,20,30],
                imgpath: gridimgpath,
                pager: jQuery('#pager_iftype'),
                sortname: colNamesT[0],
                viewrecords: true,
                caption:"Infrastructure Type",
                onSelectRow: function(){
                    var id = jQuery("#list_iftype").getGridParam('selrow');
                    
                }
            });
            jGrid.navGrid('#pager_iftype',{edit:false,add:false,del:false, search: false, refresh: true});
			
			//----------------------------------------------------------------button add di grid		
			jGrid.navButtonAdd('#pager_iftype',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$("#form_mode").val('POST');
	    			$('#dialog_iftype').dialog('open');					
			   }, 
			   position:"last"
			});
			
			//---------------------------------------------------------------- button edit di grid
			jGrid.navButtonAdd('#pager_iftype',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_iftype").getGridParam('selrow');
					$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_iftype").getRowData(id); 
						
						$('#dialog_iftype').dialog('open');
						$("#IFTYPE").val(ret.IFTYPE); 
						$("#IFTYPE_NAME").val(ret.IFTYPE_NAME);
		                $("#CONTROL_JOB").val(ret.CONTROL_JOB);
						
						
					} else {
					alert("Please select row");
					} 				
			   }, 
			   position:"last"
			});
			
			//----------------------------------------------------------------button delete di grid
			jGrid.navButtonAdd('#pager_iftype',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_iftype").getGridParam('selrow');
					
					if (id)	{
						var ret = jQuery("#list_iftype").getRowData(id);
						var id = ret.IFTYPE;
						hapus(id);		 
					}
					 
					gridReload();
			   }, 
			   position:"last"
			});
			
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_iftype").ready(loadView);


		
		//---------------------------------------------------------------- fungsi refresh grid
		function gridReload(){
			//var nm_mask = jQuery("#item_nm").val();
			//var cd_mask = jQuery("#search_cd").val();
			jQuery("#list_iftype").setGridParam({url:url+"M_infrastructure_type/read_json_format"}).trigger("reloadGrid");
		}

        var initForm = function(){
        }
        jQuery("#form_iftype").ready(initForm);
	
	
	
		//---------------------------------------------------------------- button submit data
		jQuery("#submitdata_iftype").click(function (){
			
			var mode = $("#form_mode").val();
			
			if (mode == "GET"){
				var IFTYPE = $("#IFTYPE").val()
				update(IFTYPE);
			} else if (mode == "POST")
			{
				Create();
			}
		});
	
	
		//---------------------------------------------------------------- init form
		function Init1() {  //nilai awal form
              
				$("#list_iftype").trigger("reloadGrid"); 
                $("#IFTYPE").val("");
                $("#IFTYPE_NAME").val("");
				$("#CONTROL_JOB").val("");
				
				
				i = 0;
			
		}
		
		//---------------------------------------------------------------- fungsi Add
			
			function Create() {
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['IFTYPE'] = $("#IFTYPE").val() ;
			postdata['IFTYPE_NAME'] = $("#IFTYPE_NAME").val(); 
			postdata['CONTROL_JOB'] = $("#CONTROL_JOB").val() ; 
							 
		   // Post it all 
			$.post( url+'M_infrastructure_type/create', postdata,function(message,status) { 
				if(status !== 'success') { 
					alert(message); 
				  } else { 
				Init1(); //nilai awal form
				var i=1;
					}; 
			  } ); 
		  };
		  
		  
		//---------------------------------------------------------------- fungsi delete
		function hapus(id) {
			var postdata = {}; 
			
			$.post( url+'M_infrastructure_type/delete/'+id, postdata,function(message,status) { 
			if(status !== 'success') { 
				alert(message); 
			  } else { 
					Init1(); //nilai awal form
					var i=1;
			   }; 
		  } ); 
		}
		
		//---------------------------------------------------------------- fungsi Update
		function update(id){
		
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['IFTYPE'] = $("#IFTYPE").val() ;
			postdata['IFTYPE_NAME'] = $("#IFTYPE_NAME").val(); 
			postdata['CONTROL_JOB'] = $("#CONTROL_JOB").val() ; 
			
			
		   // Post it all 
			$.post( url+'M_infrastructure_type/edit/'+id, postdata,function(message,status) { 
				if(status !== 'success') { 
					alert(message); 
				  } else { 
				Init1(); //nilai awal form
				var i=1;
					}; 
			  } ); 
			
		}
		
		//---------------------------------------------------------------- Modal Dialog
		$(function() {
			$("#dialog_iftype").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 290,
				width: 360,
				modal: true,
				buttons: {
					'close	': function() {
							
									$(this).dialog('close');
									gridReload();
									Init1();							
								}
					
				} 
			}); 
		});
		//-- end dialog modal
		

    </script>
<?php $this->load->helper('form'); ?>
 <table id="list_iftype" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_iftype" class="scroll" style="text-align:center;"></div>

<form method="POST" id="form_iftype" action="<?= base_url().'index.php/M_infrastructure_type/'?>">
<div id="dialog_iftype">

<table cellpadding="1" cellspacing="0" width="100%" border="0">
<tr><td width="120">Infrastructure Code</td><td>:</td><td><input type="text" size=8 id="IFTYPE" /></td></tr>
<tr><td width="120">Infrastructure Name</td><td>:</td><td><input type="text" size=25 id="IFTYPE_NAME" /></td></tr>
<tr><td width="120">Control Job</td><td>:</td><td><input type="text" size=25 id="CONTROL_JOB" /></td></tr>
</table>

<input type="hidden" id="form_mode">
<input type="button"  id="submitdata_iftype" value="Submit"><br />

</div>
</form>

