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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js();

$txt  = '';
$mode = strtolower(Request::getWord('mode', ''));

if ($mode != 'preview')
{
	switch ($this->model->resource->published)
	{
		case 1: $txt .= ''; break; // published
		case 2: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') . ']</span> '; break;  // external draft
		case 3: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_PENDING') . ']</span> ';        break;  // pending
		case 4: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DELETED') . ']</span> ';        break;  // deleted
		case 5: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') . ']</span> '; break;  // internal draft
		case 0; $txt .= '<span>[' . Lang::txt('COM_RESOURCES_UNPUBLISHED') . ']</span> ';    break;  // unpublished
	}
}

$tconfig  = $this->tconfig;
$helper   = $this->helper;
$thistool = $this->thistool;
$curtool  = $this->curtool;
$revision = $this->revision;
?>
<div id="content-header">
	<section class="main section upperpane">
		<div class="subject">
			<div class="grid overviewcontainer">
				<div class="col span8">
					<header>
						<h2>
							<?php echo $txt . $this->escape(stripslashes($this->model->resource->title)); ?>
						</h2>
						<input type="hidden" name="rid" id="rid" value="<?php echo $this->model->resource->id; ?>" />
					</header>

					<?php if ($this->model->params->get('show_authors', 1)) { ?>
						<div id="authorslist">
							<?php
							$this->view('_contributors')
								->set('option', $this->option)
								->set('contributors', $this->model->contributors('tool'))
								->display();
							?>
						</div>
					<?php } ?>

					<p class="ataglance">
						<?php
						if (!$this->model->resource->introtext)
						{
							$this->model->resource->introtext = $this->model->resource->fulltxt;
						}
						echo \Hubzero\Utility\String::truncate(stripslashes($this->model->resource->introtext), 255);
						?>
					</p>
					<?php if ($this->model->params->get('access-edit-resource')) { ?>
						<a class="icon-edit edit btn" href="<?php echo Route::url('index.php?option=com_tools&task=resource&step=1&app=' . $this->model->resource->alias); ?>"><?php echo Lang::txt('COM_RESOURCES_EDIT'); ?></a>
					<?php } ?>
				</div><!-- / .overviewcontainer -->

				<div class="col span4 omega launcharea">
					<?php
					// Private/Public resource access check
					if (!$this->model->access('view-all'))
					{
						$ghtml = array();
						foreach ($this->model->resource->getGroups() as $allowedgroup)
						{
							$ghtml[] = '<a href="' . Route::url('index.php?option=com_groups&cn=' . $allowedgroup) . '">' . $allowedgroup . '</a>';
						}
						?>
						<p class="warning">
							<?php echo Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
						</p>
						<?php
					}
					else
					{
						// get launch button
						$firstChild = $this->model->children(0);
						echo \Components\Resources\Helpers\Html::primary_child($this->option, $this->model->resource, $firstChild, '');

						$html = '';

						// Display version info
						$versiontext = '<strong>';
						if ($revision && $thistool) {
							$versiontext .= $thistool->version.'</strong>';
							if ($this->model->resource->revision!='dev') {
								$versiontext .=  '<br /> '.ucfirst(Lang::txt('COM_RESOURCES_PUBLISHED_ON')).' ';
								$versiontext .= ($thistool->released && $thistool->released != '0000-00-00 00:00:00') ? Date::of($thistool->released)->toLocal(Lang::txt('DATE_FORMAT_HZ1')): Date::of($this->model->resource->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
								$versiontext .= ($thistool->unpublished && $thistool->unpublished != '0000-00-00 00:00:00') ? ', '.Lang::txt('COM_RESOURCES_UNPUBLISHED_ON').' '.Date::of($thistool->unpublished)->toLocal(Lang::txt('DATE_FORMAT_HZ1')): '';
							} else {
								$versiontext .= ' ('.Lang::txt('COM_RESOURCES_IN_DEVELOPMENT').')';
							}
						} else if ($curtool) {
							$versiontext .= $curtool->version.'</strong> - '.Lang::txt('COM_RESOURCES_PUBLISHED_ON').' ';
							$versiontext .= ($curtool->released && $curtool->released != '0000-00-00 00:00:00') ? Date::of($curtool->released)->toLocal(Lang::txt('DATE_FORMAT_HZ1')): Date::of($this->model->resource->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
						}

						if (!$thistool)
						{
							$html .= "\t\t\t\t".'<p class="curversion">'.Lang::txt('COM_RESOURCES_VERSION').' '.$versiontext.'</p>'."\n";
						}
						else if ($revision == 'dev')
						{
							$html .= "\t\t\t\t".'<p class="devversion">'.Lang::txt('COM_RESOURCES_VERSION').' '.$versiontext;
							$html .= $this->model->resource->toolpublished ? ' <span>'.Lang::txt('View').' <a href="'.Route::url('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=versions').'">'.Lang::txt('other versions').'</a></span>' : '';
							$html .='</p>'."\n";
						}
						else
						{
							// Show archive message
							$msg = '<strong>'.Lang::txt('COM_RESOURCES_ARCHIVE').'</strong> '.Lang::txt('COM_RESOURCES_VERSION').' '.$versiontext;
							if (isset($this->model->resource->curversion) && $this->model->resource->curversion) {
								$msg .= ' <br />'.Lang::txt('COM_RESOURCES_LATEST_VERSION').': <a href="'.Route::url('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&rev='.$curtool->revision).'">'.$this->model->resource->curversion.'</a>.';
							}
							$msg .= ' <a href="'.Route::url('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=versions').'">'.Lang::txt('COM_RESOURCES_TOOL_ALL_VERSIONS').'</a>';
							$html .= '<p class="archive">' . $msg . '</p>' . "\n";
						}

						// doi message
						if ($revision != 'dev' && ($this->model->resource->doi || $this->model->resource->doi_label)) {
							if ($this->model->resource->doi && $tconfig->get('doi_shoulder'))
							{
								$doi = 'doi:' . $tconfig->get('doi_shoulder') . '/' . strtoupper($this->model->resource->doi);
							}
							else
							{
								$doi = 'doi:10254/' . $tconfig->get('doi_prefix') . $this->model->resource->id . '.' . $this->model->resource->doi_label;
							}

							$html .= "\t\t".'<p class="doi">'.$doi.' <span><a href="'.Route::url('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=about').'#citethis">'.Lang::txt('cite this').'</a></span></p>'."\n";
						}

						// Open/closed source
						if (isset($this->model->resource->toolsource) && $this->model->resource->toolsource == 1 && isset($this->model->resource->tool)) { // open source
							$html .= '<p class="opensource_license">'.Lang::txt('Open source').': <a class="popup" href="index.php?option='.$this->option.'&task=license&tool='.$this->model->resource->tool.'&no_html=1">license</a> ';
							$html .= ($this->model->resource->taravailable) ? ' |  <a href="index.php/'.$this->model->resource->tarname.'?option='.$this->option.'&task=sourcecode&tool='.$this->model->resource->tool.'">'.Lang::txt('download').'</a> '."\n" : ' | <span class="unavail">'.Lang::txt('code unavailable').'</span>'."\n";
							$html .= '</p>'."\n";
						} elseif (isset($this->model->resource->toolsource) && !$this->model->resource->toolsource) { // closed source, archive page
							$html .= '<p class="closedsource_license">'.Lang::txt('COM_RESOURCES_TOOL_IS_CLOSED_SOURCE').'</p>'."\n";
						}
						// do we have a first-time user guide?
						$helper->getChildren($this->model->resource->id, 0, 'all');
						$children = $helper->children;

						if (!$thistool) {
							$guide = 0;
							foreach ($children as $child)
							{
								$title = ($child->logicaltitle)
										? $child->logicaltitle
										: stripslashes($child->title);
								if ($child->access == 0 || ($child->access == 1 && !User::isGuest())) {
									if (strtolower($title) !=  preg_replace('/user guide/', '', strtolower($title))) {
										$guide = $child;
									}
								}
							}
							$url = $guide ? \Components\Resources\Helpers\Html::processPath($this->option, $guide, $this->model->resource->id) : '';
							$html .= "\t\t".'<p class="supdocs">'."\n";
							if ($url) {
								$html .= "\t\t\t".'<span><span class="guide"><a href="'.$url.'">'.Lang::txt('COM_RESOURCES_TOOL_FIRT_TIME_USER_GUIDE').'</a></span></span>'."\n";
							}
							$html .= "\t\t\t".'<span class="viewalldocs"><a href="'.Route::url('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=supportingdocs').'">'.Lang::txt('COM_RESOURCES_TOOL_VIEW_ALL_SUPPORTING_DOCS').'</a></span>'."\n";
							$html .= "\t\t".'</p>'."\n";
						}

						echo $html;

					} // --- end else (if group check passed)
					?>
				</div><!-- / .aside launcharea -->
			</div>

			<?php
			// Display canonical
			$this->view('_canonical')
				->set('option', $this->option)
				->set('model', $this->model)
				->display();
			?>
		</div><!-- / .subject -->
		<aside class="aside rankarea">
			<?php
			// Show resource ratings
			if (!$thistool)
			{
				if ($this->model->params->get('show_metadata', 1))
				{
					$this->view('_metadata')
						->set('option', $this->option)
						->set('sections', $this->sections)
						->set('model', $this->model)
						->display();
				}
			}
			else if ($revision == 'dev' or !$this->model->resource->toolpublished)
			{
				?>
				<div class="metaplaceholder">
					<p>
						<?php echo ($revision=='dev')
								? Lang::txt('This section will be filled when this tool version gets published.')
								: Lang::txt('This section is unavailable in an archive version of a tool.');

						if (isset($this->model->resource->curversion) && $this->model->resource->curversion)
						{
							echo ' '.Lang::txt('Consult the latest published version').' <a href="'.Route::url('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&rev='.$curtool->revision).'">'.$this->model->resource->curversion.'</a> '.Lang::txt('for most current information.');
						}
						?>
					</p>
				</div>
				<?php
			}
			?>
		</aside><!-- / .aside -->
	</section>
</div>

<?php if ($this->model->access('view-all')) { ?>
	<section class="main section">
		<div class="subject tabbed">
			<?php
			$this->view('_tabs')
				->set('option', $this->option)
				->set('cats', $this->cats)
				->set('resource', $this->model->resource)
				->set('active', $this->tab)
				->display();

			$this->view('_sections')
				->set('option', $this->option)
				->set('sections', $this->sections)
				->set('resource', $this->model->resource)
				->set('active', $this->tab)
				->display();
			?>
		</div><!-- / .subject -->
		<aside class="aside extracontent">
			<?php
			// Show related content
			$out = Event::trigger('resources.onResourcesSub', array($this->model->resource, $this->option, 1));
			if (count($out) > 0)
			{
				foreach ($out as $ou)
				{
					if (isset($ou['html']))
					{
						echo $ou['html'];
					}
				}
			}

			// Show what's popular
			if ($this->tab == 'about')
			{
				echo \Hubzero\Module\Helper::renderModules('extracontent');
			}
			?>
		</aside><!-- / .aside extracontent -->
	</section><!-- / .main section -->
<?php } ?>
