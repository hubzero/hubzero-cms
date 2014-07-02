//Helps in creating a structure will help later.
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
	rootcondition += '<div>';
		rootcondition += '<div class="querystmts">';
			rootcondition += '<span>';
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
	statement += '<option value="resolved">Resolution</option>';
	statement += '<option value="severity">Severity</option>';
	statement += '<option value="tag">Tag</option>';
	statement += '<option value="type" selected="selected">Type</option>';
	statement += '<option value="created">Created</option>';
	statement += '<option value="category">Category</option>';
statement += '</select>';

statement += '<select class="op">';
	statement += '<option value="=">is</option>';
	statement += '<option value="!=">is not</option>';
statement += '</select>'

statement += '<select class="val">';
	statement += '<option value="0">user submitted</option>';
	statement += '<option value="1">automatic</option>';
	statement += '<option value="3">tool</option>';
statement += '</select>';

//statement += '<input type="text" class="val" />';
statement += '</p>';

if (!jq) {
	var jq = $;
}

var Conditions = {
	jQuery: jq,

	addqueryroot: function (sel, isroot) {
		var $ = this.jQuery;
		
		var q = $(sel).find('fieldset');
		var l = q.length;

		if (l < 1) {
			$(sel).append(rootcondition);
			q = $(sel).find('fieldset');
			l = q.length;
		//} else {
			//rootcondition = q;
		}
		var elem = q;

		if (l > 1) {
			if (isroot) {
				l = 1;
			}
			elem = $(q[l - 1]);
		}
		//console.log(elem);

		//If root element remove the close image
		if (isroot) {
			elem.find('>p.operator > .remove').detach();
		}
		elem.find('p.operator > .remove').on('click', function (e) {
			// (this=button)>p>fieldset
			e.preventDefault();
			$(this).parent().parent().detach();
		});

		// Add the default statement segment to the root condition
		if (elem.find('select.fld').length < 1) {
			elem.find('div > .querystmts').append(statement);
		}

		// Add the head class to the first statement
		/*elem.find('div > .querystmts p > .remove').addClass('head').click(function(e){
			e.preventDefault();
		});
		elem.find('div > .querystmts p:first-child > .remove').addClass('head').on('click', function(e){
			e.preventDefault();
		});
		//elem.find('div > .querystmts p > .remove').filter(':not(.head)').each(function(i, el){
		elem.find('div > .querystmts p > .remove').each(function(i, el){
			if (i == 0) {
				$(el).addClass('head').on('click', function(e){
					e.preventDefault();
				});
			} else {
				$(el).unbind('click');
				$(el).on('click', function () {
					$(this).parent().detach();
				});
			}
		});*/
		elem.find('div > .querystmts').each(function(j, elm){
			$(elm).find('p > .remove').each(function(i, el){
				if (i == 0) {
					$(el).addClass('head').on('click', function(e){
						e.preventDefault();
					});
				} else {
					$(el).unbind('click');
					$(el).on('click', function () {
						$(this).parent().detach();
					});
				}
			});
		});

		// Handle click for adding new statement segment
		// When a new statement is added add a condition to handle remove click.
		elem.find('span > .add').on('click', function (e) {
			e.preventDefault();
			$(this).parent().parent().append(statement);
			
			var sels = $(this).parent().parent().find('select.fld');
			sels.unbind('change');
			sels.on('change', function () {
				var options = $(this).siblings('select.op').find('option').remove().end();
				var val = null;
				$.each(Conditions.option[$(this).val()].operators, function() {
					if (this.sel) {
						val = this.val;
					}
				    options.append($("<option />").val(this.val).text(this.label));
				});
				options.val(val);

				Conditions.populate($(this).val(), options);
			});

			var stmts = $(this).parent().parent().find('p > .remove').filter(':not(.head)');
			stmts.unbind('click');
			stmts.on('click', function () {
				$(this).parent().detach();
			});
		});

		//Conditions.populate(elem.find('select.fld').val(), elem.find('select.op'));

		// Repopulate options based on field selected
		elem.find('select.fld').on('change', function () {
			var options = $(this).siblings('select.op').find('option').remove().end();
			var val = null;
			$.each(Conditions.option[$(this).val()].operators, function() {
				if (this.sel) {
					val = this.val;
				}
			    options.append($("<option />").val(this.val).text(this.label));
			});
			options.val(val);

			Conditions.populate($(this).val(), options);
		});

		// Handle click to add new root condition
		elem.find('span > .addroot').on('click', function (e) {
			e.preventDefault();
			Conditions.addqueryroot($(this).parent().parent().parent(), false);
		});
	},

	populate: function(val, options) {
		var $ = this.jQuery;
		
		var values = Conditions.option[val].values;
		var select = $('<input type="text" class="val" />');

		if (values instanceof Array) {
			select = $('<select class="val"></select>');
			$.each(values, function() {
				if (this.sel) {
					val = this.val;
				}
			    select.append('<option value="' + this.val + '">' + this.label + '</option>');
			});
			select.val(val);
		}
		options.siblings('.val').remove();
		options.after(select);
	},

	//Recursive method to parse the condition and generate the query. Takes the selector for the root condition
	getCondition: function (rootsel) {
		var $ = this.jQuery;
		
		//Get the columns from table (to find a clean way to do it later) //tbody>tr>td
		var elem = $(rootsel).children();
		//elem 0 is for operator, elem 1 is for expressions

		var q = {},
			expressions = [],
			nestedexpressions = [];

		//var operator = $(elem[0]).find(':selected').val();
		q.operator = $(elem[0]).find(':selected').val(); //operator;

		// Get all the expressions in a condition
		var expressionelem = $(elem[1]).find('> .querystmts p');
		for (var i = 0; i < expressionelem.length; i++) {
			expressions[i] = {};

			var col = $(expressionelem[i]).find('.fld :selected');
			var op  = $(expressionelem[i]).find('.op :selected');

			expressions[i].fldval  = col.val();
			expressions[i].flddisp = col.text();
			expressions[i].opval   = op.val();
			expressions[i].opdisp  = op.text();
			expressions[i].val     = $($(expressionelem[i]).find('.val')[0]).val();//$(expressionelem[i]).find(':text').val();
		}
		q.expressions = expressions;

		// Get all the nested expressions
		if ($(elem[1]).find('fieldset').length != 0) {
			var len = $(elem[1]).find('fieldset').length;

			for (var k = 0; k < len; k++) {
				nestedexpressions[k] = Conditions.getCondition($(elem[1]).find('fieldset')[k]);
			}
		}
		q.nestedexpressions = nestedexpressions;

		return q;
	},

	// Recursive method to iterate over the condition tree and generate the query
	getQuery: function (condition) {
		var $ = this.jQuery;
		
		var op = [' ', condition.operator, ' '].join('');

		var e = [];
		var elen = condition.expressions.length;
		for (var i = 0; i < elen; i++) {
			var expr = condition.expressions[i];
			if (expr.opval.indexOf('$1') != -1) {
				e.push("`" + expr.fldval + "` " + expr.opval.replace('$1', expr.val));
			} else {
				e.push("`" + expr.fldval + "` " + expr.opval + " '" + expr.val + "'");
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
		if (e.length > 0)
			q.push(e.join(op));
		if (n.length > 0)
			q.push(n.join(op));

		return ['(', q.join(op), ')'].join(' ');
	}
};