{{--
  Citations — single citation detail page.

  Variables from controller (viewTask):
    $citation      — Citation model
    $citationType  — Type model for this citation
    $associations  — Query builder for resource associations
    $sponsors      — Citation sponsors collection
    $config        — Component configuration (Registry)
    $openUrl       — OpenURL data (disabled)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_CITATIONS'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      Lang::txt('COM_CITATIONS_BROWSE'),
      'index.php?option=' . $option . '&task=browse'
  );
  Pathway::append(
      Illuminate\Support\Str::limit($citation->title, 50),
      'index.php?option=' . $option . '&task=view&id=' . $citation->id
  );

  Document::setTitle(
      Lang::txt('COM_CITATIONS_CITATION') . ': '
      . Illuminate\Support\Str::limit($citation->title, 50)
  );

  $type = $citationType;
  $canEdit = User::get('id') == $citation->uid;

  // URL resolution
  $urlSeparator = PHP_EOL;
  $rawUrl = $citation->url ?? '';
  if (strstr($rawUrl, ' ') !== false) {
      $urlSeparator = ' ';
  } elseif (strstr($rawUrl, "\t") !== false) {
      $urlSeparator = "\t";
  }
  $urls = array_map('trim', explode($urlSeparator, html_entity_decode($rawUrl)));
  $citationUrl = (filter_var($urls[0] ?? '', FILTER_VALIDATE_URL)) ? $urls[0] : '';

  // E-print
  $eprints = array_map('trim', explode(PHP_EOL, html_entity_decode($citation->eprint ?? '')));
  $eprintUrl = (filter_var($eprints[0] ?? '', FILTER_VALIDATE_URL)) ? $eprints[0] : '';

  // Custom URL
  $customUrl = '';
  $urlFormat = $config->get('citation_url', 'url');
  $urlFormatStr = $config->get('citation_custom_url', '');
  if ($urlFormatStr != '') {
      preg_match_all('/\{(\w+)\}/', $urlFormatStr, $matches, PREG_SET_ORDER);
      if ($matches) {
          foreach ($matches as $match) {
              $field = strtolower($match[1]);
              if (property_exists($citation, $field) && $citation->$field) {
                  if (strstr($citation->$field, 'http')) {
                      $customUrl = $citation->$field;
                  } else {
                      $customUrl = str_replace($match[0], $citation->$field, $urlFormatStr);
                  }
              }
          }
      }
  }
  $finalUrl = ($urlFormat == 'custom' && $customUrl) ? $customUrl : $citationUrl;
  $finalUrl = ($eprintUrl) ? $eprintUrl : $finalUrl;

  // Download URLs
  $bibtexUrl = Route::url(
      'index.php?option=' . $option . '&task=download'
      . '&citationFormat=bibtex&id=' . $citation->id . '&no_html=1', false
  );
  $endnoteUrl = Route::url(
      'index.php?option=' . $option . '&task=download'
      . '&citationFormat=endnote&id=' . $citation->id . '&no_html=1', false
  );

  // Authors
  $authorList = [];
  if ($citation->author) {
      $authors = array_map('trim', explode(';', $citation->author));
      foreach ($authors as $author) {
          preg_match('/\{\{(.*?)\}\}/s', $author, $matches);
          if (!empty($matches) && is_numeric($matches[1])) {
              $user = User::getInstance($matches[1]);
              if (is_object($user) && $user->get('id')) {
                  $memberUrl = Route::url('index.php?option=com_members&id=' . $matches[1], false);
                  $authorName = trim(str_replace($matches[0], '', $author));
                  $authorList[] = ['name' => $authorName, 'url' => $memberUrl];
              } else {
                  $authorList[] = ['name' => $author, 'url' => ''];
              }
          } else {
              $authorList[] = ['name' => $author, 'url' => ''];
          }
      }
  }

  // Associations
  $assocLinks = [];
  foreach ($associations as $a) {
      $assocLinks[] = $a;
  }
@endphp

<x-page-container :title="Lang::txt('COM_CITATIONS')">
  @slot('actions')
    <a class="btn"
       href="{{ Route::url('index.php?option=' . $option . '&task=browse', false) }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
      </svg>
      {{ Lang::txt('COM_CITATIONS_BROWSE') }}
    </a>
    @if($canEdit)
      <a class="btn"
         href="{{ Route::url('index.php?option=' . $option . '&task=edit&id=' . $citation->id, false) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" />
        </svg>
        {{ Lang::txt('JACTION_EDIT') }}
      </a>
    @endif
  @endslot

  @slot('sidebar')
    {{-- External link --}}
    @if($finalUrl)
      <x-sidebar-card>
        <a class="btn btn-primary w-full"
           href="{{ $finalUrl }}"
           rel="external">
          {{ Lang::txt('COM_CITATIONS_VIEW_ARTICLE') }}
        </a>
      </x-sidebar-card>
    @endif

    {{-- Download --}}
    <x-sidebar-card :title="Lang::txt('COM_CITATIONS_EXPORT_MULTIPLE')">
      <div class="flex flex-col gap-2">
        <a class="btn btn-sm btn-ghost justify-start"
           href="{{ $bibtexUrl }}">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
          </svg>
          {{ Lang::txt('COM_CITATIONS_DOWNLOAD_BIBTEX') }}
        </a>
        <a class="btn btn-sm btn-ghost justify-start"
           href="{{ $endnoteUrl }}">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
          </svg>
          {{ Lang::txt('COM_CITATIONS_DOWNLOAD_ENDNOTE') }}
        </a>
      </div>
    </x-sidebar-card>

    {{-- Sponsors --}}
    @if(count($sponsors) > 0)
      <x-sidebar-card :title="Lang::txt('COM_CITATIONS_SPONSORED_BY')">
        <ul class="space-y-1">
          @foreach($sponsors as $s)
            <li>
              <a class="link link-hover text-sm" rel="external"
                 href="{{ $s->link }}">
                {{ $s->sponsor }}
              </a>
            </li>
          @endforeach
        </ul>
      </x-sidebar-card>
    @endif
  @endslot

  {{-- Citation header --}}
  <article>
    <h2 class="text-xl font-bold mb-2">{{ $citation->title }}</h2>

    {{-- Authors --}}
    @if(count($authorList) > 0)
      <p class="text-base-content/70 mb-4">
        {{ Lang::txt('COM_CITATIONS_BY') }}:
        @foreach($authorList as $i => $a)
          @if($i > 0), @endif
          @if($a['url'])
            <a class="link link-hover" href="{{ $a['url'] }}">{{ $a['name'] }}</a>
          @else
            {{ $a['name'] }}
          @endif
        @endforeach
      </p>
    @endif

    {{-- Type badge --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
      <a class="badge badge-primary"
         href="{{ Route::url('index.php?option=' . $option . '&task=browse&type=' . $type->id, false) }}">
        {{ $type->type_title }}
      </a>
      @if($citation->year)
        <span class="badge badge-ghost">{{ $citation->year }}</span>
      @endif
      @if($citation->affiliated)
        <span class="badge badge-outline">{{ Lang::txt('COM_CITATIONS_AFFILIATED') }}</span>
      @endif
    </div>

    {{-- Abstract --}}
    @if($citation->abstract)
      <div class="prose max-w-none mb-6">
        <h3 class="text-base font-semibold">{{ Lang::txt('COM_CITATIONS_ABSTRACT') }}</h3>
        <p>{{ $citation->abstract }}</p>
      </div>
    @endif

    {{-- Metadata table --}}
    <div class="overflow-x-auto mb-6">
      <table class="table table-sm">
        <tbody>
          @if($citation->journal)
            <tr>
              <th class="w-40">{{ Lang::txt('COM_CITATIONS_JOURNAL') }}</th>
              <td>{{ $citation->journal }}</td>
            </tr>
          @endif
          @if($citation->booktitle)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_BOOK_TITLE') }}</th>
              <td>{{ $citation->booktitle }}</td>
            </tr>
          @endif
          @if($citation->publisher)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_PUBLISHER') }}</th>
              <td>{{ $citation->publisher }}</td>
            </tr>
          @endif
          @if($citation->editor)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_EDITORS') }}</th>
              <td>{{ $citation->editor }}</td>
            </tr>
          @endif
          @if($citation->volume)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_VOLUME') }}</th>
              <td>{{ $citation->volume }}</td>
            </tr>
          @endif
          @if($citation->number)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_ISSUE') }}</th>
              <td>{{ $citation->number }}</td>
            </tr>
          @endif
          @if($citation->pages)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_PAGES') }}</th>
              <td>{{ $citation->pages }}</td>
            </tr>
          @endif
          @if($citation->month)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_MONTH') }}</th>
              <td>{{ $citation->month }}</td>
            </tr>
          @endif
          @if($citation->isbn)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_ISBN') }}</th>
              <td>{{ $citation->isbn }}</td>
            </tr>
          @endif
          @if($citation->doi)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_DOI') }}</th>
              <td>
                <a class="link link-hover"
                   href="https://doi.org/{{ $citation->doi }}"
                   rel="external">
                  {{ $citation->doi }}
                </a>
              </td>
            </tr>
          @endif
          @if($citation->series)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_SERIES') }}</th>
              <td>{{ $citation->series }}</td>
            </tr>
          @endif
          @if($citation->edition)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_EDITION') }}</th>
              <td>{{ $citation->edition }}</td>
            </tr>
          @endif
          @if($citation->school)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_SCHOOL') }}</th>
              <td>{{ $citation->school }}</td>
            </tr>
          @endif
          @if($citation->institution)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_INSTITUTION') }}</th>
              <td>{{ $citation->institution }}</td>
            </tr>
          @endif
          @if($citation->address)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_ADDRESS') }}</th>
              <td>{{ $citation->address }}</td>
            </tr>
          @endif
          @if($citation->location)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_LOCATION') }}</th>
              <td>{{ $citation->location }}</td>
            </tr>
          @endif
          @if($citation->organization)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_ORGANIZATION') }}</th>
              <td>{{ $citation->organization }}</td>
            </tr>
          @endif
          @if($citation->language)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_LANGUAGE') }}</th>
              <td>{{ $citation->language }}</td>
            </tr>
          @endif
          @if($citation->keywords)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_KEYWORDS') }}</th>
              <td>{{ $citation->keywords }}</td>
            </tr>
          @endif
          @if($citation->note)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_NOTES') }}</th>
              <td>{!! nl2br(e($citation->note)) !!}</td>
            </tr>
          @endif
          @if($citation->url && $citation->url !== $finalUrl)
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_URL') }}</th>
              <td>
                <a class="link link-hover break-all"
                   href="{{ $citation->url }}"
                   rel="external">
                  {{ $citation->url }}
                </a>
              </td>
            </tr>
          @endif
          @php
            $profile = $citation->uid
                ? User::getInstance($citation->uid)
                : null;
          @endphp
          @if($profile && $profile->get('id'))
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_SUBMITTED_BY') }}</th>
              <td>
                <a class="link link-hover"
                   href="{{ Route::url('index.php?option=com_members&id=' . $profile->get('id'), false) }}">
                  {{ $profile->get('name') }}
                </a>
              </td>
            </tr>
          @endif
          @if($citation->created && $citation->created != '0000-00-00 00:00:00')
            <tr>
              <th>{{ Lang::txt('COM_CITATIONS_SUBMITTED') }}</th>
              <td>
                <time datetime="{{ $citation->created }}">
                  {{ date('F j, Y', strtotime($citation->created)) }}
                </time>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    {{-- Associated resources --}}
    @if(count($assocLinks) > 0)
      <section class="mb-6">
        <h3 class="text-base font-semibold mb-2">
          {{ Lang::txt('COM_CITATIONS_CITED') }}
        </h3>
        <p class="text-sm text-base-content/60 mb-2">
          {{ Lang::txt('COM_CITATIONS_CITED_DESC') }}
        </p>
        <ul class="list-disc list-inside text-sm">
          @foreach($assocLinks as $a)
            <li>
              <a class="link link-hover" href="{{ $a->link() }}">
                {{ $a->title }}
              </a>
            </li>
          @endforeach
        </ul>
      </section>
    @endif

    {{-- Find this text --}}
    @if($citation->doi)
      <section class="border-t border-base-300 pt-4">
        <h3 class="text-base font-semibold mb-2">
          {{ Lang::txt('COM_CITATIONS_FINDTHISTEXT') }}
        </h3>
        <ul class="space-y-1 text-sm">
          <li>
            <span class="font-medium">{{ Lang::txt('COM_CITATIONS_DOI_RESOLVER') }}:</span>
            <a class="link link-hover"
               href="https://doi.org/{{ $citation->doi }}"
               rel="external">
              https://doi.org/{{ $citation->doi }}
            </a>
          </li>
          <li>
            <span class="font-medium">{{ Lang::txt('COM_CITATIONS_GOOGLE_SCHOLAR') }}:</span>
            <a class="link link-hover"
               href="https://scholar.google.com/scholar?q={{ urlencode($citation->doi ?: $citation->title) }}"
               rel="nofollow external">
              {{ Lang::txt('COM_CITATIONS_SEARCH') }}
            </a>
          </li>
        </ul>
      </section>
    @endif
  </article>

</x-page-container>
