/**
 * @version		$Id: editor.js 137 2009-06-26 10:22:17Z happynoodleboy $
 * @package      JCE
 * @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license      GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

/**
 * JContentEditor Object
 */
var JContentEditor = {

	/**
     * Set the editor content
     * @param {String} id The editor id
     * @param {String} html The html content to set
     */
    setContent: function(id, html) {
        if (tinyMCE.get(id)) {
            tinyMCE.activeEditor.setContent(html);
        } else {
            document.getElementById(id).value = html;
        }
    },
	
    /**
     * Get the editor content
     * @param {String} id The editor id
     */
    getContent: function(id) {
        if (tinyMCE.get(id)) {
            return tinyMCE.activeEditor.getContent();
        }
        return document.getElementById(id).value;
    },
	
    /**
     * Save the editor content
     * @param {String} id The editor id
     */
    save: function(id) {
        var ed = tinyMCE.get(id);
        if (ed && !ed.getContent()) {
            ed.setContent(ed.getElement().value);
        }
        tinyMCE.triggerSave();
    },
    
    /**
     * Insert content into the editor. This function is provided for editor-xtd buttons and includes methods for inserting into textareas
     * @param {String} el The editor id
     * @param {String} v The text to insert
     */
    insert: function(el, v) {
        var bm, ed;
		if (typeof el == 'string') {
            el = document.getElementById(el);
        }
        if (/mceEditor/.test(el.className)) {
            ed = tinyMCE.get(el.id);
            ed.execCommand('mceInsertContent', false, v, true);
        } else {
            // IE
            if (document.selection) {
                el.focus();
                s = document.selection.createRange();
                s.text = v;
                // Mozilla / Netscape
            } else if (el.selectionStart || el.selectionStart == '0') {
                var startPos = el.selectionStart;
                var endPos = el.selectionEnd;
                el.value = el.value.substring(0, startPos) + v + el.value.substring(endPos, el.value.length);
            // Other
            } else {
                el.value += v;
            }
        }
    }
};