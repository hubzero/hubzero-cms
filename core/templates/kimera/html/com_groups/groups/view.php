<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

//get no_html request var
$no_html = Request::getInt( 'no_html', 0 );

//logo link - links to group overview page
$link = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn'));

// Determine the join policy
switch ($this->group->get('join_policy'))
{
	case 3:
		$policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_CLOSED_SETTING');
		break;
	case 2:
		$policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_INVITE_SETTING');
		break;
	case 1:
		$policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING');
		break;
	case 0:
	default:
		$policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_OPEN_SETTING');
		break;
}

// Determine the discoverability
switch ($this->group->get('discoverability'))
{
	case 1:
		$discoverability = Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_HIDDEN_SETTING');
		break;
	case 0:
	default:
		$discoverability = Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_VISIBLE_SETTING');
		break;
}

// use created date
$created = Date::of($this->group->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
?>

<?php if (!$no_html) : ?>
	<header id="content-header">
		<h2><?php echo $this->escape($this->group->get('description')); ?></h2>

		<dl>
			<dt><?php echo Lang::txt('COM_GROUPS_INFO_DISCOVERABILITY'); ?></dt>
			<dd><?php echo $discoverability; ?></dd>
			<dt><?php echo Lang::txt('COM_GROUPS_INFO_JOIN_POLICY'); ?></dt>
			<dd><?php echo $policy; ?></dd>
			<?php if ($created) : ?>
				<dt><?php echo Lang::txt('COM_GROUPS_INFO_CREATED'); ?></dt>
				<dd><?php echo $created; ?></dd>
			<?php endif; ?>
		</dl>
	</header>

	<?php echo \Components\Groups\Helpers\View::displayBeforeSectionsContent($this->group); ?>

	<div class="innerwrap">
		<div id="page_container">
			<div id="page_sidebar">
				<div id="page_identity">
					<a href="<?php echo $link; ?>" title="<?php echo Lang::txt('COM_GROUPS_OVERVIEW_HOME', $this->group->get('description')); ?>">
						<img src="<?php echo $this->group->getLogo(); ?>" alt="<?php echo Lang::txt('COM_GROUPS_OVERVIEW_LOGO', $this->group->get('description')); ?>" />
					</a>
				</div><!-- /#page_identity -->

				<?php
					// output group options
					echo \Components\Groups\Helpers\View::displayToolbar($this->group);

					// output group menu
					echo \Components\Groups\Helpers\View::displaySections($this->group);
				?>
			</div><!-- /#page_sidebar -->

			<div id="page_main">
				<?php if ($this->tab == 'overview') : ?>
				<div id="page_header">
					<h3>
						<?php echo \Components\Groups\Helpers\View::displayTab($this->group); ?>
					</h3>

					<?php
					$gt = new \Components\Groups\Models\Tags($this->group->get('gidNumber'));
					echo $gt->render();
					?>
				</div><!-- /#page_header -->
			<?php endif; ?>

				<?php if (count($this->notifications) > 0) { ?>
					<div id="page_notifications">
						<?php
						foreach ($this->notifications as $notification)
						{
							echo "<p class=\"message {$notification['type']}\">{$notification['message']}</p>";
						}
						?>
					</div><!-- /#page_notifications -->
				<?php } ?>

				<div id="page_content" class="group_<?php echo $this->tab; ?>">
<?php endif; ?>

					<?php
						// output content
						echo $this->content;
					?>

<?php if (!$no_html) : ?>
				</div><!-- /#page_content -->
			</div><!-- /#page_main -->
			<br class="clear" />
		</div><!-- /#page_container -->
	</div><!-- /.innerwrap -->
<?php endif;
