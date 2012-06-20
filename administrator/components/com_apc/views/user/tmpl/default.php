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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Menu items
JToolBarHelper::title(JText::_('APC User Entries'), 'addedit.png');
?>

<div class=content>

<?php

	$fieldname='info';
	$fieldheading='User Entry Label';
	$fieldkey='info';

	if (!empty($MYREQUEST['SH']))
	{
		echo '<div class="info"><table cellspacing=0><tbody>';
		echo '<tr><th>Attribute</th><th>Value</th></tr>';

		$m=0;
		foreach($scope_list as $j => $list)
		{
			foreach($cache[$list] as $i => $entry)
			{
				if (md5($entry[$fieldkey])!=$MYREQUEST['SH']) continue;
				foreach($entry as $k => $value)
				{
					if ($k == "num_hits")
					{
						$value = sprintf("%s (%.2f%%)",$value,$value*100/$cache['num_hits']);
					}
					if ($k == 'deletion_time')
					{
						if(!$entry['deletion_time']) $value = "None";
					}
					echo
						"<tr class=tr-$m>",
						"<td class=td-0>",ucwords(preg_replace("/_/"," ",$k)),"</td>",
						"<td class=td-last>",(preg_match("/time/",$k) && $value!='None') ? date(DATE_FORMAT,$value) : $value,"</td>",
						"</tr>";
					$m=1-$m;
				}
				if($fieldkey == 'info')
				{
					echo "<tr class=tr-$m><td class=td-0>Stored Value</td><td class=td-last><pre>";
					$output = var_export(apc_fetch($entry[$fieldkey]),true);
					echo htmlspecialchars($output);
					echo "</pre></td></tr>\n";
				}
				break;
			}
		}

		echo '</tbody></table>';
		echo '</div>';

		break;
	}

	$cols = 6;
	echo '<div class=sorting><form>Scope:';
	echo "<input type=hidden name=OB value={$MYREQUEST['OB']}>";
	echo "<input type=hidden name=option value={$this->option}>";
	echo "<input type=hidden name=task value={$this->task}>";
	echo '<select name=SCOPE>';

	echo
		"<option value=A",$MYREQUEST['SCOPE']=='A' ? " selected":"",">Active</option>",
		"<option value=D",$MYREQUEST['SCOPE']=='D' ? " selected":"",">Deleted</option>",
		"</select>",
		", Sorting:<select name=SORT1>",
		"<option value=H",$MYREQUEST['SORT1']=='H' ? " selected":"",">Hits</option>",
		"<option value=Z",$MYREQUEST['SORT1']=='Z' ? " selected":"",">Size</option>",
		"<option value=S",$MYREQUEST['SORT1']=='S' ? " selected":"",">$fieldheading</option>",
		"<option value=A",$MYREQUEST['SORT1']=='A' ? " selected":"",">Last accessed</option>",
		"<option value=M",$MYREQUEST['SORT1']=='M' ? " selected":"",">Last modified</option>",
		"<option value=C",$MYREQUEST['SORT1']=='C' ? " selected":"",">Created at</option>",
		"<option value=D",$MYREQUEST['SORT1']=='D' ? " selected":"",">Deleted at</option>";
	if($fieldname=='info') echo
		"<option value=D",$MYREQUEST['SORT1']=='T' ? " selected":"",">Timeout</option>";
	echo 
		'</select>',
		'<select name=SORT2>',
		'<option value=D',$MYREQUEST['SORT2']=='D' ? ' selected':'','>DESC</option>',
		'<option value=A',$MYREQUEST['SORT2']=='A' ? ' selected':'','>ASC</option>',
		'</select>',
		'<select name=COUNT onChange="form.submit()">',
		'<option value=10 ',$MYREQUEST['COUNT']=='10' ? ' selected':'','>Top 10</option>',
		'<option value=20 ',$MYREQUEST['COUNT']=='20' ? ' selected':'','>Top 20</option>',
		'<option value=50 ',$MYREQUEST['COUNT']=='50' ? ' selected':'','>Top 50</option>',
		'<option value=100',$MYREQUEST['COUNT']=='100'? ' selected':'','>Top 100</option>',
		'<option value=150',$MYREQUEST['COUNT']=='150'? ' selected':'','>Top 150</option>',
		'<option value=200',$MYREQUEST['COUNT']=='200'? ' selected':'','>Top 200</option>',
		'<option value=500',$MYREQUEST['COUNT']=='500'? ' selected':'','>Top 500</option>',
		'<option value=0  ',$MYREQUEST['COUNT']=='0'  ? ' selected':'','>All</option>',
		'</select>','&nbsp; Search: <input name=SEARCH value="',$MYREQUEST['SEARCH'],'" type=text size=25/>',
		'&nbsp;<input type=submit value="GO!">',
		'</form></div>';

	if (isset($MYREQUEST['SEARCH']))
	{
		// Don't use preg_quote because we want the user to be able to specify a
		// regular expression subpattern.
		$MYREQUEST['SEARCH'] = '/'.str_replace('/', '\\/', $MYREQUEST['SEARCH']).'/i';
		if (preg_match($MYREQUEST['SEARCH'], 'test') === false)
		{
			echo '<div class="error">Error: enter a valid regular expression as a search query.</div>';
			break;
		}
	}

	echo
		'<div class="info"><table cellspacing=0><tbody>',
		'<tr>',
		'<th>',ApcHTML::sortheader('S',$fieldheading,  "&OB=".$MYREQUEST['OB']),'</th>',
		'<th>',ApcHTML::sortheader('H','Hits',         "&OB=".$MYREQUEST['OB']),'</th>',
		'<th>',ApcHTML::sortheader('Z','Size',         "&OB=".$MYREQUEST['OB']),'</th>',
		'<th>',ApcHTML::sortheader('A','Last accessed',"&OB=".$MYREQUEST['OB']),'</th>',
		'<th>',ApcHTML::sortheader('M','Last modified',"&OB=".$MYREQUEST['OB']),'</th>',
		'<th>',ApcHTML::sortheader('C','Created at',   "&OB=".$MYREQUEST['OB']),'</th>';

	if($fieldname=='info')
	{
		$cols+=2;
		 echo '<th>',ApcHTML::sortheader('T','Timeout',"&OB=".$MYREQUEST['OB']),'</th>';
	}
	echo '<th>',ApcHTML::sortheader('D','Deleted at',"&OB=".$MYREQUEST['OB']),'</th></tr>';

	// builds list with alpha numeric sortable keys
	//
	$list = array();
	foreach($this->cache[$this->scope_list[$MYREQUEST['SCOPE']]] as $i => $entry)
	{
		switch($MYREQUEST['SORT1'])
		{
			case 'A': $k=sprintf('%015d-',$entry['access_time']); 	break;
			case 'H': $k=sprintf('%015d-',$entry['num_hits']); 		break;
			case 'Z': $k=sprintf('%015d-',$entry['mem_size']); 		break;
			case 'M': $k=sprintf('%015d-',$entry['mtime']);			break;
			case 'C': $k=sprintf('%015d-',$entry['creation_time']);	break;
			case 'T': $k=sprintf('%015d-',$entry['ttl']);			break;
			case 'D': $k=sprintf('%015d-',$entry['deletion_time']);	break;
			case 'S': $k='';										break;
		}
		$list[$k.$entry[$fieldname]]=$entry;
	}

	if ($list)
	{
		// sort list
		//
		switch ($MYREQUEST['SORT2']) {
			case "A":	krsort($list);	break;
			case "D":	ksort($list);	break;
		}

		// output list
		$i=0;
		foreach($list as $k => $entry)
		{
			if(!$MYREQUEST['SEARCH'] || preg_match($MYREQUEST['SEARCH'], $entry[$fieldname]) != 0)
			{
				$field_value = htmlentities(strip_tags($entry[$fieldname],''), ENT_QUOTES, 'UTF-8');
				echo
					'<tr class=tr-',$i%2,'>',
					"<td class=td-0><a href=\"$MY_SELF&OB=",$MYREQUEST['OB'],"&SH=",md5($entry[$fieldkey]),"\">",$field_value,'</a></td>',
					'<td class="td-n center">',$entry['num_hits'],'</td>',
					'<td class="td-n right">',$entry['mem_size'],'</td>',
					'<td class="td-n center">',date(DATE_FORMAT,$entry['access_time']),'</td>',
					'<td class="td-n center">',date(DATE_FORMAT,$entry['mtime']),'</td>',
					'<td class="td-n center">',date(DATE_FORMAT,$entry['creation_time']),'</td>';

				if($fieldname=='info')
				{
					if($entry['ttl'])
						echo '<td class="td-n center">'.$entry['ttl'].' seconds</td>';
					else
						echo '<td class="td-n center">None</td>';
				}
				if ($entry['deletion_time'])
				{
					echo '<td class="td-last center">', date(DATE_FORMAT,$entry['deletion_time']), '</td>';
				}
				else if ($MYREQUEST['OB'] == OB_USER_CACHE)
				{
					echo '<td class="td-last center">';
					echo '[<a href="', $MY_SELF, '&OB=', $MYREQUEST['OB'], '&DU=', urlencode($entry[$fieldkey]), '">Delete Now</a>]';
					echo '</td>';
				}
				else
				{
					echo '<td class="td-last center"> &nbsp; </td>';
				}
				echo '</tr>';
				$i++;
				if ($i == $MYREQUEST['COUNT'])
					break;
			}
		}
	} else {
		echo '<tr class=tr-0><td class="center" colspan=',$cols,'><i>No data</i></td></tr>';
	}
	echo '</tbody></table>';

	if ($list && $i < count($list))
	{
		echo "<a href=\"$MY_SELF&OB=",$MYREQUEST['OB'],"&COUNT=0\"><i>",count($list)-$i,' more available...</i></a>';
	}

	echo "</div>";
?>