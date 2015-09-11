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
	<div id="content-header-extra">
		<p>
			<a class="icon-prev btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_STORE_BACK_STOREFRONT'); ?></a>
			<a class="icon-basket btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart'); ?>"><?php echo Lang::txt('COM_STORE_CART'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<div class="section-inner">
		<div class="grid break4">
			<div id="cartcontent" class="col span8 cf">
				<?php if ($this->getError()) { ?>
					<p class="error"><?php echo $this->getError(); ?></p>
				<?php } ?>
				<form id="hubForm" method="post" class="full" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="process" />

					<fieldset>
						<legend><?php echo Lang::txt('COM_STORE_SHIPPING_ADDRESS'); ?></legend>

						<label for="name">
							<?php echo Lang::txt('COM_STORE_RECEIVER_NAME'); ?> <span class="required"><?php echo Lang::txt('COM_STORE_REQUIRED'); ?></span>
							<input name="name" id="name" type="text" value="<?php echo (isset($this->posted['name'])) ? $this->escape($this->posted['name']) : $this->escape(User::get('name')); ?>" />
						</label>

						<label for="address">
							<?php echo Lang::txt('COM_STORE_COMPLETE_ADDRESS'); ?> <span class="required"><?php echo Lang::txt('COM_STORE_REQUIRED'); ?></span>
							<textarea name="address" id="address" rows="10" cols="50"><?php echo (isset($this->posted['address'])) ? $this->escape($this->posted['address']) : ''; ?></textarea>
						</label>

						<p class="hint"><?php echo Lang::txt('COM_STORE_ADDRESS_MSG'); ?></p>

						<label for="country">
							<?php echo Lang::txt('COM_STORE_COUNTRY'); ?> <span class="required"><?php echo Lang::txt('COM_STORE_REQUIRED'); ?></span>
							<select name="country" id="country" class="input-select">
								<option value=""><?php echo Lang::txt('(select from list)'); ?></option>
								<?php 
								$countries = \Hubzero\Geocode\Geocode::countries();
								$mycountry = (isset($this->posted['country'])) ? $this->posted['country'] : \Hubzero\Geocode\Geocode::getcountry($this->xprofile->get('countryresident'));
								foreach ($countries as $country)
								{
									?>
									<option value="<?php echo $this->escape($country->name); ?>"<?php echo ($country->name == $mycountry) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($country->name); ?></option>
									<?php
								}
								?>
							</select>
						</label>
					</fieldset>
					<fieldset>
						<legend><?php echo Lang::txt('COM_STORE_CONTACT_INFO'); ?></legend>

						<label for="phone">
							<?php echo Lang::txt('COM_STORE_CONTACT_PHONE'); ?>
							<input name="phone" id="phone" type="text" value="<?php echo (isset($this->posted['phone'])) ? $this->escape($this->posted['phone']) : $this->escape(User::get('phone')); ?>" />
						</label>

						<label for="email">
							<?php echo Lang::txt('COM_STORE_CONTACT_EMAIL'); ?>
							<input name="email" id="email" type="text" value="<?php echo (isset($this->posted['email'])) ? $this->escape($this->posted['email']) : $this->escape(User::get('email')); ?>" />
						</label>
						<p class="hint"><?php echo Lang::txt('COM_STORE_CONTACT_MSG'); ?></p>
					</fieldset>
					<fieldset>
						<legend><?php echo Lang::txt('COM_STORE_ADDITIONAL_COMMENTS'); ?></legend>

						<label for="comments">
							<?php echo Lang::txt('COM_STORE_DETAILS'); ?>
							<textarea name="comments" id="comments" rows="10" cols="50"><?php echo (isset($this->posted['comments'])) ? $this->escape($this->posted['comments']) : ''; ?></textarea>
						</label>
					</fieldset>
					<p class="submit">
						<span class="reassure">(<?php echo Lang::txt('COM_STORE_NOTE_NOCHARGE'); ?>)</span>
						<input type="submit" class="btn btn-primary" value="<?php echo ucfirst(Lang::txt('COM_STORE_CONFIRM')); ?>" />
					</p>
					<?php echo Html::input('token'); ?>
				</form>
			</div>

			<div id="balanceupdate" class="col span4 omega">
				<div class="order_summary">
					<h4><span class="coin">&nbsp;</span><?php echo Lang::txt('COM_STORE_ORDER_SUMMARY'); ?></h4>
					<?php foreach ($this->items as $item) { ?>
						<p>
							<?php echo \Hubzero\Utility\String::truncate($item->title, 60); ?>
					<?php if ($item->selectedsize) { ?>
						</p>
						<p>
							<?php echo Lang::txt('COM_STORE_SIZE') . ' ' . $item->selectedsize . '&nbsp;(x ' . $item->quantity . ')'; ?>
					<?php } else if ($item->category != 'service') { ?>
							&nbsp;(x <?php echo $item->quantity; ?>)
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
				<p class="sidenotes"><?php echo Lang::txt('COM_STORE_CONSULT'); ?> <a href="/legal/terms"><?php echo Lang::txt('COM_STORE_TERMS'); ?></a></p>
			</div><!-- / #balanceupdate -->
		</div><!-- / .grid -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
