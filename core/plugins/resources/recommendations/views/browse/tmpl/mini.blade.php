{{--
  Recommendations mini — compact list without sidebar.

  Variables (from plugin):
    $option   — string: component option
    $resource — object: resource model
    $results  — array: recommended resource objects

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
@endphp

<div class="container" id="recommendations">
  <h3>{{ Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_HEADER') }}</h3>

  @if($results)
    <ul>
      @foreach($results as $line)
        @php
          $param = $line->alias ? 'alias=' . $line->alias : 'id=' . $line->id;
          $url = Route::url('index.php?option=' . $option . '&' . $param . '&rec_ref=' . $resource->id);
        @endphp
        <li>
          <a href="{{ $url }}">{{ e(stripslashes($line->title)) }}</a>
        </li>
      @endforeach
    </ul>
  @else
    <p>{{ Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_NO_RESULTS_FOUND') }}</p>
  @endif

  <p id="credits">
    @php $creditsUrl = Request::base(true) . '/about/hubzero#recommendations'; @endphp
    <a href="{{ $creditsUrl }}">{{ Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_POWERED_BY') }}</a>
  </p>
</div>
