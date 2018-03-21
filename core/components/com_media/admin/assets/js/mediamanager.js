/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * JMediaManager behavior for media component
 *
 * @package		Joomla.Extensions
 * @subpackage  Media
 * @since		1.5
 */
(function() {
	var MediaManager = this.MediaManager = {

		initialize: function()
		{
			this.folderframe	= $('#folderframe');
			this.folderpath		= $('#folderpath');

			this.updatepaths	= $('input.update-folder');

			this.frame		= window.frames['folderframe'];
			this.frameurl	= this.frame.location.href;
			//this.frameurl   = window.frames['folderframe'].location.href;

			var self = this;
			$('#media-tree').find('a').on('click', function(e){
				var node = $(this);

				target = node.attr('target') != null ? node.attr('target') : '_self';

				// Get the current URL.
				uri = self._getUriObject(self.frameurl);
				current = uri.file+'?'+uri.query;

				if (current != 'undefined?undefined' && current != encodeURI(node.attr('href'))) {
					window.frames[target].location.href = node.attr('href');
				}
			});
			/*this.tree = new MooTreeControl({ div: 'media-tree_tree', mode: 'folders', grid: true, theme: '../core/assets/images/mootree.gif', onClick:
					function(node){
						target = node.data.target != null ? node.data.target : '_self';

						// Get the current URL.
						uri = this._getUriObject(this.frameurl);
						current = uri.file+'?'+uri.query;

						if (current != 'undefined?undefined' && current != encodeURI(node.data.url)) {
							window.frames[target].location.href = node.data.url;
						}
					}.bind(this)
				},{ text: '', open: true, data: { url: 'index.php?option=com_media&view=mediaList&tmpl=component', target: 'folderframe'}});
			this.tree.adopt('media-tree');*/
			this.tree = $('#media-tree').treeview({
				collapsed: true
			});
		},

		submit: function(task)
		{
			form = window.frames['folderframe'].document.forms['mediamanager-form'];
			form.task.value = task;
			if ($('#username').length) {
				form.username.value = $('#username').val();
				form.password.value = $('#password').val();
			}
			form.submit();
		},

		onloadframe: function()
		{
			// Update the frame url
			this.frameurl = this.frame.location.href;

			var folder = this.getFolder();
			var basepath = "/var/www/hub/app/site/media";
			if (folder) {
				this.updatepaths.each(function(i, path){ path.value =folder; });
				this.folderpath.value = basepath+'/'+folder;
				node = this.tree.get('node_'+folder);
				//node.toggle(false, true);
			} else {
				this.updatepaths.each(function(i, path){ path.value = ''; });
				this.folderpath.value = basepath;
				node = this.tree.root;
			}

			if (node) {
				this.tree.select(node, true);
			}

			//$(viewstyle).addClass('active');

//console.log($('#uploadForm[action]'));
//			a = this._getUriObject($('#uploadForm[action]'));
			/*q = new Hash(this._getQueryObject(a.query));
			q.set('folder', folder);
			var query = [];
			q.each(function(v, k){
				if (v != null) {
					this.push(k+'='+v);
				}
			}, query);*/
/*
			q = this._getQueryObject(a.query);
			q.folder = folder;
			var query = [];
			for (var k in q) {
				if (q[k] != null) {
					query.push(k+'='+q[k]);
				}
			};
			a.query = query.join('&');

			if (a.port) {
				$('#uploadForm').attr('action', a.scheme+'://'+a.domain+':'+a.port+a.path+'?'+a.query);
			} else {
				$('#uploadForm').attr('action', a.scheme+'://'+a.domain+a.path+'?'+a.query);
			}
*/
		},

		oncreatefolder: function()
		{
			if ($('#foldername').val().length) {
				$('#dirpath').val(this.getFolder());
				Joomla.submitbutton('createfolder');
			}
		},

		setViewType: function(type)
		{
			$('#' + type).addClass('active');
			$('#' + viewstyle).removeClass('active');
			viewstyle = type;
			var folder = this.getFolder();
			this._setFrameUrl('index.php?option=com_media&view=mediaList&tmpl=component&folder='+folder+'&layout='+type);
		},

		refreshFrame: function()
		{
			this._setFrameUrl();
		},

		getFolder: function()
		{
			var url  = this.frame.location.search.substring(1);
			var args = this.parseQuery(url);

			if (args['folder'] == "undefined") {
				args['folder'] = "";
			}

			return args['folder'];
		},

		parseQuery: function(query)
		{
			var params = new Object();
			if (!query) {
				return params;
			}
			var pairs = query.split(/[;&]/);
			for ( var i = 0; i < pairs.length; i++ )
			{
				var KeyVal = pairs[i].split('=');
				if ( ! KeyVal || KeyVal.length != 2 ) {
					continue;
				}
				var key = unescape( KeyVal[0] );
				var val = unescape( KeyVal[1] ).replace(/\+ /g, ' ');
				params[key] = val;
			}
			return params;
		},

		_setFrameUrl: function(url)
		{
			if (url != null) {
				this.frameurl = url;
			}
			this.frame.location.href = this.frameurl;
		},

		_getQueryObject: function(q) {
			var vars = q.split(/[&;]/);
			var rs = {};
			if (vars.length) {
				//vars.each(function(val) {
				for ( var i = 0; i < vars.length; i++ )
				{
					var keys = vars[i].split('=');
					if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
				};
			}
			return rs;
		},

		_getUriObject: function(u) {
			var bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
			var barts = null;
			if (bits) {
				barts = {};
				var keys = ['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'];
				for ( var i = 0; i < bits.length; i++ )
				{
					barts[keys[i]] = bits[i];
				}
			}
			return (barts)
				? barts //.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
				: null;
		},

		delete: function()
		{
			var self = this;
			$("#toolbar-delete").click(function() {
				$("#folderframe").contents().find(".manager input[name=folder]").each(function() {
					$("form[name=adminForm] input[name=folder]").val($(this).val());
					return;
				});
				var clicked = [];
				$("#folderframe").contents().find(".imginfoBorder input[type=checkbox]").each(function() {
					if ($(this).is(":checked")) {
						clicked.push($(this).attr("value"));
					}
					return;
				});
				var count = 0;
				var input = $("form[name=adminForm] input[name=task]");
				clicked.forEach(function(element) {
					var cb = $("<input type='hidden' name='rm[]' id='cb" + count + "' value='" + clicked[count] + "' />");
					input.append(cb);
					count++;
					return;
				});
				Joomla.submitbutton('delete');
			});
			return;
		}
	};
})($);

jQuery(document).ready(function($){
	// Added to populate data on iframe load
	MediaManager.initialize();
	MediaManager.trace = 'start';
	document.updateUploader = function() { MediaManager.onloadframe(); };
	MediaManager.onloadframe();
	MediaManager.delete();
});
