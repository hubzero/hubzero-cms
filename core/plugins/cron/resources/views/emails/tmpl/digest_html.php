<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
$base = str_replace('/administrator', '', $base);
$sef  = Route::urlForClient('site', $this->member->link());
$link = $base . '/' . trim($sef, '/');
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
					<?php echo Lang::txt('COM_RESOURCES'); ?>
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

	<!-- Start Header -->
	<table class="tbl-message" width="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
					<?php echo Lang::txt('PLG_CRON_RESOURCES_EMAIL_MEMBERS_EXPLANATION', $link, $this->member->get('name') . ' (' . $this->member->get('username') . ')'); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="20"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

<?php foreach ($this->rows as $row) { ?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; font-size: 0.9em; line-height: 1.6em;">
		<tbody>
			<tr>
				<td style="padding: 5px; font-size: 1.2em; font-weight: bold; text-align: left; vertical-align: middle;" align="left">
					<a href="<?php echo $base . Route::urlForClient('site', $row->link()); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				</td>
			</tr>
			<tr>
				<td style="font-weight: normal; padding: 5px; text-align: left; color: #aaaaaa;" align="left">
					<?php
					$info = array();
					if ($thedate = $row->date)
					{
						$info[] = $thedate;
					}

					if ($row->params->get('show_type'))
					{
						$info[] = stripslashes($row->type->get('type'));
					}

					if ($row->authors->count() && $row->params->get('show_authors'))
					{
						$authors = $row->authorsList();

						if (trim($authors))
						{
							$info[] = Lang::txt('PLG_CRON_RESOURCES_CONTRIBUTORS') . ': ' . $authors;
						}
					}

					echo implode(' <span>|</span> ', $info);
					?>
				</td>
			</tr>
			<tr>
				<td style="font-weight: normal; padding: 5px; text-align: left;" align="left">
					<?php
					$content = '';
					if ($row->get('introtext'))
					{
						$content = $row->get('introtext');
					}
					else if ($row->get('fulltxt'))
					{
						$content = $row->get('fulltxt');
						$content = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $content);
						$content = trim($content);
					}

					echo \Hubzero\Utility\Str::truncate(strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($content))), 300);
					?>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="10"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->
<?php } ?>

	<!-- Start Footer -->
	<table class="tbl-message" width="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px;">
					<a href="<?php echo $base . Route::urlForClient('site', 'index.php?option=com_resources'); ?>">
						<?php echo Lang::txt('PLG_CRON_RESOURCES_EMAIL_MEMBERS_MORE', Config::get('sitename')); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Footer -->

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="20"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->