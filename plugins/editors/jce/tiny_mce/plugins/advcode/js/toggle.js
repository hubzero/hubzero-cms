/**
* @version		$Id: toggle.js 72 2009-06-03 18:13:26Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

var AdvCode = {
	/**
	 * Initialise the AdvCode Object
	 * @param {String} link The toggle link text
	 * @param {String} state The default editor state
	 * @param {String} toggle Allow toggle
	 */
	init: function(link, state, toggle){
		var t = this, dom = tinymce.DOM, Event = tinymce.dom.Event;
		tinymce.each(dom.select('textarea.mceEditor'), function(el){
			var cookie = tinymce.util.Cookie.get('jce_editor_' + el.id + '_state');
			
			if (parseInt(state) == 0) {
				el.className = 'mceNoEditor';
			}
			else {
				if (parseInt(cookie) == 0) {
					el.className = 'mceNoEditor';
				}
				else {
					el.className = 'mceEditor';
				}
			}
			if (parseInt(toggle) == 1) {
				var div = dom.create('div', {
					'class': 'advcode_toggle'
				}, link);
				
				dom.setStyle(div, 'cursor', 'pointer');
				el.parentNode.insertBefore(div, el);
				
				Event.add(div, 'click', function(e){
					if (el.className == 'mceNoEditor') {
						tinyMCE.execCommand('mceAddControl', false, el.id);
						tinymce.util.Cookie.set('jce_editor_' + el.id + '_state', 1);
						el.className = 'mceEditor';
					}
					else {
						tinyMCE.execCommand('mceRemoveControl', false, el.id);
						tinymce.util.Cookie.set('jce_editor_' + el.id + '_state', 0);
						el.className = 'mceNoEditor';
					}
				});
			}
		});
	}
}