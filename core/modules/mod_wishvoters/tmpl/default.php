<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();
?>
<div<?php echo ($this->params->get('moduleclass')) ? ' class="' . $this->params->get('moduleclass') . '"' : ''; ?>>
	<h3><?php echo Lang::txt('MOD_WISHVOTERS_GIVING_MOST_INPUT'); ?></h3>
<?php if (count($this->rows) <= 0) { ?>
	<p><?php echo Lang::txt('MOD_WISHVOTERS_NO_VOTES'); ?></p>
<?php } else { ?>
	<ul class="voterslist">
		<li class="title">
			<?php echo Lang::txt('MOD_WISHVOTERS_COL_NAME'); ?>
			<span><?php echo Lang::txt('MOD_WISHVOTERS_COL_RANKED'); ?></span>
		</li>
		<?php
			$k=1;
			foreach ($this->rows as $row)
			{
				if ($k <= intval($this->params->get('limit', 10)))
				{
					$name = Lang::txt('MOD_WISHVOTERS_UNKNOWN');
					$auser = User::getInstance($row->userid);
					if (is_object($auser))
					{
						$name  = $auser->get('name');
						$login = $auser->get('username');
					}
					?>
					<li>
						<span class="lnum"><?php echo $k; ?>.</span>
						<?php echo stripslashes($name); ?>
						<span class="wlogin">(<?php echo stripslashes($login); ?>)</span>
						<span><?php echo $row->times; ?></span>
					</li>
					<?php
					$k++;
				}
			}
		?>
	</ul>
<?php } ?>
</div><!-- / <?php echo ($this->params->get('moduleclass')) ? '.' . $this->params->get('moduleclass') : ''; ?> -->