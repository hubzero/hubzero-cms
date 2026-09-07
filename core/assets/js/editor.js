/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 * HUB.Editor - Editor-agnostic abstraction layer
 *
 * Provides a registry for editor instances so application code
 * can interact with any editor (CKEditor 4, CKEditor 5, etc.)
 * without referencing editor-specific globals.
 */
if (typeof HUB === 'undefined') {
	var HUB = {};
}

HUB.Editor = {

	/**
	 * Registered editor adapters keyed by textarea ID
	 * Each adapter: { getData, setData, updateElement, instance }
	 */
	_instances: {},

	/**
	 * File browser callbacks keyed by callback ID
	 */
	_fileBrowserCallbacks: {},

	/**
	 * Next auto-increment callback ID
	 */
	_nextCallbackId: 1,

	/**
	 * Register an editor instance
	 *
	 * @param {string} id        The textarea element ID
	 * @param {object} adapter   Object with methods: getData, setData, updateElement
	 *                           and optional: instance (raw editor reference)
	 */
	register: function(id, adapter) {
		this._instances[id] = adapter;
	},

	/**
	 * Unregister an editor instance
	 *
	 * @param {string} id  The textarea element ID
	 */
	unregister: function(id) {
		delete this._instances[id];
	},

	/**
	 * Check if an editor instance is registered
	 *
	 * @param  {string}  id  The textarea element ID
	 * @return {boolean}
	 */
	has: function(id) {
		return id in this._instances;
	},

	/**
	 * Get editor content. Falls back to textarea value if no editor registered.
	 *
	 * @param  {string} id  The textarea element ID
	 * @return {string}
	 */
	getData: function(id) {
		if (this._instances[id] && typeof this._instances[id].getData === 'function') {
			return this._instances[id].getData();
		}
		var el = document.getElementById(id);
		return el ? el.value : '';
	},

	/**
	 * Set editor content
	 *
	 * @param {string} id    The textarea element ID
	 * @param {string} html  The content to set
	 */
	setData: function(id, html) {
		if (this._instances[id] && typeof this._instances[id].setData === 'function') {
			this._instances[id].setData(html);
			return;
		}
		var el = document.getElementById(id);
		if (el) {
			el.value = html;
		}
	},

	/**
	 * Sync editor content to its source textarea
	 *
	 * @param {string} id  The textarea element ID
	 */
	updateElement: function(id) {
		if (this._instances[id] && typeof this._instances[id].updateElement === 'function') {
			this._instances[id].updateElement();
		}
	},

	/**
	 * Sync all registered editor instances to their textareas
	 */
	updateAllElements: function() {
		for (var id in this._instances) {
			if (this._instances.hasOwnProperty(id)) {
				this.updateElement(id);
			}
		}
	},

	/**
	 * Append text to editor content
	 *
	 * @param {string} id    The textarea element ID
	 * @param {string} text  Text to append
	 */
	insertText: function(id, text) {
		var content = this.getData(id);
		this.setData(id, content + text);
	},

	/**
	 * Register a file browser callback
	 *
	 * @param  {function} fn  Callback function(file, done)
	 * @return {number}       Callback ID to pass to file browser popup
	 */
	registerFileBrowserCallback: function(fn) {
		var callbackId = this._nextCallbackId++;
		this._fileBrowserCallbacks[callbackId] = fn;
		return callbackId;
	},

	/**
	 * Remove a file browser callback
	 *
	 * @param {number} callbackId  The callback ID returned by registerFileBrowserCallback
	 */
	unregisterFileBrowserCallback: function(callbackId) {
		delete this._fileBrowserCallbacks[callbackId];
	},

	/**
	 * Execute a file browser callback (called from file browser popup)
	 *
	 * The callback ID arrives from a URL query string, so it is a string here
	 * and a number where it was registered. Object keys are strings either way.
	 *
	 * @param  {number}   callbackId  The callback ID
	 * @param  {string}   file        The file URL to insert
	 * @param  {function} done        Optional completion callback
	 * @return {boolean}              True if a callback handled the file
	 */
	insertFile: function(callbackId, file, done) {
		var fn = this._fileBrowserCallbacks[callbackId];

		if (!fn) {
			if (window.console && console.warn) {
				console.warn('HUB.Editor: no file browser callback registered for id ' + callbackId);
			}
			return false;
		}

		fn(file, done);

		return true;
	}
};

/**
 * Legacy global entry points.
 *
 * Application code predating HUB.Editor calls these directly, and each editor
 * plugin used to define its own copy. Defining them here means they work for
 * whichever editor is configured; an editor with its own version (CKEditor 4)
 * loads after this file and overrides them.
 */
function jInsertEditorText(text, editor) {
	HUB.Editor.insertText(editor, text);
}

function jSaveEditorText() {
	HUB.Editor.updateAllElements();
}

function getEditorContent(id) {
	HUB.Editor.updateElement(id);
	return HUB.Editor.getData(id);
}

function setEditorContent(id, content) {
	HUB.Editor.setData(id, content);
}

// Listen for editorSave events (legacy compatibility)
if (typeof jQuery !== 'undefined') {
	jQuery(document).on('editorSave', function() {
		HUB.Editor.updateAllElements();
	});
}
