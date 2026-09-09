{{--
  Canonical/newer version notice banner.

  Variables:
    $option — component option string
    $model  — Entry model

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $canonical = $model->attribs->get('canonical', '');
@endphp

@if($canonical)
  @php
    $title = $canonical;
    $url   = $canonical;

    if (preg_match('/^(\/?resources\/(.+))/i', $canonical, $matches)) {
        $canonModel = \Components\Resources\Models\Entry::getInstance($matches[2]);
        $title = $canonModel->title;
        $url   = Route::url($canonModel->link());
    } elseif (is_numeric($canonical)) {
        $canonModel = \Components\Resources\Models\Entry::getInstance(intval($canonical));
        $title = $canonModel->title;
        $url   = Route::url($canonModel->link());
    }

    if (!preg_match('/^(https?:|mailto:|ftp:|gopher:|news:|file:|rss:)/i', $url)) {
        $url = rtrim(Request::base(), '/') . '/' . ltrim($url, '/');
    }
  @endphp
  <div role="alert" class="alert alert-info mt-4">
    <span>
      <strong>{{ Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL') }}</strong>
      {{ Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL_EXTENDED') }}
      <a class="link" href="{{ $url }}">{{ $title }}</a>
    </span>
  </div>
@endif
