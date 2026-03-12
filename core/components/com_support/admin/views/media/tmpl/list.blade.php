{{--
  Support — Media attachment list partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Request;

  $no_html = Request::getInt('no_html', 0);

  $assets = $model->attachments('list', [
      'ticket'     => $ticket,
      'comment_id' => $comment,
  ]);
@endphp

@if($assets->total() > 0)
    @foreach($assets as $i => $asset)
        @include('com_support::admin/views/media/tmpl/_asset', [
            'i'          => $i,
            'option'     => $option,
            'controller' => $controller,
            'asset'      => $asset,
            'no_html'    => $no_html,
        ])
    @endforeach
@endif
