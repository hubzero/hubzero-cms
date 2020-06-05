<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt( 'COM_EVENTS' ).': '.Lang::txt('COM_EVENTS_RESPONDANT'), 'user');
//Toolbar::cancel();

$resp = $this->resp;
?>
<h2><?php echo $this->escape(stripslashes($this->event->title)); ?></h2>

<table class="adminlist">
	<thead>
		<tr><th colspan="2"><?php echo Lang::txt('COM_EVENTS_RESPONDENT_DATA'); ?></th></tr>
	</thead>
	<tbody>
		<?php if (!empty($resp->last_name) || !empty($resp->first_name)) : ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_NAME'); ?></th>
				<td><?php echo $this->escape($resp->last_name) . ', ' . $this->escape($resp->first_name); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->email)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_EMAIL'); ?></th>
				<td><a href="mailto:<?php echo $resp->email; ?>"><?php echo $this->escape($resp->email); ?></a></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->affiliation)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_AFFILIATION'); ?></th>
				<td><?php echo $this->escape($resp->affiliation); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->title)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_TITLE'); ?></th>
				<td><?php echo $this->escape($resp->title) . (empty($resp->position_description) ? '' : ' - ' . $this->escape($resp->position_description)); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->city) || !empty($resp->state) || !empty($resp->zip) || !empty($resp->country)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_LOCATION'); ?></th>
				<td><?php echo $this->escape($resp->city) . ' ' . $this->escape($resp->state) . ' ' . $this->escape($resp->country) . ' ' . $this->escape($resp->zip); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->telephone) || !empty($resp->fax)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_TELEPHONE'); ?></th>
				<td><?php echo $this->escape($resp->telephone) . (empty($resp->fax) ? '' : ' ' . $this->escape($resp->fax) . ' ('.Lang::txt('COM_EVENTS_FAX').')'); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->website)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_WEBSITE'); ?></th>
				<td><?php echo $this->escape($resp->website); ?></td>
			</tr>
		<?php endif; ?>
		<?php
		$races = $resp->racial;
		if (count($races)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_RACE'); ?></th>
				<td>
					<?php
					$r = array();
					foreach ($races as $race)
					{
						$r[] = $this->escape($race . ($race->tribal_affiliation ? ' (' . $race->tribal_affiliation . ')' : ''));
					}
					echo implode(', ', $r);
					?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->gender)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_GENDER'); ?></th>
				<td><?php echo $resp->gender == 'm' ? Lang::txt('COM_EVENTS_RESPONDANT_MALE') : Lang::txt('COM_EVENTS_RESPONDANT_FEMALE'); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->arrival)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_ARRIVAL'); ?></th>
				<td><?php echo $this->escape($resp->arrival); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->arrival)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_DEPARTURE'); ?></th>
				<td><?php echo $this->escape($resp->departure); ?></td>
			</tr>
		<?php endif; ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_DISABILITY_CONTACT_REQUESTED'); ?></th>
				<td><?php echo $resp->disability_needs ? Lang::txt('COM_EVENTS_RESPONDANT_YES') : Lang::txt('COM_EVENTS_RESPONDANT_NO'); ?></td>
			</tr>
		<?php if (!empty($resp->dietary_needs)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_DIETARY_RESTRICTION'); ?></th>
				<td><?php echo $this->escape($resp->dietary_needs); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_ATTENDING_DINNER'); ?></th>
			<td><?php echo $resp->attending_dinner ? Lang::txt('COM_EVENTS_RESPONDANT_YES') : Lang::txt('COM_EVENTS_RESPONDANT_NO'); ?></td>
		</tr>
		<?php if (!empty($resp->abstract)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_ABSTRACT'); ?></th>
				<td><?php echo $this->escape($resp->abstract); ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($resp->comment)): ?>
			<tr>
				<th><?php echo Lang::txt('COM_EVENTS_RESPONDANT_COMMENT'); ?></th>
				<td><?php echo $this->escape($resp->comment); ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
