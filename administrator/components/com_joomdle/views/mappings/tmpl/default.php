<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');
//JToolBarHelper::preferences('com_joomdle', 350);

JToolBarHelper::addNew('new_mapping');
JToolBarHelper::trash('delete_mappings');

                           /*<th><?php echo JText::_('Username'); ?></th> */
?>
<form action="index.php?option=com_joomdle&view=mappings" method="POST"  id="adminForm" name="adminForm">
<table>
	<tr>
		<td width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('joomla_app').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
<?php echo $this->lists['type'];?>
		</td>
	</tr>
</table>

       <table class="adminlist">
             <thead>
                    <tr>
                           <th width="10">ID</th>
                           <th width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->mappings); ?>)" /></th>
			   <th><?php echo JHTML::_('grid.sort',   JText::_('CJ JOOMLA COMPONENT'), 'joomla_app', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
	<!--		   <th><?php //echo JHTML::_('grid.sort',   JText::_('CJ JOOMLA FIELD'), 'joomla_field', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th> !-->
			   <th><?php echo JText::_('CJ JOOMLA FIELD'); ?></th>
			   <th><?php echo JHTML::_('grid.sort',   JText::_('CJ MOODLE FIELD'), 'moodle_field', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
                    </tr>              
             </thead>
		<tfoot>
                        <tr>
                                <td colspan="10">
                                        <?php echo $this->pagination->getListFooter(); ?>
                                </td>
                        </tr>
                </tfoot>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->mappings as $row){
                           $checked = JHTML::_('grid.id', $i, $row['id']);
			   //$j_account      = JHTML::_('grid.published', $row, $i );

			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['id'];?></td>
                                  <td><?php  echo $checked; ?></td>
                                  <td><?php echo $row['joomla_app'];?></td>
                                  <td><a href='index.php?option=com_joomdle&view=mappings&task=edit&mapping_id=<?php echo $row['id'];?>'><?php echo $row['joomla_field_name']; ?></a></td>
                                  <td><a href='index.php?option=com_joomdle&view=mappings&task=edit&mapping_id=<?php echo $row['id'];?>'><?php echo $row['moodle_field']; ?></a></td>
                           </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    }
                    ?>
             </tbody>
       </table>
      
       <input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' );?>"/>
       <input type="hidden" name="task" value=""/>
       <input type="hidden" name="boxchecked" value="0"/>   
       <input type="hidden" name="hidemainmenu" value="0"/> 
        <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
       <?php echo JHTML::_( 'form.token' ); ?>
</form>
