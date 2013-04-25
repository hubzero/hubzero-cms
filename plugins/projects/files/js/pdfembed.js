/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectFilesPdfEmbed = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;
		
		if ($('#compiled-doc').length)
		{
			var pUrl = $("#compiled-doc").attr("embed-src");
			var pWidth = $("#compiled-doc").attr("embed-width");
			var pHeight = $("#compiled-doc").attr("embed-height");
			
			var myPDF = new PDFObject( {
			  url: pUrl,
			  id: "pdf_content",
			  width: pWidth,
			  height: pHeight
			}).embed("compiled-doc");
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectFilesPdfEmbed.initialize();
});