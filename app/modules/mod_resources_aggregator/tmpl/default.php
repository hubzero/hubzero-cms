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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="explore">
	<h2><span>explore</span> the freshest and most popular</h2>

	<div class="inner cf">

		<?php

			foreach ($this->featured as $result)
			{
				$res = $result['res'];


				// resolve the css class
				$class = $res->tAlias;
				if (substr($res->tAlias, 0, 4) == 'tool')
				{
					$class = 'tool';
				}
				elseif ($res->tAlias == 'teaching')
				{
					$class = 'instruct';
				}
				elseif ($res->tAlias == 'models')
				{
					$class = 'model';
				}

				if($result['type'] == 'new') {
					// Date
					$published = $res->publish_up;
					//echo $published; die;
					if (strtotime($published) == -62169966000)
					{
						$published = $res->created;
						//die;
					}
					$published = date(("Y-m-d"), strtotime($published));

					$start_date = new DateTime($published);
					$now = new DateTime('now');
					$diff = $now->diff($start_date);

					$since = '';
					if ($diff->y > 1)
					{
						$since = 'more then a year ago';
					}
					elseif ($diff->m > 1)
					{
						$since = $diff->m . ' months ago';
					}
					elseif ($diff->m == 1)
					{
						$since = 'a month ago';
					}
					elseif ($diff->d > 1)
					{
						$since = $diff->d . ' days ago';
					}
					elseif ($diff->d == 1)
					{
						$since = 'a day ago';
					}
					elseif ($diff->h > 1)
					{
						$since = $diff->h . ' hours ago';
					}
					elseif ($diff->h == 1)
					{
						$since = 'an hour ago';
					}
					elseif ($diff->i > 1)
					{
						$since = $diff->i . ' minutes ago';
					}
					else
					{
						$since = 'just now';
					}
				}
				elseif($result['type'] == 'rating') {
					$rating = $res->rating;
				}

				echo '<div class="expoblock ' . $class . '">';
				echo '<a href="' . '/resources/' . $res->id . '"><div class="inn">';
				echo '<p>' . $res->title . '</p>';
				echo '</div></a>';

				if($result['type'] == 'new') {
					echo '<p class="posted">' . $since . ' in <a href="/resources/' . $res->tAlias . '">' . $res->tType . '</a><br>';
				}
				elseif($result['type'] == 'rating') {
					echo '<p class="posted">Rated ' . $rating . ' out of 5 in <a href="/resources/' . $res->tAlias . '">' . $res->tType . '</a><br>';
				}
				echo 'by <a href="/members/' . $res->uidNumber . '">' . $res->name . '</a></p>';

				echo '<div class="go"><a href="/resources/' . $res->id . '">Go</a></div>';
				echo '</div>';
			}

		?>

</div>

</div>
