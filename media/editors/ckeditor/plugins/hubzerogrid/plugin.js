(function() {
	
	CKEDITOR.plugins.add( 'hubzerogrid', {
		icons: 'hubzerogrid',
		init: function( editor ) {
			
			// define ckeditor command
			var c = editor.addCommand('hubzeroGridDialogCommand', new CKEDITOR.dialogCommand('hubzeroGridDialog'));
			c.modes = { source: 1, wysiwyg: 1};
			//
			
			// setup toolbar
			editor.ui.addButton( 'HubzeroGrid', {
			    label: 'Add Grid',
			    command: 'hubzeroGridDialogCommand',
			});
			
			// add dialogs
			CKEDITOR.dialog.add(
				'hubzeroGridDialog', 
				this.path + 'dialogs/grid.js'
			);
		}
	});
	
})();