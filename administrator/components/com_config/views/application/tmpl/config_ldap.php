<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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
