﻿/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

(function() {
	
	CKEDITOR.plugins.add( 'hubzeromacro', {
		icons: 'hubzeromacro',
		init: function( editor ) {
			
			// define ckeditor command
			var c = editor.addCommand('hubzeroMacroDialogCommand', new CKEDITOR.dialogCommand('hubzeroMacroDialog'));
			c.modes = { source: 1, wysiwyg: 1};
			
			// setup toolbar
			editor.ui.addButton( 'HubzeroMacro', {
			    label: 'Add Macro',
			    command: 'hubzeroMacroDialogCommand',
			});
			
			// add dialogs
			CKEDITOR.dialog.add(
				'hubzeroMacroDialog', 
				this.path + 'dialogs/macro.js'
			);
		}
	});
	
})();