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

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if (User::isGuest()) { ?>
			<li><?php echo Lang::txt('COM_JOBS_PLEASE') . ' <a href="' . Route::url('index.php?option=' . $this->option . '&task=view&action=login') . '">' . Lang::txt('COM_JOBS_ACTION_LOGIN') . '</a> ' . Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
		<?php } else if ($this->emp && $this->config->get('allowsubscriptions', 0)) {  ?>
			<li><a class="myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
			<li><a class="shortlist btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes') . '?filterby=shortlisted'; ?>"><?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?></a></li>
		<?php } else if ($this->admin) { ?>
			<li><?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
				<a class="myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?></a></li>
		<?php } else { ?>
			<li><a class="myresume btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addresume'); ?>"><?php echo Lang::txt('COM_JOBS_MY_RESUME'); ?></a></li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=confirm'); ?>" method="post" id="hubForm">
		<div class="explaination">
				<p><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_HINT_EMPLOYER_INFO') ?></p>
		</div>
		<fieldset id="subForm">
			<legend><?php echo Lang::txt('COM_JOBS_SUBSCRIPTION_EMPLOYER_INFORMATION'); ?></legend>

			<label for="companyName">
				<?php echo Lang::txt( 'COM_JOBS_EMPLOYER_COMPANY_NAME' ); ?>:
				<span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
				<input class="inputbox" type="text" id="companyName" name="companyName" size="50" maxlength="100" value="<?php echo $this->escape($this->employer->companyName); ?>" />
			</label>
			<label for="companyLocation">
				<?php echo Lang::txt( 'COM_JOBS_EMPLOYER_COMPANY_LOCATION' ); ?>:
				<span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
				<input class="inputbox" type="text" id="companyLocation" name="companyLocation" size="50" maxlength="200" value="<?php echo $this->escape($this->employer->companyLocation); ?>" />
			</label>
			<label for="companyWebsite">
				<?php echo Lang::txt( 'COM_JOBS_EMPLOYER_COMPANY_WEBSITE' ); ?>:
				<input class="inputbox" type="text" id="companyWebsite" name="companyWebsite" size="50" maxlength="200" value="<?php echo $this->escape($this->employer->companyWebsite); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>

		<div class="explaination">
			<p><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_HINT_PICK') ?></p>
			<h4><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_NEXT_STEP') ?></h4>
			<p><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_HINT_PAYMENT') ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_JOBS_SUBSCRIPTION_DETAILS'); ?></legend>

			<label>
				<?php echo Lang::txt( 'COM_JOBS_SUBSCRIBE_SELECT_SERVICE' ); ?>:
				<span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
			</label>
			<?php
				$html = '';
				$now = Date::toSql();
				for ($i=0, $n=count( $this->services ); $i < $n; $i++)  {
					// do we have an active subscription?
					$thissub = ($this->services[$i]->id == $this->subscription->serviceid) ? 1 : 0;

					// Determine expiration date
					if ($thissub) {
						$length = $this->subscription->status==0 ? $this->subscription->pendingunits : $this->subscription->units;
						$expires  = $this->subscription->expires > $now && $this->subscription->status==1 ?  '<p class="yes">' : '<p class="no">';
						$expires .= Lang::txt( 'COM_JOBS_YOUR' ) . ' ' . $length . '-' . $this->services[$i]->unitmeasure . ' ' . Lang::txt( 'COM_JOBS_SUBSCRIPTION' ) . ' ';
						if ($this->subscription->status==1) {
							$expires .= $this->subscription->expires > $now ? strtolower(Lang::txt( 'COM_JOBS_SUBSCRIPTION_STATUS_EXPIRES' )) : strtolower(Lang::txt( 'COM_JOBS_SUBSCRIPTION_STATUS_EXPIRED' )) ;
							$expires .= ' ' . Lang::txt( 'COM_JOBS_ON' ) . ' '.Date::of($this->subscription->expires)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '.';
						}
						else {
							$expires .= Lang::txt( 'COM_JOBS_SUBSCRIPTION_IS_PENDING' ) ;
						}

						$expires .= '</p>' . "\n";
						$expires .= $this->subscription->expires > $now ? ' <a href="' . Route::url('index.php?option=' . $this->option . '&task=cancel&uid=' . $this->uid) . '" class="cancelit" id="showconfirm">[ ' . Lang::txt( 'COM_JOBS_SUBSCRIPTION_CANCEL_THIS' ) . ' ]</a>' : '';
						$expires .= $this->subscription->pendingunits > 0 && $this->subscription->status==1  ? '<p class="no">' . Lang::txt( 'COM_JOBS_SUBSCRIPTION_EXTEND_REQUEST_PENDING' ) . '</p>' :'';

					}

					$units_select = array();
					$numunits = $this->services[$i]->maxunits / $this->services[$i]->unitsize;
					$unitsize = $this->services[$i]->unitsize;

					if ($thissub)
					{
						$units_select[0] = 0;
					}
					for ($p=1; $p <= $numunits; $p++)
					{
						$units_select[$unitsize] = $unitsize;
						$unitsize = $unitsize + $this->services[$i]->unitsize;
					}

					$unitsChoice = \Components\Jobs\Helpers\Html::formSelect('units_' . $this->services[$i]->id, $units_select, '', "option units");
					$iniprice = $thissub ? 0 : $this->services[$i]->unitprice;
					?>
					<div class="bindtogether product">
					<input class="option service" type="radio" name="serviceid" id="service_<?php echo $this->services[$i]->id; ?>" value="<?php echo $this->services[$i]->id; ?>"
					<?php if ($thissub or ($this->subscription->serviceid==0 && $i==0)) {
						echo 'checked="checked"';
					}
					echo $this->services[$i]->title . ' - '; ?>
					<span class="priceline"><?php echo $this->services[$i]->currency . ' ' . $this->services[$i]->unitprice.'  ' . Lang::txt( 'COM_JOBS_PER' ) . ' ' . $this->services[$i]->unitmeasure; ?></span>
					<span><?php echo $this->services[$i]->description; ?></span>

					<div class="subdetails" id="plan_<?php echo $this->services[$i]->id; ?>">
					<?php if ($thissub) {
						echo  $expires;
					} else {
						echo '';
					}
					if ($thissub or ($this->subscription->serviceid==0 && $i==0)) {
						\Components\Jobs\Helpers\Html::confirmscreen(Route::url('index.php?option=' . $this->option . '&task=dashboard&uid=' . $this->uid), Route::url('index.php?option=' . $this->option . '&task=cancel&uid=' . $this->uid));
					}
					?>
					<label>
					<?php if ($thissub) { 
						echo Lang::txt('COM_JOBS_SUBSCRIPTION_EXTEND_OR_RENEW'); 
					} else {
						echo Lang::txt('COM_JOBS_ACTION_SIGN_UP');
					}
					echo ' ' . Lang::txt('for') . ' ';
					echo $unitsChoice;
					echo $this->services[$i]->unitmeasure; ?>
					(s) </label>
					<span class="totalprice"><?php echo Lang::txt( 'COM_JOBS_SUBSCRIBE_YOUR_TOTAL' ) . ' ';
					if ($thissub) {
						echo strtolower(Lang::txt( 'COM_JOBS_NEW' )) . ' ';
					} else {
						echo '';
					}
					echo Lang::txt('COM_JOBS_SUBSCRIBE_PAYMENT_WILL_BE'); ?>
					<span class="no"><?php echo $this->services[$i]->currency; ?></span> 
					<span id="injecttotal_<?php echo $this->services[$i]->id; ?>"><?php echo $iniprice; ?></span>
					</span>

					<!-- GOOGLE Checkout (TBD) -->
					<input type="hidden" class="product-price" value="<?php echo $this->escape($this->services[$i]->unitprice); ?>" />
					<input type="hidden" class="product-title" value="<?php echo $this->escape($this->services[$i]->title); ?>" />

					</div>
					</div>
					<input type="hidden" name="price_<?php echo $this->services[$i]->id; ?>" id="price_<?php echo $this->services[$i]->id; ?>" value="<?php echo $this->escape($this->services[$i]->unitprice); ?>" />
				<?php }	
				$btn = $this->subscription->id ? Lang::txt( 'COM_JOBS_SUBSCRIPTION_SAVE' ) : Lang::txt( 'COM_JOBS_SUBSCRIPTION_PROCESS_ORDER' );
			?>
			<label for="contact">
				<?php echo Lang::txt( 'COM_JOBS_SUBSCRIPTION_CONTACT_PHONE' ) . ': <span class="required">' . Lang::txt( 'COM_JOBS_REQUIRED_WITH_PAYMENT' ) . '</span>'; ?>
				<input class="inputbox" type="text" id="contact" name="contact" size="50" maxlength="15" value="<?php echo $this->escape($this->subscription->contact); ?>" />
			</label>

			<div class="submitblock">
				<input type="hidden" name="subid" value="<?php echo $this->employer->subscriptionid; ?>" />
				<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
				<input type="submit" class="option" value="<?php echo $btn; ?>" />
			</div>
		</fieldset>
	</form>
	<div class="clear"></div>
</section>
