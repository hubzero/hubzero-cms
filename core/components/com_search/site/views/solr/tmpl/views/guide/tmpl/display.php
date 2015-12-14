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

// No direct access
defined('_HZEXEC_') or die();

$this->css()
		->css('syntaxhighlighter/shCore')
		->css('syntaxhighlighter/shThemeDefault')
		->js('syntaxhighlighter/shCore')
		->js('syntaxhighlighter/shBrushCss')
		->js('syntaxhighlighter/shBrushXml')
		->js();
?>

<div class="guide">
	<nav class="guide-nav">
		<nav class="guide-nav-menu">
			<?php
			$view = new \Hubzero\Component\View(array('name'=>'guide', 'layout' => 'navigation'));
			$view->display();
			?>
		</nav>

		<div class="guide-controls">
			<a class="guide-panels-toggle" href="#">
					Hide the panel
					<button>
						<span>Toggle the panel</span>
					</button>
			</a>
		</div>
	</nav>
	<main>
		<div id="component">
			<?php
				// Try to load the page requested
				$layout = $this->page;
				$view = new \Hubzero\Component\View(array('name'=>'pages', 'layout' => $layout));
				try
				{
					$view->display();
				}
				// The view for the requested page doesn't exist, load the 404 view instead
				catch (\Exception $e)
				{
					$view = new \Hubzero\Component\View(array('name'=>'guide', 'layout' => '404'));
					$view->display();
				}
			?>
		</div>
	</main>
</div>