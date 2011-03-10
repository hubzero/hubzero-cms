<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');
//JToolBarHelper::preferences('com_joomdle', 350);
JToolBarHelper::custom( 'create_profiletype_on_moodle', 'publish', 'publish', 'CJ CREATE ON MOODLE', true, false );
JToolBarHelper::custom( 'dont_create_profiletype_on_moodle', 'unpublish', 'unpublish', 'CJ NOT CREATE ON MOODLE', true, false );



                           /*<th><?php echo JText::_('Username'); ?></th> */
?>
<form action="index.php?option=com_joomdle&view=customprofiletypes" id="adminForm" method="POST" name="adminForm">
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
                           <th width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->profiletypes); ?>)" /></th>
			   <th><?php echo JHTML::_('grid.sort',   JText::_('CJ PROFILE TYPE'), 'name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
	<!--		   <th><?php //echo JHTML::_('grid.sort',   JText::_('CJ JOOMLA FIELD'), 'joomla_field', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th> !-->
			   <th><?php echo JHTML::_('grid.sort',   JText::_('CJ CREATE ON MOODLE'), 'create_on_moodle', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
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
                    foreach ($this->profiletypes as $row){
                           $checked = JHTML::_('grid.id', $i, $row->id);
			   //$j_account      = JHTML::_('grid.published', $row, $i );
				$published      = JHTML::_('grid.published', $row, $i );


			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row->id;?></td>
                                  <td><?php  echo $checked; ?></td>
                                  <td><?php echo $row->name; ?></td>
				<td align="center"><?php echo $row->create_on_moodle ? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': 
										'<img src="images/publish_x.png" width="16" height="16" border="0" alt="" />'; ?>
				</td>
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
