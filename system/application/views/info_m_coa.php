<script type="text/javascript">
        		
		//-------------------------------------------------------- date picker
		$(function() {
		$("#COA_INPUTDATE").datepicker({dateFormat:"yy-mm-dd"});
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
		// $('#last_activity').val().toString()
		
		//--------------------------------------------------------- url global js
		var url = "<?= base_url().'index.php/' ?>";
		
		
		//--------------------------------------------------------- grid
        var jGrid = null;
        var colNamesT = new Array();
        var colModelT = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';
                       
        colNamesT.push('Account Code');
        colModelT.push({name:'ACCOUNTCODE',index:'ACCOUNTCODE', editable: false, width: 100, align:'center'});
        colNamesT.push('Account Type');
        colModelT.push({name:'ACCOUNTTYPE',index:'ACCOUNTTYPE', editable: false, width: 100, align:'center'});
        colNamesT.push('Group Type');
        colModelT.push({name:'COA_GROUPTYPE',index:'COA_GROUPTYPE', editable: false, hidden:true, width: 100, align:'center'});
		colNamesT.push('Operational');
        colModelT.push({name:'COA_OPERATIONAL',index:'COA_OPERATIONAL', editable: false, hidden:true, width: 100, align:'left'});
		colNamesT.push('Description');
        colModelT.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', editable: false, width: 400, align:'left'});
      	colNamesT.push('Status');
        colModelT.push({name:'COA_STATUS',index:'COA_STATUS', editable: false, hidden:true, width: 100, align:'left'});
      	colNamesT.push('Input By');
        colModelT.push({name:'COA_INPUTBY',index:'COA_INPUTBY', editable: false, hidden:true, width: 100, align:'left'});
      	colNamesT.push('Input Date');
        colModelT.push({name:'COA_INPUTDATE',index:'COA_INPUTDATE', editable: false, hidden:true, width: 100, align:'left'});
      
		
		
        var loadView = function()
        {
            jGrid = jQuery("#list_coa").jqGrid(
            {
                url:url+'m_coa/read_json_format',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT ,
                rowNum:20,
                height: 350,
                rowList:[10,20,30],
                imgpath: gridimgpath,
                pager: jQuery('#pager_coa'),
                sortname: colNamesT[0],
                viewrecords: true,
                caption:"coa",
                onSelectRow: function(){
                    var id = jQuery("#list_coa").getGridParam('selrow');
                    //Diadiem.setData(jQuery("#list_coa").getRowData(id));
                }
            });
            jGrid.navGrid('#pager_coa',{edit:false,add:false,del:false, search: false, refresh: true});
			
			//----------------------------------------------------------------button add di grid		
			jGrid.navButtonAdd('#pager_coa',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$("#form_mode").val('POST');
	    			$('#dialog_coa').dialog('open');
					//$("#DATE_INPUT").val(current_date());						
			   }, 
			   position:"last"
			});
			
			//---------------------------------------------------------------- button edit di grid
			
			
			jGrid.navButtonAdd('#pager_coa',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_coa").getGridParam('selrow');
					$("#form_mode").val('GET');
					
					//alert("id="+ret.ACCOUNTCODE+" invdate="+ret.ACCOUNTTYPE+"...");
						
					if (id) 
					{ 
						var ret = jQuery("#list_coa").getRowData(id); 
						$('#dialog_coa').dialog('open');
						$("#ACCOUNTCODE").val(ret.ACCOUNTCODE); 
						$("#ACCOUNTTYPE").val(ret.ACCOUNTTYPE);
		                $("#COA_GROUPTYPE").val(ret.COA_GROUPTYPE);
						$("#COA_OPERATIONAL").val(ret.COA_OPERATIONAL);
						$("#COA_DESCRIPTION").val(ret.COA_DESCRIPTION);
						$("#COA_STATUS").val(ret.COA_STATUS);
						$("#COA_INPUTBY").val(ret.COA_INPUTBY);
						$("#COA_INPUTDATE").val(ret.COA_INPUTDATE);
						
					} else {
					alert("Please select row");
					} 				
			   }, 
			   position:"last"
			});
			
									
			//----------------------------------------------------------------button delete di grid
			jGrid.navButtonAdd('#pager_coa',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_coa").getGridParam('selrow');
					
					if (id)	{
						var ret = jQuery("#list_coa").getRowData(id);
						var id = ret.ACCOUNTCODE;
						hapus(id);		 
					}
					 
					gridReload();
			   }, 
			   position:"last"
			});
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_coa").ready(loadView);
		

        var initForm = function(){        
        }
        jQuery("#form_coa").ready(initForm);
		
		//---------------------------------------------------------------- fungsi refresh grid
		function gridReload(){
			
			jQuery("#list_coa").setGridParam({url:url+"m_coa/read_json_format"}).trigger("reloadGrid");
		}

        var initForm = function(){
        }
        jQuery("#form_coa").ready(initForm);
	
	
	
		//---------------------------------------------------------------- button submit data
		jQuery("#submitdata").click(function (){
			var mode = $("#form_mode").val();
			
			if (mode == "GET"){
				var id = $("#ACCOUNTCODE").val()
				//alert(id);
				update(id);
			} else if (mode == "POST")
			{
				Create();
			}
		});
	
	
		//---------------------------------------------------------------- init form
		function Init1() {  //nilai awal form
              
				$("#list_coa").trigger("reloadGrid"); 
                $("#ACCOUNTCODE").val("");
                $("#ACCOUNTTYPE").val("");
				$("#COA_GROUPTYPE").val("");
				$("#COA_OPERATIONAL").val("");
				$("#COA_DESCRIPTION").val("");
				$("#COA_STATUS").val("");
				$("#COA_INPUTBY").val("");
				$("#COA_INPUTDATE").val("");
		
				i = 0;
			
		}
		
		//---------------------------------------------------------------- fungsi Add
			
			function Create() {
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['ACCOUNTCODE'] = $("#ACCOUNTCODE").val() ;
			postdata['ACCOUNTTYPE'] = $("#ACCOUNTTYPE").val(); 
			postdata['COA_GROUPTYPE'] = $("#COA_GROUPTYPE").val() ; 
			postdata['COA_OPERATIONAL'] = $("#COA_OPERATIONAL").val();
			postdata['COA_DESCRIPTION'] = $("#COA_DESCRIPTION").val();
			postdata['COA_STATUS'] = $("#COA_STATUS").val();
			postdata['COA_INPUTBY'] = $("#COA_INPUTBY").val();
			postdata['COA_INPUTDATE'] = $("#COA_INPUTDATE").val();
			 
				 
		   // Post it all 
			$.post( url+'m_coa/create', postdata,function(message,status) { 
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
			
			$.post( url+'m_coa/delete/'+id, postdata,function(message,status) { 
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
			postdata['ACCOUNTCODE'] = $("#ACCOUNTCODE").val() ;
			postdata['ACCOUNTTYPE'] = $("#ACCOUNTTYPE").val(); 
			postdata['COA_GROUPTYPE'] = $("#COA_GROUPTYPE").val() ; 
			postdata['COA_OPERATIONAL'] = $("#COA_OPERATIONAL").val();
			postdata['COA_DESCRIPTION'] = $("#COA_DESCRIPTION").val();
			postdata['COA_STATUS'] = $("#COA_STATUS").val();
			postdata['COA_INPUTBY'] = $("#COA_INPUTBY").val();
			postdata['COA_INPUTDATE'] = $("#COA_INPUTDATE").val();
			
		   // Post it all 
			$.post( url+'m_coa/edit/'+id, postdata,function(message,status) { 
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
			$("#dialog_coa").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 280,
				width: 280,
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

 <table id="list_coa" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_coa" class="scroll" style="text-align:center;"></div>


<form method="POST" id="form_coa" action="<?= base_url().'index.php/m_coa/' ?>">
<div id="dialog_coa">

	<table cellpadding="1" cellspacing="0" width="100%" border="0">
	<tr>
	<td>Account Code</td><td align="center">:</td><td style="padding-left:10px">
	<input type="text" size=8 id="ACCOUNTCODE" />
	</td>
	</tr>
    <tr><td>Account Type</td><td align="center">:</td><td style="padding-left:10px">
	<input type="text" size=20 id="ACCOUNTTYPE" />
	</td></tr>
	<tr>
	<td>Group Type</td><td align="center">:</td><td style="padding-left:10px">
	<input type="text" size=12 id="COA_GROUPTYPE" />
	</td>
	</tr>
	<tr><td>Operational</td><td align="center">:</td><td style="padding-left:10px" >
	<input type="text" size=12 id="COA_OPERATIONAL" />
	</td></tr>
	<tr><td>Description</td><td align="center">:</td><td style="padding-left:10px">
	<input type="text" size=12 id="COA_DESCRIPTION" />
	</td></tr>
	<tr><td>Status</td><td align="center">:</td><td style="padding-left:10px">
	<input type="text" size=12 id="COA_STATUS" />
	</td></tr>
	<tr><td>Input By</td><td align="center">:</td><td style="padding-left:10px">
	<input type="text" size=12 id="COA_INPUTBY" />
	</td></tr>
	<tr><td>Input Date	</td><td align="center">:</td><td style="padding-left:10px">
	<input type="text" size=12 id="COA_INPUTDATE" />
	</td></tr>
	</table>
<input type="hidden" id="form_mode">
<input type="button"  id="submitdata" value="Submit"><br />


</div>
</form>