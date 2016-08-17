<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$browser = new \Hubzero\Browser\Detector();
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS'); ?>
</h3>

<?php if ($this->isAuthorised) { ?>
	<p class="admin"><a class="btn icon-edit" href="<?php echo Route::url($this->base . '&action=edit'); ?>"><?php echo Lang::txt('JACTION_EDIT'); ?></a></p>
<?php } ?>

<?php if ($browser->name() != 'chrome') { ?>
	<p class="warning">Note: Windows Tools require the use of the Chrome web browser only.</p>
<?php } ?>

<?php if ($content = $this->page->get('content')) { ?>
	<?php echo $content; ?>
<?php } else { ?>
	<ol>
		<li>Click <strong>Launch Tool</strong>
			<ol style="list-style-type: lower-alpha">
				<li>First time Windows Tools users will be prompted to download and install the Hubzero Application, if you are a first time user follow these extra steps:
					<ol style="list-style-type: lower-roman">
						<li>In a new tab you will be taken to the <strong>Chrome Web Store</strong> and directly to the <strong>Hubzero Windows Client for Amazon AppStream</strong></li>
						<li>Click <strong>+ Add to Chrome</strong></li>
						<li>A pop-up will appear stating “Add ‘Hubzero Windows Client for Amazon AppStream’? It can: Exchange data with any device on the local network or internet” and confirm by clicking <strong>Add app</strong></li>
						<li>The app will download and plugin to your Chrome browser</li>
						<li>Navigate back to cdmhub.org and complete steps 1 and 2 again</li>
					</ol>
				</li>
			</ol>
		</li>
		<li>Review the <strong>Terms &amp; Conditions</strong> and click <strong>Accept</strong></li>
		<li>The <strong>Hubzero Windows Client for Amazon AppStream</strong> will then connect to the tool and will be ready to use
			<ol style="list-style-type: lower-alpha">
				<li>
					Note: If there are too many sessions running, you will be given an error stating, “No available sessions, try again in 10 minutes”<br />
					<img src="/core/plugins/resources/windowstools/assets/img/winappclient.png" alt="Windows App Client" />
				</li>
				<li>Close the <strong>Hubzero Windows Client for Amazon AppStream</strong> and then wait 10 minutes before completing steps 1 – 4 again</li>
			</ol>
		</li>
	</ol>
<?php } ?>
