/*
 * jQuery JSON Plugin
 * version: 2.1 (2009-08-14)
 *
 * This document is licensed as free software under the terms of the
 * MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 * Brantley Harris wrote this plugin. It is based somewhat on the JSON.org 
 * website's http://www.json.org/json2.js, which proclaims:
 * "NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.", a sentiment that
 * I uphold.
 *
 * It is also influenced heavily by MochiKit's serializeJSON, which is 
 * copyrighted 2005 by Bob Ippolito.
 */
 
(function($) {
    /** jQuery.toJSON( json-serializble )
        Converts the given argument into a JSON respresentation.

        If an object has a "toJSON" function, that will be used to get the representation.
        Non-integer/string keys are skipped in the object, as are keys that point to a function.

        json-serializble:
            The *thing* to be converted.
     **/
    $.toJSON = function(o)
    {
        if (typeof(JSON) == 'object' && JSON.stringify)
            return JSON.stringify(o);
        
        var type = typeof(o);
    
        if (o === null)
            return "null";
    
        if (type == "undefined")
            return undefined;
        
        if (type == "number" || type == "boolean")
            return o + "";
    
        if (type == "string")
            return $.quoteString(o);
    
        if (type == 'object')
        {
            if (typeof o.toJSON == "function") 
                return $.toJSON( o.toJSON() );
            
            if (o.constructor === Date)
            {
                var month = o.getUTCMonth() + 1;
                if (month < 10) month = '0' + month;

                var day = o.getUTCDate();
                if (day < 10) day = '0' + day;

                var year = o.getUTCFullYear();
                
                var hours = o.getUTCHours();
                if (hours < 10) hours = '0' + hours;
                
                var minutes = o.getUTCMinutes();
                if (minutes < 10) minutes = '0' + minutes;
                
                var seconds = o.getUTCSeconds();
                if (seconds < 10) seconds = '0' + seconds;
                
                var milli = o.getUTCMilliseconds();
                if (milli < 100) milli = '0' + milli;
                if (milli < 10) milli = '0' + milli;

                return '"' + year + '-' + month + '-' + day + 'T' +
                             hours + ':' + minutes + ':' + seconds + 
                             '.' + milli + 'Z"'; 
            }

            if (o.constructor === Array) 
            {
                var ret = [];
                for (var i = 0; i < o.length; i++)
                    ret.push( $.toJSON(o[i]) || "null" );

                return "[" + ret.join(",") + "]";
            }
        
            var pairs = [];
            for (var k in o) {
                var name;
                var type = typeof k;

                if (type == "number")
                    name = '"' + k + '"';
                else if (type == "string")
                    name = $.quoteString(k);
                else
                    continue;  //skip non-string or number keys
            
                if (typeof o[k] == "function") 
                    continue;  //skip pairs where the value is a function.
            
                var val = $.toJSON(o[k]);
            
                pairs.push(name + ":" + val);
            }

            return "{" + pairs.join(", ") + "}";
        }
    };

    /** jQuery.evalJSON(src)
        Evaluates a given piece of json source.
     **/
    $.evalJSON = function(src)
    {
        if (typeof(JSON) == 'object' && JSON.parse)
            return JSON.parse(src);
        return eval("(" + src + ")");
    };
    
    /** jQuery.secureEvalJSON(src)
        Evals JSON in a way that is *more* secure.
    **/
    $.secureEvalJSON = function(src)
    {
        if (typeof(JSON) == 'object' && JSON.parse)
            return JSON.parse(src);
        
        var filtered = src;
        filtered = filtered.replace(/\\["\\\/bfnrtu]/g, '@');
        filtered = filtered.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
        filtered = filtered.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
        
        if (/^[\],:{}\s]*$/.test(filtered))
            return eval("(" + src + ")");
        else
            throw new SyntaxError("Error parsing JSON, source is not valid.");
    };

    /** jQuery.quoteString(string)
        Returns a string-repr of a string, escaping quotes intelligently.  
        Mostly a support function for toJSON.
    
        Examples:
            >>> jQuery.quoteString("apple")
            "apple"
        
            >>> jQuery.quoteString('"Where are we going?", she asked.')
            "\"Where are we going?\", she asked."
     **/
    $.quoteString = function(string)
    {
        if (string.match(_escapeable))
        {
            return '"' + string.replace(_escapeable, function (a) 
            {
                var c = _meta[a];
                if (typeof c === 'string') return c;
                c = a.charCodeAt();
                return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
            }) + '"';
        }
        return '"' + string + '"';
    };
    
    var _escapeable = /["\\\x00-\x1f\x7f-\x9f]/g;
    
    var _meta = {
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"' : '\\"',
        '\\': '\\\\'
    };
})(jQuery);


/*
 * jqGrid Smart Search Panel  1.0.17
 * Copyright (c) 2010, Igor Telmenko, izumeroot@gmail.com
 * Dual licensed under the MIT and GPL licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 * Date: 2010-12-14
 */

$.jgrid.extend({
    smartSearchPanel : function (place, options, xurl) {
        // Inisialisasi. Mendapatkan pengaturan grid
        function filterPanel(jQ, grid, place) {
            this.$ = jQ;
            this.columnsModel = grid.getGridParam('colModel');
            this.columnsNames = grid.getGridParam('colNames');
            for(var j=0; j < this.columnsNames.length; j++) {
                this.columnsModel[j].label = this.columnsNames[j];
            }
            this.place = $(place);
            this.grid = grid;
            this.initOptions = function() {
                this.options = {};
                //console.log(jQuery.fn.searchFilter.defaults, $.jgrid.search )
                this.options.groupOps = (typeof($.jgrid.search)!='undefined') ? $.jgrid.search.groupOps : $.fn.searchFilter.defaults;
                this.options.findText = (typeof($.jgrid.search)!='undefined') ? $.jgrid.search.Find : 'Find';
                this.options.resetText = (typeof($.jgrid.search)!='undefined') ? $.jgrid.search.Reset : 'Reset';
                this.options.matchText = (typeof($.jgrid.search)!='undefined') ? $.jgrid.search.matchText :  $.fn.searchFilter.matchText;
                this.options.rulesText = (typeof($.jgrid.search)!='undefined') ? $.jgrid.search.rulesText :  $.fn.searchFilter.rulesText;
                this.operators =  {eq: '=', ne: '&#8800;', lt: '&#60;', le: '&#8804;', gt: '&#62;', ge: '&#8805;'};
                if(typeof($.jgrid.search)!='undefined') {
                    //var opsDefaults = ['eq', 'ne', 'lt', 'le', 'gt', 'ge', 'in', 'ni', 'bw', 'bn', 'ew', 'en', 'cn', 'nc'];
                    //var opsLang = ['eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'in', 'ni', 'ew', 'en', 'cn', 'nc'];
                    var tmpOps = $.jgrid.search.odata;
                    this.operators.bw = tmpOps[6];
                    this.operators.ew =  tmpOps[10];
                    this.operators.cn = tmpOps[12];    
                } else {
                    var tmpOps = $.fn.searchFilter.defaults.operators;
                    this.operators.bw = tmpOps[8].text;
                    this.operators.ew =  tmpOps[10].text;
                    this.operators.cn = tmpOps[12].text;
                }
            }
            var dialogOptions = this.dialogOptions = { title: 'Search Dialog', height: 'auto', modal: true, position: 'center' };
            if(typeof(options)!='undefined') {
                if(typeof(options.dialog)!='undefined') {
                    this.dialogOptions = $.extend(dialogOptions, options.dialog);
                }
            }
            this.dialogOptions.autoOpen = false;
            this.dialogOptions.dialogClass = 'ui-jqgrid';

            this.getOperatorSettings = function(arr) {
                var result = {};
                for(var j=0; j < arr.length;j++){
                    var key = arr[j];
                    result[key] = this.operators[key];
                }
                return result;
            }

            // Metode untuk menampilkan panel kontrol
            this.showPanel = function() {
                this.initOptions();
                var groupOps = this.options.groupOps;
                this.place.append("<table class='smart-search-table ui-jqgrid ui-widget ui-widget-content ui-corner-all'>"+
                    "<thead><tr><td colspan='3' align='left'><select class='smart-search-fields'></select></td>"+
                    "<td><div class='ui-add-last ui-state-default ui-corner-all'><span class='ui-icon ui-icon-plusthick smart-search-add-btn'></span></div></td>"+
                    "<tbody></tbody><tfoot>"+
                    "<tr><td colspan='4'>"+
                    "<span style='display: inline-block; float: left;' class='ui-reset ui-state-default ui-corner-all smart-search-reset-btn'><span style='float: left;' class='ui-icon ui-icon-arrowreturnthick-1-w'></span><span style='line-height: 18px; padding: 0pt 7px 0pt 3px;'>"+this.options.resetText+"</span></span>"+
                    "<span style='display: inline-block; float: right;' class='ui-search ui-state-default ui-corner-all smart-search-find-btn'>"+
                        "<span style='float: left;' class='ui-icon ui-icon-search'></span><span style='line-height: 18px; padding: 0pt 7px 0pt 3px;'>"+ this.options.findText +"</span>"+
                    "</span>"+
                    "&nbsp;<span class='matchText'>" + this.options.matchText + "</span> "+
                    "<select class='smart-search-variant'>"+
                    "<option value='"+groupOps[0].op+"'>"+ groupOps[0].text +"</option>"+
                    "<option value='"+groupOps[1].op+"'>"+groupOps[1].text +"</option>"+
                    "</select>"+
                    "<span class='rulesText'>" + this.options.rulesText + "</span>&nbsp;"+
                    "</td>"+
                    "</tfoot>");
                this.footer = this.place.find('tfoot');
                this.body = this.place.find('tbody');
                this.fieldsSelector = this.place.find('.smart-search-fields');
                for(var j=0; j < this.columnsModel.length; j++) {
                    var tmpColumn = this.columnsModel[j];
                    if(typeof(tmpColumn.search)=='undefined' || tmpColumn.search) {
                        this.fieldsSelector.append("<option value='" + j + "' >" + tmpColumn.label + "</option>");
                    }
                }
                this.addConditionBtn = this.place.find('.smart-search-add-btn');
                this.addConditionBtn.bind('click', {parent: this}, function(e) {e.data.parent.addCondition()});
                this.findBtn = this.place.find('.smart-search-find-btn');
                this.findBtn.bind('click', {parent: this}, function(e) {e.data.parent.runFilter()});
                this.resetBtn = this.place.find('.smart-search-reset-btn');
                this.resetBtn.bind('click', {parent: this}, function(e) {e.data.parent.resetFilter()});
                this.groupOpSelector = this.place.find('.smart-search-variant');
                
            }
            
            // Tambahkan berturut-turut dengan kondisi di panel
            this.addCondition = function() {
                var tmpColumn = this.columnsModel[this.fieldsSelector.val()];
                //console.log(tmpColumn);
                var editType = (typeof(tmpColumn.edittype)!='undefined') ? tmpColumn.edittype : 'text';
                if(typeof(tmpColumn.formatter)!='undefined' && tmpColumn.formatter=='date') {
                    editType = 'date';
                }
                var operators =  this.getOperatorSettings(['eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'ew', 'cn']);
                switch (editType) {
                    case 'select':
                        var operators =  this.getOperatorSettings(['eq', 'ne']);
                        var editOptions = tmpColumn.editoptions.value;
                        var valOptions = editOptions.split(';');
                        var controlHtml = "<select class='smart-search-value'>";
                        for(var i=0; i < valOptions.length; i++) {
                            var tmpOpt = valOptions[i].split(':');
                            controlHtml += "<option value='"+tmpOpt[0]+"'>"+tmpOpt[1]+"</option>";
                        }
                        controlHtml += "</select>";
                    break;
                    case 'date':
                        var operators =  this.getOperatorSettings(['eq', 'ne', 'lt', 'le', 'gt', 'ge']);
                        var controlHtml = "<input type='text' value='' class='smart-search-value' />";
                    break;
                    case 'checkbox':
                        var operators =  this.getOperatorSettings(['eq', 'ne']);
                        var controlHtml = "<select class='smart-search-value'>";
                        controlHtml += "<option value='1'>TRUE</option>";
                        controlHtml += "<option value='0'>FALSE</option>";
                        controlHtml += "</select>";
                    break;
                    case 'text':
                    default:
                        var controlHtml = "<input type='text' value='' class='smart-search-value' />";
                }
                conditions = "<select class='smart-search-oper'>";
                for(var propName in operators) {
                    conditions += "<option value='"+propName+"'>"+operators[propName]+"</option>";
                }
                conditions += "</select>";
                var fieldName = (typeof(tmpColumn.field)!='undefined') ? tmpColumn.field : tmpColumn.index;
                this.body.append("<tr>"+
                        "<td class='smart-search-label'>"+tmpColumn.label+
                        "<input type='hidden' value='"+fieldName+"' >"+
                        "</td><td>"+conditions+"</td>"+
                        "<td>"+controlHtml+"</td><td>"+
                        "<div class='ui-del ui-state-default ui-corner-all'><span class='ui-icon ui-icon-minus smart-search-minus-btn'></span></div>"+
                        "</td></tr>");
                if(editType == 'date') {
                    this.body.find('tr:last input').datepicker({dateFormat:"yy-mm-dd"});
                    $('#ui-datepicker-div').css('z-index', this.dlgZIndex + 1).addClass('ui-jqgrid');
                }
                this.body.find('tr:last .ui-icon-minus').click(function(){$(this).parent().parent().parent().remove();});
            }

            // Reset Filter
            this.resetFilter = function() {
                $('.smart-search-label').parent().remove();
                this.grid[0].p.search = false;
                $.extend(this.grid[0].p.postData, {filters: []});
                this.runFilter();
            }
            
            // Terapkan filter ke grid
            this.runFilter = function() {
                var rows  = this.body.find('tr');
                if(rows.length<1) {
                    //this.grid.trigger("reloadGrid",[{page:1}]);
                    this.grid.setGridParam({url:url+xurl}).trigger("reloadGrid");
                    return;
                }
                var rules = [];
                for(var j=0; j < rows.length; j++) {
                    var row = $(rows[j]);
                    var tmpRule = {
                        // Set JSON rule
                        field: row.find('td:first input').val(),
                        op: row.find('.smart-search-oper').val(),
                        data: row.find('.smart-search-value').val()
                    };
                    rules.push(tmpRule);
                }
                var result = {};
                result.groupOp = this.groupOpSelector.val();
                result.rules = rules;
                // Prepare data untuk POST dan redraw grid
                this.grid[0].p.search = true;
                $.extend(this.grid[0].p.postData, {filters: $.toJSON(result)});
                this.grid.setGridParam({url:url+xurl}).trigger("reloadGrid"); 
                //this.grid.trigger("reloadGrid",[{page:1}]);
            }

            this.showDialog = function() {
                if(typeof(this.dlg)=='undefined') {
                    this.dlg = $(place).dialog(this.dialogOptions);
                    var parent = this;
                    $(place).bind( "dialogbeforeclose", function(event, ui) {
                        parent.body.find('input.hasDatepicker').datepicker( "hide" );
                    });
                }
                this.dlg.dialog('open');
                this.dlg.css('min-height', '9px');
                this.dlgZIndex = parseInt(this.dlg.parent().css('z-index'));
            }

            this.hideDialog = function() {
                this.body.find('input.hasDatepicker').datepicker( "hide" );
                this.dlg.dialog('close');
            }
        
        }
        var panelObject = new filterPanel(jQuery, this, place);
        panelObject.showPanel();
        return panelObject;
        
    }
    
}); 

