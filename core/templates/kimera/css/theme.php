<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();

function hex2rgb($hex)
{
	$hex = str_replace('#', '', $hex);

	if (strlen($hex) == 3)
	{
		$r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
		$g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
		$b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
	}
	else
	{
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
	}
	$rgb = array($r, $g, $b);

	return implode(',', $rgb);
}

$tmpl = dirname(__DIR__);
$base = dirname(dirname(dirname($tmpl)));
$tmpl = substr($tmpl, strlen($_SERVER['DOCUMENT_ROOT']));

$styles = '';
switch ($bground)
{
	case 'delauney':
		$opacity = $opacity ?: '0.75';
		$styles .= '
		#outer-wrap {
			background: transparent url("' . $tmpl . '/img/delauney.svg") 0 0 no-repeat;
			background-size: 100% auto;
		}
		@media (max-width: 1000px) {
			#outer-wrap {
				background-size: 1000px auto;
				background-position: 50% 0;
			}
		}
		';
	break;

	case 'triangles':
		$opacity = $opacity ?: '0.75';
		$styles .= '
		#outer-wrap {
			background: #c1c1c1 url("' . $tmpl . '/img/triangles.svg") 0 0;
		}
		@media (max-width: 700px) {
			#outer-wrap {
				background-size: 700px auto;
				background-position: 50% 0;
			}
		}
		';
	break;

	case 'plaid':
		$opacity = $opacity ?: '0.9';
		$styles .='
		#outer-wrap {
			background: -webkit-repeating-linear-gradient(45deg, rgba(0,0,0,0) 5px, rgba(10,35,45,0.498039) 5px, rgba(10,35,45,0.498039) 10px, rgba(211,119,111,0) 10px, rgba(211,119,111,0) 35px, rgba(211,119,111,0.498039) 35px, rgba(211,119,111,0.498039) 40px, rgba(10,35,45,0.498039) 40px, rgba(10,35,45,0.498039) 50px, rgba(10,35,45,0) 50px, rgba(10,35,45,0) 60px, rgba(211,119,111,0.498039) 60px, rgba(211,119,111,0.498039) 70px, rgba(247,179,84,0.498039) 70px, rgba(247,179,84,0.498039) 80px, rgba(247,179,84,0) 80px, rgba(247,179,84,0) 90px, rgba(211,119,111,0.498039) 90px, rgba(211,119,111,0.498039) 110px, rgba(211,119,111,0) 110px, rgba(211,119,111,0) 120px, rgba(10,35,45,0.498039) 120px, rgba(10,35,45,0.498039) 140px), -webkit-repeating-linear-gradient(-45deg, rgba(0,0,0,0) 5px, rgba(10,35,45,0.498039) 5px, rgba(10,35,45,0.498039) 10px, rgba(211,119,111,0) 10px, rgba(211,119,111,0) 35px, rgba(211,119,111,0.498039) 35px, rgba(211,119,111,0.498039) 40px, rgba(10,35,45,0.498039) 40px, rgba(10,35,45,0.498039) 50px, rgba(10,35,45,0) 50px, rgba(10,35,45,0) 60px, rgba(211,119,111,0.498039) 60px, rgba(211,119,111,0.498039) 70px, rgba(247,179,84,0.498039) 70px, rgba(247,179,84,0.498039) 80px, rgba(247,179,84,0) 80px, rgba(247,179,84,0) 90px, rgba(211,119,111,0.498039) 90px, rgba(211,119,111,0.498039) 110px, rgba(211,119,111,0) 110px, rgba(211,119,111,0) 140px, rgba(10,35,45,0.498039) 140px, rgba(10,35,45,0.498039) 160px), rgb(234, 213, 185);
			background: -moz-repeating-linear-gradient(45deg, rgba(0,0,0,0) 5px, rgba(10,35,45,0.498039) 5px, rgba(10,35,45,0.498039) 10px, rgba(211,119,111,0) 10px, rgba(211,119,111,0) 35px, rgba(211,119,111,0.498039) 35px, rgba(211,119,111,0.498039) 40px, rgba(10,35,45,0.498039) 40px, rgba(10,35,45,0.498039) 50px, rgba(10,35,45,0) 50px, rgba(10,35,45,0) 60px, rgba(211,119,111,0.498039) 60px, rgba(211,119,111,0.498039) 70px, rgba(247,179,84,0.498039) 70px, rgba(247,179,84,0.498039) 80px, rgba(247,179,84,0) 80px, rgba(247,179,84,0) 90px, rgba(211,119,111,0.498039) 90px, rgba(211,119,111,0.498039) 110px, rgba(211,119,111,0) 110px, rgba(211,119,111,0) 120px, rgba(10,35,45,0.498039) 120px, rgba(10,35,45,0.498039) 140px), -moz-repeating-linear-gradient(135deg, rgba(0,0,0,0) 5px, rgba(10,35,45,0.498039) 5px, rgba(10,35,45,0.498039) 10px, rgba(211,119,111,0) 10px, rgba(211,119,111,0) 35px, rgba(211,119,111,0.498039) 35px, rgba(211,119,111,0.498039) 40px, rgba(10,35,45,0.498039) 40px, rgba(10,35,45,0.498039) 50px, rgba(10,35,45,0) 50px, rgba(10,35,45,0) 60px, rgba(211,119,111,0.498039) 60px, rgba(211,119,111,0.498039) 70px, rgba(247,179,84,0.498039) 70px, rgba(247,179,84,0.498039) 80px, rgba(247,179,84,0) 80px, rgba(247,179,84,0) 90px, rgba(211,119,111,0.498039) 90px, rgba(211,119,111,0.498039) 110px, rgba(211,119,111,0) 110px, rgba(211,119,111,0) 140px, rgba(10,35,45,0.498039) 140px, rgba(10,35,45,0.498039) 160px), rgb(234, 213, 185);
			background: repeating-linear-gradient(45deg, rgba(0,0,0,0) 5px, rgba(10,35,45,0.498039) 5px, rgba(10,35,45,0.498039) 10px, rgba(211,119,111,0) 10px, rgba(211,119,111,0) 35px, rgba(211,119,111,0.498039) 35px, rgba(211,119,111,0.498039) 40px, rgba(10,35,45,0.498039) 40px, rgba(10,35,45,0.498039) 50px, rgba(10,35,45,0) 50px, rgba(10,35,45,0) 60px, rgba(211,119,111,0.498039) 60px, rgba(211,119,111,0.498039) 70px, rgba(247,179,84,0.498039) 70px, rgba(247,179,84,0.498039) 80px, rgba(247,179,84,0) 80px, rgba(247,179,84,0) 90px, rgba(211,119,111,0.498039) 90px, rgba(211,119,111,0.498039) 110px, rgba(211,119,111,0) 110px, rgba(211,119,111,0) 120px, rgba(10,35,45,0.498039) 120px, rgba(10,35,45,0.498039) 140px), repeating-linear-gradient(135deg, rgba(0,0,0,0) 5px, rgba(10,35,45,0.498039) 5px, rgba(10,35,45,0.498039) 10px, rgba(211,119,111,0) 10px, rgba(211,119,111,0) 35px, rgba(211,119,111,0.498039) 35px, rgba(211,119,111,0.498039) 40px, rgba(10,35,45,0.498039) 40px, rgba(10,35,45,0.498039) 50px, rgba(10,35,45,0) 50px, rgba(10,35,45,0) 60px, rgba(211,119,111,0.498039) 60px, rgba(211,119,111,0.498039) 70px, rgba(247,179,84,0.498039) 70px, rgba(247,179,84,0.498039) 80px, rgba(247,179,84,0) 80px, rgba(247,179,84,0) 90px, rgba(211,119,111,0.498039) 90px, rgba(211,119,111,0.498039) 110px, rgba(211,119,111,0) 110px, rgba(211,119,111,0) 140px, rgba(10,35,45,0.498039) 140px, rgba(10,35,45,0.498039) 160px), rgb(234, 213, 185);
			-webkit-background-origin: padding-box;
			   -moz-background-origin: padding-box;
			        background-origin: padding-box;
			-webkit-background-clip: border-box;
			   -moz-background-clip: border-box;
			        background-clip: border-box;
			-webkit-background-size: auto auto;
			   -moz-background-size: auto auto;
			        background-size: auto auto;
			-webkit-transform-origin: 50% 50% 0;
			   -moz-transform-origin: 50% 50% 0;
			        transform-origin: 50% 50% 0;
		}
		';
	break;

	case 'picnic':
		$opacity = $opacity ?: '0.75';
		$styles .='
		#outer-wrap {
			background: -webkit-linear-gradient(135deg, rgba(0,0,0,0) 25%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.2) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0) 75%, rgba(255,255,255,0.2) 75%), -webkit-linear-gradient(45deg, rgba(0,0,0,0) 25%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.2) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0) 75%, rgba(255,255,255,0.2) 75%), rgb(153, 153, 153);
			background: -moz-linear-gradient(-45deg, rgba(0,0,0,0) 25%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.2) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0) 75%, rgba(255,255,255,0.2) 75%), -moz-linear-gradient(45deg, rgba(0,0,0,0) 25%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.2) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0) 75%, rgba(255,255,255,0.2) 75%), rgb(153, 153, 153);
			background: linear-gradient(-45deg, rgba(0,0,0,0) 25%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.2) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0) 75%, rgba(255,255,255,0.2) 75%), linear-gradient(45deg, rgba(0,0,0,0) 25%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.2) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0) 75%, rgba(255,255,255,0.2) 75%), rgb(153, 153, 153);
			background-position: auto auto;
			-webkit-background-origin: padding-box;
			   -moz-background-origin: padding-box;
			        background-origin: padding-box;
			-webkit-background-clip: border-box;
			   -moz-background-clip: border-box;
			        background-clip: border-box;
			-webkit-background-size: 200px 200px;
			   -moz-background-size: 200px 200px;
			        background-size: 200px 200px;
		}
		';
	break;

	case 'blueprint':
		$opacity = $opacity ?: '0.9';
		$styles .='
		#outer-wrap {
			background: -webkit-linear-gradient(90deg, #FFFFFF 2px, rgba(0,0,0,0) 2px), -webkit-linear-gradient(0deg, #FFFFFF 2px, rgba(0,0,0,0) 2px), -webkit-linear-gradient(90deg, rgba(255,255,255,0.298039) 1px, rgba(0,0,0,0) 1px), -webkit-linear-gradient(0deg, rgba(255,255,255,0.298039) 1px, rgba(0,0,0,0) 1px), rgb(153, 153, 153);
			background: -moz-linear-gradient(0deg, #FFFFFF 2px, rgba(0,0,0,0) 2px), -moz-linear-gradient(90deg, #FFFFFF 2px, rgba(0,0,0,0) 2px), -moz-linear-gradient(0deg, rgba(255,255,255,0.298039) 1px, rgba(0,0,0,0) 1px), -moz-linear-gradient(90deg, rgba(255,255,255,0.298039) 1px, rgba(0,0,0,0) 1px), rgb(153, 153, 153);
			background: linear-gradient(0deg, #FFFFFF 2px, rgba(0,0,0,0) 2px), linear-gradient(90deg, #FFFFFF 2px, rgba(0,0,0,0) 2px), linear-gradient(0deg, rgba(255,255,255,0.298039) 1px, rgba(0,0,0,0) 1px), linear-gradient(90deg, rgba(255,255,255,0.298039) 1px, rgba(0,0,0,0) 1px), rgb(153, 153, 153);
			background-position: -2px -2px, -2px -2px, -1px -1px, -1px -1px;
			-webkit-background-origin: padding-box;
			   -moz-background-origin: padding-box;
			        background-origin: padding-box;
			-webkit-background-clip: border-box;
			   -moz-background-clip: border-box;
			        background-clip: border-box;
			-webkit-background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px;
			   -moz-background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px;
			        background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px;
		}
		';
	break;

	case 'checkered':
		$opacity = $opacity ?: '0.9';
		$styles .='
		#outer-wrap {
			background: -webkit-linear-gradient(135deg, rgba(0,0,0,0) 0, rgba(0,0,0,0) 75%, rgb(200,200,200) 75%, rgb(200,200,200) 100%), -webkit-linear-gradient(45deg, rgb(200,200,200) 0, rgb(200,200,200) 25%, rgba(140,140,140,0) 25%, rgba(255,255,255,0) 100%), -webkit-linear-gradient(135deg, rgb(200,200,200) 0, rgb(200,200,200) 25%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 100%), -webkit-linear-gradient(45deg, rgba(0,0,0,0) 0, rgba(0,0,0,0) 75%, rgb(200,200,200) 75%, rgb(200,200,200) 100%), rgb(255, 255, 255);
			background: -moz-linear-gradient(-45deg, rgba(0,0,0,0) 0, rgba(0,0,0,0) 75%, rgb(200,200,200) 75%, rgb(200,200,200) 100%), -moz-linear-gradient(45deg, rgb(200,200,200) 0, rgb(200,200,200) 25%, rgba(140,140,140,0) 25%, rgba(255,255,255,0) 100%), -moz-linear-gradient(-45deg, rgb(200,200,200) 0, rgb(200,200,200) 25%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 100%), -moz-linear-gradient(45deg, rgba(0,0,0,0) 0, rgba(0,0,0,0) 75%, rgb(200,200,200) 75%, rgb(200,200,200) 100%), rgb(255, 255, 255);
			background: linear-gradient(-45deg, rgba(0,0,0,0) 0, rgba(0,0,0,0) 75%, rgb(200,200,200) 75%, rgb(200,200,200) 100%), linear-gradient(45deg, rgb(200,200,200) 0, rgb(200,200,200) 25%, rgba(140,140,140,0) 25%, rgba(255,255,255,0) 100%), linear-gradient(-45deg, rgb(200,200,200) 0, rgb(200,200,200) 25%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 100%), linear-gradient(45deg, rgba(0,0,0,0) 0, rgba(0,0,0,0) 75%, rgb(200,200,200) 75%, rgb(200,200,200) 100%), rgb(255, 255, 255);
			background-position: 50% 50%;
			-webkit-background-origin: padding-box;
			   -moz-background-origin: padding-box;
			        background-origin: padding-box;
			-webkit-background-clip: border-box;
			   -moz-background-clip: border-box;
			        background-clip: border-box;
			-webkit-background-size: 50px 50px;
			   -moz-background-size: 50px 50px;
			        background-size: 50px 50px;
		}
		';
	break;

	case 'stripes':
		$opacity = $opacity ?: '0.9';
		$styles .='
		#outer-wrap {
			background: -webkit-linear-gradient(45deg, rgba(255,255,255,0.2) 25%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, rgba(0,0,0,0) 75%, rgba(0,0,0,0) 0), rgba(153, 153, 153,1);
			background: -moz-linear-gradient(45deg, rgba(255,255,255,0.2) 25%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, rgba(0,0,0,0) 75%, rgba(0,0,0,0) 0), rgba(153, 153, 153,1);
			background: linear-gradient(45deg, rgba(255,255,255,0.2) 25%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, rgba(0,0,0,0) 75%, rgba(0,0,0,0) 0), rgba(153, 153, 153,1);
			background-position: auto auto;
			-webkit-background-origin: padding-box;
			   -moz-background-origin: padding-box;
			        background-origin: padding-box;
			-webkit-background-clip: border-box;
			   -moz-background-clip: border-box;
			        background-clip: border-box;
			-webkit-background-size: 50px 50px;
			   -moz-background-size: 50px 50px;
			        background-size: 50px 50px;
		}
		';
	break;

	case 'hubbub2015':
		$styles .='
		#outer-wrap {
			background-color: #f7b82b;
			background-color: rgba(248, 180, 45, 1);
			/*background-image: -webkit-linear-gradient(120deg, rgba(255, 255, 255, .6), rgba(255, 255, 255, .6) 40%, rgba(0, 0, 0, 0.1) 40%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0));
			background-image: -moz-linear-gradient(120deg, rgba(255, 255, 255, .6), rgba(255, 255, 255, .6) 40%, rgba(0, 0, 0, 0.1) 40%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0));
			background-image: -ms-linear-gradient(120deg, rgba(255, 255, 255, .6), rgba(255, 255, 255, .6) 40%, rgba(0, 0, 0, 0.1) 40%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0));
			background-image: -o-linear-gradient(120deg, rgba(255, 255, 255, .6), rgba(255, 255, 255, .6) 40%, rgba(0, 0, 0, 0.1) 40%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0));*/
			background-image: linear-gradient(120deg, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0) 40%, rgba(243, 174, 39, 1) 40%, rgba(0, 0, 0, 0) 45%, rgba(0, 0, 0, 0));
			/*-webkit-background-size: 400px 400px;
			   -moz-background-size: 400px 400px;
			    -ms-background-size: 400px 400px;
			     -o-background-size: 650px 1000px;*/
			        background-size: 870px 1000px;
			background-position: 0 0;
			background-repeat: no-repeat;
		}';
	break;

	case 'bokeh':
		$opacity = $opacity ?: '0.7';
		$styles .='
		#outer-wrap {
			background-color: transparent;
			background-image: -webkit-gradient(radial, 50% 50%, 36, 50% 50%, 40,from(rgba(150, 150, 150, 0.2)), color-stop(0.3, rgba(150, 150, 150, 0.3)),  to(transparent)),
							-webkit-gradient(radial, 50% 50%, 16, 50% 50%, 20, from(rgba(203, 203, 203, 0.1)), color-stop(0.2, rgba(203, 203, 203, 0.2)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 16, 50% 50%, 20, from(rgba(203, 203, 203, 0.1)), color-stop(0.2, rgba(203, 203, 203, 0.2)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 38, 50% 50%, 46, from(rgba(113, 113, 113, 0.3)), color-stop(0.3, rgba(113, 113, 113, 0.4)),  to(transparent)),
							-webkit-gradient(radial, 50% 50%, 20, 50% 50%, 80, from(rgba(113, 113, 113, 0)), color-stop(0.3, rgba(113, 113, 113, 0.2)),  to(transparent)),
							-webkit-gradient(radial, 50% 50%, 30, 50% 50%, 90, from(rgba(113, 113, 113, 0)), color-stop(0.5, rgba(113, 113, 113, 0.2)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 30, 50% 50%, 50, from(rgba(50, 50, 50, 0.2)), color-stop(0.2, rgba(50, 50, 50, 0.3)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 16, 50% 50%, 20, from(rgba(50, 50, 50, 0.2)), color-stop(0.2, rgba(50, 50, 50, 0.3)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 26, 50% 50%, 30, from(rgba(50, 50, 50, 0.2)), color-stop(0.2, rgba(50, 50, 50, 0.3)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 36, 50% 50%, 40, from(rgba(50, 50, 50, 0.3)), color-stop(0.2, rgba(50, 50, 50, 0.4)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 30, 50% 50%, 50, from(rgba(209, 209, 209, 0.1)), color-stop(0.2, rgba(209, 209, 209, 0.1)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 16, 50% 50%, 20, from(rgba(209, 209, 209, 0.1)), color-stop(0.2, rgba(209, 209, 209, 0.2)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 26, 50% 50%, 30, from(rgba(209, 209, 209, 0.1)), color-stop(0.2, rgba(209, 209, 209, 0.2)), to(transparent)),
							-webkit-gradient(radial, 50% 50%, 36, 50% 50%, 40, from(rgba(209, 209, 209, 0.2)), color-stop(0.2, rgba(209, 209, 209, 0.3)), to(transparent));
			background-image: -webkit-radial-gradient(circle contain, rgba(150, 150, 150, 0.2) 36px, rgba(150, 150, 150, 0.3) 38px, transparent 40px),
							-webkit-radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							-webkit-radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							-webkit-radial-gradient(circle contain, rgba(113, 113, 113, 0.2) 38px, rgba(113, 113, 113, 0.3) 41px, transparent 46px),
							-webkit-radial-gradient(circle contain, rgba(113, 113, 113, 0) 20px, rgba(113, 113, 113, 0.2) 38px, transparent 80px),
							-webkit-radial-gradient(circle contain, rgba(113, 113, 113, 0) 30px, rgba(113, 113, 113, 0.2) 60px, transparent 90px),
							-webkit-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 30px, rgba(50, 50, 50, 0.3) 34px, transparent 50px),
							-webkit-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 16px, rgba(50, 50, 50, 0.3) 17px, transparent 20px),
							-webkit-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 26px, rgba(50, 50, 50, 0.3) 27px, transparent 30px),
							-webkit-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 36px, rgba(50, 50, 50, 0.3) 37px, transparent 40px),
							-webkit-radial-gradient(circle contain, rgba(209, 209, 209, 0.1) 30px, rgba(209, 209, 209, 0.1) 34px, transparent 50px),
							-webkit-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 16px, rgba(209, 209, 209, 0.3) 17px, transparent 20px),
							-webkit-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 26px, rgba(209, 209, 209, 0.3) 27px, transparent 30px),
							-webkit-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 36px, rgba(209, 209, 209, 0.3) 37px, transparent 40px);
			background-image: -moz-radial-gradient(circle contain, rgba(150, 150, 150, 0.2) 36px, rgba(150, 150, 150, 0.3) 38px, transparent 40px),
							-moz-radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							-moz-radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							-moz-radial-gradient(circle contain, rgba(113, 113, 113, 0.2) 38px, rgba(113, 113, 113, 0.3) 41px, transparent 46px),
							-moz-radial-gradient(circle contain, rgba(113, 113, 113, 0) 20px, rgba(113, 113, 113, 0.2) 38px, transparent 80px),
							-moz-radial-gradient(circle contain, rgba(113, 113, 113, 0) 30px, rgba(113, 113, 113, 0.2) 60px, transparent 90px),
							-moz-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 30px, rgba(50, 50, 50, 0.3) 34px, transparent 50px),
							-moz-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 16px, rgba(50, 50, 50, 0.3) 17px, transparent 20px),
							-moz-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 26px, rgba(50, 50, 50, 0.3) 27px, transparent 30px),
							-moz-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 36px, rgba(50, 50, 50, 0.3) 37px, transparent 40px),
							-moz-radial-gradient(circle contain, rgba(209, 209, 209, 0.1) 30px, rgba(209, 209, 209, 0.1) 34px, transparent 50px),
							-moz-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 16px, rgba(209, 209, 209, 0.3) 17px, transparent 20px),
							-moz-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 26px, rgba(209, 209, 209, 0.3) 27px, transparent 30px),
							-moz-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 36px, rgba(209, 209, 209, 0.3) 37px, transparent 40px);
			background-image: -o-radial-gradient(circle contain, rgba(150, 150, 150, 0.2) 36px, rgba(150, 150, 150, 0.3) 38px, transparent 40px),
							-o-radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							-o-radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							-o-radial-gradient(circle contain, rgba(113, 113, 113, 0.2) 38px, rgba(113, 113, 113, 0.3) 41px, transparent 46px),
							-o-radial-gradient(circle contain, rgba(113, 113, 113, 0) 20px, rgba(113, 113, 113, 0.2) 38px, transparent 80px),
							-o-radial-gradient(circle contain, rgba(113, 113, 113, 0) 30px, rgba(113, 113, 113, 0.2) 60px, transparent 90px),
							-o-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 30px, rgba(50, 50, 50, 0.3) 34px, transparent 50px),
							-o-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 16px, rgba(50, 50, 50, 0.3) 17px, transparent 20px),
							-o-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 26px, rgba(50, 50, 50, 0.3) 27px, transparent 30px),
							-o-radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 36px, rgba(50, 50, 50, 0.3) 37px, transparent 40px),
							-o-radial-gradient(circle contain, rgba(209, 209, 209, 0.1) 30px, rgba(209, 209, 209, 0.1) 34px, transparent 50px),
							-o-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 16px, rgba(209, 209, 209, 0.3) 17px, transparent 20px),
							-o-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 26px, rgba(209, 209, 209, 0.3) 27px, transparent 30px),
							-o-radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 36px, rgba(209, 209, 209, 0.3) 37px, transparent 40px);
			background-image: radial-gradient(circle contain, rgba(150, 150, 150, 0.2) 36px, rgba(150, 150, 150, 0.3) 38px, transparent 40px),
							radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							radial-gradient(circle contain, rgba(203, 203, 203, 0.2) 16px, rgba(203, 203, 203, 0.3) 17px, transparent 20px),
							radial-gradient(circle contain, rgba(113, 113, 113, 0.2) 38px, rgba(113, 113, 113, 0.3) 41px, transparent 46px),
							radial-gradient(circle contain, rgba(113, 113, 113, 0) 20px, rgba(113, 113, 113, 0.2) 38px, transparent 80px),
							radial-gradient(circle contain, rgba(113, 113, 113, 0) 30px, rgba(113, 113, 113, 0.2) 60px, transparent 90px),
							radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 30px, rgba(50, 50, 50, 0.3) 34px, transparent 50px),
							radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 16px, rgba(50, 50, 50, 0.3) 17px, transparent 20px),
							radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 26px, rgba(50, 50, 50, 0.3) 27px, transparent 30px),
							radial-gradient(circle contain, rgba(50, 50, 50, 0.2) 36px, rgba(50, 50, 50, 0.3) 37px, transparent 40px),
							radial-gradient(circle contain, rgba(209, 209, 209, 0.1) 30px, rgba(209, 209, 209, 0.1) 34px, transparent 50px),
							radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 16px, rgba(209, 209, 209, 0.3) 17px, transparent 20px),
							radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 26px, rgba(209, 209, 209, 0.3) 27px, transparent 30px),
							radial-gradient(circle contain, rgba(209, 209, 209, 0.2) 36px, rgba(209, 209, 209, 0.3) 37px, transparent 40px);
			background-size: 593px 483px,
							549px 847px,
							581px 867px,
							683px 559px,
							679px 589px,
							571px 489px,
							647px 481px,
							577px 571px,
							633px 463px,
							763px 549px,
							491px 341px,
							611px 350px,
							439px 593px;
			background-position: 197px 251px,
								313px 499px,
								419px 359px,
								263px 433px,
								449px 197px,
								389px 317px,
								439px 281px,
								541px 263px,
								397px 449px,
								191px 463px,
								271px 271px,
								491px 397px,
								211px 349px;
			box-shadow: inset 0 0 4em rgba(0, 0, 0, 0.4);
		}
		';
	break;

	default:
		if ($bground && $bground != 'none')
		{
			$opacity = $opacity ?: '0';
			$styles .= '
			#outer-wrap {
				background-image: url(' . $bground . ');
				background-size: 100% auto;
				background-position: 0 0;
				background-repeat: no-repeat;
			}
			@media (max-width: 700px) {
				#outer-wrap {
					background-size: 700px auto;
					background-position: 50% 0;
				}
			}
			';
		}
		else
		{
			$opacity = $opacity ?: '1';
		}
	break;
}

$styles .= '
	a,
	a:active,
	a:visited {
		color: ' . ($opacity2 ? 'rgba(' . hex2rgb($color2) . ', ' . $opacity2 . ')' : '#' . $color2) . ';
	}
	a.btn,
	a.btn:active,
	a.btn:visited {
		color: #777;
		border: 2px solid rgba(0,0,0,0.2);
	}
	#top {
		background-color: rgba(' . hex2rgb($color1) . ', ' . $opacity . ');
	}
	#wrap {
		background-color: rgba(' . hex2rgb($color1) . ', ' . $opacity . ');
	}
';

$patterns = array(
	'!/\*[^*]*\*+([^/][^*]*\*+)*/!',  /* remove comments */
	'/[\n\r \t]/',                    /* remove tabs, spaces, newlines, etc. */
	'/ +/'                           /* collapse multiple spaces to a single space */
	/* '/ ?([,:;{}]) ?/'                 remove space before and after , : ; { }     [!] apparently, IE 7 doesn't like this and won't process the stylesheet */
);
$replacements = array(
	'',
	' ',
	' '/*,
	'$1'*/
);
$styles = preg_replace($patterns, $replacements, $styles);

/*$hash = md5($color1 . $bground . $color2);
$path = '/cache/site/' . $hash . '.css';

if (!file_exists(PATH_APP . $path))
{
	@file_put_contents(PATH_APP . $path, $styles);

	if (file_exists(PATH_APP . $path))
	{
		$this->addStyleSheet($this->baseurl . $path);
		return '';
	}
}*/

return $styles;
