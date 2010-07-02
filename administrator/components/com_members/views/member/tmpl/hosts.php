<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_HOSTS'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
 </head>
 <body>
	<form action="index.php" method="post">
		<table>
		 <tbody>
		  <tr>
		   <td>
		    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
			<input type="hidden" name="task" value="addhost" />

			<input type="text" name="host" value="" /> 
			<input type="submit" value="<?php echo JText::_('ADD_HOST'); ?>" />
		   </td>
		  </tr>
		 </tbody>
		</table>
		<br />
		<table class="paramlist admintable">
			<tbody>
		<?php
		if (count($this->rows) > 0) {
			foreach ($this->rows as $row)
			{
				?>
				<tr>
					<td class="paramlist_key"><?php echo $row; ?></td>
					<td class="paramlist_value"><a href="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=deletehost&amp;host=<?php echo $row; ?>&amp;id=<?php echo $this->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1"><?php echo JText::_('DELETE'); ?></a></td>
				</tr>
				<?php
			}
		}
		?>
			</tbody>
		</table>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
 </body>
</html>