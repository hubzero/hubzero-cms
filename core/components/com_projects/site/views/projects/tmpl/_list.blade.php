{{--
 * Project list — loops through rows rendering _item partial
 *
 * Variables:
 *   $rows    - Collection of project model objects
 *   $option  - Component option string
 *   $filters - Active filters array
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div class="flex flex-col gap-4">
    @foreach ($rows as $row)
        @if ($row->get('owned_by_group') && !$row->groupOwner())
            @continue
        @endif

        @include('projects::_item', [
            'option'  => $option,
            'filters' => $filters,
            'row'     => $row,
        ])
    @endforeach
</div>
