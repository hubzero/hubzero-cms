/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Project Setup JS
//----------------------------------------------------------

if (!jq) {
	var jq = $;
}

HUB.ProjectReports = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;
			
		$('.datepicker').each(function(i, el) {
			$( this ).datepicker({
				dateFormat: 'mm/yy',
				minDate: '-10Y',
				maxDate: 0
			});
		});
		
	}

}
	
jQuery(document).ready(function($){
	HUB.ProjectReports.initialize();
});