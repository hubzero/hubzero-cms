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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
