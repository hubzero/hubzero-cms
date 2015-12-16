Hubzero.initApi();
var equationChanged;
var equationLimiter;
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
						validate: CKEDITOR.dialog
						.validate.notEmpty( "Equation field cannot be empty" )
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
				equationElement = this.getContentElement('equationbasic', 'equationField'),
				equationTextarea = $('#' + equationElement.domId).find('textarea'),
				equationPreview  = $('#equationPreview');
			if (image.is('img'))
			{
				// get source from data attr
				var equation = decodeURIComponent(image.getAttribute('data-equation'));

				// get source from image url
				if (equation == null)
				{
					var source = image.getAttribute('src');
					equation = decodeURIComponent(source);
				}
				
				equationTextarea.val(equation);
				equationPreview.attr('src', image.getAttribute('src'));
			} else {
				//we're making a new equation
				var equation = '';
				var source = '';
				
				equationTextarea.val(equation);
				equationPreview.attr('src', image.getAttribute('src'));
			}
			
			// show preview
			$('#' + equationElement.domId).on('keyup', 'textarea', function(event) {
				equationChanged = true;
			});
			
			equationLimiter = setInterval( function() {
				if (equationChanged) {
					var equation = $('#' + equationElement.domId).find('textarea').val();
					$.ajax({
						url: "/api/resources/renderlatex",
						data: "expression="+equation,
						dataType: "json",
						cache: false,
						success: function(json) {
							if (json.error == '') {
								//no error, display the image
								equationPreview.attr('src', json.img);
							} else {
								//there was an error, display an error icon
								equationPreview.attr('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAABsUlEQVQ4T62UzytEURTHv+c+jadkjxT5ce9b2Pk7/ChM0mSjFEX8HxQiCwtZSBjK32GnzH0ZDMJeMn7Mu0fvlmnMeM9g7vKecz7nfM+95xC+OVft7e6L6yYBDBBzHzM3h25E9MBEJ0x01JDP73fkci/l4VR+kVFqDMYsQggLiToGuHeY55Xv75X6FIEMOFqpNQBTcaCKipjXpe/PEhBYFZ8OGaU2omBOb691C05Pv81FzOvK92eKQCsT2ImqrOn42Joe+/sjiyfmZCif7AMkEpdxPasGaIC7RKHQSRmlJgBsxfWtGqCVy5wKgYcAhmoBNECatJQ3TNRWCyCMuaZMT887hKirEfCt9sBqJDdubloBT5OTsX/eALmqHuUXk3NAZ56XIubtuKCfJqUkdtx+7GfXvRBAyz8n5dYJgm47y2eelyTm3Shgw9ycNeWXl+OEDHtap4vLQUu5xkTTv+hXqeuqp/Xsl20Tri9fypU/QFeU1gsV6+sznZZyNCBaEkBrXLXEfMtE86HMUr+KjR0az7u66gPHGQmIBoUxfQDs9jZC3AvgBMBRXaGQ7s5mX8uTfgDCabpOjteIBgAAAABJRU5ErkJggg==');
							}
							
						}
					});
					equationChanged = false;
				}
			}, 2500);
		},
		onOk: function() {
			equationTextarea = this.getContentElement('equationbasic', 'equationField'),
			equation = $('#' + equationTextarea.domId).find('textarea').val(),
			equationPreview  = $('#equationPreview');
			equationImage = editor.document.createElement('img');

			// set attributes and insert
			equationImage.setAttribute('class', 'hubzeroequation-result');
			equationImage.setAttribute('data-equation', equation);
			equationImage.setAttribute('src', equationPreview.attr('src'));
			editor.insertElement( equationImage );
		}
	}
});
