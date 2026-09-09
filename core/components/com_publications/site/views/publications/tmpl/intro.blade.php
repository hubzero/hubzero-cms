{{--
  Publications introduction / landing page.

  Variables from controller:
    $title         — page title
    $option        — component option string
    $results       — recent publications collection
    $best          — popular publications collection
    $config        — component config
    $contributable — whether current user can contribute

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;
  use Hubzero\Utility\Str;

  $__view->css('introduction.css', 'system')
         ->css()
         ->js();

  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
  $browseLabel = Lang::txt('COM_PUBLICATIONS_BROWSE')
      . ' ' . Lang::txt('COM_PUBLICATIONS_PUBLICATIONS');

  $isContributor = !User::isGuest() && $contributable;

  $database = \Hubzero\Facades\App::get('db');
  $pa = new \Components\Publications\Tables\Author($database);
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-ghost" href="{{ $browseUrl }}">{{ $browseLabel }}</a>
  @endslot

  @if ($__view->getError())
    <div class="alert alert-error">
      <p>{{ $__view->getError() }}</p>
    </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-2 {{ $isContributor ? 'lg:grid-cols-3' : '' }} gap-6">
    {{-- Recent Publications --}}
    <div>
      <h3>{{ Lang::txt('COM_PUBLICATIONS_RECENT_PUBLICATIONS') }}</h3>
      @if ($results && count($results) > 0)
        <ul class="list bg-base-100 rounded-box shadow-sm">
          @foreach ($results as $row)
            @php
              $authors = $pa->getAuthors($row->version_id);
              $info = [];
              $info[] = Date::of($row->published_up)->toLocal('d M Y');
              $info[] = $row->cat_name;
              $info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': '
                  . \Components\Publications\Helpers\Html::showContributors($authors, false, true);
              $thumbUrl = Route::url($row->link('thumb'));
              $detailUrl = Route::url('index.php?option=com_publications&id=' . $row->get('id'));
              $pubTitle = Str::truncate(stripslashes($row->get('title')), 100);
              $abstract = stripslashes($row->get('abstract'));
            @endphp
            <li>
              <span class="pub-thumb">
                <img width="40" height="40" src="{{ $thumbUrl }}" alt="" />
              </span>
              <span class="pub-details">
                <a href="{{ $detailUrl }}" title="{{ e($abstract) }}">{{ $pubTitle }}</a>
                <span class="text-sm text-base-content/70">
                  {!! implode(' <span>|</span> ', $info) !!}
                </span>
              </span>
            </li>
          @endforeach
        </ul>
      @else
        <p class="alert alert-info">{{ Lang::txt('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND') }}</p>
      @endif
    </div>

    {{-- Popular Publications --}}
    <div>
      <h3>{{ Lang::txt('COM_PUBLICATIONS_PUPULAR') }}</h3>
      @if ($best && count($best) > 0)
        <ul class="list bg-base-100 rounded-box shadow-sm">
          @foreach ($best as $row)
            @php
              $authors = $pa->getAuthors($row->version_id);
              $info = [];
              $info[] = Date::of($row->published_up)->toLocal('d M Y');
              $info[] = $row->cat_name;
              $info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': '
                  . \Components\Publications\Helpers\Html::showContributors($authors, false, true);
              $thumbUrl = Route::url($row->link('thumb'));
              $detailUrl = Route::url('index.php?option=com_publications&id=' . $row->get('id'));
              $pubTitle = Str::truncate(stripslashes($row->get('title')), 100);
              $abstract = stripslashes($row->get('abstract'));
            @endphp
            <li>
              <span class="pub-thumb">
                <img width="40" height="40" src="{{ $thumbUrl }}" alt="" />
              </span>
              <span class="pub-details">
                <a href="{{ $detailUrl }}" title="{{ e($abstract) }}">{{ $pubTitle }}</a>
                <span class="text-sm text-base-content/70">
                  {!! implode(' <span>|</span> ', $info) !!}
                </span>
              </span>
            </li>
          @endforeach
        </ul>
      @else
        <p class="alert alert-info">{{ Lang::txt('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND') }}</p>
      @endif
    </div>

    {{-- Contribute panel --}}
    @if ($isContributor)
      <div>
        <h3>{{ Lang::txt('COM_PUBLICATIONS_WHO_CAN_SUBMIT') }}</h3>
        <p>{{ Lang::txt('COM_PUBLICATIONS_WHO_CAN_SUBMIT_ANYONE') }}</p>
        @php
          $submitUrl = Route::url('index.php?option=com_publications&task=submit');
        @endphp
        <p>
          <a href="{{ $submitUrl }}" class="btn">
            {{ Lang::txt('COM_PUBLICATIONS_START_PUBLISHING') }} &raquo;
          </a>
        </p>
      </div>
    @endif
  </div>
</x-page-container>
