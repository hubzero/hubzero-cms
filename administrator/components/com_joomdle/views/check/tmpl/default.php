<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');

//print_r ($this->system_info);
?>
       <table class="adminlist">
             <thead>
                    <tr>
                           <th><?php echo JText::_('CJ CHECK'); ?></th>
                           <th><?php echo JText::_('CJ STATUS'); ?></th>
                           <th><?php echo JText::_('CJ ERROR'); ?></th>
                    </tr>
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->system_info as $row){
                           //$checked = JHTML::_('grid.id', $i, $row['id']);
                           //$j_account      = JHTML::_('grid.published', $row, $i );

                           ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['description'];?></td>
                                  <td align="center"><?php echo ($row['value'] == 1)? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': '<img src="images/cancel_f2.png" width="16" height="16" border="0" alt="" />'; ?></td>
                                  <td align="center"><?php echo $row['error']; ?> </td>
                           </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    }
                    ?>
             </tbody>
       </table>
			<center><?php echo JText::_('CJ FOR PROBLEM RESOLUTION SEE'); ?>: <a target='_blank' href="http://www.joomdle.com/wiki/System_health_check">http://www.joomdle.com/wiki/System_health_check</a></center>
