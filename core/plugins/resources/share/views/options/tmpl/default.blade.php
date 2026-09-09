{{--
  Share options — social sharing links with popup expansion.

  Variables (from plugin):
    $option   — string: component option
    $resource — object: resource model
    (access $_params via $__view — underscore-prefixed props are filtered from Blade data)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $__view->css()->js();

  $_params = $__view->_params;
  $i = 1;
  $limit = intval($_params->get('icons_limit')) ?: 8;

  $popup = '<ol class="sharelinks">';
  $metadata  = '<div class="share">' . "\n";
  $metadata .= "\t" . Lang::txt('PLG_RESOURCES_SHARE') . ': ';

  $sharing = ['facebook', 'twitter', 'google', 'pinterest', 'linkedin', 'delicious', 'reddit'];

  foreach ($sharing as $shared) {
      if ($_params->get('share_' . $shared, 1)) {
          $link = $__view->view('_item')
              ->set('option', $option)
              ->set('resource', $resource)
              ->set('name', $shared)
              ->loadTemplate();

          $metadata .= (!$limit || $i <= $limit) ? $link : '';

          $popup .= '<li class="' . (($i % 2) ? 'odd' : 'even') . '">' . $link . '</li>';
          $i++;
      }
  }

  if (($i + 2) > $limit) {
      $metadata .= '...';
  }

  $popup .= '</ol>';

  $metadata .= '<dl class="shareinfo">' . "\n";
  $metadata .= "\t<dt>" . Lang::txt('PLG_RESOURCES_SHARE') . "</dt>\n";
  $metadata .= "\t<dd>\n";
  $metadata .= "\t\t<p>\n";
  $metadata .= "\t\t\t" . Lang::txt('PLG_RESOURCES_SHARE_RESOURCE') . "\n";
  $metadata .= "\t\t</p>\n";
  $metadata .= "\t\t<div>\n";
  $metadata .= $popup;
  $metadata .= "\t\t</div>\n";
  $metadata .= "\t</dd>\n";
  $metadata .= "</dl>\n";
  $metadata .= "</div>\n";
@endphp

{!! $metadata !!}
