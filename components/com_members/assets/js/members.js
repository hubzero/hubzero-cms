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

HUB.Members = {
	initialize: function() {
		HUB.Members.menu();
		HUB.Members.dashboard();
		HUB.Members.messageMember();
	},
	
	//-----
	
	menu: function()
	{   
		$$("#page_menu li").each(function(element, index){
			var meta = element.getElements(".meta"),
				alrt = element.getElements(".alrt"),
				metasize = meta.getSize()[0],
				oldpos = 33;
			
			if(metasize.size.x > 20)
			{
				diff = metasize.size.x - 20;
				alrt.setProperty("style", "right:"+(33+diff)+"px");
			}
			else if(metasize.size.x < 20 && metasize.size.x != 0)
			{
				diff = 20 - metasize.size.x;
				alrt.setProperty("style", "right:"+(33-diff)+"px");
			}
		});
		
	},
	
	//-----
	
	messageMember: function()
	{
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) {
				SqueezeBoxHub.initialize({ size: {x: 300, y: 375} });
			}
			
			// Modal boxes member messaing
			$$('a.message').each(function(el) {
				if (el.href.indexOf('?') == -1) {
					el.href = el.href + '?no_html=1';
				} else {
					el.href = el.href + '&no_html=1';
				}
				el.addEvent('click', function(e) {
					new Event(e).stop();

					SqueezeBoxHub.fromElement(el,{
						handler: 'url', 
						size: {x: 700, y: 418}, 
						ajaxOptions: {
							evalScripts: true,
							method: 'get',
							onComplete: function() {
								frm = $('hubForm-ajax');
								if (frm) {
									frm.addEvent('submit', function(e) {
										new Event(e).stop();
										frm.send({
											//update: $('sbox-content'),
											onComplete: function() {
												SqueezeBoxHub.close();
											}
								        });
									});
								}
							}
						}
					});
				});
			});
		}
	},
	
	//-----
	
	dashboard: function()
	{
		//move the modules button to top
		if( $("personalize") )
		{
			$("personalize").injectInside( $("page_options") );
			$("personalize").removeClass("hide");
		}
	}
	
	//-----
};

window.addEvent('domready', HUB.Members.initialize);

//------------------------------------------------------------                                                   
