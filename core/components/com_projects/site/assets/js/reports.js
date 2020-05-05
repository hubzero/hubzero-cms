/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	$('.datepicker').datepicker({
		dateFormat: 'yy-mm',//'mm/yy',
		minDate: '-10Y',
		maxDate: 0
	});
});
