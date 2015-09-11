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

// No direct access.
defined('_HZEXEC_') or die();

$newSession = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->option . '&task=invoke&app=' . $this->app->toolname . '&version='. $this->app->version), 'server');
if (strstr($newSession, '?'))
{
	$newSession .= '&amp;newinstance=1';
}
else
{
	$newSession .= '?newinstance=1';
}
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_TOOLS_MYSESSIONS'); ?></h2>
</header><!-- / #content-header -->

<section class="main section" id="mysessions-section">
	<p class="info">
		<?php echo Lang::txt('COM_TOOLS_MYSESSIONS_WARNING_INSTANCE_RUNNING'); ?>
	</p>
	<table class="sessions">
		<thead>
			<tr>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_SESSION'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_STARTED'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_LAST_ACCESSED'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_OPTION'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<a href="<?php echo $newSession; ?>">
						<?php echo Lang::txt('COM_TOOLS_MYSESSIONS_START_NEW'); ?>
					</a>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		if ($this->sessions) {
			$cls = 'even';
			foreach ($this->sessions as $session)
			{
				$cls = ($cls == 'odd') ? 'even' : 'odd';
		?>
			<tr class="<?php echo $cls; ?>">
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=session&app=' . $session->appname . '&sess=' . $session->sessnum); ?>" title="<?php echo Lang::txt('COM_TOOLS_RESUME_TITLE'); ?>">
						<?php echo $session->sessname; ?>
					</a>
				</td>
				<td>
					<?php echo $session->start; ?>
				</td>
				<td>
					<?php echo $session->accesstime; ?>
				</td>
			<?php if (User::get('username') == $session->username) { ?>
				<td>
					<a class="closetool" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=stop&app=' . $session->appname . '&sess=' . $session->sessnum); ?>" title="<?php echo Lang::txt('COM_TOOLS_TERMINATE_TITLE'); ?>">
						<?php echo Lang::txt('COM_TOOLS_TERMINATE'); ?>
					</a>
				</td>
			<?php } else { ?>
				<td>
					<a class="disconnect" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=unshare&app=' . $session->appname . '&sess=' . $session->sessnum); ?>" title="<?php echo Lang::txt('COM_TOOLS_DISCONNECT_TITLE'); ?>">
						<?php echo Lang::txt('COM_TOOLS_DISCONNECT'); ?>
					</a>
					<span class="owner"><?php echo Lang::txt('COM_TOOLS_MY_SESSIONS_OWNER').': '.$session->username; ?></span>
				</td>
			<?php } ?>
			</tr>
		<?php
			}
		}
		?>
		</tbody>
	</table>
</section><!-- / .section -->