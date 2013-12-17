(function() {
	
	CKEDITOR.plugins.add( 'hubzeroequation', {
		icons: 'hubzeroequation',
		init: function( editor ) {
			
			// define ckeditor command
			editor.addCommand(
				'hubzeroEquationDialog', 
				new CKEDITOR.dialogCommand('hubzeroEquationDialog') 
			);
			
			// setup toolbar
			editor.ui.addButton( 'HubzeroEquation', {
			    label: 'Add Equation',
			    command: 'hubzeroEquationDialog',
			});
			
			// add dialogs
			CKEDITOR.dialog.add(
				'hubzeroEquationDialog', 
				this.path + 'dialogs/equation.js'
			);
			
			// 
			editor.on( 'doubleclick', function(evt) {
				var element = evt.data.element;
				if (element && element.is('img')) 
				{
					if ( element.getAttribute('class') ===  'hubzeroequation-result') 
					{
						evt.data.dialog = 'hubzeroEquationDialog';
						evt.cancelBubble = true;
						evt.returnValue = false;
						evt.stop();
					}
				}
			}, null, null, 1);
			
			// add context-menu entry
			if ( editor.contextMenu ) 
			{
				editor.addMenuGroup( 'Math' );
				editor.addMenuItem( 'hubzeroequation', {
					label: 'Edit function',
					icon: this.path + 'icons/hubzeroequation.png',
					command: 'hubzeroEquationDialog',
					group: 'Math'
				});
				
				editor.contextMenu.addListener( function(element) {
					var res = {};
					if (element) 
					{
						element = element.getAscendant( 'img', true );
					}
					if ( element && ! element.data('cke-realelement') && element.getAttribute('class') === 'hubzeroequation-result' ) 
					{
						res['hubzeroequation'] = CKEDITOR.TRISTATE_OFF;
						return res;
					}
				});
			}
		}
	});
	
})();