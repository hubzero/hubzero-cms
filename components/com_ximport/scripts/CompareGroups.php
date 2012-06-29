<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'CompareGroups'
 * 
 * Long description (if any) ...
 */
class CompareGroups extends XImportHelperScript
{

	/**
	 * Description for '_description'
	 * 
	 * @var string
	 */
	protected $_description = 'Compare groups from LDAP.';

	/**
	 * Short description for 'run'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function run()
	{
        $conn = &Hubzero_Factory::getPLDC();
		$db   = &JFactory::getDBO();
		
		$ldap_params = JComponentHelper::getParams('com_ldap');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

           $dn = 'ou=licenses,' . $hubLDAPBaseDN;
           $filter = '(&(objectclass=*)(hasSubordinates=FALSE))';

           $sr = @ldap_search($conn, $dn, $filter, array("*","+")); //, $attributes, 0, 0, 0);

           if ($sr === false)
                return false;

           $count = @ldap_count_entries($conn, $sr);

           if ($count === false)
                return false;

           $entry = @ldap_first_entry($conn, $sr);

		echo "<table>";

           do
           {
                $attributes = ldap_get_attributes($conn, $entry);
			$rowhtml = '';
				$showrow = false;

			for($i = 0; $i < $attributes['count']; $i++)
			{
				$key = $attributes[$i];
				$value = $attributes[$key];

				for($j = 0; $j < $value['count']; $j++)
				{
					if (in_array($key,array('objectClass','structuralObjectClass','entryUUID','entryCSN','creatorsName','modifiersName','entryDN','subschemaSubentry','hasSubordinates')))
						continue; // don't care about these

					//if (in_array($key,array('member')))
					//	continue; // don't care about these for the moment

					$query = "SELECT * FROM #__licenses WHERE alias=" . $db->Quote( $attributes['license'][0] );
					$db->setQuery($query);
					$result = $db->loadObject();

					if (!is_object($result))
					{
						echo "<tr><td>Unable to load database record for " . $attributes['license'][0] . "</td></tr>";
					}
					else if ($key == 'license')
					{
						// nothing to do for this
					}
					else if ($key == 'member')
					{
						$license_id = $result->id;
                               $myvalue = $value[$j];
						$result = preg_match('/^tool=([^,.]*),/', $myvalue, $matches);
						$showrow = true;
						$rowhtml = "<tr>";
						if ($result)
						{
							$alias = $matches[1];

							$rowhtml = "<td>$license_id</td><td>$alias</td>";

							$query = "SELECT * FROM #__tool_version WHERE instance=" . $db->Quote( $alias );
							$db->setQuery($query);
							$result = $db->loadObject();
						}

						if (!is_object($result))
						{
							$rowhtml .=  "<td>Unable to load database record for tool $alias</td>";
						}
						else
						{
							$tool_id = $result->id;
							$query = "SELECT * FROM #__licenses_tools WHERE license_id=" . $db->Quote($license_id) . " AND tool_id = " . $db->Quote($result->id);

							$db->setQuery($query);
							$result = $db->loadObject();

							if (!is_object($result))
							{
								$query = "INSERT INTO #__licenses_tools (license_id,tool_id) VALUES (" . $db->Quote($license_id) . "," . $db->Quote($tool_id) . ");";
								$rowhtml .= "<td>$query</td>";
								$result = $db->execute($query);

								if ($result)
									$rowhtml .=  "<td>FIXED</td>";
								else
									$rowhtml .= "<td>FIX FAILED</td>";
							}
							else
							{
								$showrow = false;
								$rowhtml .= "<td>Already exists</td>";
							}

						}
						$rowhtml .= "</tr>";
					}
					else if ($key == 'createTimestamp')
                          {
                               $ddate = $result->created;
                               $myvalue = $value[$j];
                               $ldate = strftime("%F %T",strtotime($myvalue));
                               $dts = strtotime($ddate);
                               $lts = strtotime($ldate);

                               if (($ddate == "0000-00-00 00:00:00") || ($lts < $dts))
                               {
                               	$showrow = true;
                               	$rowhtml .= "<tr><td>" . $attributes['license'][0] . "</td>";
                               	$rowhtml .= "<td>$key</td><td>DD:" . $ddate . "</td>" . "<td>LV:" . $value[$j] . "</td>";
                               	$rowhtml .= "<td>DD:" . $ddate . "</td>" . "<td>LD:" . $ldate . "</td>";
                               	$rowhtml .= "<td>DTS:" . $dts . "</td>" . "<td>LTS:" . $lts . "</td>";
                               	//$rowhtml .= "<td>LDAP CREATED EARLIER!</td></tr>";

							$query = "UPDATE #__licenses SET created=" . $db->Quote($ldate) . " WHERE alias=" . $db->Quote( $attributes['license'][0] );
							$result = $db->execute($query);

							if ($result)
								$rowhtml .= "<td>FIXED</td></tr>";
							else
								$rowhtml .= "<td>FIX FAILED</td></tr>";
                               }

                          }
					else if ($key == 'modifyTimestamp')
                          {
                               $ddate = $result->modified;
                               $myvalue = $value[$j];
                               $ldate = strftime("%F %T",strtotime($myvalue));
                               $dts = strtotime($ddate);
                               $lts = strtotime($ldate);

                               if (($ddate == "0000-00-00 00:00:00") || ($lts > $dts))
                               {
                               	$showrow = true;
                               	$rowhtml .= "<tr><td>" . $attributes['license'][0] . "</td>";
                               	$rowhtml .= "<td>$key</td><td>DD:" . $ddate . "</td>" . "<td>LV:" . $value[$j] . "</td>";
                               	$rowhtml .= "<td>DD:" . $ddate . "</td>" . "<td>LD:" . $ldate . "</td>";
                               	$rowhtml .= "<td>DTS:" . $dts . "</td>" . "<td>LTS:" . $lts . "</td>";
                               	//$rowhtml .= "<td>LDAP CREATED EARLIER!</td></tr>";

							$query = "UPDATE #__licenses SET modified=" . $db->Quote($ldate) . " WHERE alias=" . $db->Quote( $attributes['license'][0] );
							$result = $db->execute($query);

							if ($result)
								$rowhtml .= "<td>FIXED</td></tr>";
							else
								$rowhtml .= "<td>FIX FAILED</td></tr>";
                               }

                          }
					else
						echo "$key: " . $value[$j] . "<br>";

					if ($showrow) echo $rowhtml;
				}

			}

                $entry = @ldap_next_entry($conn, $entry);
           }
           while($entry !== false);

		echo "</table>";
	}
}
