{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $abstract = $series->abstract;
    $publicationId = $series->publication_id;
    $versionNumber = $series->version_number;
    $title = $series->title;
    $url = Route::url('index.php?option=com_publications&id=' . $publicationId . '&v=' . $versionNumber);
@endphp

@if (!empty($series))
    <li>
        <a href="{{ $url }}" rel="noreferrer noopener" target="_blank">
            <u>{{ e($title) }}</u>
        </a>
        <p>{{ e($abstract) }}</p>
    </li>
@endif
