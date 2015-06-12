CKEDITOR.dialog.add( 'hubzeroGridDialog', function( editor ) {
	return {
		title: 'Grid Creator',
		minWidth: 400,
		minHeight: 300,
		resizable: false,
		contents: [
			{
				id: 'colbasic',
				label: 'Basic Settings',
				elements: [
					{
						type: 'select',
						id: 'colcount',
						label: 'Number of Columns:',
						items: [['One','one'],['Two','two'],['Three','three'],['Four','four'],['Five','five'],['Six','six']]
					},
					{
						type: 'checkbox',
						id: 'colplaceholder',
						label: 'Include Placeholders?',
					}
				]
			}
		],
		onShow: function() {},
		onOk: function() {
			
			var gridHtml       = '',
				colCount       = this.getValueOf('colbasic', 'colcount'),
				colPlaceholder = this.getValueOf('colbasic', 'colplaceholder');
			
			var map = {
				'one': {
					'cols'  : 1,
					'class' : 'col span12'
				},
				'two': {
					'cols'  : 2,
					'class' : 'col span6'
				},
				'three': {
					'cols'  :  3,
					'class' : 'col span4'
				},
				'four': {
					'cols'  : 4,
					'class' : 'col span3'
				},
				'five': {
					'cols'  : 5,
					'class' : 'col five columns'
				},
				'six': {
					'cols'  : 6,
					'class' : 'col span2'
				}
			};
			
			
			// build grid
			gridHtml = '<div class="grid">' + "\n";
			
			for (var i = 0, n = map[colCount].cols; i < n; i++)
			{
				var classes     = map[colCount].class,
					placeholder = '';
					
				// do we need to append omega
				if ((i+1) == n)
				{
					classes += ' omega';
				}
				
				// display placeholders?
				if (colPlaceholder)
				{
					placeholder = 'Column ' + (i+1);
				}
				
				gridHtml += "\t" + '<div class="' + classes + '">' + placeholder + '</div>' + "\n";
			}
			gridHtml += '</div>';
			
			if (editor.mode == 'wysiwyg')
			{
				// insert grid
				editor.insertHtml( gridHtml );
			}
			else
			{
				//insert into codemirror
				var codemirror = window["codemirror_" + editor.id];
				codemirror.doc.replaceSelection(gridHtml);
			}
		}
	}
});