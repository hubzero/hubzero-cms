/**
* $Id: editor_plugin.js 113 2009-06-22 17:40:52Z happynoodleboy $
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
	tinymce.create('tinymce.plugins.ImageManager', {
		init : function(ed, url) {
			function isMceItem(n) {
				return /mceItem/.test(n.className);
			};
			
			// Register commands
			ed.addCommand('mceImageManager', function() {
				// Internal image object like a flash placeholder
				if (isMceItem(ed.selection.getNode())){
					return;
				}

				ed.windowManager.open({
					file : ed.getParam('site_url') + 'index.php?option=com_jce&task=plugin&plugin=imgmanager&file=imgmanager',
					width : 760 + ed.getLang('imgmanager.delta_width', 0),
					height : 640 + ed.getLang('imgmanager.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url
				});
			});
			// Register buttons
			ed.addButton('imgmanager', {
				title : 'imgmanager.desc',
				cmd : 'mceImageManager',
				image : url + '/img/imgmanager.gif'
			});
			
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('imgmanager', n.nodeName == 'IMG' && !isMceItem(n));
			});
			
			ed.onInit.add(function() {				
				if (ed && ed.plugins.contextmenu) {
					ed.plugins.contextmenu.onContextMenu.add(function(th, m, e) {
						m.add({title : 'imgmanager.desc', icon_src : url + '/img/imgmanager.gif', cmd : 'mceImageManager'});
					});
				}
			});
		},
		getInfo : function() {
			return {
				longname : 'Image Manager',
				author : 'Ryan Demmer',
				authorurl : 'http://www.joomlacontenteditor.net',
				infourl : 'http://www.joomlacontenteditor.net/index2.php?option=com_content&amp;task=findkey&amp;pop=1&amp;lang=en&amp;keyref=imgmanager.about',
				version : '1.5.0'
			};
		}
	});
	// Register plugin
	tinymce.PluginManager.add('imgmanager', tinymce.plugins.ImageManager);
})();