<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = JURI::base();
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
{
	$base = str_replace('http://', 'https://', $base);
}

$controller = JRequest::getCmd('controller', JRequest::getCmd('view', ''));

$this->css();
?>

<div class="captcha-block">
	<table>
		<tbody>
			<tr>
				<td>
					<img id="captchaCode<?php echo $this->total; ?>" src="<?php echo $base; ?>index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $controller; ?>&amp;task=<?php echo $this->task; ?>&amp;no_html=1&amp;showCaptcha=True&amp;instanceNo=<?php echo $this->total; ?>" alt="CAPTCHA Image" />
				</td>
				<td>
					<script type="text/javascript">
					//<![CDATA[
					function reloadCapthcha<?php echo $this->total; ?>(instanceNo)
					{
						var captchaSrc = "<?php echo $base; ?>index.php?option=<?php echo $this->option; ?>&controller=<?php echo $controller; ?>&task=<?php echo $this->task; ?>&no_html=1&showCaptcha=True&instanceNo="+instanceNo+"&time="+ new Date().getTime();
						document.getElementById('captchaCode'+instanceNo).src = captchaSrc;
					}
					//]]>
					</script>

					<a href="#" onclick="reloadCapthcha<?php echo $this->total; ?>(<?php echo $this->total; ?>);return false;" ><?php echo JText::_('PLG_HUBZERO_IMAGECAPTCHA_REFRESH_CAPTCHA'); ?></a>
				</td>
			</tr>
		</tbody>
	</table>

	<label for="imgCatchaTxt<?php echo $this->total; ?>">
		<?php echo JText::_('PLG_HUBZERO_IMAGECAPTCHA_ENTER_CAPTCHA_VALUE'); ?>
		<input type="text" name="imgCatchaTxt" id="imgCatchaTxt<?php echo $this->total; ?>" />
	</label>

	<input type="hidden" name="imgCatchaTxtInst" id="imgCatchaTxtInst" value="<?php echo $this->total; ?>" />
</div>
