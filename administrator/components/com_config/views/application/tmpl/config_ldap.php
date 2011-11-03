<?php
/**
 * HUBzero CMS
 *
 * Copyright (c) 2010-2011 Purdue University
 * All rights reserved.
 *
 * This file is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This file is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * This file incorporates work covered by the following copyright and  
 * permission notice:  
 *
 * @version    $Id: view.php 14401 2010-01-26 14:10:00Z louis $
 * @package    Joomla
 * @subpackage Config
 * @copyright  Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license    GNU/GPL, see LICENSE.php
 *             Joomla! is free software. This version may have been modified pursuant
 *             to the GNU General Public License, and as distributed it includes or
 *             is derivative of works licensed under the GNU General Public License or
 *             other free or open source software licenses.
 *             See COPYRIGHT.php for copyright notices and details.
 *             
 * @package    hubzero-cms-joomla
 * @file       administrator/components/com_config/views/application/tmpl/config_ldap.php
 * @author     Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright  Copyright (c) 2010-2011 Purdue University. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' ); ?>
<fieldset class="adminform">
     <legend><?php echo JText::_( 'LDAP Settings' ); ?></legend>
     <table class="admintable" cellspacing="1">
          <tbody>
          <tr>
               <td width="185" class="key">
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'LDAP Primary Host URI' ); ?>::<?php echo JText::_( 'TIPDTATABASETYPE' ); ?>">
                              <?php echo JText::_( 'LDAP Primary Host URI' ); ?>
                         </span>
               </td>
               <td>
                    <input class="text_area" type="text" name="ldap_primary" size="30" value="<?php echo $row->ldap_primary; ?>" />
               </td>
          </tr>
          <tr>
               <td width="185" class="key">
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'LDAP Secondary Host URI' ); ?>::<?php echo JText::_( 'TIPDTATABASETYPE' ); ?>">
                              <?php echo JText::_( 'LDAP Secondary Host URI' ); ?>
                         </span>
               </td>
               <td>
                    <input class="text_area" type="text" name="ldap_secondary" size="30" value="<?php echo $row->ldap_secondary; ?>" />
               </td>
          </tr>
          <tr>
               <td width="185" class="key">
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'LDAP Base DN' ); ?>::<?php echo JText::_( 'TIPDTATABASETYPE' ); ?>">
                              <?php echo JText::_( 'LDAP Base DN' ); ?>
                         </span>
               </td>
               <td>
                    <input class="text_area" type="text" name="ldap_basedn" size="30" value="<?php echo $row->ldap_basedn; ?>" />
               </td>
          </tr>
          <tr>
               <td width="185" class="key">
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'LDAP Search DN' ); ?>::<?php echo JText::_( 'TIPDTATABASETYPE' ); ?>">
                              <?php echo JText::_( 'LDAP Search DN' ); ?>
                         </span>
               </td>
               <td>
                    <input class="text_area" type="text" name="ldap_searchdn" size="30" value="<?php echo $row->ldap_searchdn; ?>" />
               </td>
          </tr>
          <tr>
               <td width="185" class="key">
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'LDAP Search Password' ); ?>::<?php echo JText::_( 'TIPDTATABASETYPE' ); ?>">
                              <?php echo JText::_( 'LDAP Search Password' ); ?>
                         </span>
               </td>
               <td>
                    <input class="text_area" type="text" name="ldap_searchpw" size="30" value="<?php echo $row->ldap_searchpw; ?>" />
               </td>
          </tr>
          <tr>
               <td width="185" class="key">
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'LDAP Manager DN' ); ?>::<?php echo JText::_( 'TIPDTATABASETYPE' ); ?>">
                              <?php echo JText::_( 'LDAP Manager DN' ); ?>
                         </span>
               </td>
               <td>
                    <input class="text_area" type="text" name="ldap_managerdn" size="30" value="<?php echo $row->ldap_managerdn; ?>" />
               </td>
          </tr>
          <tr>
               <td width="185" class="key">
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'LDAP Manager Password' ); ?>::<?php echo JText::_( 'TIPDTATABASETYPE' ); ?>">
                              <?php echo JText::_( 'LDAP Manager Password' ); ?>
                         </span>
               </td>
               <td>
                    <input class="text_area" type="text" name="ldap_managerpw" size="30" value="<?php echo $row->ldap_managerpw; ?>" />
               </td>
          </tr>
          </tbody>
     </table>
</fieldset>
