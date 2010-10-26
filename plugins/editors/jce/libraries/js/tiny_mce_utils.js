/**
 * Makes select boxes editable.
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */
var TinyMCE_EditableSelects = {
	editSelectElm : null,

	init : function() {
		var nl = document.getElementsByTagName("select"), i, d = document, o, oc;

		for (i=0; i<nl.length; i++) {
			if (nl[i].className.indexOf('mceEditableSelect') != -1) {
				o = new Option('(' + tinyMCEPopup.getLang('dlg.select_value', 'Add Value') + ')', '__mce_add_custom__');

				o.className = 'mceAddSelectValue';

				nl[i].options[nl[i].options.length] = o;
				nl[i].tmp = nl[i].onchange;
				nl[i].onchange = function(){
					if(this.value == '__mce_add_custom__'){
						TinyMCE_EditableSelects.onChangeEditableSelect(this);
					}else{
						if(this.tmp){
							this.tmp.call();
						}
					}
				}
			}
		}
	},
	onChangeEditableSelect : function(se) {
		var d = document, ne;

		if (se.options[se.selectedIndex].value == '__mce_add_custom__') {
			ne = d.createElement("input");
			ne.id = se.id + "_custom";
			ne.name = se.name + "_custom";
			ne.type = "text";

			ne.style.width = se.offsetWidth + 'px';
			se.parentNode.insertBefore(ne, se);
			se.style.display = 'none';
			ne.focus();
			ne.onblur = TinyMCE_EditableSelects.onBlurEditableSelectInput;
			TinyMCE_EditableSelects.editSelectElm = se;
		}
	},

	onBlurEditableSelectInput : function() {
		var se = TinyMCE_EditableSelects.editSelectElm;

		if (se) {			
			if (se.previousSibling.value != '') {
				se.options[se.options.length] = new Option(se.previousSibling.value, se.previousSibling.value);
				se.selectedIndex = se.options.length - 1;
			} else {
				for (var i=0; i<se.options.length; i++) {
					var o = se.options[i];
					if (o.value === '') {
						o.selected = true;
					} else{
						o.selected = false;
					}
				}
			}
			
			if(se.tmp && se.tmp != 'undefined'){
				se.tmp.call();	
			}

			se.style.display = 'inline';
			se.parentNode.removeChild(se.previousSibling);
			TinyMCE_EditableSelects.editSelectElm = null;
		}
	}
};
TinyMCE_Utils = {
	getColorPickerHTML : function(id){
		var h = "";

		h += '<a id="' + id + '_pick_link" href="javascript:;" onclick="tinyMCEPopup.pickColor(event,\'' + id +'\');" onmousedown="return false;" class="pickcolor">';
		h += '<span id="' + id + '_pick" title="' + tinyMCEPopup.getLang('browse') + '"></span></a>';

		return h;
	},
	updateColor : function(parent){
		if(typeof parent == 'string'){
			parent = document.getElementById(parent);	
		}
		document.getElementById(parent.id + '_pick').style.backgroundColor = parent.value;
	},
	setBrowserDisabled : function(id, state) {
		var img = document.getElementById(id);
		var lnk = document.getElementById(id + "_link");
	
		if (lnk) {
			if (state) {
				lnk.setAttribute("realhref", lnk.getAttribute("href"));
				lnk.removeAttribute("href");
				tinyMCEPopup.dom.addClass(img, 'disabled');
			} else {
				lnk.setAttribute("href", lnk.getAttribute("realhref"));
				tinyMCEPopup.dom.removeClass(img, 'disabled');
			}
		}
	},
	getBrowserHTML : function(id, target_form_element, type, prefix) {
		var option = prefix + "_" + type + "_browser_callback", cb, html;
		var cb, html;
	
		cb = tinyMCEPopup.getParam(option, tinyMCEPopup.getParam("file_browser_callback"));
	
		if (!cb){
			return "";
		}
	
		html = "";
		html += '<a id="' + id + '_link" href="javascript:TinyMCE_Utils.openBrowser(\'' + id + '\',\'' + target_form_element + '\', \'' + type + '\',\'' + option + '\');" onmousedown="return false;" class="browse">';
		html += '<span class="' + type + '" id="' + id + '" title="' + tinyMCEPopup.getLang('browse') + '"></span></a>';
	
		return html;
	},
	openBrowser : function(img_id, target_form_element, type, option){	
		if(document.getElementById(img_id).className != "mceButtonDisabled"){
			tinyMCEPopup.openBrowser(target_form_element, type, option);
		}
	},
	fillClassList : function(id) {
		var ed = tinyMCEPopup.editor, lst = dom.get(id), v, cl;
	
		if(v = tinyMCEPopup.getParam('theme_advanced_styles')){
			cl = [];
	
			tinymce.each(v.split(';'), function(v) {
				var p = v.split('=');
	
				cl.push({'title' : p[0], 'class' : p[1]});
			});
		}else{
			cl = ed.dom.getClasses();
		}
		
		tinymce.each(['jcepopup','jcetooltip'], function(o){
			lst.options[lst.options.length] = new Option(o, o);											  
		});
	
		if(cl.length > 0){
			tinymce.each(cl, function(o) {
				lst.options[lst.options.length] = new Option(o.title || o['class'], o['class']);
			});
		}
	}
}
/**
 * Moxiecode DHTML Tabs script.
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

function MCTabs() {
	this.settings = new Array();
};

MCTabs.prototype.init = function(settings) {
	this.settings = settings;
};

MCTabs.prototype.getParam = function(name, default_value) {
	var value = null;

	value = (typeof(this.settings[name]) == "undefined") ? default_value : this.settings[name];

	// Fix bool values
	if (value == "true" || value == "false")
		return (value == "true");

	return value;
};

MCTabs.prototype.displayTab = function(tab_id, panel_id) {
	var panelElm = document.getElementById(panel_id);
	var panelContainerElm = panelElm ? panelElm.parentNode : null;
	var tabElm = document.getElementById(tab_id);
	var tabContainerElm = tabElm ? tabElm.parentNode : null;
	var selectionClass = this.getParam('selection_class', 'current');

	if (tabElm && tabContainerElm) {
		var nodes = tabContainerElm.childNodes;

		// Hide all other tabs
		for (var i=0; i<nodes.length; i++) {
			if (nodes[i].nodeName == "LI")
				nodes[i].className = '';
		}

		// Show selected tab
		tabElm.className = 'current';
	}

	if (panelElm && panelContainerElm) {
		var nodes = panelContainerElm.childNodes;

		// Hide all other panels
		for (var i=0; i<nodes.length; i++) {
			if (nodes[i].nodeName == "DIV")
				nodes[i].className = 'panel';
		}

		// Show selected panel
		panelElm.className = 'current';
	}
};

MCTabs.prototype.getAnchor = function() {
	var pos, url = document.location.href;

	if ((pos = url.lastIndexOf('#')) != -1)
		return url.substring(pos + 1);

	return "";
};

// Global instance
var mcTabs = new MCTabs();
/**
 * Various form validation methods.
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

/**
	// String validation:

	if (!Validator.isEmail('myemail'))
		alert('Invalid email.');

	// Form validation:

	var f = document.forms['myform'];

	if (!Validator.isEmail(f.myemail))
		alert('Invalid email.');
*/

var Validator = {
	isEmail : function(s) {
		return this.test(s, '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$');
	},

	isAbsUrl : function(s) {
		return this.test(s, '^(news|telnet|nttp|file|http|ftp|https)://[-A-Za-z0-9\\.]+\\/?.*$');
	},

	isSize : function(s) {
		return this.test(s, '^[0-9]+(px|%)?$');
	},

	isId : function(s) {
		return this.test(s, '^[A-Za-z_]([A-Za-z0-9_])*$');
	},

	isEmpty : function(s) {
		var nl, i;

		if (s.nodeName == 'SELECT' && s.selectedIndex < 1)
			return true;

		if (s.type == 'checkbox' && !s.checked)
			return true;

		if (s.type == 'radio') {
			for (i=0, nl = s.form.elements; i<nl.length; i++) {
				if (nl[i].type == "radio" && nl[i].name == s.name && nl[i].checked)
					return false;
			}

			return true;
		}

		return new RegExp('^\\s*$').test(s.nodeType == 1 ? s.value : s);
	},

	isNumber : function(s, d) {
		return !isNaN(s.nodeType == 1 ? s.value : s) && (!d || !this.test(s, '^-?[0-9]*\\.[0-9]*$'));
	},

	test : function(s, p) {
		s = s.nodeType == 1 ? s.value : s;

		return s == '' || new RegExp(p).test(s);
	}
};

var AutoValidator = {
	settings : {
		id_cls : 'id',
		int_cls : 'int',
		url_cls : 'url',
		number_cls : 'number',
		email_cls : 'email',
		size_cls : 'size',
		required_cls : 'required',
		invalid_cls : 'invalid',
		min_cls : 'min',
		max_cls : 'max'
	},

	init : function(s) {
		var n;

		for (n in s)
			this.settings[n] = s[n];
	},

	validate : function(f) {
		var i, nl, s = this.settings, c = 0;

		nl = this.tags(f, 'label');
		for (i=0; i<nl.length; i++)
			this.removeClass(nl[i], s.invalid_cls);

		c += this.validateElms(f, 'input');
		c += this.validateElms(f, 'select');
		c += this.validateElms(f, 'textarea');

		return c == 3;
	},

	invalidate : function(n) {
		this.mark(n.form, n);
	},

	reset : function(e) {
		var t = new Array('label', 'input', 'select', 'textarea');
		var i, j, nl, s = this.settings;

		if (e == null)
			return;

		for (i=0; i<t.length; i++) {
			nl = this.tags(e.form ? e.form : e, t[i]);
			for (j=0; j<nl.length; j++)
				this.removeClass(nl[j], s.invalid_cls);
		}
	},

	validateElms : function(f, e) {
		var nl, i, n, s = this.settings, st = true, va = Validator, v;

		nl = this.tags(f, e);
		for (i=0; i<nl.length; i++) {
			n = nl[i];

			this.removeClass(n, s.invalid_cls);

			if (this.hasClass(n, s.required_cls) && va.isEmpty(n))
				st = this.mark(f, n);

			if (this.hasClass(n, s.number_cls) && !va.isNumber(n))
				st = this.mark(f, n);

			if (this.hasClass(n, s.int_cls) && !va.isNumber(n, true))
				st = this.mark(f, n);

			if (this.hasClass(n, s.url_cls) && !va.isAbsUrl(n))
				st = this.mark(f, n);

			if (this.hasClass(n, s.email_cls) && !va.isEmail(n))
				st = this.mark(f, n);

			if (this.hasClass(n, s.size_cls) && !va.isSize(n))
				st = this.mark(f, n);

			if (this.hasClass(n, s.id_cls) && !va.isId(n))
				st = this.mark(f, n);

			if (this.hasClass(n, s.min_cls, true)) {
				v = this.getNum(n, s.min_cls);

				if (isNaN(v) || parseInt(n.value) < parseInt(v))
					st = this.mark(f, n);
			}

			if (this.hasClass(n, s.max_cls, true)) {
				v = this.getNum(n, s.max_cls);

				if (isNaN(v) || parseInt(n.value) > parseInt(v))
					st = this.mark(f, n);
			}
		}

		return st;
	},

	hasClass : function(n, c, d) {
		return new RegExp('\\b' + c + (d ? '[0-9]+' : '') + '\\b', 'g').test(n.className);
	},

	getNum : function(n, c) {
		c = n.className.match(new RegExp('\\b' + c + '([0-9]+)\\b', 'g'))[0];
		c = c.replace(/[^0-9]/g, '');

		return c;
	},

	addClass : function(n, c, b) {
		var o = this.removeClass(n, c);
		n.className = b ? c + (o != '' ? (' ' + o) : '') : (o != '' ? (o + ' ') : '') + c;
	},

	removeClass : function(n, c) {
		c = n.className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' ');
		return n.className = c != ' ' ? c : '';
	},

	tags : function(f, s) {
		return f.getElementsByTagName(s);
	},

	mark : function(f, n) {
		var s = this.settings;

		this.addClass(n, s.invalid_cls);
		this.markLabels(f, n, s.invalid_cls);

		return false;
	},

	markLabels : function(f, n, ic) {
		var nl, i;

		nl = this.tags(f, "label");
		for (i=0; i<nl.length; i++) {
			if (nl[i].getAttribute("for") == n.id || nl[i].htmlFor == n.id)
				this.addClass(nl[i], ic);
		}

		return null;
	}
};