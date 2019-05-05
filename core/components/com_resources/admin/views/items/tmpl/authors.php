<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->js('authors.js');

$authIDs = array();
?>
<label for="authid"><?php echo Lang::txt('COM_RESOURCES_AUTHID'); ?></label>
<input type="text" name="authid" id="authid" value="" />
<div class="grid">
	<div class="col span9">
<select name="authrole" id="authrole">
	<option value=""><?php echo Lang::txt('COM_RESOURCES_ROLE_AUTHOR'); ?></option>
	<?php
	if ($this->roles)
	{
		foreach ($this->roles as $role)
		{
	?>
		<option value="<?php echo $this->escape($role->alias); ?>"><?php echo $this->escape($role->title); ?></option>
	<?php
		}
	}
	?>
</select>
	</div>
	<div class="col span3">
<input type="button" name="addel" id="addel" onclick="HUB.Resources.addAuthor();" value="<?php echo Lang::txt('Add'); ?>" />
	</div>
</div>

<ul id="author-list">
	<?php
	if ($this->authnames != null)
	{
		foreach ($this->authnames as $authname)
		{
			if ($authname->name)
			{
				$name = $authname->name;
			}
			else
			{
				$name = $authname->givenName . ' ';
				if ($authname->middleName != null)
				{
					$name .= $authname->middleName . ' ';
				}
				$name .= $authname->surname;
			}

			$authIDs[] = $authname->authorid;

			$org = ($authname->organization) ? $this->escape($authname->organization) : $this->attribs->get($authname->authorid, '');
		?>
		<li id="author_<?php echo $authname->authorid; ?>">
			<span class="handle"><?php echo Lang::txt('COM_RESOURCES_AUTHOR_DRAG'); ?></span>
			<a class="state trash" data-parent="author_<?php echo $authname->authorid; ?>" href="#" onclick="HUB.Resources.removeAuthor('author_<?php echo $authname->authorid; ?>');return false;"><span><?php echo Lang::txt('JACTION_DELETE'); ?></span></a>
			<?php echo $this->escape(stripslashes($name)); ?> (<?php echo $authname->authorid; ?>)
			<br /><?php echo Lang::txt('COM_RESOURCES_AUTHOR_AFFILIATION'); ?>: <input type="text" name="<?php echo $authname->authorid; ?>_organization" value="<?php echo $org; ?>" />

			<select name="<?php echo $authname->authorid; ?>_role">
				<option value=""<?php if ($authname->role == '') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_RESOURCES_ROLE_AUTHOR'); ?></option>
				<?php
					if ($this->roles)
					{
						foreach ($this->roles as $role)
						{
				?>
							<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($authname->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
				<?php
						}
					}
				?>
			</select>
			<input type="hidden" name="<?php echo $authname->authorid; ?>_name" value="<?php echo $this->escape($name); ?>" />
		</li>
		<?php
		}
	}
	?>
</ul>
<input type="hidden" name="old_authors" id="old_authors" value="<?php echo implode(',', $authIDs); ?>" />
<input type="hidden" name="new_authors" id="new_authors" value="<?php echo implode(',', $authIDs); ?>" />
