{{--
  Mailing Tracking — Statistics view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $openRate   = $recipients > 0 ? number_format(($opens / $recipients) * 100) : 0;
  $bounceRate = $recipients > 0 ? number_format(($bounces / $recipients) * 100) : 0;

  // Prepare geo data for jVectorMap
  $countryGeo = $opensGeo['country'] ?? [];
  $stateGeo   = $opensGeo['state'] ?? [];
  unset($countryGeo['undetermined'], $stateGeo['undetermined']);
  $countryJson = strtoupper(json_encode($countryGeo));
  $stateJson   = strtoupper(json_encode($stateGeo));

  $__view->css();
  $__view->js();
  $__view->js('jvectormap/jquery.jvectormap.min.js', 'system');
  $__view->js('jvectormap/maps/jquery.jvectormap.us.js', 'system');
  $__view->js('jvectormap/maps/jquery.jvectormap.world.js', 'system');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_STATS') }}"
    icon="stats"
    option="{{ $option }}"
/>

@php
  $formAction = Route::url('index.php?option=' . $option, false);
@endphp
<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="item-form">

  {{-- Statistics cards --}}
  <div class="admin-fieldset mb-4">
    <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_STATISTICS') }}</h3>
    <div class="admin-fieldset-body">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <div class="stat bg-base-200 rounded-box p-4">
          <div class="stat-title text-base-content">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENRATE') }}</div>
          <div class="stat-value text-2xl">{{ $openRate }}%</div>
          @if($recipients > 0)
            <div class="stat-desc text-muted-foreground">
              {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENED', $opens, $recipients) }}
            </div>
          @endif
        </div>

        <div class="stat bg-base-200 rounded-box p-4">
          <div class="stat-title text-base-content">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_BOUNCERATE') }}</div>
          <div class="stat-value text-2xl">{{ $bounceRate }}%</div>
        </div>

        <div class="stat bg-base-200 rounded-box p-4">
          <div class="stat-title text-base-content">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_FORWARDS') }}</div>
          <div class="stat-value text-2xl">{{ $forwards }}</div>
        </div>

        <div class="stat bg-base-200 rounded-box p-4">
          <div class="stat-title text-base-content">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_PRINTS') }}</div>
          <div class="stat-value text-2xl">{{ $prints }}</div>
        </div>

      </div>
    </div>
  </div>

  {{-- Opens by location --}}
  <div class="admin-fieldset mb-4">
    <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENS_BY_LOCATION') }}</h3>
    <div class="admin-fieldset-body">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div>
          <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
            <table class="admin-table">
              <thead>
                <tr>
                  <th colspan="2">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS') }}</th>
                  <th>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS_OPENS_COUNT') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($opensGeo['country'] ?? [] as $country => $count)
                  <tr>
                    <td class="w-8">
                      @if($country != 'undetermined')
                        @php
                          $flagSrc = Request::base()
                              . '/core/assets/images/flags/'
                              . strtolower($country) . '.gif';
                        @endphp
                        <img src="{{ $flagSrc }}"
                             alt="{{ $country }}"
                             class="w-5" />
                      @endif
                    </td>
                    <td>{{ strtoupper($country) }}</td>
                    <td>{{ $count }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted-foreground">
                      {{ Lang::txt('COM_NEWSLETTER_NO_DATA') }}
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div class="lg:col-span-2">
          <div id="world-map-data" data-src='{!! $countryJson !!}'></div>
          <div id="us-map-data" data-src='{!! $stateJson !!}'></div>
          <div id="location-map-container">
            <div id="us-map"></div>
            <div id="world-map"></div>
            <div class="jvectormap-world">{{ Lang::txt('COM_NEWSLETTER_WORLD_MAP') }}</div>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- Click-throughs --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS') }}">
      <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_URL') }}</th>
              <th>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_COUNT') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($clicks as $url => $count)
              <tr>
                <td>
                  <a href="{{ $url }}"
                     rel="nofollow"
                     class="link link-primary break-all">{{ $url }}</a>
                </td>
                <td>{{ number_format($count) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="text-center text-muted-foreground">
                  {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_NO_CLICK_THROUGHS') }}
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
  </x-admin-fieldset>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
</form>
