<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br>
<div id='main_form'>
    <div id"gridSearch">  
    	<table border="0" class="teks_"  cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">Periode</td><td>:</td><td class="fieldcell"><? echo $periode; ?></td>
            </tr> 
        </table>
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_ba" class="scroll"></table> 
            <div id="pager_ba" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" id="add_ba" onclick="input_ba()">Input Daily Production Report</button>&nbsp;
        <button class="testBtn" type="submit" id="add_ba" onclick="pdf()">Pdf Report Per Period</button>&nbsp;
        <button class="testBtn" type="submit" id="add_ba" onclick="excel()">Excel Report Per Period</button>&nbsp;
    </div>

</div> 
<!-- start here -->
<div id="frm_ba">
    <div id="fragment-1" class="panes">
    	<br>
        <table width="100%" border="0" class="teks_" id="input_table" >
            <tr>
                <td class="labelcell"><p tabindex="1">Date</p></td>
                <td>:</td>
                <td class="fieldcell">
	                <input tabindex="1" type="hidden" id="txt_idba" name="nt_input" maxlength="50"/> 
                    <input type="text" id="txt_tglba"/> 
                    <input tabindex="1" type="hidden" id="txt_sta" name="nt_input" maxlength="50"/>
                    <input tabindex="100" type="hidden" id="txt_company" name="nt_input" maxlength="50"/> 
                </td>
                <td>*</td>
                <td></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="labelcell">Quality Control</td><td>:</td>
                <td class="fieldcell">
                    <input tabindex="1" type="text" id="txt_qc" name="nt_input" maxlength="100"/>							
                </td>
                <td></td>
                <td></td>
                <td  class="labelcell">Note</td>
                <td>:</td>
                <td rowspan="5" class="fieldcell"><textarea tabindex="6" name="nt_input" id="txt_desc" cols="45" rows="4"></textarea></td>
           	</tr>
            <tr>
           		<td class="labelcell">Mill Manager</td><td>:</td>
           		<td class="fieldcell">
                	<input tabindex="2" type="text" id="txt_manager" name="nt_input" maxlength="100"/>							
              	</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
          	</tr>
           	<tr>
             	<td class="labelcell">KTU</td><td>:</td>
                <td class="fieldcell">
               		<input tabindex="3" type="text" id="txt_ktu" name="nt_input" maxlength="100"/>							
               	</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
           	</tr>
          	<tr>
            	<td class="labelcell">Administratur</td><td>:</td>
                <td class="fieldcell">
              		<input tabindex="4" type="text" id="txt_adm" name="nt_input" maxlength="100"/>							
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
           	</tr> 
            <tr>
                <td class="labelcell">Ast. Labor</td>
                <td>:</td>
                <td class="fieldcell">
                	<input tabindex="4" type="text" id="txt_lab" name="nt_input" maxlength="100"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>                                                               
      	</table>
            <!-- start tab --> 
            <div id="tabs" style="width:98%;margin-top:25px">
                <ul>
                    <!-- <li><a href="#tabs-1">FFB</a></li>-->                    
                    <li><a href="#tabs-2">RECEIPT</a></li>
                    <li><a href="#tabs-3">DISPATCH</a></li>
                    <li><a href="#tabs-4">SOUNDING STOCK</a></li>
                    <li><a href="#tabs-5">STOCK</a></li>                    
                    <!-- <li><a href="#tabs-6">PERFORMANCE</a></li>
                    <li><a href="#tabs-7">GRADING CRITERIA</a></li> -->
                </ul>
                <!-- start FFB tab --> 
                <!--
                <div id="tabs-1">
                    <table width="100%" border="0" class="teks_" id="input_table" >
                    	<tr>
                            <td class="labelcell">Inti</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="6" type="text" id="txt_inti" name="txt_inti" maxlength="100" disabled="disabled"/>							
                            </td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Plasma</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="7" type="text" id="txt_plasma" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Supplier</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="8" type="text" id="txt_supplier" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Asosiasi/Group</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="9" type="text" id="txt_group" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Balance Yesterday</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="10" type="text" id="txt_balance_yesterday" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                    	</tr>
                        <tr>
                            <td class="labelcell">Lori Olah</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="10" type="text" id="txt_lori_olah" name="txt_lori_olah" maxlength="100"/>							
                            </td>
                    	</tr>
                        <tr>
                            <td class="labelcell">Lori Restan</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="10" type="text" id="txt_lori_restan" name="txt_lori_restan" maxlength="100"/>							
                            </td>
                    	</tr>
                        <tr>
                            <td class="labelcell">FFB Processed</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="11" type="text" id="txt_ffb_processed" name="nt_input" maxlength="100" disabled="disabled"/><button class="testBtn" type="submit" id="cmd_recalculate2" onclick="processedAutoComplete()">Recalculate</button>							
                            </td>
                    	</tr>  
                        <tr>
                            <td class="labelcell">Average cage weight<td>:</td>
                            <td class="fieldcell">
                                <input tabindex="12" type="text" id="txt_cage_weight" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                    	</tr>
                    </table>
                </div>
                -->
                <!-- end FFB tab -->                                
                <!-- start PRODUCTION tab --> 
                <div id="tabs-2">
                <p>                    
                    <div id="prod">
                    <table id="list_production" class="scroll" cellpadding="0" cellspacing="0"></table>
                    <div id="pager_production" class="scroll" style="text-align:center;"></div>
                    </div>
                </p>
                
                </div>
                <!-- end PRODUCTION tab -->
                <!-- start DISPATCH tab --> 
                <div id="tabs-3">
                <p>                    
                    <div id="dispatch">
                    <table id="list_dispatch" class="scroll" cellpadding="0" cellspacing="0"></table>
                    <div id="pager_dispatch" class="scroll" style="text-align:center;"></div>
                    </div>
                </p>
                
                </div>
                <!-- end DISPATCH tab -->
                <!-- start STOCK tab -->              
                <div id="tabs-5">
                <p>                    
                    <div id="stock">
                    <table id="list_stock" class="scroll" cellpadding="0" cellspacing="0"></table>
                    <div id="pager_stock" class="scroll" style="text-align:center;"></div>
                    </div>
                </p>
                
                </div>
                <!-- end STOCK tab -->
                <!-- start STOCK CPO/KERNEL tab --> 
                <div id="tabs-4">
                <p>                    
                    <div id="prod">
                    <table id="list_cpo" class="scroll" cellpadding="0" cellspacing="0"></table>
                    <div id="pager_cpo" class="scroll" style="text-align:center;"></div>
                    </div>
                </p>
                
                </div>
                <!-- end CPO/KERNEL tab -->
                <!-- start PERFORMANCE tab -->
                <!-- 
                <div id="tabs-6">
                    <table width="100%" border="0" class="teks_" id="input_table" >
                    	<tr>
                            <td class="labelcell">Processed hour</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="13" type="text" id="txt_processed" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                            <td class="fieldcell"></td>
                            <td class="fieldcell"><button class="testBtn" type="submit" id="cmd_recalculate" onclick="hourAutoComplete()">Recalculate</button></td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Throughput</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="14" type="text" id="txt_throughput" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                            <td class="fieldcell"></td>
                            <td class="fieldcell"></td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Mill Utilization</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="15" type="text" id="txt_mill" name="nt_input" maxlength="100" disabled="disabled"/>							
                            </td>
                            <td class="fieldcell"></td>
                            <td class="fieldcell"></td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Process Hour</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="15" type="text" id="txt_hour_from" name="nt_input" maxlength="100"/>					
                            </td>
                            <td class="labelcell">s/d</td>
                            <td class="fieldcell"><input tabindex="15" type="text" id="txt_hour_to" name="nt_input" maxlength="100"/></td>
                    	</tr>     
                        <tr>
                            <td class="labelcell">CBC Hour</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="15" type="text" id="txt_cbc_from" name="nt_input" maxlength="100"/>					
                            </td>
                            <td class="labelcell">s/d</td>
                            <td class="fieldcell"><input tabindex="15" type="text" id="txt_cbc_to" name="nt_input" maxlength="100"/></td>
                    	</tr>
                    </table>
                </div>
                -->
                <!-- end PERFORMANCE tab -->                
                <!-- start GRADING CRITERIA tab --> 
                <!-- 
                <div id="tabs-7">
                	<table width="100%" border="0" class="teks_" id="input_table" >
                    	<tr>
                            <td class="labelcell" width="100%">Buah Mentah</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="13" type="text" id="txt_crud" name="nt_input" maxlength="100" />							
                            </td>
                    	</tr> 
                        <tr>
                            <td class="labelcell">Buah Busuk</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="14" type="text" id="txt_busuk" name="nt_input" maxlength="100" />							
                            </td>
                    	</tr>                           
                        <tr>
                            <td class="labelcell">Tangkai Panjang</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="15" type="text" id="txt_tangkai" name="nt_input" maxlength="100" />							
                            </td>
                    	</tr>  
                        <tr>
                            <td class="labelcell">Janjang Kosong</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="15" type="text" id="txt_jjk" name="nt_input" maxlength="100" />							
                            </td>
                    	</tr>     
                        <tr>
                            <td class="labelcell">Brondolan</td><td>:</td>
                            <td class="fieldcell">
                                <input tabindex="15" type="text" id="txt_brondolan" name="nt_input" maxlength="100"/>							
                            </td>
                    	</tr>                                   
                    </table>
                </div>
                -->
<!-- end GRADING CRITERIA tab -->
            </div>
            <!-- end tab --> 
        
            <div id="btn_section">
                <button class="testBtn" type="submit" id="cmd_newNote" onclick="save_ba()">SAVE</button>&nbsp;
                <button class="testBtn" type="submit" id="cmd_appNote" onclick="approve()">APPROVE</button>&nbsp;
                <button class="testBtn" type="submit" id="cmd_opnNote" onclick="reopen()">REOPEN</button>&nbsp;
            </div>              
    </div>
    <input type="hidden" id="txt_frmMode"> 
</div>

<div id="frm_load">
    Wait...
</div>

<div id="search_form"></div>
</body>
<script type="text/javascript">
		<!-- start stock cpo grid -->

/* Grid User Group*/
var jGrid_cpo = null;
var colNamesT_cpo = new Array();
var colModelT_cpo= new Array();

colNamesT_cpo.push('no');
colModelT_cpo.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 70, align:'center'});

colNamesT_cpo.push('CODE');
colModelT_cpo.push({name:'ID_STORAGE',index:'ID_STORAGE', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_cpo.push('STORAGE');
colModelT_cpo.push({name:'STORAGE',index:'STORAGE', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_cpo.push('COMPANY_CODE');
colModelT_cpo.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 75, align:'left'});

colNamesT_cpo.push('ID_STORAGE_STOCK');
colModelT_cpo.push({name:'ID_STORAGE_STOCK',index:'ID_STORAGE_STOCK', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_cpo.push('STOCK (kg)');
colModelT_cpo.push({name:'WEIGHT',index:'WEIGHT', editable: false, hidden:false, width: 100, align:'right'});

colNamesT_cpo.push('FFA/BROKEN');
colModelT_cpo.push({name:'FFA',index:'FFA', editable: true, hidden:false, width: 100, align:'right'});

colNamesT_cpo.push('MOIST');
colModelT_cpo.push({name:'MOISTURE',index:'MOISTURE', editable: true, hidden:false, width: 100, align:'right'});

colNamesT_cpo.push('DIRT');
colModelT_cpo.push({name:'DIRT',index:'DIRT', editable: true, hidden:false, width: 100, align:'right'});
	
var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
	jGrid_va = jQuery("#list_cpo").jqGrid(
            {
                url:url+'s_stock_cpo/LoadData_Storage/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_cpo,
                colModel: colModelT_cpo,
                sortname: colModelT_cpo[1].name,
                pager:jQuery("#pager_cpo"),
                rowNum: 400,
                rownumbers: true,
                height: 100,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_cpo").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];                                                        
                            pr = "<img title='Print' src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_cpo").setRowData(ids[i],{act:pr}) 
                        }
                                            
                    },
                afterEditCell: function (id,name,val,iRow,iCol)
                    {             
                     if(name=='DATE')
                      { jQuery("#"+iRow+"_DATE","#list_cpo").datepicker({dateFormat:"yy-mm-dd"});} 
                    }
            });
            jGrid_va.navGrid('#pager_cpo',{edit:false,del:false,add:false, search: false, refresh: true});
         }
jQuery("#list_cpo").ready(loadView);

<!-- end stock cpo grid -->

<!-- start production grid -->

/* Grid User Group*/
var jGrid_prod = null;
var colNamesT_prod = new Array();
var colModelT_prod= new Array();

colNamesT_prod.push('no');
colModelT_prod.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 100, align:'center'});

colNamesT_prod.push('CODE');
colModelT_prod.push({name:'ID_COMMODITY',index:'ID_COMMODITY', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_prod.push('COMMODITY NAME');
colModelT_prod.push({name:'COMMODITY',index:'COMMODITY', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_prod.push('COMPANY_CODE');
colModelT_prod.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT_prod.push('ID_PRODUCTION');
colModelT_prod.push({name:'ID_PRODUCTION',index:'ID_PRODUCTION', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_prod.push('PRODUCTION (kg)');
colModelT_prod.push({name:'WEIGHT',index:'WEIGHT', editable: false, hidden:false, width: 150, align:'right'});

colNamesT_prod.push('FFA/BROKEN');
colModelT_prod.push({name:'FFA',index:'FFA', editable: true, hidden:false, width: 100, align:'right'});

colNamesT_prod.push('MOISTURE');
colModelT_prod.push({name:'MOISTURE',index:'MOISTURE', editable: true, hidden:false, width: 100, align:'right'});

colNamesT_prod.push('DIRTY');
colModelT_prod.push({name:'DIRT',index:'DIRT', editable: true, hidden:false, width: 80, align:'right'});
	
var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
	jGrid_va = jQuery("#list_production").jqGrid(
            {
                url:url+'s_stock_cpo/LoadData_Commodities/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_prod,
                colModel: colModelT_prod,
                sortname: colModelT_prod[1].name,
                pager:jQuery("#pager_production"),
                rowNum: 400,
                rownumbers: true,
                height: 175,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_production").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                                          
                            pr = "<img title='Print' src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_production").setRowData(ids[i],{act:pr}) 
                        }
                                            
                    },
                afterEditCell: function (id,name,val,iRow,iCol)
                    {             
                     if(name=='DATE')
                      { jQuery("#"+iRow+"_DATE","#list_production").datepicker({dateFormat:"yy-mm-dd"});} 
                    }
            });
            jGrid_va.navGrid('#pager_production',{edit:false,del:false,add:false, search: false, refresh: true});
         }
jQuery("#list_production").ready(loadView);

<!-- end production grid -->

<!-- start dispatch grid -->

/* Grid User Group*/
var jGrid_dispatch = null;
var colNamesT_dispatch = new Array();
var colModelT_dispatch= new Array();

colNamesT_dispatch.push('no');
colModelT_dispatch.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 100, align:'center'});

colNamesT_dispatch.push('CODE');
colModelT_dispatch.push({name:'ID_COMMODITY',index:'ID_COMMODITY', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_dispatch.push('COMMODITY NAME');
colModelT_dispatch.push({name:'COMMODITY',index:'COMMODITY', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_dispatch.push('COMPANY_CODE');
colModelT_dispatch.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT_dispatch.push('ID_PRODUCTION');
colModelT_dispatch.push({name:'ID_PRODUCTION',index:'ID_PRODUCTION', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_dispatch.push('DISPATCH (kg)');
colModelT_dispatch.push({name:'WEIGHT',index:'WEIGHT', editable: false, hidden:false, width: 150, align:'right'});

colNamesT_dispatch.push('FFA/BROKEN');
colModelT_dispatch.push({name:'FFA',index:'FFA', editable: true, hidden:false, width: 100, align:'right'});

colNamesT_dispatch.push('MOISTURE');
colModelT_dispatch.push({name:'MOISTURE',index:'MOISTURE', editable: true, hidden:false, width: 100, align:'right'});

colNamesT_dispatch.push('DIRTY');
colModelT_dispatch.push({name:'DIRT',index:'DIRT', editable: true, hidden:false, width: 80, align:'right'});
	
var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
	jGrid_va = jQuery("#list_dispatch").jqGrid(
            {
                url:url+'s_stock_cpo/LoadData_Commodity/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_dispatch,
                colModel: colModelT_dispatch,
                sortname: colModelT_dispatch[1].name,
                pager:jQuery("#pager_dispatch"),
                rowNum: 400,
                rownumbers: true,
                height: 175,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_dispatch").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];                            
                            pr = "<img title='Print' src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_dispatch").setRowData(ids[i],{act:pr}) 
                        }
                                            
                    },
                afterEditCell: function (id,name,val,iRow,iCol)
                    {             
                     if(name=='DATE')
                      { jQuery("#"+iRow+"_DATE","#list_dispatch").datepicker({dateFormat:"yy-mm-dd"});} 
                    }
            });
            jGrid_va.navGrid('#pager_dispatch',{edit:false,del:false,add:false, search: false, refresh: true});
         }
jQuery("#list_dispatch").ready(loadView);

<!-- end dispatch grid -->
<!-- start stock grid -->

var jGrid_stock = null;
var colNamesT_stock = new Array();
var colModelT_stock= new Array();

colNamesT_stock.push('no');
colModelT_stock.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 100, align:'center'});

colNamesT_stock.push('CODE');
colModelT_stock.push({name:'ID_COMMODITY',index:'ID_COMMODITY', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_stock.push('COMMODITY NAME');
colModelT_stock.push({name:'COMMODITY',index:'COMMODITY', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_stock.push('COMPANY_CODE');
colModelT_stock.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT_stock.push('ID_PRODUCTION');
colModelT_stock.push({name:'ID_PRODUCTION',index:'ID_PRODUCTION', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_stock.push('STOCK (kg)');
colModelT_stock.push({name:'WEIGHT',index:'WEIGHT', editable: false, hidden:false, width: 150, align:'right'});

colNamesT_stock.push('FFA/BROKEN');
colModelT_stock.push({name:'FFA',index:'FFA', editable: true, hidden:true, width: 100, align:'right'});

colNamesT_stock.push('MOISTURE');
colModelT_stock.push({name:'MOISTURE',index:'MOISTURE', editable: true, hidden:true, width: 100, align:'right'});

colNamesT_stock.push('DIRTY');
colModelT_stock.push({name:'DIRT',index:'DIRT', editable: true, hidden:true, width: 80, align:'right'});
	
var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
	jGrid_va = jQuery("#list_stock").jqGrid(
            {
                url:url+'s_stock_cpo/LoadData_Commodity/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_stock,
                colModel: colModelT_stock,
                sortname: colModelT_stock[1].name,
                pager:jQuery("#pager_stock"),
                rowNum: 400,
                rownumbers: true,
                height: 175,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_stock").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];                            
                            pr = "<img title='Print' src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_stock").setRowData(ids[i],{act:pr}) 
                        }
                                            
                    },
                afterEditCell: function (id,name,val,iRow,iCol)
                    {             
                     if(name=='DATE')
                      { jQuery("#"+iRow+"_DATE","#list_stock").datepicker({dateFormat:"yy-mm-dd"});} 
                    }
            });
            jGrid_va.navGrid('#pager_stock',{edit:false,del:false,add:false, search: false, refresh: true});
			
         }
jQuery("#list_stock").ready(loadView);

<!-- end stock grid -->
</script>

<!-- start: prin pdf -->
<script type="text/javascript">
function print_pdf(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_ba").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di Print...");
    }else{
        var answer = confirm ("Anda ingin mencetak Berita Acara Laporan Produksi Harian tanggal : " + data.BA_DATE + " ?" );
        if (answer){
            // open pdf files in new window
            var postdata_id = {};
			//ben ora error
			var postprod = {};
			var postffa = {};
			var postperform = {};
			var postdispatch ={};
			var poststock ={};
			var poststorage={};
    		var dates = formatDate(new Date(getDateFromFormat(data.BA_DATE,'dd/MM/yyyy')),'yyyyMMdd');
			var id_ba = data.ID_BA;
            postdata_id['BA_DATE'] = data.BA_DATE;
            postdata_id['ID_BA'] = data.ID_BA;
            postdata_id['COMPANY_CODE'] = data.COMPANY_CODE;
            postdata_id['CRUD'] =  'PRINT';
			
            var data = {
						  id:postdata_id,
						  prod:postprod,
						  ffa_prod:postffa,
						  dispatch:postdispatch,
						  stock:poststock,
						  storage:poststorage
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_stock_cpo/pdf_daily/'+ dates+'/'+id_ba,
                    data:           {myJson:  data} ,
                    success: function(msg){
						var win=window.open(url+'s_stock_cpo/pdf_daily/'+ dates+'/'+id_ba, '_blank');
  						win.focus();
                    }
            });        
        }
        
    }             
}
function pdf(){
    //var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    //var data = $("#list_ba").getRowData(ids) ;
    //if (ids=="" || ids==null || ids==undefined)
    //{
        //alert("harap pilih data untuk di Print...");
    //}else{
        var answer = confirm ("Anda ingin mencetak pdf Berita Acara Laporan Produksi Harian periode : " + get_periode() + " ?" );
        if (answer){
            // open pdf files in new window
            var postdata_id = {};
			//ben ora error
    		//var dates = formatDate(new Date(getDateFromFormat(data.BA_DATE,'dd/MM/yyyy')),'yyyyMMdd');
			
            var data = {
						  id:postdata_id,
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_stock_cpo/pdf/'+ get_periode(),
                    data:           {myJson:  data} ,
                    success: function(msg){
						var win=window.open(url+'s_stock_cpo/pdf/'+ get_periode(), '_blank');
  						win.focus();
                    }
            });        
        //}
        
    }             
}
function excel(){
	var answer = confirm ("Anda ingin mencetak excel Berita Acara Laporan Produksi Harian periode : " + get_periode() + " ?" );
	if (answer){

	var postdata_id = {};
    var data = {
					id:postdata_id,
               };
        data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_stock_cpo/xls_month/'+ get_periode(),
                    data:           {myJson:  data} ,
                    success: function(msg){
						var win=window.open(url+'s_stock_cpo/xls_month/'+ get_periode(), '_blank');
  						win.focus();
                    }
            });        
        
    }             
}
</script>


<!-- end print pdf -->
<script type="text/javascript">
$(function() {
   
});
	
/*
function addcporow(){
	var ids = jQuery("#list_cpo").getDataIDs();
    var i = ids.length;
    i=i+1;    
    var datArr = {};
    if (i>1){
        var datArr = {ID_BA:jdesc1};
    }
    
    var su=jQuery("#list_cpo").addRowData(i,datArr,'last');
    var act=jQuery("#list_cpo").setRowData(i,{no:i});
        
    jQuery("#list_cpo").setRowData(i,{act:ce});  
}

function addprodrow(){
	var ids = jQuery("#list_production").getDataIDs();
    var i = ids.length;
    i=i+1;    
    var datArr = {};
    if (i>1){
        var datArr = {ID_PRODUCTION:jdesc1};
    }
    
    var su=jQuery("#list_production").addRowData(i,datArr,'last');
    var act=jQuery("#list_production").setRowData(i,{no:i});
        
    jQuery("#list_production").setRowData(i,{act:ce});  
}
*/
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
	//start: for timepicker
	date_obj = new Date();
	date_obj_hours = date_obj.getHours();
	date_obj_mins = date_obj.getMinutes();

	if (date_obj_mins < 10) { date_obj_mins = "0" + date_obj_mins; }
	if (date_obj_hours > 11) {
    	date_obj_hours = date_obj_hours - 12;
    	date_obj_am_pm = " PM";
	} else {
    	date_obj_am_pm = " AM";
	}

	date_obj_time = "'  "+date_obj_hours+":"+date_obj_mins+"'";
	//end: for timepicker
	
	jQuery("#txt_tglba").datepicker({dateFormat:"yy-mm-dd"});	
	$('#txt_hour_from').datepicker({dateFormat: $.datepicker.W3C + date_obj_time});
	$('#txt_hour_to').datepicker({dateFormat: $.datepicker.W3C + date_obj_time});
	
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $('input').val('');

    $("#frm_ba").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 630,
        width: 1060,
        modal: true,
        title: "Berita Acara Harian Produksi",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $('input').val('');
                        $(this).dialog('close')       
                    }
           
        } 
    });
    
    $("#frm_load").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 100,
        width: 200,
        modal: true
    });
    
    $("#search_form").dialog({
        bgiframe: true,
        dialogClass : 'dialog1',
        autoOpen: false,
        width: 530,
        modal: true,
        position: 'center',
        onShow: function(dlg){
            $(dlg.container).css('height','auto')
        },
        close: function(event, ui){
            $(this).dialog('destroy');
            setDialogWindows('#search_form');
        },
        open:function(){
            jQuery("#list_ba").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_stock_cpo/search_data');
        }
    });   
})

function setDialogWindows($element) {
$('#search_form').dialog({
        bgiframe: true,
        dialogClass : 'dialog1',
        autoOpen: false,
        width: 530,
        modal: true,
        position: 'center'
    }); 
}

function get_periode(){
    var lPeriode = $("#tahun").val() + $("#bulan").val();
    return lPeriode;    
}

jQuery(document).ready(function(){
    $('#tahun').change(function() {
        //get_periode();
        reloadGrid();
    });
    
    $('#bulan').change(function() {
        //get_periode();
        reloadGrid(); 
    });
})

$("#txt_tglba").change(resetAutocomplete);
$("#txt_lori_olah").change(countAutoComplete);
$("#txt_lori_restan").change(countAutoComplete);
$("#txt_cbc_from").change(hourAutoComplete);
$("#txt_cbc_to").change(hourAutoComplete);

function countAutoComplete(){
	var averageCageWeight=0;
	var ffb = parseFloat($("#txt_inti").val())+parseFloat($("#txt_plasma").val())+parseFloat($("#txt_supplier").val())+parseFloat($("#txt_group").val())+parseFloat($("#txt_balance_yesterday").val());
	var lori_restan =  isNaN(parseFloat($("#txt_lori_restan").val())) ? 0 : parseFloat($("#txt_lori_restan").val());
	var lori_olah =  isNaN(parseFloat($("#txt_lori_olah").val())) ? 0 : parseFloat($("#txt_lori_olah").val());
	if (lori_restan!=0 || lori_olah!=0){
		var averageCageWeight = (ffb/(lori_restan+lori_olah));		
	}else{
		var averageCageWeight = 0;
	}
	if($("#txt_company").val()=='MAG'){
		var ffb_processed = rounds(Math.floor(averageCageWeight*lori_olah,0));
	}else if($("#txt_company").val()=='LIH'){
		var ffb_processed = rounds_thousand(rounds(Math.floor(averageCageWeight,0))*lori_olah);
	}else{
		var ffb_processed = rounds_thousand(Math.floor(averageCageWeight*lori_olah,0));	
	}
	if($("#txt_company").val()=='LIH'){
		$("#txt_cage_weight").val(rounds(Math.round(averageCageWeight,0)));
	}else{
		$("#txt_cage_weight").val(Math.round(averageCageWeight,0));	
	}
	$("#txt_ffb_processed").val(ffb_processed);
}

function processedAutoComplete(){
	var averageCageWeight=0;
	var ffb_inti = isNaN(parseFloat($("#txt_inti").val())) ? 0 : parseFloat($("#txt_inti").val());
	var ffb_plasma = isNaN(parseFloat($("#txt_plasma").val())) ? 0 : parseFloat($("#txt_plasma").val());
	var ffb_suplier = isNaN(parseFloat($("#txt_supplier").val())) ? 0 : parseFloat($("#txt_supplier").val());
	var ffb_group = isNaN(parseFloat($("#txt_group").val())) ? 0 : parseFloat($("#txt_group").val());
	var ffb_balance_yesterday = isNaN(parseFloat($("#txt_balance_yesterday").val())) ? 0 : parseFloat($("#txt_balance_yesterday").val());

	//var ffb = parseFloat($("#txt_inti").val())+parseFloat($("#txt_plasma").val())+parseFloat($("#txt_supplier").val())+parseFloat($("#txt_group").val())+parseFloat($("#txt_balance_yesterday").val());
	var ffb = (ffb_inti + ffb_plasma + ffb_suplier + ffb_group + ffb_balance_yesterday);
	var lori_restan =  isNaN(parseFloat($("#txt_lori_restan").val())) ? 0 : parseFloat($("#txt_lori_restan").val());
	var lori_olah =  isNaN(parseFloat($("#txt_lori_olah").val())) ? 0 : parseFloat($("#txt_lori_olah").val());
	if (lori_restan!=0 || lori_olah!=0){
		var averageCageWeight = (ffb/(lori_restan+lori_olah));		
	}else{
		var averageCageWeight = 0;
	}
	if($("#txt_company").val()=='MAG'){
		var ffb_processed = rounds(Math.floor(averageCageWeight*lori_olah,0));
	}else if($("#txt_company").val()=='LIH'){
		var ffb_processed = rounds_thousand(rounds(Math.floor(averageCageWeight,0))*lori_olah);
	}else{
		var ffb_processed = rounds_thousand(Math.floor(averageCageWeight*lori_olah,0));	
	}
	if($("#txt_company").val()=='LIH'){
		$("#txt_cage_weight").val(rounds(Math.round(averageCageWeight,0)));
	}else{
		$("#txt_cage_weight").val(Math.round(averageCageWeight,0));	
	}
	$("#txt_ffb_processed").val(ffb_processed);
	
	if(ffb_processed==0){
		jQuery("#list_production").setGridParam({url:url+"s_stock_cpo/LoadNoProductionByDate"+"/"+$("#txt_tglba").val()}).trigger("reloadGrid");	
	}else{
		jQuery("#list_production").setGridParam({url:url+"s_stock_cpo/LoadProductionByDate"+"/"+$("#txt_tglba").val()}).trigger("reloadGrid");	
	}
	
}

function rounds_thousand($round){	
	var str = String($round);
	var digit =str.length - 3
	var right = parseFloat(str.substr(digit)); 

	var x=0;
	var result=0;
	
	if (right>=500){
		x=1000-right;
		result = parseFloat(str) + x;
	}else{
		result = parseFloat(str) - right;
	}
		return result;		
}

function rounds($round){	
	var str = String($round);
	var digit =str.length - 1
	var right = parseFloat(str.substr(digit)); 

	var x=0;
	var result=0;
	
	if (right>=5){
		x=10-right;
		result = parseFloat(str) + x;
	}else{
		result = parseFloat(str) - right;
	}
		return result;		
}

function hourAutoComplete(){
	var processedHour=0;
	var cbc_from =  isNaN(parseFloat($("#txt_cbc_from").val())) ? 0 : parseFloat($("#txt_cbc_from").val());
	var cbc_to =  isNaN(parseFloat($("#txt_cbc_to").val())) ? 0 : parseFloat($("#txt_cbc_to").val());
	
	if (cbc_from!=0 && cbc_to!=0){
		processedHour = cbc_to-cbc_from;
	}else{
		processedHour = 0;
	}
		$("#txt_processed").val(processedHour);
		
		var throughput=0;
		var ffb =  isNaN(parseFloat($("#txt_ffb_processed").val())) ? 0 : parseFloat($("#txt_ffb_processed").val());
		if (processedHour!=0 && ffb!=0){
			throughput = (ffb/(processedHour))/1000;		
		}else{
			throughput = 0;
		}
		$("#txt_throughput").val(throughput);
		
		var mill_utilization=0;
		if (ffb!=0){
			if($("#txt_company").val()=='GKM'){
				var kapasitas = 45;
			}else{
				var kapasitas = 30;
			}
			mill_utilization = (ffb/(20*kapasitas))/10;		
		}else{
			mill_utilization = 0;
		}
		$("#txt_mill").val(mill_utilization);
	
	
}

function resetAutocomplete(){	 
	 
	 jQuery("#list_production").setGridParam({url:url+"s_stock_cpo/LoadProductionByDate"+"/"+$("#txt_tglba").val()}).trigger("reloadGrid");
	 jQuery("#list_dispatch").setGridParam({url:url+"s_stock_cpo/LoadDispatchByDate"+"/"+$("#txt_tglba").val()}).trigger("reloadGrid");
	 jQuery("#list_cpo").setGridParam({url:url+"s_stock_cpo/LoadStorageByDate"+"/"+$("#txt_tglba").val()}).trigger("reloadGrid");
	 jQuery("#list_stock").setGridParam({url:url+"s_stock_cpo/LoadOtherStockByDate"+"/"+$("#txt_tglba").val()}).trigger("reloadGrid");
}
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0                 
    $("#tabs").tabs();
});
  
</script>

<script type="text/javascript">
var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';
     
colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('ID_BA');
colModelT.push({name:'ID_BA',index:'ID_BA', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('TANGGAL');
colModelT.push({name:'BA_DATE',index:'BA_DATE', editable: false, hidden:false, width: 120, align:'left', formatter:'date'});

colNamesT.push('DESCRIPTION');
colModelT.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, hidden:true, width: 140, align:'center'});

colNamesT.push('QC');
colModelT.push({name:'QC',index:'QC', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('MILL_MANAGER');
colModelT.push({name:'MILL_MANAGER',index:'MILL_MANAGER', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('KTU');
colModelT.push({name:'KTU',index:'KTU', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('ADMINISTRATUR');
colModelT.push({name:'ADMINISTRATUR',index:'ADMINISTRATUR', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('LABOR');
colModelT.push({name:'LABOR',index:'LABOR', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('FFB_INTI');
colModelT.push({name:'FFB_INTI',index:'FFB_INTI', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('FFB_PLASMA');
colModelT.push({name:'FFB_PLASMA',index:'FFB_PLASMA', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('FFB_SUPPLIER');
colModelT.push({name:'FFB_SUPPLIER',index:'FFB_SUPPLIER', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('FFB_GROUP');
colModelT.push({name:'FFB_GROUP',index:'FFB_GROUP', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('FFB_PROCESSED');
colModelT.push({name:'FFB_PROCESSED',index:'FFB_PROCESSED', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BALANCE');
colModelT.push({name:'BALANCE',index:'BALANCE', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BALANCE_YESTERDAY');
colModelT.push({name:'BALANCE_YESTERDAY',index:'BALANCE_YESTERDAY', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('LORI_OLAH');
colModelT.push({name:'LORI_OLAH',index:'LORI_OLAH', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('LORI_RESTAN');
colModelT.push({name:'LORI_RESTAN',index:'LORI_RESTAN', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('CAGE_WEIGHT');
colModelT.push({name:'CAGE_WEIGHT',index:'CAGE_WEIGHT', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('PROCESSED_HOUR');
colModelT.push({name:'PROCESSED_HOUR',index:'PROCESSED_HOUR', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('THROUGHPUT');
colModelT.push({name:'THROUGHPUT',index:'THROUGHPUT', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('MILL_UTILIZATION');
colModelT.push({name:'MILL_UTILIZATION',index:'MILL_UTILIZATION', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BUAH_MENTAH');
colModelT.push({name:'BUAH_MENTAH',index:'BUAH_MENTAH', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BUAH_BUSUK');
colModelT.push({name:'BUAH_BUSUK',index:'BUAH_BUSUK', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('JJK');
colModelT.push({name:'JJK',index:'JJK', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('TANGKAI');
colModelT.push({name:'TANGKAI',index:'TANGKAI', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BRONDOLAN');
colModelT.push({name:'BRONDOLAN',index:'BRONDOLAN', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('HOUR_FROM');
colModelT.push({name:'HOUR_FROM',index:'HOUR_FROM', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('HOUR_TO');
colModelT.push({name:'HOUR_TO',index:'HOUR_TO', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('CBC_FROM');
colModelT.push({name:'CBC_FROM',index:'CBC_FROM', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('CBC_TO');
colModelT.push({name:'CBC_TO',index:'CBC_TO', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('STATUS');
colModelT.push({name:'STATUS',index:'STATUS', editable: false, hidden:false, width: 140, align:'center'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

//var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_ba").jqGrid(
            {
                url:url+'s_stock_cpo/LoadData/'+get_periode(),
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_ba"),
                rowNum: 20,
                rownumbers: true,
                height: 350,
                width: 600,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_ba").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
							ap = "<img style='padding-right:6px;' title='Approve/Reopen' src='<?= $template_path ?>themes/base/images/row_edit.gif' width='12px' height='13px' onclick=\"approve_ba('"+cl+"');\" />";
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_ba('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Delete' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"delete_ba('"+cl+"');\"/>";
							pr_adb = "<img title='Print' src='<?= $template_path ?>themes/base/images/adobe-print.png' width='12px' height='13px' onclick=\"print_pdf('"+cl+"');\"/> &nbsp;";                            
                            jQuery("#list_ba").setRowData(ids[i],{act:ap+be+ce+pr_adb}) 
                        }
                                            
                    }
            });
            jGrid_va.navGrid('#pager_ba',{edit:false,del:false,add:false, search: false, refresh: true});
            jGrid_va.navButtonAdd('#pager_ba',{
               caption:"search", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        //jQuery("#list_sounding").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_sounding_kernel/search_data');
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });
         }
jQuery("#list_ba").ready(loadView); 

function crot(cl){
	
	var ids = jQuery("#list_ba").getGridParam('selrow'); 
    var datas = $("#list_ba").getRowData(ids) ;
	$.post('<?= base_url()?>index.php/s_stock_cpo/get_approval/', '', function(data){
            var d = data.split('~');
             alert(d[2]);
			 $("#submitdata").hide();
           
	});
	
}

</script>

<script type="text/javascript">
function reloadGrid(){
	 var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_ba").setGridParam({url:url+'s_stock_cpo/LoadData/'+get_periode()}).trigger("reloadGrid");   
}
</script>

<script type="text/javascript">
/*
var timeoutHnd; 
var flAuto = false; 

function doSearch(ev){ 

// var elem = ev.target||ev.srcElement; 
if(timeoutHnd) 
    clearTimeout(timeoutHnd) 
    timeoutHnd = setTimeout(gridReload,500) 
} 
*/
</script>

<script type="text/javascript">
function input_ba(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    $('input').val('');
    $("#txt_frmMode").val("ADD");
    $("#frm_ba").dialog('open');
	$("#cmd_appNote").hide();
	$("#cmd_newNote").show();
	$("#txt_tglba").attr('disabled',false);
	
	jQuery("#list_production").setGridParam({url:url+"s_stock_cpo/LoadData_Commodities/"}).trigger("reloadGrid"); // clear grid production
	jQuery("#list_cpo").setGridParam({url:url+"s_stock_cpo/LoadData_Storage/"}).trigger("reloadGrid"); // clear grid quality
	jQuery("#list_dispatch").setGridParam({url:url+"s_stock_cpo/LoadData_Commodity/"}).trigger("reloadGrid"); // clear grid dispatch
	//jQuery("#list_stock").setGridParam({url:url+"s_stock_cpo/LoadData_OtherStock/"}).trigger("reloadGrid"); // clear grid stock
	jQuery("#list_stock").setGridParam({url:url+"s_stock_cpo/LoadData_Commodity/"}).trigger("reloadGrid"); // clear grid stock
	
	$.post('<?= base_url()?>index.php/s_stock_cpo/get_approval/', '', function(data){																
    	var d = data.split('~');
		
		$("#txt_qc").val(d[5]);
		$("#txt_manager").val(d[1]);
		$("#txt_ktu").val(d[2]);
		$("#txt_adm").val(d[3]);
		$("#txt_lab").val(d[6]);
		$("#txt_company").val(d[7]);
	});
		
}

function edit_ba(cl){
	
    var ids = cl;
    var data = $("#list_ba").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("Harap pilih data untuk di edit.");
    }else{
        
        $('input').val('');
        var nDate= formatDate(new Date(getDateFromFormat(data.BA_DATE,'dd/MM/yyyy')),'yyyy-MM-dd');
		
		$("#cmd_appNote").hide();
		$("#cmd_newNote").show();
        $("#txt_frmMode").val("EDIT");
		$("#txt_idba").val(data.ID_BA);
		$("#txt_sta").val(data.STATUS);
        $("#txt_tglba").val(nDate);
        $("#txt_qc").val(data.QC);
		$("#txt_manager").val(data.MILL_MANAGER);
		$("#txt_ktu").val(data.KTU);
		$("#txt_adm").val(data.ADMINISTRATUR);
		$("#txt_lab").val(data.LABOR);
		
		$("#txt_crud").val(data.BUAH_MENTAH);
		
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_ba").dialog('open');

		jQuery("#list_production").setGridParam({url:url+"s_stock_cpo/LoadDetail_Production"+"/"+data.ID_BA}).trigger("reloadGrid");
		
		jQuery("#list_dispatch").setGridParam({url:url+"s_stock_cpo/LoadDetail_Dispatch"+"/"+data.ID_BA}).trigger("reloadGrid");
		
		jQuery("#list_stock").setGridParam({url:url+"s_stock_cpo/LoadDetail_Stock"+"/"+data.ID_BA}).trigger("reloadGrid");
		
		jQuery("#list_cpo").setGridParam({url:url+"s_stock_cpo/LoadDetail_StorageStock"+"/"+data.ID_BA}).trigger("reloadGrid");

    } 
}

function approve(){
	var answer = confirm("Approve berita acara?");
    if (answer){
        var postdata_id = {};

        $("#frm_load").dialog('open');
		postdata_id['CRUD'] =  $("#txt_frmMode").val();	
		postdata_id['ID_BA'] =  $("#txt_idba").val();
        postdata_id['BA_DATE'] =  $("#txt_tglba").val();
		postdata_id['STATUS'] =  1;			
				
        var data = {
                      id:postdata_id,
                    };
        data = JSON.stringify(data);
        
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_stock_cpo/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){ 
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                    }else{   
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                        
                        reloadGrid(); 
                        $("#frm_ba").dialog('close');    
                    }    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                    }    
               });
    }	
}

function reopen(){
	var answer = confirm("Reopen berita acara?");
    if (answer){
        var postdata_id = {};
        $("#frm_load").dialog('open');
		postdata_id['CRUD'] =  $("#txt_frmMode").val();	
		postdata_id['ID_BA'] =  $("#txt_idba").val();
        postdata_id['BA_DATE'] =  $("#txt_tglba").val();
		postdata_id['STATUS'] =  0;			
        var data = {
                      id:postdata_id,
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_input_ba/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){ 
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                    }else{   
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                        reloadGrid(); 
                        $("#frm_ba").dialog('close');    
                    }    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                    }    
               });
    }	
}

function approve_ba(cl){
    var ids = cl;
    var data = $("#list_ba").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("Harap pilih data untuk diapprove");
    }else{
        
        $('input').val('');
        var nDate= formatDate(new Date(getDateFromFormat(data.BA_DATE,'dd/MM/yyyy')),'yyyy-MM-dd');
		//var hour_from= formatDate(new Date(getDateFromFormat(data.HOUR_FROM,'dd/MM/yyyy')),'yyyy-MM-dd');
		//var hour_to= formatDate(new Date(getDateFromFormat(data.HOUR_TO,'dd/MM/yyyy')),'yyyy-MM-dd');
		if (data.STATUS == "APPROVED"){
			$("#cmd_opnNote").show();
			$("#cmd_appNote").hide();
			$("#txt_frmMode").val("REOPEN");
		}else{
			$("#cmd_appNote").show();
			$("#cmd_opnNote").hide();
			$("#txt_frmMode").val("APPROVE");
		}
		
		$("#cmd_newNote").hide();
		$("#txt_idba").val(data.ID_BA);
        $("#txt_tglba").val(nDate);
        $("#txt_qc").val(data.QC);
		$("#txt_manager").val(data.MILL_MANAGER);
		$("#txt_ktu").val(data.KTU);
		$("#txt_adm").val(data.ADMINISTRATUR);
		$("#txt_lab").val(data.LABOR);
		$("#txt_inti").val(data.FFB_INTI);
		$("#txt_plasma").val(data.FFB_PLASMA);
		$("#txt_supplier").val(data.FFB_SUPPLIER);
		$("#txt_group").val(data.FFB_GROUP);
		$("#txt_ffb_processed").val(data.FFB_PROCESSED);
		$("#txt_balance_yesterday").val(data.BALANCE_YESTERDAY);
		$("#txt_lori_olah").val(data.LORI_OLAH);
		$("#txt_lori_restan").val(data.LORI_RESTAN);
		$("#txt_cage_weight").val(data.CAGE_WEIGHT);
		$("#txt_processed").val(data.PROCESSED_HOUR);
		$("#txt_throughput").val(data.THROUGHPUT);
		$("#txt_mill").val(data.MILL_UTILIZATION);
		
		$("#txt_crud").val(data.BUAH_MENTAH);
		$("#txt_busuk").val(data.BUAH_BUSUK);
		$("#txt_jjk").val(data.JJK);
		$("#txt_tangkai").val(data.TANGKAI);
		$("#txt_brondolan").val(data.BRONDOLAN);
		$("#txt_hour_from").val(data.HOUR_FROM);
		$("#txt_hour_to").val(data.HOUR_TO);
		$("#txt_cbc_from").val(data.CBC_FROM);
		$("#txt_cbc_to").val(data.CBC_TO);
    	$("#txt_desc").val(data.DESCRIPTION);
		
		$("#txt_tglba").attr('disabled','disabled');
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_ba").dialog('open');
		
		jQuery("#list_production").setGridParam({url:url+"s_stock_cpo/LoadDetail_Production"+"/"+data.ID_BA}).trigger("reloadGrid");
		
		jQuery("#list_dispatch").setGridParam({url:url+"s_stock_cpo/LoadDetail_Dispatch"+"/"+data.ID_BA}).trigger("reloadGrid");
		
		jQuery("#list_stock").setGridParam({url:url+"s_stock_cpo/LoadDetail_Stock"+"/"+data.ID_BA}).trigger("reloadGrid");
		
		jQuery("#list_cpo").setGridParam({url:url+"s_stock_cpo/LoadDetail_StorageStock"+"/"+data.ID_BA}).trigger("reloadGrid");

    } 
}

function delete_ba(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_ba").getRowData(ids) ;
	if (data.STATUS=="APPROVED"){
		alert('Berita acara sudah diapprove, tidak dapat dihapus');
	}else{
		if (ids=="" || ids==null || ids==undefined){
			alert("harap pilih data untuk di hapus...");
		}else{
			var answer = confirm ("Hapus Berita Acara dengan ID : " + data.ID_BA+ " ?" );		
			if (answer){
				$("#frm_load").dialog('open');
				var postdata_id = {};
		
				postdata_id['ID_BA'] = data.ID_BA;
				postdata_id['CRUD'] =  'DEL';
	
				var data = {
							  id:postdata_id
							};
				data = JSON.stringify(data);
				$.ajax({
						type:           'post',
						cache:          false,
						url:            url+'s_stock_cpo/CRUD_METHOD',
						data:           {myJson:  data} ,
						success: function(msg){
							var obj = jQuery.parseJSON(msg);    
							if(obj.error==true){
								alert(obj.status)
								$("#frm_load").dialog('close');
							}else{
								alert(obj.status)
								$("#frm_load").dialog('close');
								reloadGrid();   
							} 
						}
					   });        
			}
		} 
	}
}

function save_ba(){
    
    var answer = confirm("Simpan berita acara ?");
    if (answer){//start answer
		if($("#txt_sta").val()=='APPROVED'){ //start approved
			alert('Berita acara sudah diapprove, tidak dapat diedit lagi');
		}else{
			var postdata_id = {};
			var postprod = {};
			var postperform = {};
			var postdispatch ={};
			var poststock ={};
			var poststorage={};
	
			$("#frm_load").dialog('open');
			postdata_id['CRUD'] =  $("#txt_frmMode").val();	
			postdata_id['ID_BA'] =  $("#txt_idba").val();
			postdata_id['BA_DATE'] =  $("#txt_tglba").val();
			postdata_id['QC'] =  $("#txt_qc").val();	
			postdata_id['MILL_MANAGER'] =  $("#txt_manager").val();		
			postdata_id['KTU'] =  $("#txt_ktu").val();
			postdata_id['ADMINISTRATUR'] =  $("#txt_adm").val();
			postdata_id['LABOR'] =  $("#txt_lab").val();
			postdata_id['DESCRIPTION'] =  $("#txt_desc").val();
			
			//production
			i=0;
			p = $("#list_production").getDataIDs();
	
			$.each(p, function(n, rowid) 
			{ 
				var production = $("#list_production").getRowData(rowid) ; 
				i=i+1;
				postprod['PRODUCTION_DATE'+i] = $("#txt_tglba").val();
				postprod['ID_COMMODITY'+i] = production.ID_COMMODITY;
				postprod['WEIGHT'+i] = production.WEIGHT;
				postprod['FFA'+i] = production.FFA;
				postprod['MOISTURE'+i] = production.MOISTURE;
				postprod['DIRT'+i] = production.DIRT;
				postprod['COMPANY_CODE'+i] = production.COMPANY_CODE;
			});
			
			l=0;
			q=$("#list_cpo").getDataIDs();
	
			$.each(q, function(n, rowid) 
			{ 
				var storage = $("#list_cpo").getRowData(rowid) ; 
				l=l+1;
				poststorage['STRG_STOCK_DATE'+l] = $("#txt_tglba").val();
				poststorage['ID_STORAGE'+l] = storage.ID_STORAGE;
				poststorage['WEIGHT'+l] = storage.WEIGHT;
				poststorage['FFA'+l] = storage.FFA;
				poststorage['MOISTURE'+l] = storage.MOISTURE;
				poststorage['DIRT'+l] = storage.DIRT;
				poststorage['COMPANY_CODE'+l] = storage.COMPANY_CODE;
			});
			
			j=0;
			d=$("#list_dispatch").getDataIDs();
	
			$.each(d, function(n, rowid) 
			{ 
				var dispatch = $("#list_dispatch").getRowData(rowid) ; 
				j=j+1;
				postdispatch['DISPATCH_DATE'+j] = $("#txt_tglba").val();
				postdispatch['ID_COMMODITY'+j] = dispatch.ID_COMMODITY;
				postdispatch['WEIGHT'+j] = dispatch.WEIGHT;
				postdispatch['FFA'+j] = dispatch.FFA;
				postdispatch['MOISTURE'+j] = dispatch.MOISTURE;
				postdispatch['DIRT'+j] = dispatch.DIRT;
				postdispatch['COMPANY_CODE'+j] = dispatch.COMPANY_CODE;			
			});
			//stock
			k=0;
			s=$("#list_stock").getDataIDs();
	
			$.each(s, function(n, rowid) 
			{ 
				var stock = $("#list_stock").getRowData(rowid) ; 
				k=k+1;
				poststock['STOCK_DATE'+k] = $("#txt_tglba").val();
				poststock['ID_COMMODITY'+k] = stock.ID_COMMODITY;
				poststock['WEIGHT'+k] = stock.WEIGHT;
				poststock['FFA'+k] = stock.FFA;
				poststock['MOISTURE'+k] = stock.MOISTURE;
				poststock['DIRT'+k] = stock.DIRT;
				poststock['COMPANY_CODE'+k] = stock.COMPANY_CODE;
			});
								
			var data = {
						  id:postdata_id,
						  prod:postprod,
						  dispatch:postdispatch,
						  stock:poststock,
						  storage:poststorage
						};
			data = JSON.stringify(data);
			
			$.ajax({
					type:           'post',
					cache:          false,
					url:            url+'s_stock_cpo/CRUD_METHOD',
					data:           {myJson:  data} ,
					success: function(msg){
						var obj = jQuery.parseJSON(msg);    
						if(obj.error===true){ 
							alert(obj.status)
							$("#frm_load").dialog('close');
						}else{   
							alert(obj.status)
							$("#frm_load").dialog('close');
							
							reloadGrid(); 
							$("#frm_ba").dialog('close');    
						}    
					},
					error:function (xhr, ajaxOptions, thrownError){
							alert(xhr.status);
							alert(thrownError);
						}    
				   });
		}//end Approved
    }//end answer
}

var MONTH_NAMES=new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
var DAY_NAMES=new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat');
function LZ(x) {return(x<0||x>9?"":"0")+x}

// ------------------------------------------------------------------
// formatDate (date_object, format)
// Returns a date in the output format specified.
// The format string uses the same abbreviations as in getDateFromFormat()
// ------------------------------------------------------------------
function formatDate(date,format) {
    format=format+"";
    var result="";
    var i_format=0;
    var c="";
    var token="";
    var y=date.getYear()+"";
    var M=date.getMonth()+1;
    var d=date.getDate();
    var E=date.getDay();
    var H=date.getHours();
    var m=date.getMinutes();
    var s=date.getSeconds();
    var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;
    // Convert real date parts into formatted versions
    var value=new Object();
    if (y.length < 4) {y=""+(y-0+1900);}
    value["y"]=""+y;
    value["yyyy"]=y;
    value["yy"]=y.substring(2,4);
    value["M"]=M;
    value["MM"]=LZ(M);
    value["MMM"]=MONTH_NAMES[M-1];
    value["NNN"]=MONTH_NAMES[M+11];
    value["d"]=d;
    value["dd"]=LZ(d);
    value["E"]=DAY_NAMES[E+7];
    value["EE"]=DAY_NAMES[E];
    value["H"]=H;
    value["HH"]=LZ(H);
    if (H==0){value["h"]=12;}
    else if (H>12){value["h"]=H-12;}
    else {value["h"]=H;}
    value["hh"]=LZ(value["h"]);
    if (H>11){value["K"]=H-12;} else {value["K"]=H;}
    value["k"]=H+1;
    value["KK"]=LZ(value["K"]);
    value["kk"]=LZ(value["k"]);
    if (H > 11) { value["a"]="PM"; }
    else { value["a"]="AM"; }
    value["m"]=m;
    value["mm"]=LZ(m);
    value["s"]=s;
    value["ss"]=LZ(s);
    while (i_format < format.length) {
        c=format.charAt(i_format);
        token="";
        while ((format.charAt(i_format)==c) && (i_format < format.length)) {
            token += format.charAt(i_format++);
            }
        if (value[token] != null) { result=result + value[token]; }
        else { result=result + token; }
        }
    return result;
    }
    
// ------------------------------------------------------------------
// Utility functions for parsing in getDateFromFormat()
// ------------------------------------------------------------------
function _isInteger(val) {
    var digits="1234567890";
    for (var i=0; i < val.length; i++) {
        if (digits.indexOf(val.charAt(i))==-1) { return false; }
        }
    return true;
    }
function _getInt(str,i,minlength,maxlength) {
    for (var x=maxlength; x>=minlength; x--) {
        var token=str.substring(i,i+x);
        if (token.length < minlength) { return null; }
        if (_isInteger(token)) { return token; }
        }
    return null;
    }
    
// ------------------------------------------------------------------
// getDateFromFormat( date_string , format_string )
//
// This function takes a date string and a format string. It matches
// If the date string matches the format string, it returns the 
// getTime() of the date. If it does not match, it returns 0.
// ------------------------------------------------------------------
function getDateFromFormat(val,format) {
    val=val+"";
    format=format+"";
    var i_val=0;
    var i_format=0;
    var c="";
    var token="";
    var token2="";
    var x,y;
    var now=new Date();
    var year=now.getYear();
    var month=now.getMonth()+1;
    var date=1;
    var hh=now.getHours();
    var mm=now.getMinutes();
    var ss=now.getSeconds();
    var ampm="";
    
    while (i_format < format.length) {
        // Get next token from format string
        c=format.charAt(i_format);
        token="";
        while ((format.charAt(i_format)==c) && (i_format < format.length)) {
            token += format.charAt(i_format++);
            }
        // Extract contents of value based on format token
        if (token=="yyyy" || token=="yy" || token=="y") {
            if (token=="yyyy") { x=4;y=4; }
            if (token=="yy")   { x=2;y=2; }
            if (token=="y")    { x=2;y=4; }
            year=_getInt(val,i_val,x,y);
            if (year==null) { return 0; }
            i_val += year.length;
            if (year.length==2) {
                if (year > 70) { year=1900+(year-0); }
                else { year=2000+(year-0); }
                }
            }
        else if (token=="MMM"||token=="NNN"){
            month=0;
            for (var i=0; i<MONTH_NAMES.length; i++) {
                var month_name=MONTH_NAMES[i];
                if (val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()) {
                    if (token=="MMM"||(token=="NNN"&&i>11)) {
                        month=i+1;
                        if (month>12) { month -= 12; }
                        i_val += month_name.length;
                        break;
                        }
                    }
                }
            if ((month < 1)||(month>12)){return 0;}
            }
        else if (token=="EE"||token=="E"){
            for (var i=0; i<DAY_NAMES.length; i++) {
                var day_name=DAY_NAMES[i];
                if (val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()) {
                    i_val += day_name.length;
                    break;
                    }
                }
            }
        else if (token=="MM"||token=="M") {
            month=_getInt(val,i_val,token.length,2);
            if(month==null||(month<1)||(month>12)){return 0;}
            i_val+=month.length;}
        else if (token=="dd"||token=="d") {
            date=_getInt(val,i_val,token.length,2);
            if(date==null||(date<1)||(date>31)){return 0;}
            i_val+=date.length;}
        else if (token=="hh"||token=="h") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<1)||(hh>12)){return 0;}
            i_val+=hh.length;}
        else if (token=="HH"||token=="H") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<0)||(hh>23)){return 0;}
            i_val+=hh.length;}
        else if (token=="KK"||token=="K") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<0)||(hh>11)){return 0;}
            i_val+=hh.length;}
        else if (token=="kk"||token=="k") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<1)||(hh>24)){return 0;}
            i_val+=hh.length;hh--;}
        else if (token=="mm"||token=="m") {
            mm=_getInt(val,i_val,token.length,2);
            if(mm==null||(mm<0)||(mm>59)){return 0;}
            i_val+=mm.length;}
        else if (token=="ss"||token=="s") {
            ss=_getInt(val,i_val,token.length,2);
            if(ss==null||(ss<0)||(ss>59)){return 0;}
            i_val+=ss.length;}
        else if (token=="a") {
            if (val.substring(i_val,i_val+2).toLowerCase()=="am") {ampm="AM";}
            else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") {ampm="PM";}
            else {return 0;}
            i_val+=2;}
        else {
            if (val.substring(i_val,i_val+token.length)!=token) {return 0;}
            else {i_val+=token.length;}
            }
        }
    // If there are any trailing characters left in the value, it doesn't match
    if (i_val != val.length) { return 0; }
    // Is date valid for month?
    if (month==2) {
        // Check for leap year
        if ( ( (year%4==0)&&(year%100 != 0) ) || (year%400==0) ) { // leap year
            if (date > 29){ return 0; }
            }
        else { if (date > 28) { return 0; } }
        }
    if ((month==4)||(month==6)||(month==9)||(month==11)) {
        if (date > 30) { return 0; }
        }
    // Correct hours value
    if (hh<12 && ampm=="PM") { hh=hh-0+12; }
    else if (hh>11 && ampm=="AM") { hh-=12; }
    var newdate=new Date(year,month-1,date,hh,mm,ss);
    return newdate.getTime();
    }

</script>


