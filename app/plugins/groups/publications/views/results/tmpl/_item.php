<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
// No direct access
defined('_HZEXEC_') or die();
$database = App::get('db');
// Instantiate a helper object
//$RE = new \Components\Resources\Helpers\Helper($this->row->id, $database);
//$RE->getContributors();
// Get the component params and merge with resource params
$config = Component::params('com_publications');
$rparams = new \Hubzero\Config\Registry($this->row->params);
$params = $config;
$params->merge($rparams);
// Set the display date
$thedate = '';
switch ($params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = Date::of($this->row->created)->toLocal('d M Y');    break;
	case 2: $thedate = Date::of($this->row->modified)->toLocal('d M Y');   break;
	//case 3: $thedate = Date::of($this->row->publish_up)->toLocal('d M Y'); break;
}
if (strstr($this->row->href, 'index.php'))
{
	$this->row->href = Route::url($this->row->href);  //where href is set
	
}
switch ($this->row->access)
{
	case 1: $cls = 'registered'; break;
	case 2: $cls = 'special';    break;
	case 3: $cls = 'protected';  break;
	case 4: $cls = 'private';    break;
	case 0:
	default: $cls = 'public'; break;
}
?>

<li class="<?php echo $cls; ?> resource">
	<p class="title"><a href="<?php echo $this->row->href; ?>"><?php echo $this->escape(stripslashes($this->row->title)); ?></a></p>


	<?php if ($params->get('show_rating')) { ?>
		<?php
		switch ($this->row->rating)
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}
		?>
		<div class="metadata">
			<p class="rating"><span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_OUT_OF_5_STARS', $this->row->rating); ?></span>&nbsp;</span></p>
		</div>
	<?php } ?>


	<p class="href"><?php echo Request::base() . ltrim($this->row->href, '/'); ?></p>
</li>