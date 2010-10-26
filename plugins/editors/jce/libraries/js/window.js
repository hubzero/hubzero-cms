/**
* @version		$Id: window.js 145 2009-07-01 11:07:51Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

//Requires mootools.js
var Dialog = new Class({
	getOptions : function(){
		return {			
			width: 250,
			height: 250,
			parent: 'body',
			onOpen: Class.empty,
			onClose: Class.empty,
			onDragStop : Class.empty,
			buttons: [{
				'text': tinyMCEPopup.getLang('dlg.cancel', 'Cancel'),
				'class': 'mceButton mceCancel',
				'click': function(){
					this.close();
				}.bind(this)
			}],
			features: {
				movable: true,
				resizable: true,
				minimize: false,
				modal: true,
				focus: true
			}
		};
	},
	initialize : function(type, title, body, options){
		this.setOptions(this.getOptions(), options);
		if (this.options.initialize) {
			this.options.initialize.call(this);
		}
		this.type 	= type;
		this.title 	= title;
		this.body 	= body;
		this.skin 	= tinyMCEPopup.editor.settings.inlinepopups_skin;
		this.id 	= 'inline-'+ this.type;					
		if($(this.id)) return;
		this.openWin();	
	},
	setContent : function(){
		var c = $(this.id + '-content-body');
		if($type(this.body) == 'string'){
			c.setHTML(this.body);
		}else{
			c.adopt(this.body);	
		}
	},
	openWin : function(){		
		this.features = ['mce' + this.type.capitalize()];
		for(n in this.options.features){
			if(this.options.features[n]){
				this.features.push('mce' + n.capitalize());	
			}
		}	
		
		this.frame = new Element('div', {
			id: 'inline-frame',
			'class': 'frame',
			styles: {
				position: 'absolute',
				left: 0,
				top: 0,
				width: '100%',
				height: '100%',
				'z-index': 2000
			}
		}).injectInside($E(this.options.parent));
			
		this.dialog = new Element('div', {
			'class': this.skin,
			'id': this.id,
			'styles': {
				'width': this.options.width.toInt() + 2, 
				'height': this.options.height.toInt(),
				'z-index': 3000
			}
		}).injectInside(this.frame);
		
		this.wrapper = new Element('div', {'class': 'mceWrapper ' + this.features.join(' ')}).adopt(
			new Element	('div').addClass('mceTop').adopt([
				new Element('div').addClass('mceLeft')	,																			 
				new Element('div').addClass('mceCenter'),
				new Element('div').addClass('mceRight'),
				new Element('span').setHTML(this.title)																			 
			])
		).adopt(
			new Element	('div').addClass('mceMiddle').adopt([
				new Element('div').addClass('mceLeft'),																				 
				new Element('span', {'id': this.id + '-content'}).adopt(
					new Element('div', {'id': this.id + '-content-body'}).addClass('mceContentBody')
				),
				new Element('div').addClass('mceRight'),
				new Element('div').addClass('mceIcon')
			])
		).adopt(
			new Element	('div').addClass('mceBottom').adopt([
				new Element('div').addClass('mceLeft'), 																				 
				new Element('div').addClass('mceCenter'),
				new Element('div').addClass('mceRight')
			])
		).adopt([
			new Element('a', {
				'href': 'javascript:;',
				'class': 'mceMove', 
				'events': {
					'mousedown': function(e){
						this.startMove(e);
					}.bind(this)
				}
			}),
			new Element('a', {
				'href': 'javascript:;',
				'class': 'mceClose', 
				'events': {
					'click': function(e){
						this.close();
					}.bind(this)
				}
			})
		]).adopt(this.getResizeHandles()).injectInside(this.dialog);
				
		this.setContent();
		this.addButtons();
			
		if(window.ie6){ 
			var w = this.windowSize();
			this.blocker = new Element('iframe', {
				'src': 'about:blank', 
				'frameborder': '0', 
				'scrolling': 'no',
				'class': 'blocker',
				styles : {
					width: w.x,
					height: w.y
				}
			}).injectInside(document.body);
		}
		if($(this.wrapper).hasClass('mceModal')){
			if(!$('inline-overlay')){
				this.overlay = new Element('div', {
					id: 'inline-overlay',
					'class': 'overlay',
					styles: {
						position: 'absolute',
						left: 0,
						top: 0,
						width: '100%',
						height: '100%',
						'background-color': '#ffffff',
						'z-index': 1000
					}
				}).setOpacity(0.5).injectInside(document.body);	
			}
		}
		this.content = $(this.id + '-content');
		this.fireEvent('onOpen');
		var h = parseInt($(this.id + '-content-body').offsetHeight) + 70;

		this.setHeight(h < this.options.height ? this.options.height : h);
		this.centerWindow();
	},
	getResizeHandles : function(){
		var points = ['S', 'E', 'SE'];
		var links = [];
		points.each(function(el){
			links.push(
				new Element('a', {
					'href': 'javascript:;',
					'class': 'mceResize mceResize' + el,
					'events': {
						'mousedown': function(e){
							this.startResize(e, el);
						}.bind(this)
					}
				})
			)
		}.bind(this))
		return links;
	},
	addButtons : function(){
		if(this.options.buttons.length > 0){
			this.options.buttons.each(function(b){
				$(this.wrapper).adopt(
					new Element('a', {
						'href': 'javascript:;',
						'class': 'mceButton',
						'events': {
							'click': function(args){
								b['click'].pass(args, this)();
							}
						}
					}).addClass(b['class'] || '').setHTML(b.text)						  
				)								   
			}.bind(this));
		}
	},
	windowSize : function(){
		return {
			x : window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth || 0,
			y : window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight || 0
		}
	},
	centerWindow : function(){
		var s 	= $(this.dialog).getSize().size;
		var w	= this.windowSize();

		var x = w.x / 2 - s.x / 2;
		var y = w.y / 2 - s.y / 2;
		
		$(this.dialog).setStyles({'left': x + 'px', 'top': y + 'px'});	
	},
	setWidth: function(w){
		if(w.toInt() < 250) w = 250;
		this.options.width = w.toInt();
		this.dialog.setStyle('width', w.toInt() + 'px');
	},
	setHeight: function(h){
		//if(h.toInt() < 250) h = 250;
		this.options.height = h.toInt();
		this.dialog.setStyle('height', this.options.height + 'px');
	},
	close : function() {			
		this.fireEvent('onClose');
		if(this.content){
			//this.content.setHTML('').remove();
			this.content.remove();
		}
		if(this.dialog){
			this.dialog.setHTML('');
		}
		if(window.ie6){
			this.blocker.remove();
		}
		if(this.frame){
			this.frame.remove();
		}
		if(this.overlay){
			this.overlay.remove();
		}
		this.type 	= null;
		this.id 	= null;
	},
	startResize : function(e, dir){
		e = new Event(e).stop();
		var d	= $(this.dialog);
		var s 	= d.getSize();
		var ph = new Element('div', {
			'class': 'mcePlaceHolder', 
			'styles': {
				width: s.size.x,
				height: s.size.y
			}
		}).injectInside(d);
		ph.makeResizable({
			limit: {
				x: [this.options.width, this.options.width * 2], 
				y: [this.options.height, this.options.height * 2]
			},
			modifiers: {
				x: dir.test(/e|se/i) ? 'width' : false, 
				y: dir.test(/s|se/i) ? 'height' : false
			},
			onComplete: function(){
				var s 	= $(ph).getSize();
				d.setStyles({
					width: s.size.x,
					height: s.size.y
				})
				$(ph).remove();
			}	
		}).start(e);
	},
	startMove : function(e){
		e = new Event(e).stop();
		var d	= $(this.dialog);
		var s 	= d.getSize();
		var ph = new Element('div', {
			'class': 'mcePlaceHolder', 
			'styles': {
				width: s.size.x,
				height: s.size.y
			}
		}).injectInside(d);
		ph.makeDraggable({
			onComplete: function(){
				d.setStyles({
					top: $(ph).getTop(),
					left: $(ph).getLeft()
				})
				$(ph).remove();
				this.fireEvent('onDragStop');
			}.bind(this)	
		}).start(e);
	}
});
Dialog.implement(new Options, new Events);

var Alert = Dialog.extend({
    getExtended : function(){
		return {
			width: 300,
			height: 150,
			buttons: [{
				'text': tinyMCEPopup.getLang('dlg.ok', 'OK'),
				'class': 'mceOk',
				'click': function(){
					this.close();
				}.bind(this)
			}]
		}
	},
	initialize: function(html, options){
        this.setOptions(this.getExtended(), options);
		this.parent('alert', tinyMCEPopup.getLang('dlg.alert', 'Alert'), html, this.options);
    }
});
var Confirm = Dialog.extend({
    getExtended : function(){
		return {
			onConfirm: Class.empty,
			width: 270,
			height: 150,
			buttons: [{
				'text': tinyMCEPopup.getLang('dlg.yes', 'Yes'),
				'class': 'mceOk',
				'click': function(){
					this.fireEvent('onConfirm', [true]);
					this.close();
				}.bind(this)
			},{
				'text': tinyMCEPopup.getLang('dlg.no', 'No'),
				'class': 'mceCancel',
				'click': function(){
					this.fireEvent('onConfirm', [false]);
					this.close();
				}.bind(this)
			}]
		}
	},
	initialize: function(html, onConfirm, options){
        if(!options) options = {};
		options.onConfirm = onConfirm;

		this.setOptions(this.getExtended(), options);
		
		this.parent('confirm', tinyMCEPopup.getLang('dlg.confirm', 'Confirm'), html, this.options);
    }
});
var Prompt = Dialog.extend({
	getExtended : function(){
		return {
			onConfirm : Class.empty,
			text : '',
			id : 'prompt',
			name : '',
			value : '',
			multiline : false,
			elements: '',
			width: 250,
			height: 120,
			buttons: [{
				'text': tinyMCEPopup.getLang('dlg.ok', 'OK'),
				'class': 'mceOk',
				'click': function(){
					this.fireEvent('onConfirm');
				}.bind(this)
			},{
				'text': tinyMCEPopup.getLang('dlg.cancel', 'Cancel'),
				'class': 'mceCancel',
				'click': function(){
					this.close();
				}.bind(this)
			}]
		};
	},
	initialize: function(title, options){
        this.setOptions(this.getExtended(), options);
		this.html = new Element('div', {'class': 'formElm'});
		if(this.options.text){
			this.html.adopt(
				new Element('label', {
					'for': this.options.id
				}).setHTML(this.options.text)
			)
		}
		if(this.options.multiline){
			this.html.adopt(
				new Element('textarea', {
					'id': this.options.id, 
					styles: {
						width: '200px',
						height: '75px'
					}
				}).setHTML(this.options.value)
			)
		}else{
			this.html.adopt(
				new Element('input', {
					id: this.options.id,
					name: this.options.name,
					type: 'text',
					value: this.options.value
				})
			)
		}
		this.parent('prompt', title, [this.html, this.options.elements], this.options);
    }					   
});
var basicDialog = Dialog.extend({
	initialize: function(title, body, options){
		this.parent('dialog', title, body, options);
    }							
});
var uploadDialog = Dialog.extend({
	getExtended : function(){
		return {
			parent: 'form',
			width: 350,
			height: 300,
			buttons: [{
				'text': tinyMCEPopup.getLang('dlg.close', 'Close'),
				'class': 'mceCancel',
				'click': function(){
					this.close();
				}.bind(this)
			}],
			extended: {
				body: null
			},
			onUpload : Class.empty
		};
	},
	initialize: function(options){
		this.setOptions(this.getExtended(), options);
					
		var body = new Element('div', {
			id: 'upload-body'
		}).adopt(
			new Element('fieldset').adopt(
				new Element('legend').setHTML(tinyMCEPopup.getLang('dlg.browse', 'Browse'))
			).adopt(
				new Element('input', {
					type: 'hidden',
					id: 'upload-dir',
					name: 'upload-dir'
				})
			).adopt(
				new Element('input', {
					'type': 'file', 
					'name': 'Filedata',
					'size': 40,
					styles: {
						position: 'relative' 
					}
				})
			)
		).adopt(
			new Element('fieldset').adopt(
				new Element('legend').setHTML(tinyMCEPopup.getLang('dlg.options', 'Options'))							  
			).adopt(
				new Element('div', {
					id: 'upload-options',
					styles: {
						position: 'relative'
					}		
				}).adopt(
					new Element('div').adopt(
						new Element('label').setHTML(tinyMCEPopup.getLang('dlg.upload_exists', 'Action if file exists:'))
					).adopt(
						new Element('select', {
							id: 'upload-overwrite',
							name: 'upload-overwrite'
						})
					)
				).adopt(this.options.extended.body || '')
			)
		).adopt(
			new Element('fieldset').adopt(
				new Element('legend').setHTML(tinyMCEPopup.getLang('dlg.queue', 'Queue'))							  
			).adopt(
				new Element('div', {
					id: 'upload-queue-block',
					styles: {
						position: 'relative'
					}		
				}).adopt(
					new Element('ul', {id: 'upload-queue'}).adopt(
						new Element('li', {
							styles: {'display': 'none'}
						})
					)
				)
			)
		)
		this.parent('dialog', tinyMCEPopup.getLang('dlg.upload', 'Upload'), body, this.options);
		$(this.wrapper).adopt(
			new Element('input', {
				styles: {
					position: 'absolute'
				},
				'id': 'upload-submit',
				'type': 'button',
				'class': 'mceButton mceOk',
				value : tinyMCEPopup.getLang('dlg.upload', 'Upload'),
				events : {
					'click': function(){
						this.fireEvent('onUpload');	
					}.bind(this)
				}
			})
		)
    }
});
var iframeDialog = basicDialog.extend({
	moreOptions : function(){
		return {
			buttons: [],
			modal: true,
			width: 480,
			height: 320,
			onOpen: function(){
				this.displayFrame();	
			}.bind(this),
			onFrameLoad: Class.empty
		};
	},
	initialize: function(title, url, options){
		this.setOptions(this.moreOptions(), options);
		this.url 	= url;
		this.title 	= title;
		this.parent(title, '', this.options);
    },
	displayFrame: function(){
		$(this.id + '-content-body').setStyle('height', '100%');
		
		this.display = new Element('div').addClass('iframe-preview').adopt(
			new Element('iframe', {
				'src': this.url,
				'scrolling': 'auto',
				'frameborder': '0',
				'styles': {
					'width': '99%',
					'height': '95%'
				},
				'events': {
					'load': function(){
						this.fireEvent('onFrameLoad');							
					}.bind(this)
				}
			})
		).injectInside($(this.id + '-content-body'))
	}
});
var mediaPreview = basicDialog.extend({
    moreOptions : function(){
        return {
            buttons: [],
			modal: true,
			width: 640,
			height: 480,
			vars : {},
			onOpen: function(){
				this.displayMedia();	
			}.bind(this)
        };
    },
    initialize: function(title, url, options){		
		this.setOptions (this.moreOptions(), options);
		this.url 	= url;
		this.title 	= title;
		this.parent(title, '', this.options);
    },
	displayMedia : function(){	
		// Image
		if(/\.(jpg|jpeg|gif|png)/i.test(this.url)){
			$(this.id + '-content-body').setStyle('height', '100%');
			
			this.display = new Element('div').addClass('image-preview').addClass('loader').injectInside($(this.id + '-content-body'));
			this.img = new Asset.image(this.url, {
				onload : function(){
					if(this.loaded) return false;
					this.loaded = true;
					this.options.width 	= this.img.width;
					this.options.height = this.img.height;
					this.setDimensions();
					this.img.width 	= this.mediaWidth;
					this.img.height = this.mediaHeight;
					this.display.removeClass('loader');
					this.display.adopt(this.img).addEvent('click', function(){
						this.close();
					}.bind(this));
				}.bind(this),
				title : this.title
			})
		}else{
			var p, classid, codebase, mediatype;
			type = this.getType(this.url);
			this.display = new Element('div').addClass('media-preview').addClass('loader').setStyle('margin', 0).injectInside($(this.id + '-content-body'));
			this.setDimensions();
			
			$(this.id + '-content-body').setStyle('height', this.mediaHeight);
		
			var html = '';	
			switch (type) {
				case 'director':
				case 'application/x-director':
					ci = '166b1bca-3f9c-11cf-8075-444553540000';
					cb = 'http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,1,0';
					mt = 'application/x-director';
					break;
				case 'windowsmedia':
				case 'mplayer':
					ci = '6bf52a52-394a-11d3-b153-00c04f79faa6';
					cb = 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701';
					mt = 'application/x-mplayer2';
					break;
				case 'quicktime':
				case 'video/quicktime':
					ci = '02bf25d5-8c17-4b23-bc80-d3488abddc6b';
					cb = 'http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0';
					mt = 'video/quicktime';
					break;
				case 'real':
				case 'realaudio':
				case 'audio/x-pn-realaudio-plugin':
					ci = 'cfcdaa03-8be4-11cf-b84b-0020afbbccfa';
					cb = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0';
					mt = 'audio/x-pn-realaudio-plugin';
					break;
				case 'divx':
				case 'video/divx':
					ci = '67dabfbf-d0ab-41fa-9c46-cc0f21721616';
					cb = 'http://go.divx.com/plugin/DivXBrowserPlugin.cab';
					mt = 'video/divx';
					break;
				default:
				case 'flash':
				case 'application/x-shockwave-flash':
					ci = 'd27cdb6e-ae6d-11cf-96b8-444553540000';
					cb = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0';
					mt = 'application/x-shockwave-flash';
					break;
			}
			p = {
				width: this.mediaWidth,
				height: this.mediaHeight,
				src: this.url
			}
			$extend(p, this.options.vars);
			
			if(type == 'flash'){
				p.wmode = 'opaque';
			}
			if(/(mplayer|windowsmedia)/i.test(type)){
				if(window.ie){
					p.url = p.src;
					delete p.src;
				}
			}			
			
			html = '<object id="media-preview" ';
			if(/flash/i.test(type)){
				html += 'type="'+ mt +'" data="'+ p.src +'" ';	
			}else{
				html += 'codebase="' + cb + '" classid="clsid:' + ci + '" ';
			}
			for (n in p){
				if(p[n] !== ''){
					if (/(id|name|width|height|style)$/.test(n)){
						html += n + '="' + p[n] + '"';	
					}
				}
			}
			html += '>';
			for (n in p){
				if(p[n] !== ''){
					if (!/(id|name|width|height|style)$/.test(n)){
						html += '<param name="' + n + '" value="' + p[n] + '">';
					}
				}
			}
			if(!window.ie && !/flash/i.test(type)){
				html += '<object type="'+ mt +'" data="'+ p.src +'" ';
				for (n in p){
					if(p[n] !== ''){
						html += n + '="' + p[n] + '"';
					}
				}
				html += '></object>';	
			}
			html += '</object>';

			this.display.setHTML(html);
			this.display.focus();
		}
	},
	getType : function(v){
		var fo = tinyMCEPopup.editor.getParam("media_types", "windowsmedia=avi,wmv,wm,asf,asx,wmx,wvx;quicktime=mov,qt,mpg,mp3,mp4,mpeg;flash=swf,flv,xml;shockwave=dcr;real=rm,ra,ram;divx=divx").split(';'), i, c, el, x;
		for (i=0; i<fo.length; i++) {
			c = fo[i].split('=');
	
			el = c[1].split(',');
			for (x=0; x<el.length; x++){
				if (v.indexOf('.' + el[x]) != -1){
					return c[0];
				}
			}
		}
		return null;
	},
	setDimensions: function(){
		var x = Math.round(window.getWidth()) - 100;
	   	var y = Math.round(window.getHeight()) - 100;
		
		var w = this.options.width.toInt();
		var h = this.options.height.toInt();
		
		if(w > x){
			h = h * (x / w); 
			w = x; 
			if(h > y){ 
				w = w * (y / h); 
				h = y; 
			}
		}else if(h > y){ 
			w = w * (y / h); 
			h = y; 
			if(w > x){ 
				h = h * (x / w); 
				w = x;
			}
		}		
		this.options.width 	= this.mediaWidth 	= w;
		this.options.height = this.mediaHeight 	= h;
		
        this.setWidth(w + 40);
		this.setHeight(h + 60);
		this.centerWindow();
	}
});