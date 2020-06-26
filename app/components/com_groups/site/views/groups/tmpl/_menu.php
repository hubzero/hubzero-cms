<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<ul <?php echo $this->classOrId; ?>>
	<?php foreach ($this->sections as $k => $section) : ?>
		<?php
			//do we want to display item in menu?
			if (!$section['display_menu_tab'])
			{
				continue;
			}

			//set some vars
			$access  = $this->pluginAccess[$section['name']];
			$class   = strtolower($section['name']);
			$title   = $section['title'];
			$link    = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=' . $section['name']);
			$liClass = ($this->tab == $section['name']) ? 'active' : '';

			if (!isset($section['icon']))
			{
				$section['icon'] = 'f009';
			}

			//if we are on the overview tab and we have group pages
			if ($section['name'] == 'overview' && count($this->pages) > 0)
			{
				$trueTab = strtolower(Request::getString('active', 'overview'));
				$liClass = ($trueTab != $this->tab) ? '' : $liClass;

				if (($access == 'registered' && User::isGuest()) || ($access == 'members' && !in_array(User::get("id"), $this->group->get('members'))))
				{
					$item  = "<li class=\"protected group-overview-tab\"><span data-icon=\"&#x{$section['icon']};\" class=\"disabled overview\">Overview</span>";
				}
				else
				{
					$item  = "<li class=\"{$liClass} group-overview-tab\">";
					$item .= "<a class=\"overview\" data-icon=\"&#x{$section['icon']};\" title=\"{$this->group->get('description')}'s Overview Page\" href=\"{$link}\">Overview</a>";
				}

				// append pages html
				// only pass in the children of the root node
				// basically skip the overview page here
				$item .= \Components\Groups\Helpers\View::buildRecursivePageMenu($this->group, $this->pages[0]->get('children'));
			}
			else
			{
				if ($access == 'nobody')
				{
					$item = '';
				}
				elseif (!$this->group->get('approved'))
				{
					$item  = '<li class="protected members-only group-' . $class . '-tab" title="' . Lang::txt('This page is restricted until the group has been approved!') . '">';
					$item .= '<span data-icon="&#x' . $section['icon'] . '" class="disabled ' . $class . '">' . $title . '</span>';
					$item .= '</li>';
				}
				elseif ($access == 'members' && !in_array(User::get('id'), $this->group->get('members')))
				{
					$item  = '<li class="protected members-only group-' . $class . '-tab" title="' . Lang::txt('This page is restricted to group members only!') . '">';
					$item .= '<span data-icon="&#x' . $section['icon'] . '" class="disabled ' . $class . '">' . $title . '</span>';
					$item .= '</li>';
				}
				elseif ($access == 'registered' && User::isGuest())
				{
					$item  = '<li class="protected registered-only group-' . $class . '-tab" title="' . Lang::txt('This page is restricted to registered hub users only!') . '">';
					$item .= '<span data-icon="&#x' . $section['icon'] . '" class="disabled ' . $class . '">' . $title . '</span>';
					$item .= '</li>';
				}
				else
				{
					//menu item meta data vars
					$metadata = (isset($this->sectionsContent[$k]['metadata'])) ? $this->sectionsContent[$k]['metadata'] : array();
					$meta_count = (isset($metadata['count']) && $metadata['count'] != '') ? $metadata['count'] : '';
					$meta_alert = (isset($metadata['alert']) && $metadata['alert'] != '') ? $metadata['alert'] : '';

					$cls  = ($meta_count) ? 'hasmeta' : '';
					$cls .= ($meta_alert) ? ' hasalert' : '';

					//create menu item
					$item  = "<li class=\"{$liClass} group-{$class}-tab {$cls}\">";
					$item .= "<a class=\"{$class}\"  data-icon=\"&#x{$section['icon']};\" title=\"{$this->group->get('description')}'s {$title} Page\" href=\"{$link}\">{$title}</a>";
					$item .= '<span class="meta">';
					if ($meta_count)
					{
						$item .= '<span class="count">' . $meta_count . '</span>';
					}
					$item .= '</span>';
					$item .= $meta_alert;

					if (isset($metadata['options']) && is_array($metadata['options']))
					{
						$item .= '<ul class="tab-options">';
						foreach ($metadata['options'] as $option)
						{
							if (!isset($option['text']))
							{
								if (!isset($option['title']))
								{
									continue;
								}
								$option['text'] = $option['title'];
							}

							$attribs = array();
							foreach ($option as $key => $val)
							{
								if ($key == 'text')
								{
									continue;
								}

								$attribs[] = $key . '="' . $this->escape($val) . '"';
							}

							$item .= '<li><a ' . implode(' ', $attribs) . '>' . $this->escape($option['text']) . '</a></li>';
						}
						$item .= '</ul>';
					}

					$item .= '</li>';
				}
			}
			echo $item;
		?>
	<?php endforeach; ?>
</ul>