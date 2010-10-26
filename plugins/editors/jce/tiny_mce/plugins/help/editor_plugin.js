/**
 * $Id: editor_plugin.js 26 2009-05-25 10:21:53Z happynoodleboy $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('help');

	tinymce.create('tinymce.plugins.HelpPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceHelp', function() {
				ed.windowManager.open({
					url : ed.getParam('site_url') + 'index.php?option=com_jce&task=help&lang='+ ed.getParam('language') +'&plugin=help&file=help',
					width : 780,
					height : 480,
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register example button
			ed.addButton('help', {
				title : 'help.desc',
				cmd : 'mceHelp',
				image : url + '/img/help.gif'
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Help plugin',
				author : 'Moxiecode / Ryan Demmer',
				authorurl : 'http://www.joomlacontenteditor.net',
				infourl : 'http://www.joomlacontenteditor.net',
				version : "1.5.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('help', tinymce.plugins.HelpPlugin);
})();