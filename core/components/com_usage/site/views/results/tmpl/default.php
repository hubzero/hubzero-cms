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

if (!$this->no_html) {
	$this->css()
	     ->js();
	?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<nav>
	<ul class="sub-menu">
		<?php
		if ($this->cats) {
			$i = 1;
			$cs = array();
			foreach ($this->cats as $cat)
			{
				$name = key($cat);
				if ($cat[$name] != '') {
		?>
				<li id="sm-<?php echo $i; ?>"<?php if (strtolower($name) == $this->task) { echo ' class="active"'; } ?>><a class="tab" rel="<?php echo $name; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task='.$name); ?>"><span><?php echo $cat[$name]; ?></span></a></li>
		<?php
					$i++;
					$cs[] = $name;
				}
			}
		}
		?>
	</ul>
</nav>

<?php } ?>

<?php
$h = 'hide';
$c = 'main';
if ($this->sections) {
	$k = 0;
	foreach ($this->sections as $section)
	{
		if ($section != '')
		{
			$cls  = ($c) ? $c.' ' : '';
			if (key($this->cats[$k]) != $this->task)
			{
				$cls .= ($h) ? $h.' ' : '';
			}
			?>
			<section class="<?php echo $cls; ?>section" id="statistics">
				<?php echo $section; ?>
			</section><!-- / #statistics.<?php echo $cls; ?>section -->
			<?php
		}
		$k++;
	}
}
?>