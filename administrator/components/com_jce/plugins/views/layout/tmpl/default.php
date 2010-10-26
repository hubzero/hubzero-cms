<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::script( 'sortables.js', 'administrator/components/com_jce/js/' );?>
<?php JHTML::stylesheet( 'layout.css', 'administrator/components/com_jce/css/' );?>
<?php
	
?>
<style type="text/css">
	.editor { width: <?php echo $this->dimensions['width'];?>px; }
</style>
<form action="index.php" method="post" name="adminForm">
    <fieldset>
        <div style="float: right">
            <button type="button" onclick="submitbutton();"><?php echo JText::_( 'Save' );?></button>
            <button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_( 'Cancel' );?></button>
        </div>
        <div class="configuration" >
            <?php echo JText::_( 'JCE LAYOUT EDITOR' );?>
        </div>
    </fieldset>
    <fieldset>
    	<div><?php echo JText::_('JCE LAYOUT NOTE');?>
        </div>
    </fieldset>
    <fieldset>
        <table class="admintable">
            <tr>
                <td><?php echo JText::_('JCE LAYOUT DESC');?></td>
            </tr>
            <tr>
                <td><div class="sortableList" style="cursor:auto;">
                <?php 
                $width 	= $this->dimensions['width'] + 100;
				$sortid = array();
				for( $i=0; $i<count( $this->items ); $i++ ){
					$r = $i + 1;
					$sortid[] = "'row". $r ."'";
				?>
					<ul class="sortableRow" id="row<?php echo $r;?>" style="width:<?php echo $width;?>px">
                <?php
					foreach( $this->items[$i] as $item ){
						$n = "row_li_". $item->id;
						$path = $item->type == 'command' ? '../plugins/editors/jce/tiny_mce/themes/advanced/img/'. $item->layout .'.gif' : '../plugins/editors/jce/tiny_mce/plugins/'. $item->name .'/img/'. $item->layout .'.gif';
					?>
						<li class="sortableItem" id="<?php echo $n;?>"><img src="<?php echo $path;?>" alt="<?php echo $item->title;?>" title="<?php echo $item->title;?>" /></li>
					<?php }?>
                    </ul>
				<?php }?>
				<input type="hidden" id="row<?php echo $r;?>_out" name="row<?php echo $r;?>_out" />
            	</div></td>
            </tr>
        </table>
    </fieldset>		
	<script type="text/javascript">
        var form = document.adminForm;
        function submitbutton(){
			form.layout.value = $$('.sortableItem').map(function(el, i){
				return el.getParent().getProperty('id') + '[]=' + el.getProperty('id').replace(/[^0-9]/gi, '');
            }).join('&').replace(',', '&', 'gi');
            submitform('layoutsave');
        }
       	new Sortables('.sortableRow', {revert: true});
    </script>
    <span id="debug"></span>
    <input type="hidden" name="layout" value="" />
    <input type="hidden" name="option" value="com_jce" />
    <input type="hidden" name="type" value="plugin" />
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>