//----------------------------------------------------------
// Scripts for the Support component
//----------------------------------------------------------
var Support = {	

	conditions: {
		'search':'[ Search All Fields ]',
		'status':'Status',
		'owner':'Owner',
		'group':'Group',
		'tag':'Tag',
		'severity':'Severity',
		'reportedby':'Reporter',
		'id':'ID',
		'summary':'Summary',
		'report':'Report',
		'created':'Created',
		'type':'Type',
		'resolved':'Resolution'
	},

	chosen: [], 

	in_array: function(needle, haystack, argStrict) {
		var key = '', strict = !!argStrict;

		if (strict) {
			for (key in haystack) {
				if (haystack[key] === needle) {
					return true;
				}
			}
		} else {
			for (key in haystack) {
				if (haystack[key] == needle) {
					return true;
				}
			}
		}

		return false;
	},

	addRow: function(id) {
		$('.condition-nouns').each(function(i, noun) {
			Support.chosen.push(noun.options[noun.selectedIndex].value);
		});
		$('.condition-nouns').each(i, function(noun) {
			/*var i = 0;
			var rmv = new Array();
			for (var word in Support.conditions) 
			{
				if (word == noun.options[noun.selectedIndex].value) {
					i++;
					continue;
				}
				if (Support.in_array(noun.options[i].value,Support.chosen)) {
					rmv.push(i);
				}
				i++;
			}
			for (var i=0;i<rmv.length;i++) 
			{
				noun.remove(rmv[i]);
			}*/
			var rmv = new Array();
			for (var i=0;i<noun.options.length;i++) 
			{
				if (i == noun.selectedIndex) {
					continue;
				}
				if (Support.in_array(noun.options[i].value,Support.chosen)) {
					rmv.push(i);
				}
				i++;
			}
			for (var i=0;i<rmv.length;i++) 
			{
				noun.remove(rmv[i]);
			}
		});
		
		// Get the table of conditions
		var tbody = document.getElementById(id).tBodies[0];
		// Count the rows
		var counter = tbody.rows.length;
		if (counter == 1) {
			$('.remove-condition').each(function(i, trigger) {
				$(trigger).css('visibility', 'visible');
			});
		}
		// Get the last row
		var lastNode = tbody.rows[(counter - 1)];
		// Get the ID of the condition in the last row
		var startId = lastNode.childNodes[1].childNodes[1].id;
		// Break the ID apart (eg, condition_1) and incriment the value (eg, condition_2) for the new row we're adding
		var sidtokens = startId.split('_');
		var s = sidtokens[1];
		var start = parseInt(s) + 1;

		// Clone the last row
		var newNode = tbody.rows[(counter - 1)].cloneNode(true);
		// Get the condition selected
		var firstList = document.getElementById(id+'_'+s+'_0');
		var key = firstList.options[firstList.selectedIndex].value;
		var firstSelect = null;
		var newField = newNode.childNodes;
		for (var i=0;i<newField.length;i++) 
		{
			// Run through each select/input in the table row
			var inputs = newField[i].childNodes;
			for (var k=0;k<inputs.length;k++) 
			{
				// Break the name apart (eg, condition[1][0]) and incriment the value (eg, condition[2][0]) for the new row we're adding
				var theName = inputs[k].name;
				var theId = inputs[k].id;
				if (theName) {
					tokens = theName.split('[');
					n = tokens[2];
					inputs[k].name = id + '[' + start + ']['+ n;
				}
				// Break the ID apart (eg, condition_1) and incriment the value (eg, condition_2) for the new row we're adding
				if (theId) {
					idtokens = theId.split('_');
					idn = idtokens[2];
					inputs[k].id = id + '_' + start + '_'+ idn;
					// Set the value of the new condition <select> to whatever the previous row was
					if (inputs[k].id == id + '_' + start + '_0') {
						/*for (var z=0;z<inputs[k].options.length;z++) 
						{
							if (inputs[k].options[z].value == key) {
								inputs[k].options[z].selected = true;
								//inputs[k].remove(z);
							}
						}*/
						inputs[k].remove(firstList.selectedIndex);
						firstSelect = id + '_' + start + '_0';
						inputs[k].options[0].selected = true;
						Support.chosen.push(inputs[k].options[0].value);
						if (inputs[k].options.length == 1) {
							$('.add-condition').each(function(i, trigger) {
								$(trigger).css('visibility', 'hidden');
							});
						}
					}
				}
			}
		}
		// Add the new row to the table
		tbody.appendChild(newNode);

		Support.changeDynaList(document.getElementById(firstSelect));
		
		/*$('.condition-nouns').each(function(i, noun) {
			Support.chosen.push(noun.options[noun.selectedIndex].value);
		});*/
		$('.condition-nouns').each(function(i, noun) {
			var rmv = new Array();
			for (var i=0;i<noun.options.length;i++) 
			{
				if (i == noun.selectedIndex) {
					continue;
				}
				if (Support.in_array(noun.options[i].value,Support.chosen)) {
					rmv.push(i);
				}
				i++;
			}
			for (var i=0;i<rmv.length;i++) 
			{
				noun.remove(rmv[i]);
			}
		});
		
		// Remove the "add" button from the previous row
		var addcondition = lastNode.childNodes[3].childNodes[3];
		lastNode.childNodes[3].removeChild(addcondition);
		
		return false;
	},
	
	removeRow: function(el) {
		var row = el.parentNode.parentNode;
		var tby = row.parentNode;

		$('.condition-nouns').each(function(i, noun) {
			word = row.childNodes[1].childNodes[1].value; //Support.chosen[(Support.chosen.length - 1)];
			Support.addOption(noun,Support.conditions[word],word);
		});

		$('.add-condition').each(function(i, trigger) {
			$(trigger).css('visibility', 'visible');
		});

		var addcondition = null;
		if (el.parentNode.childNodes[3]) {
			switch (el.parentNode.childNodes[3].nodeType) 
			{
				case 1:
					// nodeType = html-object
					var addcondition = el.parentNode.childNodes[3];
				break;
				case 3:
					// nodeType = text
					var addcondition = el.parentNode.childNodes[4];
				break;
			}
		}

		tby.removeChild(row);
		
		if (addcondition !== null) {
			var counter = tby.rows.length;
			var lastNode = tby.rows[(counter - 1)];
			if (counter == 1) {
				$('.remove-condition').each(function(i, trigger) {
					$(trigger).css('visibility', 'hidden');
				});
			}
			lastNode.childNodes[3].appendChild(addcondition);
		}
		return false;
	},
	
	// empty the list
	emptyList: function( list ) {
		for (i in list.options.length) 
		{
			list.options[i] = null;
		}
		return list;
	},
	
	// repopulate the list
	repopulateList: function( list, key, source, orig_key, orig_val ) {
		var name = list.name;
		var id = list.id;
		i = 0;
		for (x in source) 
		{
			if (source[x][0] == key) {
				if (source[x][1] == '' || source[x][1] == ' ') {
					var txt  = document.createElement('input');
					txt.type = 'text';
					txt.name = name;
					txt.id   = id;
					if (key == 'created') {
						txt.value = '0000-00-00 00:00:00';
					}
					
					var parent = list.parentNode;
					parent.removeChild(list);
					parent.appendChild(txt);
					break;
				} else {
					opt = new Option();
					opt.value = source[x][1];
					opt.text  = source[x][2];

					if ((orig_key == key && orig_val == opt.value) || i == 0) {
						opt.selected = true;
					}
					list.options[i++] = opt;
				}
			}
		}
		list.length = i;
		return list;
	},
	
	changeDynaList: function( el ) {
		var theId = el.id;
		if (theId) {
			tokens = theId.split('_');
			var listname1 = tokens[0] + '_' + tokens[1] + '_' + '1';
			var listname2 = tokens[0] + '_' + tokens[1] + '_' + '2';
		}

		var key = el.options[el.selectedIndex].value;
		
		// List 1
		var list1 = document.getElementById(listname1);
		list1 = Support.emptyList( list1 );
		list1 = Support.repopulateList( list1, key, Support.verbs, Support.originalKey, Support.originalVal );
		
		// List 2
		var list2 = document.getElementById(listname2);
		// If it's a textbox, convert it back to a list
		if (list2.type && list2.type == 'text') {
			var sel2  = document.createElement('select');
			sel2.name = list2.name;
			sel2.id   = list2.id;
			
			var parent2 = list2.parentNode;
			parent2.removeChild(list2);
			parent2.appendChild(sel2);
			
			list2 = sel2;
		}
		list2 = Support.emptyList(list2);
		list2 = Support.repopulateList( list2, key, Support.nouns, Support.originalKey, Support.originalVal );
	},
	
	// User queries
	getCondition: function(id) {
		var tbody = document.getElementById(id).tBodies[0];
		//var counter = tbody.rows.length;
		//var val = new Array();
		
		var q = {}, expressions = [], nestedexpressions = [];
		q.operator = 'AND';
		
		for (var i=0; i<tbody.rows.length; i++) 
		{
			//val[i] = $('conditions_' + i + '_0').value + ':' + $('conditions_'+i+'_1').value + ':' + $('conditions_'+i+'_2').value;
			expressions[i] = {};
			expressions[i].colval  = $('#conditions_' + i + '_0').val();
			expressions[i].coldisp = $('#conditions_' + i + '_0').options[$('conditions_' + i + '_0').selectedIndex].text;
			expressions[i].opval   = $('#conditions_' + i + '_1').val();
			expressions[i].opdisp  = $('#conditions_' + i + '_1').options[$('conditions_' + i + '_1').selectedIndex].text;
			expressions[i].val     = $('#conditions_' + i + '_2').val()
		}
		q.expressions = expressions;
		
		// Get all the nested expressions
		/*if ($(elem[1]).find('table').length != 0) {
			var len = $(elem[1]).find('table').length;

			for (var k = 0; k < len; k++) {
				nestedexpressions[k] = getCondition($(elem[1]).find('table')[k]);
			}
		}*/
		q.nestedexpressions = nestedexpressions;
		//val = val.join(',');
		return q;
	},

	saveQuery: function(id) {
		
		var q = this.getCondition(id);
		console.log(JSON.stringify(q));
		/*var t = $('#save-con-title').val();
		$.get('index.php?option=com_support&task=savequery&no_html=1&t='+t+'&c='+val,{}, function(response){
			$('#saved-queries').html(response);
		});*/
	},
	
	deleteQuery: function(id) {
		$.get('index.php?option=com_support&task=deletequery&no_html=1&id='+id,{}, function(response){
			$('#saved-queries').html(response);
		});
	},
	
	// <select> manipulation
	/*removeOptions: function(selectbox) {
		for (var i=selectbox.options.length-1;i>=0;i--)
		{
			if (selectbox.options[i].selected) {
				selectbox.remove(i);
			}
		}
	},
	
	removeAllOptions: function(selectbox) {
		selectbox.length = 0;
	},*/
	
	addOption: function(selectbox, text, value) {
		var optn = document.createElement('option');
		optn.text = text;
		optn.value = value;
		selectbox.options.add(optn);
	}
}
