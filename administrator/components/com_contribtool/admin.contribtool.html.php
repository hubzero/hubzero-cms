<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined("n")) {

/**
 * Description for ''t''
 */
	define('t',"\t");

/**
 * Description for ''n''
 */
	define('n',"\n");

/**
 * Description for ''r''
 */
	define('r',"\r");

/**
 * Description for ''br''
 */
	define('br','<br />');

/**
 * Description for ''sp''
 */
	define('sp','&#160;');

/**
 * Description for ''a''
 */
	define('a','&amp;');
}

/**
 * Short description for 'ContribtoolHtml'
 * 
 * Long description (if any) ...
 */
class ContribtoolHtml
{
	//----------------------------------------------------------
	// Misc. 
	//----------------------------------------------------------


	/**
	 * Short description for 'error'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}

	/**
	 * Short description for 'warning'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	/**
	 * Short description for 'alert'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	/**
	 * Short description for 'hed'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $level Parameter description (if any) ...
	 * @param      string $txt Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	/**
	 * Short description for 'shortenText'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $text Parameter description (if any) ...
	 * @param      integer $chars Parameter description (if any) ...
	 * @param      integer $p Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function shortenText($text, $chars=300, $p=1)
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		if ($text == '') {
			$text = '...';
		}
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	/**
	 * Short description for 'browseTools'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $rows Parameter description (if any) ...
	 * @param      object $pageNav Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      array $filters Parameter description (if any) ...
	 * @return     void
	 */
	public function browseTools( $rows, $pageNav, $option, $filters )
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
                         <?php echo JText::_('SEARCH'); ?>
                         <select name="search_field">
                              <option value="all"<?php if ($filters['search_field'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_SEARCH_ALL'); ?></option>
                              <option value="id"<?php if ($filters['search_field'] == 'id') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_ID'); ?></option>
                              <option value="toolname"<?php if ($filters['search_field'] == 'toolname') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_NAME'); ?></option>
                              <option value="title"<?php if ($filters['search_field'] == 'title') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_TITLE'); ?></option>
                         </select>
                         for
                         <input type="text" name="search" value="<?php echo $filters['search']; ?>" />


                    <label>
                         <?php echo JText::_('SORT_BY'); ?>:
                         <select name="sortby">
                              <option value="state_changed DESC"<?php if ($filters['sortby'] == 'state_changed DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_STATECHANGED_DESC'); ?></option>
                              <option value="state_changed ASC"<?php if ($filters['sortby'] == 'state_changed ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_STATECHANGED_ASC'); ?></option>
                              <option value="registered DESC"<?php if ($filters['sortby'] == 'registered DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_REGISTERED_DESC'); ?></option>
                              <option value="registered ASC"<?php if ($filters['sortby'] == 'registered ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_REGISTERED_ASC'); ?></option>
                              <option value="id DESC"<?php if ($filters['sortby'] == 'id DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_ID_DESC'); ?></option>
                              <option value="id ASC"<?php if ($filters['sortby'] == 'id ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_ID_ASC'); ?></option>
                              <option value="toolname ASC"<?php if ($filters['sortby'] == 'toolname ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_NAME_ASC'); ?></option>
                              <option value="toolname DESC"<?php if ($filters['sortby'] == 'toolname DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_NAME_DESC'); ?></option>
                              <option value="title ASC"<?php if ($filters['sortby'] == 'title ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_TITLE_ASC'); ?></option>
                              <option value="title DESC"<?php if ($filters['sortby'] == 'title DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_TITLE_DESC'); ?></option>
                              <option value="versions ASC"<?php if ($filters['sortby'] == 'versions DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_VERSIONS_ASC'); ?></option>
                              <option value="versions DESC"<?php if ($filters['sortby'] == 'versions ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_VERSIONS_DESC'); ?></option>
                         </select>
                    </label>

                    <input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>

				<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
                         <thead>
                              <tr>
                                   <th width="2%"><?php echo JText::_('#'); ?></th>
                                   <th width="3%"></th>
                                   <th><?php echo JText::_('TOOL_NAME'); ?></th>
                                   <th><?php echo JText::_('TOOL_TITLE'); ?></th>
                                   <th><?php echo JText::_('TOOL_STATE'); ?></th>
                                   <th><?php echo JText::_('TOOL_REGISTERED'); ?></th>
                                   <th><?php echo JText::_('TOOL_STATECHANGED'); ?></th>
                                   <th><?php echo JText::_('TOOL_VERSIONS'); ?></th>
                                   <th><?php echo JText::_('TOOL_ID'); ?></th>
                              </tr>
                         </thead>
                         <tfoot>
                              <tr>
                                   <td colspan="7">
                                        <?php echo $pageNav->getListFooter(); ?>
                                   </td>
                              </tr>
                         </tfoot>
                         <tbody>
<?php
		$k = 0;
		for($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = &$rows[$i];
?>
               <tr class="<?php echo "row$k"; ?>">
				<td><?php echo $i+1+$pageNav->limitstart;?></td>
               	<td><input type="radio" name="id" id="cb<?php echo $i;?>" value="<?php echo $row['id'] ?>" onclick="isChecked(this.checked);" /></td>
                    <td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;toolid=<? echo $row['id']; ?>"><?php echo stripslashes($row['toolname']); ?></a></td>
				<td><?php echo $row['title']; ?></td>
				<td><?php echo $row['state']; ?></td>
				<td><?php echo $row['registered']; ?></td>
				<td><?php echo $row['state_changed']; ?></td>
                    <td><a href="index.php?option=<?php echo $option ?>&amp;task=view&amp;toolid=<? echo $row['id'];?>"><img src="/includes/js/ThemeOffice/mainmenu.png" border="0" /></a><?php echo " " . $row['versions']; ?></td>
                    <td><?php echo $row['id']; ?></td>
                </tr>
<?php
			$k = 1 - $k;
		}
?>
					</tbody>
				</table>
                    <input type="hidden" name="option" value="<?php echo $option ?>" />
                    <input type="hidden" name="task" value="view" />
                    <input type="hidden" name="boxchecked" value="0" />
          </form>
<?php
	}

	/**
	 * Short description for 'editTool'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $data Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @return     void
	 */
	public function editTool($data,$option)
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
               <div class="col width-60">
                    <fieldset class="adminform">
                         <legend><?php echo JText::_('TOOL_DETAILS'); ?></legend>

                         <input type="hidden" name="toolid" value="<?php echo $data['toolid'] ?>" />
                         <input type="hidden" name="type" value="tool" />
                         <input type="hidden" name="option" value="<?php echo $option; ?>" />
                         <input type="hidden" name="task" value="save" />

					<table class="admintable">
					 <tbody>
					  <tr>
					    <td class="key"><label for="toolid"><?php echo JText::_('TOOL_ID'); ?>:</label></td>
					    <td><?php echo $data['toolid'];?></td>
					  </tr>
					  <tr>
					    <td class="key"><label for="toolname"><?php echo JText::_('TOOL_NAME'); ?>:</label></td>
					    <td><?php echo $data['toolname'];?></td>
					  </tr>
					  <tr>
					    <td class="key"><label for="tooltitle"><?php echo JText::_('TOOL_TITLE'); ?>:</label></td>
					    <td><input type="text" name="tooltitle" id="tooltitle" value="<?php echo $data['title'];?>" size="50" /> </td>
					  </tr>
					 </tbody>
					</table>
				</fieldset>
			</div>
		</form>
<?php
	}

	/**
	 * Short description for 'editToolVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $data Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @return     void
	 */
	public function editToolVersion($data,$option)
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
               <div class="col width-60">
                    <fieldset class="adminform">
                         <legend><?php echo JText::_('TOOL_VERSION_DETAILS'); ?></legend>

                         <input type="hidden" name="id" value="<?php echo $data['id'] ?>" />
                         <input type="hidden" name="toolid" value="<?php echo $data['toolid'] ?>" />
                         <input type="hidden" name="type" value="toolversion" />
                         <input type="hidden" name="option" value="<?php echo $option; ?>" />
                         <input type="hidden" name="task" value="save" />

					<table class="admintable">
					 <tbody>
					  <tr>
					    <td class="key"><label for="command"><?php echo JText::_('TOOL_COMMAND'); ?>:</label></td>
					    <td><input type="text" name="command" id="command" value="<?php echo $data['vnc_command'];?>" size="50" /> </td>
					  </tr>
					  <tr>
					    <td class="key"><label for="timeout"><?php echo JText::_('TOOL_TIMEOUT'); ?>:</label></td>
					    <td><input type="text" name="timeout" id="timeout" value="<?php echo $data['vnc_timeout'];?>" size="50" /> </td>
					  </tr>
					  <tr>
					    <td class="key"><label for="hostreq"><?php echo JText::_('TOOL_HOSTREQ'); ?>:</label></td>
					    <td><input type="text" name="hostreq" id="hostreq" value="<?php echo implode(',',$data['hostreq']);?>" size="50" /> </td>
					  </tr>
					 </tbody>
				    	</table>
				</fieldset>
			</div>
		</form>
<?php
	}

	/**
	 * Short description for 'browseToolVersions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $data Parameter description (if any) ...
	 * @param      object $pageNav Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      array $filters Parameter description (if any) ...
	 * @return     void
	 */
	public function browseToolVersions( $data, $pageNav, $option, $filters )
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

    		<form action="index.php" method="post" name="adminForm2">

		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top">
				<table class="adminform">
				<tr>
					<td width="5%">
						<label for="name">Tool ID</label>
					</td>
					<td>
						<?php echo $data['id'];?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="name">Name</label>
					</td>
					<td>
						<?php echo $data['toolname'];?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="title">Title</label>
					</td>
					<td>
						<?php echo $data['title'];?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		</form>

          <form action="index.php" method="post" name="adminForm" id="adminForm">
               <fieldset id="filter">
                         <?php echo JText::_('SEARCH'); ?>
                         <select name="search_field">
                              <option value="id"<?php if ($filters['search_field'] == 'id') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOLID'); ?></option>
                              <option value="toolname"<?php if ($filters['search_field'] == 'toolname') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOLNAME'); ?></option>
                              <option value="title"<?php if ($filters['search_field'] == 'title') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOLTITLE'); ?></option>
                         </select>
                         for
                         <input type="text" name="search" value="<?php echo $filters['search']; ?>" />


                    <label>
                         <?php echo JText::_('SORT_BY'); ?>:
                         <select name="sortby">
                              <option value="id DESC"<?php if ($filters['sortby'] == 'id DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_ID_DESC'); ?></option>
                              <option value="id ASC"<?php if ($filters['sortby'] == 'id ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_ID_ASC'); ?></option>
                              <option value="toolname_ASC"<?php if ($filters['sortby'] == 'toolname ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_NAME_ASC'); ?></option>
                              <option value="toolname_DESC"<?php if ($filters['sortby'] == 'toolname DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_NAME_DESC'); ?></option>
                              <option value="title ASC"<?php if ($filters['sortby'] == 'title ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_TITLE_ASC'); ?></option>
                              <option value="title DESC"<?php if ($filters['sortby'] == 'title DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_TITLE_DESC'); ?></option>
                              <option value="versions ASC"<?php if ($filters['sortby'] == 'versions DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_VERSIONS_ASC'); ?></option>
                              <option value="versions DESC"<?php if ($filters['sortby'] == 'versions ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOL_VERSIONS_DESC'); ?></option>
                         </select>
                    </label>

                    <input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>

				<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
                         <thead>
                              <tr>
                                   <th width="2%"><?php echo JText::_('#'); ?></th>
                                   <th width="3%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $data['version'] );?>);" /></th>
                                   <th><?php echo JText::_('TOOLINSTANCE'); ?></th>
                                   <th><?php echo JText::_('TOOLVERSION'); ?></th>
                                   <th><?php echo JText::_('TOOLREVISION'); ?></th>
                                   <th><?php echo JText::_('TOOLSTATE'); ?></th>
                                   <th><?php echo JText::_('TOOLID'); ?></th>
                              </tr>
                         </thead>
                         <tfoot>
                              <tr>
                                   <td colspan="7">
                                        <?php echo $pageNav->getListFooter(); ?>
                                   </td>
                              </tr>
                         </tfoot>
                         <tbody>
<?php
		$k = 0;
		$rows = $data['version'];
		for($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = &$rows[$i];
?>
               <tr class="<?php echo "row$k"; ?>">
				<td><?php echo $i+1;?></td>
               	<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row['id'] ?>" onclick="isChecked(this.checked);" /></td>
                    <td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;toolid=<? echo $data['id'];?>&amp;id=<? echo $row['id']; ?>"><?php echo stripslashes($row['instance']); ?></a></td>
                    <td><?php echo $row['version']; ?></td>
                    <td><?php echo $row['revision']; ?></td>
                    <td><?php echo $row['state']; ?></td>
                    <td><?php echo $row['id']; ?></td>
                </tr>
<?php
			$k = 1 - $k;
		}
?>
					</tbody>
				</table>
                    <input type="hidden" name="option" value="<?php echo $option ?>" />
                    <input type="hidden" name="toolid" value="<?php echo $data['id']; ?>" />
                    <input type="hidden" name="task" value="view" />
                    <input type="hidden" name="boxchecked" value="0" />
          </form>
<?php
	}
}
?>