/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
