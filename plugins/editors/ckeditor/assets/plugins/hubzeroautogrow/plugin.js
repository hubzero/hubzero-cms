if (!jq) {
	var jq = $;
}

(function() {
	var $ = jq;
	var codemirrorOn = false,
		codemirrorInit = false;
	
	CKEDITOR.plugins.add( 'hubzeroautogrow', {
		icons: 'hubzeroautogrow',
		init: function( editor ) {
			
			// define a list of events to autogrow on
			var eventList = ['key', 'mode'];
			
			// define ckeditor command
			editor.addCommand('hubzeroAutogrow', {
				modes : { 
					wysiwyg: 1, 
					source: 1 
				},
				exec: function( editor ) {
					
					// toggle state
					editor.commands.hubzeroAutogrow.toggleState();
					
					// if we just turned on lets run auto-grow
					if (editor.commands.hubzeroAutogrow.state == CKEDITOR.TRISTATE_ON)
					{
						resizeWorkingArea( { name: 'mode', editor: editor } );
					}
				}
			});
			
			// define UI button
			editor.ui.addButton( 'HubzeroAutoGrow', {
			    label: 'Auto-Grow Editing Area',
			    command: 'hubzeroAutogrow',
			});
			
			// code mirror is on
			if (editor.plugins.codemirror)
			{
				codemirrorOn = true
			}
			
			// do we want to auto start
			if (editor.config.hubzeroAutogrow_autoStart === true)
			{
				editor.commands.hubzeroAutogrow.setState(CKEDITOR.TRISTATE_ON)
			}
			
			// add event hooks
			for (var i = 0, n = eventList.length; i < n; i++)
			{
				editor.on(eventList[i], function( event ){
					resizeWorkingArea( event );
				});
			}
			
			// listen for maximize event firing
			editor.on( 'afterCommandExec', function( event ) {
				if (event.data.name == 'maximize')
				{
					if (event.data.command.state != CKEDITOR.TRISTATE_ON)
					{
						resizeWorkingArea( event );
					}
				}
			});
			
			// codemirror
			editor.on('mode', function(event) {
				
				if (event.editor.mode == 'source' && !codemirrorInit && codemirrorOn)
				{
					setTimeout(function(){
						window['codemirror_' + editor.id].on('keydown', function(codemirror, event){
							resizeWorkingArea({ 
								name: 'key', 
								data: {
									keyCode: event.keyCode
								},
								editor: editor 
							})
						});
					}, 1000);
				}
			});
		}
	});
	
	function resizeWorkingArea( event ) 
	{
		// setup vars
		var newHeight  = 0, 
			lineHeight = 0,
			editor     = event.editor,
			contents   = editor.ui.space('contents')
			maximize   = editor.getCommand( 'maximize' )
			element    = null;
		
		//make sure to only run auto grow if we have it on
		if (editor.commands.hubzeroAutogrow.state == CKEDITOR.TRISTATE_OFF)
		{
			return;
		}
		
		// Disable autogrow when the editor is maximized
		if (maximize && maximize.state == CKEDITOR.TRISTATE_ON)
		{
			return;
		}
		
		//reset editor height to 0
		editor.resize(editor.container.getStyle('width'), 0, true);
		
		// are we in source mode?
		if (editor.mode == 'source')
		{
			element = $('#'+contents.$.id).find('textarea');
			if (codemirrorOn)
			{
				element = $('#'+contents.$.id).find('.CodeMirror-sizer'); 
			}
			newHeight  = element.prop('scrollHeight');
			
			lineHeight = parseInt(element.css('line-height'));
			
			// add extra if we just hit return (Not sure why??)
			if (event.name == 'key' && event.data.keyCode == 13)
			{
				newHeight += lineHeight;
			}

			// make sure we follow honor or min and max config values
			newHeight = Math.max( newHeight, editor.config.hubzeroAutogrow_minHeight );
			newHeight = Math.min( newHeight, editor.config.hubzeroAutogrow_maxHeight );
			
			// resize the editor
			editor.resize( editor.container.getStyle('width'), newHeight, true );
		}
		else if (editor.mode == 'wysiwyg')
		{
			//get the iframe
			element = $('#'+contents.$.id).find('iframe');
			
			//get iframe height
			iframeHeight = getIframeHeight( element, event );
			editor.resize( editor.container.getStyle('width'), iframeHeight, true );
			
			// on iframe load
			element.load(function() {
				
				//get iframe height
				iframeHeight = getIframeHeight( element, event );
				
				// resize the editor
				editor.resize( editor.container.getStyle('width'), iframeHeight, true );
			});
		}
	}
	
	function getIframeHeight( iframe, event )
	{
		// get needed dimensions
		var newHeight  = iframe.contents().find("html").height(),
			lineHeight = parseInt(iframe.contents().find("body").css('line-height'));
		
		// add extra if we just hit return (Not sure why??)
		if (event.name == 'key' && event.data.keyCode == 13 && !isNaN(lineHeight))
		{
			newHeight += lineHeight;
		}
		
		// make sure we follow honor or min and max config values
		newHeight = Math.max( newHeight, event.editor.config.hubzeroAutogrow_minHeight );
		newHeight = Math.min( newHeight, event.editor.config.hubzeroAutogrow_maxHeight );
		
		return newHeight;
	}
})();