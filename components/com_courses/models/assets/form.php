<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Form (exam) Asset handler class
*/
class FormAssetHandler extends ContentAssetHandler
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
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'form.php');

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
		$this->asset['title']   = $filename;
		$this->asset['type']    = 'form';
		$this->asset['subtype'] = $subtype;
		$this->asset['url']     = $id;

		// Call the primary create method on the file asset handler
		$return = parent::create();

		// Check for errors in response
		if(array_key_exists('error', $return))
		{
			return array('error' => $return['error']);
		}
		else
		{
			// Set the asset id on the form
			$pdf->setAssetId($return['assets']['asset_id']);

			$gid = JRequest::getVar('course_id');
			$oid = JRequest::getVar('offering');

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
					href: '/courses/".$gid."/".$oid."/form.layout?formId=".$id."&tmpl=component',
					afterShow: function() {
						// Highjack the 'done' button to close the iframe
						$('.fancybox-iframe').contents().find('#done').bind('click', function(e) {
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

						// Listen for savesuccessful call from iframe
						$('body').on('savesuccessful', function() {
							$.fancybox.close();

							HUB.CoursesOutline.asset.insert(" .
								json_encode(
									array(
										'assets'=>array(
											$return['assets']
										)
									)
								) .
								", assetslist, {'progressBarId':progressBarId});
						});
					}
				});";
		}

		return array('js'=>$js);
	}

	/**
	 * Edit method for this handler
	 *
	 * @param  object $asset - asset
	 * @return array((string) type, (string) text)
	 **/
	public function edit($asset)
	{
		// Get form object
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'form.php');
		$form = PdfForm::loadByAssetId($asset->get('id'));

		// Make sure we got a proper object
		if (!is_object($form))
		{
			return array('error' => "Asset " . $asset->get('id') . " is not associated with a valid form.");
		}

		$gid = JRequest::getVar('course_id');
		$oid = JRequest::getVar('offering');

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
				href: '/courses/".$gid."/".$oid."/form.layout?formId=" . $form->getId() . "&tmpl=component',
				afterShow: function() {
					// Highjack the 'done' button to close the iframe
					$('.fancybox-iframe').contents().find('#done').bind('click', function(e) {
						e.preventDefault();

						$.fancybox.close();
					});

					// Listen for savesuccessful call from iframe
					$('body').on('savesuccessful', function() {
						$.fancybox.close();
					});
				}
			});";

		return array('type'=>'js', 'value'=>$js);
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
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'form.php');
		$form = PdfForm::loadByAssetId($asset->get('id'));

		// Make sure we got a proper object
		if (!is_object($form))
		{
			return array('error' => "Asset " . $asset->get('id') . " is not associated with a valid form.");
		}

		$gid = JRequest::getVar('course_id');
		$oid = JRequest::getVar('offering');

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
					// Highjack the 'done' button to close the iframe
					$('.fancybox-iframe').contents().find('#done').bind('click', function(e) {
						e.preventDefault();

						$.fancybox.close();
					});
				}
			});";

		return array('type'=>'js', 'value'=>$js);
	}
}