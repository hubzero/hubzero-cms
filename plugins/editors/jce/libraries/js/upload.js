/**
* @version		$Id: upload.js 49 2009-05-28 10:02:46Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

var Uploader = new Class({

	options: {
		size: false,
		limit: 5,
		auto: false,
		validate: false, // provide a function that returns true for valid and false for invalid files.
		debug: false,
		filter: null,
		
		fileInvalid: null, // called for invalid files with error stack as 2nd argument
		fileCreate: null, // creates file element after select
		fileUpload: null, // called when file is opened for upload, allows to modify the upload options (2nd argument) for every upload
		fileComplete: null, // updates the file element to completed state and gets the response (2nd argument)
		fileRemove: null,// removes the element
		uploadComplete: null

	},

	initialize: function(list, options) {
		this.list 		= $(list);	
		this.element 	= options.field;
		this.files 		= [];
		this.current	= null;
		
		// Convert flash type filter into array
		this.filter 	= [];
		for(n in options.filter){
			s = options.filter[n].replace(/[\*\.]*/gi, '');
			if(s.length > 1){
				this.filter.merge(s.split(/[,;]/));
			}
		}
		
		this.element.setStyle('display', 'none');
				
		this.target = new Element('div', {
			'class': 'uploadButton',
			styles : {
				'float': 'left'	,
				'display': 'none'
			}
		}).adopt(
			new Element('span', {
				'class': 'addQueue'			
			}).setHTML(tinyMCEPopup.getLang('dlg.add', 'Add'))
		).injectBefore(this.element);
		
		new Element('div', {
			'class': 'uploadButton',
			events: {
				click: function(){
					this.removeFile();
				}.bind(this)
			},
			styles : {
				'float': 'right',
				'display': 'none'
			}
		}).adopt(
			new Element('span', {
				'class': 'removeQueue'			
			}).setHTML(tinyMCEPopup.getLang('dlg.clear', 'Clear'))
		).injectBefore(this.element);
		
		this.initIFrame();
		this.setOptions(options);
	},
		
	initIFrame : function(){
		$$('div.uploadButton').setStyle('display', '');
		
		this.element.setProperty('size', 1).addEvent('change', function(e){
			e 	= new Event(e);
			el 	= e.target;
			if(el.value == '') return;
			// Create copy
			el.getParent().adopt(el.clone().setProperty('value', '').cloneEvents(el, 'change'));
			// Hide previous input
			el.removeProperty('name').setStyle('display', 'none');
			this.onSelect({'name': el.value, 'input': el});
		}.bind(this)).setStyles({display: '', position: 'absolute', opacity: 0, visibility: 'visible', 'height': '100%'}).inject($(this.target));
		
		if(window.ie){
			this.element.setStyle('left', -26);	
		}else if(window.opera){
			this.element.setStyle('left', -18);
		}else{
			this.element.setStyle('left', -27);
		}
		
		this.iframe = new IFrame({
			action 	: 'upload',
			cleanup	: false,
			onStart : function(){
				var el = this.current.element
				// Add loader
				el.getElement('span.status').addClass('load');
				el.getElement('span.status').removeProperty('title');
				el.getElement('span.rename').setStyle('display', 'none');
			}.bind(this),
			onComplete : function(o){					
					var el = this.current.element, status = el.getElement('span.status');
					// Remove loader
					status.removeClass('load');
					if(o.error){
						alert(o.error);	
					}
					if(o.result.error){
						status.addClass('error');
						el.getNext().setStyle('display', '').getElement('span').setHTML(o.result.text);
						el.addClass('error');
						
					}else{
						status.addClass('complete');
					}
					this.onComplete(this.current, o.result.text);
					$ES('input', this.current.element).remove();
					// Upload next file
					this.upload();
			}.bind(this)
		});
	},

	onSelect: function(file) {
		// No duplicates
		if(this.getFile(file)){
			return false;
		}
		var ext = this.fileinfo(file.name).ext;
		if(this.filter.length && !this.filter.contains(ext.toLowerCase())){
			alert('Unsupported file type - '+ ext +'!');
			return false;	
		}
		
		if(this.options.limit && this.countFiles() >= this.options.limit){
			alert('Upload limit reached!');
			return false;	
		}
		// Add file to array
		this.files.push(file);
		(this.options.fileCreate || this.fileCreate).call(this, file);
		return true;
	},

	onComplete: function(file, response) {
		file.complete = true;
		(this.options.fileComplete || this.fileComplete).call(this, file, response);
		
		if(this.files.indexOf(file) == this.files.length - 1){
			(this.options.uploadComplete || this.uploadComplete).call(this);	
		}
	},

	onAllComplete: function() {
		this.uploading = false;	
	},

	upload: function() {
		// Only if there are files to upload
		if(this.files.length){
			this.uploading = true;
			var index = !this.current ? 0 : this.files.indexOf(this.current) + 1;
			if(index < this.files.length){
				this.current = this.files[index];
				if(!this.current.complete){
					this.current.input.setProperty('name', 'Filedata');
					this.current.element.adopt(new Element('input', {type: 'hidden', name: 'upload-name', value: this.fileinfo(this.current.element.getText()).name}));
					this.iframe.submit();
				}
			}else{
				this.onAllComplete();	
			}
		}
		return false;
	},

	removeFile: function(file) {
		var remove = this.options.fileRemove || this.fileRemove;
		if (!file) {
			this.list.empty();
			this.files = [];
		} else {
			this.files.remove(file);
			remove.call(this, file);
		}
	},

	countFiles: function() {
		var ret = 0;
		for (var i = 0, j = this.files.length; i < j; i++) {
			if (!this.files[i].finished) ret++;
		}
		return ret;
	},
	
	getFile: function(file) {
		var ret = null;
		this.files.some(function(v) {
			if(v.name != file.name) return false;
			ret = v;
			return true;
		});
		return ret;
	},

	fileCreate: function(file) {
		var f = this.fileinfo(file.name);
		
		// Edit-In-Place
		this.eip = function(el){
			new Element('input', {
				type: 'text',
				value: this.fileinfo(el.getText()).name,
				events: {
					'blur': function(e){
						e 	= new Event(e);
						re 	= e.target;
						e.stop();
						el.setHTML(re.value + '.' + f.ext).setStyle('display', '');
						re.remove();
					}
				}
			}).injectAfter(el).setStyle('display', 'none');
			// Seems overly complicated but prevents relative span elements after collapsing.
			el.setStyle('display', 'none').getNext().setStyle('display', '').focus();
		}.bind(this)
		
		var status = new Element('span', {
			title : tinyMCEPopup.getLang('dlg.delete', 'Delete'),
			'class': 'status',
			events: {
				click: function(e) {
					e = new Event(e);
					if(!/(complete|error|load)/i.test(e.target.className)){
						this.removeFile(file);
					}
					return false;
					e.stop();
				}.bind(this)
			}
		}).addClass('delete'); 
		
		var text = new Element('span', {
			'title': f.base, 
			events: {
				'click' : function(e){
					e 	= new Event(e);
					p 	= e.target.getParent();	
					el 	= e.target;
					e.stop();
					if(/(complete|error|load)/i.test($E('span.status', p).className)) return false;
					this.eip(el);
				}.bind(this)
			}
		}).addClass('queue-name').setHTML(f.base);
		
		var rename = new Element('span', {
			title : tinyMCEPopup.getLang('dlg.rename', 'Rename'),
			events : {
				'click' : function(e){
					e 	= new Event(e);
					p 	= e.target.getParent();
					el 	= p.getElement('span.queue-name');
					e.stop();
					this.eip(el);
				}.bind(this)
			}
		}).addClass('rename');
		
		file.element = new Element('li').addClass('queue-text').addClass('file').addClass(f.ext.toLowerCase()).adopt(				
			text
		).adopt(
			status
		).adopt(
			rename
		).adopt(	
			file.input || ''
		).injectInside(this.list);
		
		this.list.adopt(new Element('li').addClass('queue-error').setStyle('display', 'none').adopt(new Element('span')));
	},

	fileRemove: function(file) {
		if(file.element){
			file.element.remove();	
		}
	},
	
	fileinfo : function(s){
		// base name
		s = s.replace(/\\/g, '/');
		s = s.substring(s.length, s.lastIndexOf('/')+1)
		// safe file	
		
		s = s.replace(/(\.){2,}/g, '').replace(/\s/g, '_').replace(/[^a-z0-9\.\_\-]/gi, '');
		
		var f = {
			base: s, 
			name: s.replace(/\.[^.]+$/i, ''), 
			ext: s.substring(s.length, s.lastIndexOf('.')+1)
		};
		return f;
	}
});
Uploader.implement(new Events, new Options);