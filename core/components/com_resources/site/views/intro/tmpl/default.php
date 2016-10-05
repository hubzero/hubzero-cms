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

$this->css('introduction.css', 'system')
     ->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
				<?php echo Lang::txt('COM_RESOURCES_SUBMIT_A_RESOURCE'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<div class="grid">
				<div class="col span6">
					<h3><?php echo Lang::txt('COM_RESOURCES_WHAT_ARE_RESOURCES'); ?></h3>
					<p><?php echo Lang::txt('COM_RESOURCES_WHAT_ARE_RESOURCES_EXPLANATION'); ?></p>
				</div>
				<div class="col span6 omega">
					<h3><?php echo Lang::txt('COM_RESOURCES_WHO_CAN_SUBMIT'); ?></h3>
					<p><?php echo Lang::txt('COM_RESOURCES_WHO_CAN_SUBMIT_EXPLANATION'); ?></p>
				</div>
			</div>
		</div>
		<div class="col span3 omega">
			<p>
				<a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=resources&page=index'); ?>">
					<?php echo Lang::txt('COM_RESOURCES_NEED_HELP'); ?>
				</a>
			</p>
		</div>
	</div><!-- / .aside -->
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('COM_RESOURCES_FIND_RESOURCE'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span-half">
					<form action="<?php echo Route::url('index.php?option=com_resources&task=browse'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="rsearch"><?php echo Lang::txt('COM_RESOURCES_SEARCH_LABEL'); ?></label>
								<input type="text" name="search" id="rsearch" value="" />
								<input type="submit" value="<?php echo Lang::txt('COM_RESOURCES_SEARCH'); ?>" />
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span-half -->
				<div class="col span-half omega">
					<div class="browse">
						<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_RESOURCES_BROWSE_LIST'); ?></a></p>
					</div><!-- / .browse -->
				</div><!-- / .col span-half -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

<?php
if ($this->categories) {
?>
	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('COM_RESOURCES_CATEGORIES'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
			<?php
			$i = 0;
			$clm = '';

			foreach ($this->categories as $category)
			{
				if ($category->id == 7 && !Component::isEnabled('com_tools', true))
				{
					continue;
				}

				$i++;
				switch ($i)
				{
					case 3: $clm = 'omega'; break;
					case 2: $clm = ''; break;
					case 1:
					default: $clm = ''; break;
				}

				if (substr($category->alias, -3) == 'ies')
				{
					$cls = $category->alias;
				}
				else
				{
					$cls = rtrim($category->alias, 's');
				}
				// Need to do some decoding to ensure escaped characters aren't encoded twice.
				$category->description = html_entity_decode(strip_tags(stripslashes($this->escape($category->description))));
				?>
				<div class="col span-third <?php echo $clm; ?>">
					<div class="resource-type <?php echo $cls; ?>">
						<h3>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $category->alias); ?>">
								<?php echo $this->escape(strip_tags(stripslashes($category->type))); ?>
							</a>
						</h3>
						<p>
							<?php echo $category->description; ?>
						</p>
						<p>
							<a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $category->alias); ?>">
								<?php echo Lang::txt('COM_RESOURCES_BROWSE_CATEGORY', $this->escape(stripslashes($category->type))); ?>
							</a>
						</p>
					</div>
				</div><!-- / .col span-third <?php echo $clm; ?> -->
				<?php
				if ($clm == 'omega') {
					echo '</div><div class="grid">';
					$clm = '';
					$i = 0;
				}
			}
			if ($i == 1) {
				?>
				<div class="col span-third">
					<p> </p>
				</div><!-- / .col span-third -->
				<?php
			}
			if ($i == 1 || $i == 2) {
				?>
				<div class="col span-third omega">
					<p> </p>
				</div><!-- / .col span-third -->
				<?php
			}
			?>
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->
<?php
}
?>

</section><!-- / .section -->
