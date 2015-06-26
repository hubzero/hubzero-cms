<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

return '#login-body #toolbar-box, div.configuration, h2.modal-title, .light .main-navigation {background: ' . $color . ';} .light .main-navigation ul#menu ul {background: ' . $color . ';} .light .main-navigation ul#menu ul li {background-color: rgba(0, 0, 0, 0.2);} .dark #toolbar-box {background:' . $color . ';border-color:' . $color . ';} .dark #toolbar-box .pagetitle h2 {color: #fff;} .dark .toolbar-box li a span:after {text-shadow:-1px -1px 0 ' . $color . ',1px -1px 0 ' . $color . ',-1px 1px 0 ' . $color . ',1px 1px 0 ' . $color . ';}';