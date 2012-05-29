/**
 * @package     hubzero-cms
 * @file        components/com_members/members.js
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

HUB.Members = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		if (!jQuery().fancybox) {
			return;
		}
		
		//move the modules button to top
		if ($("#personalize")) {
			$("#personalize").appendTo($("#page_options"));
			$("#personalize").removeClass("hide");
		}
		
		$('a.message').fancybox({
			type: 'ajax',
			width: 700,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			tpl: {
				wrap:'<div class="fancybox-wrap"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div>'
			},
			beforeLoad: function() {
				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);	
			},
			afterShow: function() {
				if ($('#hubForm-ajax')) {
					$('#hubForm-ajax').submit(function(e) {
						e.preventDefault();
						$.post($(this).attr('action'), $(this).serialize(), function(returndata) {
							$.fancybox().close();
						});
					});
				}
			}
		});
		
		$("#page_menu li").each(function(index){
			var meta = $(this).find(".meta"),
				metawidth = meta.outerWidth(true),
				alrt = $(this).find(".alrt");
			
			if(alrt.length)
			{
				if(metawidth > 20)
				{
					alrt.css("right", 33+(metawidth-20));
				}
				else if(metawidth < 20 && metawidth != 0)
				{
					alrt.css("right", 33-(20-metawidth));
				}
			} 
		});
		
		
		$("#member-stats-graph").fancybox({
			fitToView: true,
			title:'',
			beforeShow: function()
			{
				$(".fancybox-inner img").css("width","100%");
			}
		});
		
	}
	
}

jQuery(document).ready(function($){
	HUB.Members.initialize();
});