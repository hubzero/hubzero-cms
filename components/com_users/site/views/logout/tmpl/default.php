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
 * @package   hubzero-cms-joomla
 * @file      components/com_user/views/logout/tmpl/default.php
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright (c) 2010-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

defined('_JEXEC') or die('Restricted access');

JFactory::getApplication()->redirect(
	JRoute::_(
		'index.php?option=com_users&task=user.logout&return=' . $this->return,
		false
	)
);