<script type="text/javascript">
        
		//-------------------------------------------------------- date picker
		$(function() {
		$("#INSTALLDATE").datepicker({dateFormat:"yy-mm-dd"});
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
       
	   
		// $('#last_activity').val().toString()
        var jGrid = null;
        var colNamesT = new Array();
        var colModelT = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';

         colNamesT.push('Infrastructure Code');
        colModelT.push({name:'IFCODE',index:'IFCODE', editable: false, width: 120, align:'center'});
        colNamesT.push('Fixed Asset code');
        colModelT.push({name:'FIXEDASSETCODE',index:'FIXEDASSETCODE', hidden:true, editable: false, width: 120, align:'center'});
        colNamesT.push('Infrastructure Type');
        colModelT.push({name:'IFTYPE',index:'IFTYPE', editable: false, width: 140, align:'center'});
		colNamesT.push('Infrastructure Sub Type');
        colModelT.push({name:'IFSUBTYPE',index:'IFSUBTYPE', editable: false, width: 100, align:'center'});
	    colNamesT.push('Infrastructure Name');
        colModelT.push({name:'IFNAME',index:'IFNAME', editable: false, width: 330, align:'left'});
		colNamesT.push('Install Date');
        colModelT.push({name:'INSTALLDATE',index:'INSTALLDATE',hidden:true, editable: false, width: 180, align:'center'});
		
		colNamesT.push('length');
        colModelT.push({name:'IFLENGTH',index:'IFLENGTH', editable: false, hidden:true, width: 180, align:'center'});
		colNamesT.push('width');
        colModelT.push({name:'IFWIDTH',index:'IFWIDTH', editable: false, hidden:true, width: 180, align:'center'});
		colNamesT.push('UOM');
        colModelT.push({name:'UOM',index:'UOM', editable: false, hidden:true, width: 180, align:'center'});
		
		colNamesT.push('Development Cost');
        colModelT.push({name:'DEVELOPMENT_COST',index:'DEVELOPMENT_COST', editable: false, width: 120, align:'center'});
		colNamesT.push('Volume');
        colModelT.push({name:'VOLUME',index:'VOLUME',hidden:true, editable: false, width: 180, align:'center'});
		colNamesT.push('Rolling');
        colModelT.push({name:'ROLLING',index:'ROLLING',hidden:true, editable: false, width: 180, align:'center'});
		
		colNamesT.push('Flat');
        colModelT.push({name:'FLAT',index:'FLAT',hidden:true, editable: false, width: 180, align:'center'});
		colNamesT.push('Lowland');
        colModelT.push({name:'LOWLAND',index:'LOWLAND',hidden:true, editable: false, width: 180, align:'center'});
		colNamesT.push('Estate');
        colModelT.push({name:'ESTATE',index:'ESTATE', editable: false,hidden:true, width: 180, align:'center'});
		
		colNamesT.push('Division');
        colModelT.push({name:'DIVISION',index:'DIVISION',hidden:true, editable: false, width: 180, align:'center'});
		colNamesT.push('Inactive Date');
        colModelT.push({name:'INACTIVEDATE',index:'INACTIVEDATE',hidden:true, editable: false, width: 180, align:'center'});
		colNamesT.push('Company Code');
        colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE',hidden:true, editable: false, width: 180, align:'center'});
		
      
        var loadView = function()
        {
           jGrid = jQuery("#list_if").jqGrid(
            {
                url:url+'m_infrastructure/read_json_format',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT ,
                rowNum:20,
                height: 350,
                rowList:[10,20,30],
                imgpath: gridimgpath,
                pager: jQuery('#pager_if'),
                sortname: colNamesT[0],
                viewrecords: true,
                caption:"Infrastructure",
                onSelectRow: function(){
           			var id = jQuery("#list_if").getGridParam('selrow');
                                        
                }
            });
			jGrid.navGrid('#pager_if',{edit:false,add:false,del:false, search: false, refresh: true});
			
            //----------------------------------------------------------------button add di grid		
			jGrid.navButtonAdd('#pager_if',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$("#form_mode").val('POST');
	    			$('#dialog_if').dialog('open');					
			   }, 
			   position:"last"
			});
			
			//---------------------------------------------------------------- button edit di grid
			jGrid.navButtonAdd('#pager_if',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_if").getGridParam('selrow');
					$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_if").getRowData(id); 
						
						$('#dialog_if').dialog('open');
						$("#IFCODE").val(ret.IFCODE);
						$("#FIXEDASSETCODE").val(ret.FIXEDASSETCODE); 
						$("#IFTYPE").val(ret.IFTYPE);  
						
						$("#IFSUBTYPE").val(ret.IFSUBTYPE);
						$("#IFNAME").val(ret.IFNAME); 
						$("#IFLENGTH").val(ret.IFLENGTH);
						
						$("#IFWIDTH").val(ret.IFWIDTH);
						$("#UOM").val(ret.UOM); 
						$("#INSTALLDATE").val(ret.INSTALLDATE);
						
						$("#DEVELOPMENT_COST").val(ret.DEVELOPMENT_COST);
						$("#VOLUME").val(ret.VOLUME); 
						$("#ROLLING").val(ret.ROLLING);
						
						$("#FLAT").val(ret.FLAT);
						$("#LOWLAND").val(ret.LOWLAND); 
						$("#ESTATE").val(ret.ESTATE);
						
						$("#DIVISION").val(ret.FLAT);
						$("#INACTIVEDATE").val(ret.LOWLAND); 
						$("#COMPANY_CODE").val(ret.COMPANY_CODE);
						
						
					} else {
					alert("Please select row");
					} 				
			   }, 
			   position:"last"
			});
			
			//----------------------------------------------------------------button delete di grid
			jGrid.navButtonAdd('#pager_if',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_if").getGridParam('selrow');
					
					if (id)	{
						var ret = jQuery("#list_if").getRowData(id);
						var id = ret.IFCODE;
						hapus(id);		 
					}
					 
					gridReload();
			   }, 
			   position:"last"
			});
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_if").ready(loadView);


		
		//---------------------------------------------------------------- fungsi refresh grid
		function gridReload(){
			//var nm_mask = jQuery("#item_nm").val();
			//var cd_mask = jQuery("#search_cd").val();
			jQuery("#list_if").setGridParam({url:url+"m_infrastructure/read_json_format"}).trigger("reloadGrid");
		}

        var initForm = function(){
        }
        jQuery("#form_if").ready(initForm);
	
	
	
		//---------------------------------------------------------------- button submit data
		jQuery("#submitdata_if").click(function (){
			
			var mode = $("#form_mode").val();
			
			if (mode == "GET"){
				var ifcode = $("#IFCODE").val()
				update(ifcode);
				//alert(mode);
				
			} else if (mode == "POST")
			{
				
				Create();
			}
		});
	
	
		//---------------------------------------------------------------- init form
		function Init1() {  //nilai awal form
              
				$("#list_if").trigger("reloadGrid"); 
                $("#IFCODE").val("");
				$("#FIXEDASSETCODE").val("");
				$("#IFTYPE").val("");
				
				$("#IFSUBTYPE").val("");
				$("#IFNAME").val("");
				$("#IFLENGTH").val("");
				
				$("#IFWIDTH").val("");
				$("#UOM").val("");
				$("#INSTALLDATE").val("");
				
				$("#DEVELOPMENT_COST").val("");
				$("#VOLUME").val("");
				$("#ROLLING").val("");
				
				$("#FLAT").val("");
				$("#LOWLAND").val("");
				$("#ESTATE").val("");
				
				$("#DIVISION").val("");
				$("#INACTIVEDATE").val("");
				$("#COMPANY_CODE").val("");
				
				
				i = 0;
			
		}
		
		//---------------------------------------------------------------- fungsi Add
			
			function Create() {
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['IFCODE'] = $("#IFCODE").val() ;
			postdata['FIXEDASSETCODE'] = $("#FIXEDASSETCODE").val() ;
			postdata['IFTYPE'] = $("#IFTYPE").val() ;
			
			postdata['IFSUBTYPE'] = $("#IFSUBTYPE").val() ;
			postdata['IFNAME'] = $("#IFNAME").val() ;
			postdata['IFLENGTH'] = $("#IFLENGTH").val() ;
			
			postdata['IFWIDTH'] = $("#IFWIDTH").val() ;
			postdata['UOM'] = $("#UOM").val() ;
			postdata['INSTALLDATE'] = $("#INSTALLDATE").val() ;
			
			postdata['DEVELOPMENT_COST'] = $("#DEVELOPMENT_COST").val() ;
			postdata['VOLUME'] = $("#VOLUME").val() ;
			postdata['ROLLING'] = $("#ROLLING").val() ;
			
			postdata['FLAT'] = $("#FLAT").val() ;
			postdata['LOWLAND'] = $("#LOWLAND").val() ;
			postdata['ESTATE'] = $("#ESTATE").val() ;
			
			postdata['DIVISION'] = $("#DIVISION").val() ;
			postdata['INACTIVEDATE'] = $("#INACTIVEDATE").val() ;
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val() ; 
							 
		   // Post it all 
			$.post( url+'m_infrastructure/create', postdata,function(message,status) { 
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
			
			$.post( url+'m_infrastructure/delete/'+id, postdata,function(message,status) { 
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
			postdata['IFCODE'] = $("#IFCODE").val() ;
			postdata['FIXEDASSETCODE'] = $("#FIXEDASSETCODE").val() ;
			postdata['IFTYPE'] = $("#IFTYPE").val() ;
			
			postdata['IFSUBTYPE'] = $("#IFSUBTYPE").val() ;
			postdata['IFNAME'] = $("#IFNAME").val() ;
			postdata['IFLENGTH'] = $("#IFLENGTH").val() ;
			
			postdata['IFWIDTH'] = $("#IFWIDTH").val() ;
			postdata['UOM'] = $("#UOM").val() ;
			postdata['INSTALLDATE'] = $("#INSTALLDATE").val() ;
			
			postdata['DEVELOPMENT_COST'] = $("#DEVELOPMENT_COST").val() ;
			postdata['VOLUME'] = $("#VOLUME").val() ;
			postdata['ROLLING'] = $("#ROLLING").val() ;
			
			postdata['FLAT'] = $("#FLAT").val() ;
			postdata['LOWLAND'] = $("#LOWLAND").val() ;
			postdata['ESTATE'] = $("#ESTATE").val() ;
			
			postdata['DIVISION'] = $("#DIVISION").val() ;
			postdata['INACTIVEDATE'] = $("#INACTIVEDATE").val() ;
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val() ;
			
			
		   // Post it all 
			$.post( url+'m_infrastructure/edit/'+id, postdata,function(message,status) { 
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
			$("#dialog_if").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 520,
				width: 460,
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
 <table id="list_if" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_if" class="scroll" style="text-align:center;"></div>

<form method="POST" id="form_if" action="<?= base_url().'index.php/m_infrastructure/'?>">
<div id="dialog_if">

<table cellpadding="1" cellspacing="0" width="100%" border="0">

<tr><td width="160">Infrastructure Code</td><td>:</td><td><input type="text" size=15 id="IFCODE" /></td></tr>
<tr><td width="160">Fixed Asset Code</td><td>:</td><td><input type="text" size=25 id="FIXEDASSETCODE" /></td></tr>
<tr><td width="160">Infrastructure Type</td><td>:</td><td><?php echo $IFTYPE; ?></td></tr>

<tr><td width="160">Infrastructure Sub Type</td><td>:</td><td> <?php echo $IFSUBTYPE; ?> </td></tr>
<tr><td width="160">Infrastructure Name</td><td>:</td><td><input type="text" size=40 id="IFNAME" /></td></tr>
<tr><td width="120">Length</td><td>:</td><td><input type="text" size=25 id="IFLENGTH" /></td></tr>


<tr><td width="120">Width</td><td>:</td><td><input type="text" size=25 id="IFWIDTH" /></td></tr>
<tr><td width="120">UOM</td><td>:</td><td><input type="text" size=25 id="UOM" /></td></tr>
<tr><td width="160">Install Date</td><td>:</td><td><input type="text" size=25 id="INSTALLDATE" /></td></tr>


<tr><td width="160">Development Cost</td><td>:</td><td><input type="text" size=25 id="DEVELOPMENT_COST" /></td></tr>
<tr><td width="120">Volume</td><td>:</td><td><input type="text" size=25 id="VOLUME" /></td></tr>
<tr><td width="120">Rolling</td><td>:</td><td><input type="text" size=25 id="ROLLING" /></td></tr>

<tr><td width="120">Flat</td><td>:</td><td><input type="text" size=25 id="FLAT" /></td></tr>
<tr><td width="120">Lowland</td><td>:</td><td><input type="text" size=25 id="LOWLAND" /></td></tr>
<tr><td width="120">Estate</td><td>:</td><td><input type="text" size=25 id="ESTATE" /></td></tr>


<tr><td width="120">Division</td><td>:</td><td><input type="text" size=25 id="DIVISION" /></td></tr>
<tr><td width="120">Inactive Date</td><td>:</td><td><input type="text" size=25 id="INACTIVEDATE" /></td></tr>
<tr><td width="120">Company Code</td><td>:</td><td> <?php echo $COMPANY_CODE; ?> </td></tr>

</table>

<input type="hidden" id="form_mode">
<input type="button"  id="submitdata_if" value="Submit"><br />

</div>
</form>

