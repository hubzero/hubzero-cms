/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	$('.datepicker').datepicker({
		dateFormat: 'yy-mm',//'mm/yy',
		minDate: '-10Y',
		maxDate: 0
	});
});
