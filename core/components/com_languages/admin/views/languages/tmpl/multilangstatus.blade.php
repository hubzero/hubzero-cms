{{--
  com_languages — Multilingual status dashboard (module-style embed)

  Variables:
    $language_filter (bool), $switchers (int), $homes (int)
    $contentlangs (array), $site_langs (array), $homepages (array)
    $statuses (array of objects with ->element, ->lang_code, ->published, ->home_language)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $notice_homes     = $homes == 2 || ($homes == 1 && ($language_filter || $switchers != 0));
  $notice_disabled  = !$language_filter && ($homes > 1 || $switchers != 0);
  $notice_switchers = !$switchers && ($homes > 1 || $language_filter);
@endphp

<div class="mod-multilangstatus p-4 space-y-4">

  @if (!$language_filter && $switchers == 0)

    <div role="alert" class="alert alert-info">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           class="h-6 w-6 shrink-0 stroke-current">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>
        @if ($homes == 1)
          {{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_NONE') }}
        @else
          {!! Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_USELESS_HOMES') !!}
        @endif
      </span>
    </div>

  @else

    {{-- Warning alerts --}}
    @if ($notice_homes || $notice_disabled || $notice_switchers)
      <div class="space-y-2">
        @if ($notice_homes)
          <div role="alert" class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_MISSING') }}</span>
          </div>
        @endif
        @if ($notice_disabled)
          <div role="alert" class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGUAGEFILTER_DISABLED') }}</span>
          </div>
        @endif
        @if ($notice_switchers)
          <div role="alert" class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGSWITCHER_UNPUBLISHED') }}</span>
          </div>
        @endif

        {{-- Per-language content warnings --}}
        @foreach ($contentlangs as $contentlang)
          @php
            $inHomepages   = array_key_exists($contentlang->lang_code, $homepages);
            $notInSiteLangs = !array_key_exists($contentlang->lang_code, $site_langs);
          @endphp
          @if ($inHomepages && ($notInSiteLangs || !$contentlang->published))
            <div role="alert" class="alert alert-warning">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span>
                {!! Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_ERROR_CONTENT_LANGUAGE', $contentlang->lang_code) !!}
              </span>
            </div>
          @endif
        @endforeach
      </div>
    @endif

    {{-- System status summary --}}
    <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th>{{ Lang::txt('JDETAILS') }}</th>
            <th class="text-center">{{ Lang::txt('JSTATUS') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row" class="font-medium">
              {{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGUAGEFILTER') }}
            </th>
            <td class="text-center">
              @if ($language_filter)
                <span class="badge badge-success badge-sm">{{ Lang::txt('JENABLED') }}</span>
              @else
                <span class="badge badge-error badge-sm">{{ Lang::txt('JDISABLED') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th scope="row" class="font-medium">
              {{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGSWITCHER_PUBLISHED') }}
            </th>
            <td class="text-center">
              @if ($switchers != 0)
                <span class="badge badge-success badge-sm">{{ $switchers }}</span>
              @else
                <span class="badge badge-ghost badge-sm">{{ Lang::txt('JNONE') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th scope="row" class="font-medium">
              @if ($homes > 1)
                {{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED_INCLUDING_ALL') }}
              @else
                {{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED') }}
              @endif
            </th>
            <td class="text-center">
              @if ($homes > 1)
                <span class="badge badge-success badge-sm">{{ $homes }}</span>
              @else
                <span class="badge badge-ghost badge-sm">
                  {{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED_ALL') }}
                </span>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    {{-- Per-language status grid --}}
    <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th>{{ Lang::txt('JGRID_HEADING_LANGUAGE') }}</th>
            <th class="text-center">{{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_SITE_LANG_PUBLISHED') }}</th>
            <th class="text-center">{{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_CONTENT_LANGUAGE_PUBLISHED') }}</th>
            <th class="text-center">{{ Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($statuses as $status)
            @if ($status->element)
              <tr>
                <td>{{ $status->element }}</td>
                {{-- Site language published --}}
                <td class="text-center">
                  <span class="badge badge-success badge-sm" title="{{ Lang::txt('JON') }}">✓</span>
                </td>
                {{-- Content language published --}}
                <td class="text-center">
                  @if ($status->lang_code && $status->published)
                    <span class="badge badge-success badge-sm" title="{{ Lang::txt('JON') }}">✓</span>
                  @else
                    <span class="badge badge-warning badge-sm" title="{{ Lang::txt('WARNING') }}">!</span>
                  @endif
                </td>
                {{-- Homepage published --}}
                <td class="text-center">
                  @if ($status->home_language)
                    <span class="badge badge-success badge-sm" title="{{ Lang::txt('JON') }}">✓</span>
                  @else
                    <span class="badge badge-warning badge-sm" title="{{ Lang::txt('WARNING') }}">!</span>
                  @endif
                </td>
              </tr>
            @endif
          @endforeach

          @foreach ($contentlangs as $contentlang)
            @if (!array_key_exists($contentlang->lang_code, $site_langs))
              <tr>
                <td>{{ $contentlang->lang_code }}</td>
                {{-- Site lang: not found --}}
                <td class="text-center">
                  <span class="badge badge-ghost badge-sm" title="{{ Lang::txt('JNO') }}">–</span>
                </td>
                {{-- Content lang --}}
                <td class="text-center">
                  @if ($contentlang->published)
                    <span class="badge badge-success badge-sm" title="{{ Lang::txt('JON') }}">✓</span>
                  @elseif (!$contentlang->published && array_key_exists($contentlang->lang_code, $homepages))
                    <span class="badge badge-warning badge-sm" title="{{ Lang::txt('WARNING') }}">!</span>
                  @else
                    <span class="badge badge-ghost badge-sm" title="{{ Lang::txt('NOTICE') }}">–</span>
                  @endif
                </td>
                {{-- Homepage --}}
                <td class="text-center">
                  @if (!array_key_exists($contentlang->lang_code, $homepages))
                    <span class="badge badge-ghost badge-sm" title="{{ Lang::txt('NOTICE') }}">–</span>
                  @else
                    <span class="badge badge-success badge-sm" title="{{ Lang::txt('JON') }}">✓</span>
                  @endif
                </td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>

  @endif

</div>
