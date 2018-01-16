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

// no direct access
defined('_HZEXEC_') or die;

$namesMap = array('blog' => 'Blog', 'collection' => 'Collections', 'group' => 'Groups', 'question' => 'Questions and Answers', 'project' => 'Projects', 'discussion' => 'Forum');

?>


<div class="explore">
	<h2><span>explore</span> the freshest and most popular</h2>
	
	<div class="inner cf">
	
		<?php
		
			foreach ($this->featured as $result)
			{
				$res = $result['item'];
				
				
				// resolve the css class
				$class = $result['type'];
				
				$since = '';
				if(!empty($res->publish_up)) {
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
				
				$url = '';
				if($class == 'group') {
					$base = '/groups/';
					$url .= '/groups/' . $res->cn;
				}
				elseif($class == 'collection') {
					$base = '/collections/';
					if($res->object_type == 'member') {
						$url .= '/members/' . $res->object_id . '/collections/' . $res->alias;	
					}
					else {
						$url .= '/groups/' . $res->group_alias . '/collections/' . $res->alias;
					}
				}
				elseif($class == 'discussion') {
					$base = '/forum/';
					if($res->scope == 'group') {
						$url .= '/groups/' . $res->cn . '/forum/' . $res->s_alias . '/' . $res->c_alias . '/' . $res->id;
					}
					else {
						$url .= '/forum/' . $res->s_alias . '/' . $res->c_alias . '/' . $res->id;
					}
				}
				elseif($class == 'project') {
					$base = '/projects/';
					$url = '/projects/' . $res->alias;
				}
				elseif($class == 'blog') {
					$base = '/blog/';
					if($res->scope == 'site') {
						$url .= '/blog/' . Date::of($res->publish_up)->toLocal('Y') . '/' . Date::of($this->get('publish_up'))->toLocal('m') . '/' . $res->alias;
					}
					elseif($res->scope == 'group') {
						$url .= 'groups/' . $res->cn . '/blog/' . Date::of($res->publish_up)->toLocal('Y') . '/' . Date::of($this->get('publish_up'))->toLocal('m') . '/' . $res->alias;	
					}
					elseif($res->scope == 'member') {
						$url .= 'members/' . $res->created_by . '/blog/' . Date::of($res->publish_up)->toLocal('Y') . '/' . Date::of($this->get('publish_up'))->toLocal('m') . '/' . $res->alias;	
					}
				}
				elseif($class == 'question') {
					$base = '/answers/';
					$url = '/answers/question/' . $res->id;
				}
				
				echo '<div class="expoblock ' . $class . '">';
					echo '<a href="' . $url . '"><div class="inn">';
						echo '<p>' . strip_tags($res->title) . '</p>';
					echo '</div></a>';
				
					if(!empty($since)) {
						echo '<p class="posted">Last activity: ' . $since . '<br>';
					}
					
					if($class == 'group') {
						echo '<p class="posted">' . $res->members . ' member' . ($res->members == 1 ? '': 's') . '<br>';
					}
					elseif($class == 'collection') {
						echo '<p class="posted">' . $res->followers . ' follower' . ($res->followers == 1 ? '': 's') . '<br>';
					}
					
					echo 'in <a href="' . $base . '">' . $namesMap[$class]. '</a></p>';
					
					echo '<div class="go"><a href="' . $url . '">Go</a></div>';
				echo '</div>'; 
			}
		
		?>
		
	</div>
	
</div>
