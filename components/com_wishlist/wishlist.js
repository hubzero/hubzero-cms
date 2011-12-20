/**
 * @package     hubzero-cms
 * @file        components/com_wishlist/wishlist.js
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
// Answers Scripts
//----------------------------------------------------------
HUB.Wishlist = {
	initialize: function() {
		//------------------------
		// reply to review or comment
		//------------------------
		var add = $$('.addcomment');
		var show = $$('.reply');
		if (show) {
			for (i = 0; i < add.length; i++) 
			{
				//if(add[i].hasClass('hide') == true) {
				//add[i].style.display = "none"; // hide form	
				//}
				
			}
			
			if ($$('.reply')) {		
				$$('.reply').each(function(item) { 
					item.addEvent('click', function(e) {
							new Event(e).stop();
							
							var vnum = this.getProperty('id').replace('rep_','');
							if ($('comm_' + vnum).hasClass('hide')) {
								$('comm_' + vnum).removeClass('hide');
								t = $('comm_' + vnum).getElement('.commentarea');
								t.value = 'Enter your comments...';
								//$('comm_' + vnum).style.display = "block";
							} else {
								$('comm_' + vnum).addClass('hide');
								//$('comm_' + vnum).style.display = "none";
							}
						}
					);
				});
			}
			
			// show hide comments area
			if ($('section-comments') && $('part_com')) { 
				$('part_com').addEvent('click', function() {
					if ($('part_com').hasClass('collapse')) {
						$('part_com').removeClass('collapse');
						$('full_com').style.display = "none";
						$('part_com').addClass('expand');
					} else {
						$('part_com').removeClass('expand');
						$('full_com').style.display = "block";
						$('part_com').addClass('collapse');
					}
					return false;
				});
			}
			
			// show/hide plan area
			if ($('section-plan') && $('part_plan')) { 
				$('part_plan').addEvent('click', function() {
					if ($('part_plan').hasClass('collapse')) {
						$('part_plan').removeClass('collapse');
						$('full_plan').style.display = "none";
						$('part_plan').addClass('expand');
					} else {
						$('part_plan').removeClass('expand');
						$('full_plan').style.display = "block";
						$('part_plan').addClass('collapse');
					}
					return false;
				});
			}
			
			// due date
			if ($('nodue')) { 
				var frm = $('hubForm');
				$('nodue').addEvent('click', function() {
					frm.publish_up.value = '';
				});
			}
			
			if ($$('.commentarea')) {	
				$$('.commentarea').each(function(item) {
					// clear the default text						 
					item.addEvent('focus', function() {	
							if (item.value =='Enter your comments...') {
								item.value = '';
							}
						}
					);
				});
			}
			
			if ($$('.closeform')) {		
				$$('.closeform').each(function(item) {
					// clear the default text						 
					item.addEvent('click', function() {	
							var vnum = this.getProperty('id').replace('close_','');
							$('comm_' + vnum).addClass('hide');
							$('comm_' + vnum).style.display = "none";
						}
					);
				});
			}
		}
		
	},

	setZindex: function(el) {
		var LIs = el.parentNode.parentNode.parentNode.getElementsByTagName('li');

		if (LIs) {
			for (i = 0; i < LIs.length; i++) {
				LIs[i].style.zIndex = 1;
			}
		}
	}
}

window.addEvent('domready', HUB.Wishlist.initialize);

