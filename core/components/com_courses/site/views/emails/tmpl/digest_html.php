<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = Request::root() . 'courses/' . $this->course->get('alias');
?>
<!-- Start Header -->
<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
				<?php echo Config::get('sitename'); ?>
			</td>
			<td width="80%" align="left" valign="bottom" class="tagline mobilehide">
				<span class="home">
					<a href="<?php echo Request::base(); ?>"><?php echo Request::base(); ?></a>
				</span>
				<br />
				<span class="description"><?php echo Config::get('MetaDesc'); ?></span>
			</td>
			<td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
				Instructor Digest
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

<table id="course-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; background-color: #F3F3F3; border: 1px solid #DDDDDD;">
	<tr>
		<td width="85" style="padding: 0 0 0 15px; opacity: 0.8">
			<?php $cap_path = rtrim(Request::root(), '/') . '/core/components/com_courses/site/assets/img/cap.png'; ?>
			<img width="80" src="<?php echo $cap_path; ?>" />
		</td>
		<td width="565" style="padding: 14px; border-bottom: 1px solid #CCCCCC;">
			<span style="font-weight: bold; font-size:14px;">Course Update:</span>
			<br />
			<span><?php echo $this->course->get('title'); ?></span>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="padding: 10px 14px;">
			Link: <a href="<?php echo $base; ?>"><?php echo $base; ?></a>
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

<table id="course-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; background-color: #EEEEEE;">
	<tr>
		<td width="315" style="background-color: #FFFFFF;">
			<table  width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #DDDDDD;">
				<tr style="border-collapse: collapse;">
					<td height="30" width="40%" style="border-collapse: collapse; padding: 10px;">
						<span style="">Enrollments</span>
						<br />
						<span style="line-height: 40px; font-size:35px;"><?php echo $this->enrollments; ?></span>
					</td>
					<td height="30" width="60%" style="border-collapse: collapse;">
						<table  width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
							<tr style="border-collapse: collapse; background-color: rgb(229, 244, 235);">
								<td nowrap="nowrap" style="border-collapse: collapse; padding: 10px; border-bottom: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD;">
									<span style="color: green; font-weight: bold; font-size: 15px;"><?php echo $this->passing; ?> passing</span>
								</td>
							</tr>
							<tr style="border-collapse: collapse; background-color: rgb(252, 229, 229);">
								<td nowrap="nowrap" style="border-collapse: collapse; padding: 10px; border-left: 1px solid #DDDDDD;">
									<span style="color: red; font-weight: bold; font-size: 15px;"><?php echo $this->failing; ?> failing</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td height="20" width="20" style="border-collapse: collapse; background-color: #FFFFFF; border: none;"></td>
		<td width="315">
			<table  width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #DDDDDD;">
				<tr style="border-collapse: collapse;">
					<td height="30" width="50%" style="border-collapse: collapse; padding: 10px; background-color: #FFFFFF;">
						<span style="">Discussion Topics</span>
						<br />
						<span style="line-height: 40px; font-size:35px;"><?php echo $this->posts_cnt; ?></span>
					</td>
					<td height="30" width="50%" style="border-collapse: collapse; border-left: 1px solid #DDDDDD; padding: 10px; background-color:rgb(252, 243, 223); color: rgb(230, 158, 0)">
						<span style="">New</span>
						<br />
						<span style="line-height: 40px; font-size:35px;"><?php echo $this->latest_cnt; ?></span>
					</td>
				</tr>
			</table>
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

<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
	<tr style="border-bottom: 1px solid #DDDDDD;">
		<td style="font-size: 13px; font-weight: bold; padding: 4px 0;">
			Latest Discussions
		</td>
		<td style="text-align: right;">
			<a href="<?php echo $base . '/' . $this->offering->get('alias') . '/discussions'; ?>"><?php echo $base . '/' . $this->offering->get('alias') . '/discussions'; ?></a>
		</td>
	</tr>
</table>
<?php if (count($this->latest) > 0) : ?>
	<?php foreach ($this->latest as $post) : ?>
		<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
			<tr>
				<td width="75" style="padding: 10px 0;">
					<img width="50" src="<?php echo Request::root() . User::getInstance($post->created_by)->picture(); ?>" />
				</td>
				<td style="padding: 10px 0;">
					<div style="position: relative; border: 1px solid #CCCCCC; padding: 12px; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px;">
						<div style="background: #FFFFFF; border: 1px solid #CCCCCC; width: 15px; height: 15px;
							position: absolute; top: 50%; left: -10px; margin-top: -7px;
							transform:rotate(45deg); -ms-transform:rotate(45deg); -webkit-transform:rotate(45deg);"></div>
						<div style="background: #FFFFFF; width: 11px; height: 23px; position: absolute; top: 50%; left: -1px; margin-top: -10px;"></div>
						<div style="color: #AAAAAA; font-size: 11px; text-align:center;">
							<?php echo User::getInstance($post->created_by)->get('name'); ?> | created: <?php echo Date::of($post->created)->toLocal('M j, Y g:i:s a'); ?>
						</div>
						<div>
							<?php echo $post->comment; ?>
						</div>
						<div style="color: #AAAAAA; font-size: 11px; text-align:center;">
							<?php $reply  = $base . '/' . $this->offering->get('alias') . '/discussions'; ?>
							<?php $thread = ($post->parent) ? $post->parent : $post->id; ?>
							<a href="<?php echo $reply . '?thread='.$thread.'&reply='.$post->id; ?>">reply</a>
						</div>
					</div>
				</td>
			</tr>
		</table>
	<?php endforeach; ?>
<?php else : ?>
	<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
		<tr>
			<td style="padding: 10px 0;">
				<div>
					No new comments to display
				</div>
			</td>
		</tr>
	</table>
<?php endif; ?>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->
