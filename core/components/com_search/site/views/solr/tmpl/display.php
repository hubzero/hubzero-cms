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

// No direct access
defined('_HZEXEC_') or die();

$layout = 'fullsearch';
?>

<div class="guide">

<?php if (0): ?>
	<nav class="guide-nav">
		<nav class="guide-nav-menu">
			<?php
			$view = new \Hubzero\Component\View(array('name'=>'solr', 'layout' => 'navigation'));
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
<?php endif; ?>
	<div id="search-params">

	</div>

	<div id="component">
		<?php
			// Try to load the page requested
			//$layout = $this->page;
			$view = new \Hubzero\Component\View(array('name'=>'solr', 'layout' => $layout));
			$view->results = $this->results;
			$view->queryString = $this->queryString;
			$view->option = $this->option;
			$view->controller = $this->controller;

			try
			{
				$view->display();
			}
			// The view for the requested page doesn't exist, load the 404 view instead
			catch (\Exception $e)
			{
				echo "<pre>";
				var_dump($e);
				die;
				$view = new \Hubzero\Component\View(array('name'=>'solr', 'layout' => '404'));
				$view->display();
			}
		?>
	</div>
</div>
