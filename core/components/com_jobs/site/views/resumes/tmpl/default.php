<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

	/* Resume List */
	$seekers 	= $this->seekers;
	$filters 	= $this->filters;
	$emp 		= $this->emp;
	$admin 		= $this->admin;
	$pageNav 	= $this->pageNav;
	$cats 		= $this->cats;
	$types 		= $this->types;

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->emp or $this->admin) {  ?>
	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if ($this->emp) {  ?>
			<li><a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('Employer Dashboard'); ?></a></li>
			<?php if ($filters['filterby'] == 'shortlisted') { ?>
			<li><a class="complete btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes'); ?>"><?php echo Lang::txt('All Candidates'); ?></a></li>
			<?php } else { ?>
			<li><a class="icon-list shortlist btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes') . '?filterby=shortlisted'; ?>"><?php echo Lang::txt('Candidate Shortlist'); ?></a></li>
			<?php } ?>
		<?php } else {  ?>
			<li>
				<a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('Administrator Dashboard'); ?></a>
			</li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header><!-- / #content-header -->

<form method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes'); ?>">
	<section class="main section">
		<div class="section-inner hz-layout-with-aside">
			<div class="subject">
			<?php if ($filters['filterby']== 'shortlisted') { ?>
				<h4><?php echo Lang::txt('Candidate Shortlist '); ?></h4>
			<?php } ?>
			<?php if (count($seekers) > 0) { // show how many ?>
				<p class="note_total">
					<?php echo Lang::txt('Displaying '); ?>
					<?php
					if ($filters['start'] == 0) {
						echo $pageNav->total > count($seekers) ? ' top ' . count($seekers) . ' out of ' . $pageNav->total : strtolower(Lang::txt('all')) . ' ' . count($seekers);
					} else {
						echo ($filters['start'] + 1);
						echo ' - ' . ($filters['start'] + count($seekers)) . ' out of ' . $pageNav->total;
					}
					echo ' ';
					if ($filters['filterby'] == 'shortlisted') {
						echo Lang::txt('shortlisted') . ' ';
					} else {
						echo '';
					}
					echo strtolower(Lang::txt('candidates'));
					?>
				</p>

				<ul id="candidates">
				<?php
				foreach ($seekers as $seeker)
				{
					?>
					<li>
					<?php
						$this->controller = '';
						$this->task = 'resumes';
						$view = $this->view('seeker');
						$params = new \Hubzero\Config\Registry(Plugin::params('members', 'resume'));

						$view->seeker   = $seeker;
						$view->emp      = $emp;
						$view->option   = 'com_members';
						$view->admin    = $admin;
						$view->params   = $params;
						$view->list     = 1;
						echo $view->loadTemplate();
					?>
					</li>
					<?php
				}
				?>
				</ul>
			<?php } else { // no candidates found ?>
				<p>
					<?php echo $filters['filterby']=='shortlisted' ? Lang::txt('You haven\'t yet included any candidates on your shortlist. Keep searching!') : Lang::txt('Sorry, no resumes found at the moment.'); ?>
				</p>
			<?php } ?>

			<?php
			// Insert page navigation
			$pageNav->setAdditionalUrlParam('task', 'resumes');
			$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
			$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
			$pageNav->setAdditionalUrlParam('category', $this->filters['category']);
			$pageNav->setAdditionalUrlParam('type', $this->filters['type']);
			$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
			echo $this->pageNav->render();
			?>
			</div><!-- / .subject -->
			<div class="aside">
		<?php if ($filters['filterby'] != 'shortlisted') { ?>
			<fieldset id="matchsearch">
				<label>
					<?php echo Lang::txt('Sort by'); ?>:
					<div class="together">
						<input class="option" type="radio" name="sortby" value="lastupdate"<?php if ($filters['sortby']!='bestmatch') { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('last update'); ?> &nbsp;
						<input class="option" type="radio" name="sortby" value="bestmatch"<?php if ($filters['sortby']=='bestmatch') { echo ' checked="checked"';
} else if (!$filters['match']) { echo ' disabled="disabled"'; } ?> /> <?php echo Lang::txt('best match'); ?>
					</div>
				</label>
				<label>
					<?php echo Lang::txt('Keywords'); ?>:
					<span class="questionmark tooltips" title="Keywords Search :: Use skill and action keywords separated by commas, e.g. XML, web, MBA etc."></span>
					<input name="q" maxlength="250" type="text" value="<?php echo $this->escape($filters['search']); ?>" />
				</label>
				<label>
					<?php echo Lang::txt('Category sought'); ?>:
					<?php echo \Components\Jobs\Helpers\Html::formSelect('category', $cats, $filters['category'], '', ''); ?>
				</label>
				<label>
					<?php echo Lang::txt('Type sought'); ?>:
					<?php echo \Components\Jobs\Helpers\Html::formSelect('type', $types, $filters['type'], '', ''); ?>
				</label>
				<label>
					<input class="option" type="checkbox" name="saveprefs" value="1" checked="checked" />
					<?php echo Lang::txt('Save my search preferences'); ?>
				</label>
				<input type="hidden" name="performsearch" value="1" />
				<p class="submit">
					<input type="submit" value="<?php echo Lang::txt('Search'); ?>" />
				</p>
			</fieldset>
		<?php } else { ?>
			<p>
				<?php echo Lang::txt('The listed candidates are those you bookmarked for further contact. Return to a list of '); ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes'); ?>"><?php echo Lang::txt('All Candidates'); ?></a>.
			</p>
		<?php } ?>
		</div><!-- / .aside -->
		</div>
	</section>
</form>
