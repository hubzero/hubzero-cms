{{--
  com_media — Medialist default view (AJAX container for list + thumbs)

  Variables: $folder, $children, $layout, $option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Request;

if (!Request::getInt('no_html') && Request::getWord('format') !== 'raw') {
    $__view->css()->js();
}
@endphp

@include('com_media::admin.views.medialist.tmpl.list', [
    'folder'   => $folder,
    'children' => $children,
    'active'   => ($layout === 'list'),
    'option'   => $option,
])

@include('com_media::admin.views.medialist.tmpl.thumbs', [
    'folder'   => $folder,
    'children' => $children,
    'active'   => ($layout === 'thumbs'),
    'option'   => $option,
])
