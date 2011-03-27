/**
 * @package     hubzero-cms
 * @file        components/com_kb/kb.js
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
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Kb = {
	initialize: function() {
		// Voting
		$$('.vote-button').each(function(v) {
			v.addEvent('click', function(e) {
				new Event(e).stop();
				if (this.href) {
					new Ajax(this.href+'?no_html=1',{
						'method' : 'get',
						'update' : $(this.parentNode.parentNode)
					}).request();
				}
			});
		});
	} // end initialize
}

window.addEvent('domready', HUB.Kb.initialize);
