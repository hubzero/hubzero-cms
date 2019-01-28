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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Compute some counts for later use
$groups = count($this->posts);
$posts  = 0;

array_walk($this->posts, function($val, $idx) use (&$posts)
{
	$posts += $val->count();
});
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

<table id="group-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; background-color: #F3F3F3; border: 1px solid #DDDDDD;">
	<tr>
		<td width="85" style="padding: 0 0 0 15px; opacity: 0.8">
			<img width="80" src="<?php echo Request::root() . '/core/components/com_forum/site/assets/img/group.png'; ?>" />
		</td>
		<td width="565" style="padding: 14px;">
			<span style="font-weight: bold; font-size:14px;">Your <?php echo $this->interval; ?> group discussion digest</span>
			<hr />
			<span>You have <?php echo $posts; ?> new post<?php if ($posts > 1) echo 's'; ?> across <?php echo $groups; ?> of your groups</span>
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
	</tr>
</table>

<?php foreach ($this->posts as $group => $posts) : ?>
	<table id="group-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
		<tr>
			<td colspan="2" style="text-align:center;font-size:14px;">
				<?php $group = Hubzero\User\Group::getInstance($group); ?>
				<?php echo $group->description; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php foreach ($posts as $post) : ?>
					<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
						<tr>
							<td width="75" style="padding: 10px 0;">
								<img width="50" src="<?php echo Request::root() . '/members/' . $post->created_by . '/Image:thumb.png'; ?>" />
							</td>
							<td style="padding: 10px 0;">
								<div style="position: relative; border: 1px solid #CCCCCC; padding: 12px; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px;">
									<div style="background: #FFFFFF; border: 1px solid #CCCCCC; width: 15px; height: 15px;
										position: absolute; top: 50%; left: -10px; margin-top: -7px;
										transform:rotate(45deg); -ms-transform:rotate(45deg); -webkit-transform:rotate(45deg);"></div>
									<div style="background: #FFFFFF; width: 11px; height: 23px; position: absolute; top: 50%; left: -1px; margin-top: -10px;"></div>
									<div style="color: #AAAAAA; font-size: 11px;">
										<?php
										$name = Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS');
										if (!$post->anonymous)
										{
											$name = User::getInstance($post->created_by)->get('name');
										}
										?>

										<?php echo $name; ?> | <?php echo Date::of($post->created)->toLocal('M j, Y g:i:s a'); ?>
									</div>
									<div>
										<?php echo $post->comment; ?>
									</div>
									<div style="color: #AAAAAA; font-size: 11px;">
										<?php $base = rtrim(Request::root(), '/'); ?>
										<?php $sef  = Route::urlForClient('site', Components\Forum\Models\Post::one($post->id)->link()); ?>
										<?php $link = $base . '/' . trim($sef, '/'); ?>
										<a href="<?php echo $link; ?>">View this post on <?php echo Config::get('sitename'); ?></a>
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
