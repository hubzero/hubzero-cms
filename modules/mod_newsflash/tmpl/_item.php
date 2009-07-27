<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($params->get('item_title')) : ?>
<table class="contentpaneopen<?php echo $params->get( 'moduleclass_sfx' ); ?>">
<tr>
	<td class="contentheading<?php echo $params->get( 'moduleclass_sfx' ); ?>" width="100%">
	<?php if ($params->get('link_titles') && $item->linkOn != '') : ?>
		<a href="<?php echo $item->linkOn;?>" class="contentpagetitle<?php echo $params->get( 'moduleclass_sfx' ); ?>">
			<?php echo $item->title;?></a>
	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
	</td>
</tr>
</table>
<?php endif; ?>

<?php if (!$params->get('intro_only')) :
	echo $item->afterDisplayTitle;
endif; ?>

<?php echo $item->beforeDisplayContent; ?>

<table class="contentpaneopen<?php echo $params->get( 'moduleclass_sfx' ); ?>">
	<tr>
		<td valign="top" ><?php echo $item->text; ?></td>
	</tr>
	<tr>
        <td valign="top" >

       <?php if (isset($item->linkOn) && $item->readmore && $params->get('readmore')) :
	      echo '<a class="readmore" href="'.$item->linkOn.'">'.$item->linkText.'</a>';
        endif; ?>
		</td>
     </tr>
</table>
