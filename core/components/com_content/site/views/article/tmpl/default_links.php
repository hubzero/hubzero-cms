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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Create shortcut
$urls = json_decode($this->item->urls);

// Create shortcuts to some parameters.
$params = $this->item->params;

if ($urls && (!empty($urls->urla) || !empty($urls->urlb) || !empty($urls->urlc))): ?>
	<div class="content-links">
		<ul>
			<?php
			$urlarray = array(
				array($urls->urla, $urls->urlatext, $urls->targeta, 'a'),
				array($urls->urlb, $urls->urlbtext, $urls->targetb, 'b'),
				array($urls->urlc, $urls->urlctext, $urls->targetc, 'c')
			);
			foreach ($urlarray as $url):
				$link = $url[0];
				$label = $url[1];
				$target = $url[2];
				$id = $url[3];

				if (!$link):
					continue;
				endif;

				// If no label is present, take the link
				$label = ($label) ? $label : $link;

				// If no target is present, use the default
				$target = $target ? $target : $params->get('target' . $id);
				?>
				<li class="content-links-<?php echo $id; ?>">
					<?php
					// Compute the correct link
					switch ($target)
					{
						case 1:
							// open in a new window
							echo '<a href="' . htmlspecialchars($link) . '" target="_blank" rel="nofollow">' . htmlspecialchars($label) . '</a>';
							break;

						case 2:
							// open in a popup window
							$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=600';
							echo '<a href="' . htmlspecialchars($link) . '" onclick="window.open(this.href, \'targetWindow\', \'' . $attribs . '\'); return false;">'. htmlspecialchars($label) . '</a>';
							break;
						case 3:
							// open in a modal window
							Html::behavior('modal', 'a.modal'); ?>
							<a class="modal" href="<?php echo htmlspecialchars($link); ?>" rel="{handler: 'iframe', size: {x:600, y:600}}">
								<?php echo htmlspecialchars($label); ?>
							</a>
							<?php
							break;

						default:
							// open in parent window
							echo '<a href="' . htmlspecialchars($link) . '" rel="nofollow">' . htmlspecialchars($label) . ' </a>';
							break;
					}
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif;
