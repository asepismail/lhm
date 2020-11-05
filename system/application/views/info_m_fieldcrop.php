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
		
		
		// $('#last_activity').val().toString()
        var jGrid = null;
        var colNamesT = new Array();
        var colModelT = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';

        colNamesT.push('Block ID');
        colModelT.push({name:'BLOCKID',index:'BLOCKID', editable: false, width: 60, align:'center'});
        colNamesT.push('Field Code');
        colModelT.push({name:'FIELDCODE',index:'FIELDCODE', editable: false, width: 80, align:'center'});
        colNamesT.push('Estate');
        colModelT.push({name:'ESTATECODE',index:'ESTATECODE', editable: false, width: 60, align:'center'});
		colNamesT.push('Division Code');
        colModelT.push({name:'DIVISIONCODE',index:'DIVISIONCODE', editable: false, width: 60, align:'center'});
      	colNamesT.push('Description');
        colModelT.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, width: 250, align:'left'});
      	colNamesT.push('Crop Type');
        colModelT.push({name:'CROPTYPE',index:'CROPTYPE', hidden:true, editable: false, width: 70, align:'center'});
      	colNamesT.push('Hectplanted');
        colModelT.push({name:'HECTPLANTED',index:'HECTPLANTED', editable: false, width: 90, align:'center'});
		colNamesT.push('Planting Date');
        colModelT.push({name:'PLANTINGDATE',index:'PLANTINGDATE', editable: false, width: 100, align:'left'});
      	colNamesT.push('Total Stand');
        colModelT.push({name:'TOTSTANDOFFIELD',index:'TOTSTANDOFFIELD', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('Species');
        colModelT.push({name:'SPECIES',index:'SPECIES', editable: false, width: 60, align:'left'});
      	colNamesT.push('Harvcommdate');
        colModelT.push({name:'HARVCOMMDATE',index:'HARVCOMMDATE', editable: false, width: 100, align:'left'});
		colNamesT.push('Total Ha');
        colModelT.push({name:'TOTALHECTARAGE',index:'TOTALHECTARAGE', editable: false, width: 70, align:'left'});
		
		//hidden field on grid
		colNamesT.push('CONCESSIONID');
        colModelT.push({name:'CONCESSIONID',index:'CONCESSIONID', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('COMPANY_CODE');
        colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('NOHGU');
        colModelT.push({name:'NOHGU',index:'NOHGU', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('USAGEID');
        colModelT.push({name:'USAGEID',index:'USAGEID', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('PLANTINGDISTANCE');
        colModelT.push({name:'PLANTINGDISTANCE',index:'PLANTINGDISTANCE', hidden:true, editable: false, width: 60, align:'left'});
		
		colNamesT.push('LASTSUPPHECT');
        colModelT.push({name:'LASTSUPPHECT',index:'LASTSUPPHECT', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('LASTSUPPDATE');
        colModelT.push({name:'LASTSUPPDATE',index:'LASTSUPPDATE', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('STANDPERHECT');
        colModelT.push({name:'STANDPERHECT',index:'STANDPERHECT', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('CHECKROLLPRACTICE');
        colModelT.push({name:'CHECKROLLPRACTICE',index:'CHECKROLLPRACTICE', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('PAYMENTMETHOD');
        colModelT.push({name:'PAYMENTMETHOD',index:'PAYMENTMETHOD', hidden:true, editable: false, width: 60, align:'left'});
		
		colNamesT.push('HEIGHTCLASS');
        colModelT.push({name:'HEIGHTCLASS',index:'HEIGHTCLASS', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('CROPPOLICY');
        colModelT.push({name:'CROPPOLICY',index:'CROPPOLICY', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('YEARREPLANT');
        colModelT.push({name:'YEARREPLANT',index:'YEARREPLANT', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('LONGCARRYPERC');
        colModelT.push({name:'LONGCARRYPERC',index:'LONGCARRYPERC', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('PALMSHARV');
        colModelT.push({name:'PALMSHARV',index:'PALMSHARV', hidden:true, editable: false, width: 60, align:'left'});


		colNamesT.push('HECTHARV');
        colModelT.push({name:'HECTHARV',index:'HECTHARV', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('HECTRESTED');
        colModelT.push({name:'HECTRESTED',index:'HECTRESTED', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('CLONES');
        colModelT.push({name:'CLONES',index:'CLONES', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('FIELDAGE');
        colModelT.push({name:'FIELDAGE',index:'FIELDAGE', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('COSTCENTERID');
        colModelT.push({name:'COSTCENTERID',index:'COSTCENTERID', hidden:true, editable: false, width: 60, align:'left'});
		
		colNamesT.push('INTIPLASMA');
        colModelT.push({name:'INTIPLASMA',index:'INTIPLASMA', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('INACTIVE');
        colModelT.push({name:'INACTIVE',index:'INACTIVE', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('INACTIVEDATE');
        colModelT.push({name:'INACTIVEDATE',index:'INACTIVEDATE', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('TERAINTYPE');
        colModelT.push({name:'TERAINTYPE',index:'TERAINTYPE', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('ROLLING');
        colModelT.push({name:'ROLLING',index:'ROLLING', hidden:true, editable: false, width: 60, align:'left'});

		colNamesT.push('FLAT');
        colModelT.push({name:'FLAT',index:'FLAT', hidden:true, editable: false, width: 60, align:'left'});
		colNamesT.push('LOWLAND');
        colModelT.push({name:'LOWLAND',index:'LOWLAND', hidden:true, editable: false, width: 60, align:'left'});

        var loadView = function()
        {
            jGrid = jQuery("#list_fieldcrop").jqGrid(
            {
                url:url+'M_fieldcrop/read_json_format',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT ,
                rowNum:20,
                height: 350,
                rowList:[10,20,30],
                imgpath: gridimgpath,
                pager: jQuery('#pager_fieldcrop'),
                sortname: colNamesT[0],
                viewrecords: true,
                caption:"Fieldcrop",
                onSelectRow: function(){
                    var id = jQuery("#list_fieldcrop").getGridParam('selrow');
                }
            });
            jGrid.navGrid('#pager_fieldcrop',{edit:false,add:false,del:false, search: false, refresh: true});
			
			//----------------------------------------------------------------button add di grid		
			jGrid.navButtonAdd('#pager_fieldcrop',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$("#form_mode").val('POST');
	    			$('#dialog_fieldcrop').dialog('open');					
			   }, 
			   position:"last"
			});
			
			//---------------------------------------------------------------- button edit di grid
			jGrid.navButtonAdd('#pager_fieldcrop',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_fieldcrop").getGridParam('selrow');
					$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_fieldcrop").getRowData(id); 
						
						$('#dialog_fieldcrop').dialog('open');
						
						$("#CONCESSIONID").val(ret.CONCESSIONID); 
						$("#COMPANY_CODE").val(ret.COMPANY_CODE);
		                $("#NOHGU").val(ret.NOHGU);
						$("#BLOCKID").val(ret.BLOCKID);
						$("#USAGEID").val(ret.USAGEID);
						
						$("#FIELDCODE").val(ret.FIELDCODE);
						$("#ESTATECODE").val(ret.ESTATECODE);			
						$("#DIVISIONCODE").val(ret.DIVISIONCODE); 
						$("#DESCRIPTION").val(ret.DESCRIPTION);
		                $("#CROPTYPE").val(ret.CROPTYPE);
						
						$("#HECTPLANTED").val(ret.HECTPLANTED);
						$("#PLANTINGDATE").val(ret.PLANTINGDATE);			
						$("#PLANTINGDISTANCE").val(ret.PLANTINGDISTANCE); 
						$("#LASTSUPPHECT").val(ret.LASTSUPPHECT);
		                $("#LASTSUPPDATE").val(ret.LASTSUPPDATE);
						
						$("#TOTSTANDOFFIELD").val(ret.TOTSTANDOFFIELD);
						$("#STANDPERHECT").val(ret.STANDPERHECT);			
						$("#CHECKROLLPRACTICE").val(ret.CHECKROLLPRACTICE); 
						$("#PAYMENTMETHOD").val(ret.PAYMENTMETHOD);
		                $("#HEIGHTCLASS").val(ret.HEIGHTCLASS);
						
						$("#CROPPOLICY").val(ret.CROPPOLICY);
						$("#YEARREPLANT").val(ret.YEARREPLANT);			
						$("#LONGCARRYPERC").val(ret.LONGCARRYPERC); 
						$("#SPECIES").val(ret.SPECIES);
		                $("#HARVCOMMDATE").val(ret.HARVCOMMDATE);
						
						$("#PALMSHARV").val(ret.PALMSHARV);
						$("#HECTHARV").val(ret.HECTHARV);			
						$("#HECTRESTED").val(ret.HECTRESTED); 
						$("#CLONES").val(ret.CLONES);
		                $("#FIELDAGE").val(ret.FIELDAGE);
						
						$("#COSTCENTERID").val(ret.COSTCENTERID);			
						$("#INTIPLASMA").val(ret.INTIPLASMA); 
						$("#INACTIVE").val(ret.INACTIVE);
		                $("#INACTIVEDATE").val(ret.INACTIVEDATE);
	
						$("#TERAINTYPE").val(ret.TERAINTYPE);
						$("#TOTALHECTARAGE").val(ret.TOTALHECTARAGE);
						$("#ROLLING").val(ret.ROLLING);
						$("#FLAT").val(ret.FLAT);						
						$("#LOWLAND").val(ret.HECTARAGE); 

					} else {
					alert("Please select row");
					} 				
			   }, 
			   position:"last"
			});
			
			//----------------------------------------------------------------button delete di grid
			jGrid.navButtonAdd('#pager_fieldcrop',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
	    			var id = jQuery("#list_fieldcrop").getGridParam('selrow');
					
					if (id)	{
						var ret = jQuery("#list_fieldcrop").getRowData(id);
						var fc_id = ret.FIELDCODE;
						//alert(block_id);
						hapus(fc_id);		 
					}
					 
					gridReload();
			   }, 
			   position:"last"
			});
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_fieldcrop").ready(loadView);


        var initForm = function(){
        }
        jQuery("#form_fieldcrop").ready(initForm);
		
		
		//---------------------------------------------------------------- button submit data
		jQuery("#submitdata_fc").click(function (){
			
			var mode = $("#form_mode").val();
			
			if (mode == "GET"){
				var field_id = $("#FIELDCODE").val()
				//alert(mode);
				update(field_id);
			} else if (mode == "POST")
			{
				//alert(mode);
				Create();
			}
		});
	
	
		//---------------------------------------------------------------- init form
		function Init1() {  //nilai awal form
              
				$("#list_fieldcrop").trigger("reloadGrid"); 
						
						$("#CONCESSIONID").val(""); 
						$("#COMPANY_CODE").val("");
		                $("#NOHGU").val("");
						$("#BLOCKID").val("");
						$("#USAGEID").val("");
						
						$("#FIELDCODE").val("");
						$("#ESTATECODE").val("");			
						$("#DIVISIONCODE").val(""); 
						$("#DESCRIPTION").val("");
		                $("#CROPTYPE").val("");
						
						$("#HECTPLANTED").val("");
						$("#PLANTINGDATE").val("");			
						$("#PLANTINGDISTANCE").val(""); 
						$("#LASTSUPPHECT").val("");
		                $("#LASTSUPPDATE").val("");
						
						$("#TOTSTANDOFFIELD").val("");
						$("#STANDPERHECT").val("");			
						$("#CHECKROLLPRACTICE").val(""); 
						$("#PAYMENTMETHOD").val("");
		                $("#HEIGHTCLASS").val("");
						
						$("#CROPPOLICY").val("");
						$("#YEARREPLANT").val("");			
						$("#LONGCARRYPERC").val(""); 
						$("#SPECIES").val("");
		                $("#HARVCOMMDATE").val("");
						
						$("#PALMSHARV").val("");
						$("#HECTHARV").val("");			
						$("#HECTRESTED").val(""); 
						$("#CLONES").val("");
		                $("#FIELDAGE").val("");
						
						$("#COSTCENTERID").val("");			
						$("#INTIPLASMA").val(""); 
						$("#INACTIVE").val("");
		                $("#INACTIVEDATE").val("");
	
						$("#TERAINTYPE").val("");
						$("#TOTALHECTARAGE").val("");
						$("#ROLLING").val("");
						$("#FLAT").val("");						
						$("#LOWLAND").val(""); 
               				
				i = 0;
			
		}
		
		//---------------------------------------------------------------- fungsi Add
			
			function Create() {
			var postdata = {}; 
			
		  	// Data dari form
			postdata['CONCESSIONID'] = $("#CONCESSIONID").val() ;
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val(); 
			postdata['NOHGU'] = $("#NOHGU").val() ; 
			postdata['USAGEID'] = $("#USAGEID").val();
			postdata['FIELDCODE'] = $("#FIELDCODE").val();
			 
			postdata['BLOCKID'] = $("#BLOCKID").val();
			postdata['ESTATECODE'] = $("#ESTATECODE").val();
			postdata['DIVISIONCODE'] = $("#DIVISIONCODE").val();
			postdata['DESCRIPTION'] = $("#DESCRIPTION").val();
			postdata['CROPTYPE'] = $("#CROPTYPE").val();
			
			postdata['HECTPLANTED'] = $("#HECTPLANTED").val();
			postdata['PLANTINGDATE'] = $("#PLANTINGDATE").val();
			postdata['PLANTINGDISTANCE'] = $("#PLANTINGDISTANCE").val();
			postdata['LASTSUPPHECT'] = $("#LASTSUPPHECT").val();
			postdata['LASTSUPPDATE'] = $("#LASTSUPPDATE").val();
			
			postdata['TOTSTANDOFFIELD'] = $("#TOTSTANDOFFIELD").val();
			postdata['STANDPERHECT'] = $("#STANDPERHECT").val();
			postdata['CHECKROLLPRACTICE'] = $("#CHECKROLLPRACTICE").val();
			postdata['PAYMENTMETHOD'] = $("#PAYMENTMETHOD").val();
			postdata['HEIGHTCLASS'] = $("#HEIGHTCLASS").val();
			
			postdata['CROPPOLICY'] = $("#CROPPOLICY").val();
			postdata['YEARREPLANT'] = $("#YEARREPLANT").val();
			postdata['LONGCARRYPERC'] = $("#LONGCARRYPERC").val();
			postdata['SPECIES'] = $("#SPECIES").val();
			postdata['HARVCOMMDATE'] = $("#HARVCOMMDATE").val();
			
			postdata['PALMSHARV'] = $("#PALMSHARV").val();
			postdata['HECTHARV'] = $("#HECTHARV").val();
			postdata['HECTRESTED'] = $("#HECTRESTED").val();
			postdata['CLONES'] = $("#CLONES").val();
			postdata['FIELDAGE'] = $("#FIELDAGE").val();
			
			postdata['COSTCENTERID'] = $("#COSTCENTERID").val();
			postdata['INTIPLASMA'] = $("#INTIPLASMA").val();
			postdata['INACTIVE'] = $("#INACTIVE").val();
			postdata['INACTIVEDATE'] = $("#INACTIVEDATE").val();
			postdata['TERAINTYPE'] = $("#TERAINTYPE").val();
			
			postdata['TOTALHECTARAGE'] = $("#TOTALHECTARAGE").val();
			postdata['ROLLING'] = $("#ROLLING").val();
			postdata['FLAT'] = $("#FLAT").val();
			postdata['LOWLAND'] = $("#LOWLAND").val();
										 
		   	// Post it all 
			$.post( url+'M_fieldcrop/create', postdata,function(message,status) { 
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
			
			$.post( url+'M_fieldcrop/delete/'+id, postdata,function(message,status) { 
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
			postdata['USAGEID'] = $("#USAGEID").val();
			postdata['FIELDCODE'] = $("#FIELDCODE").val();
			 
			postdata['BLOCKID'] = $("#BLOCKID").val();
			postdata['ESTATECODE'] = $("#ESTATECODE").val();
			postdata['DIVISIONCODE'] = $("#DIVISIONCODE").val();
			postdata['DESCRIPTION'] = $("#DESCRIPTION").val();
			postdata['CROPTYPE'] = $("#CROPTYPE").val();
			
			postdata['HECTPLANTED'] = $("#HECTPLANTED").val();
			postdata['PLANTINGDATE'] = $("#PLANTINGDATE").val();
			postdata['PLANTINGDISTANCE'] = $("#PLANTINGDISTANCE").val();
			postdata['LASTSUPPHECT'] = $("#LASTSUPPHECT").val();
			postdata['LASTSUPPDATE'] = $("#LASTSUPPDATE").val();
			
			postdata['TOTSTANDOFFIELD'] = $("#TOTSTANDOFFIELD").val();
			postdata['STANDPERHECT'] = $("#STANDPERHECT").val();
			postdata['CHECKROLLPRACTICE'] = $("#CHECKROLLPRACTICE").val();
			postdata['PAYMENTMETHOD'] = $("#PAYMENTMETHOD").val();
			postdata['HEIGHTCLASS'] = $("#HEIGHTCLASS").val();
			
			postdata['CROPPOLICY'] = $("#CROPPOLICY").val();
			postdata['YEARREPLANT'] = $("#YEARREPLANT").val();
			postdata['LONGCARRYPERC'] = $("#LONGCARRYPERC").val();
			postdata['SPECIES'] = $("#SPECIES").val();
			postdata['HARVCOMMDATE'] = $("#HARVCOMMDATE").val();
			
			postdata['PALMSHARV'] = $("#PALMSHARV").val();
			postdata['HECTHARV'] = $("#HECTHARV").val();
			postdata['HECTRESTED'] = $("#HECTRESTED").val();
			postdata['CLONES'] = $("#CLONES").val();
			postdata['FIELDAGE'] = $("#FIELDAGE").val();
			
			postdata['COSTCENTERID'] = $("#COSTCENTERID").val();
			postdata['INTIPLASMA'] = $("#INTIPLASMA").val();
			postdata['INACTIVE'] = $("#INACTIVE").val();
			postdata['INACTIVEDATE'] = $("#INACTIVEDATE").val();
			postdata['TERAINTYPE'] = $("#TERAINTYPE").val();
			
			postdata['TOTALHECTARAGE'] = $("#TOTALHECTARAGE").val();
			postdata['ROLLING'] = $("#ROLLING").val();
			postdata['FLAT'] = $("#FLAT").val();
			postdata['LOWLAND'] = $("#LOWLAND").val();
									 
			
		   // Post it all 
			$.post( url+'M_fieldcrop/edit/'+id, postdata,function(message,status) { 
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
			$("#dialog_fieldcrop").dialog({
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
 <form method="POST" id="form_fieldcrop" action="http://localhost/lhm/index.php/M_fieldcrop/">

</form>
 <table id="list_fieldcrop" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_fieldcrop" class="scroll" style="text-align:center;"></div>

<form method="POST" id="form_fieldcrop" action="<?= base_url().'index.php/M_fieldcrop/' ?>">
<div id="dialog_fieldcrop">


<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr><td>Consession ID</td><td>:</td><td><input type="text" size=25 id="CONCESSIONID" /></td></tr>
<tr><td>Company Code</td><td>:</td><td><input type="text" size=25 id="COMPANY_CODE" /></td></tr>
<tr><td>HGU</td><td>:</td><td><input type="text" size=25 id="NOHGU" /></td></tr>
<tr><td>Usage ID</td><td>:</td><td><input type="text" size=25 id="USAGEID" /></td></tr>
<tr><td>Fieldcode</td><td>:</td><td><input type="text" size=25 id="FIELDCODE" /></td></tr>
<tr><td>Block</td><td>:</td><td><input type="text" size=25 id="BLOCKID" /></td></tr>

<tr><td>Estate</td><td>:</td><td><input type="text" size=25 id="ESTATECODE" /></td></tr>
<tr><td>Division</td><td>:</td><td><input type="text" size=25 id="DIVISIONCODE" /></td></tr>
<tr><td>Description</td><td>:</td><td><input type="text" size=25 id="DESCRIPTION" /></td></tr>
<tr><td>Croptype</td><td>:</td><td><input type="text" size=25 id="CROPTYPE" /></td></tr>
<tr><td>Hect Planted</td><td>:</td><td><input type="text" size=25 id="HECTPLANTED" /></td></tr>

<tr><td>Planted Date</td><td>:</td><td><input type="text" size=25 id="PLANTINGDATE" /></td></tr>
<tr><td>Planting Distance</td><td>:</td><td><input type="text" size=25 id="PLANTINGDISTANCE" /></td></tr>
<tr><td>Last Supp Hect</td><td>:</td><td><input type="text" size=25 id="LASTSUPPHECT" /></td></tr>
<tr><td>Lasr Update</td><td>:</td><td><input type="text" size=25 id="LASTSUPPDATE" /></td></tr>
<tr><td>Total Stand Of Field</td><td>:</td><td><input type="text" size=25 id="TOTSTANDOFFIELD" /></td></tr>

<tr><td>Stand Per Hect</td><td>:</td><td><input type="text" size=25 id="STANDPERHECT" /></td></tr>
<tr><td>Checkroll Practice</td><td>:</td><td><input type="text" size=25 id="CHECKROLLPRACTICE" /></td></tr>
<tr><td>Payment Method</td><td>:</td><td><input type="text" size=25 id="PAYMENTMETHOD" /></td></tr>
<tr><td>Height Class</td><td>:</td><td><input type="text" size=25 id="HEIGHTCLASS" /></td></tr>
<tr><td>Crop Policy</td><td>:</td><td><input type="text" size=25 id="CROPPOLICY" /></td></tr>

<tr><td>Year Replant</td><td>:</td><td><input type="text" size=25 id="YEARREPLANT" /></td></tr>
<tr><td>Long Carry</td><td>:</td><td><input type="text" size=25 id="LONGCARRYPERC" /></td></tr>
<tr><td>Species</td><td>:</td><td><input type="text" size=25 id="SPECIES" /></td></tr>
<tr><td>Harvcommdate</td><td>:</td><td><input type="text" size=25 id="HARVCOMMDATE" /></td></tr>
<tr><td>Palm Sharv</td><td>:</td><td><input type="text" size=25 id="PALMSHARV" /></td></tr>

<tr><td>Hectharv</td><td>:</td><td><input type="text" size=25 id="HECTHARV" /></td></tr>
<tr><td>Hectrested</td><td>:</td><td><input type="text" size=25 id="HECTRESTED" /></td></tr>
<tr><td>Clones</td><td>:</td><td><input type="text" size=25 id="CLONES" /></td></tr>
<tr><td>Field Age</td><td>:</td><td><input type="text" size=25 id="FIELDAGE" /></td></tr>
<tr><td>Cost Center ID</td><td>:</td><td><input type="text" size=25 id="COSTCENTERID" /></td></tr>

<tr><td>Intiplasma</td><td>:</td><td><input type="text" size=25 id="INTIPLASMA" /></td></tr>
<tr><td>Inactive</td><td>:</td><td><input type="text" size=25 id="INACTIVE" /></td></tr>
<tr><td>Inactive Date</td><td>:</td><td><input type="text" size=25 id="INACTIVEDATE" /></td></tr>
<tr><td>Terrain Type</td><td>:</td><td><input type="text" size=25 id="TERAINTYPE" /></td></tr>
<tr><td>Total Hectarage</td><td>:</td><td><input type="text" size=25 id="TOTALHECTARAGE" /></td></tr>

<tr><td>Rolling</td><td>:</td><td><input type="text" size=25 id="ROLLING" /></td></tr>
<tr><td>Flat</td><td>:</td><td><input type="text" size=25 id="FLAT" /></td></tr>
<tr><td>Lowland</td><td>:</td><td><input type="text" size=25 id="LOWLAND" /></td></tr>

</table>

<input type="hidden" id="form_mode">
<input type="button"  id="submitdata_fc" value="Submit"><br />

</div>
</form>