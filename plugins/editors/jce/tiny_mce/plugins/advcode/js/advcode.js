/**
* @version		$Id: advcode.js 149 2009-07-11 13:15:16Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

tinyMCEPopup.requireLangPack();
var SourceEditor = {
	saveContent : function() {
		var content;
		if(this.textarea.disabled){
			content = this.editor.getCode();	
		}else{
			content = this.textarea.value;
		}
		tinyMCEPopup.editor.setContent(content);
		tinyMCEPopup.close();
	},
	init : function() {
		var ed = tinyMCEPopup.editor, d = document, t = this, s, o = '', v, k, f = {
			p : 'advanced.paragraph',
			address : 'advanced.address',
			pre : 'advanced.pre',
			h1 : 'advanced.h1',
			h2 : 'advanced.h2',
			h3 : 'advanced.h3',
			h4 : 'advanced.h4',
			h5 : 'advanced.h5',
			h6 : 'advanced.h6',
			div : 'advanced.div',
			blockquote : 'advanced.blockquote',
			code : 'advanced.code',
			dt : 'advanced.dt',
			dd : 'advanced.dd',
			samp : 'advanced.samp'
		};
		this.textarea = d.getElementById('htmlSource');
				
		// Remove Gecko spellchecking
		if (tinymce.isGecko){
			d.body.spellcheck = tinyMCEPopup.editor.getParam("gecko_spellcheck");
			if(this.textarea.value == '') {
				this.textarea.value == '&nbsp;';
			}
		}
		
		tinymce.each(['strong', 'em', 'underline', 'removeformat', 'undo', 'redo'], function(s){
			o += '<a href="javascript:;" onclick="SourceEditor.toggleCommand(\''+ s +'\');" title="'+ ed.getLang('advcode_dlg.' + s) +'"><span class="button '+ s +'"></span></a>';																					 
		});
		var blocks = ed.getParam('theme_advanced_blockformats', ed.settings.theme_advanced_blockformats).split(',');
		if(blocks.length){
			o += '<select onchange="SourceEditor.toggleCommand(this.value);">';
			o += '<option value="">' + ed.getLang('advcode_dlg.format') + '</option>';
			tinymce.each(blocks, function(v, k){
				o += '<option value="'+ v +'">' + ed.getLang(f[v]) + '</option>';					  
			});
			o += '</select>';
		}
		d.getElementById('commands').innerHTML = o;
		this.textarea.value = tinyMCEPopup.editor.getContent();
		this.textarea.disabled = true;
		
		this.initEditor();
	},
	initEditor : function(){
		var dom = tinyMCEPopup.dom, t = this;
		
		var w = 'auto';
		var h = tinymce.isWebKit || tinymce.isIE ? '90%' : '95%';
		
		this.editor = CodeMirror.fromTextArea('htmlSource', {
			width: w,
			height: h,
			parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"],
			stylesheet: ["css/xmlcolors.css", "css/jscolors.css", "css/phpcolors.css", "css/csscolors.css"],
			path: "js/",
			lineNumbers : true,
			textWrapping : true,
			indentUnit : 4,
			reindentOnLoad : true,
			//dumbTabs: true,
			initCallback : function(){
				// Iframe
				t.iframe 	= t.editor.frame;
				// Body
				t.body 		= t.iframe.contentWindow.document.body;
				
				var fs = tinymce.util.Cookie.get('jce_advcode_fontsize') || 11;
				fs = /[0-9]/g.test(parseInt(fs)) ? fs : 11;
		
				t.body.style.fontSize = fs + 'px';
				
				dom.setStyles(t.iframe, {
					'border-width': 1,
					'border-style': 'solid',
					'border-color': '#dddddd'
				});
				t.onInit();
			}
		 });
	},
	onInit : function(){
		this.toggleEditor(tinymce.util.Cookie.get('jce_advcode_highlight'), true);
		this.toggleLineNumbers(tinymce.util.Cookie.get('jce_advcode_linenumbers'));
		this.toggleWordWrap(tinymce.util.Cookie.get('jce_advcode_wordwrap'));
	},
	toggleClass : function(e, c){
		var dom = tinyMCEPopup.dom;
		if(dom.hasClass(e, c)){
			dom.removeClass(e, c);	
		}else{
			dom.addClass(e, c);	
		}
	},
	getBool : function(v){
		if(typeof(v) == 'string') v = parseInt(v);
		if(v == 'undefined' || v == 'NaN' || v == null) v = true;
		
		return v;
	},
	toggleEditor : function(v, init){
		var d = document;
		v = this.getBool(v);

		d.getElementById('highlight').checked = v;
		
		var fs = tinymce.util.Cookie.get('jce_advcode_fontsize') || 11;
		fs = /[0-9]/g.test(parseInt(fs)) ? fs : 11;
		
		if(tinymce.isGecko && this.textarea.value == '') {
			this.textarea.value == '&nbsp;';
		}
		
		if(v){
			this.textarea.style.display = 'none';
			this.textarea.disabled = true;
			this.editor.setCode(this.textarea.value);
			this.iframe.parentNode.style.visibility = 'visible';

			this.body.style.fontSize = fs + 'px';
			d.getElementById('numbers').disabled = false;
			d.getElementById('commands').style.display = 'block';
			
			this.editor.focus();
			this.editor.reindent();
			
		}else{
			this.textarea.value = this.editor.getCode();
			this.textarea.disabled = false;
			this.iframe.parentNode.style.visibility = 'hidden';
			this.textarea.style.display = '';		
			
			this.textarea.style.fontSize = fs + 'px';
			d.getElementById('numbers').disabled = true;
			d.getElementById('commands').style.display = 'none';
		}
		if(!init){
			this.toggleWordWrap(document.getElementById('wraped').checked);
		}
		tinymce.util.Cookie.set('jce_advcode_highlight', v ? 1 : 0);
	},
	toggleFontSize : function(v){
		var editor, size;
		if(this.textarea.disabled){
			editor 	= this.editor.frame.contentWindow.document.body;
			size 	= parseInt(editor.style.fontSize);
		}else{
			editor	= this.textarea;
			size 	= parseInt(this.textarea.style.fontSize);
		}
		if(v == '+'){
			size++;
		}else{
			size--;
		}
		tinymce.util.Cookie.set('jce_advcode_fontsize', size);
		editor.style.fontSize = size + 'px';
	},
	toggleLineNumbers : function(v){
		v = this.getBool(v);
		
		document.getElementById('numbers').checked = v;	
		tinyMCEPopup.dom.setStyle(tinyMCEPopup.dom.select('.CodeMirror-line-numbers'), 'display',  v ? '' : 'none' );
		tinymce.util.Cookie.set('jce_advcode_linenumbers', v ? 1 : 0);
	},
	toggleWordWrap : function(v) {
		v = this.getBool(v);
		
		document.getElementById('wraped').checked = v;
		this.setWrap(v);
		tinymce.util.Cookie.set('jce_advcode_wordwrap', v ? 1 : 0);
	},
	setWrap : function(check) {
		var v, w, n, s = this.textarea;
		if(s.disabled){
			if(check){
				tinyMCEPopup.dom.addClass(this.body, 'wrap');
			}else{
				tinyMCEPopup.dom.removeClass(this.body, 'wrap');
			}
		}else{
			w = check ? 'soft' : 'off';
			s.wrap = w;
			if (!tinymce.isIE) {
				v = s.value;
				n = s.cloneNode(false);
				n.setAttribute("wrap", w);
				s.parentNode.replaceChild(n, s);
				n.value = v;
				this.textarea = n;
			}
		}
	},
	toggleCommand : function(c){
		var ed = this.editor, s = ed.selection();	
				
		if(c == '' || s == '') return false;		
		switch(c){
			default:
				s = '<'+ c +'>' + s + '</'+ c +'>';
				break;
			case 'undo':
			case 'redo':
				return ed[c]();
				break;
			case 'removeformat':
				s = s.replace(/<\/?[^>]*>/gi, '');	
				break;
			case 'underline':
				s = '<span style="text-decoration:underline;">' + s + '</span>';
				break;
		}
		return ed.replaceSelection(s);
	}
}
tinyMCEPopup.onInit.add(SourceEditor.init, SourceEditor);