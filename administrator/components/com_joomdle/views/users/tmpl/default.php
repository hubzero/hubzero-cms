<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');
//JToolBarHelper::preferences('com_joomdle', 350);

JToolBarHelper::custom( 'add_to_joomla', 'joomla', 'joomla', 'Add users to Joomla', true, false );
JToolBarHelper::custom( 'add_to_moodle', 'moodle', 'moodle', 'Add users to Moodle', true, false );
JToolBarHelper::custom( 'migrate_to_joomdle', 'restore', 'restore', 'Migrate users to Joomdle', true, false );
JToolBarHelper::custom( 'sync_profile_to_moodle', 'restore', 'restore', 'Sync Moodle profiles', true, false );
JToolBarHelper::custom( 'sync_parents_from_moodle', 'restore', 'restore', 'Sync parents from Moodle', true, false );

                           /*<th><?php echo JText::_('Username'); ?></th> */
?>
<form action="index.php?option=com_joomdle&view=users"  id="adminForm" method="POST" name="adminForm">
<table>
	<tr>
		<td width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
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
                           <th width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->users); ?>)" /></th>
			   <th><?php echo JHTML::_('grid.sort',   JText::_('Username'), 'username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
			   <th><?php echo JHTML::_('grid.sort',   JText::_('Name'), 'name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
			   <th><?php echo JHTML::_('grid.sort',   JText::_('Email'), 'email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
                           <th><?php echo JText::_('CJ JOOMLA ACCOUNT'); ?></th>
                           <th><?php echo JText::_('CJ MOODLE ACCOUNT'); ?></th>
                           <th><?php echo JText::_('CJ JOOMDLE USER'); ?></th>
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
                    foreach ($this->users as $row){
                           $checked = JHTML::_('grid.id', $i, $row['id']);
			   //$j_account      = JHTML::_('grid.published', $row, $i );

			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['id'];?></td>
                                  <td><?php if (!$row['admin']) echo $checked; ?></td>
                                  <td><?php echo $row['username'];?></td>
                                  <td><?php echo $row['name']; ?></td>
                                  <td><?php echo $row['email']; ?></td>
				  <td align="center"><?php echo $row['j_account'] ? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': ''; ?></td>
				  <td align="center"><?php echo $row['m_account'] ? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': ''; ?></td>
				  <td align="center"><?php echo ($row['auth'] == 'joomdle') ? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': ''; ?></td>
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
