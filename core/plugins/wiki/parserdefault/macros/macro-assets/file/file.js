/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ($) {
	// Unity player
	$('.unityPlayer_macro').each(function(i, el){
		var config = {
			width: $(el).attr('data-width'),
			height: $(el).attr('data-height'),
			params: { enableDebugging:"0" }
		}
		var u = new UnityObject2(config);

		u.initPlugin($(el), $(el).attr('data-href'));
	});

	// CDF player
	$('.embedded-plugin').each(function(i, el){
		var cdf = new cdfplugin();
		var defaultContent = $(el).html();
		if (defaultContent != "") {
			cdf.setDefaultContent(defaultContent);
		}
		cdf.embed($(el).attr('data-href'), $(el).attr('data-width'), $(el).attr('data-height'));
	});
});
