/**
 * @package     hubzero-cms
 * @file        components/com_myhub/myhub.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-------------------------------------------------------------
// My Hub (singleton)
//-------------------------------------------------------------

HUB.Members.Profile = {
	
	init: function()
	{
		//each tel link
		$$("a.phone").each(function(el) {
			var mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null; 
			var link = (mobile) ? "tel: " + el.innerHTML : "callto://" + el.innerHTML;
			el.setProperty("href", link);
		});
		
		//save privacy
		$$("td.privacy select").each(function(el) {
			el.addEvent("change", function(e) {
				//get the privacy level
				var privacy = el.selectedIndex;
				
				//get the table row
				var table_row = el.getParent().getParent();
				
				//clear any private class on table row
				table_row.removeClass("private");

				//add private class if privacy has been set to private
				if(privacy == 2) {
					table_row.addClass("private");
				}
				
				$("member-profile").send({
					onComplete: function() {
						if (typeof(Growl) != "undefined") {
							Growl.Bezel({
								image: '/components/com_members/images/save.png',
								title: 'Profile Saved',
								text: 'You have successfully saved your profile privacy settings.',
								duration: 2
							});
						} else {
							alert("Profile Saved");
						}
					}
				});
			});
		});
	}
	
	//-----
};

//-----

window.addEvent('domready', HUB.Members.Profile.init);

