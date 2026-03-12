{{--
  Event Respondent — Admin detail view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_EVENTS') . ': ' . Lang::txt('COM_EVENTS_RESPONDANT'),
      'user'
  );
@endphp

<h2 class="text-lg font-semibold mb-4">
  {{ $event->title }}
</h2>

<div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
  <table class="admin-table">
    <thead>
      <tr>
        <th colspan="2">{{ Lang::txt('COM_EVENTS_RESPONDENT_DATA') }}</th>
      </tr>
    </thead>
    <tbody>
      @if(!empty($resp->last_name) || !empty($resp->first_name))
        <tr>
          <th class="font-medium w-1/3">{{ Lang::txt('COM_EVENTS_RESPONDANT_NAME') }}</th>
          <td>{{ $resp->last_name }}, {{ $resp->first_name }}</td>
        </tr>
      @endif

      @if(!empty($resp->email))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_EMAIL') }}</th>
          <td>
            <a href="mailto:{{ $resp->email }}" class="link link-hover text-primary">
              {{ $resp->email }}
            </a>
          </td>
        </tr>
      @endif

      @if(!empty($resp->affiliation))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_AFFILIATION') }}</th>
          <td>{{ $resp->affiliation }}</td>
        </tr>
      @endif

      @if(!empty($resp->title))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_TITLE') }}</th>
          <td>
            {{ $resp->title }}
            @if(!empty($resp->position_description))
              — {{ $resp->position_description }}
            @endif
          </td>
        </tr>
      @endif

      @if(!empty($resp->city) || !empty($resp->state) || !empty($resp->zip) || !empty($resp->country))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_LOCATION') }}</th>
          <td>
            {{ $resp->city }}
            {{ $resp->state }}
            {{ $resp->country }}
            {{ $resp->zip }}
          </td>
        </tr>
      @endif

      @if(!empty($resp->telephone) || !empty($resp->fax))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_TELEPHONE') }}</th>
          <td>
            {{ $resp->telephone }}
            @if(!empty($resp->fax))
              {{ $resp->fax }} ({{ Lang::txt('COM_EVENTS_FAX') }})
            @endif
          </td>
        </tr>
      @endif

      @if(!empty($resp->website))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_WEBSITE') }}</th>
          <td>{{ $resp->website }}</td>
        </tr>
      @endif

      @php
        $races = $resp->racial;
      @endphp
      @if(count($races))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_RACE') }}</th>
          <td>
            @php
              $r = [];
              foreach ($races as $race) {
                  $tribal = $race->tribal_affiliation
                      ? ' (' . $race->tribal_affiliation . ')'
                      : '';
                  $r[] = e($race . $tribal);
              }
            @endphp
            {{ implode(', ', $r) }}
          </td>
        </tr>
      @endif

      @if(!empty($resp->gender))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_GENDER') }}</th>
          <td>
            {{ $resp->gender == 'm'
                ? Lang::txt('COM_EVENTS_RESPONDANT_MALE')
                : Lang::txt('COM_EVENTS_RESPONDANT_FEMALE') }}
          </td>
        </tr>
      @endif

      @if(!empty($resp->arrival))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_ARRIVAL') }}</th>
          <td>{{ $resp->arrival }}</td>
        </tr>
      @endif

      @if(!empty($resp->departure))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_DEPARTURE') }}</th>
          <td>{{ $resp->departure }}</td>
        </tr>
      @endif

      <tr>
        <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_DISABILITY_CONTACT_REQUESTED') }}</th>
        <td>
          @if($resp->disability_needs)
            <span class="badge badge-sm badge-info">{{ Lang::txt('COM_EVENTS_RESPONDANT_YES') }}</span>
          @else
            {{ Lang::txt('COM_EVENTS_RESPONDANT_NO') }}
          @endif
        </td>
      </tr>

      @if(!empty($resp->dietary_needs))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_DIETARY_RESTRICTION') }}</th>
          <td>
            <span class="badge badge-sm badge-warning">{{ $resp->dietary_needs }}</span>
          </td>
        </tr>
      @endif

      <tr>
        <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_ATTENDING_DINNER') }}</th>
        <td>
          @if($resp->attending_dinner)
            <span class="badge badge-sm badge-success">{{ Lang::txt('COM_EVENTS_RESPONDANT_YES') }}</span>
          @else
            {{ Lang::txt('COM_EVENTS_RESPONDANT_NO') }}
          @endif
        </td>
      </tr>

      @if(!empty($resp->abstract))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_ABSTRACT') }}</th>
          <td class="whitespace-pre-line">{{ $resp->abstract }}</td>
        </tr>
      @endif

      @if(!empty($resp->comment))
        <tr>
          <th class="font-medium">{{ Lang::txt('COM_EVENTS_RESPONDANT_COMMENT') }}</th>
          <td>{{ $resp->comment }}</td>
        </tr>
      @endif
    </tbody>
  </table>
</div>
