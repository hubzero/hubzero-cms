{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $no_html = \Hubzero\Facades\Request::getInt('no_html', 0);
    $assets = $model->attachments;
@endphp

@if ($assets->count() > 0)
    @foreach ($assets as $i => $asset)
        {!! $__view->view('_asset')
            ->set('i', $i)
            ->set('option', $option)
            ->set('controller', $controller)
            ->set('asset', $asset)
            ->set('no_html', $no_html)
            ->display() !!}
    @endforeach
@endif
