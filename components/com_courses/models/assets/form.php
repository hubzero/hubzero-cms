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
class FormAssetHandler extends AssetHandler
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
			'action_message' => 'As a scoreable test',
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
						url: '/api/courses/asset/save',
						data: form.serialize()+'&title=" . $filename . "&type=exam&url=/courses/form/layout/" . $id . "&progress_bar_id=" . JRequest::getCmd('progress_bar_id', '') . "',
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
									var callback = function() {
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
									}

									// Reset progress bar
									HUB.CoursesOutline.resetProgresBar(file.asset_progress_bar_id, 1000, callback);
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
}