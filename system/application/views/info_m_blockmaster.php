
<script type="text/javascript">
        //-------------------------------------------------------- date picker
		$(function() {
		$("#INACTIVEDATE").datepicker({dateFormat:"yy-mm-dd"});
		});
		
		
		//-------------------------------------------------------- current time
		function current_date(){
			var currentTime = new Date()
			var month = currentTime.getMonth() + 1
			var day = currentTime.getDate()
			var year = currentTime.getFullYear()
			var time = year + "/" + month + "/" + day
			
			return time;
		}		
		
		//--------------------------------------------------------- url global js
		var url = "<?= base_url().'index.php/' ?>";
		
		
		// ----------------------------------------------------------------grid
        
		var jGrid = null;
        var colNamesT = new Array();
        var colModelT = new Array();
        

		        colNamesT.push('Concession ID');
		        colModelT.push({name:'CONCESSIONID',index:'CONCESSIONID', editable: false, hidden:true, width: 100, align:'center'});
				
		        colNamesT.push('Company Code');
		        colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 100, align:'center'});
		      
			  	colNamesT.push('No HGU');
		        colModelT.push({name:'NOHGU',index:'NOHGU', editable: false, width: 120, align:'center'});
			
				colNamesT.push('Block ID');
		        colModelT.push({name:'BLOCKID',index:'BLOCKID', editable: false, width: 70, align:'center'});
			
				colNamesT.push('Description');
		        colModelT.push({name:'>DESCRIPTION);',index:'DESCRIPTION', editable: false, width: 150, align:'center'});
			
				colNamesT.push('Soil Type');
		        colModelT.push({name:'SOILTYPE',index:'SOILTYPE', editable: false, hidden:true, width: 100, align:'center'});
			
				colNamesT.push('Topograph');
		        colModelT.push({name:'TOPOGRAPH',index:'TOPOGRAPH', editable: false, hidden:true, width: 100, align:'center'});
			
				colNamesT.push('Hectarage');
		        colModelT.push({name:'HECTARAGE',index:'HECTARAGE', editable: false, width: 80, align:'center'});
			
				colNamesT.push('Plantable');
		        colModelT.push({name:'PLANTABLE',index:'PLANTABLE', editable: false, width: 80, align:'center'});
			
				colNamesT.push('Unplantable');
		        colModelT.push({name:'UNPLANTABLE',index:'UNPLANTABLE', editable: false, width: 90, align:'center'});
		    
			   	colNamesT.push('Inactive');
		       	colModelT.push({name:'INACTIVE',index:'INACTIVE', editable: false, hidden:true, width: 100, align:'center'});
				
				colNamesT.push('Inactive Date');
		       	colModelT.push({name:'INACTIVEDATE',index:'INACTIVEDATE', editable: false, hidden:true, width: 100, align:'center'});
				
			   	colNamesT.push('Rolling');
		       	colModelT.push({name:'ROLLING',index:'ROLLING', editable: false, hidden:true, width: 100, align:'left'});
		    
			    colNamesT.push('Flat');
		        colModelT.push({name:'FLAT',index:'FLAT', editable: false, width: 100, hidden:true, align:'left'});
		    
			   	colNamesT.push('Lowland');
		       	colModelT.push({name:'LOWLAND',index:'LOWLAND', editable: false, width: 100, hidden:true, align:'left'});
		    
			  	colNamesT.push('Planted');
		        colModelT.push({name:'PLANTED',index:'PLANTED', editable: false, width: 80, align:'center'});
		    
			  	colNamesT.push('Unplanted');
		        colModelT.push({name:'UNPLANTED',index:'UNPLANTED', editable: false, width: 90, align:'center'});
		    
			  	colNamesT.push('Noneffektive');
		        colModelT.push({name:'NONEFFECTIVE',index:'NONEFFECTIVE', hidden:true, editable: false, width: 100, align:'left'});
			
				colNamesT.push('Vegetation');
		       	colModelT.push({name:'VEGETATION',index:'VEGETATION', editable: false, hidden:true, width: 100, align:'left'});
		    
			  	colNamesT.push('Intiplasma');
		        colModelT.push({name:'INTIPLASMA',index:'INTIPLASMA', editable: false, hidden:true, width: 100, align:'left'});

      
	  
		
        var loadView = function()
        {
            jGrid = jQuery("#list_blockmaster").jqGrid(
            {      
		   	    url:url+'M_blockmaster/read_json_format',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT ,
                rowNum:20,
                height: 350,
                rowList:[10,20,30],
                imgpath: gridimgpath,
                pager: jQuery('#pager_blockmaster'),
                sortname: colNamesT[0],
                viewrecords: true,
                caption:"Block Master",
                onSelectRow: function(){
                    var id = jQuery("#list_blockmaster").getGridParam('selrow');
					
                }
            });
            jGrid.navGrid('#pager_blockmaster',{edit:false,add:false,del:false, search: false, refresh: true});
			
			//----------------------------------------------------------------button add di grid		
			jGrid.navButtonAdd('#pager_blockmaster',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$("#form_mode").val('POST');
	    			$('#dialog_blockmaster').dialog('open');					
			   }, 
			   position:"last"
			});
			
			//---------------------------------------------------------------- button edit di grid
			jGrid.navButtonAdd('#pager_blockmaster',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_blockmaster").getGridParam('selrow');
					$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_blockmaster").getRowData(id); 
						
						$('#dialog_blockmaster').dialog('open');
						
						$("#CONCESSIONID").val(ret.CONCESSIONID); 
						$("#COMPANY_CODE").val(ret.COMPANY_CODE);
		                $("#NOHGU").val(ret.NOHGU);
						$("#BLOCKID").val(ret.BLOCKID);
						$("#DESCRIPTION").val(ret.DESCRIPTION);
						$("#SOILTYPE").val(ret.SOILTYPE);
						$("#TOPOGRAPH").val(ret.TOPOGRAPH);			
						$("#HECTARAGE").val(ret.HECTARAGE); 
						$("#PLANTABLE").val(ret.PLANTABLE);
		                $("#UNPLANTABLE").val(ret.UNPLANTABLE);
						$("#INACTIVE").val(ret.INACTIVE);
						$("#INACTIVEDATE").val(ret.INACTIVEDATE);
						$("#ROLLING").val(ret.ROLLING);
						$("#FLAT").val(ret.FLAT);						
						$("#LOWLAND").val(ret.LOWLAND); 
						$("#PLANTED").val(ret.PLANTED);
		                $("#UNPLANTED").val(ret.UNPLANTED);
						$("#NONEFFECTIVE").val(ret.NONEFFECTIVE);
						$("#VEGETATION").val(ret.VEGETATION);
						$("#INTIPLASMA").val(ret.INTIPLASMA);
					

	
					} else {
					alert("Please select row");
					} 				
			   }, 
			   position:"last"
			});
			
			//----------------------------------------------------------------button delete di grid
			jGrid.navButtonAdd('#pager_blockmaster',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_blockmaster").getGridParam('selrow');
					
					if (id)	{
						var ret = jQuery("#list_blockmaster").getRowData(id);
						var block_id = ret.BLOCKID;
						//alert(block_id);
						hapus(block_id);		 
					}
					 
					gridReload();
			   }, 
			   position:"last"
			});
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_blockmaster").ready(loadView);
	
		//---------------------------------------------------------------- fungsi refresh grid
		function gridReload(){
;
			jQuery("#list_blockmaster").setGridParam({url:url+"M_blockmaster/read_json_format"}).trigger("reloadGrid");
		}

        var initForm = function(){
        }
        jQuery("#form_blockmaster").ready(initForm);
	
	
	
		//---------------------------------------------------------------- button submit data
		jQuery("#submitdata_bm").click(function (){
			
			var mode = $("#form_mode").val();
			
			if (mode == "GET"){
				var block_id = $("#BLOCKID").val()
				//alert(mode);
				update(block_id);
			} else if (mode == "POST")
			{
				//alert(mode);
				Create();
			}
		});
	
	
		//---------------------------------------------------------------- init form
		function Init1() {  //nilai awal form
              
				$("#list_blockmaster").trigger("reloadGrid"); 
						$("#CONCESSIONID").val(""); 
						$("#COMPANY_CODE").val(""); 
		                $("#NOHGU").val(""); 
						$("#BLOCKID").val(""); 
						$("#DESCRIPTION").val(""); 
						$("#SOILTYPE").val(""); 
						$("#TOPOGRAPH").val(""); 		
						$("#HECTARAGE").val(""); 
						$("#PLANTABLE").val(""); 
		                $("#UNPLANTABLE").val(""); 
						$("#INACTIVE").val(""); 
						$("#INACTIVEDATE").val(""); 
						$("#ROLLING").val(""); 
						$("#FLAT").val(""); 					
						$("#LOWLAND").val("");  
						$("#PLANTED").val(""); 
		                $("#UNPLANTED").val(""); 
						$("#NONEFFECTIVE").val(""); 
						$("#VEGETATION").val(""); 
						$("#INTIPLASMA").val(""); 
               				
				i = 0;
			
		}
		
		//---------------------------------------------------------------- fungsi Add
			
			function Create() {
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['CONCESSIONID'] = $("#CONCESSIONID").val() ;
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val(); 
			postdata['NOHGU'] = $("#NOHGU").val() ; 
			postdata['BLOCKID'] = $("#BLOCKID").val();
			postdata['DESCRIPTION'] = $("#DESCRIPTION").val(); 
			postdata['SOILTYPE'] = $("#SOILTYPE").val();
			postdata['TOPOGRAPH'] = $("#TOPOGRAPH").val();
			
			postdata['HECTARAGE'] = $("#HECTARAGE").val();
			postdata['PLANTABLE'] = $("#PLANTABLE").val();
			postdata['UNPLANTABLE'] = $("#UNPLANTABLE").val();
			postdata['INACTIVE'] = $("#INACTIVE").val();
			postdata['INACTIVEDATE'] = $("#INACTIVEDATE").val();
			postdata['ROLLING'] = $("#ROLLING").val();
			postdata['FLAT'] = $("#FLAT").val();
			postdata['LOWLAND'] = $("#LOWLAND").val();
			postdata['PLANTED'] = $("#PLANTED").val();
			
			postdata['UNPLANTED'] = $("#UNPLANTED").val();
			postdata['NONEFFECTIVE'] = $("#NONEFFECTIVE").val();
			postdata['VEGETATION'] = $("#VEGETATION").val();
			postdata['INTIPLASMA'] = $("#INTIPLASMA").val();
									 
		   	// Post it all 
			$.post( url+'M_blockmaster/create', postdata,function(message,status) { 
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
			
			$.post( url+'M_blockmaster/delete/'+id, postdata,function(message,status) { 
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
			postdata['CONCESSIONID'] = $("#CONCESSIONID").val() ;
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val(); 
			postdata['NOHGU'] = $("#NOHGU").val() ; 
			postdata['BLOCKID'] = $("#BLOCKID").val();
			postdata['DESCRIPTION'] = $("#DESCRIPTION").val(); 
			postdata['SOILTYPE'] = $("#SOILTYPE").val();
			postdata['TOPOGRAPH'] = $("#TOPOGRAPH").val();
			
			postdata['HECTARAGE'] = $("#HECTARAGE").val();
			postdata['PLANTABLE'] = $("#PLANTABLE").val();
			postdata['UNPLANTABLE'] = $("#UNPLANTABLE").val();
			postdata['INACTIVE'] = $("#INACTIVE").val();
			postdata['INACTIVEDATE'] = $("#INACTIVEDATE").val();
			postdata['ROLLING'] = $("#ROLLING").val();
			postdata['FLAT'] = $("#FLAT").val();
			postdata['LOWLAND'] = $("#LOWLAND").val();
			postdata['PLANTED'] = $("#PLANTED").val();
			
			postdata['UNPLANTED'] = $("#UNPLANTED").val();
			postdata['NONEFFECTIVE'] = $("#NONEFFECTIVE").val();
			postdata['VEGETATION'] = $("#VEGETATION").val();
			postdata['INTIPLASMA'] = $("#INTIPLASMA").val();
									 
			
		   // Post it all 
			$.post( url+'M_blockmaster/edit/'+id, postdata,function(message,status) { 
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
			$("#dialog_blockmaster").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 550,
				width: 350,
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
<table id="list_blockmaster" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_blockmaster" class="scroll" style="text-align:center;"></div>

<form method="POST" id="form_blockmaster" action="<?= base_url().'index.php/M_blockmaster/' ?>">
<div id="dialog_blockmaster">


<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr><td>Consession ID</td><td>:</td><td><input type="text" size=25 id="CONCESSIONID" /></td></tr>
<tr><td>Company / Estate</td><td>:</td><td><?php echo $COMPANY_CODE; ?></td></tr>
<tr><td>HGU</td><td>:</td><td><input type="text" size=25 id="NOHGU" /></td></tr>
<tr><td>Block</td><td>:</td><td><input type="text" size=25 id="BLOCKID" /></td></tr>
<tr><td>Description</td><td>:</td><td><input type="text" size=25 id="DESCRIPTION" /></td></tr>
<tr><td>Soil Type</td><td>:</td><td><input type="text" size=25 id="SOILTYPE" /></td></tr>
<tr><td colspan="3">Topograph</td></tr>
<tr><td> - Topograph</td><td>:</td><td><input type="text" size=25 id="TOPOGRAPH" /></td></tr>
<tr><td> - Hectarage</td><td>:</td><td><input type="text" size=10 id="HECTARAGE" /> &nbsp Ha</td></tr>
<tr><td> - Plantable</td><td>:</td><td><input type="text" size=10 id="PLANTABLE" /> &nbsp Ha</td></tr>
<tr><td> - Unplantable</td><td>:</td><td><input type="text" size=10 id="UNPLANTABLE" /> &nbsp Ha</td></tr>
<tr><td> - Inactive</td><td>:</td><td><select id="INACTIVE">
<option value="0"> No </option>
<option value="1"> Yes </option> 
</select></td></tr>
<tr><td> - Inactive Date</td><td>:</td><td><input type="text" size=25 id="INACTIVEDATE" /></td></tr>
<tr><td> - Rolling</td><td>:</td><td><input type="text" size=10 id="ROLLING" />  &nbsp Ha</td></tr>
<tr><td> - Flat</td><td>:</td><td><input type="text" size=10 id="FLAT" />  &nbsp Ha</td></tr>
<tr><td> - Lowland</td><td>:</td><td><input type="text" size=10 id="LOWLAND" />  &nbsp Ha</td></tr>
<tr><td> - Planted</td><td>:</td><td><input type="text" size=10 id="PLANTED" />  &nbsp Ha</td></tr>
<tr><td> - Unplanted</td><td>:</td><td><input type="text" size=10 id="UNPLANTED" />  &nbsp Ha</td></tr>
<tr><td> - Non Effective</td><td>:</td><td><input type="text" size=10 id="NONEFFECTIVE" />  &nbsp Ha</td></tr>
<tr><td>Vegetation</td><td>:</td><td><input type="text" size=25 id="VEGETATION" /></td></tr>
<tr><td>Intiplasma</td><td>:</td><td><input type="text" size=25 id="INTIPLASMA" /></td></tr>
</table>

<input type="hidden" id="form_mode">
<input type="button"  id="submitdata_bm" value="Submit"><br />

</div>
</form>




