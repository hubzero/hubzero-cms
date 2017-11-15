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
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="grid break4">
			<div id="cartcontent" class="col span8 cf">
				<?php if ($this->getError()) { ?>
					<p class="error"><?php echo $this->getError(); ?></p>
				<?php } ?>
				<form id="storeForm" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>" class="store-wrap">
					<fieldset>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
						<input type="hidden" name="task" value="finalize" />
						<input type="hidden" name="action" value="" />
						<input type="hidden" name="name" value="<?php echo (isset($this->posted['name'])) ? $this->escape($this->posted['name']) : $this->escape(User::get('name')); ?>" />
						<input type="hidden" name="address" value="<?php echo (isset($this->posted['address'])) ? $this->escape($this->posted['address']) : ''; ?>" />
						<input type="hidden" name="address1" value="<?php echo (isset($this->posted['address1'])) ? $this->escape($this->posted['address1']) : ''; ?>" />
						<input type="hidden" name="address2" value="<?php echo (isset($this->posted['address2'])) ? $this->escape($this->posted['address2']) : ''; ?>" />
						<input type="hidden" name="city" value="<?php echo (isset($this->posted['city'])) ? $this->escape($this->posted['city']) : ''; ?>" />
						<input type="hidden" name="state" value="<?php echo (isset($this->posted['state'])) ? $this->escape($this->posted['state']) : ''; ?>" />
						<input type="hidden" name="country" value="<?php echo (isset($this->posted['country'])) ? $this->escape($this->posted['country']) : $this->escape(\Hubzero\Geocode\Geocode::getcountry($this->xprofile->get('countryresident'))); ?>" />
						<input type="hidden" name="postal" value="<?php echo (isset($this->posted['postal'])) ? $this->escape($this->posted['postal']) : ''; ?>" />
						<input type="hidden" name="phone" value="<?php echo (isset($this->posted['phone'])) ? $this->escape($this->posted['phone']) : ''; ?>" />
						<input type="hidden" name="email" value="<?php echo (isset($this->posted['email'])) ? $this->escape($this->posted['email']) : User::get('email'); ?>" />
						<input type="hidden" name="comments" value="<?php echo (isset($this->posted['comments'])) ? $this->escape($this->posted['comments']) : ''; ?>" />

						<h2><?php echo Lang::txt('COM_STORE_ORDER_WILL_SHIP'); ?></h2>
						<pre><?php echo (isset($this->posted['name'])) ? $this->escape($this->posted['name']) : $this->escape(User::get('name')); ?>

<?php echo (isset($this->posted['address'])) ? $this->escape($this->posted['address']) : ''; ?>

<?php echo (isset($this->posted['country'])) ? $this->escape($this->posted['country']) : $this->escape(\Hubzero\Geocode\Geocode::getcountry($this->xprofile->get('countryresident'))); ?>
						</pre>
					</fieldset>
					<fieldset>
						<h4><?php echo Lang::txt('COM_STORE_CONTACT_INFO'); ?></h4>
						<p>
							<?php if (isset($this->posted['phone'])) { ?>
								<?php echo Lang::txt('Phone'); ?>: <?php echo $this->posted['phone']; ?><br />
							<?php } ?>
							<?php if (isset($this->posted['email'])) { ?>
								<?php echo Lang::txt('Email'); ?>: <?php echo $this->posted['email']; ?>
							<?php } ?>
						</p>
					</fieldset>
					<p><a class="actionlink" href="javascript:void(0);" id="change_address"><?php echo Lang::txt('COM_STORE_CHANGE_ADDRESS'); ?></a></p>
					<?php if (isset($this->posted['comments']) && $this->posted['comments'] != '') { ?>
						<fieldset>
							<h4><?php echo Lang::txt('COM_STORE_ADDITIONAL_COMMENTS'); ?></h4>
							<p><?php echo $this->posted['comments']; ?></p>
						</fieldset>
					<?php } ?>
					<div class="clear"></div>
					<p class="submit">
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart&action=empty'); ?>" class="btn"><?php echo Lang::txt('COM_STORE_CANCEL_ORDER'); ?></a>
						<input type="submit" class="btn btn-primary" value="<?php echo ucfirst(Lang::txt('COM_STORE_FINALIZE')); ?>" />
					</p>
					<?php echo Html::input('token'); ?>
				</form>
			</div><!-- / #cartcontent -->

			<div id="balanceupdate" class="col span4 omega">
				<div class="order_summary">
					<h4><span class="coin">&nbsp;</span><?php echo Lang::txt('COM_STORE_ORDER_SUMMARY'); ?></h4>
					<?php foreach ($this->items as $item) { ?>
						<p>
							<?php echo \Hubzero\Utility\Str::truncate($item->title, 28); ?>
						<?php if ($item->selectedsize) { ?>
							</p>
							<p>
								<?php echo Lang::txt('COM_STORE_SIZE') . ' ' . $item->selectedsize . ' (x ' . $item->quantity . ')'; ?>
						<?php } else if ($item->category != 'service') { ?>
								(x <?php echo $item->quantity; ?>)
						<?php } ?>
							<span><?php echo ($item->price*$item->quantity); ?></span>
						</p>
					<?php } ?>
					<p><?php echo Lang::txt('COM_STORE_SHIPPING'); ?>: <span>0</span></p>
					<p class="totals"><?php echo Lang::txt('COM_STORE_TOTAL_POINTS'); ?>: <span><?php echo $this->cost; ?></span></p>
					<p><a class="actionlink" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart'); ?>"><?php echo Lang::txt('COM_STORE_CHANGE_ORDER'); ?></a></p>
				</div><!-- / .order_summary -->
				<?php if (!$this->final) { ?>
					<p class="sidenotes"><?php echo Lang::txt('COM_STORE_MSG_CHANCE_TO_REVIEW'); ?></p>
					<p class="sidenotes"><span class="sidetitle"><?php echo Lang::txt('COM_STORE_SHIPPING'); ?></span><?php echo Lang::txt('COM_STORE_MSG_SHIPPING'); ?></p>
					<p class="sidenotes"><span class="sidetitle"><?php echo Lang::txt('COM_STORE_NO_RETURNS'); ?></span><?php echo Lang::txt('COM_STORE_MSG_NO_RETURNS'); ?> <?php echo Lang::txt('COM_STORE_MSG_CONTACT_SUPPORT'); ?> <a href="<?php echo Route::url('index.php?option=com_support'); ?>"><?php echo Lang::txt('COM_STORE_SUPPORT'); ?></a>.</p>
				<?php } ?>
				<p class="sidenotes"><?php echo Lang::txt('COM_STORE_CONSULT'); ?> <a href="<?php echo Request::base(true); ?>/legal/terms"><?php echo Lang::txt('COM_STORE_TERMS'); ?></a></p>
			</div><!-- / #balanceupdate -->
		</div><!-- / .grid -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
