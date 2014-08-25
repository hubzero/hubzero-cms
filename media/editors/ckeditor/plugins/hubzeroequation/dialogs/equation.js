CKEDITOR.dialog.add( 'hubzeroEquationDialog', function( editor ) {
	return {
		title: 'Equation Editor',
		minWidth: 400,
		minHeight: 300,
		resizable: false,
		contents: [
			{
				id: 'equationbasic',
				label: 'Basic Settings',
				elements: [
					{
						type: 'textarea',
						id: 'equationField',
						label: 'Equation',
						validate: CKEDITOR.dialog.validate.notEmpty( "Equation field cannot be empty" )
					},
					{
						type: 'html',
						html: '<a target="_blank" href="http://web.ift.uib.no/Teori/KURS/WRK/TeX/symALL.html">LaTex Help</a><br /><br />'
					},
					{
						type: 'html',
						html: '<label>Preview</label><br /><img id="equationPreview" src="" />'
					}
				]
			}
		],
		onShow: function() {
			var sel = editor.getSelection(),
				image = sel.getStartElement(),
				latexUrl = 'https://latex.codecogs.com/gif.latex?',
				equationElement = this.getContentElement('equationbasic', 'equationField'),
				equationTextarea = $('#' + equationElement.domId).find('textarea'),
				equationPreview  = $('#equationPreview');
			
			if (image.is('img'))
			{
				// get source from data attr
				var equation = image.getAttribute('data-equation');

				// get source from image url
				if (equation == null)
				{
					var source = image.getAttribute('src');
					source = decodeURIComponent(source);
					equation = source.replace(latexUrl, '').trim();
				}
				
				equationTextarea.val(equation);
				equationPreview.attr('src', latexUrl + equation);
			}
			
			// show preview
			$('#' + equationElement.domId).on('keyup', 'textarea', function(event) {
				var equation = $(this).val();
				equationPreview.attr('src', latexUrl + equation);
			});
		},
		onOk: function() {
			var latexUrl = 'https://latex.codecogs.com/gif.latex?',
				equationTextarea = this.getContentElement('equationbasic', 'equationField'),
				equation = $('#' + equationTextarea.domId).find('textarea').val(),
				equationImage = editor.document.createElement('img');
				
			// set attributes and insert
			equationImage.setAttribute('class', 'hubzeroequation-result');
			equationImage.setAttribute('data-equation', equation);
			equationImage.setAttribute('src', latexUrl + equation);
			editor.insertElement( equationImage );
		}
	}
});