/**
 * @package     hubzero-cms
 * @file        components/com_citations/citations.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Scripts for the NCNReporting component
//----------------------------------------------------------
var Citations = {	
	addRow: function(id) {
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
					inputs[k].name = id + '[' + counter + ']['+ n;
				}
				//var n = id + '[' + counter + '][type]';
				//var z = id + '[' + counter + '][status]';
				//if (inputs[k].value && inputs[k].name != n && inputs[k].name != z) {
				if (inputs[k].value) {
					inputs[k].value = '';
				}
			}
		}
		tbody.appendChild(newNode);
		return false;
	}
}

