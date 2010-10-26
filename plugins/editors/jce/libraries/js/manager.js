/**
* @version		$Id: manager.js 84 2009-06-11 19:04:04Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

/**
Class: Manager
	Base manager class for creating a JCE Manager object.

Arguments:
	options - optional, an object containing options.

Options:
	interface 		- various interface identifiers.
	filter 			- file extension filter list
	tree 			- use folder tree (requires tree.js)
	onDeleteFiles 	- Delete files callback function.
	onDeleteFolder 	- Delete folder callback function.
	onRename 		- Fodler / file rename callback function.
	onNewFolder 	- New folder callback function.
	onListComplete 	- File / folder list load complete callback function.
	onFileClick 	- File click callback function.
	onFileDetails	- File details callback function.

Example:
	var imgmanager = new Manager('imgmanager', src, args, {params: {'key': 'value'}});
*/
var Manager = Plugin.extend({
	_actions 		: [],
	_buttons 		: {
		'folder'	: [],
		'file'		: []
	},
	// Selected files array
	_selectedItems 	: [],
	_selectedIndex 	: [],
	_activeItem		: 0,	
	// Returned items array
	_returnedItems 	: [],
	// 'Clipboard'
	_pastefiles 	: '',
	_pasteaction	: '',
	// List limits	
	_limitcount		: 0,
	_limitend 		: 0,
	// Options
	moreOptions : function(){
		return {
			// Various dialog containers
			dialog :{
				list:  		'dir-list',
				tree: 		'tree-body',
				info: 		'info-text',
				limit:		'dir-limit',
				comments:	'info-comment',
				nav: 		'info-nav',
				status: 	'message-status',
				message:	'message-info',
				buttons: 	'buttons',
				actions: 	'actions',
				refresh: 	'refresh',
				search: 	'search',
				sortExt:	'sort-ext',
				sortName: 	'sort-name'
			},
			actions:		null,
			buttons:		null,
			tree:			true,
			upload:			{
				size: 1024,
				types: {},
				conflict: 'all',
				limit: false,
				onLoad: Class.empty
			},
			listlimit	:	25, 
			onInit:			Class.empty,
			onFileInsert:	Class.empty,
			onFileDelete: 	Class.empty,
			onFolderDelete: function(node){
				if(this.treeLoaded){
					// Remove tree node
					this.tree.removeNode(node);
				}
			}.bind(this),
			onFileRename: 	Class.empty,
			onFolderRename:	function(node, name){
				if(this.treeLoaded){
					// Rename tree node
					this.tree.renameNode(node, name);
				}	
			}.bind(this),
			onFolderNew: Class.empty,
			onLoadList: function(o){
				if (o.folders.length) {
					o.folders.each(function(e){
						$('folder-list').setStyle('display', '').adopt(
							new Element('li').addClass('folder').addClass(e.classes).setProperties({'id': e.id, 'title': e.name}).addEvent('click', function(event){
								this.setSelectedItems(event, false);
							}.bind(this)).adopt(
								new Element('a').setProperty('href', 'javascript:;').setHTML(decodeURIComponent(e.name)).addEvent('click', function(){
									this.changeDir(e.id);
								}.bind(this))
							)
						)
					}.bind(this));
				} else {
					if (this.isRoot()) {
						$('folder-list').setStyle('display', 'none');
					}
				}
									
				$('file-list').empty();
				if(o.total.files){
					o.files.each(function(e){
						$('file-list').adopt(
							new Element('li', {
								'title'	: e.name,
								//'id' : e.id,
								events: {
									click: function(event){
										this.setSelectedItems(event, true);
									}.bind(this),
									dblclick: function(){
										return false;
									}.bind(this)
								}
							}).addClass('file').addClass(string.getExt(e.name)).addClass(e.classes).adopt(
								new Element('a').setProperty('href', 'javascript:void(0);').setHTML(decodeURIComponent(e.name)).addEvent('click', function(){	
									this.fireEvent('onFileClick', [e.name]);
								}.bind(this))
							)
						)
					}.bind(this));
				} else {
					$('file-list').adopt(
						new Element('li').addClass('nofile').setHTML(tinyMCEPopup.getLang('dlg.no_files', 'No files'))
					)	
				}
			},
			onListComplete: Class.empty,
			onFileClick:	Class.empty,
			onFileDetails:	Class.empty
		};
	},
	/**
	 * Initialise the class
	 * @param {String} plugin 	The plugin name.
	 * @param {String} src 		The returned src if any.
	 * @param {Object} vars 	Optional variables object.
	 * @param {Object} options 	Optional options object.
	 */
	initialize : function(plugin, src, vars, options){				
		// Set options
		this.setOptions(this.moreOptions(), options);
		this.parent(plugin, this.options);
		// Preload image files
		new Asset.images([this.getImage('libraries.icons.gif'), this.getImage('libraries.ext.gif')]);
		// Load theme language file
		this.loadLanguage();
		// Setup default values
		this._vars = vars || '';
		// Create Actions and Button
		this.addActions(this.options.actions);
		this.addButtons(this.options.buttons);
		
		// Build file and folder lists
		$(this.options.dialog.list).adopt([
			new Element('ul').addClass('item-list').setProperty('id', 'folder-list'),
			new Element('ul').addClass('item-list').setProperty('id', 'file-list')
		]);
		// Info navigation buttons
		$(this.options.dialog.nav + '-left').addEvent('click', function(){
			this._activeItem--;
			if(this._activeItem < 0){
				this._activeItem = 0;	
			}
			this.showFileDetails();
		}.bind(this));
		$(this.options.dialog.nav + '-right').addEvent('click', function(){
			this._activeItem++;
			var n = this._selectedItems.length;
			if(this._activeItem > n-1){
				this._activeItem = n-1;	
			}
			this.showFileDetails();
		}.bind(this));
		
		$(this.options.dialog.limit + '-select').value = Cookie.get('jce_' + plugin + '_limit') || this.options.listlimit;
		
		// Setup limit list
		$(this.options.dialog.limit + '-select').addEvent('change', function(){
			this._limitcount = 0;
			Cookie.set('jce_' + plugin + '_limit', $(this.options.dialog.limit + '-select').value);
			this.refreshList();
		}.bind(this));
		
		// Limit List Nav
		// Info navigation buttons
		$(this.options.dialog.limit + '-left').addEvent('click', function(){		
			this._limitcount = this._limitcount - parseInt(this._limit);
			this.refreshList();
		}.bind(this));
		$(this.options.dialog.limit + '-left-end').addEvent('click', function(){		
			this._limitcount = 0;
			this.refreshList();
		}.bind(this));
		$(this.options.dialog.limit + '-right').addEvent('click', function(){
			this._limitcount = this._limitcount + parseInt(this._limit);
			this.refreshList();
		}.bind(this));
		$(this.options.dialog.limit + '-right-end').addEvent('click', function(){
			this._limitcount = this._limitend;
			this.refreshList();
		}.bind(this));

		// Sortables
		new ListSorter(this.options.dialog.sortExt, 'ext', ['file-list']);
		new ListSorter(this.options.dialog.sortName, 'name', ['folder-list', 'file-list']);
		// Searchables
		new Searchables(this.options.dialog.search, this.options.dialog.list, 'file-list', {
			onFind : function(el){
				if(el.length){
					t.selectNoItems();
					t.selectItems(el, true);	
				}else{				 	
					t.selectNoItems();
				}
			}.bind(this)
		});
		// Setup refresh button
		$(this.options.dialog.refresh).addEvent('click', function(){
			this.refreshList();
		}.bind(this));
		
		this.setupDir(src);
		this.fireEvent('onInit');
	},
	/**
	 * Set up the base directory
	 * @param {String} src The base url
	 */
	setupDir : function(src){
		var p = '/', f = '', base = this.getParam('base'), n = base.length;
		if(src){
			if(src.substring(0, n) == base){
				p = string.dirname(src).replace(base, '', 'g') || '/';
				f = string.basename(src) || '';
			}
		}else{
			p = Cookie.get('jce_' + this.getPlugin() + '_dir') || '/';			
			f = '';
		}
		
		this._dir = string.path('/', this._encode(p));
		
		this.addReturnedItem(f);
		
		if(this.treeLoaded){
			// Initialize tree view
			this.initTree();
		}else{
			// Load folder / file list
			this.getList();	
		}
	},
	/**
	 * Check if the Tree option is set and the Tree Class is loaded
	 * return Boolean.
	 */
	treeLoaded : function(){
		return this.options.tree && typeof Tree != 'undefined';
	},
	/**
	 * Initialize the Tree
	 */
	initTree : function(){
		/* Initialise tree */
		this.setStatus(tinyMCEPopup.getLang('dlg.message_tree', 'Building tree list...'), true);
		this.tree = new Tree(this.options.dialog.tree, {
			onInit : function(fn){
				this.xhr('getTree', this._dir, function(o){
					// Set default tree
					$(this.options.dialog.tree).setHTML(o);
					// Init callback
					fn.apply();
					// Load folder / file list
					this.getList(this._dir);
				}.bind(this));
			}.bind(this),
			// When a node is clicked
			onNodeClick : function(el, node){
				this.changeDir(node.id);
			}.bind(this),
			// When a node is toggled and loaded
			onNodeLoad : function(node){
				this.tree.toggleLoader(node);
				this.xhr('getTreeItem', node.id, function(o){
					if(o){
						if(!o.error){
							var ul = $E('ul', node);
							if(ul){
								ul.remove();	
							}
							this.tree.createNode(o.folders, node.id);
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
	
	/**
	 * Reset the Manager
	 */
	resetManager : function(){
		// Clear selects
		this.selectNoItems();
		// Clear returns
		this._returnedItems	= [];
		// Close any dialogs
		this._dialog.each(function(dialog){
			if(typeof dialog.close() != 'undefined'){
				dialog.close();	
			}
		});
	},
	
	/**
	 * Clear the Paste action
	 */
	clearPaste : function(){
		// Clear paste 
		this._pasteaction = '';
		this._pastefiles  = '';
		this.hideButton(this.getButton('file', 'paste').element);
	},
	/**
	 * Set a status message
	 * @param {String} message
	 * @param {String} loading
	 */
	setStatus : function(message, loading){
		$(this.options.dialog.status).className = loading ? 'load' : '';
		$(this.options.dialog.status).setHTML('<span>' + message + '</span>');
	},
	/**
	 * Set a message
	 * @param {String} message
	 * @param {String} classname
	 */
	setMessage : function(message, classname){
		$(this.options.dialog.message).className = classname || 'info';
		$(this.options.dialog.message).setHTML('<span>' + message + '</span>');
	},
	/**
	 * Sets a loading message
	 */
	setLoader : function(){
		this.setStatus(tinyMCEPopup.getLang('dlg.message_load', 'Loading...'), true);
	},
	/**
	 * Reset the message display
	 */
	resetMessage : function(){
		this.setMessage(tinyMCEPopup.getLang('dlg.file_select', 'Click on a file name to select for insert.'), 'info');
	},
	/**
	 * Reset the status display
	 */
	resetStatus : function(){
		this.setStatus(tinyMCEPopup.getLang('dlg.current_dir', 'Current directory is: ') + decodeURIComponent(this._dir) + ' ( ' + this._foldercount + ' ' + tinyMCEPopup.getLang('folders', 'folders') + ', ' + this._filecount + ' ' + tinyMCEPopup.getLang('files', 'files') + ' )');
	},
	/**
	 * Get the parent directory
	 * @return {String} s The parent/previous directory.
	*/
	getPreviousDir : function(){
		if(this._dir.length < 2){
    		return this._dir;
		}
		var dirs = this._dir.split('/');
		var s = '/';
		for(var i = 0; i < dirs.length-1; i++){
			s = string.path(s, dirs[i]);
		}
		return s;
	},
	/**
	 * Add an item to the returnedItems array
	 * @return {String} file The item name.
	*/
	addReturnedItem : function(file){
		this._returnedItems.include(file);
	},
	/**
	* Setup the returned file after upload
	* @param {String} file The returning file name.
	*/
	returnFile : function(file){
		this.addReturnedItem(string.basename(file));
		this.changeDir(string.dirname(file));
	},
	/**
	 * Set the current directory
	 * @param {String} dir
	 */
	setDir: function(dir){
		this._dir = dir;
	},
	/**
	 * Get the current directory
	 */
	getDir: function(){
		return this._dir;
	},
	/**
	  Determine whether current directory is root
	 */
	isRoot : function(){
		return this._dir == '' || this._dir == '/';
	},
	/**
	 * Change Directory
	 * @param {String} dir
	 */
	changeDir: function(dir){
		this.resetManager();
		this._limitcount = 0;		
		this.setDir(dir);
		this.getList();
	},
	/**
	* Retrieve a list of files and folders
	*/
	getList : function(){
		this.action = $E('form').action + '&upload-dir=' + this._dir;
		Cookie.set("jce_" + this.getPlugin() + '_dir', this._dir, 1);
		this.setLoader();
		this.hideButtons('folder');
		this.hideButtons('file');		
		this._limit = $(this.options.dialog.limit + '-select').value || this.options.listlimit;		
		this.xhr('getItems', [this._dir, this._limit, this._limitcount, this._vars], this.loadList);
	},
	/**
	 * Refresh the file list
	 */
	refreshList : function(){
		this.resetManager();
		this.getList();
	},
	/**
	* Load the file/folder list into the container div
	* @param {Object} The folder/file JSON object
	*/
	loadList: function(o){	
		$('folder-list').empty();
		this._foldercount 	= o.total.folders;
		this._filecount 	= o.total.files;

		this._limitend		= (o.total.folders + o.total.files) - this._limit;
		var count			= this._limitcount + o.folders.length + o.files.length;
				
		if (count < (o.total.folders + o.total.files)) {
			$(this.options.dialog.limit + '-right').setStyle('visibility', 'visible');
			$(this.options.dialog.limit + '-right-end').setStyle('visibility', 'visible');
		} else {
			$(this.options.dialog.limit + '-right').setStyle('visibility', 'hidden');
			$(this.options.dialog.limit + '-right-end').setStyle('visibility', 'hidden');
		}
		
		if ((count - this._limit) > 0) {
			$(this.options.dialog.limit + '-left').setStyle('visibility', 'visible');
			$(this.options.dialog.limit + '-left-end').setStyle('visibility', 'visible');
		} else {
			$(this.options.dialog.limit + '-left').setStyle('visibility', 'hidden');
			$(this.options.dialog.limit + '-left-end').setStyle('visibility', 'hidden');
		}
		
		if (o.folders.length) {
			this._dir = this._encode(string.dirname(o.folders[0].id) || '/'); 	
		} else if (o.files.length) {
			this._dir = this._encode(string.dirname(o.files[0].id) || '/');
		}
		
		if (!this.isRoot()) {
			$('folder-list').adopt(
				new Element('li').addClass('folder').addClass('folder-up').setProperties({'title': 'Up'}).adopt(
					new Element('a').setProperty('href', 'javascript:;').setHTML('...')
				).addEvent('click', function(){
					this.changeDir(this.getPreviousDir());
				}.bind(this))
			);
		}

		if(this.options.tree){
			this.tree.createNode(o.folders, this._dir);
		}
		
		// Alternate loadList function
		this.fireEvent('onLoadList', [o]);

		/*Hover fix for IE*/
		if(window.ie6){
			$ES('li', '.item-list').each(function(e){
				e.addEvent('mouseover', function(){
					e.addClass('hover');
				});
				e.addEvent('mouseout', function(){
					e.removeClass('hover');
				});
			});
		}

		/* Browser specific selection settings */
		$ES('li', this.options.dialog.list).each(function(e){
			if(window.gecko){
				e.setStyle('-moz-user-select', 'none');
			}else{
				e.setProperty('unselectable', 'on');
			}
		});
		if(this._returnedItems.length){
			this.selectItemsByName(this._returnedItems);
			this._returnedItems = [];
		}
		if(this._pastefiles !== ''){
			this.showButton(this.getButton('file', 'paste').element);
		}
		this.resetStatus();
		this.resetMessage();
		this.fireEvent('onListComplete');
	},
	/**
	* Execute a command
	* @param {String} The command name
	* @param {String} The command type
	*/
	execCommand : function(name, type){		
		var dir 	= this._dir;
		var list	= this.serializeSelectedItems();
		switch(name){
			case 'help':
				this.openHelp('manager');
				break;
			case 'insert':
				this.fireEvent('onFileInsert', [list]);
				break;
			case 'view':
				var url 		= string.path(string.path(this.getSite(), this.getParam('base')), string.path(this._dir, this._encode(this._selectedItems[0].title)));	
				var name 		= string.basename(this._selectedItems[0].title);
				var viewable 	= this.getParam('viewable') || 'jpeg,jpg,gif,png';
				if(viewable.split(',').contains(string.getExt(name))){
					if (/\.(js|asp|php|vb|vbs|exe|ocx|dll)/i.test(name)) {
						return false;
					}
					if(/\.(jpeg|jpg|gif|png|avi|wmv|wm|asf|asx|wmx|wvx|mov|qt|mpg|mp3|mp4|mpeg|swf|flv|xml|dcr|rm|ra|ram|divx)/i.test(name)){
						new mediaPreview(name, url, {
							width: 400,
							height: 400
						});
					}else{
						new iframeDialog(name, url, {
							width: 500,
							frameHeight: 500,
							modal: true,
							onFrameLoad : function(e){								
								var h = $E('div.iframe-preview iframe').contentWindow.document.body.innerHTML;								
								var tmpDiv = new Element('div').setHTML(h);
								
								function toRelative(s) {
									s = tinyMCEPopup.editor.documentBaseURI.toRelative(s);
									return s.replace(/^administrator\//, '');
								}
								
								$ES('img, embed', tmpDiv).each(function(el){
									var s = toRelative(el.getProperty('src'));

									if (!/http(s)?:\/\//.test(s)) {
										s = string.path(this.getSite(), s);
									}
									el.setProperty('src', s);
								}.bind(this));
								
								$ES('a, area', tmpDiv).each(function(el){
									var s = toRelative(el.getProperty('href'));
									if (!/http(s)?:\/\//.test(s)) {
										s = string.path(this.getSite(), s);
									}
									el.setProperty('href', s);
								}.bind(this));
								
								$ES('object', tmpDiv).each(function(el){
									$E('param[name=movie], param[name=src]', el).each(function(c){
										var s = toRelative(c.getProperty('value'));
										if (!/http(s)?:\/\//.test(s)) {
											s = string.path(this.getSite(), s);
										}
										c.setProperty('value', s);
									}.bind(this));
								}.bind(this));
								
								$E('div.iframe-preview iframe').contentWindow.document.body.innerHTML = tmpDiv.innerHTML;
								
							}.bind(this)
						});
					}
				}
				break;
			case 'upload':
				this._dialog['upload'] = new uploadDialog({
					extended : this.options.upload,
					onOpen : function(){
						// Set hidden dir value to current dir
						$('upload-dir').value = this._dir;
						// Set overwrite options
						var o = {
							'overwrite' : new Option(tinyMCEPopup.getLang('dlg.overwrite', 'Overwrite file'), 0),
							'unique'	: new Option(tinyMCEPopup.getLang('dlg.unique', 'Create unique name'), 1)
						}
						var s = $('upload-overwrite');
						var x = this.options.upload.conflict.split('|');
						
						x.each(function(e){
							s.options[s.options.length] = o[e];				
						});
						o = null;
						// Initialize uploader
						this.uploader = new Uploader('upload-queue', {															 
							url		:	$E('form').getProperty('action'),
							field	:	$E('input[name^=Filedata]'),
							size	:	this.options.upload.size,
							limit	: 	this.options.upload.limit,
							filter	:	this.options.upload.types,
							fileComplete : function(file, name){
								// Set uploaded files
								this.addReturnedItem(string.safe(name));
							}.bind(this),
							uploadComplete : function(){
								$('upload-submit').disabled = false;
								// Reset action
								this.action = $E('form').action;
								// Refresh file list
								this.getList();
							}.bind(this)
						});
						this.options.upload.onLoad.delay(10);
					}.bind(this),
					onUpload : function(){
						if(this.uploader.uploading) return false;
						this.uploader.upload();
						return false;
					}.bind(this)
				});
				break;
			case 'folder_new':
				this._dialog['folder_new'] = new Prompt(tinyMCEPopup.getLang('dlg.folder_new', 'New Folder'), {
					text: tinyMCEPopup.getLang('dlg.name', 'Name'),
					onConfirm: function(){
						var folder = $('prompt').value;
						if(folder){
							this.setLoader();
							this.xhr('folderNew', [dir, string.safe(folder)], function(o){		  															  
								if(!o.error){
									this.fireEvent('onFolderNew');
									this.refreshList();
									this._dialog['folder_new'].close();
								}else{
									this.raiseError(o.error);
								}
							})
						}
					}.bind(this)
				});
				break;
			// Cut / Copy operation
			case 'copy':
			case 'cut':
				this._pasteaction 	= name;
				this._pastefiles 	= list;
				this.showButton(this.getButton('file', 'paste').element, true);
				break;
			// Paste the file
			case 'paste':
				var fn = (this._pasteaction == 'copy') ? 'fileCopy' : 'fileMove';
				this.setLoader();
				this.xhr(fn, [this._pastefiles, dir], function(o){		  															  
					if(!o.error){
						this.fireEvent('onPaste');
						this.refreshList();
						this.clearPaste();
					}else{
						this.raiseError(o.error);
					}
				}.bind(this))
				break;
			// Delete a file or folder
			case 'delete':
				var msg = tinyMCEPopup.getLang('dlg.delete_folder_alert', 'Delete Folder?');
				var fn 	= 'folderDelete';
				if(type == 'file'){
					msg = tinyMCEPopup.getLang('dlg.delete_file_alert', 'Delete Files(s)?');
					fn 	= 'fileDelete';	
				}
				this._dialog['confirm'] = new Confirm(msg, function(state){
						if(state){
							this.setLoader();
							this.xhr(fn, list, function(o){		  															  
								if(!o.error){
									if(fn == 'folderDelete'){
										this.fireEvent('onFolderDelete', list);
									}else{
										this.fireEvent('onFileDelete');	
									}
									this.refreshList();
								}else{
									this.raiseError(o.error);
								}
							})
						}
					}.bind(this)
				);
				break;
			// Rename a file or folder
			case 'rename':
				var msg = tinyMCEPopup.getLang('dlg.rename_folder', 'Rename Folder');
				var fn 	= 'folderRename';
				var v	= string.basename(list);
				if(type == 'file'){
					msg = tinyMCEPopup.getLang('dlg.rename_file', 'Rename File');
					fn 	= 'fileRename';
					v	= string.basename(string.stripExt(list));
				}
				this._dialog['rename'] = new Prompt(msg, {
					text: tinyMCEPopup.getLang('name', 'Name'),
					value : v,
					onConfirm : function(){
						var name = string.safe($('prompt').value);
						this._dialog['confirm'] = new Confirm(tinyMCEPopup.getLang('dlg.rename_alert', 'Renaming files/folders will break existing links. Continue?'), function(state){
							if(state){
								this.setLoader();
								this.xhr(fn, [list, name], function(o){		  															  
									if(!o.error){
										this.resetManager();
										if(fn == 'folderRename'){
											this.addReturnedItem(string.path(this._dir, name));
											this.fireEvent('onFolderRename', [list, string.path(this._dir, name)]);
										}else{
											this.addReturnedItem(string.path(this._dir, name + '.' + string.getExt(list)));
											this.fireEvent('onFileRename');	
										}
										this._dialog['rename'].close();
										this.getList();
									}else{
										this.raiseError(o.error);
									}
								});
							}
						}.bind(this));
					}.bind(this)
				});
				break;
		}
	},
	/**
	 * Show an error dialog
	 * @param {String} error
	 */
	raiseError : function(error){
		this._dialog['alert'] = new Alert(error, {
			onClose: function(){
				this.refreshList();	
			}.bind(this)
		});
	},
	/**
	 * Add an array of actions
	 * @param {Object} actions
	 */
	addActions : function(actions){
		$each(actions, function(e){
			this.addAction(e);
		}.bind(this));
	},
	/**
	 * Add an action to the Manager
	 * @param {Object} options
	 */
	addAction : function(options){
		var name 	= options.name;
		var action	= eval(options.action) || this.execCommand;
		var atn = new Element('div', {'id': name, 'title': options.title}).addClass('action').addClass(name).setStyles({
			'cursor': 'pointer'
		});
		if(options.icon){
			btn.setStyle('background-image', string.path(this.getPluginUrl(), options.icon));	
		}
		if(options.name){
			atn.addEvent('click', function(){
				action.pass([name], this)();														   
			}.bind(this))	
		}
		if(window.ie){
			atn.addEvent('mouseover', function(){
				atn.addClass('hover');   
			}).addEvent('mouseout', function(){
				atn.removeClass('hover');   
			});
		}
		this._actions[name] = atn;
		$(this.options.dialog.actions).adopt(atn);
	},
	/**
	 * Get an action by name
	 * @param {String} name
	 */
	getAction : function(name){
		return this._actions[name];
	},
	/**
	 * Add an array of buttons to the Manager
	 * @param {Object} btns
	 */
	addButtons : function(btns){
		$each(btns.folder, function(e){
			this.addButton(e, 'folder');
		}.bind(this));
		$each(btns.file, function(e){
			this.addButton(e, 'file');
		}.bind(this));
	},
	/**
	 * Add a button to the Manager
	 * @param {Object} options
	 * @param {String} type
	 */
	addButton : function(options, type){
		var action	= options.action || this.execCommand;
		var btn = new Element('div').setProperty('title', options.title).addClass('button').addClass(options.name).addClass('hide').setStyles({
			'cursor': 'pointer'
		});
		if(options.icon){
			btn.setStyle('background-image', string.path(this.getPluginUrl(), options.icon));	
		}
		if(options.name){
			var n = options.name;
			btn.addEvent('click', function(){
				if(this._selectedItems){
					eval(action).pass([n, type], this)();
				}
			}.bind(this))	
		}
		if(window.ie){
			btn.addEvent('mouseover', function(){
				btn.addClass('hover');   
			}).addEvent('mouseout', function(){
				btn.removeClass('hover');   
			});
		}
		this._buttons[type].include({'name': options.name, 'element': btn, 'trigger': options.trigger, 'multiple': options.multiple});
		$(this.options.dialog.buttons).adopt(btn);
	},
	/**
	 * Hide all buttons
	 */
	hideAllButtons : function(){
		$$('div.button').each(function(e){
			this.hideButton(e);   
		}.bind(this));
	},
	/**
	 * Hide buttons by type
	 * @param {String} type The button type
	 */
	hideButtons : function(type){
		this._buttons[type].each(function(e){
			this.hideButton(e.element);
		}.bind(this));
	},
	/**
	 * Hide a button
	 * @param {String} button The button to hide
	 */
	hideButton : function(button){
		if(button){
			if(button.hasClass('show')){
				button.removeClass('show');
				button.addClass('hide');
			}
		}
	},
	/**
	 * Show all buttons by type
	 * @param {String} type The button type to show
	 */
	showButtons : function(type){
		this.hideAllButtons();
		this._buttons[type].each(function(e){
			if(!e.trigger){
				this.showButton(e.element, e.multiple);
			}
		}.bind(this));
	},
	/**
	 * Show a button
	 * @param {String} button The button to show
	 * @param {Boolean} multiple Whether a button is a multiple selection action
	 */
	showButton : function(button, multiple){
		if(button){
			if(button.hasClass('hide')){
				button.removeClass('hide');
				button.addClass('show');
			}
			if(this._selectedItems.length > 1 && !multiple){
				this.hideButton(button);
			}
		}
	},
	/**
	 * Get a button
	 * @param {String} type The button type
	 * @param {String} name The button name
	 */
	getButton : function(type, name){
		var btn;
		this._buttons[type].each(function(el){
			if(el.name == name){
				btn = el;
			}
		});
		return btn;
	},
	/**
	 * Determine whether an item is selected
	 * @param {Object} el The list item
	 */
	isSelectedItem : function(el){
		// Quick check
		if($type(el) == 'element'){
			return el.hasClass('selected') && this._selectedItems.contains(el);
		}
		// Check on element or element title/name
		this._selectedItems.each(function(e){
			return e.title == el;
		});
	},
	/**
	 * Deselect all list items
	 */
	selectNoItems : function(){
		this._selectedItems.each(function(el){
			if($(el)){
				el.removeClass('selected');
			}
		});
		this._selectedItems = [];
		this._activeItem 	= 0;
		$(this.options.dialog.info).empty();
		$(this.options.dialog.comments).empty();
		// Shortcut for nav
		var nav = this.options.dialog.nav;
		[nav + '-left', nav + '-right', nav + '-text'].each(function(el){
			$(el).setStyle('visibility', 'hidden');											  
		});
		this.hideAllButtons();
	},
	/**
	 * Select an array of items
	 * @param {Array} items The array of items to select
	 * @param {Boolean} show Show item properties
	 */	
	selectItems : function(items, show){
		this._selectedItems.merge(items).each(function(el){
			if($(el)){
				el.addClass('selected');
			}
		}.bind(this));	
		if(show){
			this.showSelectedItems();
		}
	},
	/**
	 * Remove items from a selection
	 * @param {Array} el Array of elements to remove
	 * @param {Boolean} show Show remaining item properties
	 */
	removeSelectedItems : function(el, show){
		el.each(function(o){
			if(o){
				o.removeClass('selected');
				this._selectedItems.remove(o);	
			}
		}, this);
		if(show){
			this.showSelectedItems();
		}
	},
	/**
	 * Return selected items by key or all selected items
	 * @param {String} key Item key
	 */
	getSelectedItems : function(key){
		return this._selectedItems[key] || this._selectedItems;
	},
	/**
	* Process a selection click
	* @param {String} e The click event.
	* @param {Boolean} multiple Allow multiple selections.
	*/
	setSelectedItems : function(e, multiple){
		e = new Event(e);
		// the selected element
		var el = e.target;
			
		// If not li element, must be a so get parent li
		if(el.getTag() != 'li') el = el.getParent();
		
		// Prevent double clicking
		if (this.isSelectedItem(el) && this._selectedItems.length == 1) {
			e.stop();
			return false;
		}
		
		// if folder selected, remove file selections & select folder
		if(el.hasClass('folder')){
			if(this.isSelectedItem(el)){
				//return this.removeSelectedItems([el], true);	
			}
			this.selectNoItems();
			this.selectItems([el], e.target.getTag() == 'a' ? false : true);
		// selection hit is a file
		}else{
			// Get file items
			var items = $ES('li', 'file-list');
			// Single click
			if(!e.control && !e.shift || !multiple){
				this._selectedIndex = items.indexOf(el);
				if(this.isSelectedItem(el) && this._selectedItems.length == 1){
					//return this.removeSelectedItems([el], true);
				}			
				// deselect all
				this.selectNoItems();
				if(this._selectedItems.length == 0){
					if(this.isSelectedItem(el)){
						this.removeSelectedItems([el], true);
					}else{
						this.selectItems([el], true);
					}
				}
			// ctrl & shift
			}else if(multiple && (e.control || e.shift)){
				// Remove selected folder
				if(this._selectedItems.length == 1 && this._selectedItems[0].hasClass('folder')){
					this.removeSelectedItems([this._selectedItems[0]], true);
				}			
				// ctrl
				if(e.control){
					this._selectedIndex = items.indexOf(el);
					if(this.isSelectedItem(el)){
						this.removeSelectedItems([el], true);
					}else{
						this.selectItems([el], true);
					}
				}
				// shift
				if(e.shift){
					if(this._selectedItems.length){
						// selected item index
						var si 		= this._selectedIndex;
						// click item index
						var ci		= items.indexOf(el);				
						var selection 	= [];
						
						// Clear selection
						this.selectNoItems();
						// Clicked item further up list than selected item
						if(ci > si){
							for(var i=ci; i>=si; i--){
								selection.include(items[i]);
							}
						}else{
							// Clicked item further down list than selected item
							for(var i=si; i>=ci; i--){
								selection.include(items[i]);
							}	
						}
						this.selectItems(selection, true);
					}else{
						this.selectItems([el], true);	
					}
				}
			}
		}
	},
	/**
	* Show the selected items' details
	*/
	showSelectedItems : function(){
		var n = this._selectedItems.length;
		if(!n){
			this.resetManager();
		}else{
			if(this._selectedItems[0].hasClass('folder')){
				this.showFolderDetails();				
			}else{
				this._activeItem = n-1;
				this.showFileDetails();
			}
		}
	},
	/**
	* Select an item (file) by name
	* @param {String} name The file name.
	*/
	selectItemsByName : function(names, type){
		var items = [];
		if(!type) type = 'file';
		if($type(names) == 'string'){
			names = [names];
		}
		names.each(function(e){
			if(e){
				items.merge($ES('li[title='+ decodeURIComponent(e) +']', type + '-list'));
			}
		}.bind(this));
		if(items.length){
			// Set item index for last selection
			this._selectedIndex = $ES('li', type + '-list').indexOf(items[items.length-1]);
			// Scroll to first item in list
			new Fx.Scroll(this.options.dialog.list, {
				wait: false,
				duration: 1000
			}).toElement(items[0]);
		}
		// Select items and display properties
		this.selectItems(items, true);
	},
	/**
	* Serialize the current item selection, add current dir to path
	*/
	serializeSelectedItems : function(){
		var s = [];
		this._selectedItems.each(function(e){
			s.include(string.path(this._dir, e.title));
		}.bind(this));
		return s.join(',');
	},
	/**
	 * Show a file number
	 */
	showFileNumber : function(){
		var n = this._selectedItems.length;
		// Shortcut for nav
		var nav = this.options.dialog.nav;
		if(this._activeItem){
			$(nav + '-left').setStyle('visibility', 'visible');	
		}else{
			$(nav + '-left').setStyle('visibility', 'hidden');	
		}
		if(this._activeItem + 1 < n){
			$(nav + '-right').setStyle('visibility', 'visible');	
		}else{
			$(nav + '-right').setStyle('visibility', 'hidden');	
		}
		$(nav + '-text').setStyle('visibility', 'visible').setHTML(this._activeItem + 1 + ' of ' + n);
	},
	/**
	 * Show a files properties / details
	 */
	showFileDetails : function(){		
		var n = this._selectedItems.length;
		// Shortcut for nav
		var nav = this.options.dialog.nav;
		if(n < 2){
			[nav + '-left', nav + '-right', nav + '-text'].each(function(el){
				$(el).setStyle('visibility', 'hidden');											  
			});
		}
		this.getFileDetails();
		this.fireEvent('onFileDetails');
		
		if(n > 1){
			this.showFileNumber(n);
		}
		this.showButtons('file');
	},
	/**
	 * Show a folder's details / properties
	 */
	showFolderDetails : function(){
		this.getFolderDetails();		
		this.showButtons('folder');
	},
	/**
	 * Get a folder's details properties
	 */
	getFolderDetails : function(){
		var title 	= string.basename(this._selectedItems[0].title);
		
		var info = new Element('dl').adopt([
			new Element('dt').setHTML(title),					
			new Element('dd').setHTML(tinyMCEPopup.getLang('dlg.folder', 'Folder')),
			new Element('dd').setProperty('id', 'loader')
		]);		
		$(this.options.dialog.info).empty().adopt(info);
		
		var comments = [];
		if($(this._selectedItems[0]).hasClass('notwritable')){
			comments.include(
				new Element('dd').addClass('comments').addClass('folder').addClass('notwritable').adopt(
					new Element('span').addClass('hastip').setProperty('title', tinyMCEPopup.getLang('dlg.notwritable_desc', 'Unwritable')).setHTML(tinyMCEPopup.getLang('dlg.notwritable', 'Unwritable'))
				)
			)
		}
		if($(this._selectedItems[0]).hasClass('notsafe')){
			comments.include(
				new Element('dd').addClass('comments').addClass('folder').addClass('notsafe').adopt(
					new Element('span').addClass('hastip').setProperty('title', tinyMCEPopup.getLang('dlg.bad_name_desc', 'Bad file or folder name')).setHTML(tinyMCEPopup.getLang('dlg.bad_name', 'Bad file or folder name'))
				)
			)
		}
		
		var path = string.path(this._dir, this._encode(this._selectedItems[0].title));
		
		this.xhr('getFolderDetails', path, function(o){
			if($('loader')){
				$('loader').remove();
			}
			var props = [];
			$each(o, function(v, k){
				props.include(
					new Element('dd').setHTML(tinyMCEPopup.getLang('dlg.' + k, k) + ': ' + v)			  
				)					   
			}.bind(this));
			info.adopt(props);
			if(comments.length){
				$(this.options.dialog.comments).empty().adopt(
					new Element('dl').adopt(
						new Element('dt').setHTML(tinyMCEPopup.getLang('dlg.comments', 'Comments'))					
					).adopt(comments)			
				);
			}
			this.addToolTip($ES('span.hastip', $ES('dd.comments')));
			this.fireEvent('onFolderDetails', [o]);
		}.bind(this));
	},
	/**
	 * Get a files details / properties
	 */
	getFileDetails : function(){
		var t = this;
		var file 		= this._selectedItems[this._activeItem];
		var title 		= $(file).title;
			
		// Info list
		var info = new Element('dl').adopt([
			new Element('dt').setHTML(string.stripExt(title)), 
			new Element('dd').setHTML(string.getExt(title).toUpperCase() + ' ' + tinyMCEPopup.getLang('dlg.file', 'File')), 
			new Element('dd').setProperty('id', 'info-properties'), 
			new Element('dd').setProperty('id', 'info-preview'),
			new Element('dd').setProperty('id', 'loader')
		]);
		// create tmp div
		var div = new Element('div').adopt(info);
		$(this.options.dialog.info).setHTML(div.innerHTML);
		//div.remove();

		this.xhr('getFileDetails', string.path(this._dir, this._selectedItems[this._activeItem].title), function(o){
			if ($('loader')) {
				$('loader').remove();
			}
			
			$('info-properties').empty();
			
			$each(o, function(v, k){
				// If a button trigger or triggers
				if (o.trigger) {
					o.trigger.each(function(t){
						if (t !== '') {
							var b = this.getButton('file', t);
							if (b) {
								this.showButton(b.element, b.multiple);
							}
						}
					}.bind(this));
				}
				if (!/(trigger|preview)/i.test(k)) {
					$('info-properties').adopt(new Element('dd').setProperty('id', 'info-' + k.toLowerCase()).setHTML(tinyMCEPopup.getLang('dlg.' + k, k) + ': ' + v));
				}
			}.bind(this));

			// Preview
			if (o.preview) {										
				$('info-preview').empty().adopt(new Element('dl').adopt([
					new Element('dt').setHTML(tinyMCEPopup.getLang('dlg.preview', 'Preview') + ': '), 
					new Element('dd').setStyle('height', 80).addClass('loader')
				]));
				
				var src = this._encode(o.preview.src);
				
                var img = new Image();
                img.onload = function() {
                    var w = img.width;
                    var h = img.height;
					
					// set dimensions
					if (typeof o.dimensions != 'undefined' && o.dimensions == '') {
						if ($('info-dimensions')) {
							$('info-dimensions').setHTML(tinyMCEPopup.getLang('dlg.dimensions', 'Dimensions') + ': ' + w + ' x ' + h);
						}
					}
                    
                    if (w > 100) {
                        h = h * (100 / w);
                        w = 100;
                        if (h > 80) {
                            w = w * (80 / h);
                            h = 80;
                        }
                    } else if (h > 80) {
                        w = w * (80 / h);
                        h = 80;
                        if (w > 100) {
                            h = h * (100 / w);
                            w = 100;
                        }
                    }
                    $E('dd', $('info-preview')).removeClass('loader').adopt(new Element('img', {
                        src: src,
                        width: Math.round(w),
                        height: Math.round(h),
                        title: 'Preview'
                    }));
                }
				img.onerror = function(){
					$E('dd', $('info-preview')).removeClass('loader').addClass('preview-error');
					alert('error');
				}
				img.src = src;
			}
			// Comments
			var comments = [];
			if (/not(writable|safe)/i.test($(file).className)) {
				comments.include(new Element('dt').setHTML(tinyMCEPopup.getLang('dlg.comments', 'Comments')));
				// not writable
				if ($(file).hasClass('notwritable')) {
					comments.include(new Element('dd').addClass('comments').addClass('file').addClass('notwritable').adopt(new Element('span').addClass('hastip').setProperty('title', tinyMCEPopup.getLang('dlg.notwritable_desc', 'Unwritable')).setHTML(tinyMCEPopup.getLang('dlg.notwritable', 'Unwritable'))))
				}
				// not safe
				if ($(file).hasClass('notsafe')) {
					comments.include(new Element('dd').addClass('comments').addClass('file').addClass('notsafe').adopt(new Element('span').addClass('hastip').setProperty('title', tinyMCEPopup.getLang('dlg.bad_name_desc', 'Bad file or folder name')).setHTML(tinyMCEPopup.getLang('dlg.bad_name', 'Bad file or folder name'))))
				}
			}
			if (comments.length) {
				$(this.options.dialog.comments).empty().adopt(new Element('dl').adopt(comments))
			}
			// Add tooltip
			this.addToolTip($ES('span.hastip', $ES('dd.comments')));
			// Fire event
			this.fireEvent('onFileDetails', [o]);
		}.bind(this));
	},
	_encode : function(s) {
		s = decodeURIComponent(s);
		return encodeURIComponent(s).replace(/%2F/gi, '\/');
	}
});
// Implement Manager class
Manager.implement(new Events, new Options);