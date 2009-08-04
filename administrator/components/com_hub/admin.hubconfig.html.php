<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class HubConfigHTML 
{
	public function site( $arr ) 
	{
	?>
			<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;

				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}

				submitform( pressbutton );
			}
			</script>

			<form action="index.php" method="post" name="adminForm">
				<div class="col width-50">
				<fieldset class="adminform">
					<legend>Site Settings</legend>
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Short Name
									</span>
								</td>
								<td>
									<input class="text_area" type="text" name="settings[hubShortName]" size="30" value="<?php echo (isset($arr['hubShortName'])) ? $arr['hubShortName'] : ''; ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Short URL
									</span>
								</td>
								<td>
									<input class="text_area" type="text" name="settings[hubShortURL]" size="30" value="<?php echo (isset($arr['hubShortURL'])) ? $arr['hubShortURL'] : ''; ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Long URL
									</span>
								</td>
								<td>
									<input class="text_area" type="text" name="settings[hubLongURL]" size="30" value="<?php echo (isset($arr['hubLongURL'])) ? $arr['hubLongURL'] : ''; ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Support Email
									</span>
								</td>
								<td>
									<input class="text_area" type="text" name="settings[hubSupportEmail]" size="30" value="<?php echo (isset($arr['hubSupportEmail'])) ? $arr['hubSupportEmail'] : ''; ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Monitor Email
									</span>
								</td>
								<td>
									<input class="text_area" type="text" name="settings[hubMonitorEmail]" size="30" value="<?php echo (isset($arr['hubMonitorEmail'])) ? $arr['hubMonitorEmail'] : ''; ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Home Dir
									</span>
								</td>
								<td>
									<input class="text_area" type="text" name="settings[hubHomeDir]" size="30" value="<?php echo (isset($arr['hubHomeDir'])) ? $arr['hubHomeDir'] : ''; ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Site Images Dir
									</span>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
										Site Images Path
									</span>
								</td>
							</tr>
							
							<?php
							for ($i=1, $n=11; $i < $n; $i++) 
							{
								?>
								<tr>
									<td class="key">
										<span class="editlinktip hasTip" title="Focus Area::Enter a tag that represents one of the primary focus areas of your site.">
											Focus Area (tag) <?php echo $i; ?>
										</span>
									</td>
									<td>
										<input class="text_area" type="text" name="settings[hubFocusArea<?php echo $i; ?>]" size="30" value="<?php echo (isset($arr['hubFocusArea'.$i])) ? $arr['hubFocusArea'.$i] : ''; ?>" />
									</td>
								</tr>
								<?php
							}
							?>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="Login Redirect::The page you wish to redirect users to after they login.">
										Login Redirect
									</span>
								</td>
								<td>
									<input class="text_area" type="text" name="settings[hubLoginReturn]" size="30" value="<?php echo (isset($arr['hubLoginReturn'])) ? $arr['hubLoginReturn'] : ''; ?>" />
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>Forge settings</legend>
					<table class="admintable">
						<tbody>
<?php
	foreach ($arr as $field => $value) 
	{
		if (substr($field, 0, strlen('forge')) == 'forge') {
			?>
			<tr>
				<td class="key"><?php echo str_replace('forge', '', $field); ?></td>
				<td>
					<input class="text_area" type="text" name="settings[<?php echo $field; ?>]" size="30" value="<?php echo $value; ?>" />
				</td>
			</tr>
			<?php
		}	
	}
?>
						</tbody>
					</table>
				</fieldset>
				</div>
				<div class="col width-50">
					<fieldset class="adminform">
						<legend>LDAP Settings</legend>
						<table class="admintable">
							<tbody>
	<?php
		foreach ($arr as $field => $value) 
		{
			if (substr($field, 0, strlen('hubLDAP')) == 'hubLDAP') {
				?>
				<tr>
					<td class="key"><?php echo str_replace('hubLDAP', '', $field); ?></td>
					<td>
						<input class="text_area" type="text" name="settings[<?php echo $field; ?>]" size="30" value="<?php echo $value; ?>" />
					</td>
				</tr>
				<?php
			}	
		}
	?>
							</tbody>
						</table>
					</fieldset>
				</div>
				<div class="clr"></div>
				
				<input type="hidden" name="option" value="com_hub" />
				<input type="hidden" name="task" value="savesite" />
			</form>
	<?php
	}
	
	//-----------
	
	public function databases( $arr ) 
	{
	?>
			<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;

				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}

				submitform( pressbutton );
			}
			</script>

			<form action="index.php" method="post" name="adminForm">
				<div class="col width-50">
					<fieldset class="adminform">
						<legend>IP Database</legend>
						<table class="admintable">
							<tbody>
								<?php
								foreach ($arr as $field => $value) 
								{
									if (substr($field, 0, strlen('ipDB')) == 'ipDB') {
										?>
								<tr>
									<td class="key"><?php echo str_replace('ipDB', '', $field); ?></td>
									<td>
										<input class="text_area" type="text" name="settings[<?php echo $field; ?>]" size="30" value="<?php echo $value; ?>" />
									</td>
								</tr>
								<?php
									}	
								}
								?>
							</tbody>
						</table>
					</fieldset>
					<fieldset class="adminform">
						<legend>Middleware Database</legend>
						<table class="admintable">
							<tbody>
								<?php
								foreach ($arr as $field => $value) 
								{
									if (substr($field, 0, strlen('mwDB')) == 'mwDB') {
										?>
								<tr>
									<td class="key"><?php echo str_replace('mwDB', '', $field); ?></td>
									<td>
										<input class="text_area" type="text" name="settings[<?php echo $field; ?>]" size="30" value="<?php echo $value; ?>" />
									</td>
								</tr>
								<?php
									}	
								}
								?>
							</tbody>
						</table>
					</fieldset>
				</div>
				<div class="clr"></div>
				
				<input type="hidden" name="option" value="com_hub" />
				<input type="hidden" name="task" value="savedb" />
			</form>
	<?php
	}
	
	//-----------
	
	public function registration( $arr ) 
	{
	?>
			<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;

				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}

				submitform( pressbutton );
			}
			</script>

			<form action="index.php" method="post" name="adminForm">
				<fieldset class="adminform">
					<table class="admintable">
						<thead>
							<tr>
								<th>Field/Area</th>
								<th>Create</th>
								<th>Proxy</th>
								<th>Update</th>
								<th>Edit</th>
							</tr>
						</thead>
						<tbody>
<?php
	foreach ($arr as $field => $value) 
	{
		if (substr($field, 0, strlen('registration')) == 'registration') {
			$create = strtoupper(substr($value, 0, 1));
			$proxy  = strtoupper(substr($value, 1, 1));
			$update = strtoupper(substr($value, 2, 1));
			$edit   = strtoupper(substr($value, 3, 1));

			$field = str_replace('registration', '', $field);
			?>
			<tr>
				<td class="key"><?php echo $field; ?></td>
				<td>
					<select name="settings[<?php echo $field; ?>][create]">
						<option value="O"<?php if ($create == 'O') { echo ' selected="selected"'; }?>>Optional</option>
						<option value="R"<?php if ($create == 'R') { echo ' selected="selected"'; }?>>Required</option>
						<option value="H"<?php if ($create == 'H') { echo ' selected="selected"'; }?>>Hide</option>
						<option value="U"<?php if ($create == 'U') { echo ' selected="selected"'; }?>>Read only</option>
					</select>
				</td>
				<td>
					<select name="settings[<?php echo $field; ?>][proxy]">
						<option value="O"<?php if ($proxy == 'O') { echo ' selected="selected"'; }?>>Optional</option>
						<option value="R"<?php if ($proxy == 'R') { echo ' selected="selected"'; }?>>Required</option>
						<option value="H"<?php if ($proxy == 'H') { echo ' selected="selected"'; }?>>Hide</option>
						<option value="U"<?php if ($proxy == 'U') { echo ' selected="selected"'; }?>>Read only</option>
					</select>
				</td>
				<td>
					<select name="settings[<?php echo $field; ?>][update]">
						<option value="O"<?php if ($update == 'O') { echo ' selected="selected"'; }?>>Optional</option>
						<option value="R"<?php if ($update == 'R') { echo ' selected="selected"'; }?>>Required</option>
						<option value="H"<?php if ($update == 'H') { echo ' selected="selected"'; }?>>Hide</option>
						<option value="U"<?php if ($update == 'U') { echo ' selected="selected"'; }?>>Read only</option>
					</select>
				</td>
				<td>
					<select name="settings[<?php echo $field; ?>][edit]">
						<option value="O"<?php if ($edit == 'O') { echo ' selected="selected"'; }?>>Optional</option>
						<option value="R"<?php if ($edit == 'R') { echo ' selected="selected"'; }?>>Required</option>
						<option value="H"<?php if ($edit == 'H') { echo ' selected="selected"'; }?>>Hide</option>
						<option value="U"<?php if ($edit == 'U') { echo ' selected="selected"'; }?>>Read only</option>
					</select>
				</td>
			</tr>
			<?php
		}	
	}
?>
						</tbody>
					</table>
					<input type="hidden" name="option" value="com_hub" />
					<input type="hidden" name="task" value="savereg" />
				</fieldset>
			</form>
	<?php
	}
	
	//-----------
	
	public function components( $components, $option, $component, $msg='' )
	{
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Edit Preferences') );
		JHTML::_('behavior.tooltip');
	?>
<?php if ($msg) { ?>
	<dl id="system-message">
		<dt class="message">Message</dt>
		<dd class="message message fade">
			<ul>
				<li><?php echo $msg; ?></li>
			</ul>
		</dd>
	</dl>
<?php } ?>
		<form action="index.php" method="post" name="adminForm" autocomplete="off">
			<div class="col width-30">
				<fieldset class="adminform">
					<h3><?php echo JText::_('HUB Components'); ?></h3>
					<ul>
						<?php
						foreach ($components as $com) 
						{
							echo '<li><a href="index.php?option='.$option.a.'task=components'.a.'component='.$com.'">'.$com.'</a></li>'.n;
						}
						?>
					</ul>
				</fieldset>
			</div>
			<div class="col width-70">
				<fieldset class="adminform">
					<div class="configuration">
						<?php echo JText::_($component->name) ?>
					</div>
				</fieldset>

				<fieldset class="adminform">
					<legend>
						<?php echo JText::_( 'Configuration' );?>
					</legend>
					<?php
					$path = JPATH_ADMINISTRATOR.DS.'components'.DS.$component->option.DS.'config.xml';
					if (is_file($path)) {
						$params =& new JParameter( $component->params, $path );
						echo $params->render();
					} else {
						echo '<p>'.JText::_('No parameters to render').'</p>';
					}
					?>
				</fieldset>
			</div>
			<div class="clr"></div>

			<input type="hidden" name="id" value="<?php echo $component->id; ?>" />
			<input type="hidden" name="component" value="<?php echo $component->option; ?>" />

			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savecom" />
		</form>
	<?php
	}
	
	//-----------
	
	public function misc( &$rows, &$pageNav, $mtask) 
	{
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm">

			<table class="adminlist" summary="A list of variables and their values.">
			 <thead>
			  <tr>
			   <th class="aRight"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
			   <th>Variable</th>
			   <th width="100%">Value</th>
			  </tr>
			 </thead>
			<tfoot>
				<tr>
					<td colspan="3"><?php echo $pageNav->getListFooter(); ?></td>
				</tr>
			</tfoot>
			 <tbody>
<?php
		$k = 0;
		$keys =  array_keys($rows);


        $i = $pageNav->limitstart;
		$n = $pageNav->limit;
		$count = count($keys);
		$end = $i + $n;
		if ($end > $count)
		    $end = $count;

		for (; $i < $end; $i++) 
		{
			$value = $rows[$keys[$i]];
			$name = $keys[$i];
?>
			  <tr class="<?php echo "row$k"; ?>">
			   <td class="aRight"><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo "$name" ?>" onclick="isChecked(this.checked);" /></td>
			   <td><a href="index.php?option=com_hub&amp;task=edit&amp;name=<?php echo $name;?>" title="Edit this variable"><?php echo stripslashes($name); ?></a></td>
			   <td><?php echo stripslashes($value); ?></td>
			  </tr>
<?php
			$k = 1 - $k;
		}
?>
			 </tbody>
			</table>
		    <p style="text-align:center;">Note: These variable settings can be overridden with the file <span style="text-decoration:underline;">hubconfiguration-local.php</span></p>
			
	
			<input type="hidden" name="option" value="com_hub" />
			<input type="hidden" name="task" value="<?php echo $mtask; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<br>
<?php
	}
	
	//-----------
	
	public function edit($name = null, $value = null ) 
	{
	    $editonly = (!empty($name));
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.name.value == '') {
				alert( 'You must fill in a variable name' );
			} else if (form.value.value == '') {
				alert( 'You must fill in a value' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<h2><?php echo ($editonly) ? 'Edit' : 'New'; ?> Variable</h2>

			<fieldset class="adminform">
				<table class="admintable">
				 <tbody>
				  <tr>
				   <td class="key"><label for="name">Variable:</label></td>
				   <td>
<?php               if ($editonly) {
			         echo $name;
			         echo '<input type="hidden" name="editname" value="' . $name . '" />';
                    } else { 
				     echo '<input type="text" name="name" id="name" size="30" maxlength="250" value="' . $name . '" />';
                    } 
?>
                   </td>
				  </tr>
				  <tr>
				   <td style="vertical-align: top;" class="key"><label for="value">Value:</label></td>
				   <td><textarea name="value" id="value" cols="50" rows="15"><?php echo $value;?></textarea></td>
				  </tr>
				 </tbody>
				</table>
				<input type="hidden" name="option" value="com_hub" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</form>
	    <p style="text-align:center;">Note: These variable settings can be overridden with the file <span style="text-decoration:underline;">hubconfiguration-local.php</span></p>
<?php
	}
	
	//-----------

	public function orgs( &$rows, &$pageNav, $option, $filters ) 
	{
			?>
			<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.getElementById('adminForm');
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}
				// do field validation
				submitform( pressbutton );
			}
			</script>

			<form action="index.php" method="post" name="adminForm" id="adminForm">
				<fieldset id="filter">
					<label>
						<?php echo JText::_('SEARCH'); ?>: 
						<input type="text" name="search" value="<?php echo $filters['search']; ?>" />
					</label>

					<input type="submit" value="<?php echo JText::_('Go'); ?>" />
				</fieldset>

				<table class="adminlist" summary="<?php echo JText::_('Organizations'); ?>">
					<thead>
						<tr>
							<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
							<th><?php echo JText::_('ID'); ?></th>
							<th><?php echo JText::_('Organization'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="3">
								<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
	<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) 
			{
				$row = &$rows[$i];
	?>
						<tr class="<?php echo "row$k"; ?>">
							<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
							<td><?php echo $row->id; ?></td>
							<td><a href="index.php?option=<?php echo $option ?>&amp;task=editorg&amp;id[]=<? echo $row->id; ?>"><?php echo stripslashes($row->organization); ?></a></td>
						</tr>
	<?php
				$k = 1 - $k;
			}
	?>
					</tbody>
				</table>

				<input type="hidden" name="option" value="<?php echo $option ?>" />
				<input type="hidden" name="task" value="orgs" />
				<input type="hidden" name="boxchecked" value="0" />
			</form>
	<?php
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function editorg( $org, $option ) 
	{
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<fieldset class="adminform">
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="id" value="<?php echo $org->id; ?>" />
				<input type="hidden" name="task" value="saveorg" />
					
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="organization"><?php echo JText::_('Organization'); ?>:</label></td>
				 			<td><input type="text" name="organization" id="organization" value="<?php echo $org->organization; ?>" size="50" /></td>
				 		</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
		<?php
	}
}
?>
