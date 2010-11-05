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

// Propel startup and include code
set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path(JPATH_SITE . "/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path(JPATH_SITE . "/api/org/nees" . PATH_SEPARATOR . get_include_path());
spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init(JPATH_SITE . "/api/org/phpdb/propel/central/conf/central-conf.php");

include_once "lib/data/Person.php";
include_once "lib/data/PersonPeer.php";

jimport('joomla.event.plugin');

class plgUserNeesusers extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param object $subject The object to observe
     * @param array $config An array that holds the plugin configuration
     */
    function plgUserNeesusers(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }


    /**
     * Example store user method
     *
     * Method is called after user data is stored in the database
     *
     * @param array holds the new user data
     * @param boolean true if a new user is stored
     * @param boolean true if user was succesfully stored in the database
     * @param string message
     */
    function onAfterStoreUser($user, $isnew, $succes, $msg)
    {

        $db = &JFactory::getDBO();

        $modifiedDate = gmdate('Y-m-d H:i:s');

        //Split the Joomla username 0 - firstname, 1 - lastname
        $namearray = explode(' ', $user['name']);

        // Look up more detailed information about this user not available from the $user object passed in
        ximport('xprofile');
        $profile = new XProfile();
        $profile->load( $user['username'] );

        $firstname = $profile->get('givenName');
        $lastname = $profile->get('surname');

        // We try to get these from the hub, but sometimes the info isn't there
        // Fallback to standard splitting of name reported by Joomla from jos_user table
        if(empty($firstname) || empty($lastname))
        {
            if(count($namearray) == 2)
            {
                $firstname = $namearray[0];
                $lastname = $namearray[1];
            }
            else
            {
                // middle initial case (hopefully), but this case will also fire for users with spaces in their first or last name.
                // We can't currently account for spaces in fname or lname
                $firstname = $namearray[0];
                $lastname = $namearray[2];
            }

        }


        if(empty($user['email']))
            $email = '<none>';
        else
            $email = $user['email'];

        // See if this person already exsits, onAfterStoreUser fires after every account save, so we need
        // this check cause there is no onAfterCreateUser event
        $personLookup = PersonPeer::findByUserName($user['username']);

        if($personLookup == NULL) //add
        {
            $person = new Person($user['username'], $firstname, $lastname, '', '', $email, 'Other', '', 'First onAfterStoreUser call at ' . $modifiedDate, 0);
            $person->save();
        }
        else
        {
            // save. Stopped doing this for now, until we have the name issue solved,
            // I don't want to potentially update something we've fixed manually and mess stuff up more.
            //$personLookup->setFirstName($firstname);
            //$personLookup->setLastName($lastname);
            //$personLookup->setEMail($email);
            $personLookup->setComment('Last onAfterStoreUser call at ' . $modifiedDate);
            $personLookup->save();
        }

    }

    /**
     * Method is called after user data is deleted from the database
     *
     * @param array holds the user data
     * @param boolean true if user was succesfully stored in the database
     * @param string message
     */
    function onAfterDeleteUser($user, $succes, $msg)
    {

    }



}

?>
