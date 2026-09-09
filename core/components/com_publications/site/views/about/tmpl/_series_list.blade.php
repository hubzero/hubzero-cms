{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<h4>{{ Lang::txt('COM_PUBLICATIONS_SERIES') }}</h4>
<div>
    <p>{{ Lang::txt('COM_PUBLICATIONS_IS_PART_OF_SERIES') }}</p>
    <ul class="list-disc list-inside">
        @foreach ($series as $seriesData)
            @include('about::_series_item', ['series' => $seriesData])
        @endforeach
    </ul>
</div>
