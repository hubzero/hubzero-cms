<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::root(), '/');

// Accent colour for buttons/links (neutral, brand-agnostic default)
$accent = '#2b6cb0';

// Compute some counts for later use
$groups = count($this->posts);
$posts  = 0;

array_walk($this->posts, function($val, $idx) use (&$posts)
{
	$posts += $val->count();
});

// Inbox preview text (preheader): the short summary most mail clients show
// beside the subject line. Kept out of the visible body via inline styles.
$preheader = 'You have ' . $posts . ' new post' . ($posts != 1 ? 's' : '')
	. ' across ' . $groups . ' of your group' . ($groups != 1 ? 's' : '') . '.';
?>
<!-- Start Preheader -->
<span style="display:none !important; visibility:hidden; opacity:0; color:transparent; height:0; width:0; max-height:0; max-width:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px;"><?php echo $this->escape($preheader); ?></span>
<!-- End Preheader -->

<!-- Start Header -->
<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
				<?php echo $this->escape(Config::get('sitename')); ?>
			</td>
			<td width="80%" align="left" valign="bottom" class="tagline mobilehide">
				<span class="home">
					<a href="<?php echo Request::base(); ?>"><?php echo Request::base(); ?></a>
				</span>
				<br />
				<span class="description"><?php echo $this->escape(Config::get('MetaDesc')); ?></span>
			</td>
			<td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
				Group Digest
			</td>
		</tr>
	</tbody>
</table>
<!-- End Header -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->

<table id="digest-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; background-color: #f7f7f8; border: 1px solid #dddddd; border-radius: 6px;">
	<tr>
		<td width="85" style="padding: 0 0 0 15px; opacity: 0.8;">
			<img width="80" src="<?php echo $base . '/core/components/com_forum/site/assets/img/group.png'; ?>" alt="" />
		</td>
		<td width="565" style="padding: 14px;">
			<span style="font-weight: bold; font-size: 14px; color: #333;">Your <?php echo $this->escape($this->interval); ?> group discussion digest</span>
			<hr style="border: none; border-top: 1px solid #dddddd; margin: 8px 0;" />
			<span>You have <strong><?php echo $posts; ?></strong> new post<?php echo ($posts != 1 ? 's' : ''); ?> across <strong><?php echo $groups; ?></strong> of your group<?php echo ($groups != 1 ? 's' : ''); ?>.</span>
		</td>
	</tr>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->

<table width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
	<tr style="border-bottom: 1px solid #dddddd;">
		<td style="font-size: 13px; font-weight: bold; color: #333; padding: 4px 0;">
			Latest Discussions
		</td>
	</tr>
</table>

<?php foreach ($this->posts as $group => $posts) : ?>
	<?php $group = Hubzero\User\Group::getInstance($group); ?>
	<table id="group-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
		<tr>
			<td colspan="2" style="text-align: center; font-size: 14px; padding: 14px 0 4px;">
				<strong><?php echo $this->escape($group->get('description')); ?></strong>
				<span style="color: #999;">&mdash; <?php echo $posts->count(); ?> new post<?php echo ($posts->count() != 1 ? 's' : ''); ?></span>
			</td>
		</tr>
		<tr>
			<td>
				<?php foreach ($posts as $post) : ?>
					<table width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
						<tr>
							<td width="75" valign="top" style="padding: 10px 0;">
								<img width="50" height="50" style="border-radius: 50%;" src="<?php echo $base . '/members/' . $post->created_by . '/Image:thumb.png'; ?>" alt="" />
							</td>
							<td style="padding: 10px 0;">
								<div style="border: 1px solid #dddddd; padding: 12px; border-radius: 6px; background: #ffffff;">
									<div style="color: #999999; font-size: 11px; padding-bottom: 6px;">
										<?php
										$name = Lang::txt('JANONYMOUS');
										if (!$post->anonymous)
										{
											$name = User::getInstance($post->created_by)->get('name');
										}
										?>
										<strong style="color: #555;"><?php echo $this->escape($name); ?></strong> &middot; <?php echo Date::of($post->created)->toLocal('M j, Y g:i a'); ?>
									</div>
									<div style="color: #333;">
										<?php echo $post->comment; ?>
									</div>
									<?php
									$sef  = Route::urlForClient('site', Components\Forum\Models\Post::one($post->id)->link());
									$link = $base . '/' . trim($sef, '/');
									?>
									<div style="margin-top: 10px; text-align: right;">
										<a href="<?php echo $link; ?>" style="display: inline-block; padding: 6px 14px; font-size: 11px; color: #ffffff; background-color: <?php echo $accent; ?>; text-decoration: none; border-radius: 4px;">View this post &rsaquo;</a>
									</div>
								</div>
							</td>
						</tr>
					</table>
				<?php endforeach; ?>
			</td>
		</tr>
	</table>
<?php endforeach; ?>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->
