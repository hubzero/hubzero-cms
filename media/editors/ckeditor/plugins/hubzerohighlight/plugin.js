(function() {
	//CKEDITOR
	CKEDITOR.plugins.add( 'hubzerohighlight', {
		init: function( editor ) {
			var mode = '';
			//console.log(editor);
			editor.on('mode', function(event) {
				// run
				highlight();

				// add css for mark elements
				if (editor.mode != 'source')
				{
					this.document.appendStyleSheet('/media/editors/ckeditor/plugins/hubzerohighlight/plugin.css');
				}
			});

			editor.on('beforeSave', function(event) {
				
				var data  = this.getData();

				// remove old mark tags
				data = data.replace(/<mark class="macro">/g, '');
				data = data.replace(/<\/mark>/g, '');
				data = data.replace(/<mark class="group-include">/g, '');
				data = data.replace(/<\/mark>/g, '');
				data = data.replace(/<mark class="xhubtag">/g, '');
				data = data.replace(/<\/mark>/g, '');

				// set new data
				this.setData(data);
			});

			editor.on('blur', function(event) {
				highlight();

				// add css for mark elements
				if (editor.mode != 'source')
				{
					this.document.appendStyleSheet('/media/editors/ckeditor/plugins/hubzerohighlight/plugin.css');
				}
			});

			function highlight()
			{
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
		}
	});
})();