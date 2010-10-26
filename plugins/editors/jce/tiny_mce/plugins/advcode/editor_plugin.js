/**
* $Id: editor_plugin.js 26 2009-05-25 10:21:53Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

(function() {
	tinymce.PluginManager.requireLangPack('advcode'); 
	tinymce.create('tinymce.plugins.AdvancedCodeEditorPlugin', {
    	init : function(ed, url) {
			var t 		= this;
			t.editor 	= ed;
			t.url 		= url;
						
			// Register commands
			ed.addCommand('AdvCodeEditor', function() {
				ed.windowManager.open({
					file : url + '/advcode.html',
					width : 720 + parseInt(ed.getLang('advcode.delta_width', 0)),
					height : 600 + parseInt(ed.getLang('advcode.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});
			// Register buttons
			ed.addButton('advcode', {
				title : 'advcode.desc',
				cmd : 'AdvCodeEditor',
				image : url + '/img/advcode.gif'
			});
    	},
		getInfo: function(){
			return {
				longname: 'Advanced Code Editor',
				author: 'Ryan Demmer',
				authorurl: 'http://www.joomlacontenteditor.net',
				infourl: 'http://www.joomlacontenteditor.net',
				version: '1.5.2'
			};
		}
	});
  	// Register plugin
	tinymce.PluginManager.add('advcode', tinymce.plugins.AdvancedCodeEditorPlugin);
})();
