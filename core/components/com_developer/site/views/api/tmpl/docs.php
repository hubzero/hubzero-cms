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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$host = $_SERVER['HTTP_HOST'];
list($base, ) = explode('.', $host);
$url = 'https://' . $host . '/api';

// include needed css
$this->css('docs')
     ->css();

// add highlight lib
//Document::addStyleSheet('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/styles/github.min.css');
//Document::addScript('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js');

// pull list of versions from doc
$versions = $this->documentation['versions']['available'];
$versions = array_reverse($versions);

// either the request var or the first version (newest)
$activeVersion = Request::getVar('version', reset($versions));
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_DOCS'); ?></h2>
	<div id="content-header-extra">
		<ul>
			<li>
				<a class="btn icon-cog" href="<?php echo Route::url('index.php?option=com_developer&controller=api'); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_HOME'); ?>
				</a>
			<?php if (!empty($versions)) : ?>
				<div class="btn-group dropdown">
					<a class="btn" href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs&version=' . $activeVersion); ?>"><?php echo $activeVersion; ?></a>
					<span class="btn dropdown-toggle"></span>
					<ul class="dropdown-menu">
						<?php foreach ($versions as $version) : ?>
							<li>
								<a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs&version=' . $version); ?>">
									<?php echo $version; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			</li>
		</ul>
	</div>
</header>

<section class="section api docs">
	<div class="section-inner">
		<aside class="aside">
			<?php 
			$this->view('_menu')
				 ->set('documentation', $this->documentation)
				 ->set('active', '')
				 ->set('version', $activeVersion)
				 ->display();
			?>
		</aside>
		<div class="subject">
			<?php 
			$this->view('_docs_overview')
				 ->set('url', $url)
				 ->set('base', $base)
				 ->display();

			$this->view('_docs_oauth')
				 ->set('url', $url)
				 ->display();
			?>
		</div>
	</div>
</section>
