/**
 * @package     hubzero-cms
 * @file        components/com_answers/answers.js
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
HUB.Answers = {
	initialize: function() {
		//------------------------
		// reply to review or comment
		//------------------------
		var add = $$('.addcomment');
		var show = $$('.showreplyform');
		if (add) {
			for (i = 0; i < add.length; i++) 
			{
				if (add[i].hasClass('hide') == true) {
					add[i].style.display = "none"; // hide form	
				}
				if (show) {	
					show[i].onclick=function() {
						p = $(this.parentNode.parentNode.parentNode).getElement('.addcomment');
						p.style.display = "block";
						t = p.getElement('.commentarea');
						t.value = 'Enter your comments...';
						return false;
					}
				}
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
							$(item.parentNode.parentNode.parentNode.parentNode).style.display = 'none';
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

window.addEvent('domready', HUB.Answers.initialize);

