{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 --}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$__view->css()->css('wiki.css')->js();

$html = $page->pagehtml;
$html = str_replace('projects/projects/', 'projects/', $html);
$html = str_replace(
    $page->scope . DS . $page->pagename,
    'wiki/' . $page->id,
    $html
);

$backUrl = Route::url('index.php?option=' . $option . '&id=' . $publication->id);
@endphp

<div class="wiki-wrap">
    <p class="mb-4">
        <a href="{{ $backUrl }}" class="link link-hover">
            {{ Lang::txt('COM_PUBLICATIONS_BACK_TO_PUBLICATION') }}
            &ldquo;{{ $publication->title }}&rdquo;
        </a>
    </p>
    <div class="prose max-w-none">
        <h1>{{ $page->title }}</h1>
        <div class="wikipage">{!! $html !!}</div>
    </div>
</div>
