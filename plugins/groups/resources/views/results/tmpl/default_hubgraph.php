<? defined('_JEXEC') or die(); ?>
<pre><? print_r($this); ?></pre>
<h3 class="section-header">
	<a name="resources"></a>
	<?php echo JText::_('PLG_GROUPS_RESOURCES'); ?>
</h3>

<ul id="page_options">
	<li>
		<a class="add btn" href="<?php echo JRoute::_('index.php?option=com_resources&task=draft&group=' . $this->group->get('cn')); ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_START_A_CONTRIBUTION'); ?></a>
	</li>
</ul>

<?= $this->hubgraphResponse ?>
