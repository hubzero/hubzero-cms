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
