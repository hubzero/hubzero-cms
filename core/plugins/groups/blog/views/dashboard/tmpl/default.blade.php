{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Date;
@endphp

<div class="overflow-x-auto">
    <table class="table table-zebra">
        <tbody>
            @if ($entries)
                @foreach ($entries as $entry)
                    @php
                    $authorUrl = Route::url(
                        'index.php?option=com_members&id=' . $entry->created_by
                    );
                    $dateFormat = Lang::txt('DATE_FORMAT_HZ1')
                        . ' @' . Lang::txt('TIME_FORMAT_HZ1');
                    $entryDate = Date::of($entry->publish_up)->toLocal($dateFormat);
                    @endphp
                    <tr>
                        <th scope="row">{{ $area }}</th>
                        <td>
                            <a class="link link-hover" href="{{ $authorUrl }}">
                                {{ stripslashes($name) }}
                            </a>
                        </td>
                        <td>{{ stripslashes($entry->title) }}</td>
                        <td>{{ $entryDate }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
