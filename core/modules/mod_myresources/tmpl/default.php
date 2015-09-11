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

// no direct access
defined('_HZEXEC_') or die();

if (!$this->no_html)
{
	// Push the module CSS to the template
	$this->css();
	$this->js();
?>
	<ul class="module-nav">
		<li>
			<a class="icon-browse" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=contributions&area=resources'); ?>">
				<?php echo Lang::txt('MOD_MYRESOURCES_ALL_PUBLICATIONS'); ?>
			</a>
		</li>
	</ul>
	<form method="get" action="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>" data-module="<?php echo $this->module->id; ?>" id="myresources-form" enctype="multipart/form-data">
<?php } ?>
		<div id="myresources-content">
			<?php if (!$this->contributions) { ?>
				<p><?php echo Lang::txt('MOD_MYRESOURCES_NONE_FOUND'); ?></p>
			<?php } else { ?>
				<ul class="expandedlist">
					<?php
					for ($i=0; $i < count($this->contributions); $i++)
					{
						// Determine css class
						switch ($this->contributions[$i]->published)
						{
							case 1:  $class = 'published';  break;  // published
							case 2:  $class = 'draft';      break;  // draft
							case 3:  $class = 'pending';    break;  // pending
							case 0:  $class = 'deleted';    break;  // pending
						}

						$thedate = Date::of($this->contributions[$i]->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
						?>
						<li class="<?php echo $class; ?>">
							<a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->contributions[$i]->id); ?>">
								<?php echo \Hubzero\Utility\String::truncate(stripslashes($this->contributions[$i]->title), 40); ?>
							</a>
							<span class="under">
								<?php echo $thedate . ' &nbsp; ' . $this->escape(stripslashes($this->contributions[$i]->typetitle)); ?>
							</span>
						</li>
						<?php
					}
					?>
				</ul>
			<?php } ?>
		</div>
<?php if (!$this->no_html) { ?>
	</form>
<?php }