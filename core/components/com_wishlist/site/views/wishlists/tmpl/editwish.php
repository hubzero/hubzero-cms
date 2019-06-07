<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$tags = $this->wish->tags('string') ? $this->wish->tags('string') : Request::getString('tag', '');

if ($this->wishlist->get('id'))
{
	$login = Lang::txt('COM_WISHLIST_UNKNOWN');

	// what is submitter name?
	if ($this->task == 'editwish')
	{
		$login = $this->wish->proposer->get('username');
	}

	$this->wish->set('about', preg_replace('/<br\\s*?\/??>/i', '', $this->wish->get('about')));
?>
	<header id="content-header">
		<h2><?php echo $this->escape($this->title); ?></h2>

		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-lightbulb nav_wishlist btn" href="<?php echo Route::url($this->wishlist->link()); ?>">
						<?php echo Lang::txt('COM_WISHLIST_WISHES_ALL'); ?>
					</a>
				</li>
			</ul>
		</div><!-- / #content-header-extra -->
	</header>

	<section class="main section">
		<form id="hubForm" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

			<div class="explaination">
				<p><?php echo Lang::txt('COM_WISHLIST_TEXT_ADD_WISH'); ?></p>
				<?php if ($this->banking && $this->task != 'editwish') { ?>
					<p class="help">
						<strong><?php echo Lang::txt('COM_WISHLIST_WHAT_IS_REWARD'); ?></strong><br />
						<?php echo Lang::txt('COM_WISHLIST_WHY_ADDBONUS'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo Lang::txt('COM_WISHLIST_LEARN_MORE'); ?></a> <?php echo Lang::txt('COM_WISHLIST_ABOUT_POINTS'); ?>.
					</p>
				<?php } ?>
			</div><!-- / .aside -->
			<fieldset>
				<legend><?php echo Lang::txt('COM_WISHLIST_DETAILS'); ?></legend>

			<?php if ($this->task == 'editwish') { ?>
				<div class="form-group">
					<label for="field-by">
						<?php echo Lang::txt('COM_WISHLIST_WISH_PROPOSED_BY'); ?>: <span class="required"><?php echo Lang::txt('COM_WISHLIST_REQUIRED'); ?></span>
						<input name="by" maxlength="50" id="field-by" type="text" class="form-control" value="<?php echo $this->escape($login); ?>" />
					</label>
				</div>
			<?php } ?>

				<div class="form-group form-check">
					<label for="field-anonymous" class="form-check-label">
						<input class="option form-check-input" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" <?php echo ($this->wish->get('anonymous')) ? 'checked="checked"' : ''; ?>/>
						<?php echo Lang::txt('COM_WISHLIST_WISH_POST_ANONYMOUSLY'); ?>
					</label>
				</div>

			<?php if ($this->wishlist->access('manage') && $this->wishlist->isPublic()) { // list owner ?>
				<div class="form-group form-check">
					<label for="field-private" class="form-check-label">
						<input class="option form-check-input" type="checkbox" name="fields[private]" id="field-private" value="1" <?php echo ($this->wish->get('private')) ? 'checked="checked"' : ''; ?>/>
						<?php echo Lang::txt('COM_WISHLIST_WISH_MAKE_PRIVATE'); ?>
					</label>
				</div>
			<?php } ?>

				<input type="hidden" name="fields[proposed_by]" value="<?php echo $this->escape($this->wish->get('proposed_by')); ?>" />
				<input type="hidden" name="task" value="savewish" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="wishlist" value="<?php echo $this->escape($this->wishlist->get('id')); ?>" />
				<input type="hidden" name="fields[wishlist]" value="<?php echo $this->escape($this->wishlist->get('id')); ?>" />
				<input type="hidden" name="fields[status]" value="<?php echo $this->escape($this->wish->get('status')); ?>" />
				<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->wish->get('id')); ?>" />

				<?php echo Html::input('token'); ?>

				<div class="form-group">
					<label for="subject">
						<?php echo Lang::txt('COM_WISHLIST_SUMMARY_OF_WISH'); ?> <span class="required"><?php echo Lang::txt('COM_WISHLIST_REQUIRED'); ?></span>
						<input name="fields[subject]" maxlength="200" id="subject" type="text" class="form-control" value="<?php echo $this->escape(stripslashes($this->wish->get('subject'))); ?>" />
					</label>
				</div>

				<div class="form-group">
					<label for="field_about">
						<?php echo Lang::txt('COM_WISHLIST_WISH_EXPLAIN_IN_DETAIL'); ?>:
						<?php
							echo $this->editor('fields[about]', $this->escape($this->wish->get('about')), 35, 10, 'field_about', array('class' => 'form-control minimal no-footer'));
						?>
					</label>
				</div>

				<div class="form-group">
					<label for="actags">
						<?php echo Lang::txt('COM_WISHLIST_WISH_ADD_TAGS'); ?>: <br />
						<?php
						// Tag editor plug-in
						$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','', $tags)));
						if (count($tf) > 0) {
							echo $tf[0];
						} else { ?>
							<textarea name="tags" id="actags" class="form-control" rows="6" cols="35"><?php echo $this->escape($this->wish->tags('string')); ?></textarea>
						<?php } ?>
					</label>
				</div>

			<?php if ($this->banking && $this->task != 'editwish') { ?>
				<div class="form-group">
					<label for="field-reward">
						<?php echo Lang::txt('COM_WISHLIST_ASSIGN_REWARD'); ?>:<br />
						<input type="text" name="reward" id="field-reward" class="form-control" value="" size="5"<?php if ($this->funds <= 0) { echo ' disabled="disabled"'; } ?> />
						<span class="subtext"><?php echo Lang::txt('COM_WISHLIST_YOU_HAVE'); ?> <strong><?php echo $this->escape($this->funds); ?></strong> <?php echo Lang::txt('COM_WISHLIST_POINTS_TO_SPEND'); ?>.</span>
					</label>
				</div>
				<input type="hidden" name="funds" value="<?php echo $this->escape($this->funds); ?>" />
			<?php } ?>
			</fieldset>
			<div class="clear"></div>

			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_WISHLIST_FORM_SUBMIT'); ?>" />
				<a class="btn btn-secondary" href="<?php echo $this->wish->link(); ?>">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			</p>
		</form>
	</section><!-- / .main section -->
<?php } else { ?>
	<section class="main section">
		<p class="error"><?php echo Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'); ?></p>
	</section><!-- / .main section -->
<?php }
