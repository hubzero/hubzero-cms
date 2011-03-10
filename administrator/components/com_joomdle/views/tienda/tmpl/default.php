<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');
//JToolBarHelper::preferences('com_joomdle', 350);

JToolBarHelper::publishList ();
JToolBarHelper::unpublishList ();
JToolBarHelper::custom( 'reload', 'moodle', 'moodle', 'Reload from Moodle', true, false );

?>

<form action="index.php" method="POST"  id="adminForm" name="adminForm">
       <table class="adminlist">
             <thead>
                    <tr>
                           <th width="10">ID</th>
                           <th width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>)" /></th>
                           <th><?php echo JText::_('CJ COURSE'); ?></th>
                           <th><?php echo JText::_('CJ SELL ON TIENDA'); ?></th>
                    </tr>              
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->courses as $row){
                           $checked = JHTML::_('grid.id', $i, $row->id);
			   $published      = JHTML::_('grid.published', $row, $i );

			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row->id;?></td>
                                  <td><?php echo $checked; ?></td>
                                  <td><?php echo $row->fullname;?></td>
				  <td align="center"><?php echo $published; ?> </td>
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
       <?php echo JHTML::_( 'form.token' ); ?>
</form>
