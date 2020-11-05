<script type="text/javascript">
        
		//--------------------------------------------------------- url global js
		var url = "<?= base_url().'index.php/' ?>";
		// ----------------------------------------------------------------grid
       
	   
		// $('#last_activity').val().toString()
        var jGrid = null;
        var colNamesT = new Array();
        var colModelT = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';

        colNamesT.push('Subtype Code');
        colModelT.push({name:'IFSUBTYPE',index:'IFSUBTYPE', editable: false, width: 100, align:'center'});
        colNamesT.push('Subtype Name');
        colModelT.push({name:'IFSUBTYPE_NAME',index:'IFSUBTYPE_NAME', editable: true, width: 300, align:'center'});
        colNamesT.push('Infrastructure Type');
        colModelT.push({name:'IFTYPE',index:'IFTYPE', editable: false, width: 200, align:'center'});
		
      
        var loadView = function()
        {
            jGrid = jQuery("#list_ifsubtype").jqGrid(
            {
                url:url+'M_infrastructure_subtype/read_json_format',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT ,
                rowNum:20,
                height: 350,
                rowList:[10,20,30],
                imgpath: gridimgpath,
                pager: jQuery('#pager_ifsubtype'),
                sortname: colNamesT[0],
                viewrecords: true,
                caption:"Infrastructure Sub Type",
                onSelectRow: function(){
                    var id = jQuery("#list_ifsubtype").getGridParam('selrow');
                    
                }
            });
            jGrid.navGrid('#pager_ifsubtype',{edit:false,add:false,del:false, search: false, refresh: true});
            //----------------------------------------------------------------button add di grid		
			jGrid.navButtonAdd('#pager_ifsubtype',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$("#form_mode").val('POST');
	    			$('#dialog_ifsubtype').dialog('open');					
			   }, 
			   position:"last"
			});
			
			//---------------------------------------------------------------- button edit di grid
			jGrid.navButtonAdd('#pager_ifsubtype',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_ifsubtype").getGridParam('selrow');
					$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_ifsubtype").getRowData(id); 
						
						$('#dialog_ifsubtype').dialog('open');
						$("#IFSUBTYPE").val(ret.IFSUBTYPE); 
						$("#IFSUBTYPE_NAME").val(ret.IFSUBTYPE_NAME);
		                $("#IFTYPE").val(ret.IFTYPE);
						
						
					} else {
					alert("Please select row");
					} 				
			   }, 
			   position:"last"
			});
			
			//----------------------------------------------------------------button delete di grid
			jGrid.navButtonAdd('#pager_ifsubtype',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_ifsubtype").getGridParam('selrow');
					
					if (id)	{
						var ret = jQuery("#list_ifsubtype").getRowData(id);
						var id = ret.IFSUBTYPE;
						hapus(id);		 
					}
					 
					gridReload();
			   }, 
			   position:"last"
			});
			
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_ifsubtype").ready(loadView);


		
		//---------------------------------------------------------------- fungsi refresh grid
		function gridReload(){
			//var nm_mask = jQuery("#item_nm").val();
			//var cd_mask = jQuery("#search_cd").val();
			jQuery("#list_ifsubtype").setGridParam({url:url+"M_infrastructure_subtype/read_json_format"}).trigger("reloadGrid");
		}

        var initForm = function(){
        }
        jQuery("#form_ifsubtype").ready(initForm);
	
	
	
		//---------------------------------------------------------------- button submit data
		jQuery("#submitdata_ifsubtype").click(function (){
			
			var mode = $("#form_mode").val();
			
			if (mode == "GET"){
				var idsubtype = $("#IFSUBTYPE").val()
				update(idsubtype);
				
			} else if (mode == "POST")
			{
				Create();
			}
		});
	
	
		//---------------------------------------------------------------- init form
		function Init1() {  //nilai awal form
              
				$("#list_ifsubtype").trigger("reloadGrid"); 
                $("#IFSUBTYPE").val("");
                $("#IFSUBTYPE_NAME").val("");
				$("#IFTYPE").val("");
				
				
				i = 0;
			
		}
		
		//---------------------------------------------------------------- fungsi Add
			
			function Create() {
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['IFSUBTYPE'] = $("#IFSUBTYPE").val() ;
			postdata['IFSUBTYPE_NAME'] = $("#IFSUBTYPE_NAME").val(); 
			postdata['IFTYPE'] = $("#IFTYPE").val() ; 
							 
		   // Post it all 
			$.post( url+'M_infrastructure_subtype/create', postdata,function(message,status) { 
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
			
			$.post( url+'m_infrastructure_subtype/delete/'+id, postdata,function(message,status) { 
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
			postdata['IFSUBTYPE'] = $("#IFSUBTYPE").val() ;
			postdata['IFSUBTYPE_NAME'] = $("#IFSUBTYPE_NAME").val(); 
			postdata['IFTYPE'] = $("#IFTYPE").val() ; 
			
			
		   // Post it all 
			$.post( url+'m_infrastructure_subtype/edit/'+id, postdata,function(message,status) { 
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
			$("#dialog_ifsubtype").dialog({
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

 <table id="list_ifsubtype" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_ifsubtype" class="scroll" style="text-align:center;"></div>

<form method="POST" id="form_ifsubtype" action="<?= base_url().'index.php/M_infrastructure_subtype/'?>">
<div id="dialog_ifsubtype">

<table cellpadding="1" cellspacing="0" width="100%" border="0">
<tr><td width="120">Infrastructure Sub Type Code</td><td>:</td><td><input type="text" size=8 id="IFSUBTYPE" /></td></tr>
<tr><td width="120">Infrastructure Sub Type Name</td><td>:</td><td><input type="text" size=25 id="IFSUBTYPE_NAME" /></td></tr>
<tr><td width="120">Infrastructure Type</td><td>:</td><td><?php echo $IFTYPE; ?></td></tr>
</table>

<input type="hidden" id="form_mode">
<input type="button"  id="submitdata_ifsubtype" value="Submit"><br />

</div>
</form>

