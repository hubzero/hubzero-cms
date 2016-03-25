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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$webpath 	= $this->config->get('webpath');

$authorized = $this->publication->access('view-all');

$abstract	= $this->publication->abstract;
$description = $this->publication->describe('parsed');

$this->publication->authors();
$this->publication->attachments();
$this->publication->license();

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
$customFields = $this->publication->_curationModel->getMetaSchema();

include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');

$metaElements 	= new \Components\Publications\Models\Elements($data, $customFields);
$schema 	= $metaElements->getSchema();

?>
<div class="pubabout">
<?php
	// Show gallery images
	$modelHandler = new \Components\Publications\Models\Handlers($this->database);

	// Load image handler
	if ($handler = $modelHandler->ini('imageviewer'))
	{
		echo $handler->showImageBand($this->publication);
	}
?>

	<h4><?php echo Lang::txt('COM_PUBLICATIONS_DESCRIPTION'); ?></h4>
	<div class="pub-content">
		<?php echo $description; ?>
	</div>

<?php
	// List all content?
	$listAll = isset($this->publication->_curationModel->_manifest->params->list_all)
			? $this->publication->_curationModel->_manifest->params->list_all :  0;
	$listLabel = isset($this->publication->_curationModel->_manifest->params->list_label)
			? $this->publication->_curationModel->_manifest->params->list_label
			: Lang::txt('COM_PUBLICATIONS_CONTENT_LIST');
	// Add plugin style
	\Hubzero\Document\Assets::addPluginStylesheet('publications', 'supportingdocs');

	if ($listAll)
	{
		// Get elements in primary and supporting role
		$prime    = $this->publication->_curationModel->getElements(1);
		$second   = $this->publication->_curationModel->getElements(2);
		$elements = array_merge($prime, $second);

		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->database);

		if ($elements)
		{
			$append = NULL;
			// Get file path
			$path 	= \Components\Publications\Helpers\Html::buildPubPath(
				$this->publication->id,
				$this->publication->version_id,
				$webpath,
				'',
				1
			);
			$licFile 	= $path . DS . 'LICENSE.txt';
			if (file_exists($licFile))
			{
				$licenseUrl = Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&task=license' . '&v=' . $this->publication->version_id);
				$append = '<li><a href="' . $licenseUrl . '" class="license-terms play" rel="external">' . Lang::txt('COM_PUBLICATIONS_LICENSE_TERMS') . '</a></li>';
			}

			// Archival path
			$tarname  = Lang::txt('Publication') . '_' . $this->publication->id . '.zip';
			$archPath = $path . DS . $tarname;

			$showArchive = isset($this->publication->_curationModel->_manifest->params->show_archival)
					? $this->publication->_curationModel->_manifest->params->show_archival :  0;
			$archiveBase = 'index.php?option=com_publications&id=' . $this->publication->id . '&task=serve&v=' . $this->publication->version_number;
			$archiveUrl = Route::url($archiveBase . '&render=archive');
			$showArchive = ($showArchive && file_exists($archPath)) ? true : false;

			// Draw list
			$list = $attModel->listItems(
				$elements,
				$this->publication,
				$authorized,
				$append
			);
			?>
			<h4 class="list-header">
				<?php echo $listLabel ? $listLabel : Lang::txt('COM_PUBLICATIONS_CONTENT_LIST'); ?>
				<?php if ($showArchive && $authorized) : ?>
					<span class="browsebundle">
						(<a class="showBundle" href="<?php echo Route::url($archiveBase . '&render=showcontents&tmpl=component'); ?>" target="_blank">
							<?php echo Lang::txt('COM_PUBLICATIONS_BROWSE_ARCHIVE_PACKAGE'); ?>
						</a>)
					</span>
					<span class="viewalltypes archival-package">
						<a href="<?php echo $archiveUrl; ?>">
							<?php echo Lang::txt('COM_PUBLICATIONS_ARCHIVE_PACKAGE'); ?>
						</a>
					</span>
				<?php endif; ?>
			</h4>
			<div class="pub-content">
				<?php echo $list; ?>
			</div>
		<?php
		}
	}
?>
<?php
	$citations = NULL;
	if ($this->publication->params->get('show_metadata'))
	{
		if (!isset($schema->fields) || !is_array($schema->fields))
		{
			$schema = new stdClass();
			$schema->fields = array();
		}
		foreach ($schema->fields as $field)
		{
			if (isset($data[$field->name]))
			{
				if ($field->name == 'citations')
				{
					$citations = $data[$field->name];
				}
				elseif ($value = $metaElements->display($field->type, $data[$field->name]))
				{
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

<?php if (($this->publication->params->get('show_citation'))) { ?>
	<?php
	if ($this->publication->params->get('show_citation') == 1
	|| $this->publication->params->get('show_citation') == 2)
	{
		// Build our citation object
		$cite = new stdClass();
		$cite->title = $this->publication->title;
		$cite->year = $this->publication->published_up && $this->publication->published_up != '0000-00-00 00:00:00' ? Date::of($this->publication->published_up)->toLocal('Y') : Date::of('now')->toLocal('Y');

		$cite->location = '';
		$cite->date = '';

		$cite->doi 		= $this->publication->doi ? $this->publication->doi : '';
		$cite->url 		= $cite->doi ? trim($this->config->get('doi_resolve', 'http://dx.doi.org/'), DS) . DS . $cite->doi
							: NULL;
		$cite->type 	= '';
		$cite->pages 	= '';
		$cite->author 	= $this->publication->getUnlinkedContributors();
		$cite->publisher= $this->config->get('doi_publisher', '' );

		if ($this->publication->params->get('show_citation') == 2)
		{
			$citations = '';
		}
	}
	else
	{
		$cite = null;
	}

	$citeinstruct  = \Components\Publications\Helpers\Html::citation($cite, $this->publication, $citations);
	?>
	<h4 id="citethis"><?php echo Lang::txt('COM_PUBLICATIONS_CITE_THIS'); ?></h4>
	<div class="pub-content">
		<?php echo $citeinstruct; ?>
	</div>
<?php } ?>
<?php if ($this->publication->params->get('show_submitter') && $this->publication->submitter()) { ?>
	<h4><?php echo Lang::txt('COM_PUBLICATIONS_SUBMITTER'); ?></h4>
	<div class="pub-content">
		<?php
			$submitter  = $this->publication->_submitter->name;
			$submitter .= $this->publication->_submitter->organization
					? ', ' . $this->publication->_submitter->organization : '';
			echo $submitter;
		?>
	</div>
<?php } ?>
<?php if ($this->publication->params->get('show_tags')) {
	$this->publication->getTagCloud( $this->authorized );
	?>
	<?php if ($this->publication->_tagCloud) { ?>
		<h4><?php echo Lang::txt('COM_PUBLICATIONS_TAGS'); ?></h4>
		<div class="pub-content">
			<?php
				echo $this->publication->_tagCloud;
			?>
		</div>
	<?php } ?>
<?php } ?>
<?php
// Show version notes
if (($this->publication->params->get('show_notes')) && $this->publication->get('release_notes'))
{
	$notes = $this->publication->notes('parsed');
	?>
	<h4><?php echo Lang::txt('COM_PUBLICATIONS_NOTES'); ?></h4>
	<div class="pub-content">
		<?php
			echo $notes;
		?>
	</div>
<?php
} ?>
</div><!-- / .pubabout -->
