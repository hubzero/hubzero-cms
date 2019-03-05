/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only define the Joomla namespace if not defined.
if (typeof(Joomla) === 'undefined') {
	var Joomla = {};
}

Joomla.editors = {};
// An object to hold each editor instance on page
Joomla.editors.instances = {};

/**
 * Generic submit form
 *
 * @param   string  task
 * @param   mixed   form
 * @return  void
 */
Joomla.submitform = function(task, form) {
	if (typeof(form) === 'undefined') {
		form = document.getElementById('adminForm');
		/**
		 * Added to ensure Joomla 1.5 compatibility
		 */
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
Joomla.submitbutton = function(pressbutton) {
	Joomla.submitform(pressbutton);
}

/**
 * Custom behavior for JavaScript I18N
 *
 * Allows you to call Joomla.JText._() to get a translated JavaScript string pushed in with JText::script() in Joomla.
 *
 * @return  mixed
 */
Joomla.JText = {
	strings: {},
	'_': function(key, def) {
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
Joomla.replaceTokens = function(n) {
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
Joomla.isEmail = function(text) {
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
Joomla.checkAll = function(checkbox, stub) {
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
Joomla.renderMessages = function(messages) {
	Joomla.removeMessages();
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
Joomla.removeMessages = function() {
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
Joomla.isChecked = function(isitchecked, form) {
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
Joomla.popupWindow = function(mypage, myname, w, h, scroll) {
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
Joomla.tableOrdering = function(order, dir, task, form) {
	if (typeof(form) === 'undefined') {
		form = document.getElementById('adminForm');

		if (!form) {
			form = document.adminForm;
		}
	}

	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;

	Joomla.submitform(task, form);
}

/**
 * Check the checkbox for this item and submit the form with the task
 *
 * @param   string  id
 * @param   string  task
 * @return  bool
 */
Joomla.listItemTask = function(id, task) {
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
		Joomla.submitform(task, form);
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
 * @deprecated  12.1 This function will be removed in a future version. Use Joomla.checkAll() instead.
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
	return Joomla.listItemTask(id, task);
}

/**
 * @deprecated  12.1 This function will be removed in a future version. Use Joomla.isChecked() instead.
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
 * @deprecated  12.1 This function will be removed in a future version. Use Joomla.submitbutton() instead.
 */
function submitbutton(pressbutton) {
	return Joomla.submitbutton(pressbutton);
}

/**
 * Submit the admin form
 *
 * @param   string  pressbutton
 * @return  void
 * @deprecated  12.1 This function will be removed in a future version. Use Joomla.submitform() instead.
 */
function submitform(pressbutton) {
	return Joomla.submitform(pressbutton);
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
 * @deprecated  12.1 This function will be removed in a future version. Use Joomla.popupWindow() instead.
 */
function popupWindow(mypage, myname, w, h, scroll) {
	return Joomla.popupWindow(mypage, myname, w, h, scroll);
}

/**
 * Set the table sort and direction on a form and submit it
 *
 * @param   string  order
 * @param   string  dir
 * @param   string  task
 * @return  void
 * @deprecated  12.1 This function will be removed in a future version. Use Joomla.tableOrdering() instead.
 */
function tableOrdering(order, dir, task) {
	var form = document.adminForm;

	return Joomla.tableOrdering(order, dir, task, form);
}

/**
 * Check if an element has the specified class name
 *
 * @param   integer  n
 * @param   string   task
 * @return  mixed
 * @deprecated  12.1 This function will be removed in a future version. Use Joomla.tableOrdering() instead.
 */
function saveorder(n, task) {
	checkAll_button(n, task);
}

function checkAll_button(n, task) {
	return Joomla.saveOrder(n, task);
}

/**
 * Check if an element has the specified class name
 *
 * @param   integer  n
 * @param   string   task
 * @return  mixed
 */
Joomla.saveOrder = function(n, task) {
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

	return Joomla.submitform(task);
}

/**
 * Check if an element has the specified class name
 *
 * @param   el         The element to test
 * @param   className  The class to test for
 * @return  bool
 */
Joomla.hasClass = function(el, className) {
	return el.classList ? el.classList.contains(className) : new RegExp('\\b'+ className+'\\b').test(el.className);
}

/**
 * Add a class to an element
 *
 * @param   el         The element to add the class to
 * @param   className  The class to add
 * @return  bool
 */
Joomla.addClass = function(el, className) {
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
Joomla.removeClass = function(el, className) {
	if (el.classList) {
		el.classList.remove(className);
	} else {
		el.className = el.className.replace(new RegExp('\\b'+ className+'\\b', 'g'), '');
	}
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
		toolbarbuttons[i].addEventListener('click', function(event) {
			event.preventDefault();

			var el = this;

			if (Joomla.hasClass(el, 'toolbar-submit')) {
				if (Joomla.hasClass(el, 'toolbar-list') && document.adminForm.boxchecked.value == 0) {
					alert(el.getAttribute('data-message'));
				} else {
					if (el.getAttribute('data-task')) {
						Joomla.submitbutton(el.getAttribute('data-task'));
					} else {
						console.log('Error: no task found.');
					}
				}
			}

			if (Joomla.hasClass(el, 'toolbar-popup')) {
				var width  = (el.getAttribute('data-width') ? el.getAttribute('data-width') : 700),
					height = (el.getAttribute('data-height') ? el.getAttribute('data-height') : 500),
					scroll = 1;

				Joomla.popupWindow(
					el.getAttribute('href'),
					el.getAttribute('data-message'),
					width,
					height,
					scroll
				);
			}

			if (Joomla.hasClass(el, 'toolbar-confirm')) {
				if (Joomla.hasClass(el, 'toolbar-list') && document.adminForm.boxchecked.value == 0) {
					alert(el.getAttribute('data-message'));
				} else {
					if (confirm(el.getAttribute('data-confirm'))) {
						if (el.getAttribute('data-task')) {
							Joomla.submitbutton(el.getAttribute('data-task'));
						} else {
							console.log('Error: no task found.');
						}
					}
				}
			}
		});
	}

	// Add event listener for checkbox toggles
	var checkboxes = document.getElementsByClassName('checkbox-toggle');
	for (i = 0; i < checkboxes.length; i++)
	{
		checkboxes[i].addEventListener('click', function(event) {
			if (Joomla.hasClass(this, 'toggle-all')) {
				Joomla.checkAll(this);
			} else {
				Joomla.isChecked(this.checked);
			}
		});
	}

	// Add event listener for filters
	var filters = document.getElementsByClassName('filter-submit');
	for (i = 0; i < filters.length; i++)
	{
		filters[i].addEventListener('change', function(event) {
			this.form.submit();
		});
	}

	var clearfilters = document.getElementsByClassName('filter-clear');
	for (i = 0; i < clearfilters.length; i++)
	{
		clearfilters[i].addEventListener('click', function(event) {
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
		});
	}

	// Add event listener for table sorting
	var clearfilters = document.getElementsByClassName('grid-order');
	for (i = 0; i < clearfilters.length; i++)
	{
		clearfilters[i].addEventListener('click', function(event) {
			event.preventDefault();

			Joomla.tableOrdering(
				this.getAttribute('data-order'),
				this.getAttribute('data-direction'),
				this.getAttribute('data-task')
			);

			return false;
		});
	}

	// Add event listener for saving table sorting
	var ordering = document.getElementsByClassName('grid-order-save');
	for (i = 0; i < ordering.length; i++) {
		ordering[i].addEventListener('click', function(event) {
			event.preventDefault();

			var rows = this.getAttribute('data-rows'),
				task = this.getAttribute('data-task');

			if (rows && task) {
				Joomla.saveOrder(rows, task);
			}

			return false;
		});
	}

	// Add event listener for action items
	var actions = document.getElementsByClassName('grid-action');
	for (i = 0; i < actions.length; i++) {
		actions[i].addEventListener('click', function(event) {
			event.preventDefault();

			var id = this.getAttribute('data-id'),
				task = this.getAttribute('data-task');

			if (id && task) {
				return Joomla.listItemTask(id, task);
			}

			return false;
		});
	}

	/*$("a.move_up, a.move_down, a.grid_true, a.grid_false, a.trash")
		.on("click", function(){
			if ($(this).attr("rel")) {
				args = JSON.parse($(this).attr("rel").replace(/\'/g, '"'));
				Joomla.listItemTask(args.id, args.task);
			}
		});*/
});
