/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Helps in creating a structure will help later.
function makeStruct(names) {
	var names = names.split(' ');
	var count = names.length;
	function constructor() {
		for (var i = 0; i < count; i++) {
			this[names[i]] = arguments[i];
		}
	}
	return constructor;
}

var rootcondition = '<fieldset class="condition-set">';
	rootcondition += '<p class="operator">';
		rootcondition += '<button class="remove" alt="Remove">&times;</button> ';
		rootcondition += 'Match ';
		rootcondition += '<select>';
			rootcondition += '<option value="AND">all</option>';
			rootcondition += '<option value="OR">any</option>';
		rootcondition += '</select> of the following:';
	rootcondition += '</p>';
	rootcondition += '<div class="querycntnr">';
		rootcondition += '<div class="querycntnr querystmts">';
			rootcondition += '<span class="query-btns">';
				rootcondition += '<button class="add">+</button> <button class="addroot">...</button>';
			rootcondition += '</span>';
		rootcondition += '</div>';
	rootcondition += '</div>';
rootcondition += '</fieldset>';

var statement = '<p class="conditions">';
	statement += '<button class="remove" alt="Remove">&times;</button> ';

statement += '<select class="fld">';
	statement += '<option value="open">Open/Closed</option>';
	statement += '<option value="status">Status</option>';
	statement += '<option value="login">Submitter</option>';
	statement += '<option value="owner">Owner</option>';
	statement += '<option value="group">Group</option>';
	statement += '<option value="id">ID</option>';
	statement += '<option value="report">Report</option>';
	statement += '<option value="status">Status</option>';
	statement += '<option value="severity">Severity</option>';
	statement += '<option value="tag">Tag</option>';
	statement += '<option value="type" selected="selected">Type</option>';
	statement += '<option value="created">Created</option>';
	statement += '<option value="closed">Closed</option>';
	statement += '<option value="category">Category</option>';
statement += '</select>';

statement += '<select class="op">';
	statement += '<option value="=">is</option>';
	statement += '<option value="!=">is not</option>';
statement += '</select>';

statement += '<select class="val">';
	statement += '<option value="0">user submitted</option>';
	statement += '<option value="1">automatic</option>';
	statement += '<option value="3">tool</option>';
statement += '</select>';

statement += '</p>';

/**
 * Helper: parse an HTML string into a DocumentFragment
 */
function parseHTML(htmlString) {
	var template = document.createElement('template');
	template.innerHTML = htmlString.trim();
	return template.content;
}

/**
 * Helper: insert HTML string as children at end of element
 */
function appendHTML(parent, htmlString) {
	var fragment = parseHTML(htmlString);
	parent.appendChild(fragment);
}

var Conditions = {

	addqueryroot: function (sel, isroot) {
		var container = (typeof sel === 'string') ? document.querySelector(sel) : sel;
		if (!container) {
			return;
		}

		var q = container.querySelectorAll('fieldset');
		var l = q.length;

		if (l < 1) {
			appendHTML(container, rootcondition);
			q = container.querySelectorAll('fieldset');
			l = q.length;
		}
		var elem = q[0];

		if (l > 1) {
			if (isroot) {
				l = 1;
			}
			elem = q[l - 1];
		}

		// If root element remove the close button
		if (isroot) {
			var rootRemove = elem.querySelector(':scope > p.operator > .remove');
			if (rootRemove) {
				rootRemove.parentNode.removeChild(rootRemove);
			}
		}

		var operatorRemoves = elem.querySelectorAll('p.operator > .remove');
		for (var r = 0; r < operatorRemoves.length; r++) {
			operatorRemoves[r].addEventListener('click', function (e) {
				e.preventDefault();
				// (this=button)>p>fieldset
				this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
			});
		}

		// Add the default statement segment to the root condition
		if (elem.querySelectorAll('select.fld').length < 1) {
			var stmtContainer = elem.querySelector('div > .querystmts');
			if (stmtContainer) {
				appendHTML(stmtContainer, statement);
			}
		}

		// Mark first remove buttons as head
		var queryStmts = elem.querySelectorAll('div > .querystmts');
		for (var j = 0; j < queryStmts.length; j++) {
			var removes = queryStmts[j].querySelectorAll('p > .remove');
			for (var i = 0; i < removes.length; i++) {
				if (i == 0) {
					removes[i].classList.add('head');
					removes[i].addEventListener('click', function (e) {
						e.preventDefault();
					});
				} else {
					// Clone to remove old listeners
					var oldEl = removes[i];
					var newEl = oldEl.cloneNode(true);
					oldEl.parentNode.replaceChild(newEl, oldEl);
					newEl.addEventListener('click', function () {
						this.parentNode.parentNode.removeChild(this.parentNode);
					});
				}
			}
		}

		// Handle click for adding new statement segment
		var addBtns = elem.querySelectorAll('span > .add');
		for (var a = 0; a < addBtns.length; a++) {
			addBtns[a].addEventListener('click', function (e) {
				e.preventDefault();
				var stmtParent = this.parentNode.parentNode;
				appendHTML(stmtParent, statement);

				// Rebind change on all fld selects in this container
				var sels = stmtParent.querySelectorAll('select.fld');
				for (var s = 0; s < sels.length; s++) {
					// Clone to remove old listeners
					var oldSel = sels[s];
					var newSel = oldSel.cloneNode(true);
					oldSel.parentNode.replaceChild(newSel, oldSel);
					newSel.addEventListener('change', function () {
						var opSelect = this.parentNode.querySelector('select.op');
						// Clear options
						while (opSelect.options.length > 0) {
							opSelect.remove(0);
						}
						var val = null;
						var operators = Conditions.option[this.value].operators;
						for (var o = 0; o < operators.length; o++) {
							if (operators[o].sel) {
								val = operators[o].val;
							}
							var opt = document.createElement('option');
							opt.value = operators[o].val;
							opt.textContent = operators[o].label;
							opSelect.appendChild(opt);
						}
						if (val !== null) {
							opSelect.value = val;
						}
						Conditions.populate(this.value, opSelect);
					});
				}

				// Rebind remove buttons (skip .head)
				var stmtRemoves = stmtParent.querySelectorAll('p > .remove:not(.head)');
				for (var sr = 0; sr < stmtRemoves.length; sr++) {
					var oldRm = stmtRemoves[sr];
					var newRm = oldRm.cloneNode(true);
					oldRm.parentNode.replaceChild(newRm, oldRm);
					newRm.addEventListener('click', function () {
						this.parentNode.parentNode.removeChild(this.parentNode);
					});
				}
			});
		}

		// Repopulate options based on field selected
		var fldSelects = elem.querySelectorAll('select.fld');
		for (var f = 0; f < fldSelects.length; f++) {
			fldSelects[f].addEventListener('change', function () {
				var opSelect = this.parentNode.querySelector('select.op');
				// Clear options
				while (opSelect.options.length > 0) {
					opSelect.remove(0);
				}
				var val = null;
				var operators = Conditions.option[this.value].operators;
				for (var o = 0; o < operators.length; o++) {
					if (operators[o].sel) {
						val = operators[o].val;
					}
					var opt = document.createElement('option');
					opt.value = operators[o].val;
					opt.textContent = operators[o].label;
					opSelect.appendChild(opt);
				}
				if (val !== null) {
					opSelect.value = val;
				}
				Conditions.populate(this.value, opSelect);
			});
		}

		// Handle click to add new root condition
		var addrootBtns = elem.querySelectorAll('span > .addroot');
		for (var ar = 0; ar < addrootBtns.length; ar++) {
			addrootBtns[ar].addEventListener('click', function (e) {
				e.preventDefault();
				Conditions.addqueryroot(this.parentNode.parentNode.parentNode, false);
			});
		}
	},

	populate: function (val, opSelect) {
		var values = Conditions.option[val].values;
		var oldVal = opSelect.parentNode.querySelector('.val');

		// Remove old value element
		if (oldVal) {
			oldVal.parentNode.removeChild(oldVal);
		}

		var newEl;
		if (values instanceof Array) {
			newEl = document.createElement('select');
			newEl.className = 'val';
			var selectedVal = null;
			for (var v = 0; v < values.length; v++) {
				if (values[v].sel) {
					selectedVal = values[v].val;
				}
				var opt = document.createElement('option');
				opt.value = values[v].val;
				opt.textContent = values[v].label;
				newEl.appendChild(opt);
			}
			if (selectedVal !== null) {
				newEl.value = selectedVal;
			}
		} else {
			newEl = document.createElement('input');
			newEl.type = 'text';
			newEl.className = 'val';
			if (val == 'created') {
				newEl.placeholder = 'YYYY-MM-DD';
			}
		}

		// Insert after op select
		if (opSelect.nextSibling) {
			opSelect.parentNode.insertBefore(newEl, opSelect.nextSibling);
		} else {
			opSelect.parentNode.appendChild(newEl);
		}
	},

	// Recursive method to parse the condition and generate the query
	getCondition: function (rootsel) {
		var root = (typeof rootsel === 'string') ? document.querySelector(rootsel) : rootsel;
		if (!root) {
			return {};
		}

		var children = root.children;
		// children[0] is for operator, children[1] is for expressions

		var q = {},
			expressions = [],
			nestedexpressions = [];

		var operatorSelect = children[0].querySelector(':checked');
		q.operator = operatorSelect ? operatorSelect.value : 'AND';

		// Get all the expressions in a condition
		var expressionElems = children[1].querySelectorAll(':scope > .querystmts p');
		for (var i = 0; i < expressionElems.length; i++) {
			expressions[i] = {};

			var colSelect = expressionElems[i].querySelector('.fld');
			var opSelect = expressionElems[i].querySelector('.op');

			var colSelected = colSelect ? colSelect.options[colSelect.selectedIndex] : null;
			var opSelected = opSelect ? opSelect.options[opSelect.selectedIndex] : null;

			expressions[i].fldval  = colSelected ? colSelected.value : '';
			expressions[i].flddisp = colSelected ? colSelected.textContent : '';
			expressions[i].opval   = opSelected ? opSelected.value : '';
			expressions[i].opdisp  = opSelected ? opSelected.textContent : '';

			var valEl = expressionElems[i].querySelector('.val');
			expressions[i].val = valEl ? valEl.value : '';
		}
		q.expressions = expressions;

		// Get all the nested expressions
		var nestedFieldsets = children[1].querySelectorAll(':scope > fieldset');
		if (nestedFieldsets.length > 0) {
			for (var k = 0; k < nestedFieldsets.length; k++) {
				nestedexpressions[k] = Conditions.getCondition(nestedFieldsets[k]);
			}
		}
		q.nestedexpressions = nestedexpressions;

		return q;
	},

	// Recursive method to iterate over the condition tree and generate the query
	getQuery: function (condition) {
		var op = [' ', condition.operator, ' '].join('');

		var e = [];
		var elen = condition.expressions.length;
		for (var i = 0; i < elen; i++) {
			var expr = condition.expressions[i];
			if (expr.opval.indexOf('$1') != -1) {
				e.push('`' + expr.fldval + '` ' + expr.opval.replace('$1', expr.val));
			} else {
				e.push('`' + expr.fldval + '` ' + expr.opval + " '" + expr.val + "'");
			}
		}

		var n = [];
		var nlen = condition.nestedexpressions.length;
		for (var k = 0; k < nlen; k++) {
			var nestexpr = condition.nestedexpressions[k];
			var result = Conditions.getQuery(nestexpr);
			n.push(result);
		}

		var q = [];
		if (e.length > 0) q.push(e.join(op));
		if (n.length > 0) q.push(n.join(op));

		return ['(', q.join(op), ')'].join(' ');
	}
};

Hubzero.submitbutton = function (task) {
	var query = {},
		component = false;

	var frm = document.getElementById('item-form');

	if (!frm) {
		frm = document.getElementById('component-form');
		component = true;
	}

	if (frm) {
		if (task == 'cancel') {
			Hubzero.submitform(task, frm);
			return;
		}

		var query = {};
		query = Conditions.getCondition('.query > fieldset');
		var condField = document.getElementById('field-conditions');
		if (condField) {
			condField.value = JSON.stringify(query);
		}

		if (!component) {
			Hubzero.submitform(task, frm);
		}
	}
};

document.addEventListener('DOMContentLoaded', function () {
	var cdata = document.getElementById('conditions-data');

	if (cdata) {
		var data = JSON.parse(cdata.content ? cdata.content.textContent : cdata.innerHTML);
		Conditions.option = data.conditions;
	}

	Conditions.addqueryroot('.query', true);

	var btnApply = document.getElementById('btn-apply');
	if (btnApply) {
		btnApply.addEventListener('click', function (e) {
			var query = {};
			var titleField = document.getElementById('field-title');

			if (!titleField || !titleField.value) {
				alert(this.getAttribute('data-invalid'));
				return false;
			}

			query = Conditions.getCondition('.query > fieldset');
			var condField = document.getElementById('field-conditions');
			if (condField) {
				condField.value = JSON.stringify(query);
			}

			var form = document.getElementById('component-form');
			var formData = new FormData(form);
			var params = new URLSearchParams(formData).toString();

			fetch(this.getAttribute('data-action'), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: params
			})
			.then(function (response) { return response.text(); })
			.then(function (data) {
				var parentQueryList = window.parent.document.getElementById('query-list');
				if (parentQueryList) {
					parentQueryList.innerHTML = data;
				}
				if (typeof window.parent.applySortable === 'function') {
					window.parent.applySortable();
				}
				window.top.setTimeout(function () {
					window.parent.postMessage('admin-popup-close', '*');
				}, 700);
			});
		});
	}

	var btnCancel = document.getElementById('btn-cancel');
	if (btnCancel) {
		btnCancel.addEventListener('click', function (e) {
			window.parent.postMessage('admin-popup-close', '*');
		});
	}
});
