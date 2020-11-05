/* jqmodal */
/*
 * jqModal - Minimalist Modaling with jQuery
 *   (http://dev.iceburg.net/jquery/jqmodal/)
 *
 * Copyright (c) 2007,2008 Brice Burgess <bhb@iceburg.net>
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 * 
 * $Version: 07/06/2008 +r13
 */
(function($) {
$.fn.jqm=function(o){
var p={
overlay: 50,
closeoverlay : true,
overlayClass: 'jqmOverlay',
closeClass: 'jqmClose',
trigger: '.jqModal',
ajax: F,
ajaxText: '',
target: F,
modal: F,
toTop: F,
onShow: F,
onHide: F,
onLoad: F
};
return this.each(function(){if(this._jqm)return H[this._jqm].c=$.extend({},H[this._jqm].c,o);s++;this._jqm=s;
H[s]={c:$.extend(p,$.jqm.params,o),a:F,w:$(this).addClass('jqmID'+s),s:s};
if(p.trigger)$(this).jqmAddTrigger(p.trigger);
});};

$.fn.jqmAddClose=function(e){return hs(this,e,'jqmHide');};
$.fn.jqmAddTrigger=function(e){return hs(this,e,'jqmShow');};
$.fn.jqmShow=function(t){return this.each(function(){$.jqm.open(this._jqm,t);});};
$.fn.jqmHide=function(t){return this.each(function(){$.jqm.close(this._jqm,t)});};

$.jqm = {
hash:{},
open:function(s,t){var h=H[s],c=h.c,cc='.'+c.closeClass,z=(parseInt(h.w.css('z-index')));z=(z>0)?z:3000;var o=$('<div></div>').css({height:'100%',width:'100%',position:'fixed',left:0,top:0,'z-index':z-1,opacity:c.overlay/100});if(h.a)return F;h.t=t;h.a=true;h.w.css('z-index',z);
 if(c.modal) {if(!A[0])setTimeout(function(){L('bind');},1);A.push(s);}
 else if(c.overlay > 0) {if(c.closeoverlay) h.w.jqmAddClose(o);}
 else o=F;

 h.o=(o)?o.addClass(c.overlayClass).prependTo('body'):F;
 if(ie6){$('html,body').css({height:'100%',width:'100%'});if(o){o=o.css({position:'absolute'})[0];for(var y in {Top:1,Left:1})o.style.setExpression(y.toLowerCase(),"(_=(document.documentElement.scroll"+y+" || document.body.scroll"+y+"))+'px'");}}

 if(c.ajax) {var r=c.target||h.w,u=c.ajax;r=(typeof r == 'string')?$(r,h.w):$(r);u=(u.substr(0,1) == '@')?$(t).attr(u.substring(1)):u;
  r.html(c.ajaxText).load(u,function(){if(c.onLoad)c.onLoad.call(this,h);if(cc)h.w.jqmAddClose($(cc,h.w));e(h);});}
 else if(cc)h.w.jqmAddClose($(cc,h.w));

 if(c.toTop&&h.o)h.w.before('<span id="jqmP'+h.w[0]._jqm+'"></span>').insertAfter(h.o);	
 (c.onShow)?c.onShow(h):h.w.show();e(h);return F;
},
close:function(s){var h=H[s];if(!h.a)return F;h.a=F;
 if(A[0]){A.pop();if(!A[0])L('unbind');}
 if(h.c.toTop&&h.o)$('#jqmP'+h.w[0]._jqm).after(h.w).remove();
 if(h.c.onHide)h.c.onHide(h);else{h.w.hide();if(h.o)h.o.remove();} return F;
},
params:{}};
var s=0,H=$.jqm.hash,A=[],ie6=$.browser.msie&&($.browser.version == "6.0"),F=false,
e=function(h){var i=$('<iframe src="javascript:false;document.write(\'\');" class="jqm"></iframe>').css({opacity:0});if(ie6)if(h.o)h.o.html('<p style="width:100%;height:100%"/>').prepend(i);else if(!$('iframe.jqm',h.w)[0])h.w.prepend(i); f(h);},
f=function(h){try{$(':input:visible',h.w)[0].focus();}catch(_){}},
L=function(t){$()[t]("keypress",m)[t]("keydown",m)[t]("mousedown",m);},
m=function(e){var h=H[A[A.length-1]],r=(!$(e.target).parents('.jqmID'+h.s)[0]);if(r)f(h);return !r;},
hs=function(w,t,c){return w.each(function(){var s=this._jqm;$(t).each(function() {
 if(!this[c]){this[c]=[];$(this).click(function(){for(var i in {jqmShow:1,jqmHide:1})for(var s in this[i])if(H[this[i][s]])H[this[i][s]].w[i](this);return F;});}this[c].push(s);});});};
})(jQuery);

/*  JQDNR */

/*
 * jqDnR - Minimalistic Drag'n'Resize for jQuery.
 *
 * Copyright (c) 2007 Brice Burgess <bhb@iceburg.net>, http://www.iceburg.net
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: 2007.08.19 +r2
 */

(function($){
$.fn.jqDrag=function(h){return i(this,h,'d');};
$.fn.jqResize=function(h,ar){return i(this,h,'r',ar);};
$.jqDnR={
	dnr:{},
	e:0,
	drag:function(v){
 		if(M.k == 'd')E.css({left:M.X+v.pageX-M.pX,top:M.Y+v.pageY-M.pY});
 		else {
			E.css({width:Math.max(v.pageX-M.pX+M.W,0),height:Math.max(v.pageY-M.pY+M.H,0)});
			if(M1){E1.css({width:Math.max(v.pageX-M1.pX+M1.W,0),height:Math.max(v.pageY-M1.pY+M1.H,0)});}
		}
  		return false;
  	},
	stop:function(){
		//E.css('opacity',M.o);
		$().unbind('mousemove',J.drag).unbind('mouseup',J.stop);
	}
};
var J=$.jqDnR,M=J.dnr,E=J.e,E1,
i=function(e,h,k,aR){
	return e.each(function(){
		h=(h)?$(h,e):e;
 		h.bind('mousedown',{e:e,k:k},function(v){
 			var d=v.data,p={};E=d.e;E1 = aR ? $(aR) : false;
 			// attempt utilization of dimensions plugin to fix IE issues
 			if(E.css('position') != 'relative'){try{E.position(p);}catch(e){}}
 			M={
 				X:p.left||f('left')||0,
 				Y:p.top||f('top')||0,
 				W:f('width')||E[0].scrollWidth||0,
 				H:f('height')||E[0].scrollHeight||0,
 				pX:v.pageX,
 				pY:v.pageY,
 				k:d.k
 				//o:E.css('opacity')
 			};
			// also resize
			if(E1 && d.k != 'd'){
 				M1={
					X:p.left||f1('left')||0,
					Y:p.top||f1('top')||0,
					W:E1[0].offsetWidth||f1('width')||0,
					H:E1[0].offsetHeight||f1('height')||0,
					pX:v.pageX,
					pY:v.pageY,
					k:d.k
				};
			} else {M1 = false;}			
 			//E.css({opacity:0.8});
 			$().mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
 			return false;
 		});
	});
},
f=function(k){return parseInt(E.css(k))||false;};
f1=function(k){	return parseInt(E1.css(k))||false;};
})(jQuery);

//jqform

(function(b){b.fn.ajaxSubmit=function(p){if(!this.length){a("ajaxSubmit: skipping submit process - no element selected");return this}if(typeof p=="function"){p={success:p}}p=b.extend({url:this.attr("action")||window.location.toString(),type:this.attr("method")||"GET"},p||{});var s={};this.trigger("form-pre-serialize",[this,p,s]);if(s.veto){a("ajaxSubmit: submit vetoed via form-pre-serialize trigger");return this}if(p.beforeSerialize&&p.beforeSerialize(this,p)===false){a("ajaxSubmit: submit aborted via beforeSerialize callback");return this}var i=this.formToArray(p.semantic);if(p.data){p.extraData=p.data;for(var e in p.data){if(p.data[e] instanceof Array){for(var f in p.data[e]){i.push({name:e,value:p.data[e][f]})}}else{i.push({name:e,value:p.data[e]})}}}if(p.beforeSubmit&&p.beforeSubmit(i,this,p)===false){a("ajaxSubmit: submit aborted via beforeSubmit callback");return this}this.trigger("form-submit-validate",[i,this,p,s]);if(s.veto){a("ajaxSubmit: submit vetoed via form-submit-validate trigger");return this}var d=b.param(i);if(p.type.toUpperCase()=="GET"){p.url+=(p.url.indexOf("?")>=0?"&":"?")+d;p.data=null}else{p.data=d}var r=this,h=[];if(p.resetForm){h.push(function(){r.resetForm()})}if(p.clearForm){h.push(function(){r.clearForm()})}if(!p.dataType&&p.target){var m=p.success||function(){};h.push(function(j){b(p.target).html(j).each(m,arguments)})}else{if(p.success){h.push(p.success)}}p.success=function(q,k){for(var n=0,j=h.length;n<j;n++){h[n].apply(p,[q,k,r])}};var c=b("input:file",this).fieldValue();var o=false;for(var g=0;g<c.length;g++){if(c[g]){o=true}}if(p.iframe||o){if(b.browser.safari&&p.closeKeepAlive){b.get(p.closeKeepAlive,l)}else{l()}}else{b.ajax(p)}this.trigger("form-submit-notify",[this,p]);return this;function l(){var u=r[0];if(b(":input[@name=submit]",u).length){alert('Error: Form elements must not be named "submit".');return}var q=b.extend({},b.ajaxSettings,p);var D=jQuery.extend(true,{},b.extend(true,{},b.ajaxSettings),q);var t="jqFormIO"+(new Date().getTime());var z=b('<iframe id="'+t+'" name="'+t+'" />');var B=z[0];if(b.browser.msie||b.browser.opera){B.src='javascript:false;document.write("");'}z.css({position:"absolute",top:"-1000px",left:"-1000px"});var C={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(){this.aborted=1;z.attr("src","about:blank")}};var A=q.global;if(A&&!b.active++){b.event.trigger("ajaxStart")}if(A){b.event.trigger("ajaxSend",[C,q])}if(D.beforeSend&&D.beforeSend(C,D)===false){D.global&&jQuery.active--;return}if(C.aborted){return}var k=0;var w=0;var j=u.clk;if(j){var v=j.name;if(v&&!j.disabled){p.extraData=p.extraData||{};p.extraData[v]=j.value;if(j.type=="image"){p.extraData[name+".x"]=u.clk_x;p.extraData[name+".y"]=u.clk_y}}}setTimeout(function(){var G=r.attr("target"),E=r.attr("action");r.attr({target:t,method:"POST",action:q.url});if(!p.skipEncodingOverride){r.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"})}if(q.timeout){setTimeout(function(){w=true;x()},q.timeout)}var F=[];try{if(p.extraData){for(var H in p.extraData){F.push(b('<input type="hidden" name="'+H+'" value="'+p.extraData[H]+'" />').appendTo(u)[0])}}z.appendTo("body");B.attachEvent?B.attachEvent("onload",x):B.addEventListener("load",x,false);u.submit()}finally{r.attr("action",E);G?r.attr("target",G):r.removeAttr("target");b(F).remove()}},10);function x(){if(k++){return}B.detachEvent?B.detachEvent("onload",x):B.removeEventListener("load",x,false);var E=0;var F=true;try{if(w){throw"timeout"}var G,I;I=B.contentWindow?B.contentWindow.document:B.contentDocument?B.contentDocument:B.document;if(I.body==null&&!E&&b.browser.opera){E=1;k--;setTimeout(x,100);return}C.responseText=I.body?I.body.innerHTML:null;C.responseXML=I.XMLDocument?I.XMLDocument:I;C.getResponseHeader=function(K){var J={"content-type":q.dataType};return J[K]};if(q.dataType=="json"||q.dataType=="script"){var n=I.getElementsByTagName("textarea")[0];C.responseText=n?n.value:C.responseText}else{if(q.dataType=="xml"&&!C.responseXML&&C.responseText!=null){C.responseXML=y(C.responseText)}}G=b.httpData(C,q.dataType)}catch(H){F=false;b.handleError(q,C,"error",H)}if(F){q.success(G,"success");if(A){b.event.trigger("ajaxSuccess",[C,q])}}if(A){b.event.trigger("ajaxComplete",[C,q])}if(A&&!--b.active){b.event.trigger("ajaxStop")}if(q.complete){q.complete(C,F?"success":"error")}setTimeout(function(){z.remove();C.responseXML=null},100)}function y(n,E){if(window.ActiveXObject){E=new ActiveXObject("Microsoft.XMLDOM");E.async="false";E.loadXML(n)}else{E=(new DOMParser()).parseFromString(n,"text/xml")}return(E&&E.documentElement&&E.documentElement.tagName!="parsererror")?E:null}}};b.fn.ajaxForm=function(c){return this.ajaxFormUnbind().bind("submit.form-plugin",function(){b(this).ajaxSubmit(c);return false}).each(function(){b(":submit,input:image",this).bind("click.form-plugin",function(f){var d=this.form;d.clk=this;if(this.type=="image"){if(f.offsetX!=undefined){d.clk_x=f.offsetX;d.clk_y=f.offsetY}else{if(typeof b.fn.offset=="function"){var g=b(this).offset();d.clk_x=f.pageX-g.left;d.clk_y=f.pageY-g.top}else{d.clk_x=f.pageX-this.offsetLeft;d.clk_y=f.pageY-this.offsetTop}}}setTimeout(function(){d.clk=d.clk_x=d.clk_y=null},10)})})};b.fn.ajaxFormUnbind=function(){this.unbind("submit.form-plugin");return this.each(function(){b(":submit,input:image",this).unbind("click.form-plugin")})};b.fn.formToArray=function(q){var p=[];if(this.length==0){return p}var d=this[0];var h=q?d.getElementsByTagName("*"):d.elements;if(!h){return p}for(var k=0,m=h.length;k<m;k++){var e=h[k];var f=e.name;if(!f){continue}if(q&&d.clk&&e.type=="image"){if(!e.disabled&&d.clk==e){p.push({name:f+".x",value:d.clk_x},{name:f+".y",value:d.clk_y})}continue}var r=b.fieldValue(e,true);if(r&&r.constructor==Array){for(var g=0,c=r.length;g<c;g++){p.push({name:f,value:r[g]})}}else{if(r!==null&&typeof r!="undefined"){p.push({name:f,value:r})}}}if(!q&&d.clk){var l=d.getElementsByTagName("input");for(var k=0,m=l.length;k<m;k++){var o=l[k];var f=o.name;if(f&&!o.disabled&&o.type=="image"&&d.clk==o){p.push({name:f+".x",value:d.clk_x},{name:f+".y",value:d.clk_y})}}}return p};b.fn.formSerialize=function(c){return b.param(this.formToArray(c))};b.fn.fieldSerialize=function(d){var c=[];this.each(function(){var h=this.name;if(!h){return}var f=b.fieldValue(this,d);if(f&&f.constructor==Array){for(var g=0,e=f.length;g<e;g++){c.push({name:h,value:f[g]})}}else{if(f!==null&&typeof f!="undefined"){c.push({name:this.name,value:f})}}});return b.param(c)};b.fn.fieldValue=function(h){for(var g=[],e=0,c=this.length;e<c;e++){var f=this[e];var d=b.fieldValue(f,h);if(d===null||typeof d=="undefined"||(d.constructor==Array&&!d.length)){continue}d.constructor==Array?b.merge(g,d):g.push(d)}return g};b.fieldValue=function(c,j){var e=c.name,p=c.type,q=c.tagName.toLowerCase();if(typeof j=="undefined"){j=true}if(j&&(!e||c.disabled||p=="reset"||p=="button"||(p=="checkbox"||p=="radio")&&!c.checked||(p=="submit"||p=="image")&&c.form&&c.form.clk!=c||q=="select"&&c.selectedIndex==-1)){return null}if(q=="select"){var k=c.selectedIndex;if(k<0){return null}var m=[],d=c.options;var g=(p=="select-one");var l=(g?k+1:d.length);for(var f=(g?k:0);f<l;f++){var h=d[f];if(h.selected){var o=b.browser.msie&&!(h.attributes.value.specified)?h.text:h.value;if(g){return o}m.push(o)}}return m}return c.value};b.fn.clearForm=function(){return this.each(function(){b("input,select,textarea",this).clearFields()})};b.fn.clearFields=b.fn.clearInputs=function(){return this.each(function(){var d=this.type,c=this.tagName.toLowerCase();if(d=="text"||d=="password"||c=="textarea"){this.value=""}else{if(d=="checkbox"||d=="radio"){this.checked=false}else{if(c=="select"){this.selectedIndex=-1}}}})};b.fn.resetForm=function(){return this.each(function(){if(typeof this.reset=="function"||(typeof this.reset=="object"&&!this.reset.nodeType)){this.reset()}})};b.fn.enable=function(c){if(c==undefined){c=true}return this.each(function(){this.disabled=!c})};b.fn.selected=function(c){if(c==undefined){c=true}return this.each(function(){var d=this.type;if(d=="checkbox"||d=="radio"){this.checked=c}else{if(this.tagName.toLowerCase()=="option"){var e=b(this).parent("select");if(c&&e[0]&&e[0].type=="select-one"){e.find("option").selected(false)}this.selected=c}}})};function a(){if(b.fn.ajaxSubmit.debug&&window.console&&window.console.log){window.console.log("[jquery.form] "+Array.prototype.join.call(arguments,""))}}})(jQuery);

/*tblnd*/
jQuery.tableDnD={currentTable:null,dragObject:null,mouseOffset:null,oldY:0,build:function(a){this.each(function(){this.tableDnDConfig=jQuery.extend({onDragStyle:null,onDropStyle:null,onDragClass:"tDnD_whileDrag",onDrop:null,onDragStart:null,scrollAmount:5,serializeRegexp:/[^\-]*$/,serializeParamName:null,dragHandle:null},a||{});jQuery.tableDnD.makeDraggable(this)});jQuery(document).bind("mousemove",jQuery.tableDnD.mousemove).bind("mouseup",jQuery.tableDnD.mouseup);return this},makeDraggable:function(c){var b=c.tableDnDConfig;if(c.tableDnDConfig.dragHandle){var a=jQuery("td."+c.tableDnDConfig.dragHandle,c);a.each(function(){jQuery(this).mousedown(function(e){jQuery.tableDnD.dragObject=this.parentNode;jQuery.tableDnD.currentTable=c;jQuery.tableDnD.mouseOffset=jQuery.tableDnD.getMouseOffset(this,e);if(b.onDragStart){b.onDragStart(c,this)}return false})})}else{var d=jQuery("tr",c);d.each(function(){var e=jQuery(this);if(!e.hasClass("nodrag")){e.mousedown(function(f){if(f.target.tagName=="TD"){jQuery.tableDnD.dragObject=this;jQuery.tableDnD.currentTable=c;jQuery.tableDnD.mouseOffset=jQuery.tableDnD.getMouseOffset(this,f);if(b.onDragStart){b.onDragStart(c,this)}return false}}).css("cursor","move")}})}},updateTables:function(){this.each(function(){if(this.tableDnDConfig){jQuery.tableDnD.makeDraggable(this)}})},mouseCoords:function(a){if(a.pageX||a.pageY){return{x:a.pageX,y:a.pageY}}return{x:a.clientX+document.body.scrollLeft-document.body.clientLeft,y:a.clientY+document.body.scrollTop-document.body.clientTop}},getMouseOffset:function(d,c){c=c||window.event;var b=this.getPosition(d);var a=this.mouseCoords(c);return{x:a.x-b.x,y:a.y-b.y}},getPosition:function(c){var b=0;var a=0;if(c.offsetHeight==0){c=c.firstChild}if(c&&c.offsetParent){while(c.offsetParent){b+=c.offsetLeft;a+=c.offsetTop;c=c.offsetParent}b+=c.offsetLeft;a+=c.offsetTop}return{x:b,y:a}},mousemove:function(g){if(jQuery.tableDnD.dragObject==null){return}var d=jQuery(jQuery.tableDnD.dragObject);var b=jQuery.tableDnD.currentTable.tableDnDConfig;var i=jQuery.tableDnD.mouseCoords(g);var f=i.y-jQuery.tableDnD.mouseOffset.y;var c=window.pageYOffset;if(document.all){if(typeof document.compatMode!="undefined"&&document.compatMode!="BackCompat"){c=document.documentElement.scrollTop}else{if(typeof document.body!="undefined"){c=document.body.scrollTop}}}if(i.y-c<b.scrollAmount){window.scrollBy(0,-b.scrollAmount)}else{var a=window.innerHeight?window.innerHeight:document.documentElement.clientHeight?document.documentElement.clientHeight:document.body.clientHeight;if(a-(i.y-c)<b.scrollAmount){window.scrollBy(0,b.scrollAmount)}}if(f!=jQuery.tableDnD.oldY){var e=f>jQuery.tableDnD.oldY;jQuery.tableDnD.oldY=f;if(b.onDragClass){d.addClass(b.onDragClass)}else{d.css(b.onDragStyle)}var h=jQuery.tableDnD.findDropTargetRow(d,f);if(h){if(e&&jQuery.tableDnD.dragObject!=h){jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject,h.nextSibling)}else{if(!e&&jQuery.tableDnD.dragObject!=h){jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject,h)}}}}return false},findDropTargetRow:function(f,g){var j=jQuery.tableDnD.currentTable.rows;for(var e=0;e<j.length;e++){var h=j[e];var b=this.getPosition(h).y;var a=parseInt(h.offsetHeight)/2;if(h.offsetHeight==0){b=this.getPosition(h.firstChild).y;a=parseInt(h.firstChild.offsetHeight)/2}if((g>b-a)&&(g<(b+a))){if(h==f){return null}var c=jQuery.tableDnD.currentTable.tableDnDConfig;if(c.onAllowDrop){if(c.onAllowDrop(f,h)){return h}else{return null}}else{var d=jQuery(h).hasClass("nodrop");if(!d){return h}else{return null}}return h}}return null},mouseup:function(c){if(jQuery.tableDnD.currentTable&&jQuery.tableDnD.dragObject){var b=jQuery.tableDnD.dragObject;var a=jQuery.tableDnD.currentTable.tableDnDConfig;if(a.onDragClass){jQuery(b).removeClass(a.onDragClass)}else{jQuery(b).css(a.onDropStyle)}jQuery.tableDnD.dragObject=null;if(a.onDrop){a.onDrop(jQuery.tableDnD.currentTable,b)}jQuery.tableDnD.currentTable=null}},serialize:function(){if(jQuery.tableDnD.currentTable){return jQuery.tableDnD.serializeTable(jQuery.tableDnD.currentTable)}else{return"Error: No Table id set, you need to set an id on your table and every row"}},serializeTable:function(d){var a="";var c=d.id;var e=d.rows;for(var b=0;b<e.length;b++){if(a.length>0){a+="&"}var f=e[b].id;if(f&&f&&d.tableDnDConfig&&d.tableDnDConfig.serializeRegexp){f=f.match(d.tableDnDConfig.serializeRegexp)[0]}a+=c+"[]="+f}return a},serializeTables:function(){var a="";this.each(function(){a+=jQuery.tableDnD.serializeTable(this)});return a}};jQuery.fn.extend({tableDnD:jQuery.tableDnD.build,tableDnDUpdate:jQuery.tableDnD.updateTables,tableDnDSerialize:jQuery.tableDnD.serializeTables});

/* jquery layout */
/*
 * jquery.layout 1.2.0
 *
 * Copyright (c) 2008 
 *   Fabrizio Balliano (http://www.fabrizioballiano.net)
 *   Kevin Dalman (http://allpro.net)
 *
 * Dual licensed under the GPL (http://www.gnu.org/licenses/gpl.html)
 * and MIT (http://www.opensource.org/licenses/mit-license.php) licenses.
 *
 * $Date: 2008-12-27 02:17:22 +0100 (sab, 27 dic 2008) $
 * $Rev: 203 $
 * 
 * NOTE: For best code readability, view this with a fixed-space font and tabs equal to 4-chars
 */
(function($){$.fn.layout=function(opts){var
prefix="ui-layout-",defaults={paneClass:prefix+"pane",resizerClass:prefix+"resizer",togglerClass:prefix+"toggler",togglerInnerClass:prefix+"",buttonClass:prefix+"button",contentSelector:"."+prefix+"content",contentIgnoreSelector:"."+prefix+"ignore"};var options={name:"",scrollToBookmarkOnLoad:true,defaults:{applyDefaultStyles:false,closable:true,resizable:true,slidable:true,contentSelector:defaults.contentSelector,contentIgnoreSelector:defaults.contentIgnoreSelector,paneClass:defaults.paneClass,resizerClass:defaults.resizerClass,togglerClass:defaults.togglerClass,buttonClass:defaults.buttonClass,resizerDragOpacity:1,maskIframesOnResize:true,minSize:0,maxSize:0,spacing_open:6,spacing_closed:6,togglerLength_open:50,togglerLength_closed:50,togglerAlign_open:"center",togglerAlign_closed:"center",togglerTip_open:"Close",togglerTip_closed:"Open",resizerTip:"Resize",sliderTip:"Slide Open",sliderCursor:"pointer",slideTrigger_open:"click",slideTrigger_close:"mouseout",hideTogglerOnSlide:false,togglerContent_open:"",togglerContent_closed:"",showOverflowOnHover:false,enableCursorHotkey:true,customHotkeyModifier:"SHIFT",fxName:"slide",fxSpeed:null,fxSettings:{},initClosed:false,initHidden:false},north:{paneSelector:"."+prefix+"north",size:"auto",resizerCursor:"n-resize"},south:{paneSelector:"."+prefix+"south",size:"auto",resizerCursor:"s-resize"},east:{paneSelector:"."+prefix+"east",size:200,resizerCursor:"e-resize"},west:{paneSelector:"."+prefix+"west",size:200,resizerCursor:"w-resize"},center:{paneSelector:"."+prefix+"center"}};var effects={slide:{all:{duration:"fast"},north:{direction:"up"},south:{direction:"down"},east:{direction:"right"},west:{direction:"left"}},drop:{all:{duration:"slow"},north:{direction:"up"},south:{direction:"down"},east:{direction:"right"},west:{direction:"left"}},scale:{all:{duration:"fast"}}};var config={allPanes:"north,south,east,west,center",borderPanes:"north,south,east,west",zIndex:{resizer_normal:1,pane_normal:2,mask:4,sliding:100,resizing:10000,animation:10000},resizers:{cssReq:{position:"absolute",padding:0,margin:0,fontSize:"1px",textAlign:"left",overflow:"hidden",zIndex:1},cssDef:{background:"#DDD",border:"none"}},togglers:{cssReq:{position:"absolute",display:"block",padding:0,margin:0,overflow:"hidden",textAlign:"center",fontSize:"1px",cursor:"pointer",zIndex:1},cssDef:{background:"#AAA"}},content:{cssReq:{overflow:"auto"},cssDef:{}},defaults:{cssReq:{position:"absolute",margin:0,zIndex:2},cssDef:{padding:"10px",background:"#FFF",border:"1px solid #BBB",overflow:"auto"}},north:{edge:"top",sizeType:"height",dir:"horz",cssReq:{top:0,bottom:"auto",left:0,right:0,width:"auto"}},south:{edge:"bottom",sizeType:"height",dir:"horz",cssReq:{top:"auto",bottom:0,left:0,right:0,width:"auto"}},east:{edge:"right",sizeType:"width",dir:"vert",cssReq:{left:"auto",right:0,top:"auto",bottom:"auto",height:"auto"}},west:{edge:"left",sizeType:"width",dir:"vert",cssReq:{left:0,right:"auto",top:"auto",bottom:"auto",height:"auto"}},center:{dir:"center",cssReq:{left:"auto",right:"auto",top:"auto",bottom:"auto",height:"auto",width:"auto"}}};var state={id:Math.floor(Math.random()*10000),container:{},north:{},south:{},east:{},west:{},center:{}};var
altEdge={top:"bottom",bottom:"top",left:"right",right:"left"},altSide={north:"south",south:"north",east:"west",west:"east"};var isStr=function(o){if(typeof o=="string")return true;else if(typeof o=="object"){try{var match=o.constructor.toString().match(/string/i);return(match!==null);}catch(e){}}return false;};var str=function(o){if(typeof o=="string"||isStr(o))return $.trim(o);else return o;};var min=function(x,y){return Math.min(x,y);};var max=function(x,y){return Math.max(x,y);};var transformData=function(d){var json={defaults:{fxSettings:{}},north:{fxSettings:{}},south:{fxSettings:{}},east:{fxSettings:{}},west:{fxSettings:{}},center:{fxSettings:{}}};d=d||{};if(d.effects||d.defaults||d.north||d.south||d.west||d.east||d.center)json=$.extend(json,d);else
$.each(d,function(key,val){a=key.split("__");json[a[1]?a[0]:"defaults"][a[1]?a[1]:a[0]]=val;});return json;};var setFlowCallback=function(action,pane,param){var
cb=action+","+pane+","+(param?1:0),cP,cbPane;$.each(c.borderPanes.split(","),function(i,p){if(c[p].isMoving){bindCallback(p);return false;}});function bindCallback(p,test){cP=c[p];if(!cP.doCallback){cP.doCallback=true;cP.callback=cb;}else{cpPane=cP.callback.split(",")[1];if(cpPane!=p&&cpPane!=pane)bindCallback(cpPane,true);}}};var execFlowCallback=function(pane){var cP=c[pane];c.isLayoutBusy=false;delete cP.isMoving;if(!cP.doCallback||!cP.callback)return;cP.doCallback=false;var
cb=cP.callback.split(","),param=(cb[2]>0?true:false);if(cb[0]=="open")open(cb[1],param);else if(cb[0]=="close")close(cb[1],param);if(!cP.doCallback)cP.callback=null;};var execUserCallback=function(pane,v_fn){if(!v_fn)return;var fn;try{if(typeof v_fn=="function")fn=v_fn;else if(typeof v_fn!="string")return;else if(v_fn.indexOf(",")>0){var
args=v_fn.split(","),fn=eval(args[0]);if(typeof fn=="function"&&args.length>1)return fn(args[1]);}else
fn=eval(v_fn);if(typeof fn=="function")return fn(pane,$Ps[pane],$.extend({},state[pane]),$.extend({},options[pane]),options.name);}catch(ex){}};var cssNum=function($E,prop){var
val=0,hidden=false,visibility="";if(!$.browser.msie){if($.curCSS($E[0],"display",true)=="none"){hidden=true;visibility=$.curCSS($E[0],"visibility",true);$E.css({display:"block",visibility:"hidden"});}}val=parseInt($.curCSS($E[0],prop,true),10)||0;if(hidden){$E.css({display:"none"});if(visibility&&visibility!="hidden")$E.css({visibility:visibility});}return val;};var cssW=function(e,outerWidth){var $E;if(isStr(e)){e=str(e);$E=$Ps[e];}else
$E=$(e);if(outerWidth<=0)return 0;else if(!(outerWidth>0))outerWidth=isStr(e)?getPaneSize(e):$E.outerWidth();if(!$.boxModel)return outerWidth;else
return outerWidth
-cssNum($E,"paddingLeft")-cssNum($E,"paddingRight")-($.curCSS($E[0],"borderLeftStyle",true)=="none"?0:cssNum($E,"borderLeftWidth"))-($.curCSS($E[0],"borderRightStyle",true)=="none"?0:cssNum($E,"borderRightWidth"));};var cssH=function(e,outerHeight){var $E;if(isStr(e)){e=str(e);$E=$Ps[e];}else
$E=$(e);if(outerHeight<=0)return 0;else if(!(outerHeight>0))outerHeight=(isStr(e))?getPaneSize(e):$E.outerHeight();if(!$.boxModel)return outerHeight;else
return outerHeight
-cssNum($E,"paddingTop")-cssNum($E,"paddingBottom")-($.curCSS($E[0],"borderTopStyle",true)=="none"?0:cssNum($E,"borderTopWidth"))-($.curCSS($E[0],"borderBottomStyle",true)=="none"?0:cssNum($E,"borderBottomWidth"));};var cssSize=function(pane,outerSize){if(c[pane].dir=="horz")return cssH(pane,outerSize);else
return cssW(pane,outerSize);};var getPaneSize=function(pane,inclSpace){var
$P=$Ps[pane],o=options[pane],s=state[pane],oSp=(inclSpace?o.spacing_open:0),cSp=(inclSpace?o.spacing_closed:0);if(!$P||s.isHidden)return 0;else if(s.isClosed||(s.isSliding&&inclSpace))return cSp;else if(c[pane].dir=="horz")return $P.outerHeight()+oSp;else
return $P.outerWidth()+oSp;};var setPaneMinMaxSizes=function(pane){var
d=cDims,edge=c[pane].edge,dir=c[pane].dir,o=options[pane],s=state[pane],$P=$Ps[pane],$altPane=$Ps[altSide[pane]],paneSpacing=o.spacing_open,altPaneSpacing=options[altSide[pane]].spacing_open,altPaneSize=(!$altPane?0:(dir=="horz"?$altPane.outerHeight():$altPane.outerWidth())),containerSize=(dir=="horz"?d.innerHeight:d.innerWidth),limitSize=containerSize-paneSpacing-altPaneSize-altPaneSpacing,minSize=s.minSize||0,maxSize=Math.min(s.maxSize||9999,limitSize),minPos,maxPos;switch(pane){case"north":minPos=d.offsetTop+minSize;maxPos=d.offsetTop+maxSize;break;case"west":minPos=d.offsetLeft+minSize;maxPos=d.offsetLeft+maxSize;break;case"south":minPos=d.offsetTop+d.innerHeight-maxSize;maxPos=d.offsetTop+d.innerHeight-minSize;break;case"east":minPos=d.offsetLeft+d.innerWidth-maxSize;maxPos=d.offsetLeft+d.innerWidth-minSize;break;}$.extend(s,{minSize:minSize,maxSize:maxSize,minPosition:minPos,maxPosition:maxPos});};var getPaneDims=function(){var d={top:getPaneSize("north",true),bottom:getPaneSize("south",true),left:getPaneSize("west",true),right:getPaneSize("east",true),width:0,height:0};with(d){width=cDims.innerWidth-left-right;height=cDims.innerHeight-bottom-top;top+=cDims.top;bottom+=cDims.bottom;left+=cDims.left;right+=cDims.right;}return d;};var getElemDims=function($E){var
d={},e,b,p;$.each("Left,Right,Top,Bottom".split(","),function(){e=str(this);b=d["border"+e]=cssNum($E,"border"+e+"Width");p=d["padding"+e]=cssNum($E,"padding"+e);d["offset"+e]=b+p;if($E==$Container)d[e.toLowerCase()]=($.boxModel?p:0);});d.innerWidth=d.outerWidth=$E.outerWidth();d.innerHeight=d.outerHeight=$E.outerHeight();if($.boxModel){d.innerWidth-=(d.offsetLeft+d.offsetRight);d.innerHeight-=(d.offsetTop+d.offsetBottom);}return d;};var setTimer=function(pane,action,fn,ms){var
Layout=window.layout=window.layout||{},Timers=Layout.timers=Layout.timers||{},name="layout_"+state.id+"_"+pane+"_"+action;if(Timers[name])return;else Timers[name]=setTimeout(fn,ms);};var clearTimer=function(pane,action){var
Layout=window.layout=window.layout||{},Timers=Layout.timers=Layout.timers||{},name="layout_"+state.id+"_"+pane+"_"+action;if(Timers[name]){clearTimeout(Timers[name]);delete Timers[name];return true;}else
return false;};var create=function(){initOptions();initContainer();initPanes();initHandles();initResizable();sizeContent("all");if(options.scrollToBookmarkOnLoad)with(self.location)if(hash)replace(hash);initHotkeys();$(window).resize(function(){var timerID="timerLayout_"+state.id;if(window[timerID])clearTimeout(window[timerID]);window[timerID]=null;if(true||$.browser.msie)window[timerID]=setTimeout(resizeAll,100);else
resizeAll();});};var initContainer=function(){try{if($Container[0].tagName=="BODY"){$("html").css({height:"100%",overflow:"hidden"});$("body").css({position:"relative",height:"100%",overflow:"hidden",margin:0,padding:0,border:"none"});}else{var
CSS={overflow:"hidden"},p=$Container.css("position"),h=$Container.css("height");if(!$Container.hasClass("ui-layout-pane")){if(!p||"fixed,absolute,relative".indexOf(p)<0)CSS.position="relative";if(!h||h=="auto")CSS.height="100%";}$Container.css(CSS);}}catch(ex){}cDims=state.container=getElemDims($Container);};var initHotkeys=function(){$.each(c.borderPanes.split(","),function(i,pane){var o=options[pane];if(o.enableCursorHotkey||o.customHotkey){$(document).keydown(keyDown);return false;}});};var initOptions=function(){opts=transformData(opts);if(opts.effects){$.extend(effects,opts.effects);delete opts.effects;}$.each("name,scrollToBookmarkOnLoad".split(","),function(idx,key){if(opts[key]!==undefined)options[key]=opts[key];else if(opts.defaults[key]!==undefined){options[key]=opts.defaults[key];delete opts.defaults[key];}});$.each("paneSelector,resizerCursor,customHotkey".split(","),function(idx,key){delete opts.defaults[key];});$.extend(options.defaults,opts.defaults);c.center=$.extend(true,{},c.defaults,c.center);$.extend(options.center,opts.center);var o_Center=$.extend(true,{},options.defaults,opts.defaults,options.center);$.each("paneClass,contentSelector,contentIgnoreSelector,applyDefaultStyles,showOverflowOnHover".split(","),function(idx,key){options.center[key]=o_Center[key];});var defs=options.defaults;$.each(c.borderPanes.split(","),function(i,pane){c[pane]=$.extend(true,{},c.defaults,c[pane]);o=options[pane]=$.extend(true,{},options.defaults,options[pane],opts.defaults,opts[pane]);if(!o.paneClass)o.paneClass=defaults.paneClass;if(!o.resizerClass)o.resizerClass=defaults.resizerClass;if(!o.togglerClass)o.togglerClass=defaults.togglerClass;$.each(["_open","_close",""],function(i,n){var
sName="fxName"+n,sSpeed="fxSpeed"+n,sSettings="fxSettings"+n;o[sName]=opts[pane][sName]||opts[pane].fxName||opts.defaults[sName]||opts.defaults.fxName||o[sName]||o.fxName||defs[sName]||defs.fxName||"none";var fxName=o[sName];if(fxName=="none"||!$.effects||!$.effects[fxName]||(!effects[fxName]&&!o[sSettings]&&!o.fxSettings))fxName=o[sName]="none";var
fx=effects[fxName]||{},fx_all=fx.all||{},fx_pane=fx[pane]||{};o[sSettings]=$.extend({},fx_all,fx_pane,defs.fxSettings||{},defs[sSettings]||{},o.fxSettings,o[sSettings],opts.defaults.fxSettings,opts.defaults[sSettings]||{},opts[pane].fxSettings,opts[pane][sSettings]||{});o[sSpeed]=opts[pane][sSpeed]||opts[pane].fxSpeed||opts.defaults[sSpeed]||opts.defaults.fxSpeed||o[sSpeed]||o[sSettings].duration||o.fxSpeed||o.fxSettings.duration||defs.fxSpeed||defs.fxSettings.duration||fx_pane.duration||fx_all.duration||"normal";});});};var initPanes=function(){$.each(c.allPanes.split(","),function(){var
pane=str(this),o=options[pane],s=state[pane],fx=s.fx,dir=c[pane].dir,size=o.size=="auto"||isNaN(o.size)?0:o.size,minSize=o.minSize||1,maxSize=o.maxSize||9999,spacing=o.spacing_open||0,sel=o.paneSelector,isIE6=($.browser.msie&&$.browser.version<7),CSS={},$P,$C;$Cs[pane]=false;if(sel.substr(0,1)==="#")$P=$Ps[pane]=$Container.find(sel+":first");else{$P=$Ps[pane]=$Container.children(sel+":first");if(!$P.length)$P=$Ps[pane]=$Container.children("form:first").children(sel+":first");}if(!$P.length){$Ps[pane]=false;return true;}$P.attr("pane",pane).addClass(o.paneClass+" "+o.paneClass+"-"+pane);if(pane!="center"){s.isClosed=false;s.isSliding=false;s.isResizing=false;s.isHidden=false;s.noRoom=false;c[pane].pins=[];}CSS=$.extend({visibility:"visible",display:"block"},c.defaults.cssReq,c[pane].cssReq);if(o.applyDefaultStyles)$.extend(CSS,c.defaults.cssDef,c[pane].cssDef);$P.css(CSS);CSS={};switch(pane){case"north":CSS.top=cDims.top;CSS.left=cDims.left;CSS.right=cDims.right;break;case"south":CSS.bottom=cDims.bottom;CSS.left=cDims.left;CSS.right=cDims.right;break;case"west":CSS.left=cDims.left;break;case"east":CSS.right=cDims.right;break;case"center":}if(dir=="horz"){if(size===0||size=="auto"){$P.css({height:"auto"});size=$P.outerHeight();}size=max(size,minSize);size=min(size,maxSize);size=min(size,cDims.innerHeight-spacing);CSS.height=max(1,cssH(pane,size));s.size=size;s.maxSize=maxSize;s.minSize=max(minSize,size-CSS.height+1);$P.css(CSS);}else if(dir=="vert"){if(size===0||size=="auto"){$P.css({width:"auto",float:"left"});size=$P.outerWidth();$P.css({float:"none"});}size=max(size,minSize);size=min(size,maxSize);size=min(size,cDims.innerWidth-spacing);CSS.width=max(1,cssW(pane,size));s.size=size;s.maxSize=maxSize;s.minSize=max(minSize,size-CSS.width+1);$P.css(CSS);sizeMidPanes(pane,null,true);}else if(pane=="center"){$P.css(CSS);sizeMidPanes("center",null,true);}if(o.initClosed&&o.closable){$P.hide().addClass("closed");s.isClosed=true;}else if(o.initHidden||o.initClosed){hide(pane,true);s.isHidden=true;}else
$P.addClass("open");if(o.showOverflowOnHover)$P.hover(allowOverflow,resetOverflow);if(o.contentSelector){$C=$Cs[pane]=$P.children(o.contentSelector+":first");if(!$C.length){$Cs[pane]=false;return true;}$C.css(c.content.cssReq);if(o.applyDefaultStyles)$C.css(c.content.cssDef);$P.css({overflow:"hidden"});}});};var initHandles=function(){$.each(c.borderPanes.split(","),function(){var
pane=str(this),o=options[pane],s=state[pane],rClass=o.resizerClass,tClass=o.togglerClass,$P=$Ps[pane];$Rs[pane]=false;$Ts[pane]=false;if(!$P||(!o.closable&&!o.resizable))return;var
edge=c[pane].edge,isOpen=$P.is(":visible"),spacing=(isOpen?o.spacing_open:o.spacing_closed),_pane="-"+pane,_state=(isOpen?"-open":"-closed"),$R,$T;$R=$Rs[pane]=$("<span></span>");if(isOpen&&o.resizable);else if(!isOpen&&o.slidable)$R.attr("title",o.sliderTip).css("cursor",o.sliderCursor);$R.attr("id",(o.paneSelector.substr(0,1)=="#"?o.paneSelector.substr(1)+"-resizer":"")).attr("resizer",pane).css(c.resizers.cssReq).css(edge,cDims[edge]+getPaneSize(pane)).addClass(rClass+" "+rClass+_pane+" "+rClass+_state+" "+rClass+_pane+_state).appendTo($Container);if(o.applyDefaultStyles)$R.css(c.resizers.cssDef);if(o.closable){$T=$Ts[pane]=$("<div></div>");$T.attr("id",(o.paneSelector.substr(0,1)=="#"?o.paneSelector.substr(1)+"-toggler":"")).css(c.togglers.cssReq).attr("title",(isOpen?o.togglerTip_open:o.togglerTip_closed)).click(function(evt){toggle(pane);evt.stopPropagation();}).mouseover(function(evt){evt.stopPropagation();}).addClass(tClass+" "+tClass+_pane+" "+tClass+_state+" "+tClass+_pane+_state).appendTo($R);if(o.togglerContent_open)$("<span>"+o.togglerContent_open+"</span>").addClass("content content-open").css("display",s.isClosed?"none":"block").appendTo($T);if(o.togglerContent_closed)$("<span>"+o.togglerContent_closed+"</span>").addClass("content content-closed").css("display",s.isClosed?"block":"none").appendTo($T);if(o.applyDefaultStyles)$T.css(c.togglers.cssDef);if(!isOpen)bindStartSlidingEvent(pane,true);}});sizeHandles("all",true);};var initResizable=function(){var
draggingAvailable=(typeof $.fn.draggable=="function"),minPosition,maxPosition,edge;$.each(c.borderPanes.split(","),function(){var
pane=str(this),o=options[pane],s=state[pane];if(!draggingAvailable||!$Ps[pane]||!o.resizable){o.resizable=false;return true;}var
rClass=o.resizerClass,dragClass=rClass+"-drag",dragPaneClass=rClass+"-"+pane+"-drag",draggingClass=rClass+"-dragging",draggingPaneClass=rClass+"-"+pane+"-dragging",draggingClassSet=false,$P=$Ps[pane],$R=$Rs[pane];if(!s.isClosed)$R.attr("title",o.resizerTip).css("cursor",o.resizerCursor);$R.draggable({containment:$Container[0],axis:(c[pane].dir=="horz"?"y":"x"),delay:200,distance:1,helper:"clone",opacity:o.resizerDragOpacity,zIndex:c.zIndex.resizing,start:function(e,ui){if(false===execUserCallback(pane,o.onresize_start))return false;s.isResizing=true;clearTimer(pane,"closeSlider");$R.addClass(dragClass+" "+dragPaneClass);draggingClassSet=false;var resizerWidth=(pane=="east"||pane=="south"?o.spacing_open:0);setPaneMinMaxSizes(pane);s.minPosition-=resizerWidth;s.maxPosition-=resizerWidth;edge=(c[pane].dir=="horz"?"top":"left");$(o.maskIframesOnResize===true?"iframe":o.maskIframesOnResize).each(function(){$('<div class="ui-layout-mask"/>').css({background:"#fff",opacity:"0.001",zIndex:9,position:"absolute",width:this.offsetWidth+"px",height:this.offsetHeight+"px"}).css($(this).offset()).appendTo(this.parentNode);});},drag:function(e,ui){if(!draggingClassSet){$(".ui-draggable-dragging").addClass(draggingClass+" "+draggingPaneClass).children().css("visibility","hidden");draggingClassSet=true;if(s.isSliding)$Ps[pane].css("zIndex",c.zIndex.sliding);}if(ui.position[edge]<s.minPosition)ui.position[edge]=s.minPosition;else if(ui.position[edge]>s.maxPosition)ui.position[edge]=s.maxPosition;},stop:function(e,ui){var
dragPos=ui.position,resizerPos,newSize;$R.removeClass(dragClass+" "+dragPaneClass);switch(pane){case"north":resizerPos=dragPos.top;break;case"west":resizerPos=dragPos.left;break;case"south":resizerPos=cDims.outerHeight-dragPos.top-$R.outerHeight();break;case"east":resizerPos=cDims.outerWidth-dragPos.left-$R.outerWidth();break;}newSize=resizerPos-cDims[c[pane].edge];sizePane(pane,newSize);$("div.ui-layout-mask").remove();s.isResizing=false;}});});};var hide=function(pane,onInit){var
o=options[pane],s=state[pane],$P=$Ps[pane],$R=$Rs[pane];if(!$P||s.isHidden)return;if(false===execUserCallback(pane,o.onhide_start))return;s.isSliding=false;if($R)$R.hide();if(onInit||s.isClosed){s.isClosed=true;s.isHidden=true;$P.hide();sizeMidPanes(c[pane].dir=="horz"?"all":"center");execUserCallback(pane,o.onhide_end||o.onhide);}else{s.isHiding=true;close(pane,false);}};var show=function(pane,openPane){var
o=options[pane],s=state[pane],$P=$Ps[pane],$R=$Rs[pane];if(!$P||!s.isHidden)return;if(false===execUserCallback(pane,o.onshow_start))return;s.isSliding=false;s.isShowing=true;if($R&&o.spacing_open>0)$R.show();if(openPane===false)close(pane,true);else
open(pane);};var toggle=function(pane){var s=state[pane];if(s.isHidden)show(pane);else if(s.isClosed)open(pane);else
close(pane);};var close=function(pane,force,noAnimation){var
$P=$Ps[pane],$R=$Rs[pane],$T=$Ts[pane],o=options[pane],s=state[pane],doFX=!noAnimation&&!s.isClosed&&(o.fxName_close!="none"),edge=c[pane].edge,rClass=o.resizerClass,tClass=o.togglerClass,_pane="-"+pane,_open="-open",_sliding="-sliding",_closed="-closed",isShowing=s.isShowing,isHiding=s.isHiding;delete s.isShowing;delete s.isHiding;if(!$P||(!o.resizable&&!o.closable))return;else if(!force&&s.isClosed&&!isShowing)return;if(c.isLayoutBusy){setFlowCallback("close",pane,force);return;}if(!isShowing&&false===execUserCallback(pane,o.onclose_start))return;c[pane].isMoving=true;c.isLayoutBusy=true;s.isClosed=true;if(isHiding)s.isHidden=true;else if(isShowing)s.isHidden=false;syncPinBtns(pane,false);if(!s.isSliding)sizeMidPanes(c[pane].dir=="horz"?"all":"center");if($R){$R.css(edge,cDims[edge]).removeClass(rClass+_open+" "+rClass+_pane+_open).removeClass(rClass+_sliding+" "+rClass+_pane+_sliding).addClass(rClass+_closed+" "+rClass+_pane+_closed);if(o.resizable)$R.draggable("disable").css("cursor","default").attr("title","");if($T){$T.removeClass(tClass+_open+" "+tClass+_pane+_open).addClass(tClass+_closed+" "+tClass+_pane+_closed).attr("title",o.togglerTip_closed);}sizeHandles();}if(doFX){lockPaneForFX(pane,true);$P.hide(o.fxName_close,o.fxSettings_close,o.fxSpeed_close,function(){lockPaneForFX(pane,false);if(!s.isClosed)return;close_2();});}else{$P.hide();close_2();}function close_2(){bindStartSlidingEvent(pane,true);if(!isShowing)execUserCallback(pane,o.onclose_end||o.onclose);if(isShowing)execUserCallback(pane,o.onshow_end||o.onshow);if(isHiding)execUserCallback(pane,o.onhide_end||o.onhide);execFlowCallback(pane);}};var open=function(pane,slide,noAnimation){var
$P=$Ps[pane],$R=$Rs[pane],$T=$Ts[pane],o=options[pane],s=state[pane],doFX=!noAnimation&&s.isClosed&&(o.fxName_open!="none"),edge=c[pane].edge,rClass=o.resizerClass,tClass=o.togglerClass,_pane="-"+pane,_open="-open",_closed="-closed",_sliding="-sliding",isShowing=s.isShowing;delete s.isShowing;if(!$P||(!o.resizable&&!o.closable))return;else if(!s.isClosed&&!s.isSliding)return;if(s.isHidden&&!isShowing){show(pane,true);return;}if(c.isLayoutBusy){setFlowCallback("open",pane,slide);return;}if(false===execUserCallback(pane,o.onopen_start))return;c[pane].isMoving=true;c.isLayoutBusy=true;if(s.isSliding&&!slide)bindStopSlidingEvents(pane,false);s.isClosed=false;if(isShowing)s.isHidden=false;setPaneMinMaxSizes(pane);if(s.size>s.maxSize)$P.css(c[pane].sizeType,max(1,cssSize(pane,s.maxSize)));bindStartSlidingEvent(pane,false);if(doFX){lockPaneForFX(pane,true);$P.show(o.fxName_open,o.fxSettings_open,o.fxSpeed_open,function(){lockPaneForFX(pane,false);if(s.isClosed)return;open_2();});}else{$P.show();open_2();}function open_2(){if(!s.isSliding)sizeMidPanes(c[pane].dir=="vert"?"center":"all");if($R){$R.css(edge,cDims[edge]+getPaneSize(pane)).removeClass(rClass+_closed+" "+rClass+_pane+_closed).addClass(rClass+_open+" "+rClass+_pane+_open).addClass(!s.isSliding?"":rClass+_sliding+" "+rClass+_pane+_sliding);if(o.resizable)$R.draggable("enable").css("cursor",o.resizerCursor).attr("title",o.resizerTip);else
$R.css("cursor","default");if($T){$T.removeClass(tClass+_closed+" "+tClass+_pane+_closed).addClass(tClass+_open+" "+tClass+_pane+_open).attr("title",o.togglerTip_open);}sizeHandles("all");}sizeContent(pane);syncPinBtns(pane,!s.isSliding);execUserCallback(pane,o.onopen_end||o.onopen);if(isShowing)execUserCallback(pane,o.onshow_end||o.onshow);execFlowCallback(pane);}};var lockPaneForFX=function(pane,doLock){var $P=$Ps[pane];if(doLock){$P.css({zIndex:c.zIndex.animation});if(pane=="south")$P.css({top:cDims.top+cDims.innerHeight-$P.outerHeight()});else if(pane=="east")$P.css({left:cDims.left+cDims.innerWidth-$P.outerWidth()});}else{if(!state[pane].isSliding)$P.css({zIndex:c.zIndex.pane_normal});if(pane=="south")$P.css({top:"auto"});else if(pane=="east")$P.css({left:"auto"});}};var bindStartSlidingEvent=function(pane,enable){var
o=options[pane],$R=$Rs[pane],trigger=o.slideTrigger_open;if(!$R||!o.slidable)return;if(trigger!="click"&&trigger!="dblclick"&&trigger!="mouseover")trigger="click";$R
[enable?"bind":"unbind"](trigger,slideOpen).css("cursor",(enable?o.sliderCursor:"default")).attr("title",(enable?o.sliderTip:""));};var bindStopSlidingEvents=function(pane,enable){var
o=options[pane],s=state[pane],trigger=o.slideTrigger_close,action=(enable?"bind":"unbind"),$P=$Ps[pane],$R=$Rs[pane];s.isSliding=enable;clearTimer(pane,"closeSlider");$P.css({zIndex:(enable?c.zIndex.sliding:c.zIndex.pane_normal)});$R.css({zIndex:(enable?c.zIndex.sliding:c.zIndex.resizer_normal)});if(trigger!="click"&&trigger!="mouseout")trigger="mouseout";if(enable){$P.bind(trigger,slideClosed);$R.bind(trigger,slideClosed);if(trigger="mouseout"){$P.bind("mouseover",cancelMouseOut);$R.bind("mouseover",cancelMouseOut);}}else{$P.unbind(trigger);$R.unbind(trigger);if(trigger="mouseout"){$P.unbind("mouseover");$R.unbind("mouseover");clearTimer(pane,"closeSlider");}}function cancelMouseOut(evt){clearTimer(pane,"closeSlider");evt.stopPropagation();}};var slideOpen=function(){var pane=$(this).attr("resizer");if(state[pane].isClosed){bindStopSlidingEvents(pane,true);open(pane,true);}};var slideClosed=function(){var
$E=$(this),pane=$E.attr("pane")||$E.attr("resizer"),o=options[pane],s=state[pane];if(s.isClosed||s.isResizing)return;else if(o.slideTrigger_close=="click")close_NOW();else
setTimer(pane,"closeSlider",close_NOW,300);function close_NOW(){bindStopSlidingEvents(pane,false);if(!s.isClosed)close(pane);}};var sizePane=function(pane,size){var
edge=c[pane].edge,dir=c[pane].dir,o=options[pane],s=state[pane],$P=$Ps[pane],$R=$Rs[pane];setPaneMinMaxSizes(pane);s.minSize=max(s.minSize,o.minSize);if(o.maxSize>0)s.maxSize=min(s.maxSize,o.maxSize);size=max(size,s.minSize);size=min(size,s.maxSize);s.size=size;$R.css(edge,size+cDims[edge]);$P.css(c[pane].sizeType,max(1,cssSize(pane,size)));if(!s.isSliding)sizeMidPanes(dir=="horz"?"all":"center");sizeHandles();sizeContent(pane);execUserCallback(pane,o.onresize_end||o.onresize);};var sizeMidPanes=function(panes,overrideDims,onInit){if(!panes||panes=="all")panes="east,west,center";var d=getPaneDims();if(overrideDims)$.extend(d,overrideDims);$.each(panes.split(","),function(){if(!$Ps[this])return;var
pane=str(this),o=options[pane],s=state[pane],$P=$Ps[pane],$R=$Rs[pane],hasRoom=true,CSS={};if(pane=="center"){d=getPaneDims();CSS=$.extend({},d);CSS.width=max(1,cssW(pane,CSS.width));CSS.height=max(1,cssH(pane,CSS.height));hasRoom=(CSS.width>1&&CSS.height>1);if($.browser.msie&&(!$.boxModel||$.browser.version<7)){if($Ps.north)$Ps.north.css({width:cssW($Ps.north,cDims.innerWidth)});if($Ps.south)$Ps.south.css({width:cssW($Ps.south,cDims.innerWidth)});}}else{CSS.top=d.top;CSS.bottom=d.bottom;CSS.height=max(1,cssH(pane,d.height));hasRoom=(CSS.height>1);}if(hasRoom){$P.css(CSS);if(s.noRoom){s.noRoom=false;if(s.isHidden)return;else show(pane,!s.isClosed);}if(!onInit){sizeContent(pane);execUserCallback(pane,o.onresize_end||o.onresize);}}else if(!s.noRoom){s.noRoom=true;if(s.isHidden)return;if(onInit){$P.hide();if($R)$R.hide();}else hide(pane);}});};var sizeContent=function(panes){if(!panes||panes=="all")panes=c.allPanes;$.each(panes.split(","),function(){if(!$Cs[this])return;var
pane=str(this),ignore=options[pane].contentIgnoreSelector,$P=$Ps[pane],$C=$Cs[pane],e_C=$C[0],height=cssH($P);;$P.children().each(function(){if(this==e_C)return;var $E=$(this);if(!ignore||!$E.is(ignore))height-=$E.outerHeight();});if(height>0)height=cssH($C,height);if(height<1)$C.hide();else
$C.css({height:height}).show();});};var sizeHandles=function(panes,onInit){if(!panes||panes=="all")panes=c.borderPanes;$.each(panes.split(","),function(){var
pane=str(this),o=options[pane],s=state[pane],$P=$Ps[pane],$R=$Rs[pane],$T=$Ts[pane];if(!$P||!$R||(!o.resizable&&!o.closable))return;var
dir=c[pane].dir,_state=(s.isClosed?"_closed":"_open"),spacing=o["spacing"+_state],togAlign=o["togglerAlign"+_state],togLen=o["togglerLength"+_state],paneLen,offset,CSS={};if(spacing==0){$R.hide();return;}else if(!s.noRoom&&!s.isHidden)$R.show();if(dir=="horz"){paneLen=$P.outerWidth();$R.css({width:max(1,cssW($R,paneLen)),height:max(1,cssH($R,spacing)),left:cssNum($P,"left")});}else{paneLen=$P.outerHeight();$R.css({height:max(1,cssH($R,paneLen)),width:max(1,cssW($R,spacing)),top:cDims.top+getPaneSize("north",true)});}if($T){if(togLen==0||(s.isSliding&&o.hideTogglerOnSlide)){$T.hide();return;}else
$T.show();if(!(togLen>0)||togLen=="100%"||togLen>paneLen){togLen=paneLen;offset=0;}else{if(typeof togAlign=="string"){switch(togAlign){case"top":case"left":offset=0;break;case"bottom":case"right":offset=paneLen-togLen;break;case"middle":case"center":default:offset=Math.floor((paneLen-togLen)/2);}}else{var x=parseInt(togAlign);if(togAlign>=0)offset=x;else offset=paneLen-togLen+x;}}var
$TC_o=(o.togglerContent_open?$T.children(".content-open"):false),$TC_c=(o.togglerContent_closed?$T.children(".content-closed"):false),$TC=(s.isClosed?$TC_c:$TC_o);if($TC_o)$TC_o.css("display",s.isClosed?"none":"block");if($TC_c)$TC_c.css("display",s.isClosed?"block":"none");if(dir=="horz"){var width=cssW($T,togLen);$T.css({width:max(0,width),height:max(1,cssH($T,spacing)),left:offset});if($TC)$TC.css("marginLeft",Math.floor((width-$TC.outerWidth())/2));}else{var height=cssH($T,togLen);$T.css({height:max(0,height),width:max(1,cssW($T,spacing)),top:offset});if($TC)$TC.css("marginTop",Math.floor((height-$TC.outerHeight())/2));}}if(onInit&&o.initHidden){$R.hide();if($T)$T.hide();}});};var resizeAll=function(){var
oldW=cDims.innerWidth,oldH=cDims.innerHeight;cDims=state.container=getElemDims($Container);var
checkH=(cDims.innerHeight<oldH),checkW=(cDims.innerWidth<oldW),s,dir;if(checkH||checkW)$.each(["south","north","east","west"],function(i,pane){s=state[pane];dir=c[pane].dir;if(!s.isClosed&&((checkH&&dir=="horz")||(checkW&&dir=="vert"))){setPaneMinMaxSizes(pane);if(s.size>s.maxSize)sizePane(pane,s.maxSize);}});sizeMidPanes("all");sizeHandles("all");};function keyDown(evt){if(!evt)return true;var code=evt.keyCode;if(code<33)return true;var
PANE={38:"north",40:"south",37:"west",39:"east"},isCursorKey=(code>=37&&code<=40),ALT=evt.altKey,SHIFT=evt.shiftKey,CTRL=evt.ctrlKey,pane=false,s,o,k,m,el;if(!CTRL&&!SHIFT)return true;else if(isCursorKey&&options[PANE[code]].enableCursorHotkey)pane=PANE[code];else
$.each(c.borderPanes.split(","),function(i,p){o=options[p];k=o.customHotkey;m=o.customHotkeyModifier;if((SHIFT&&m=="SHIFT")||(CTRL&&m=="CTRL")||(CTRL&&SHIFT)){if(k&&code==(isNaN(k)||k<=9?k.toUpperCase().charCodeAt(0):k)){pane=p;return false;}}});if(!pane)return true;o=options[pane];s=state[pane];if(!o.enableCursorHotkey||s.isHidden||!$Ps[pane])return true;el=evt.target||evt.srcElement;if(el&&SHIFT&&isCursorKey&&(el.tagName=="TEXTAREA"||(el.tagName=="INPUT"&&(code==37||code==39))))return true;toggle(pane);evt.stopPropagation();evt.returnValue=false;return false;};function allowOverflow(elem){if(this&&this.tagName)elem=this;var $P;if(typeof elem=="string")$P=$Ps[elem];else{if($(elem).attr("pane"))$P=$(elem);else $P=$(elem).parents("div[pane]:first");}if(!$P.length)return;var
pane=$P.attr("pane"),s=state[pane];if(s.cssSaved)resetOverflow(pane);if(s.isSliding||s.isResizing||s.isClosed){s.cssSaved=false;return;}var
newCSS={zIndex:(c.zIndex.pane_normal+1)},curCSS={},of=$P.css("overflow"),ofX=$P.css("overflowX"),ofY=$P.css("overflowY");if(of!="visible"){curCSS.overflow=of;newCSS.overflow="visible";}if(ofX&&ofX!="visible"&&ofX!="auto"){curCSS.overflowX=ofX;newCSS.overflowX="visible";}if(ofY&&ofY!="visible"&&ofY!="auto"){curCSS.overflowY=ofX;newCSS.overflowY="visible";}s.cssSaved=curCSS;$P.css(newCSS);$.each(c.allPanes.split(","),function(i,p){if(p!=pane)resetOverflow(p);});};function resetOverflow(elem){if(this&&this.tagName)elem=this;var $P;if(typeof elem=="string")$P=$Ps[elem];else{if($(elem).hasClass("ui-layout-pane"))$P=$(elem);else $P=$(elem).parents("div[pane]:first");}if(!$P.length)return;var
pane=$P.attr("pane"),s=state[pane],CSS=s.cssSaved||{};if(!s.isSliding&&!s.isResizing)$P.css("zIndex",c.zIndex.pane_normal);$P.css(CSS);s.cssSaved=false;};function getBtn(selector,pane,action){var
$E=$(selector),err="Error Adding Button \n\nInvalid ";if(!$E.length)alert(err+"selector: "+selector);else if(c.borderPanes.indexOf(pane)==-1)alert(err+"pane: "+pane);else{var btn=options[pane].buttonClass+"-"+action;$E.addClass(btn+" "+btn+"-"+pane);return $E;}return false;};function addToggleBtn(selector,pane){var $E=getBtn(selector,pane,"toggle");if($E)$E.attr("title",state[pane].isClosed?"Open":"Close").click(function(evt){toggle(pane);evt.stopPropagation();});};function addOpenBtn(selector,pane){var $E=getBtn(selector,pane,"open");if($E)$E.attr("title","Open").click(function(evt){open(pane);evt.stopPropagation();});};function addCloseBtn(selector,pane){var $E=getBtn(selector,pane,"close");if($E)$E.attr("title","Close").click(function(evt){close(pane);evt.stopPropagation();});};function addPinBtn(selector,pane){var $E=getBtn(selector,pane,"pin");if($E){var s=state[pane];$E.click(function(evt){setPinState($(this),pane,(s.isSliding||s.isClosed));if(s.isSliding||s.isClosed)open(pane);else close(pane);evt.stopPropagation();});setPinState($E,pane,(!s.isClosed&&!s.isSliding));c[pane].pins.push(selector);}};function syncPinBtns(pane,doPin){$.each(c[pane].pins,function(i,selector){setPinState($(selector),pane,doPin);});};function setPinState($Pin,pane,doPin){var updown=$Pin.attr("pin");if(updown&&doPin==(updown=="down"))return;var
root=options[pane].buttonClass,class1=root+"-pin",class2=class1+"-"+pane,UP1=class1+"-up",UP2=class2+"-up",DN1=class1+"-down",DN2=class2+"-down";$Pin.attr("pin",doPin?"down":"up").attr("title",doPin?"Un-Pin":"Pin").removeClass(doPin?UP1:DN1).removeClass(doPin?UP2:DN2).addClass(doPin?DN1:UP1).addClass(doPin?DN2:UP2);};var
$Container=$(this).css({overflow:"hidden"}),$Ps={},$Cs={},$Rs={},$Ts={},c=config,cDims=state.container;create();return{options:options,state:state,panes:$Ps,toggle:toggle,open:open,close:close,hide:hide,show:show,resizeContent:sizeContent,sizePane:sizePane,resizeAll:resizeAll,addToggleBtn:addToggleBtn,addOpenBtn:addOpenBtn,addCloseBtn:addCloseBtn,addPinBtn:addPinBtn,allowOverflow:allowOverflow,resetOverflow:resetOverflow,cssWidth:cssW,cssHeight:cssH};}})(jQuery);

/*menu*/


jQuery.fn.bgIframe = jQuery.fn.bgiframe = function() {
	// This is only for IE6
	if ( !(jQuery.browser.msie && typeof XMLHttpRequest == 'function') ) return this;
	var html = '<iframe class="bgiframe" src="javascript:false;document.write(\'\');" tabindex="-1" '
	 					+'style="display:block; position:absolute; '
						+'top: expression(((parseInt(this.parentNode.currentStyle.borderTopWidth)  || 0) * -1) + \'px\'); '
						+'left:expression(((parseInt(this.parentNode.currentStyle.borderLeftWidth) || 0) * -1) + \'px\'); ' 
						+'z-index:-1; filter:Alpha(Opacity=\'0\'); '
						+'width:expression(this.parentNode.offsetWidth + \'px\'); '
						+'height:expression(this.parentNode.offsetHeight + \'px\')"/>';
	return this.each(function() {
		if ( !jQuery('iframe.bgiframe', this)[0] )
			this.insertBefore( document.createElement(html), this.firstChild );
	});
};

/*
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * $LastChangedDate: 2007-03-04 20:15:11 -0600 (Sun, 04 Mar 2007) $
 * $Rev: 1485 $
 */

jQuery.fn._height = jQuery.fn.height;
jQuery.fn._width  = jQuery.fn.width;

/**
 * If used on document, returns the document's height (innerHeight)
 * If used on window, returns the viewport's (window) height
 * See core docs on height() to see what happens when used on an element.
 *
 * @example $("#testdiv").height()
 * @result 200
 *
 * @example $(document).height()
 * @result 800
 *
 * @example $(window).height()
 * @result 400
 *
 * @name height
 * @type Object
 * @cat Plugins/Dimensions
 */
jQuery.fn.height = function() {
	if ( this[0] == window )
		return self.innerHeight ||
			jQuery.boxModel && document.documentElement.clientHeight ||
			document.body.clientHeight;

	if ( this[0] == document )
		return Math.max( document.body.scrollHeight, document.body.offsetHeight );

	return this._height(arguments[0]);
};

jQuery.fn.width = function() {
	if ( this[0] == window )
		return self.innerWidth ||
			jQuery.boxModel && document.documentElement.clientWidth ||
			document.body.clientWidth;

	if ( this[0] == document )
		return Math.max( document.body.scrollWidth, document.body.offsetWidth );

	return this._width(arguments[0]);
};

jQuery.fn.innerHeight = function() {
	return this[0] == window || this[0] == document ?
		this.height() :
		this.css('display') != 'none' ?
		 	this[0].offsetHeight - (parseInt(this.css("borderTopWidth")) || 0) - (parseInt(this.css("borderBottomWidth")) || 0) :
			this.height() + (parseInt(this.css("paddingTop")) || 0) + (parseInt(this.css("paddingBottom")) || 0);
};

jQuery.fn.innerWidth = function() {
	return this[0] == window || this[0] == document ?
		this.width() :
		this.css('display') != 'none' ?
			this[0].offsetWidth - (parseInt(this.css("borderLeftWidth")) || 0) - (parseInt(this.css("borderRightWidth")) || 0) :
			this.height() + (parseInt(this.css("paddingLeft")) || 0) + (parseInt(this.css("paddingRight")) || 0);
};


jQuery.fn.outerHeight = function() {
	return this[0] == window || this[0] == document ?
		this.height() :
		this.css('display') != 'none' ?
			this[0].offsetHeight :
			this.height() + (parseInt(this.css("borderTopWidth")) || 0) + (parseInt(this.css("borderBottomWidth")) || 0)
				+ (parseInt(this.css("paddingTop")) || 0) + (parseInt(this.css("paddingBottom")) || 0);
};


jQuery.fn.outerWidth = function() {
	return this[0] == window || this[0] == document ?
		this.width() :
		this.css('display') != 'none' ?
			this[0].offsetWidth :
			this.height() + (parseInt(this.css("borderLeftWidth")) || 0) + (parseInt(this.css("borderRightWidth")) || 0)
				+ (parseInt(this.css("paddingLeft")) || 0) + (parseInt(this.css("paddingRight")) || 0);
};

jQuery.fn.scrollLeft = function() {
	if ( this[0] == window || this[0] == document )
		return self.pageXOffset ||
			jQuery.boxModel && document.documentElement.scrollLeft ||
			document.body.scrollLeft;

	return this[0].scrollLeft;
};

jQuery.fn.scrollTop = function() {
	if ( this[0] == window || this[0] == document )
		return self.pageYOffset ||
			jQuery.boxModel && document.documentElement.scrollTop ||
			document.body.scrollTop;

	return this[0].scrollTop;
};

jQuery.fn.offset = function(options, returnObject) {
	var x = 0, y = 0, elem = this[0], parent = this[0], op, sl = 0, st = 0, options = jQuery.extend({ margin: true, border: true, padding: false, scroll: true }, options || {});
	do {
		x += parent.offsetLeft || 0;
		y += parent.offsetTop  || 0;

		// Mozilla and IE do not add the border
		if (jQuery.browser.mozilla || jQuery.browser.msie) {
			// get borders
			var bt = parseInt(jQuery.css(parent, 'borderTopWidth')) || 0;
			var bl = parseInt(jQuery.css(parent, 'borderLeftWidth')) || 0;

			// add borders to offset
			x += bl;
			y += bt;

			// Mozilla removes the border if the parent has overflow property other than visible
			if (jQuery.browser.mozilla && parent != elem && jQuery.css(parent, 'overflow') != 'visible') {
				x += bl;
				y += bt;
			}
		}

		if (options.scroll) {
			// Need to get scroll offsets in-between offsetParents
			op = parent.offsetParent;
			do {
				sl += parent.scrollLeft || 0;
				st += parent.scrollTop  || 0;

				parent = parent.parentNode;

				// Mozilla removes the border if the parent has overflow property other than visible
				if (jQuery.browser.mozilla && parent != elem && parent != op && jQuery.css(parent, 'overflow') != 'visible') {
					y += parseInt(jQuery.css(parent, 'borderTopWidth')) || 0;
					x += parseInt(jQuery.css(parent, 'borderLeftWidth')) || 0;
				}
			} while (parent != op);
		} else
			parent = parent.offsetParent;

		if (parent && (parent.tagName.toLowerCase() == 'body' || parent.tagName.toLowerCase() == 'html')) {
			// Safari doesn't add the body margin for elments positioned with static or relative
			if ((jQuery.browser.safari || (jQuery.browser.msie && jQuery.boxModel)) && jQuery.css(parent, 'position') != 'absolute') {
				x += parseInt(jQuery.css(op, 'marginLeft')) || 0;
				y += parseInt(jQuery.css(op, 'marginTop'))  || 0;
			}
			break; // Exit the loop
		}
	} while (parent);

	if ( !options.margin) {
		x -= parseInt(jQuery.css(elem, 'marginLeft')) || 0;
		y -= parseInt(jQuery.css(elem, 'marginTop'))  || 0;
	}

	// Safari and Opera do not add the border for the element
	if ( options.border && (jQuery.browser.safari || jQuery.browser.opera) ) {
		x += parseInt(jQuery.css(elem, 'borderLeftWidth')) || 0;
		y += parseInt(jQuery.css(elem, 'borderTopWidth'))  || 0;
	} else if ( !options.border && !(jQuery.browser.safari || jQuery.browser.opera) ) {
		x -= parseInt(jQuery.css(elem, 'borderLeftWidth')) || 0;
		y -= parseInt(jQuery.css(elem, 'borderTopWidth'))  || 0;
	}

	if ( options.padding ) {
		x += parseInt(jQuery.css(elem, 'paddingLeft')) || 0;
		y += parseInt(jQuery.css(elem, 'paddingTop'))  || 0;
	}

	// Opera thinks offset is scroll offset for display: inline elements
	if (options.scroll && jQuery.browser.opera && jQuery.css(elem, 'display') == 'inline') {
		sl -= elem.scrollLeft || 0;
		st -= elem.scrollTop  || 0;
	}

	var returnValue = options.scroll ? { top: y - st, left: x - sl, scrollTop:  st, scrollLeft: sl }
	                                 : { top: y, left: x };

	if (returnObject) { jQuery.extend(returnObject, returnValue); return this; }
	else              { return returnValue; }
};


(function($){
	// This will store an element list of all our menu objects
	var jdMenu = [];
	
	// Public methods
	$.fn.jdMenu = function(inSettings) {
		var settings = $.extend({}, arguments.callee.defaults, inSettings);
		return this.each(function() {
			jdMenu.push(this);
			$(this).addClass('jd_menu_flag_root');
			this.$settings = $.extend({}, settings, {isVerticalMenu: $(this).is('.jd_menu_vertical')});
			addEvents(this);
		});
	};
	$.fn.jdMenuShow = function() {
		return this.each(function() {
			showMenuLI.apply(this);
		});
	};
	$.fn.jdMenuHide = function() {
		return this.each(function() {
			hideMenuUL.apply(this);
		});
	};

	// Private methods and logic
	$(window)
		// Bind a click event to hide all visible menus when the document is clicked
		.bind('click', function(){
			$(jdMenu).find('ul:visible').jdMenuHide();
		})
		// Cleanup after ourself by nulling the $settings object
		.bind('unload', function() {
			$(jdMenu).each(function() {
				this.$settings = null;
			});
		});

	// These are our default settings for this plugin
	$.fn.jdMenu.defaults = {
		activateDelay: 750,
		showDelay: 150,
		hideDelay: 550,
		onShow: null,
		onHideCheck: null,
		onHide: null,
		onAnimate: null,
		onClick: null,
		offsetX: 4,
		offsetY: 2,
		iframe: $.browser.msie
	};
	
	// Our special parentsUntil method to get all parents up to and including the matched element
	$.fn.parentsUntil = function(match) {
		var a = [];
		$(this[0]).parents().each(function() {
			a.push(this);
			return !$(this).is(match);
		});
		return this.pushStack(a, arguments);
	};

	// Returns our settings object for this menu
	function getSettings(el) {
		return $(el).parents('ul.jd_menu_flag_root')[0].$settings;
	}

	// Unbind any events and then rebind them
	function addEvents(ul) {
		removeEvents(ul);
		$('> li', ul)
			.hover(hoverOverLI, hoverOutLI)
			.bind('click', itemClick)
			.find('> a.accessible')
				.bind('click', accessibleClick);
	};
	
	// Remove all events for this menu
	function removeEvents(ul) {
		$('> li', ul)
			.unbind('mouseover').unbind('mouseout')
			.unbind('click')
			.find('> a.accessible')
				.unbind('click');
	};
	
	function hoverOverLI() {
		var cls = 'jd_menu_hover' + ($(this).parent().is('.jd_menu_flag_root') ? '_menubar' : '');
		$(this).addClass(cls).find('> a').addClass(cls);
		
		if (this.$timer) {
			clearTimeout(this.$timer);
		}

		// Do we have a sub menu?
		if ($('> ul', this).size() > 0) {
			var settings = getSettings(this);
			
			// Which delay to use, the longer activate one or the shorter show delay if a menu is already visible
			var delay = ($(this).parents('ul.jd_menu_flag_root').find('ul:visible').size() == 0) 
							? settings.activateDelay : settings.showDelay;
			var t = this;
			this.$timer = setTimeout(function() {
				showMenuLI.apply(t);
			}, delay);
		}
	};
	
	function hoverOutLI() {
		// Remove both classes so we do not have to test which one we are
		$(this)	.removeClass('jd_menu_hover').removeClass('jd_menu_hover_menubar')
			.find('> a')
				.removeClass('jd_menu_hover').removeClass('jd_menu_hover_menubar');
		
		if (this.$timer) {
			clearTimeout(this.$timer);
		}

		// TODO: Possible bug with our test for visibility in that parent menus are hidden child menus are not

		// If we have a visible menu, hide it
		if ($(this).is(':visible') && $('> ul', this).size() > 0) {
			var settings = getSettings(this);
			var ul = $('> ul', this)[0];
			this.$timer = setTimeout(function() {
				hideMenuUL.apply(ul);
			}, settings.hideDelay);
		}
	};
	
	// "this" is a reference to the LI element that contains 
	// the UL that will be shown
	function showMenuLI() {
		var ul = $('> ul', this).get(0);
		// We are already visible, just return
		if ($(ul).is(':visible')) {
			return false;
		}

		// Clear our timer if it exists
		if (this.$timer) {
			clearTimeout(this.$timer);
		}

		// Get our settings object
		var settings = getSettings(this);

		// Call our callback
		if (settings.onShow != null && settings.onShow.apply(this) == false) {
			return false;
		}

		// Add hover classes, needed for accessible functionality
		var isRoot = $(this).parent().is('.jd_menu_flag_root');
		var c = 'jd_menu_active' + (isRoot ? '_menubar' : '');
		$(this).addClass(c).find('> a').addClass(c);

		if (!isRoot) {
			// Add the active class to the parent list item which maybe our menubar
			var c = 'jd_menu_active' + ($(this).parent().parent().parent().is('.jd_menu_flag_root') ? '_menubar' : '');
			$(this).parent().parent().addClass(c).find('> a').addClass(c);
		}

		// Hide any existing menues at the same level
		$(this).parent().find('> li > ul:visible').not(ul).each(function() {
			hideMenuUL.apply(this);
		});

		addEvents(ul);

		// Our range object is used in calculating menu positions
		var Range = function(x1, x2, y1, y2) {
			this.x1	= x1;
			this.x2 = x2;
			this.y1 = y1;
			this.y2 = y2;
		}
		Range.prototype.contains = function(range) {
			return 	(this.x1 <= range.x1 && range.x2 <= this.x2) 
					&& 
					(this.y1 <= range.y1 && range.y2 <= this.y2);
		}
		Range.prototype.transform = function(x, y) {
			return new Range(this.x1 + x, this.x2 + x, this.y1 + y, this.y2 + y);
		}
		Range.prototype.nudgeX = function(range) {
			if (this.x1 < range.x1) {
				return new Range(range.x1, range.x1 + (this.x2 - this.x1), this.y1, this.y2);
			} else if (this.x2 > range.x2) {
				return new Range(range.x2 - (this.x2 - this.x1), range.x2, this.y1, this.y2);
			}
			return this;
		}
		Range.prototype.nudgeY = function(range) {
			if (this.y1 < range.y1) {
				return new Range(this.x1, this.x2, range.y1, range.y1 + (this.y2 - this.y1));
			} else if (this.y2 > range.y2) {
				return new Range(this.x1, this.x2, range.y2 - (this.y2 - this.y1), range.y2);
			}
			return this;
		}

		// window width & scroll offset
		var sx = $(window).scrollLeft()
		var sy = $(window).scrollTop();
		var ww = $(window).innerWidth();
		var wh = $(window).innerHeight();

		var viewport = new Range(	sx, sx + ww, 
									sy, sy + wh);

		// "Show" our menu so we can calculate its width, set left and top so that it does not accidentally
		// go offscreen and trigger browser scroll bars
		$(ul).css({visibility: 'hidden', left: 0, top: 0}).show();

		var menuWidth		= $(ul).outerWidth();
		var menuHeight		= $(ul).outerHeight();

		// Get the LI parent UL outerwidth in case borders are applied to it
		var tp 				= $(this).parent();
		var thisWidth		= tp.outerWidth();
		var thisBorderWidth	= parseInt(tp.css('borderLeftWidth')) + parseInt(tp.css('borderRightWidth'));
		//var thisBorderTop 	= parseInt(tp.css('borderTopWidth'));
		var thisHeight		= $(this).outerHeight();
		var thisOffset 		= $(this).offset({border: false});

		$(ul).hide().css({visibility: ''});

		// We define a list of valid positions for our menu and then test against them to find one that works best
		var position = [];
	// Bottom Horizontal
		// Menu is directly below and left edges aligned to parent item
		position[0] = new Range(thisOffset.left, thisOffset.left + menuWidth, 
								thisOffset.top + thisHeight, thisOffset.top + thisHeight + menuHeight);
		// Menu is directly below and right edges aligned to parent item
		position[1] = new Range((thisOffset.left + thisWidth) - menuWidth, thisOffset.left + thisWidth,
								position[0].y1, position[0].y2);
		// Menu is "nudged" horizontally below parent item
		position[2] = position[0].nudgeX(viewport);

	// Right vertical
		// Menu is directly right and top edge aligned to parent item
		position[3] = new Range(thisOffset.left + thisWidth - thisBorderWidth, thisOffset.left + thisWidth - thisBorderWidth + menuWidth,
								thisOffset.top, thisOffset.top + menuHeight);
		// Menu is directly right and bottom edges aligned with parent item
		position[4] = new Range(position[3].x1, position[3].x2, 
								position[0].y1 - menuHeight, position[0].y1);
		// Menu is "nudged" vertically to right of parent item
		position[5] = position[3].nudgeY(viewport);

	// Top Horizontal
		// Menu is directly top and left edges aligned to parent item
		position[6] = new Range(thisOffset.left, thisOffset.left + menuWidth, 
								thisOffset.top - menuHeight, thisOffset.top);
		// Menu is directly top and right edges aligned to parent item
		position[7] = new Range((thisOffset.left + thisWidth) - menuWidth, thisOffset.left + thisWidth,
								position[6].y1, position[6].y2);
		// Menu is "nudged" horizontally to the top of parent item
		position[8] = position[6].nudgeX(viewport);
	
	// Left vertical
		// Menu is directly left and top edges aligned to parent item
		position[9] = new Range(thisOffset.left - menuWidth, thisOffset.left, 
								position[3].y1, position[3].y2);
		// Menu is directly left and bottom edges aligned to parent item
		position[10]= new Range(position[9].x1, position[9].x2, 
								position[4].y1 + thisHeight - menuHeight, position[4].y1 + thisHeight);
		// Menu is "nudged" vertically to left of parent item
		position[11]= position[10].nudgeY(viewport);

		// This defines the order in which we test our positions
		var order = [];
		if ($(this).parent().is('.jd_menu_flag_root') && !settings.isVerticalMenu) {
			order = [0, 1, 2, 6, 7, 8, 5, 11];
		} else {
			order = [3, 4, 5, 9, 10, 11, 0, 1, 2, 6, 7, 8];
		}

		// Set our default position (first position) if no others can be found
		var pos = order[0];
		for (var i = 0, j = order.length; i < j; i++) {
			// If this position for our menu is within the viewport of the browser, use this position
			if (viewport.contains(position[order[i]])) {
				pos = order[i];
				break;
			}
		}
		var menuPosition = position[pos];

		// Find if we are absolutely positioned or have an absolutely positioned parent
		$(this).add($(this).parents()).each(function() {
			if ($(this).css('position') == 'absolute') {
				var abs = $(this).offset();
				// Transform our coordinates to be relative to the absolute parent
				menuPosition = menuPosition.transform(-abs.left, -abs.top);
				return false;
			}
		});

		switch (pos) {
			case 3:
				menuPosition.y1 += settings.offsetY;
			case 4:
				menuPosition.x1 -= settings.offsetX;
				break;
			
			case 9:
				menuPosition.y1 += settings.offsetY;
			case 10:
				menuPosition.x1 += settings.offsetX;
				break;
		}

		if (settings.iframe) {
			$(ul).bgiframe();
		}

		if (settings.onAnimate) {
			$(ul).css({left: menuPosition.x1, top: menuPosition.y1});
			// The onAnimate method is expected to "show" the element it is passed
			settings.onAnimate.apply(ul, [true]);
		} else {
			$(ul).css({left: menuPosition.x1, top: menuPosition.y1}).show();
		}

		return true;
	}

	// "this" is a reference to a UL menu to be hidden
	function hideMenuUL(recurse) {
		if (!$(this).is(':visible')) {
			return false;
		}

		var settings = getSettings(this);

		// Test if this menu should get hidden
		if (settings.onHideCheck != null && settings.onHideCheck.apply(this) == false) {
			return false;
		}
		
		// Hide all of our child menus first
		$('> li ul:visible', this).each(function() {
			hideMenuUL.apply(this, [false]);
		});

		// If we are the root, do not hide ourself
		if ($(this).is('.jd_menu_flag_root')) {
			alert('We are root');
			return false;
		}

		var elms = $('> li', this).add($(this).parent());
		elms.removeClass('jd_menu_hover').removeClass('jd_menu_hover_menubar')
			.removeClass('jd_menu_active').removeClass('jd_menu_active_menubar')
			.find('> a')
				.removeClass('jd_menu_hover').removeClass('jd_menu_hover_menubar')
				.removeClass('jd_menu_active').removeClass('jd_menu_active_menubar');

		removeEvents(this);
		$(this).each(function() {
			if (settings.onAnimate != null) {
				settings.onAnimate.apply(this, [false]);
			} else {
				$(this).hide();
			}
		}).find('> .bgiframe').remove();
		// Our callback for after our menu is hidden
		if (settings.onHide != null) {
			settings.onHide.apply(this);
		}

		// Recursively hide our parent menus
		if (recurse == true) {
			$(this).parentsUntil('ul.jd_menu_flag_root')
					.removeClass('jd_menu_hover').removeClass('jd_menu_hover_menubar')
				.not('.jd_menu_flag_root').filter('ul')
					.each(function() {
						hideMenuUL.apply(this, [false]);
					});
		}

		return true;
	}

	// Prevent the default (usually following a link)
	function accessibleClick(e) {
		if ($(this).is('.accessible')) {
			// Stop the browser from the default link action allowing the 
			// click event to propagate to propagate to our LI (itemClick function)
			e.preventDefault();
		}
	}

	// Trigger a menu click
	function itemClick(e) {
		e.stopPropagation();

		var settings = getSettings(this);
		if (settings.onClick != null && settings.onClick.apply(this) == false) {
			return false;
		}

		if ($('> ul', this).size() > 0) {
			showMenuLI.apply(this);
		} else {
			if (e.target == this) {
				var link = $('> a', e.target).not('.accessible');
				if (link.size() > 0) {
					var a = link.get(0);
					if (!a.onclick) {
						window.open(a.href, a.target || '_self');
					} else {
						$(a).click();
					}
				}
			}
			
			hideMenuUL.apply($(this).parent(), [true]);
		}
	}
})(jQuery);

//modif chainselect
jQuery.fn.chainSelect_text = function( target, url, settings ) 
{ return this.each( function() {
	$(this).change( function( ) {
		settings = jQuery.extend(
		{ after : null, before : null, usePost : false, defaultValue : null, parameters : {'_id' : $(this).attr('id'), '_name' : $(this).attr('name')} } , settings);
		settings.parameters._value =  $(this).val();
		if (settings.before != null) { settings.before( target ); }
		ajaxCallback = function(data, textStatus) 
		{ $(target).html(""); data = eval(data);
			for (i = 0; i < data.length; i++) { for ( key in data[i] ) {	$(target).val(data[i][key]); } }
			if (settings.after != null) { settings.after(target); }
			$(target).change();
		};
		if (settings.usePost == true) { $.post( url, settings.parameters, ajaxCallback );} 
		else { $.get( url, settings.parameters, ajaxCallback ); } });
  });
};

jQuery.fn.chainSelect = function( target, url, settings ) 
{
  return this.each( function()
  {
	$(this).change( function( ) 
	{
		settings = jQuery.extend(
		{
			after : null,
			before : null,
			usePost : false,
			defaultValue : null,
			parameters : {'_id' : $(this).attr('id'), '_name' : $(this).attr('name')}
        } , settings);

		settings.parameters._value =  $(this).val();

		if (settings.before != null) 
		{
			settings.before( target );
		}

		ajaxCallback = function(data, textStatus) 
		{
			$(target).html("");//clear old options
			data = eval(data);//get json array
			for (i = 0; i < data.length; i++)//iterate over all options
			{
			  for ( key in data[i] )//get key => value
			  {	
					$(target).get(0).add(new Option(data[i][key],[key]), document.all ? i : null);
              }
			}

			if (settings.defaultValue != null)
			{
				$(target).val(settings.defaultValue);//select default value
			} else
			{
				$("option:first", target).attr( "selected", "selected" );//select first option
			}

			if (settings.after != null) 
			{
				settings.after(target);
			}

			$(target).change();//call next chain
		};

		if (settings.usePost == true)
		{
			$.post( url, settings.parameters, ajaxCallback );
		} else
		{
			$.get( url, settings.parameters, ajaxCallback );
		}
	});
  });
};

jQuery.download = function(url, method){
	//url and data options required
	if( url ){ 
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'"></form>')
		.appendTo('body').submit().remove();
	};
};

(function($){ 		  
	$.fn.popupWindow = function(instanceSettings){
		
		return this.each(function(){
		
		$(this).click(function(){
		
		$.fn.popupWindow.defaultSettings = {
			centerBrowser:0, // center window over browser window? {1 (YES) or 0 (NO)}. overrides top and left
			centerScreen:0, // center window over entire screen? {1 (YES) or 0 (NO)}. overrides top and left
			height:500, // sets the height in pixels of the window.
			left:0, // left position when the window appears.
			location:0, // determines whether the address bar is displayed {1 (YES) or 0 (NO)}.
			menubar:0, // determines whether the menu bar is displayed {1 (YES) or 0 (NO)}.
			resizable:0, // whether the window can be resized {1 (YES) or 0 (NO)}. Can also be overloaded using resizable.
			scrollbars:0, // determines whether scrollbars appear on the window {1 (YES) or 0 (NO)}.
			status:0, // whether a status line appears at the bottom of the window {1 (YES) or 0 (NO)}.
			width:500, // sets the width in pixels of the window.
			windowName:null, // name of window set from the name attribute of the element that invokes the click
			windowURL:null, // url used for the popup
			top:0, // top position when the window appears.
			toolbar:0 // determines whether a toolbar (includes the forward and back buttons) is displayed {1 (YES) or 0 (NO)}.
		};
		
		settings = $.extend({}, $.fn.popupWindow.defaultSettings, instanceSettings || {});
		
		var windowFeatures =    'height=' + settings.height +
								',width=' + settings.width +
								',toolbar=' + settings.toolbar +
								',scrollbars=' + settings.scrollbars +
								',status=' + settings.status + 
								',resizable=' + settings.resizable +
								',location=' + settings.location +
								',menuBar=' + settings.menubar;

				settings.windowName = this.name || settings.windowName;
				settings.windowURL = this.href || settings.windowURL;
				var centeredY,centeredX;
			
				if(settings.centerBrowser){
						
					if ($.browser.msie) {//hacked together for IE browsers
						centeredY = (window.screenTop - 120) + ((((document.documentElement.clientHeight + 120)/2) - (settings.height/2)));
						centeredX = window.screenLeft + ((((document.body.offsetWidth + 20)/2) - (settings.width/2)));
					}else{
						centeredY = window.screenY + (((window.outerHeight/2) - (settings.height/2)));
						centeredX = window.screenX + (((window.outerWidth/2) - (settings.width/2)));
					}
					window.open(settings.windowURL, settings.windowName, windowFeatures+',left=' + centeredX +',top=' + centeredY).focus();
				}else if(settings.centerScreen){
					centeredY = (screen.height - settings.height)/2;
					centeredX = (screen.width - settings.width)/2;
					window.open(settings.windowURL, settings.windowName, windowFeatures+',left=' + centeredX +',top=' + centeredY).focus();
				}else{
					window.open(settings.windowURL, settings.windowName, windowFeatures+',left=' + settings.left +',top=' + settings.top).focus();	
				}
				return false;
});
			
		});	
	};
})(jQuery);


//#######################################################################
//#######################################################################

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
 *
 * Copyright (c) 2010 C. F., Wong (<a href="http://cloudgen.w0ng.hk">Cloudgen Examplet Store</a>)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
/*
 *
 * Copyright (c) 2011 Cloudgen Wong (<a href="http://www.cloudgen.w0ng.hk">Cloudgen Wong</a>)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
// version 1.05 
// fix the problem of jQuery 1.5 when using .val() 
// fix the problem when precision has been set and the input start with decimal dot or comma ,e.g. precision set to 3 and input with ".1234"
var email={tldn:new RegExp("^[^\@]+\@[^\@]+\.(A[C-GL-OQ-UWXZ]|B[ABD-JM-OR-TVWYZ]|C[ACDF-IK-ORUVX-Z]|D[EJKMOZ]|E[CEGR-U]|F[I-KMOR]|G[ABD-IL-NP-UWY]|H[KMNRTU]|I[DEL-OQ-T]|J[EMOP]|K[EG-IMNPRWYZ]|L[A-CIKR-VY]|M[AC-EGHK-Z]|N[ACE-GILOPRUZ]|OM|P[AE-HKL-NR-TWY]|QA|R[EOSUW]|S[A-EG-ORT-VYZ]|T[CDF-HJ-PRTVWZ]|U[AGKMSYZ]|V[ACEGINU]|W[FS]|XN|Y[ETU]|Z[AMW]|AERO|ARPA|ASIA|BIZ|CAT|COM|COOP|EDU|GOV|INFO|INT|JOBS|MIL|MOBI|MUSEUM|NAME|NET|ORG|PRO|TEL|TRAVEL)$","i")};
(function($){
  $.extend($.expr[":"],{
    regex:function(d,a,c){
      var e=new RegExp(c[3],"g");
      var b=("text"===d.type)?d.value:d.innerHTML;
      return(b=="")?true:(e.exec(b))
    }
  });
  $.fn.output=function(d){
    if(typeof d=="undefined")
      return (this.is(":text"))?this.val():this.html();
    else
      return (this.is(":text"))?this.val(d):this.html(d);
  };
  formatter={
    getRegex:function(settings){
      var settings=$.extend({type:"decimal",precision:5,decimal:'.',allow_negative:true},settings);
      var result="";
      if(settings.type=="decimal"){
        var e=(settings.allow_negative)?"-?":"";
        if(settings.precision>0)
          result="^"+e+"\\d+$|^"+e+"\\d*"+settings.decimal+"\\d{1,"+settings.precision+"}$";
        else result="^"+e+"\\d+$"
      }else if(settings.type=="phone-number"){
        result="^\\d[\\d\\-]*\\d$"
      }else if(settings.type=="alphabet"){
        result="^[A-Za-z]+$"
      }
      return result
    },
    isEmail:function(d){
      var a=$(d).output();
      var c=false;
      var e=true;
      var e=new RegExp("[\s\~\!\#\$\%\^\&\*\+\=\(\)\[\]\{\}\<\>\\\/\;\:\,\?\|]+");
      if(a.match(e)!=null){
        return c
      }
      if(a.match(/((\.\.)|(\.\-)|(\.\@)|(\-\.)|(\-\-)|(\-\@)|(\@\.)|(\@\-)|(\@\@))+/)!=null){
        return c
      }
      if(a.indexOf("\'")!=-1){
        return c
      }
      if(a.indexOf("\"")!=-1){
        return c
      }
      if(email.tldn&&a.match(email.tldn)==null){
        return c
      }
      return e
    },
    formatString:function(target,settings){
      var settings=$.extend({type:"decimal",precision:5,decimal:'.',allow_negative:true},settings);
      var oldText=$(target).output();
      var newText=oldText;
      if(settings.type=="decimal"){
        if(newText!=""){
          var g;
          var h=(settings.allow_negative)?"\\-":"";
          var i="\\"+settings.decimal;
          g=new RegExp("[^\\d"+h+i+"]+","g");
          newText=newText.replace(g,"");
          var h=(settings.allow_negative)?"\\-?":"";
          if(settings.precision>0)
            g=new RegExp("^("+h+"\\d*"+i+"\\d{1,"+settings.precision+"}).*");
          else g=new RegExp("^("+h+"\\d+).*");
          newText=newText.replace(g,"$1")
        }
      }else if(settings.type=="phone-number"){
        newText=newText.replace(/[^\-\d]+/g,"").replace(/^\-+/,"").replace(/\-+/,"-")
      }else if(settings.type=="alphabet"){
        newText=newText.replace(/[^A-Za-z]+/g,"")
      }
      if(newText!=oldText)
        $(target).output(newText)
    }
  };
  $.fn.format=function(settings,wrongFormatHandler){
    var settings=$.extend({type:"decimal",precision:5,decimal:".",allow_negative:true,autofix:false},settings);
    var decimal=settings.decimal;
    wrongFormatHandler=typeof wrongFormatHandler=="function"?wrongFormatHandler:function(){};
    this.keypress(function(d){
      $(this).data("old-value",$(this).val());
      var a=d.charCode?d.charCode:d.keyCode?d.keyCode:0;
      if(a==13&&this.nodeName.toLowerCase()!="input"){return false}
      if((d.ctrlKey&&(a==97||a==65||a==120||a==88||a==99||a==67||a==122||a==90||a==118||a==86||a==45))||(a==46&&d.which!=null&&d.which==0))
        return true;
      if(a<48||a>57){
        if(settings.type=="decimal"){
          if(settings.allow_negative&&a==45&&this.value.length==0)return true;
          if(a==decimal.charCodeAt(0)){
            if(settings.precision>0&&this.value.indexOf(decimal)==-1)return true;
            else return false
          }
          if(a!=8&&a!=9&&a!=13&&a!=35&&a!=36&&a!=37&&a!=39){return false}
          return true
        }else if(settings.type=="email"){
          if(a==8||a==9||a==13||(a>34&&a<38)||a==39||a==45||a==46||(a>64&&a<91)||(a>96&&a<123)||a==95){return true}
          if(a==64&&this.value.indexOf("@")==-1)return true;
          return false
        }else if(settings.type=="phone-number"){
          if(a==45&&this.value.length==0)return false;
          if(a==8||a==9||a==13||(a>34&&a<38)||a==39||a==45){return true}
          return false
        }else if(settings.type=="alphabet"){
          if(a==8||a==9||a==13||(a>34&&a<38)||a==39||(a>64&&a<91)||(a>96&&a<123))
          return true
        }else return false
      }else{
        if(settings.type=="alphabet"){
          return false
        }else return true
      }
    })
    .blur(function(){
      if(settings.type=="email"){
        if(!formatter.isEmail(this)){
          wrongFormatHandler.apply(this)
        }
      }else{
        if(!$(this).is(":regex("+formatter.getRegex(settings)+")")){
          wrongFormatHandler.apply(this)
        }
      }
    })
    .focus(function(){
      $(this).select()
    });
    if(settings.autofix){
      this.keyup(function(d){
        if($(this).data("old-value")!=$(this).val())
          formatter.formatString(this,settings)
        }
      )
    }
    return this
  }
})(jQuery);
