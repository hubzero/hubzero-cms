/**
* @version		$Id: imgmanager.js 26 2009-05-25 10:21:53Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
var ImageManagerDialog = {
	preInit : function() {
		tinyMCEPopup.requireLangPack();
	},
	init : function() {
		var ed = tinyMCEPopup.editor, n = ed.selection.getNode(), t = this, br;
		tinyMCEPopup.resizeToInnerSize();		
		// Get src an convert to relative
		var src = ed.documentBaseURI.toRelative(ed.dom.getAttrib(n, 'src'));
		
		// Setup Manager plugin
		this.imgmanager = initManager(src);
		
		TinyMCE_Utils.fillClassList('classlist');
	
		if (n.nodeName == 'IMG') {
			dom.value('src', src);
			// Width & Height
			var w = this.getAttrib(n, 'width'), h = this.getAttrib(n, 'height');
			dom.value('width', w);
			dom.value('tmp_height',h);
			dom.value('height', h);
			dom.value('tmp_width', w);
			
			dom.value('alt', ed.dom.getAttrib(n, 'alt'));
			dom.value('title', ed.dom.getAttrib(n, 'title'));
			// Margin
			tinymce.each(['top', 'right', 'bottom', 'left'], function(o){
				dom.value('margin_' + o, ImageManagerDialog.getAttrib(n, 'margin-' + o));														  
			});	
			// Border
			dom.setSelect('border_width', this.getAttrib(n, 'border-width'), true);
			dom.setSelect('border_style', this.getAttrib(n, 'border-style'));
			dom.value('border_color', this.getAttrib(n, 'border-color'));
			dom.setSelect('align', this.getAttrib(n, 'align'));
			// Class
			dom.value('classes', ed.dom.getAttrib(n, 'class'));
			dom.setSelect('classlist', ed.dom.getAttrib(n, 'class'));
			
			dom.value('style', ed.dom.getAttrib(n, 'style'));
			dom.value('id', ed.dom.getAttrib(n, 'id'));
			dom.value('dir', ed.dom.getAttrib(n, 'dir'));
			dom.value('lang', ed.dom.getAttrib(n, 'lang'));
			dom.value('usemap', ed.dom.getAttrib(n, 'usemap'));
			
			dom.value('insert', ed.getLang('update'));
			
			// Longdesc may contain absolute url too
			dom.value('longdesc', ed.documentBaseURI.toRelative(ed.dom.getAttrib(n, 'longdesc')));
			
			// onmouseover / onmouseout
			dom.value('onmouseoutsrc', src);
			tinymce.each(['onmouseover', 'onmouseout'], function(o){
				v = ed.dom.getAttrib(n, o);
				if(/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/.test(v)){
					v = v.replace(/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/, '$1');
					v = ed.documentBaseURI.toRelative(v);
					dom.value(o + 'src', v);
				}
				dom.check('onmousemovecheck', v !== '');
				dom.disable(o + 'src', v === '');
			});
			
			br = n.nextSibling;
			if(br && br.nodeName == 'BR' && ed.dom.getStyle(br, 'clear')){
				dom.setSelect('clear', ed.dom.getStyle(br, 'clear'));
			}
		}else{
			// Setup default values
			this.setDefaults();	
		}
		dom.html('border_color_pickcontainer', TinyMCE_Utils.getColorPickerHTML('border_color'));
		TinyMCE_Utils.updateColor('border_color');
		// Setup browse button
		dom.html('longdesccontainer', TinyMCE_Utils.getBrowserHTML('longdescbrowser','longdesc','file','imgmanager'));
	
		// Setup border
		this.setBorder();
		// Setup margins
		this.setMargins(true);
		// Setup Styles
		this.updateStyles();
		TinyMCE_EditableSelects.init();
	},
	setDefaults : function(){
		var d = this.imgmanager.getParam('defaults');
		return Editor.utilities.setDefaults(d);
	},
	insert : function(){
		var ed = tinyMCEPopup.editor, t = this;
		
		AutoValidator.validate(document);
		if(dom.value('src') === ''){
			new Alert(tinyMCEPopup.getLang('imgmanager_dlg.no_src', 'Please enter a url for the image'));
			return false;		
		}
		if(dom.value('alt') === ''){
			new Confirm(tinyMCEPopup.getLang('imgmanager_dlg.missing_alt'), function(state){
					if(state){
						t.insertAndClose();	
					}
				}, {
					width: 300,
					height: 220
				}								 
			);
		}else{
			this.insertAndClose();
		}
	},
	insertAndClose : function() {
		var ed = tinyMCEPopup.editor, t = this, v, args = {}, el, br = '';
		
		this.updateStyles();

		// Fixes crash in Safari
		if (tinymce.isWebKit)
			ed.getWin().focus();

		if (!ed.settings.inline_styles) {
			args = {
				vspace : dom.value('vspace'),
				hspace : dom.value('hspace'),
				border : dom.value('border'),
				align : dom.getSelect('align')
			};
		} else {
			// Remove deprecated values
			args = {
				vspace : '',
				hspace : '',
				border : '',
				align : ''
			};
		}
		var src	= dom.value('src');
		// Add http
		if(/^\s*www./i.test(src)){
			src = 'http://' + src;	
		}

		tinymce.extend(args, {
			src : src,
			width : dom.value('width'),
			height : dom.value('height'),
			alt : dom.value('alt'),
			title : dom.value('title'),
			'class' : dom.value('classes'),
			style : dom.value('style'),
			id : dom.value('id'),
			dir : dom.getSelect('dir'),
			lang : dom.value('lang'),
			usemap : dom.value('usemap'),
			longdesc : dom.value('longdesc')
		});

		args.onmouseover = args.onmouseout = '';

		if (dom.ischecked('onmousemovecheck')){
			tinymce.each(['onmouseover', 'onmouseout'], function(o){
				v = dom.value(o + 'src');					
				if(!ed.getParam('relative_urls')){
					v 	= new tinymce.util.URI(ed.getParam('document_base_url')).toAbsolute(v);
				}
				if(v){
					args[o] = "this.src='" + v + "';";
				}
			});
		}

		el = ed.selection.getNode();
		br = el.nextSibling;
				
		if (el && el.nodeName == 'IMG') {
			ed.dom.setAttribs(el, args);
			// BR clear
			if(br && br.nodeName == 'BR'){
				if(dom.disabled('clear') || dom.getSelect('clear') === ''){
					ed.dom.remove(br);	
				}
				if(!dom.disabled('clear') && dom.getSelect('clear') !== ''){
					ed.dom.setStyle(br, 'clear', dom.getSelect('clear'));
				}
			}else{
				if(!dom.disabled('clear') && dom.getSelect('clear') !== ''){
					br = ed.dom.create('br');
					ed.dom.setStyle(br, 'clear', dom.getSelect('clear'));
					ed.dom.insertAfter(br, el);
				}
			}
		} else {
			ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" src="javascript:;" />', {skip_undo : 1});
			el = ed.dom.get('__mce_tmp');
			if(!dom.disabled('clear') && dom.getSelect('clear') !== ''){
				br = ed.dom.create('br');
				ed.dom.setStyle(br, 'clear', dom.getSelect('clear'));
				ed.dom.insertAfter(br, el);
			}			
			ed.dom.setAttribs('__mce_tmp', args);
			ed.dom.setAttrib('__mce_tmp', 'id', '');
			ed.undoManager.add();
		}

		tinyMCEPopup.close();
	},
	getAttrib : function(e, at) {
		var ed = tinyMCEPopup.editor, v, v2;
		switch (at) {
			case 'width':
			case 'height':
				return ed.dom.getAttrib(e, at) || ed.dom.getStyle(e, at) || '';
				break;	
			case 'align':
				if(v = ed.dom.getAttrib(e, 'align')){
					return v;	
				}
				if(v = ed.dom.getStyle(e, 'float')){
					return v;
				}
				if(v = ed.dom.getStyle(e, 'vertical-align')){
					return v;
				}
				break;
			case 'margin-top':
			case 'margin-bottom':
				if(v = ed.dom.getStyle(e, at)){
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				if(v = ed.dom.getAttrib(e, 'vspace')){
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				break;
			case 'margin-left':
			case 'margin-right':
				if(v = ed.dom.getStyle(e, at)){
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				if(v = ed.dom.getAttrib(e, 'hspace')){
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				break;
			case 'border-width':
			case 'border-style':
			case 'border-color':
				v = '';
				tinymce.each(['top', 'right', 'bottom', 'left'], function(n) {
					s = at.replace(/-/, '-' + n + '-');
					sv = ed.dom.getStyle(e, s);
					// False or not the same as prev
					if(sv !== '' || (sv != v && v !== '')){
						v = '';
					}
					if (sv){
						v = sv;
					}
				});
				if(at == 'border-color'){
					v = string.toHex(v);	
				}
				if(at == 'border-width' && v !== ''){
					dom.check('border', true);
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				return v;
				break;
		}
	},
	setSwapImage : function() {
		var st = dom.ischecked('onmousemovecheck');
		
		dom.disable('onmouseoversrc', !st);
		dom.disable('onmouseoutsrc', !st);
	},
	setMargins : function(init){
		var x = false;
		if(init){
			tinymce.each(['right', 'bottom', 'left'], function(e){
				x = (dom.value('margin_' + e) == dom.value('margin_top'));
				dom.disable('margin_' + e, x);
			});
			dom.check('margin_check', x);
		}else{
			x = dom.ischecked('margin_check');		
			tinymce.each(['right', 'bottom', 'left'], function(e){
				if(x){
					dom.value('margin_' + e, dom.value('margin_top'));
				}
				dom.disable('margin_' + e, x);
			});
			this.updateStyles();
		}
	},
	setBorder : function(){
		if(dom.ischecked('border')){
			dom.disable('border_width', false); 
			dom.disable('border_style', false);
			dom.disable('border_color', false);
		}else{
			dom.disable('border_width', true); 
			dom.disable('border_style', true);
			dom.disable('border_color', true);
		}
		this.updateStyles();
	},
	setClasses : function(v){
		return Editor.utilities.setClasses(v);
	},
	setDimensions : function(a, b){
		return Editor.utilities.setDimensions(a, b);
	},
	setStyles : function(){
		var ed = tinyMCEPopup, img = dom.get('sample');
		ed.dom.setAttrib(img, 'style', dom.value('style'));
		
		// Margin
		tinymce.each(['top', 'right', 'bottom', 'left'], function(o){
			dom.value('margin_' + o, ImageManagerDialog.getAttrib(img, 'margin-' + o));														  
		});													  
		// Border
		if(this.getAttrib(img, 'border-width') !== ''){
			dom.check('border', true);
			this.setBorder();
			dom.setSelect('border_width', this.getAttrib(img, 'border-width'));
			dom.setSelect('border_style', this.getAttrib(img, 'border-style'));
			dom.value('border_color', this.getAttrib(img, 'border-color'));
		}
		// Align
		dom.setSelect('align', this.getAttrib(img, 'align'));
	},
	updateStyles : function() {
		var ed = tinyMCEPopup, st, v, br, img = dom.get('sample');
		ed.dom.setAttrib(img, 'style', dom.value('style'));
		
		ed.dom.setAttrib(img, 'dir', dom.value('dir'));
		
		// Handle align
		ed.dom.setStyle(img, 'float', '');
		ed.dom.setStyle(img, 'vertical-align', '');

		v = dom.getSelect('align');
		if (v == 'left' || v == 'right'){
			dom.disable('clear', false);
			dom.removeClass('clearlabel', 'disabled');					
			ed.dom.setStyle(img, 'float', v);
		}else{
			img.style.verticalAlign = v;
			dom.disable('clear', true);
			dom.addClass('clearlabel', 'disabled');	
		}
		// Handle clear
		v = dom.getSelect('clear');
		if (v && !dom.disabled('clear')) {
			br = dom.get('sample-br');
			if(!br){
				br = ed.dom.create('br', {'id': 'sample-br'});
				ed.dom.insertAfter(br, img);
			}
			ed.dom.setStyle(br, 'clear', v);
		}else{
			if(dom.get('sample-br')){
				ed.dom.remove('sample-br');
			}
		}
		// Handle border	
		tinymce.each(['width', 'color', 'style'], function(o){
			if(dom.ischecked('border')){
				v = dom.value('border_' + o);
			}else{
				v = '';	
			}
			ed.dom.setStyle(img, 'border-' + o, v);
		});
		// Margin
		tinymce.each(['top', 'right', 'bottom', 'left'], function(o){
			v = dom.value('margin_' + o);
			ed.dom.setStyle(img, 'margin-' + o,  /[^a-z]/i.test(v) ? v + 'px' : v);
		});
		// Merge
		ed.dom.get('style').value = ed.dom.serializeStyle(ed.dom.parseStyle(img.style.cssText));
	}
}
var ImageManager = Manager.extend({
	otherOptions : function(){
		return {
			onFileClick : function(file){
				this.selectFile(file);
			},
			onFileInsert : function(file){
				this.selectFile(file);	
			}.bind(this)
		};
	},
	initialize : function(src, options){
		this.setOptions(this.otherOptions(), options);
		this.parent('imgmanager', src, '', this.options);
	},
	selectFile : function(title){
		var name 	= string.basename(title);		
		var url		= string.path(this.getDir(), encodeURIComponent(name));
		var src 	= string.path(this.getParam('base'), url);
		src			= src.charAt(0) == '/' ? src.substring(1) : src;
			
		if(dom.hasClass('swap_panel', 'current') && dom.ischecked('onmousemovecheck') ){
			if(dom.value('onmouseoutsrc') == ''){
				dom.value('onmouseoutsrc', src);
			}else{
				dom.value('onmouseoversrc', src);
			}
		}else{		
			dom.disable('insert', true);
			dom.value('alt', string.stripExt(name));
			dom.value('onmouseoutsrc', src);
			dom.value('src', src);
					
			$('dim_loader').addClass('loader');
			this.xhr('getDimensions', [url], function(o){
				if(!o.error){
					dom.value('width', o.width);
					dom.value('tmp_width', o.width);
					dom.value('height', o.height);
					dom.value('tmp_height', o.height);
				}
				$('dim_loader').removeClass('loader');
				dom.disable('insert', false);
				ImageManagerDialog.updateStyles();										
			});
		}
	}
});
ImageManager.implement(new Events, new Options);
ImageManagerDialog.preInit();
tinyMCEPopup.onInit.add(ImageManagerDialog.init, ImageManagerDialog);