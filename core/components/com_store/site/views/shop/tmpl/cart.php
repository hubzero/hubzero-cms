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
			<a class="icon-prev btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_STORE_STOREFRONT'); ?></a>
			<a class="icon-points btn" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=points'); ?>"><?php echo Lang::txt('COM_STORE_MY_POINTS'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<?php if ($this->msg) { ?>
			<p class="passed"><?php echo $this->msg; ?></p>
		<?php } ?>
		<div class="grid break4">
			<div id="cartcontent" class="col span8">

			<?php if ($this->rows) { ?>
				<p>
					<?php echo Lang::txt('COM_STORE_THERE_ARE_ITEMS_IN_CART', count($this->rows)); ?>
					&nbsp;<a href="<?php echo Route::url('index.php?option=' . $this->option); ?>" ><?php echo Lang::txt('COM_STORE_CONTINUE'); ?></a>
				</p>

				<form id="myCart" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart'); ?>" class="store-wrap">
					<input type="hidden" name="action" value="" />
					<input type="hidden" name="task" value="checkout" />
					<input type="hidden" name="funds" id="funds" value="<?php echo $this->funds; ?>" />
					<input type="hidden" name="cost" id="cost" value="<?php echo $this->cost; ?>" />
					<?php echo Html::input('token'); ?>

					<table id="tktlist">
						<thead>
							<tr>
								<th><?php echo ucfirst(Lang::txt('COM_STORE_ITEM')); ?></th>
								<th><?php echo Lang::txt('COM_STORE_AVAILABILITY'); ?></th>
								<th><?php echo Lang::txt('COM_STORE_QUANTITY'); ?>*</th>
								<th><?php echo Lang::txt('COM_STORE_SIZE'); ?></th>
								<th><a href="<?php echo $this->infolink; ?>" title="<?php echo Lang::txt('COM_STORE_WHAT_ARE_POINTS'); ?>" class="tooltips points"></a></th>
							</tr>
						</thead>
						<tbody>
						<?php
							$total = 0;
							foreach ($this->rows as $row)
							{
								$price = $row->price*$row->quantity;
								if ($row->available)
								{ // do not add if not available
									$total = $total+$price;
								}
								$sizes = array(); // build size options
								if ($row->sizes && count($row->sizes) > 0)
								{
									foreach ($row->sizes as $rs)
									{
										if (trim($rs) != '')
										{
											$sizes[$rs] = $rs;
										}
									}
									$selectedsize = ($row->selectedsize) ? $row->selectedsize : $row->sizes[0];
								}
						?>
							<tr>
								<td><?php echo $this->escape($row->title); ?></td>
								<td>
								<?php
								if ($row->category!='service') {
									if ($row->available) {
								?>
									<span class="yes"><?php echo Lang::txt('COM_STORE_INSTOCK'); ?></span>
								<?php } else { ?>
									<span class="no"><?php echo Lang::txt('COM_STORE_SOLDOUT'); ?></span>
								<?php
									}
								}
								?>
								</td>
								<td class="quantityspecs">
								<?php if ($row->category!='service') { ?>
									<input type="text" name="num<?php echo $row->itemid; ?>" id="num<?php echo $row->itemid; ?>" value="<?php echo $row->quantity; ?>" size="1" maxlength = "1" class="quantity" />
								<?php } else { ?>
									1 
								<?php } ?>
									<span class="removeitem"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart&action=remove&item=' . $row->itemid); ?>" class="tooltips" title="<?php echo Lang::txt('COM_STORE_REMOVE_FROM_CART'); ?>"></a></span>
								</td>
								<td>
								<?php if (count($sizes)>0) { ?>
									<select name="size<?php echo $row->itemid; ?>" id="size<?php echo $row->itemid; ?>">
									<?php foreach ($sizes as $anode) { ?>
										<option value="<?php echo $this->escape(stripslashes($anode)); ?>"<?php echo ($anode == $selectedsize) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($anode)); ?></option>
									<?php } ?>
									</select>
								<?php } else { ?>
									N/A
								<?php } ?>
								</td>
								<td><?php echo $price; ?></td>
							</tr>
						<?php
							}
						?>
							<tr class="totals">
								<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=cart&action=empty'); ?>" class="actionlink" title="<?php echo Lang::txt('COM_STORE_EMPTY_CART'); ?>"><?php echo Lang::txt('COM_STORE_EMPTY_CART'); ?></a></td>
								<td></td>
								<td><a href="javascript:void(0);" class="actionlink" id="updatecart" title="<?php echo Lang::txt('COM_STORE_TITLE_UPDATE'); ?>"><?php echo Lang::txt('COM_STORE_UPDATE'); ?></a></td>
								<td><?php echo Lang::txt('COM_STORE_TOTAL'); ?></td>
								<td><?php echo $total; ?></td>
							</tr>
						</tbody>
					</table>

					<p class="process">
					<?php if ($this->funds >= $total) { ?>
						<span class="reassure">(<?php echo Lang::txt('COM_STORE_NOTE_NOCHARGE'); ?>)</span>
						<input type="submit" class="btn btn-primary" value="<?php echo ucfirst(Lang::txt('COM_STORE_CHECKOUT')); ?>" />
					<?php } else { ?>
						<span class="btn btn-disabled"><?php echo ucfirst(Lang::txt('COM_STORE_CHECKOUT')); ?></span>
					<?php } ?>
					</p>
				</form>

				<div class="footernotes">
					<p>* <?php echo Lang::txt('COM_STORE_CART_NOTES'); ?></p>
				</div>
			<?php } else { ?>
				<p><?php echo Lang::txt('COM_STORE_CART_IS_EMPTY'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option); ?>" ><?php echo Lang::txt('COM_STORE_START_SHOPPING'); ?></a>.</p>
			<?php } ?>
			</div><!-- / #cartcontent -->

			<div id="balanceupdate" class="col span4 omega">
				<p class="point-balance"><small><?php echo Lang::txt('COM_STORE_YOU_HAVE') . '</small> ' . $this->funds . '<small> '.Lang::txt('COM_STORE_POINTS') . ' ' . Lang::txt('COM_STORE_TO_SPEND'); ?></small></p>
			<?php if ($this->funds < $this->cost && $this->cost != 0) { ?>
				<p class="error"><?php echo Lang::txt('COM_STORE_MSG_NO_FUNDS') . ' ' . Lang::txt('COM_STORE_LEARN_HOW') . ' <a href="' . $this->infolink . '">' . strtolower(Lang::txt('COM_STORE_EARN')) . '</a>'; ?></p>
			<?php } ?>
			</div>
		</div><!-- / .grid -->	
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
