<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('applications')
     ->css()
     ->js();

$title  = Lang::txt('COM_DEVELOPER_API_APPLICATION_NEW');
$return = Route::url('index.php?option=com_developer&controller=applications');
if ($this->application->get('id') > 0)
{
	$title  = Lang::txt('COM_DEVELOPER_API_APPLICATION_EDIT', $this->application->get('name'));
	$return = Route::url($this->application->link());
}

$sectionInnerClass = '';
if ($this->application->get('id'))
{
	$sectionInnerClass = 'hz-layout-with-aside';
}

?>

<header id="content-header">
	<h2><?php echo $title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_developer&controller=applications'); ?>">
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_ALL'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner<?php echo $sectionInnerClass; ?>">
		<div class="subject">
			<?php if ($this->getError()) { ?>
				<p class="error"><?php echo $this->getError(); ?></p>
			<?php } ?>

			<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm" class="full">
				<fieldset>
					<legend><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_DETAILS'); ?></legend>

					<label for="field-name">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_NAME'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
						<input type="text" name="application[name]" id="field-name" value="<?php echo $this->escape($this->application->get('name')); ?>" />
					</label>

					<label for="field-description">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_DESCRIPTION'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
						<textarea name="application[description]" id="field-description" rows="8"><?php echo $this->escape($this->application->get('description')); ?></textarea>
					</label>

					<label for="field-redirect_uri">
						<?php
							$uris = explode(' ', $this->application->get('redirect_uri'));
							$uris = implode(PHP_EOL, $uris);
						?>
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_REDIRECT_URI'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
						<textarea name="application[redirect_uri]" id="field-redirect_uri" rows="3"><?php echo $this->escape($uris); ?></textarea>
						<span class="hint"><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_REDIRECT_URI_HINT'); ?></span>
					</label>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM'); ?></legend>

					<?php if ($this->application->get('id')) : ?>
						<label>
							<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_CURRENT_TEAM'); ?>:
							<?php
								$team = $this->application->team();
								echo $this->view('_team')
										  ->set('members', $team)
										  ->display();
							?>
						</label>
					<?php else : ?>
						<p class="info"><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_DONT_ADD_YOURSELF'); ?></p>
					<?php endif;?>

					<label for="whoknows">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_ADD'); ?>: <span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
						<?php
							$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'team', 'acmembers')));
							if (count($mc) > 0) {
								echo $mc[0];
							} else { ?>
								<input type="text" name="team" id="acmembers" value="" size="35" />
							<?php } ?>
						<span class="hint"><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_ADD_HINT'); ?></span>
					</label>
				</fieldset>

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_DEVELOPER_SAVE'); ?>">
					<a class="btn btn-secondary" href="<?php echo $return; ?>">
						<?php echo Lang::txt('JCANCEL'); ?>
					</a>
				</p>
				<input type="hidden" name="option" value="com_developer" />
				<input type="hidden" name="controller" value="applications" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="application[id]" value="<?php echo $this->application->get('id'); ?>" />
				<?php echo Html::input('token'); ?>
			</form>
		</div>

		<?php if ($this->application->get('id')) : ?>
			<div class="aside">
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
					<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET_CLIENT_SECRET'); ?></h3>
					<p><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET_CLIENT_SECRET_DESC'); ?></p>
					<button type="submit" class="btn btn-warning confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET_CLIENT_SECRET_CONFIRM'); ?>">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET'); ?>
					</button>
					<input type="hidden" name="option" value="com_developer" />
					<input type="hidden" name="controller" value="applications" />
					<input type="hidden" name="task" value="resetclientsecret" />
					<input type="hidden" name="id" value="<?php echo $this->application->get('id'); ?>" />
					<?php echo Html::input('token'); ?>
				</form>
				<hr />
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
					<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE'); ?></h3>
					<p><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE_DESC'); ?></p>
					<button type="submit" class="btn btn-danger confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE_CONFIRM'); ?>">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE'); ?>
					</button>
					<input type="hidden" name="option" value="com_developer" />
					<input type="hidden" name="controller" value="applications" />
					<input type="hidden" name="task" value="delete" />
					<input type="hidden" name="id" value="<?php echo $this->application->get('id'); ?>" />
					<?php echo Html::input('token'); ?>
				</form>
			</div>
		<?php endif; ?>
	</div>
</section>
