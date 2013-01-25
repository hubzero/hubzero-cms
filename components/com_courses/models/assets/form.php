<?php

/**
* Form Asset handler class
*/
class FormAssetHandler extends AssetHandler
{
	private static $info = array(
			'action_message' => 'As a scoreable test',
			'responds_to'    => array('pdf')
		);

	public static function getMessage()
	{
		return self::$info['action_message'];
	}

	public static function getExtensions()
	{
		return self::$info['responds_to'];
	}

	public function create()
	{
		// Include needed files
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'form.php');

		// Instantiate form object
		$pdf = PdfForm::fromPostedFile('files');

		// No error, then render the images
		if (!$pdf->hasErrors())
		{
			$pdf->renderPageImages();
		}
		else
		{
			return array('error'=>$pdf->getErrors());
		}

		// Grab the newly created form id
		$id = $pdf->getId();

		// Build our JavaScript to return to the view to be executed
		$js = 
			"// Open up forms in a lightbox
			$.fancybox({
				fitToView: false,
				autoResize: false,
				autoSize: false,
				height: ($(window).height())*2/3,
				type: 'iframe',
				href: '/courses/form?task=layout&formId=" . $id . "&tmpl=component',
				afterShow: function() {
					// Highjack the 'done' button to close the iframe
					$('.fancybox-iframe').contents().find('#done').bind('click', function(e) {
						e.preventDefault();

						$.fancybox.close();
					});
				},
				beforeClose: function() {
					// Create ajax call to change info in the database
					$.ajax({
						url: '/api/courses/assetsave',
						data: form.serialize()+'&title=Exam&type=exam&url='+encodeURIComponent('/courses/form/layout/" . $id . "'),
						dataType: 'json',
						type: 'POST',
						cache: false,
						statusCode: {
							201: function(data){
								if(assetslist.find('li:first').hasClass('nofiles'))
								{
									assetslist.find('li:first').remove();
								}
								$.each(data.files, function (index, file) {
									// Insert in our HTML (uses 'underscore.js')
									var li = _.template(HUB.CoursesOutline.Templates.asset, file);
									assetslist.append(li);

									var newAsset = assetslist.find('.asset-item:last');

									newAsset.find('.uniform').uniform();
									newAsset.find('.toggle-editable').show();
									newAsset.find('.title-edit').hide();
									HUB.CoursesOutline.showProgressIndicator();
									HUB.CoursesOutline.resizeFileUploader();
									HUB.CoursesOutline.makeAssetsSortable();

									// Reset progress bar after 2 seconds
									HUB.CoursesOutline.resetProgresBar(asset.asset_title+'.'+asset.asset_ext, 2000);
								});
							},
							401: function(data){
								// Display the error message
								HUB.CoursesOutline.errorMessage(data.responseText);
							},
							404: function(data){
								HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');
							},
							500: function(data){
								// Display the error message
								HUB.CoursesOutline.errorMessage(data.responseText);
							}
						}
					});
				}
			});";

		return array('js'=>$js);
	}

	public function edit()
	{

	}

	public function delete()
	{

	}
}