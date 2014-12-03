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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$useBlocks 	= $this->config->get('curation', 0);
$webpath 	= $this->config->get('webpath');
$abstract	= stripslashes($this->publication->abstract);
$authorized = ($this->restricted && !$this->authorized) ? false : true;

// Set the document description
if ($this->publication->abstract)
{
	$document = JFactory::getDocument();
	$document->setDescription(PublicationsHtml::encode_html(strip_tags($abstract)));
}

$description = '';
$model = new PublicationsModelPublication($this->publication);
if ($this->publication->description)
{
	$description = $model->description('parsed');
}

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->publication->metadata, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = $match[2];
	}
}

$category = $this->publication->_category;
$customFields = $category->customFields && $category->customFields != '{"fields":[]}'
				? $category->customFields
				: '{"fields":[{"default":"","name":"citations","label":"Citations","type":"textarea","required":"0"}]}';
if ($useBlocks)
{
	$customFields = $this->publication->_curationModel->getMetaSchema();
}

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');

$metaElements 	= new PublicationsElements($data, $customFields);
$schema 	= $metaElements->getSchema();

?>
<div class="pubabout">
<?php
	// Show gallery images
	if ($this->params->get('show_gallery') || $useBlocks)
	{
		if ($useBlocks)
		{
			// Get handler model
			$modelHandler = new PublicationsModelHandlers($this->database);

			// Load image handler
			if ($handler = $modelHandler->ini('imageviewer'))
			{
				echo $handler->showImageBand($this->publication);
			}
		}
		else
		{
			// Get gallery path
			$gallery_path 	= $this->helper->buildPath(
				$this->publication->id,
				$this->publication->version_id,
				$webpath,
				'gallery'
			);

			$pScreenshot = new PublicationScreenshot($this->database);
			$gallery = $pScreenshot->getScreenshots( $this->publication->version_id );
			$shots = PublicationsHtml::showGallery($gallery, $gallery_path);
			if ($shots)
			{
				$html  = ' <div class="sscontainer">'."\n";
				$html .= $shots;
				$html .= ' </div><!-- / .sscontainer -->'."\n";
				echo $html;
			}
		}
	}
?>

	<h4><?php echo JText::_('COM_PUBLICATIONS_ABSTRACT'); ?></h4>
	<div class="pub-content">
		<?php echo $description; ?>
	</div>

<?php
	// List all content?
	if ($useBlocks)
	{
		$listAll = isset($this->publication->_curationModel->_manifest->params->list_all)
				? $this->publication->_curationModel->_manifest->params->list_all :  0;
		$listLabel = isset($this->publication->_curationModel->_manifest->params->list_label)
				? $this->publication->_curationModel->_manifest->params->list_label
				: JText::_('COM_PUBLICATIONS_CONTENT_LIST');

		if ($listAll)
		{
			// Get elements in primary and supporting role
			$prime    = $this->publication->_curationModel->getElements(1);
			$second   = $this->publication->_curationModel->getElements(2);
			$elements = array_merge($prime, $second);

			// Get attachment type model
			$attModel = new PublicationsModelAttachments($this->database);

			if ($elements)
			{
				$append = NULL;
				// Get file path
				$path 	= $this->helper->buildPath(
					$this->publication->id,
					$this->publication->version_id,
					$webpath,
					'',
					1
				);
				$licFile 	= $path . DS . 'LICENSE.txt';
				if (file_exists($licFile))
				{
					$licenseUrl = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&task=license' . '&v=' . $this->publication->version_id);
					$append = '<li><a href="' . $licenseUrl . '" class="license-terms play" rel="external">' . JText::_('COM_PUBLICATIONS_LICENSE_TERMS') . '</a></li>';
				}

				// Archival path
				$tarname  = JText::_('Publication') . '_' . $this->publication->id . '.zip';
				$archPath	= $path . DS . $tarname;

				$showArchive = isset($this->publication->_curationModel->_manifest->params->show_archival)
						? $this->publication->_curationModel->_manifest->params->show_archival :  0;
				$archiveUrl = JRoute::_('index.php?option=com_publications&id=' . $this->publication->id . '&task=serve&v=' . $this->publication->version_number . '&render=archive');
				$showArchive = ($showArchive && file_exists($archPath)) ? true : false;

				// Draw list
				$list = $attModel->listItems(
					$elements,
					$this->publication,
					$authorized,
					$append
				);
				?>
				<h4 class="list-header"><?php echo $listLabel ? $listLabel : JText::_('COM_PUBLICATIONS_CONTENT_LIST'); ?><?php if ($showArchive && $authorized) { ?><span class="viewalltypes archival-package"><a href="<?php echo $archiveUrl; ?>"><?php echo JText::_('COM_PUBLICATIONS_ARCHIVE_PACKAGE'); ?></a></span> <?php } ?></h4>
				<div class="pub-content">
					<?php echo $list; ?>
				</div>
			<?php
			}
		}
	}
?>
<?php
	$citations = NULL;
	if ($useBlocks || $this->params->get('show_metadata'))
	{
		if (!isset($schema->fields) || !is_array($schema->fields))
		{
			$schema = new stdClass();
			$schema->fields = array();
		}
		foreach ($schema->fields as $field)
		{
			if (isset($data[$field->name])) {
				if ($field->name == 'citations') {
					$citations = $data[$field->name];
				} else if ($value = $metaElements->display($field->type, $data[$field->name])) {
				?>
				<h4><?php echo $field->label; ?></h4>
				<div class="pub-content">
					<?php echo $value; ?>
				</div>
				<?php
				}
			}
		}
	}
?>

<?php if (($useBlocks || $this->params->get('show_citation'))) { ?>
	<?php
	if ($this->params->get('show_citation') == 1
	|| $this->params->get('show_citation') == 2)
	{
		// Build our citation object
		$cite = new stdClass();
		$cite->title = $this->publication->title;
		$cite->year = $this->publication->published_up && $this->publication->published_up != '0000-00-00 00:00:00' ? JHTML::_('date', $this->publication->published_up, 'Y') : JHTML::_('date', JFactory::getDate()->toSql(), 'Y');

		$cite->location = '';
		$cite->date = '';

		$cite->doi 		= $this->publication->doi ? $this->publication->doi : '';
		$cite->url 		= $cite->doi ? trim($this->config->get('doi_resolve', 'http://dx.doi.org/'), DS) . DS . $cite->doi
							: NULL;
		$cite->type 	= '';
		$cite->pages 	= '';
		$cite->author 	= $this->helper->getUnlinkedContributors( $this->authors);
		$cite->publisher= $this->config->get('doi_publisher', '' );

		if ($this->params->get('show_citation') == 2)
		{
			$citations = '';
		}
	}
	else
	{
		$cite = null;
	}

	$citeinstruct  = PublicationsHtml::citation( $this->option, $cite, $this->publication, $citations, $this->version );
	?>
	<h4 id="citethis"><?php echo JText::_('COM_PUBLICATIONS_CITE_THIS'); ?></h4>
	<div class="pub-content">
		<?php echo $citeinstruct; ?>
	</div>
<?php } ?>
<?php if ($this->params->get('show_submitter') && $this->publication->submitter) { ?>
	<h4><?php echo JText::_('COM_PUBLICATIONS_SUBMITTER'); ?></h4>
	<div class="pub-content">
		<?php
			$submitter  = $this->publication->submitter->name;
			$submitter .= $this->publication->submitter->organization
					? ', ' . $this->publication->submitter->organization : '';
			echo $submitter;
		?>
	</div>
<?php } ?>
<?php if ($useBlocks || $this->params->get('show_tags')) {
	$this->helper->getTagCloud( $this->authorized );
	?>
	<?php if ($this->helper->tagCloud) { ?>
		<h4><?php echo JText::_('COM_PUBLICATIONS_TAGS'); ?></h4>
		<div class="pub-content">
			<?php
				echo $this->helper->tagCloud;
			?>
		</div>
	<?php } ?>
<?php } ?>
<?php
// Show version notes
if (($useBlocks || $this->params->get('show_notes')) && $this->publication->release_notes)
{
	$notes = $model->notes('parsed');
	?>
	<h4><?php echo JText::_('COM_PUBLICATIONS_NOTES'); ?></h4>
	<div class="pub-content">
		<?php
			echo $notes;
		?>
	</div>
<?php
} ?>
</div><!-- / .pubabout -->