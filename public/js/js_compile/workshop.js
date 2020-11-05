		function giveLocType(){		
		var ids = jQuery('#list_wa').getGridParam('selrow'); 
		var rets = jQuery('#list_wa').getRowData(ids); 
		var type = rets.LOCATION_TYPE_CODE;
		return type;
		} 

		var jGrid_wa = null;
		var colNamesT_wa = new Array();
		var colModelT_wa = new Array();

colNamesT_wa.push('no');
colModelT_wa.push({name:'no_wa',index:'no_wa', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_wa.push('id');
colModelT_wa.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});

colNamesT_wa.push('Tgl');
colModelT_wa.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS', editable: false,hidden:true, width: 90, align:'center'});

colNamesT_wa.push('Tipe');
colModelT_wa.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: true,edittype: 'text', editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete(['GC', 'MA', 'VH'],
				  {
				  		dataType: 'ajax',
						width: 320,
						max: 5,
						highlight: false,
						multiple: false,
						scroll: true,
						scrollHeight: 300
					} 
	              )
	             
          }}, width: 100, align:'center'});

colNamesT_wa.push('Location');
colModelT_wa.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,async: false,edittype: 'text',editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { 
				
	            $(elem)
	              .autocomplete( 
				  	  url+'p_workshop_activity/location/'+giveLocType(), {
	                  dataType: 'ajax',
	                  multiple: false,
					  autoFill: false,
					  mustMatch: true,
					  matchContains: false,
				
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_id, result: row.res_name } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_dl :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $('#LOCATION_CODE').val(item.res_name );
	              });
          }}, width: 100, align:'center'});

colNamesT_wa.push('Activity');
colModelT_wa.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true,
				editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { // the moment of magic Wink
	            $(elem)
	              .autocomplete( // for more info check the autocomplete plugin docs
	                url+'p_workshop_activity/activity/'+giveLocType(), {
	                  dataType: 'ajax',
	                  multiple: false,
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_d, result: row.res_id } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_d :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $('#ACTIVITY_CODE').val(item.res_name );
	              });
				  }}, width: 100, align:'center'});
	colNamesT_wa.push('Jam Kerja');
colModelT_wa.push({name:'JAM_KERJA',index:'JAM_KERJA', editable: false,hidden:false, width: 90, align:'center'});
colNamesT_wa.push('company');
colModelT_wa.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false,hidden:true, width: 90, align:'center'});			  
				  
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		var loadView = function()
		        {
            jGrid = jQuery('#list_wa').jqGrid(
            {
                url:url+'p_workshop_activity/grid_p_workshop/xx/xx/xx',
                mtype : 'POST',
                datatype: 'json',
                colNames: colNamesT_wa ,
                colModel: colModelT_wa ,
               	sortname: colNamesT_wa[2],
				pager:jQuery('#pager_wa'),
              	rowNum: 400,
				rownumbers: true,
                height: 370,
                imgpath: gridimgpath,
				sortorder: 'asc',
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				loadComplete: function(){ 
				var ids = jQuery('#list_wa').getDataIDs(); 
				var id = jQuery('#list_wa').getGridParam('selrow'); 
				var rets = jQuery('#list_wa').getRowData(id); 
			
				for(var i=0;i<ids.length;i++)
					{ 
						var cl = ids[i]; 
						
				    	be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update('"+cl+"');\" />"; 
						ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus('"+cl+"');\"/>";
						jQuery("#list_wa").setRowData(ids[i],{action:be+ce}) 
					}
										
				}, 
				afterEditCell: function (id,name,val,iRow,iCol)
					{ 			
					 if(name=='TGL_AKTIVITAS') 
						{ jQuery('#'+iRow+'_TGL_AKTIVITAS','#list_wa').datepicker({dateFormat:'yy-mm-dd'}); } 
					}
            } ); /* tutup jgrid */
			
            jGrid.navGrid('#pager_wa',{edit:false,add:false,del:false, search: false, refresh: true});
			
			 jGrid.navButtonAdd('#pager_wa',{
			   caption:'Add', 
			   buttonicon:'ui-icon-add', 
			   onClickButton: function(){ 
						addrow()
			   }, 
			   position:'last'
			});
        }; /* tutup loadview */
		
        jQuery('#list_wa').ready(loadView);	