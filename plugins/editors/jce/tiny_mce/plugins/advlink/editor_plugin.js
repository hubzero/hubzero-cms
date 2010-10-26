(function() {
	tinymce.create('tinymce.plugins.AdvancedLinkPlugin', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mceAdvLink', function() {
				var se = ed.selection;

				// No selection and not in link
				if (se.isCollapsed() && se.getNode().nodeName != 'A')
					return;

				ed.windowManager.open({
					file : ed.getParam('site_url') + 'index.php?option=com_jce&task=plugin&plugin=advlink&file=advlink',
					width : 500 + ed.getLang('advlink.delta_width', 0),
					height : 480 + ed.getLang('advlink.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('advlink', {
				title : 'advlink.desc',
				cmd : 'mceAdvLink',
				image : url + '/img/advlink.gif'
			});

			ed.addShortcut('ctrl+k', 'advlink.desc', 'mceAdvLink');

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('advlink', co && n.nodeName != 'A');
				cm.setActive('advlink', n.nodeName == 'A' && !n.name);
			});
			
			ed.onInit.add(function() {
				if (ed && ed.plugins.contextmenu) {
					ed.plugins.contextmenu.onContextMenu.add(function(th, m, e) {
						if((e.nodeName == 'A' && !ed.dom.getAttrib(e, 'name')) || !ed.selection.isCollapsed()){
							m.addSeparator();
							m.add({title : 'advlink.desc', icon_src : url + '/img/advlink.gif', cmd : 'mceAdvLink', ui : true});
							m.add({title : 'advanced.unlink_desc', icon : 'unlink', cmd : 'UnLink'});
						}
					});
				}
			});
		},

		getInfo : function() {
			return {
				longname : 'Advanced link',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advlink',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('advlink', tinymce.plugins.AdvancedLinkPlugin);
})();