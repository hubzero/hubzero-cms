/**
* @version		$Id: pagebreak.js 122 2009-06-24 10:10:44Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
var PageBreakDialog = {
	preInit : function() {
		tinyMCEPopup.requireLangPack();
		tinyMCEPopup.resizeToInnerSize();
	},
	init : function() {
		var d = document, ed = tinyMCEPopup.editor, s = ed.selection, n = s.getNode(), action = 'insert';
		
		if(n.nodeName == 'IMG' && ed.dom.hasClass(n, 'mceItemPageBreak')){
			action = 'update';
			
			d.getElementById('title').value = ed.dom.getAttrib(n, 'title', '');
			d.getElementById('alt').value 	= ed.dom.getAttrib(n, 'alt', '');
		}
		d.getElementById('insert').value = tinyMCEPopup.getLang(action, 'Insert', true); 
	},
	insert : function(){		
		var d = document, ed = tinyMCEPopup.editor, s = ed.selection, n = s.getNode();
		
		var v = {
			title 	: d.getElementById('title').value, 
			alt 	: d.getElementById('alt').value
		};
		
		if(n && n.nodeName == 'IMG' && ed.dom.hasClass(n, 'mceItemPageBreak')){
			ed.dom.setAttribs(n, v);	
		}else{
			tinyMCEPopup.execCommand('mcePageBreak', false, v);	
		}
		tinyMCEPopup.close();
	}
}
PageBreakDialog.preInit();
tinyMCEPopup.onInit.add(PageBreakDialog.init, PageBreakDialog);