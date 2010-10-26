var AdvLinkDialog = {
	preInit : function() {
		tinyMCEPopup.requireLangPack();
	},
	init : function() {
		var ed = tinyMCEPopup.editor, n = ed.selection.getNode(), action = 'insert';
		tinyMCEPopup.resizeToInnerSize();		

		dom.html('anchorlistcontainer', this.getAnchorListHTML('anchorlist','href'));
		dom.html('hrefbrowsercontainer', TinyMCE_Utils.getBrowserHTML('hrefbrowser','href','file','advlink'));
	
		el = ed.dom.getParent(n, "A");
		if (el != null && el.nodeName == "A"){
			action = "update";
		}
		
		TinyMCE_Utils.fillClassList('classlist');
		
		// Init plugin
		this.advlink = initAdvLink();

		dom.value('insert', tinyMCEPopup.getLang(action, 'Insert', true)); 
		if (action == "update") {
			var href = ed.documentBaseURI.toRelative(ed.dom.getAttrib(el, 'href'));
			// Setup form data
			dom.value('href', href);
			dom.value('title', ed.dom.getAttrib(el, 'title'));
			dom.value('id', ed.dom.getAttrib(el, 'id'));
			dom.value('style', ed.dom.getAttrib(el, "style"));
			dom.value('charset', ed.dom.getAttrib(el, 'charset'));
			dom.value('hreflang', ed.dom.getAttrib(el, 'hreflang'));
			dom.value('dir', ed.dom.getAttrib(el, 'dir'));
			dom.value('lang', ed.dom.getAttrib(el, 'lang'));
			dom.value('tabindex', ed.dom.getAttrib(el, 'tabindex', typeof(el.tabindex) != "undefined" ? el.tabindex : ""));
			dom.value('accesskey', ed.dom.getAttrib(el, 'accesskey', typeof(el.accesskey) != "undefined" ? el.accesskey : ""));
			dom.value('type', ed.dom.getAttrib(el, 'type'));
			dom.value('target', ed.dom.getAttrib(el, 'target'));
			dom.value('classes', ed.dom.getAttrib(el, 'class'));
	
			// Select by the values
			dom.setSelect('targetlist', ed.dom.getAttrib(el, 'target'));
			dom.setSelect('dir', ed.dom.getAttrib(el, 'dir'));
			dom.setSelect('rel', ed.dom.getAttrib(el, 'rel'), true);
			dom.setSelect('rev', ed.dom.getAttrib(el, 'rev'));
	
	
			if (href.charAt(0) == '#'){
				dom.setSelect('anchorlist', href);
			}
	
			dom.setSelect('classlist', ed.dom.getAttrib(el, 'class'), true);
			dom.setSelect('targetlist', ed.dom.getAttrib(el, 'target'), true);
		}else{
			var d = this.advlink.getParam('defaults');
			Editor.utilities.setDefaults(d);
		}		
		TinyMCE_EditableSelects.init();
		window.focus();
	},
	getAnchorListHTML : function(id, target){
		var ed = tinyMCEPopup.editor;
		var n = ed.getBody().getElementsByTagName("a");
	
		var html = "";
	
		html += '<select id="' + id + '" name="' + id + '" class="mceAnchorList" onchange="this.form.' + target + '.value=';
		html += 'this.options[this.selectedIndex].value;">';
		html += '<option value="">---</option>';
	
		for (var i=0; i<n.length; i++) {
			if ((name = ed.dom.getAttrib(n[i], "name")) != "")
				html += '<option value="#' + name + '">' + name + '</option>';
		}
	
		html += '</select>';
	
		return html;
	},
	checkPrefix : function(n){
		if(Validator.isEmail(n) && !/^\s*mailto:/i.test(n.value)){
			new Confirm(tinyMCEPopup.getLang('advlink_dlg.is_email', 'The URL you entered seems to be an email address, do you want to add the required mailto: prefix?'), function(state){
				if(state){
					n.value = 'mailto:' + n.value;
				}
				AdvLinkDialog.insertAndClose();
			});
		}else if(/^\s*www./i.test(n.value)){
			new Confirm(tinyMCEPopup.getLang('advlink_dlg.is_external', 'The URL you entered seems to be an external link, do you want to add the required http:// prefix?'), function(state){
				if(state){
					n.value = 'http://' + n.value;
				}
				AdvLinkDialog.insertAndClose();
			});
		}else{
			this.insertAndClose();
		}
	},
	insert : function(){
		var ed = tinyMCEPopup.editor;
		AutoValidator.validate(document);

		if(dom.value('href') === ''){
			new Alert(tinyMCEPopup.getLang('advlink_dlg.no_href', 'A URL is required. Please select a link or enter a URL'));
			return false;		
		}
		return this.checkPrefix(dom.get('href'));
	},
	insertAndClose : function(){
		var ed = tinyMCEPopup.editor, el, s, elementArray, i, args = {};
		
		s 	= ed.selection.getNode();		
		el 	= ed.dom.getParent(s, "A");
		
		// Remove element if there is no href
		if (dom.value('href') === '') {
			tinyMCEPopup.execCommand("mceBeginUndoLevel");
			i = ed.selection.getBookmark();
			ed.dom.remove(el, 1);
			ed.selection.moveToBookmark(i);
			tinyMCEPopup.execCommand("mceEndUndoLevel");
			tinyMCEPopup.close();
			return;
		}
			
		tinymce.extend(args, {
			href 		: dom.value('href'),
			title 		: dom.value('title'),
			target		: dom.getSelect('targetlist'),
			id 			: dom.value('id'),
			style 		: dom.value('style'),
			'class' 	: dom.getSelect('classlist') == '' ? dom.value('classes') : dom.getSelect('classlist'),
			rel 		: dom.value('rel'),
			rev 		: dom.value('rev'),
			charset 	: dom.value('charset'),
			hreflang 	: dom.value('hreflang'),
			dir 		: dom.value('dir'),
			lang 		: dom.value('lang'),
			tabindex 	: dom.value('tabindex'),
			accesskey 	: dom.value('accesskey'),
			type 		: dom.value('type')
		});
		
		tinyMCEPopup.execCommand("mceBeginUndoLevel");

		// Create new anchor elements
		if(el == null){
			tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});
	
			elementArray = tinymce.grep(ed.dom.select("a"), function(n) {return ed.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
			for (i=0; i<elementArray.length; i++){
				el = elementArray[i];
				
				if(el.childNodes.length != 1 || el.firstChild.nodeName != 'IMG') {
					ed.focus();
					ed.selection.select(el);
					ed.selection.collapse(0);
					tinyMCEPopup.storeSelection();
				}		
				ed.dom.setAttribs(el, args);
			}
		}else{
			ed.dom.setAttribs(el, args);
		}	
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
	},
	setClasses : function(v){
		Editor.utilities.setClasses(v);
	},
	setTargetList : function(v){
		dom.setSelect('targetlist', v, true);
	},
	setClassList : function(v){
		dom.setSelect('classlist', v, true);
	},
	insertLink : function(v){
		dom.value('href', tinyMCEPopup.editor.documentBaseURI.toRelative(v));
	},
	createEmail : function(){
		this.advlink.emailDialog();	
	},
	openHelp : function(){
		this.advlink.openHelp();	
	}
}
var AdvLink = Plugin.extend({
	moreOptions : function(){
		return {};
	},
	initialize : function(options){
		this.setOptions(this.moreOptions(), options);
		this.parent('advlink', this.options);
		
		this.initTree();
	},
	initTree : function(){
		this.tree = new Tree('link-options', {
			collapseTree: true,
			charLength: 50,
			onInit : function(fn){
				fn.apply();
			},
			// When a node is clicked
			onNodeClick : function(e, node){
				e = new Event(e);
				var v, el = e.target;
				if(!el.getParent().hasClass('nolink')){
					v = el.getProperty('href');
					if(v == 'javascript:;') v = node.id;
					//v = el.href == 'javascript:;' ? node.id : el.href;
					AdvLinkDialog.insertLink(string.decode(v));
				}
				if(el.getParent().hasClass('folder')){
					this.tree.toggleNode(e, node);
				}
			}.bind(this),
			// When a node is toggled and loaded
			onNodeLoad : function(node){
				this.tree.toggleLoader(node);
				var query = string.query(string.unescape(node.id));
				this.xhr('getLinks', query, function(o){
					if(o){
						if(!o.error){
							var ul = $E('ul', node);
							if(ul){
								ul.remove();	
							}
							this.tree.createNode(o.folders, node);
							this.tree.toggleNodeState(node, true);
						}else{
							alert(o.error);	
						}
					}
					this.tree.toggleLoader(node);
				}.bind(this));
			}.bind(this)
		});
	},
	emailDialog : function(){
		var fields = [
			// To Address
			new Element('div', {'class': 'formElm'}).adopt(
				new Element('label', {'for': 'email_to'}).setHTML(tinyMCEPopup.getLang('advlink_dlg.to', 'To'))								 
			).adopt(
				new Element('textarea', {
					id	: 'email_mailto',
					'class' : 'email',
					styles : {
						width: 200,
						height: 30
					}
				})
			),
			// CC Address
			new Element('div', {'class': 'formElm'}).adopt(
				new Element('label', {'for': 'email_cc'}).setHTML(tinyMCEPopup.getLang('advlink_dlg.cc', 'Cc'))								 
			).adopt(
				new Element('textarea', {
					id	: 'email_cc',
					'class' : 'email',
					styles : {
						width: 200,
						height: 30
					}
				})
			),
			// Bcc Address
			new Element('div', {'class': 'formElm'}).adopt(
				new Element('label', {'for': 'email_bcc'}).setHTML(tinyMCEPopup.getLang('advlink_dlg.bcc', 'Bcc'))								 
			).adopt(
				new Element('textarea', {
					id	: 'email_bcc',
					'class' : 'email',
					styles : {
						width: 200,
						height: 30
					}
				})
			),
			// Subject
			new Element('div', {'class': 'formElm'}).adopt(
				new Element('label', {'for': 'email_subject'}).setHTML(tinyMCEPopup.getLang('advlink_dlg.subject', 'Subject'))								 
			).adopt(
				new Element('textarea', {
					id	: 'email_subject',
					styles : {
						width: 200,
						height: 30
					}
				})
			)		  
		];
		this.addDialog('email', new basicDialog(tinyMCEPopup.getLang('advlink_dlg.email', 'Create E-Mail Address'), fields, {
			width: 300,
			buttons: [{
				'text': tinyMCEPopup.getLang('dlg.ok', 'OK'),
				'class': 'mceOk',
				'click': function(){
					var args = [], errors = 0;
					['mailto', 'cc', 'bcc', 'subject'].each(function(s){
						var v = $('email_' + s).value;
						if(v){
							v = v.replace(/\n\r/g, '');
							v.split(',').each(function(o){
								if(s !== 'subject'){
									if(!Validator.isEmail(o)){
										alert(s + ' is not a valid e-mail address!');
										errors++;
									}
								}
							});
							args.push((s == 'mailto') ? v : s + '=' + v);
						}
					});
					if(errors == 0){
						if(args.length){
							$('href').value = 'mailto:' + args.join('&').replace(/&/, '?');
						}
						this.removeDialog('email');
					}
				}.bind(this)
			},{
				'text': tinyMCEPopup.getLang('dlg.cancel', 'Cancel'),
				'class': 'mceCancel',
				'click': function(){
					this.removeDialog('email');
				}.bind(this)
			}],
			onOpen : function(){
				var v = $('href').value, s;
				if(/^mailto:/.test(v)){
					v = v.replace(/&amp;/g, '&');
					s = v.replace(/\?/, '&').replace(/=/g, ':').split(/&/);

					s.each(function(a){
						kv = a.split(/:/);
						$('email_' + kv[0]).value = kv[1];
					});
				}
			}
		}));
	}
});
AdvLink.implement(new Events, new Options);
AdvLinkDialog.preInit();
tinyMCEPopup.onInit.add(AdvLinkDialog.init, AdvLinkDialog);