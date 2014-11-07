<?php
// Initialise variables.
$limits = array();

// Make the option list.
if ($this->limits)
{
	foreach ($this->limits as $val)
	{
		$limits[] = JHtml::_('select.option', $val);
	}
}

/**
 * Method to create an active pagination link to the item
 *
 * @param   Item    $item  The object with which to make an active link.
 * @return  string  HTML link
 */
function paginator_item_active($item)
{
	if (JFactory::getApplication()->isAdmin())
	{
		return '<a title="' . $item->text . '" onclick="document.adminForm.' . $this->prefix . 'limitstart.value=' . ($item->base > 0 ? $item->base : 0) . '; Joomla.submitform();return false;">' . $item->text . '</a>';
	}
	else
	{
		return '<a title="' . $item->text . '" href="' . $item->link . '" ' . ($item->rel ? 'rel="' . $item->rel . '" ' : '') . 'class="pagenav">' . $item->text . '</a>';
	}
}
?>
<nav class="pagination">
	<ul class="list-footer">
		<li class="counter">
			<?php
			$fromResult = $this->start + 1;

			// If the limit is reached before the end of the list.
			if ($this->start + $this->limit < $this->total)
			{
				$toResult = $this->start + $this->limit;
			}
			else
			{
				$toResult = $this->total;
			}

			// If there are results found.
			if ($this->total > 0)
			{
				echo JText::sprintf('JLIB_HTML_RESULTS_OF', $fromResult, $toResult, $this->total);
			}
			else
			{
				echo JText::_('JLIB_HTML_NO_RECORDS_FOUND');
			}
			?>
		</li>
		<li class="limit">
			<label for="<?php echo $this->prefix; ?>limit"><?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?></label> 
			<?php
			// Build the select list.
			$selected = $this->viewall ? 0 : $this->limit;

			$attr = 'class="inputbox" size="1" onchange="this.form.submit()"';
			if (JFactory::getApplication()->isAdmin())
			{
				$attr = 'class="inputbox" size="1" onchange="Joomla.submitform();"';
			}

			echo JHtml::_('select.genericlist', $limits, $this->pages->prefix . 'limit', $attr, 'value', 'text', $selected);
			?>
		</li>
		<li class="pagination-start start">
			<?php if ($this->pages->start->base !== null) { ?>
				<?php echo paginator_item_active($this->pages->start); ?>
			<?php } else { ?>
				<span class="pagenav"><?php echo $this->pages->start->text; ?></span>
			<?php } ?>
		</li>
		<li class="pagination-prev prev">
			<?php if ($this->pages->previous->base !== null) { ?>
				<?php echo paginator_item_active($this->pages->previous); ?>
			<?php } else { ?>
				<span class="pagenav"><?php echo $this->pages->previous->text; ?></span>
			<?php } ?>
		</li>
		<?php if ($this->pages->ellipsis && $this->pages->i > 1) { ?>
			<li class="page"><span>...</span></li>
		<?php } ?>
		<?php
		for (; $this->pages->i <= $this->pages->stoploop && $this->pages->i <= $this->pages->total; $this->pages->i++)
		{
			if (isset($this->pages->pages[$this->pages->i]))
			{
				$page = $this->pages->pages[$this->pages->i];
				if ($page->base !== null)
				{
					?>
					<li class="page"><?php echo paginator_item_active($page); ?></li>
					<?php
				}
				else
				{
					?>
					<li class="page"><strong><?php echo $page->text; ?></strong></li>
					<?php
				}
			}
		}
		?>
		<?php if ($this->pages->ellipsis && ($this->pages->i - 1) < $this->pages->total) { ?>
			<li class="page"><span>...</span></li>
		<?php } ?>
		<li class="pagination-next next">
			<?php if ($this->pages->next->base !== null) { ?>
				<?php echo paginator_item_active($this->pages->next); ?>
			<?php } else { ?>
				<span class="pagenav"><?php echo $this->pages->next->text; ?></span>
			<?php } ?>
		</li>
		<li class="pagination-end end">
			<?php if ($this->pages->end->base !== null) { ?>
				<?php echo paginator_item_active($this->pages->end); ?>
			<?php } else { ?>
				<span class="pagenav"><?php echo $this->pages->end->text; ?></span>
			<?php } ?>
		</li>
	</ul>
	<input type="hidden" name="<?php echo $this->prefix; ?>limitstart" value="<?php echo $this->start; ?>" />
</nav>