<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_GROUPS'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
 </head>
 <body>
	<form action="index2.php" method="post">
		<table>
		 <tbody>
		  <tr>
		   <td>
		    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
			<input type="hidden" name="task" value="addgroup" />

			<select name="gid" style="width: 15em;">
				<option value=""><?php echo JText::_('Select...'); ?></option>
				<?php
				foreach ($this->rows as $row) 
				{
					echo '<option value="'.$row->gidNumber.'">'.$row->description.' ('.$row->cn.')</option>'."\n";
				}
				?>
			</select>
			<select name="tbl">
				<option value="invitees"><?php echo JText::_('INVITEES'); ?></option>
				<option value="applicants"><?php echo JText::_('APPLICANTS'); ?></option>
				<option value="members" selected="selected"><?php echo JText::_('MEMBERS'); ?></option>
				<option value="managers"><?php echo JText::_('MANAGERS'); ?></option>
			</select>
			<input type="submit" value="<?php echo JText::_('ADD_GROUP'); ?>" />
		   </td>
		  </tr>
		 </tbody>
		</table>
		<br />
		<table class="paramlist admintable">
			<tbody>
		<?php
		ximport('xuserhelper');
		
		$applicants = XUserHelper::getGroups( $this->id, 'applicants' );
		$invitees = XUserHelper::getGroups( $this->id, 'invitees' );
		$members = XUserHelper::getGroups( $this->id, 'members' );
		$managers = XUserHelper::getGroups( $this->id, 'managers' );

		$applicants = (is_array($applicants)) ? $applicants : array();
		$invitees = (is_array($invitees)) ? $invitees : array();
		$members = (is_array($members)) ? $members : array();
		$managers = (is_array($managers)) ? $managers : array();

		$groups = array_merge($applicants, $invitees);
		$managerids = array();
		foreach ($managers as $manager) 
		{
			$groups[] = $manager;
			$managerids[] = $manager->cn;
		}
		foreach ($members as $mem) 
		{
			if (!in_array($mem->cn,$managerids)) {
				$groups[] = $mem;
			}
		}
		
		if (count($groups) > 0) {
			foreach ($groups as $group)
			{
				?>
				<tr>
					<td class="paramlist_key"><a href="index.php?option=com_groups&amp;task=manage&amp;gid=<?php echo $group->cn; ?>" target="_parent"><?php echo $group->description.' ('.$group->cn.')'; ?></a></td>
					<td class="paramlist_value"><?php 
					$seen[] = $group->cn;
					
					if ($group->registered) {
						$status = JText::_('applicant');
						if ($group->regconfirmed) {
							$status = JText::_('member');
							if ($group->manager) {
								$status = JText::_('manager');
							}
						}
					} else {
						$status = JText::_('invitee');
					}
					echo $status; ?></td>
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
