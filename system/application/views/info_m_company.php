<script type="text/javascript">
        
		//--------------------------------------------------------- url global js
		var url = "<?= base_url().'index.php/' ?>";
		// ----------------------------------------------------------------grid
        var jGrid = null;
        var colNamesT = new Array();
        var colModelT = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';

        colNamesT.push('Company Code');
        colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, width: 100, align:'center'});
        colNamesT.push('Name');
        colModelT.push({name:'COMPANY_NAME',index:'COMPANY_NAME', editable: false, width: 200, align:'center'});
        colNamesT.push('Address');
        colModelT.push({name:'COMPANY_ADDRESS',index:'COMPANY_ADDRESS', editable: false, width: 200, align:'center'});
		colNamesT.push('Phone');
        colModelT.push({name:'COMPANY_PHONE',index:'COMPANY_PHONE', editable: false, width: 100, align:'center'});
      	colNamesT.push('Email');
        colModelT.push({name:'COMPANY_EMAIL',index:'COMPANY_EMAIL', editable: false, width: 100, align:'center'});
		colNamesT.push('NPWP');
        colModelT.push({name:'COMPANY_NPWP',index:'COMPANY_NPWP', editable: false, width: 100, hidden:true, align:'center'});
		colNamesT.push('Flag');
        colModelT.push({name:'COMPANY_FLAG',index:'COMPANY_FLAG', editable: false, width: 100, hidden:true, align:'center'});
      
        var loadView = function()
        {
            jGrid = jQuery("#list_company").jqGrid(
            {
                url:url+'/M_company/read_json_format',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT ,
                rowNum: 20,
                height: 350,
                rowList:[10,20,30],
                imgpath: gridimgpath,
                pager: jQuery('#pager_company'),
                sortname: colNamesT[0],
                viewrecords: true,
                caption:"Company Setup",
                onSelectRow: function(){
                    var id = jQuery("#list_company").getGridParam('selrow');
                    
                }
            });
            jGrid.navGrid('#pager_company',{edit:false,add:false,del:false, search: false, refresh: true});
			
			//----------------------------------------------------------------button add di grid		
			jGrid.navButtonAdd('#pager_company',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$("#form_mode").val('POST');
	    			$('#dialog_company').dialog('open');					
			   }, 
			   position:"last"
			});
			
			//---------------------------------------------------------------- button edit di grid
			jGrid.navButtonAdd('#pager_company',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_company").getGridParam('selrow');
					$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_company").getRowData(id); 
						
						$('#dialog_company').dialog('open');
						$("#COMPANY_CODE").val(ret.COMPANY_CODE); 
						$("#COMPANY_NAME").val(ret.COMPANY_NAME);
		                $("#COMPANY_ADDRESS").val(ret.COMPANY_ADDRESS);
						$("#COMPANY_PHONE").val(ret.COMPANY_PHONE);
						$("#COMPANY_EMAIL").val(ret.COMPANY_EMAIL);
						$("#COMPANY_NPWP").val(ret.COMPANY_NPWP);
						$("#COMPANY_FLAG").val(ret.COMPANY_FLAG);
						
					} else {
					alert("Please select row");
					} 				
			   }, 
			   position:"last"
			});
			
			//----------------------------------------------------------------button delete di grid
			jGrid.navButtonAdd('#pager_company',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_company").getGridParam('selrow');
					
					if (id)	{
						var ret = jQuery("#list_company").getRowData(id);
						var id = ret.COMPANY_CODE;
						hapus(id);		 
					}
					 
					gridReload();
			   }, 
			   position:"last"
			});
			
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_company").ready(loadView);


		
		//---------------------------------------------------------------- fungsi refresh grid
		function gridReload(){
			//var nm_mask = jQuery("#item_nm").val();
			//var cd_mask = jQuery("#search_cd").val();
			jQuery("#list_company").setGridParam({url:url+"M_company/read_json_format"}).trigger("reloadGrid");
		}

        var initForm = function(){
        }
        jQuery("#form_company").ready(initForm);
	
	
	
		//---------------------------------------------------------------- button submit data
		jQuery("#submitdata").click(function (){
			
			var mode = $("#form_mode").val();
			
			if (mode == "GET"){
				var company_code = $("#COMPANY_CODE").val()
				update(company_code);
			} else if (mode == "POST")
			{
				Create();
			}
		});
	
	
		//---------------------------------------------------------------- init form
		function Init1() {  //nilai awal form
              
				$("#list_company").trigger("reloadGrid"); 
                $("#COMPANY_CODE").val("");
                $("#COMPANY_NAME").val("");
				$("#COMPANY_ADDRESS").val("");
				$("#COMPANY_PHONE").val("");
				$("#COMPANY_EMAIL").val("");
				$("#COMPANY_NPWP").val("");
				$("#COMPANY_FLAG").val("");
				
				i = 0;
			
		}
		
		//---------------------------------------------------------------- fungsi Add
			
			function Create() {
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val() ;
			postdata['COMPANY_NAME'] = $("#COMPANY_NAME").val(); 
			postdata['COMPANY_ADDRESS'] = $("#COMPANY_ADDRESS").val() ; 
			postdata['COMPANY_PHONE'] = $("#COMPANY_PHONE").val();
			postdata['COMPANY_EMAIL'] = $("#COMPANY_EMAIL").val();
			postdata['COMPANY_NPWP'] = $("#COMPANY_NPWP").val(); 
			postdata['COMPANY_FLAG'] = $("#COMPANY_FLAG").val();
				 
		   // Post it all 
			$.post( url+'M_company/create', postdata,function(message,status) { 
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
			
			$.post( url+'M_company/delete/'+id, postdata,function(message,status) { 
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
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val() ;
			postdata['COMPANY_NAME'] = $("#COMPANY_NAME").val(); 
			postdata['COMPANY_ADDRESS'] = $("#COMPANY_ADDRESS").val() ; 
			postdata['COMPANY_PHONE'] = $("#COMPANY_PHONE").val();
			postdata['COMPANY_EMAIL'] = $("#COMPANY_EMAIL").val(); 
			postdata['COMPANY_NPWP'] = $("#COMPANY_NPWP").val();
			postdata['COMPANY_FLAG'] = $("#COMPANY_FLAG").val();
			
		   // Post it all 
			$.post( url+'M_company/edit/'+id, postdata,function(message,status) { 
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
			$("#dialog_company").dialog({
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
 <table id="list_company" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_company" class="scroll" style="text-align:center;"></div>

<form method="POST" id="form_company" action="<?= base_url().'index.php/M_company/' ?>">
<div id="dialog_company">

<table>
<tr><td width="120">Company Code</td><td>:</td><td><input type="text" size=8 id="COMPANY_CODE" /></td></tr>
<tr><td>Company Name</td><td>:</td><td><input type="text" size=25 id="COMPANY_NAME" /></td></tr>
<tr><td>Address</td><td>:</td><td><input type="text" size=40 id="COMPANY_ADDRESS" /></td></tr>
<tr><td>Phone</td><td>:</td><td><input type="text" size=25 id="COMPANY_PHONE" /></td></tr>
<tr><td>Email</td><td>:</td><td><input type="text" size=25 id="COMPANY_EMAIL" /></td></tr>
<tr><td>NPWP</td><td>:</td><td><input type="text" size=25 id="COMPANY_NPWP" /></td></tr>
<tr><td>Company Flag</td><td>:</td><td><input type="text" size=25 id="COMPANY_FLAG" /></td></tr>
</table>

<input type="hidden" id="form_mode">
<input type="hidden" id="COMPANY_CODE">
<input type="button"  id="submitdata" value="Submit"><br />

</div>
</form>

