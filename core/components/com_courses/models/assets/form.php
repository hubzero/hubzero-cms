<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Components\Courses\Models\PdfForm;
use Request;

/**
* Form (exam) Asset handler class
*/
class Form extends Content
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
			'action_message' => 'Create a quiz/exam',
			'responds_to'    => array('pdf')
		);

	/**
	 * Create method for this handler
	 *
	 * @return string - javascript to be run
	 **/
	public function create()
	{
		// Include needed files
		require_once(dirname(__DIR__) . DS . 'form.php');

		// Check to make sure a file was provided
		if (isset($_FILES['files']))
		{
			$file = $_FILES['files']['name'][0];

			// Get the file extension
			$pathinfo = pathinfo($file);
			$filename = $pathinfo['filename'];
			$ext      = $pathinfo['extension'];
		}
		else
		{
			return array('error' => 'No files provided');
		}

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

		$subtype = 'quiz';

		if (strstr($filename, 'exam') !== false)
		{
			$subtype = 'exam';
		}
		elseif (strstr($filename, 'homework') !== false)
		{
			$subtype = 'homework';
		}

		// Save the actual asset
		$this->asset['title']        = $filename;
		$this->asset['type']         = 'form';
		$this->asset['subtype']      = $subtype;
		$this->asset['url']          = $id;
		$this->asset['graded']       = 1;
		$this->asset['grade_weight'] = $subtype;

		// Call the primary create method on the file asset handler
		$return = parent::create();

		// Check for errors in response
		if (array_key_exists('error', $return))
		{
			return array('error' => $return['error']);
		}
		else
		{
			// Set the asset id on the form
			$pdf->setAssetId($return['assets']['asset_id']);

			$gid = Request::getVar('course_id');
			$oid = Request::getVar('offering');

			// Build our JavaScript to return to the view to be executed
			$js =
				"// Open up forms in a lightbox
				$.fancybox({
					fitToView: false,
					autoResize: false,
					autoSize: false,
					height: ($(window).height())*2/3,
					closeBtn: false,
					modal: true,
					type: 'iframe',
					iframe: {
						preload : false
					},
					href: '/courses/".$gid."/".$oid."/form.layout?formId=".$id."&tmpl=component',
					afterLoad: function() {
						var iframe = $('.fancybox-iframe');
						iframe.load(function() {
							var frameContents = $('.fancybox-iframe').contents();

							var navHeight = frameContents.find('.navbar').height();
							frameContents.find('.main.section.courses-form').css('margin-bottom', navHeight);

							// Highjack the 'done' button to close the iframe
							frameContents.find('#done').bind('click', function(e) {
								e.preventDefault();

								$.fancybox.close();

								// Remove progress bar
								HUB.CoursesOutline.asset.resetProgresBar(progressBarId, 0);

								// Get the form data and set the published value to 2 for deleted
								var formData = form.serializeArray();
								formData.push({'name':'published', 'value':'2'});
								formData.push({'name':'id', 'value':'" . $this->assoc['asset_id'] . "'});

								// We've already saved the asset, so we need to mark asset as deleted
								$.ajax({
									url: '/api/courses/asset/save',
									data: formData
								});
							});
						});

						// Listen for savesuccessful call from iframe
						$('body').on('savesuccessful', function( e, title ) {
							$.fancybox.close();

							var data = " .
								json_encode(
									array(
										'assets'=>array(
											$return['assets']
										)
									)
								) . ";

							data.assets[0].asset_title = title;

							HUB.CoursesOutline.asset.insert(data, assetslist, {'progressBarId':progressBarId});
						});
					}
				});";
		}

		return array('js'=>$js);
	}

	/**
	 * Preview method for this handler
	 *
	 * @param  object $asset - asset
	 * @return array((string) type, (string) text)
	 **/
	public function preview($asset)
	{
		// Get form object
		require_once(dirname(__DIR__) . DS . 'form.php');
		$form = PdfForm::loadByAssetId($asset->get('id'));

		// Make sure we got a proper object
		if (!is_object($form))
		{
			return array('error' => "Asset " . $asset->get('id') . " is not associated with a valid form.");
		}

		$gid = Request::getVar('course_id');
		$oid = Request::getVar('offering');

		// Compile our return var
		$js =
			"// Open up forms in a lightbox
			$.fancybox({
				fitToView: false,
				autoResize: false,
				autoSize: false,
				height: ($(window).height())*2/3,
				closeBtn: false,
				modal: true,
				type: 'iframe',
				href: '/courses/".$gid."/".$oid."/form.layout?formId=" . $form->getId() . "&readonly=1&tmpl=component',
				afterShow: function() {
					var frameContents = $('.fancybox-iframe').contents();

					var navHeight = frameContents.find('.navbar').height();
					frameContents.find('.main.section.courses-form').css('margin-bottom', navHeight);

					// Highjack the 'done' button to close the iframe
					frameContents.find('#done').bind('click', function(e) {
						e.preventDefault();

						$.fancybox.close();
					});
				}
			});";

		return array('type'=>'js', 'value'=>$js);
	}
}