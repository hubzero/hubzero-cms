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

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_STORE_STOREFRONT'); ?></h2>

	<div id="content-header-extra">
		<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart'); ?>" class="icon-basket btn"><?php echo Lang::txt('COM_STORE_CART'); ?></a></p>
	</div>
</header>

<section class="main section">
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="get">
		<h3><?php echo Lang::txt('COM_STORE_SPEND_MERCHANDISE_AND_PREMIUM_SERVICES'); ?></h3>
		<?php if ($this->rows) { ?>
			<p><?php echo Lang::txt('COM_STORE_THERE_ARE_ITEMS_AVAILABLE', count($this->rows)); ?></p>
			<div class="store-wrap">
				<ul class="storeitems">
				<?php
					foreach ($this->rows as $row)
					{
						$cls = '';
						if ($row->featured) {
							$cls = 'featured';
						} else if ($row->created > Date::of(time() - (30 * 24 * 60 * 60))) {
							$cls = 'new';
						}
				?>
				<li class="cf<?php echo ($cls) ? ' ' . $cls : ''; ?>">
					<div class="grid">
						<div class="imageholder col span2">
							<?php echo \Components\Store\Helpers\Html::productimage($this->option, $row->id, $row->root, $row->webpath, $row->title, $row->category); ?>
						</div>
						<div class="infoholder col span8">
							<h4><?php echo $row->title; ?></h4>
							<p class="desc"><?php echo \Hubzero\Utility\String::truncate($row->description, 200); ?></p>
							<p>
								<?php if ($row->category ) { ?>
									<span class="details"><span><?php echo Lang::txt('COM_STORE_CATEGORY'); ?>:</span> <?php echo $row->category; ?></span>
								<?php } ?>
								<?php if ($row->size && $row->available) { ?>
									<span class="details"><span><?php echo Lang::txt('COM_STORE_SIZES'); ?>:</span> <?php echo $row->size; ?></span>
								<?php } ?>
								<?php 
								if ($row->category != 'service') {
									if ($row->available) { ?>
										<span class="yes"><?php echo Lang::txt('COM_STORE_INSTOCK'); ?></span>
									<?php } else { ?>
										<span class="no"><?php echo Lang::txt('COM_STORE_SOLDOUT'); ?></span>
									<?php 
									}
								}
								?>
							</p>
						</div>
						<div class="purchase col span2 omega">
							<span class="price"><a href="<?php echo $this->infolink; ?>" title="<?php echo Lang::txt('COM_STORE_WHAT_ARE_POINTS'); ?>" class="tooltips"><span class="points"></span></a><?php echo $row->price; ?></span>
							<?php if ($row->available) { ?>
								<span><a class="btn btn-primary" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart&action=add&item=' . $row->id); ?>" title="<?php echo Lang::txt('COM_STORE_BUY'); ?>"><?php echo ucfirst(Lang::txt('COM_STORE_BUY')); ?></a></span>
							<?php } else { ?>
								<span class="btn btn-disabled"><?php echo ucfirst(Lang::txt('COM_STORE_BUY')); ?></span>
							<?php } ?>
						</div>
					</div>
				</li>
				<?php
					}
				?>
				</ul>
				<div class="clear"></div>
			</div>
		<?php } else { ?>
			<p><?php echo Lang::txt('COM_STORE_NO_PRODUCTS'); ?></p>
		<?php } ?>
	</form><!-- / .section-inner -->
</section><!-- / .main section -->
