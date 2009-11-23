<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

ximport('xprofile');

$mycount = 0;

function _compareusers($mode = 0)
{
	$mycount = 0;
	$xhub = &XFactory::getHub();
	$conn = &XFactory::getPLDC();
	$db = &JFactory::getDBO();

    $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

    $dn = 'ou=users,' . $hubLDAPBaseDN;
    //$filter = '(&(objectclass=*)(hasSubordinates=FALSE)(uidNumber=12434))';
    //$filter = '(&(objectclass=*)(hasSubordinates=FALSE)(uidNumber=15825))';
    //$filter = '(&(objectclass=*)(hasSubordinates=FALSE)(uidNumber=10617))';
    $filter = '(&(objectclass=*)(hasSubordinates=FALSE))';

    $sr = @ldap_search($conn, $dn, $filter, array("*","+")); //, $attributes, 0, 0, 0);

    if ($sr === false)
    	return false;

    $count = @ldap_count_entries($conn, $sr);

    if ($count === false)
    	return false;

    $entry = @ldap_first_entry($conn, $sr);

	echo "<table>";
	echo "<tr><td>uidNumber</td><td>key</td><td>mysql</td><td>ldap</td><td>action</td></tr>";
    
	do
    {	
   		$attributes = ldap_get_attributes($conn, $entry);
		$rowhtml = '';
		$showrow = false;

		if (0 && $attributes['uidNumber'][0] < 29000)
		{
			$mycount++;
            $entry = @ldap_next_entry($conn, $entry);
		    continue;
		}

		$profile = new XProfile();
			
		$result = $profile->load($attributes['uid'][0]);
			
		if ($result === false)
			die('couldn\'t find profile for ' . $attributes['uid'][0]);

		for($i = 0; $i < $attributes['count']; $i++)
		{
			$key = $attributes[$i];
			$value = $attributes[$key];
	
			for($j = 0; $j < $value['count']; $j++)
			{
				if (in_array($key,array('objectClass','structuralObjectClass','entryUUID','entryCSN','modifiersName','subschemaSubentry','hasSubordinates','creatorsName','entryDN')))
					continue; // don't care about these
						
				if ($key == 'createTimestamp')
				{
					$ddate = $profile->get('registerDate');
					$myvalue = $value[$j];
					$ldate = strftime("%F %T",strtotime($myvalue));
					$dts = strtotime($ddate);
					$lts = strtotime($ldate);

					if ($lts < $dts)
					{
						$showrow = true;
				       	$rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
						$rowhtml .= "<td>$key</td><td>" . $ddate . "</td>" . "<td>" . $value[$j] . "</td>";
						$rowhtml .= "<td>" . $ddate . "</td>" . "<td>" . $ldate . "</td>";
						$rowhtml .= "<td>" . $dts . "</td>" . "<td>" . $lts . "</td>";
							$rowhtml .= "<td>FIXED</td></tr>";
							$profile->set('registerDate',$ldate);
							$profile->update('mysql');
					}
				}
						else if ($key == 'modifyTimestamp')
						{
							$ddate = $profile->get('modifiedDate');
							$myvalue = $value[$j];
							$ldate = strftime("%F %T",strtotime($myvalue));
							$dts = strtotime($ddate);
							$lts = strtotime($ldate);
							
							if ($lts > $dts) // ldap timestamp > recorded timestamp
							{
								if (empty($ddate) || ($ddate == '0000-00-00 00:00:00')) // no recorded timestamp
								{
									if (strpos($value[$j],'2009013020') === false) // if not mass change date, use ldap modified date
									{
										$showrow = true;
					         				$rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
										$rowhtml .= "<td>$key</td><td>" . $ddate . "</td>" . "<td>" . $value[$j] . "($ldate)</td>";
										/*
										$rowhtml .= "<td>" . $ddate . "</td>" . "<td>" . $ldate . "</td>";
										$rowhtml .= "<td>" . $dts . "</td>" . "<td>" . $lts . "</td>";
										*/
										$rowhtml .= "<td>FIXED</td></tr>";
										$profile->set('modifiedDate',$ldate);
										$profile->update('mysql');
									}
									else // if was mass change date, use created time
									{
										$showrow = true;
					         				$rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
										$rowhtml .= "<td>$key</td><td>" . $ddate . "</td>" . "<td>" . $value[$j] . "</td>";
										$rowhtml .= "<td>" . $ddate . "</td>" . "<td>" . $ldate . "</td>";
										$rowhtml .= "<td>" . $dts . "</td>" . "<td>" . $lts . "</td>";
										$rowhtml .= "<td>FIXED</td></tr>";
										$cdate = strftime("%F %T",strtotime($attributes['createTimestamp'][0] ));
										$profile->set('modifiedDate',$cdate);
										$profile->update('mysql');
									}
								}
								else // recorded timestamp is older than ldap timestamp
								{
									if (strpos($value[$j],'2009013020') === false) // if not mass change date, use ldap modified date
									{
										$showrow = true;
					         				$rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
										$rowhtml .= "<td>$key</td><td>" . $ddate . "</td>" . "<td>" . $value[$j] . "</td>";
										$rowhtml .= "<td>" . $ddate . "</td>" . "<td>" . $ldate . "</td>";
										$rowhtml .= "<td>" . $dts . "</td>" . "<td>" . $lts . "</td>";
										$rowhtml .= "<td>FIXED</td></tr>";
										$profile->set('modifiedDate',$ldate);
										$profile->update('mysql');
									}
								}
							}
						}
						else if ($key == 'uid')
						{
							$dbvalue = $profile->get('username');
							if ($dbvalue != $value[$j])
							{
								$showrow = true;
					          	$rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j] . "</td>";
								
								if (strtolower($dbvalue) == strtolower($value[$j]))
								{
									$profile->set('uid',$value[$j]);
									$profile->update('mysql');
									$rowhtml .= "<td>FIXED</td></tr>";
								}
								else
									$rowhtml .= "<td>MISMATCH</td></tr>";
							}
						}
						else if ($key == 'o')
						{
							$dbvalue = $profile->get('organization');
							if ($dbvalue != $value[$j])
							{
								$showrow = true;
					            $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j] . "</td>";
								if ($dbvalue == '')
								{
									$profile->set('organization',$value[$j]);
									$profile->update('mysql');
									$rowhtml .= "<td>SYNCD TO LDAP</td></tr>";
								}
								else if (!empty($dbvalue) && !empty($value[$j]))
								{
									$profile->set('organization',$dbvalue);
									$profile->update('ldap');
									$rowhtml .= "<td>SYNCD TO MYSQL</td></tr>";
								}
								else
								$rowhtml .= "<td>MISMATCH</td></tr>";
							}
						}
						else if ($key == 'title')
						{
							if ($profile->get('note') != $value[$j])
							{
								$dbvalue = $profile->get('note');
								$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>$dbvalue</td><td>" . $value[$j] . "</td>";
								
								if (empty($dbvalue))
								{
										$profile->set('note',$value[$j]);
										$profile->update('mysql');
										$rowhtml .= "<td>FIXED</td></tr>";
								}
								else
								{
								    	$rowhtml .= "<td>MISMATCH</td></tr>";
								}
							}
						}
						else if ($key == 'description')
						{
							if ($profile->get('reason') != $value[$j])
							{
								$dbvalue = $profile->get('reason');
								$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>$dbvalue</td><td>" . $value[$j] . "</td>";
								if (strpos($value[$j],$dbvalue) !== false)
								{
									if (strlen($value[$j]) > strlen($dbvalue))
									{
										$profile->set('reason',$value[$j]);
										$profile->update('mysql');
										$rowhtml .= "<td>FIXED</td></tr>";
									}
								}
								else
								{
								    	$rowhtml .= "<td>MISMATCH</td></tr>";
								}
							}
						}
						else if ($key == 'homePhone')
						{
							$dbvalue = $profile->get('phone');
							if ($dbvalue != $value[$j])
							{
								echo "profile: " . $profile->get('phone') . " ldap: " . $value[$j] . "<br>";
								if ($dbvalue == '')
								{
									$profile->set('phone',$value[$j]);
									$profile->update('mysql');
									echo "fixed homePhone<br>";
								}
								else
								echo($key . ' mismatch');
							}
						}
						else if ($key == 'cn')
						{
						    $dbvalue = $profile->get('name');
							if ($dbvalue != $value[$j])
							{
								$showrow = true;
					            $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j];
								if (empty($dbvalue))
								{
									$profile->set('name',$value[$j]);
									$profile->update('mysql');
									$rowhtml .= "</td><td>SYNCD TO LDAP</td></tr>";
								}
								else 
								{
									$profile->set('name',$dbvalue);
									$profile->update('ldap');
									$rowhtml .= "</td><td>SYNCD TO MYSQL</td></tr>";
								}
							}
						}
						else if ($key == 'sn')
						{
						   	$dbvalue = $profile->get('name');
							if ($dbvalue != $value[$j])
							{
								$showrow = true;
					            $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j];
								if (empty($dbvalue))
								{
									$profile->set('name',$value[$j]);
									$profile->update('mysql');
									$rowhtml .= "</td><td>SYNCD TO LDAP</td></tr>";
								}
								else 
								{
									$profile->set('name',$dbvalue);
									$profile->update('ldap');
									$rowhtml .= "</td><td>SYNCD TO MYSQL</td></tr>";
								}
							}
						}
						else if ($key == 'regDate')
						{
							$ldate = strftime("%F %T",strtotime($attributes['createTimestamp'][0]));

							if (($profile->get('registerDate') != $value[$j]) && ($profile->get('registerDate') != $ldate))
							{
								die('regDate mismatch');
							}
						}
						else if ($key == 'modDate')
						{
							$dbvalue = $profile->get('modifiedDate');
							$ldate = strftime("%F %T",strtotime($attributes['modifyTimestamp'][0]));
							$cdate = strftime("%F %T",strtotime($attributes['createTimestamp'][0]));
							$dts = strtotime($dbvalue);
                            $lts = strtotime($value[$j]);
							if (($dbvalue != $value[$j]) && ($dbvalue != $ldate) && ($dbvalue != $cdate))
							{
								if ($dts < $lts)
								{
						    		$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>$dbvalue</td><td>" . $value[$j] . "</td>";
								if ($dbvalue == '')
								{
									$profile->set('modifiedDate',$value[$j]);
									$profile->update('mysql');
								} else
								$rowhtml .= "<td>MISMATCH</td></tr>";
								}
							}
						}
						else if ($key == 'sex')
						{
							$dbvalue = $profile->get('gender');
							if ($dbvalue != $value[$j])
							{
								$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j] . "</td>";
								if ($dbvalue == "")
								{
										$profile->set('gender',$value[$j]);
										$profile->update('mysql');
									     $rowhtml .= "<td>FIXED</td></tr>";
								}
								else
									$rowhtml .= "<td>MISMATCH</td></tr>";
							}
						}
						else if ($key == 'usageAgreement')
						{
							if ($profile->get($key) && $value[$j] != 'TRUE')
							{
								die('usageAgreement mismatch');
							}
						}
						else if ($key == 'mail')
						{
							if ($profile->get('email') != $value[$j])
							{
								die('mail mismatch');
							}
						}
						else if ($key == 'shadowExpire')
						{
						    	 $dbvalue = $profile->get($key);
							if ($dbvalue != $value[$j])
							{
								$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j] . "</td>";
									if ($dbvalue == '0')
									{
										$profile->set($key,$value[$j]);
										$profile->update('mysql');
									     $rowhtml .= "<td>FIXED</td></tr>";
									}
									else
									$rowhtml .= "<td>MISMATCH</td></tr>";
							}
						}
						else if ($key == 'regIP')
						{
						    	 $dbvalue = $profile->get('regIP');
							if ($dbvalue != $value[$j])
							{
								$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j] . "</td>";
									if ($dbvalue == '')
									{
										$profile->set($key,$value[$j]);
										$profile->update('mysql');
									     $rowhtml .= "<td>FIXED</td></tr>";
									}
									else
									$rowhtml .= "<td>MISMATCH</td></tr>";
							}
						}
						else if (in_array($key,array('member','disability','hispanic','role','race','edulevel','host','admin','manager')))
						{
							if ($key == 'member')
							{
								$myhtml = '';
							     $license = $value[$j];
							    
								if (preg_match('/^license=([^,.]*),.*$/', $license, $matches) == 0)
								{
									$showrow = true;
								     $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								     $rowhtml .= "<td>$key</td><td>" . $value[$j] . "</td><td>FAILED TO PARSE</td></tr>";
								}
							     else
								{
									$license = $matches[1];

									$query = "SELECT id FROM jos_licenses WHERE alias='$license';";
									$db->setQuery( $query );
          							$result = $db->loadObject();

									if (!is_object($result))
								    	{
										$showrow = true;
								    		$rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								     	$rowhtml .= "<td>$key</td><td>" . $value[$j] . "</td><td>$license</td>";
										$rowhtml .= "<td>FAILED LICENSE LOOKUP</td>";
									}
									else
									{
										$lid = $result->id;

										$query = "SELECT license_id,user_id FROM jos_licenses_users WHERE license_id='" . $lid . "' AND user_id='" . $attributes['uidNumber'][0] . "';";
										$db->setQuery( $query );
          								$result = $db->loadObject();
									
										if ($result)
										{
											//$rowhtml .= "<td>EXISTS</td></tr>";
										}
										else
										{
							    				$query = "INSERT INTO jos_licenses_users (license_id,user_id) VALUES ('" . $lid . "','" . $attributes['uidNumber'][0] . "');";
											$result = $db->execute( $query );
											$showrow = true;
								    			$rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								     		$rowhtml .= "<td>$key</td><td>" . $value[$j] . "</td><td>$license</td>";
										
											if ($result)
												$rowhtml .= "<td>ADDED</td></tr>";
											else
												$rowhtml .= "<td>$query</td></tr>";
										}
									}
								}

							}
							else
							{

							$values = $profile->get($key);
							if (!in_array($value[$j],$values))
							{
						     	$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . implode(',',$values) . "</td><td>" . $value[$j] . "</td><td>FIXED</td></tr>";
								$profile->add($key,$value[$j]);
								$profile->update('mysql');
							}
							}
						}
						else if (in_array($key,array('regHost','homeDirectory','ftpShell','jobsAllowed','emailConfirmed','loginShell','gidNumber','uidNumber','userPassword','gid','sex','countryresident','countryorigin','mailPreferenceOption','orgtype','url','nativeTribe','proxyUidNumber','proxyPassword')))
						{
							if ($j > 0)
								die('unexpected multivalue');
						
							if ($profile->get($key) != $value[$j])
							{
								$dbvalue = $profile->get($key);
								$showrow = true;
					               $rowhtml .= "<tr><td>" . $attributes['uidNumber'][0] . "</td>";
								$rowhtml .= "<td>$key</td><td>" . $dbvalue . "</td>" . "<td>" . $value[$j] . "</td>";
								if (in_array($key,array('url','regIP','countryresident','countryorigin','orgtype')))
								{
									if (empty($dbvalue))
									{
										$profile->set($key,$value[$j]);
										$profile->update('mysql');
										$rowhtml .= "<td>FIXED</td></tr>";
									}
								}
								else if ($key == 'homeDirectory')
								{
									$dbvalue = $profile->get($key);

									if (strtolower($dbvalue) == $value[$j])
									{
										$profile->set($key,$value[$j]);
										$profile->update('mysql');
										echo "fixed $key <br>";
									}
								}
								else
								{
									$rowhtml .= "<td>MISMATCH</td></tr>\n";
								}
							}
						}
						else
						{
							var_dump($profile);
							echo "$key: " . $value[$j] . "<br>";
						}
					}


				}
					if ($showrow) echo $rowhtml . "\n";

				$mycount++;
                    $entry = @ldap_next_entry($conn, $entry);
				//if ($mycount > 14000) break;

               }
               while($entry !== false);
			echo "<tr><td>uidNumber</td><td>key</td><td>mysql</td><td>ldap</td><td>action</td></tr>";
			echo "</table>\n";
			echo "count = $count<br>";
			echo "mycount = $mycount<br>";
}


function printuser($name)
{
	global $mycount;


	if ($mycount > 1000)
		exit();

	$profile = new XProfile();
	$profile2 = new XProfile();

	$profile->load($name,'ldap');
	$profile2->load($name,'mysql');

	if (($profile2->get('uidNumber') == '0') || ($profile2->get('uidNumber') == ''))
	{
	$mycount++;
		$profile->create('mysql');
		echo "no xprofile entry for $name<br>";
		echo "created one<br>";
	}
	else if ($profile->get('uidNumber') != $profile2->get('uidNumber'))
		echo "profile mismatch name $name " . $profile->get('uidNumber') . " " . $profile2->get('uidNumber') . "<br>";

	$mjuser = JUser::getInstance($name);

	if (empty($mjuser))
	{
	$mycount++;
		$instance = new JUser();
        $usertype = 'Registered';
        $acl =& JFactory::getACL();
        $instance->set( 'id', $profile->get('uidNumber') );
        $instance->set( 'name', $profile->get('name') );
        $instance->set( 'username', $profile->get('username'));
        $instance->set( 'password_clear', '' );
        $instance->set( 'email', $profile->get('email'));
        $instance->set( 'gid', $acl->get_group_id( '', $usertype));
        $instance->set( 'usertype', $usertype );
		$result = $instance->save();
		echo "no juser entry for $name<br>";
		echo "created one<br>";
		$db =& JFactory::getDBO();

		$sql = "INSERT INTO #__users (id,name,username,email,gid,usertype) VALUES (" .
			$db->Quote( $profile->get('uidNumber') ) . "," .
			$db->Quote( $profile->get('name') ) . "," .
			$db->Quote( $profile->get('username') ) . "," .
			$db->Quote( $profile->get('email') ) . "," .
			$db->Quote( $acl->get_group_id( '', $usertype) ) . "," .
			$db->Quote( $usertype ) .
			");";
	
		$db->setQuery($sql);
		$result = $db->query();

		if (!$result)
			die('db error');

		$sql = "INSERT INTO #__core_acl_aro (section_value,value,name) VALUES ( 'users' ," .
			$db->Quote(  $profile->get('uidNumber') ) . "," .
			$db->Quote( $profile->get('name') ) .
			");";
		$db->setQuery($sql);
		$result = $db->query();
		if (!$result)
			die('db error');
		$insertid = $db->insertid();

		$sql = "INSERT INTO #__core_acl_groups_aro_map (group_id, aro_id) VALUES ( " .
			 $db->Quote( $acl->get_group_id( '', $usertype) ) . "," .
			 $db->Quote( $insertid ) .
			");";
		$db->setQuery($sql);
		$result = $db->query();
		if (!$result)
			die('db error');
	}
	else if ($profile->get('uidNumber') != $mjuser->get('id'))
		echo "juser mismatch name $name " . $profile->get('uidNumber') . " " . $mjuser->get('id') . "<br>";

}

function _showusers() 
{
XProfileHelper::iterate_profiles('printuser','ldap');
return;
}
?>
