/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

function addtag(tag)
{
	var input = document.getElementById('tags-men');
	if (input.value == '') {
		input.value = tag;
	} else {
		input.value += ', '+tag;
	}
}

jQuery(document).ready(function($){
	$('.addtag').on('click', function(e){
		e.preventDefault();
		addtag($(this).attr('data-tag'));
	});

	$('#reset_ranking,#reset_rating,#reset_hits').on('click', function(e){
		e.preventDefault();
		Hubzero.submitbutton($(this).attr('data-task'));
	});

	$('.btn-ratings').on('click', function(e){
		e.preventDefault();
		window.open($(this).attr('href'), 'ratings', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no');
		return false;
	});

	var Fields = {
		isIE8: function() {
			var rv = -1,
				ua = navigator.userAgent,
				re = new RegExp("Trident\/([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null) {
				rv = parseFloat(RegExp.$1);
			}
			return (rv == 4);
		}
	}

	var fields = $("#fields");

	if (fields.length) {
		fields
			.on('change', 'select', function (e){
				var i = $(this).attr('name').replace(/^fields\[(\d+)\]\[type\]/g, "$1");
				$.get(document.getElementById("fields").getAttribute('data-href') + '&type=' + this.value + '&name=' + i, {}, function (response) {
					$('#fields-' + i + '-options').html(response);
				});
			})
			.on('click', '.add-custom-option', function (e){
				e.preventDefault();

				var id = $(this).attr('data-rel');

				if (!id) {
					return;
				}

				var tbody = document.getElementById(id).tBodies[0],
					counter = tbody.rows.length,
					newNode = tbody.rows[0].cloneNode(true),
					newField = newNode.childNodes;

				for (var i=0; i<newField.length; i++)
				{
					var inputs = newField[i].childNodes;
					for (var k=0;k<inputs.length;k++)
					{
						var theName = inputs[k].name;
						if (theName) {
							tokens = theName.split('[');
							n = tokens[2];
							inputs[k].name = 'fields['+id+'][' +n+ '[' + counter + '][label]';
						}
						if (inputs[k].value) {
							inputs[k].value = '';
						}
					}
				}

				tbody.appendChild(newNode);

				return false;
			});

		$('#add-custom-field').on('click', function (e){
			e.preventDefault();

			var id = 'fields';

			var tbody     = document.getElementById(id).tBodies[0],
				counter   = tbody.rows.length,
				newNode   = tbody.rows[0].cloneNode(true),
				replaceme = null;

			var newField = newNode.childNodes;
			for (var i=0;i<newField.length;i++)
			{
				var inputs = newField[i].childNodes;
				for (var k=0;k<inputs.length;k++)
				{
					var theName = inputs[k].name;
					if (theName) {
						tokens = theName.split('[');
						n = tokens[2];
						inputs[k].name = id + '[' + counter + ']['+ n;
						inputs[k].id = id + '-' + counter + '-' + n.replace(']', '');

						if (Fields.isIE8() && inputs[k].type == 'select-one') {
							inputs[k].id = id + '-' + counter + '-' + n.replace(']', '') + '-tmp';
							replaceme = id + '-' + counter + '-' + n.replace(']', '') + '-tmp';
						}
					}
					var n = id + '[' + counter + '][type]';
					var z = id + '[' + counter + '][required]';
					if (inputs[k].value && inputs[k].name != z) {
						inputs[k].value = '';
						inputs[k].selectedIndex = 0;
						inputs[k].selected = false;
					}
					if (inputs[k].checked) {
						inputs[k].checked = false;
					}
				}
				if (newField[i].id) {
					newField[i].id = 'fields-' + counter + '-options';
				}
			}

			tbody.appendChild(newNode);

			// Make a clone of the clone. Why? Because IE 8 is dumb.
			// IE still retains a reference to the original object for change events
			// So, when calling onChange, the event gets fired for the clone AND the
			// original. Cloning the clone seems to fix this.
			if (replaceme) {
				var replace = $(replaceme);
				var select  = $.clone(replace).appendAfter(replace);
				$.remove(replace);
			}

			return false;
		});

		$("#fields tbody").sortable({
			handle: '.handle',
			helper: function(e, tr) {
				var originals = tr.children();
				var helper    = tr.clone();
				helper.children().each(function(index) {
					// Set helper cell sizes to match the original sizes
					$(this).width(originals.eq(index).width())
				});
				return helper;
			}
		});
	}
});

/*
//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------

HUB.Resources = {
	removeAuthor: function(el) {
		$(el).parentNode.parentNode.removeChild($(el).parentNode);
		
		// get the new serials
		$('new_authors').value = authsorts.serialize('', function(element, index){
			return element.getProperty('id').replace('author_','');
		});
		
		return false;
	},
	
	addAuthor: function() {
		var authid = $('authid');
		if (!authid) {
			alert('Author select not found');
			return;
		}

		var authorlist = $('author-list');
		if (!authorlist) {
			alert('Author list not found');
			return;
		}

		if (!authid.value) {
			return;
		}
		
		var selectedr = $('authrole').selectedIndex;
		var selectedRole = $('authrole').options[selectedr].value;
		var selectedId = authid.value.split(' ').join('_');

		// create the LI element and attach it to the UL
		var newlistitem = new Element('li',{
			'id': 'author_' + selectedId
		});

		authorlist.appendChild(newlistitem);

		var myAjax = new Ajax('index.php?option=com_resources&controller=items&task=author&no_html=1&u='+selectedId+'&role='+selectedRole+'&rid='+$('id').value,{
			update:'author_' + selectedId
		}).request();
		myAjax.addEvent('onComplete', function(){
			var id = selectedId;
			if ($$('#author_' + selectedId + ' .authid')) {
				$$('#author_' + selectedId + ' .authid').each(function(el){
					id = $(el).value;
				});
			}
			$('author_' + selectedId).setProperty('id', 'author_' + id);
			// re-apply the sorting script so the new LIst item becoems sortable
			authsorts.reinitialize();

			// get the new serials
			$('new_authors').value = authsorts.serialize('', function(element, index){
				return element.getProperty('id').replace('author_','');
			});
		});
	}
};

// a global variable to hold our sortable object
// done so the Myhub singleton can access the sortable object easily
var authsorts;

window.addEvent('domready', function(){
	authsorts = new xSortables(['author-list'], {handle:'span[class=handle]',onComplete:function() {
		$('new_authors').value = this.serialize('', function(element, index){
			return element.getProperty('id').replace('author_','');
		});
	}});
});
*/
