<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php  
JHTML::_('behavior.tooltip');  
?>
<form action="index.php?option=com_jce" method="post" name="adminForm">
	<fieldset>
        <div class="configuration" >
            <?php echo JText::_( 'Icon Legend' );?>
        </div>
    </fieldset>
    <fieldset>
    <table class="adminlist">
		<thead>
			<th nowrap="nowrap"  width="20%" class="title">
				<?php echo JText::_( 'Name' ); ?>:
			</th>
            <th nowrap="nowrap" class="title">
				<?php echo JText::_( 'Icon' ); ?>:
			</th>
        </tr>
    	</thead>
        <tbody>
        <?php foreach( $this->icons as $icon ){
			$path = $icon->type == 'command' ? '../plugins/editors/jce/tiny_mce/themes/advanced/img/'. $icon->layout .'.gif' : '../plugins/editors/jce/tiny_mce/plugins/'. $icon->name .'/img/'. $icon->layout .'.gif';
       ?>     
			<tr>
            	<td width="50%"><?php echo $icon->title;?></td>
                <td width="50%"><img src="<?php echo $path;?>" alt="<?php echo $icon->title;?>" title="<?php echo $icon->title;?>" /></td>
            </tr>
        <?php }?>
        </tbody>
	</table>
    </fieldset>
	<input type="hidden" name="option" value="com_jce" />
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="type" value="group" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>