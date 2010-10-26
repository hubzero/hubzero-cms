/**
* @package      JCE
* @copyright    Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
tinyMCEPopup.requireLangPack();

var PreviewDialog = {
	init : function() {
		var ed = tinyMCEPopup.editor, t = this;
		
		// load MediaObject
		var scriptLoader = new tinymce.dom.ScriptLoader();
		scriptLoader.add(tinymce.settings['document_base_url'] + 'plugins/system/jcemediabox/js/mediaobject.js');
		scriptLoader.loadQueue(function() {
			if (typeof JCEMediaObject != 'undefined') {
				JCEMediaObject.init(tinymce.settings['document_base_url']);
	   		}
		});
		
		ed.dom.addClass('content', 'loader');
		
		tinymce.util.JSONRequest.sendRPC({
			url : tinymce.settings['site_url'] + 'index.php?option=com_jce&task=plugin&plugin=preview&file=preview&cid=' + tinymce.settings['component_id'],
			method : 'POST',
			params : ed.getContent(),
			success : function(r) {
				document.getElementById('content').innerHTML = r;
				ed.dom.removeClass('content', 'loader');
			},
			error : function(e, x) {
				if (e.errstr || x.responseText) {
					alert(e.errstr || ('Error response: ' + x.responseText));
				}
				ed.dom.removeClass('content', 'loader');
			}
		});
	}
};
tinyMCEPopup.onInit.add(PreviewDialog.init, PreviewDialog);