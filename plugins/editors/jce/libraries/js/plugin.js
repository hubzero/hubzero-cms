/**
* @version		$Id: plugin.js 49 2009-05-28 10:02:46Z happynoodleboy $
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
Class: Plugin
	Base plugin class for creating a JCE plugin object.

Arguments:
	options - optional, an object containing options.

Options:
	site - the base site url.
	plugin - the plugin name
	lang - the language code, eg: en.
	params - parameter object.

Example:
	var advlink = new Plugin('advlink', {params: {'key': 'value'}});
*/
var Plugin = new Class({	
	_queue 			: [],
	// Dialog box default
	_dialog 		: [],
	// Arbitrary variable
	_vars 			: null,
	// Current plugin
	_plugin			: null,
	getOptions : function(){
		return {
			site 	: tinyMCEPopup.editor.documentBaseURI.getURI(true),
			lang 	: 'en',
			params 	: {},
			alerts 	: null
		}
	},
	/**
	 * Initialize the class
	 * @param {Object} plugin
	 * @param {Object} options
	 */
	initialize : function(plugin, options){
		this.setOptions(this.getOptions(), options);		
		this._plugin = plugin;

		// fallback for no plugin set
		if(!this._plugin){
			var q = string.query(document.location.href);
			this._plugin = q['plugin'];
		}
		// show any alert dialogs
		this.showAlerts();
		// initialize tooltips
		this.initToolTip();
	},
	/**
	 * Return site url option
	 * @param {String} The site url variable
	*/
	getSite : function(){
		return this.options.site;	
	},
	/**
	 * Store a custom plugin parameter
	 * Example: 'animals', ['dog', 'cat', 'mouse']
	 * @param {string} The parameter key/name
	 * @param {string/array/object} The value
	*/
	setParam : function(p, v){
		this.options.params[p] = v;
	},
	/**
	 * Store custom plugin parameters
	 * Example: {'animals': ['dog', 'cat', 'mouse']}
	 * @param {Object} The parameters object
	*/
	setParams : function(p){
		for(n in p){
			this.setParam(n, p[n]);
		}
	},
	/**
	 * Return a custom plugin parameter
	 * @param {string} The parameter key/name
	*/
	getParam : function(p){
		return this.options.params[p] || false;
	},
	/**
	 * Set the plugin as current
	 * @param {string} The plugin name
	*/
	setPlugin : function(p){
		this._plugin = p;
	},
	/**
	 * Return the current plugin
	 * @return {string} The plugin name
	*/
	getPlugin : function(){
		return this._plugin;	
	},
	/**
	 * Return a full resource url
	 * @param {string} The url type, eg: img, plugin
	 * @return {string} The url
	*/
	getUrl : function( type ){
		if( type == 'plugins' ){
			type = 'tiny_mce/plugins/' + this.getPlugin();
		}
		return string.path(this.options.site, 'plugins/editors/jce/' + type);
	},
	/**
	 * Return a full image url
	 * @param {string} The image name
	 * @return {string} The url
	*/
	getImage : function(name){
		var parts 	= name.split('.');
		var path 	= parts[0].replace(/[^a-z0-9-_]/i, '');
		var file 	= parts[1].replace(/[^a-z0-9-_]/i, '');
		var ext 	= parts[2].replace(/[^a-z0-9-_]/i, '');
		
		return this.getUrl(path) + '/img/' + file + '.' + ext;
	},
	/**
	 * Resolve a TinyMCE language string
	 * @param {string} The variable name
	 * @param {string} The default translation
	 * @return {string} The language string
	*/
	getLang : function(s, dv){
		return tinyMCEPopup.getLang(s, dv);
	},
	/**
	 * Loads a TinyMCE plugin or theme dialog language file. Requires asset.js
	 * @param {string} The variable name
	 * @param {string} The default translation
	 * @return {string} The language string
	*/
	loadLanguage : function(name){
		var path = '', parts = '', file = '';
		if(name){
			parts 	= name.split('.');
			path 	= parts[0].replace(/[^a-z0-9-_]/i, '');
			file 	= parts[1].replace(/[^a-z0-9-_]/i, '');
			path 	= path + '/' + file + '/';
		}
		var u = this.options.site + 'plugins/editors/jce/tiny_mce/' + path + 'langs/' + this.options.lang + '_dlg.js';
		new Asset.javascript(u);
	},
	/**
	 * Set a variable
	 * @param {Object} vars
	 */
	setVars: function(vars){
		this._vars = vars;
	},
	/**
	 * Get a variable
	 */
	getVars: function(){
		return this._vars;
	},
	/**
	* Add a dialog object
	* @param {String} The dialog name
	* @param {String} The dialog object
	*/
	addDialog : function(name, dialog){
		this._dialog[name] = dialog;
	},
	/**
	* Get a dialog object
	* @param {String} The dialog name
	* @return the dialog object
	*/
	getDialog : function(name){
		return this._dialog[name] || '';
	},
	/**
	* Remove a dialog object
	* Shortcut for closing a dialog too
	* @param {String} The dialog name
	*/
	removeDialog : function(name){
		if(typeof this._dialog[name].close() != 'undefined'){
			this._dialog[name].close();	
		}
		delete this._dialog[name];
	},
	/**
	 * Open help window for current language
	*/
	openHelp : function(type){
		if(!type) type = 'standard';
		tinyMCE.activeEditor.windowManager.open({
			url : tinyMCEPopup.getParam('site_url') + 'index.php?option=com_jce&view=editor&task=help&lang='+ this.options.lang +'&plugin='+ this._plugin +'&type='+ type,
			width : 780,
		    height : 560,
		    resizable : 1,
            inline : 1,
        	close_previous : 0
		});
	},
	/**
	 * 
	 * @param {Object} fn
	 * @param {Object} cb
	 */
	iframe : function(fn, cb){
		new IFrame({
			auto: true,
			action: fn,
			onComplete: function(o){
				if(o.error){
					alert(o.error);
				}else{
					r = o.result || {error: false};
					if(cb){
						cb.pass(r, this)();	
					}else{
						return r;	
					}
				}	
			}
		});
	},
	/**
	 * 
	 * @param {Object} s
	 */
	_encode : function(s){
		s = s.replace(new RegExp('"', 'g'), '\\"');
		s = s.replace(new RegExp("'", 'g'), "\\'");
		
		return s;
	},
	/**
	 * Legcay XHR function. Pass to Editor.xhr
	 * @param {Object} fn
	 * @param {Object} args
	 * @param {Object} cb
	 */
	xhr : function(fn, args, cb) {
		return Editor.xhr.request(fn, args, cb, this);
	},
	/**
	 * Alerts. Requires window.js
	*/
	showAlerts : function(){
		var alerts = this.options.alerts || [];
		if(alerts.length){
			var h = '<dl class="alert">';
			alerts.each(function(a){
				h += '<dt class="' + a['class'] + '">' + a['title'] + '</dt><dd>' + a['text'] + '</dd>';				
			});
			h += '</dl>'
			var n = new Alert(h, {height: 150 + alerts.length * 50});
		}
	},
	/**
	 * 
	 * @param {Object} elms
	 * @param {Object} options
	 */
	initToolTip : function(elements, options){
		if(!options) options = {};
		$extend(options, {
			className : 'tooltip',
			offsets: {'x': 16, 'y': 16}
		});
		this.tooltip = new JCETips(elements || $$('.hastip'), options);	
	},
	/**
	 * 
	 * @param {Object} el
	 */
	addToolTip : function(elements){
		this.tooltip.add(elements);
	},
	/**
	 * 
	 * @param {Object} el
	 */
	removeToolTip : function(elements){
		this.tooltip.remove(elements);
	}
});
Plugin.implement(new Events, new Options);
/* IFrame class for pseudo ajax/json stuff */
var IFrame = new Class({
	getOptions : function(){
		return {			
			form: $E('form'),
			frame: 'iframe',
			action: '',
			cleanup : true,
			auto : false,
			onStart: Class.empty,
			onComplete: Class.empty
		};
	},
	initialize : function(options){
		this.setOptions(this.getOptions(), options);
		if (this.options.initialize) this.options.initialize.call(this);
		// Remove existing frame
		this.remove();
		// Create IFrame
		this.frame 	= new Element('iframe', {
			'src': 'about:blank', 
			'name': this.options.frame,
			'id': this.options.frame
		}).setStyle('display', 'none').injectInside($E('form'));
		// Create action
		if(this.options.action){
			this.action = new Element('input', {
				'type' 	: 'hidden',
				'id' 	: this.options.frame + '_action',
				'name'	: 'action',
				'value'	: this.options.action
			}).injectInside($E('form'));
		}
		// Change frame name
		if(window.ie){
			window.frames[this.frame.id].name = this.frame.name;
		}
		this.options.form.setAttribute('target', this.frame.name);
		// Add submit event
		this.options.form.addEvent('submit', function(){
			this.fireEvent('onStart');
		}.bind(this));
		// Add load event
		this.frame.addEvent('load', function(){
			var f 	= $(this.frame);
			var el 	= f.contentWindow.document || f.contentDocument || window.frames[f.id].document;
			if(el.location.href == 'about:blank') return;
			var res = el.body.innerHTML;
			if(res !== ''){
				this.fireEvent('onComplete', Json.evaluate(res, true));
			}
			// Remove existing frame
			if(this.options.cleanup){
				this.remove();
			}
		}.bind(this))
		// Auto submit
		if(this.options.auto){
			this.submit();	
		}
	},
	remove : function(){
		// Remove existing frame
		if($(this.options.frame)){
			$(this.options.frame).remove();	
		}
		// Remove existing action
		if($(this.options.frame + '_action')){
			$(this.options.frame + '_action').remove();
		}
	},
	submit : function(){
		this.fireEvent('onStart');
		this.options.form.submit() || $E('form').submit();
	}
});
IFrame.implement(new Options, new Events);