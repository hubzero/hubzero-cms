(function() {
	//CKEDITOR
	CKEDITOR.plugins.add( 'hubzerohighlight', {
		init: function( editor ) {
			var $      = jq,
				mode   = '', 
				plugin = this;

			// dont highlight for ie8
			if ($('html').hasClass('ie8'))
			{
				return;
			}

			editor.on('mode', function(event) {
				plugin.highlight(editor);

				// add css for mark elements
				if (editor.mode != 'source')
				{
					this.document.appendStyleSheet('/media/editors/ckeditor/plugins/hubzerohighlight/plugin.css');
				}
			});

			editor.on('blur', function(event) {
				plugin.highlight(editor);

				// add css for mark elements
				if (editor.mode != 'source')
				{
					this.document.appendStyleSheet('/media/editors/ckeditor/plugins/hubzerohighlight/plugin.css');
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
			console.log('highlight');

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