/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Only define the Hubzero namespace if not defined.
if (typeof(Hubzero) === 'undefined') {
	var Hubzero = {};
}

Hubzero.editors = {};
// An object to hold each editor instance on page
Hubzero.editors.instances = {};

/**
 * Generic submit form
 *
 * @param   string  task
 * @param   mixed   form
 * @return  void
 */
Hubzero.submitform = function(task, form) {
	if (typeof(form) === 'undefined') {
		form = document.getElementById('adminForm');

		if (!form) {
			form = document.adminForm;
		}
	} else {
		if (form instanceof jQuery) {
			form = form[0];
		}
	}

	if (typeof(task) !== 'undefined' && '' !== task) {
		form.task.value = task;
	}

	var event;
	if (document.createEvent) {
		event = document.createEvent('HTMLEvents');
		event.initEvent('submit', true, true);
	} else if (document.createEventObject) { // IE < 9
		event = document.createEventObject();
		event.eventType = 'submit';
	}

	// Submit the form.
	if (typeof form.onsubmit == 'function') {
		form.onsubmit();
	}
	else if (typeof form.dispatchEvent == "function") {
		form.dispatchEvent(event);
	}
	else if (typeof form.fireEvent == "function") {
		form.fireEvent(event);
	}
	form.submit();
};

/**
 * Default function. Usually would be overriden by the component
 *
 * @param   string  pressbutton
 * @return  void
 */
Hubzero.submitbutton = function(pressbutton) {
	Hubzero.submitform(pressbutton);
}

/**
 * Custom behavior for JavaScript I18N
 *
 * Allows you to call Hubzero.Lang.txt() to get a translated JavaScript string pushed in with Lang::script() in Hubzero.
 *
 * @return  mixed
 */
Hubzero.Lang = {
	strings: {},
	txt: function(key, def) {
		return typeof this.strings[key.toUpperCase()] !== 'undefined' ? this.strings[key.toUpperCase()] : def;
	},
	load: function(object) {
		for (var key in object) {
			this.strings[key.toUpperCase()] = object[key];
		}
		return this;
	}
};

/**
 * Method to replace all request tokens on the page with a new one.
 *
 * @param   string  n
 * @return  void
 */
Hubzero.replaceTokens = function(n) {
	var els = document.getElementsByTagName('input');
	for (var i = 0; i < els.length; i++) {
		if ((els[i].type == 'hidden') && (els[i].name.length == 32) && els[i].value == '1') {
			els[i].name = n;
		}
	}
};

/**
 * Verifies if the string is in a valid email format
 *
 * @param   string   text
 * @return  boolean
 */
Hubzero.isEmail = function(text) {
	var regex = new RegExp("^[\\w-_\.]*[\\w-_\.]\@[\\w]\.+[\\w]+[\\w]$");
	return regex.test(text);
};

/**
 * Toggles the check state of a group of boxes
 *
 * Checkboxes must have an id attribute in the form cb0, cb1...
 *
 * @param   mixed   checkbox  The number of box to 'check', for a checkbox element
 * @param   string  stub      An alternative field name
 * @return  bool
 */
Hubzero.checkAll = function(checkbox, stub) {
	if (!stub) {
		stub = 'cb';
	}
	if (checkbox.form) {
		var c = 0;
		for (var i = 0, n = checkbox.form.elements.length; i < n; i++) {
			var e = checkbox.form.elements[i];
			if (e.type == checkbox.type) {
				if ((stub && e.id.indexOf(stub) == 0) || !stub) {
					e.checked = checkbox.checked;
					c += (e.checked == true ? 1 : 0);
				}
			}
		}
		if (checkbox.form.boxchecked) {
			checkbox.form.boxchecked.value = c;
		}
		return true;
	}
	return false;
}

/**
 * Render messages send via JSON
 *
 * @param   object  messages  JavaScript object containing the messages to render
 * @return  void
 */
Hubzero.renderMessages = function(messages) {
	Hubzero.removeMessages();
	var container = $('#system-message-container');

	var dl = $('<dl>')
				.attr('id', 'system-message')
				.attr('role', 'alert');

	$.each(messages, function (type, item) {
		var dt = $('<dt>')
					.addClass(type)
					.html(type)
					.appendTo(dl);

		var dd = $('<dd>')
					.addClass(type)
					.addClass('message');
		var list = $('<ul>');

		$.each(item, function (index, item, object) {
			var li = $('<li>')
						.html(item)
						.appendTo(list);
		});
		list.appendTo(dd);
		dd.appendTo(dl);
	});
	dl.appendTo(container);

	$(document).trigger('renderMessages');
};


/**
 * Remove messages
 *
 * @return  void
 */
Hubzero.removeMessages = function() {
	var children = $('#system-message-container > *');
	children.remove();
}

/**
 * Evaluate if any items in a list of checkboxes are checked
 *
 * @param   isitchecked
 * @param   form
 * @return  void
 */
Hubzero.isChecked = function(isitchecked, form) {
	if (typeof(form) === 'undefined') {
		form = document.getElementById('adminForm');

		if (!form) {
			form = document.adminForm;
		}
	}

	if (isitchecked == true) {
		form.boxchecked.value++;
	} else {
		form.boxchecked.value--;
	}
}

/**
 * Pops up a new window in the middle of the screen
 *
 * @param   string  mypage
 * @param   string  myname
 * @param   string  w
 * @param   string  h
 * @param   string  scroll
 * @return  void
 */
Hubzero.popupWindow = function(mypage, myname, w, h, scroll) {
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	var winprops = 'height=' + h + ',width=' + w + ',top=' + wint + ',left=' + winl
			+ ',scrollbars=' + scroll + ',resizable'

	var win = window.open(mypage, myname, winprops)
	win.window.focus();
}

/**
 * Set the table sort and direction on a form and submit it
 *
 * @param   string  order
 * @param   string  dir
 * @param   string  task
 * @param   mixed   form
 * @return  void
 */
Hubzero.tableOrdering = function(order, dir, task, form) {
	if (typeof(form) === 'undefined') {
		form = document.getElementById('adminForm');

		if (!form) {
			form = document.adminForm;
		}
	}

	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;

	Hubzero.submitform(task, form);
}

/**
 * Check the checkbox for this item and submit the form with the task
 *
 * @param   string  id
 * @param   string  task
 * @return  bool
 */
Hubzero.listItemTask = function(id, task) {
	var f = document.adminForm;
	var cb = f[id];
	if (cb) {
		// Uncheck all other checkboxes
		for (var i = 0; true; i++) {
			var cbx = f['cb'+i];
			if (!cbx) {
				break;
			}
			cbx.checked = false;
		}
		// Check this checkbox
		cb.checked = true;
		f.boxchecked.value = 1;

		// Submit the form
		var form = document.getElementById('adminForm');
		Hubzero.submitform(task, form);
	}
	return false;
}

/**
 * Writes a dynamically generated list
 *
 * @param   string  selectParams  The parameters to insert into the <select> tag
 * @param   array   source        A javascript array of list options in the form [key,value,text]
 * @param   string  key           The key to display for the initial state of the list
 * @param   string  orig_key      The original key that was selected
 * @param   string  orig_val      The original item value that was selected
 * @return  void
 */
function writeDynaList(selectParams, source, key, orig_key, orig_val) {
	var html = '\n	<select ' + selectParams + '>';
	var i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			var selected = '';
			if ((orig_key == key && orig_val == source[x][1])
					|| (i == 0 && orig_key != key)) {
				selected = 'selected="selected"';
			}
			html += '\n		<option value="' + source[x][1] + '" ' + selected + '>' + source[x][2] + '</option>';
		}
		i++;
	}
	html += '\n	</select>';

	document.writeln(html);
}

/**
 * Changes a dynamically generated list
 *
 * @param   string  listname  The name of the list to change
 * @param   array   source    A javascript array of list options in the form [key,value,text]
 * @param   string  key       The key to display
 * @param   string  orig_key  The original key that was selected
 * @param   string  orig_val  The original item value that was selected
 * @return  void
 */
function changeDynaList(listname, source, key, orig_key, orig_val) {
	var list = document.adminForm[listname];

	// empty the list
	for (i in list.options.length) {
		list.options[i] = null;
	}
	i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			opt = new Option();
			opt.value = source[x][1];
			opt.text  = source[x][2];

			if ((orig_key == key && orig_val == opt.value) || i == 0) {
				opt.selected = true;
			}
			list.options[i++] = opt;
		}
	}
	list.length = i;
}

/**
 * USED IN: administrator/components/com_menus/views/menus/tmpl/default.php
 *
 * @param radioObj
 * @return
 */
// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
function radioGetCheckedValue(radioObj) {
	if (!radioObj) {
		return '';
	}
	var n = radioObj.length;
	if (n == undefined) {
		if (radioObj.checked) {
			return radioObj.value;
		} else {
			return '';
		}
	}
	for (var i = 0; i < n; i++) {
		if (radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return '';
}

/**
 * Get the selected value of a <select> list
 *
 * @param   string  frmName
 * @param   string  srcListName
 * @return  mixed
 */
function getSelectedValue(frmName, srcListName) {
	var form = document[frmName];
	var srcList = form[srcListName];

	i = srcList.selectedIndex;
	if (i != null && i > -1) {
		return srcList.options[i].value;
	} else {
		return null;
	}
}

/**
 * Toggles the check state of a group of boxes
 *
 * Checkboxes must have an id attribute in the form cb0, cb1...
 *
 * @param   mixed   checkbox  The number of box to 'check', for a checkbox element
 * @param   string  stub      An alternative field name
 * @return  mixed
 * @deprecated  12.1 This function will be removed in a future version. Use Hubzero.checkAll() instead.
 */
function checkAll(checkbox, stub) {
	if (!stub) {
		stub = 'cb';
	}
	if (checkbox.form) {
		var c = 0;
		for (var i = 0, n = checkbox.form.elements.length; i < n; i++) {
			var e = checkbox.form.elements[i];
			if (e.type == checkbox.type) {
				if ((stub && e.id.indexOf(stub) == 0) || !stub) {
					e.checked = checkbox.checked;
					c += (e.checked == true ? 1 : 0);
				}
			}
		}
		if (checkbox.form.boxchecked) {
			checkbox.form.boxchecked.value = c;
		}
		return true;
	} else {
		// The old way of doing it
		var f = document.adminForm;
		var c = f.toggle.checked;
		var n = checkbox;
		var n2 = 0;
		for (var i = 0; i < n; i++) {
			var cb = f[stub+''+i];
			if (cb) {
				cb.checked = c;
				n2++;
			}
		}
		if (c) {
			document.adminForm.boxchecked.value = n2;
		} else {
			document.adminForm.boxchecked.value = 0;
		}
	}
}

/**
 * Check the checkbox for this item and submit the form with the task
 *
 * @param   string  id
 * @param   string  task
 * @return  bool
 */
function listItemTask(id, task) {
	return Hubzero.listItemTask(id, task);
}

/**
 * @deprecated  12.1 This function will be removed in a future version. Use Hubzero.isChecked() instead.
 *
 * @param   bool  isitchecked
 * @return  void
 */
function isChecked(isitchecked) {
	if (isitchecked == true) {
		document.adminForm.boxchecked.value++;
	} else {
		document.adminForm.boxchecked.value--;
	}
}

/**
 * Default function. Usually would be overriden by the component
 *
 * @param   string  pressbutton
 * @return  void
 * @deprecated  12.1 This function will be removed in a future version. Use Hubzero.submitbutton() instead.
 */
function submitbutton(pressbutton) {
	return Hubzero.submitbutton(pressbutton);
}

/**
 * Submit the admin form
 *
 * @param   string  pressbutton
 * @return  void
 * @deprecated  12.1 This function will be removed in a future version. Use Hubzero.submitform() instead.
 */
function submitform(pressbutton) {
	return Hubzero.submitform(pressbutton);
}

/**
 * Pops up a new window in the middle of the screen
 *
 * @param   string  mypage
 * @param   string  myname
 * @param   string  w
 * @param   string  h
 * @param   string  scroll
 * @return  void
 * @deprecated  12.1 This function will be removed in a future version. Use Hubzero.popupWindow() instead.
 */
function popupWindow(mypage, myname, w, h, scroll) {
	return Hubzero.popupWindow(mypage, myname, w, h, scroll);
}

/**
 * Set the table sort and direction on a form and submit it
 *
 * @param   string  order
 * @param   string  dir
 * @param   string  task
 * @return  void
 * @deprecated  12.1 This function will be removed in a future version. Use Hubzero.tableOrdering() instead.
 */
function tableOrdering(order, dir, task) {
	var form = document.adminForm;

	return Hubzero.tableOrdering(order, dir, task, form);
}

/**
 * Check if an element has the specified class name
 *
 * @param   integer  n
 * @param   string   task
 * @return  mixed
 * @deprecated  12.1 This function will be removed in a future version. Use Hubzero.tableOrdering() instead.
 */
function saveorder(n, task) {
	checkAll_button(n, task);
}

function checkAll_button(n, task) {
	return Hubzero.saveOrder(n, task);
}

/**
 * Check if an element has the specified class name
 *
 * @param   integer  n
 * @param   string   task
 * @return  mixed
 */
Hubzero.saveOrder = function(n, task) {
	if (!task) {
		task = 'saveorder';
	}

	for (var j = 0; j <= n; j++) {
		var box = document.adminForm['cb'+j];
		if (box) {
			if (box.checked == false) {
				box.checked = true;
			}
		} else {
			alert("You cannot change the order of items, as an item in the list is `Checked Out`");
			return;
		}
	}

	return Hubzero.submitform(task);
}

/**
 * Check if an element has the specified class name
 *
 * @param   el         The element to test
 * @param   className  The class to test for
 * @return  bool
 */
Hubzero.hasClass = function(el, className) {
	return el.classList ? el.classList.contains(className) : new RegExp('\\b'+ className+'\\b').test(el.className);
}

/**
 * Add a class to an element
 *
 * @param   el         The element to add the class to
 * @param   className  The class to add
 * @return  bool
 */
Hubzero.addClass = function(el, className) {
	if (el.classList) {
		el.classList.add(className);
	} else if (!hasClass(el, className)) {
		el.className += ' ' + className;
	}
}

/**
 * Remove a class from an element
 *
 * @param   el         The element to remove the class from
 * @param   className  The class to remove
 * @return  bool
 */
Hubzero.removeClass = function(el, className) {
	if (el.classList) {
		el.classList.remove(className);
	} else {
		el.className = el.className.replace(new RegExp('\\b'+ className+'\\b', 'g'), '');
	}
}

/**
 * Handle toolbar actions
 *
 * @param   event
 * @return  void
 */
Hubzero.toolbarAction = function(event) {
	event.preventDefault();

	var el = this;

	if (Hubzero.hasClass(el, 'toolbar-submit')) {
		if (Hubzero.hasClass(el, 'toolbar-list') && document.adminForm.boxchecked.value == 0) {
			alert(el.getAttribute('data-message'));
		} else {
			if (el.getAttribute('data-task')) {
				Hubzero.submitbutton(el.getAttribute('data-task'));
			} else {
				console.log('Error: no task found.');
			}
		}
	}

	if (Hubzero.hasClass(el, 'toolbar-popup')) {
		var width  = (el.getAttribute('data-width') ? el.getAttribute('data-width') : 700),
			height = (el.getAttribute('data-height') ? el.getAttribute('data-height') : 500),
			scroll = 1;

		Hubzero.popupWindow(
			el.getAttribute('href'),
			el.getAttribute('data-message'),
			width,
			height,
			scroll
		);
	}

	if (Hubzero.hasClass(el, 'toolbar-confirm')) {
		if (Hubzero.hasClass(el, 'toolbar-list') && document.adminForm.boxchecked.value == 0) {
			alert(el.getAttribute('data-message'));
		} else {
			if (confirm(el.getAttribute('data-confirm'))) {
				if (el.getAttribute('data-task')) {
					Hubzero.submitbutton(el.getAttribute('data-task'));
				} else {
					console.log('Error: no task found.');
				}
			}
		}
	}
}

/**
 * Submit a form
 *
 * @param   event
 * @return  void
 */
Hubzero.filterSubmit = function(event)
{
	this.form.submit();
}

/**
 * Clear filters in a form and submit
 *
 * @param   event
 * @return  void
 */
Hubzero.filterClear = function(event)
{
	var k,
		filters = this.form.getElementsByClassName('filter');

	for (k = 0; k < filters.length; k++)
	{
		if (filters[k].tagName.toLowerCase() == 'select') {
			filters[k].selectedIndex = 0;
		}
		if (filters[k].tagName.toLowerCase() == 'input') {
			filters[k].value = '';
		}
	}

	this.form.submit();
}

/**
 * Toggle check-all checkbox
 *
 * @param   event
 * @return  void
 */
Hubzero.gridCheckboxToggle = function(event)
{
	if (Hubzero.hasClass(this, 'toggle-all')) {
		Hubzero.checkAll(this);
	} else {
		Hubzero.isChecked(this.checked);
	}
}

/**
 * Grid ordering
 *
 * @param   event
 * @return  void
 */
Hubzero.gridOrder = function(event)
{
	event.preventDefault();

	Hubzero.tableOrdering(
		this.getAttribute('data-order'),
		this.getAttribute('data-direction'),
		this.getAttribute('data-task')
	);

	return false;
}

Hubzero.gridOrderSave = function(event)
{
	event.preventDefault();

	var rows = this.getAttribute('data-rows'),
		task = this.getAttribute('data-task');

	if (rows && task) {
		Hubzero.saveOrder(rows, task);
	}

	return false;
}

/**
 * Grid actions
 *
 * @param   event
 * @return  void
 */
Hubzero.gridAction = function(event)
{
	event.preventDefault();

	var id = this.getAttribute('data-id'),
		task = this.getAttribute('data-task');

	if (id && task) {
		return Hubzero.listItemTask(id, task);
	}

	return false;
}

/**
 * Attach pagination events
 *
 * @return  void
 */
Hubzero.paginate = function()
{
	var i,
		pages = document.querySelectorAll('.pagination a');
	for (i = 0; i < pages.length; i++)
	{
		pages[i].addEventListener('click', function(event){
			event.preventDefault();
			document.adminForm[this.getAttribute('data-prefix') + 'limitstart'].value = parseInt(this.getAttribute('data-start'));
			Hubzero.submitform();
		});
	}
	var limits = document.querySelectorAll('.pagination select');
	for (i = 0; i < limits.length; i++)
	{
		limits[i].addEventListener('change', function(event){
			Hubzero.submitform();
		});
	}
}

/**
 * Joomla compatibility
 */
if (typeof(Joomla) === 'undefined') {
	var Joomla = {};
}

Joomla.editors = {};
Joomla.editors.instances = {};
Joomla.submitform = function(task, form) {
	return Hubzero.submitform(task, form);
}
Joomla.submitbutton = function(pressbutton) {
	return Hubzero.submitbutton(pressbutton);
}
Joomla.JText = {
	'_': function(key, def) {
		return Hubzero.Lang.txt(key, def);
	},
	load: function(object) {
		return Hubzero.Lang.load(object);
	}
}
Joomla.replaceTokens = function(n) {
	return Hubzero.replaceTokens(n);
}
Joomla.isEmail = function(text) {
	return Hubzero.isEmail(text);
}
Joomla.checkAll = function(checkbox, stub) {
	return Hubzero.checkAll(checkbox, stub);
}
Joomla.renderMessages = function(messages) {
	return Hubzero.renderMessages(messages);
}
Joomla.removeMessages = function() {
	return Hubzero.removeMessages();
}
Joomla.isChecked = function(isitchecked, form) {
	return Hubzero.isChecked(isitchecked, form);
}
Joomla.popupWindow = function(mypage, myname, w, h, scroll) {
	return Hubzero.popupWindow(mypage, myname, w, h, scroll);
}
Joomla.tableOrdering = function(order, dir, task, form) {
	return Hubzero.tableOrdering(order, dir, task, form);
}
Joomla.listItemTask = function(id, task) {
	return Hubzero.listItemTask(id, task);
}
Joomla.saveOrder = function(n, task) {
	return Hubzero.saveOrder(n, task);
}
Joomla.hasClass = function(el, className) {
	return Hubzero.hasClass(el, className);
}
Joomla.addClass = function(el, className) {
	return Hubzero.addClass(el, className);
}
Joomla.removeClass = function(el, className) {
	return Hubzero.removeClass(el, className);
}

/**
 * Initiate event hooks
 */
document.addEventListener('DOMContentLoaded', function() {
	var i;

	// Add event listeners to toolbar buttons
	var toolbarbuttons = document.getElementsByClassName('toolbar');
	for (i = 0; i < toolbarbuttons.length; i++)
	{
		toolbarbuttons[i].addEventListener('click', Hubzero.toolbarAction);
	}

	// Add event listener for checkbox toggles
	var checkboxes = document.getElementsByClassName('checkbox-toggle');
	for (i = 0; i < checkboxes.length; i++)
	{
		checkboxes[i].addEventListener('click', Hubzero.gridCheckboxToggle);
	}

	// Add event listener for filters
	var filters = document.getElementsByClassName('filter-submit');
	for (i = 0; i < filters.length; i++)
	{
		filters[i].addEventListener('change', Hubzero.filterSubmit);
	}

	// Add event listener for clearing filters
	var clearfilters = document.getElementsByClassName('filter-clear');
	for (i = 0; i < clearfilters.length; i++)
	{
		clearfilters[i].addEventListener('click', Hubzero.filterClear);
	}

	// Add event listener for table sorting
	var clearfilters = document.getElementsByClassName('grid-order');
	for (i = 0; i < clearfilters.length; i++)
	{
		clearfilters[i].addEventListener('click', Hubzero.gridOrder);
	}

	// Add event listener for saving table sorting
	var ordering = document.getElementsByClassName('grid-order-save');
	for (i = 0; i < ordering.length; i++)
	{
		ordering[i].addEventListener('click', Hubzero.gridOrderSave);
	}

	// Add event listener for action items
	var actions = document.getElementsByClassName('grid-action');
	for (i = 0; i < actions.length; i++)
	{
		actions[i].addEventListener('click', Hubzero.gridAction);
	}

	// Attach pagination events
	Hubzero.paginate();
});
