<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title .': '. ucfirst($this->resource); ?></h2>
</header>

<section class="main section">

	<form action="<?php echo Route::url($this->profile->link() . '&task=raiselimit'); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				When you have time, please leave some <a href="<?php echo Route::url('index.php?option=com_feedback'); ?>">feedback</a>. We would like to know a little more about how you are using the site so that we can make improvements for everyone.
			</p>
		</div>
		<fieldset>
	<?php if ($this->resource != 'select') { ?>
			<p>
				Please provide a short reason why you would like this increase in resources. Your
				request for additional resources will then be e-mailed to the site administrators
				who will grant your request or provide a reason why we are unable to meet your
				request at this time.
			</p>
			<div class="form-group">
				<label for="request">
					Reason for Increase:
					<textarea class="form-control" name="request" id="request" rows="6" cols="32"></textarea>
				</label>
			</div>
		</fieldset>
		<div class="clear"></div>

		<p class="submit">
			<input type="submit" name="raiselimit[<?php echo $this->resource; ?>]" value="Submit Request" />
		</p>
	<?php } else { ?>
			<h3>HUB Resources</h3>

			<table>
				<tbody>
					<?php if ($this->authorized == 'admin') { ?>
					<tr>
						<th>User Login:</th>
						<td colspan="2">
							<a href="<?php echo Route::url($this->profile->link()); ?>"><?php echo $this->escape($this->profile->get('username')); ?></a>
							<input name="login" id="login" type="hidden" value="<?php echo $this->escape($this->profile->get('username'));?>" />
						</td>
					</tr>
					<?php } ?>
					<tr>
						<th>Maximum Concurrent Sessions:</th>
						<td><?php echo $this->jobs_allowed; ?></td>
						<td><span class="submit"><input type="submit" name="raiselimit[sessions]" id="raiselimitsessions" value="<?php echo $submit_button; ?>" /></span></td>
					</tr>
					<tr>
						<th>Online Disk Storage Limit:</th>
						<td><?php echo $this->quota; ?></td>
						<td><span class="submit"><input type="submit" name="raiselimit[storage]" id="raiselimitstorage" value="<?php echo $submit_button; ?>" /></span></td>
					</tr>
					<tr>
						<th>Maximum Online Meetings:</th>
						<td><?php echo $this->max_meetings; ?></td>
						<td><span class="submit"><input type="submit" name="raiselimit[meetings]" id="raiselimitmeetings" value="<?php echo $submit_button; ?>" /></span></td>
					</tr>
				</tbody>
			</table>

			<div class="help">
				<h4>How do I get more resources?</h4>
				<p>
					Click "Increase" for the resource you wish to request more. Depending on the resource and your
					current limits, you will either be automatically granted more resources, asked to fill out some
					feedback, asked to review a resource for others, or asked to email support.
				</p>
			</div>
		</fieldset>
		<div class="clear"></div>
	<?php } ?>
	</form>
</section><!-- / .main section -->
