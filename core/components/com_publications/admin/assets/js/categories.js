/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var Fields = {
	isIE8: function() {
		var rv = -1,
			ua = navigator.userAgent,
			re = new RegExp("Trident\/([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null) {
			rv = parseFloat(RegExp.$1);
		}
		return (rv == 4);
	},

	addRow: function(id) {
		var tbody = document.getElementById(id).tBodies[0],
			counter = tbody.rows.length,
			newNode = tbody.rows[0].cloneNode(true),
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
						inputs[k].id = id + '-' + counter + '-' + n.replace(']', '')+'-tmp';
						replaceme = id + '-' + counter + '-' + n.replace(']', '')+'-tmp';
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
				newField[i].id = 'fields-'+counter+'-options';
			}
		}

		tbody.appendChild(newNode);

		// Make a clone of the clone. Why? Because IE 8 is dumb.
		// IE still retains a reference to the original object for change events
		// So, when calling onChange, the event gets fired for the clone AND the
		// original. Cloning the clone seems to fix this.
		if (replaceme) {
			var replace = $(replaceme);
			var select = jq.clone(replace).appendAfter(replace);
			jq.remove(replace);
		}

		Fields.initSelect();

		//$('#fields tbody').sortable(); //'enable');

		return false;
	},

	addOption: function(id) {
		var tbody = document.getElementById(id).tBodies[0];
		var counter = tbody.rows.length;
		var newNode = tbody.rows[0].cloneNode(true);

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
					inputs[k].name = 'fields['+id+'][' +n+ '[' + counter + '][label]';
				}
				if (inputs[k].value) {
					inputs[k].value = '';
				}
			}
		}

		tbody.appendChild(newNode);

		return false;
	},

	initOptions: function() {
		$('.add-custom-option').each(function(i, el){
			$(el)
				.off('click')
				.on('click', function(e){
					e.preventDefault();

					Fields.addOption($(this).attr('rel'));
				});
		});
	},

	timer: 0,

	clear: function() {
		Fields.timer = 0;
	},

	initSelect: function() {
		$('#fields select').each(function(i, el){
			$(el)
				.off('change')
				.on('change', function(){
					var i = this.name.replace(/^fields\[(\d+)\]\[type\]/g,"$1");
					$.get($('#fields').attr('data-href') +'&type='+this.value+'&name='+i,{}, function (response){
						$('#fields-'+i+'-options').html(response);
						Fields.initOptions();
					});
				})
		});
	},

	initialise: function() {
		$('#add-custom-field').on('click', function (e){
			e.preventDefault();

			Fields.addRow('fields');
		});

		Fields.initSelect();
		Fields.initOptions();

		$('#fields tbody').sortable();
	}
}

jQuery(document).ready(function($){
	Fields.initialise();

	$("#fields tbody").sortable({
		handle: '.handle',
		helper: function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index) {
				// Set helper cell sizes to match the original sizes
				$(this).width($originals.eq(index).width())
			});
			return $helper;
		}
	});
});
