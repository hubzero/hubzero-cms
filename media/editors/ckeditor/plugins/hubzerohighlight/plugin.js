(function() {
	//CKEDITOR
	CKEDITOR.plugins.add( 'hubzerohighlight', {
		init: function( editor ) {
			var $      = (typeof(jq) !== "undefined" ? jq : jQuery),
				mode   = '', 
				plugin = this,
				ready  = false;

			editor.on('instanceReady', function(event){
				ready = true;

				// highlight
				plugin.highlight(editor);

				// add css for mark elements
				if (editor.mode != 'source')
				{
					this.document.appendStyleSheet('/media/editors/ckeditor/plugins/hubzerohighlight/plugin.css');
				}
			});

			editor.on('mode', function(event) {
				if (ready)
				{
					console.log('mode highlight');
					plugin.highlight(editor);

					// add css for mark elements
					if (editor.mode != 'source')
					{
						this.document.appendStyleSheet('/media/editors/ckeditor/plugins/hubzerohighlight/plugin.css');
					}
				}
			});

			editor.on('blur', function(event) {
				if (ready)
				{
					console.log('blur highlight');
					plugin.highlight(editor);

					// add css for mark elements
					if (editor.mode != 'source')
					{
						this.document.appendStyleSheet('/media/editors/ckeditor/plugins/hubzerohighlight/plugin.css');
					}
				}
			});
			
			var form = $(editor.element.$.form);
			form.submit(function(event) {
				var data  = editor.getData();

				// remove old mark tags
				data = data.replace(/<mark class="macro">/g, '');
				data = data.replace(/<\/mark>/g, '');
				data = data.replace(/<mark class="group-include">/g, '');
				data = data.replace(/<\/mark>/g, '');
				data = data.replace(/<mark class="xhubtag">/g, '');
				data = data.replace(/<\/mark>/g, '');

				// set new data
				editor.setData(data);
			});
		},

		highlight: function( editor )
		{
			// get current data
			var data  = editor.getData();

			// remove old mark tags
			data = data.replace(/<mark class="macro">/g, '');
			data = data.replace(/<\/mark>/g, '');
			data = data.replace(/<mark class="group-include">/g, '');
			data = data.replace(/<\/mark>/g, '');
			data = data.replace(/<mark class="xhubtag">/g, '');
			data = data.replace(/<\/mark>/g, '');

			// add new mark tags
			if (editor.mode == 'wysiwyg')
			{
				data = data.replace(/(\[\[[^\]]*]])/g, '<mark class="macro">$1</mark>');
				data = data.replace(/(<group:include[^>]*>)/g, '<mark class="group-include">$1</mark>');
				data = data.replace(/({xhub:[^}]*})/g, '<mark class="xhubtag">$1</mark>');
			}

			// set new data
			editor.setData(data);
		}
	});
})();