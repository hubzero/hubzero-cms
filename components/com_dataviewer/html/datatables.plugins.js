/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */


jQuery.fn.dataTableExt.oApi.fnFilterClear  = function (oSettings) {

    /* Remove global filter */
    oSettings.oPreviousSearch.sSearch = "";
      
    /* Remove the text of the global filter in the input boxes */
    if ( typeof oSettings.aanFeatures.f != 'undefined' )
    {
        var n = oSettings.aanFeatures.f;
        for ( var i=0, iLen=n.length ; i<iLen ; i++ )
        {
            jQuery('input', n[i]).val( '' );
        }
    }
      
    /* Remove the search text for the column filters */
    for ( var i=0, iLen=oSettings.aoPreSearchCols.length ; i<iLen ; i++ )
    {
        oSettings.aoPreSearchCols[i].sSearch = "";
        jQuery('th>input', oSettings.nTFoot)[i].value = '';
    }
      
    /* Redraw */
    oSettings.oApi._fnReDraw( oSettings );
};


jQuery.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
	// check that we have a column id
	if ( typeof iColumn == "undefined" ) return [];

	// by default we only wany unique data
	if ( typeof bUnique == "undefined" ) bUnique = true;

	// by default we do want to only look at filtered data
	if ( typeof bFiltered == "undefined" ) bFiltered = true;

	// by default we do not wany to include empty values
	if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;

	// list of rows which we're going to loop through
	var aiRows;

	// use only filtered rows
	if (bFiltered == true) aiRows = oSettings.aiDisplay; 
	// use all rows
	else aiRows = oSettings.aiDisplayMaster; // all row numbers

	// set up data array
	var asResultData = new Array();

	for (var i=0,c=aiRows.length; i<c; i++) {
		iRow = aiRows[i];
		var sValue = this.fnGetData(iRow, iColumn);
		sValue = ('' + sValue).stripTags().trim();

		// ignore empty values?
		if (bIgnoreEmpty == true && sValue.length == 0) continue;

		// ignore unique values?
		else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;

		// else push the value onto the result data array
		else asResultData.push(sValue);
	}

	return asResultData;
};


jQuery.fn.dataTableExt.oApi.fnGetFilteredData = function(oSettings) {
	var a = [];
	var i;
	for (i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++) {
		a.push(oSettings.aoData[ oSettings.aiDisplay[i] ]._aData);
	}
	return a;
};

jQuery.fn.dataTableExt.oSort['number-asc']  = function(a,b) {

	if (a === b) { return 0; }
	if (jQuery(a).text().trim() === '-') { return -1; }
	if (jQuery(b).text().trim() === '-') { return 1; }

	var x = a.stripTags();
	var y = b.stripTags();

	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['number-desc'] = function(a,b) {

	if (a === b) { return 0; }
	if (jQuery(a).text().trim() === '-') { return 1; }
	if (jQuery(b).text().trim() === '-') { return -1; }

	var x = a.stripTags();
	var y = b.stripTags();

	x = parseFloat( x );
	y = parseFloat( y );

	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['numrange-asc']  = function(a,b) {
	if (a === b) { return 0; }
	if (jQuery(a).text() === '-') { return -1; }
	if (jQuery(b).text() === '-') { return 1; }

	var x_min = +jQuery(a).data('min');
	var x_max = +jQuery(a).data('max');
	var y_min = +jQuery(b).data('min');
	var y_max = +jQuery(b).data('max');

	if (x_max === y_max) {
		y = y_min;
	} else {
		y = y_max
	}

	x = x_max;

	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['numrange-desc']  = function(a,b) {
	if (a === b) { return 0; }
	if (jQuery(a).text() === '-') { return 1; }
	if (jQuery(b).text() === '-') { return -1; }

	var x_min = +jQuery(a).data('min');
	var x_max = +jQuery(a).data('max');
	var y_min = +jQuery(b).data('min');
	var y_max = +jQuery(b).data('max');

	if (x_min === y_min) {
		y = y_max;
	} else {
		y = y_min;
	}

	x = x_min;

	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};


jQuery.fn.dataTableExt.oSort['datetime-desc'] = jQuery.fn.dataTableExt.oSort['date-desc'];
jQuery.fn.dataTableExt.oSort['datetime-asc'] = jQuery.fn.dataTableExt.oSort['date-asc'];


jQuery.fn.dataTableExt.ofnSearch['number'] = function (data) {
	return data.replace(/\n/g, " ").replace(/&nbsp;/g, "").stripTags();
};
