<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'COM_EVENTS' ).'</a>: <small><small>[ '.JText::_('COM_EVENTS_RESPONDANT').' ]</small></small>', 'user.png' );
//JToolBarHelper::cancel();

$resp = $this->resp;

list($resp) = $resp->getRecords();
?>
<h2><?php echo $this->escape(stripslashes($this->event->title)); ?></h2>

<table class="adminlist" summary="<?php echo JText::_('COM_EVENTS_TABLE_SUMMARY'); ?>">
	<thead>
		<tr><th colspan="2"><?php echo JText::_('COM_EVENTS_RESPONDENT_DATA'); ?></th></tr>
	</thead>
	<tbody>
		<?php if (!empty($resp->last_name) || !empty($resp->first_name)) : ?>
		<tr><td><?php echo JText::_('COM_EVENTS_NAME'); ?></td><td><?php echo $this->escape($resp->last_name) . ', ' . $this->escape($resp->first_name); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->email)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_EMAIL'); ?></td><td><a href="mailto:<?php echo $resp->email; ?>"><?php echo $this->escape($resp->email); ?></a></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->affiliation)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_AFFILIATION'); ?></td><td><?php echo $this->escape($resp->affiliation); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->title)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_TITLE'); ?></td><td><?php echo $this->escape($resp->title) . (empty($resp->position_description) ? '' : ' - ' . $this->escape($resp->position_description)); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->city) || !empty($resp->state) || !empty($resp->zip) || !empty($resp->country)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_LOCATION'); ?></td><td><?php echo $this->escape($resp->city) . ' ' . $this->escape($resp->state) . ' ' . $this->escape($resp->country) . ' ' . $this->escape($resp->zip); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->telephone) || !empty($resp->fax)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_TELEPHONE'); ?></td><td><?php echo $this->escape($resp->telephone) . (empty($resp->fax) ? '' : ' ' . $this->escape($resp->fax) . ' ('.JText::_('COM_EVENTS_FAX').')'); ?></td></tr>
		<?php endif; ?>	
		<?php if (!empty($resp->website)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_WEBSITE'); ?></td><td><?php echo $this->escape($resp->website); ?></td></tr>
		<?php endif; ?>
		<?php 
		$race = EventsRespondent::getRacialIdentification($resp->id);
		if (!empty($race)): 
		?>
		<tr><td><?php echo JText::_('COM_EVENTS_RACE'); ?></td><td><?php echo $this->escape($race); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->gender)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_GENDER'); ?></td><td><?php echo $resp->gender == 'm' ? JText::_('COM_EVENTS_MALE') : JText::_('COM_EVENTS_FEMALE'); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->arrival)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_ARRIVAL'); ?></td><td><?php echo $this->escape($resp->arrival); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->arrival)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_DEPARTURE'); ?></td><td><?php echo $this->escape($resp->departure); ?></td></tr>
		<?php endif; ?>
		<tr><td><?php echo JText::_('COM_EVENTS_DISABILITY_CONTACT_REQUESTED'); ?></td><td><?php echo $resp->disability_needs ? JText::_('COM_EVENTS_YES') : JText::_('COM_EVENTS_NO'); ?></td></tr>
		<?php if (!empty($resp->dietary_needs)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_DIETARY_RESTRICTION'); ?></td><td><?php echo $this->escape($resp->dietary_needs); ?></td></tr>
		<?php endif; ?>
		<tr><td><?php echo JText::_('COM_EVENTS_ATTENDING_DINNER'); ?></td><td><?php echo $resp->attending_dinner ? JText::_('COM_EVENTS_YES') : JText::_('COM_EVENTS_NO'); ?></td></tr>
		<?php if (!empty($resp->abstract)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_ABSTRACT'); ?></td><td><?php echo $this->escape($resp->abstract); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->comment)): ?>
		<tr><td><?php echo JText::_('COM_EVENTS_COMMENT'); ?></td><td><?php echo $this->escape($resp->comment); ?></td></tr>
		<?php endif; ?>
	</tbody>
</table>