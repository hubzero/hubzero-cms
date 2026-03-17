<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<nav aria-label="<?php echo Lang::txt('COM_GROUPS_MENU_LABEL'); ?>">
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
			$ariaCurrent = ($this->tab == $section['name']) ? ' aria-current="page"' : '';

			if (!isset($section['icon']))
			{
				$section['icon'] = 'f009';
			}

			//if we are on the overview tab and we have group pages
			if ($section['name'] == 'overview' && is_array($this->pages) && count($this->pages) > 0)
			{
				$trueTab = strtolower(Request::getString('active', 'overview'));
				$liClass = ($trueTab != $this->tab) ? '' : $liClass;
				$ariaCurrent = ($trueTab == $this->tab && $this->tab == $section['name']) ? ' aria-current="page"' : '';

				if (($access == 'registered' && User::isGuest()) || ($access == 'members' && !in_array(User::get("id"), $this->group->get('members'))))
				{
					$item  = "<li class=\"protected group-overview-tab\"><span data-icon=\"&#x{$section['icon']};\" aria-disabled=\"true\" class=\"disabled overview\">Overview<span class=\"sr-only\"> - " . Lang::txt('COM_GROUPS_MENU_RESTRICTED') . "</span></span>";
				}
				else
				{
					$item  = "<li class=\"{$liClass} group-overview-tab\">";
					$item .= "<a class=\"overview\" data-icon=\"&#x{$section['icon']};\" href=\"{$link}\"{$ariaCurrent}>Overview</a>";
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
					$item  = '<li class="protected members-only group-' . $class . '-tab">';
					$item .= '<span data-icon="&#x' . $section['icon'] . '" aria-disabled="true" class="disabled' . $class . '">' . $title . '<span class="sr-only"> - ' . Lang::txt('COM_GROUPS_MENU_RESTRICTED_APPROVAL') . '</span></span>';
					$item .= '</li>';
				}
				elseif ($access == 'members' && !in_array(User::get('id'), $this->group->get('members')))
				{
					$item  = '<li class="protected members-only group-' . $class . '-tab">';
					$item .= '<span data-icon="&#x' . $section['icon'] . '" aria-disabled="true" class="disabled' . $class . '">' . $title . '<span class="sr-only"> - ' . Lang::txt('COM_GROUPS_MENU_MEMBERS_ONLY') . '</span></span>';
					$item .= '</li>';
				}
				elseif ($access == 'registered' && User::isGuest())
				{
					$item  = '<li class="protected registered-only group-' . $class . '-tab">';
					$item .= '<span data-icon="&#x' . $section['icon'] . '" aria-disabled="true" class="disabled' . $class . '">' . $title . '<span class="sr-only"> - ' . Lang::txt('COM_GROUPS_MENU_REGISTERED_ONLY') . '</span></span>';
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
					$item .= "<a class=\"{$class}\" data-icon=\"&#x{$section['icon']};\" href=\"{$link}\"{$ariaCurrent}>{$title}</a>";
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
</nav>
