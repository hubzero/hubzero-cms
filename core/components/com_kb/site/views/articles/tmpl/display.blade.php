{{--
  Knowledge Base overview — categories with article previews.

  Variables from controller (displayTask):
    $archive — Archive model

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_KB'),
          'index.php?option=' . $option
      );
  }

  Document::setTitle(Lang::txt('COM_KB'));

  $viewLevels = User::getAuthorisedViewLevels();
  $allUrl = Route::url('index.php?option=' . $option . '&section=all', false);
@endphp

<x-page-container :title="Lang::txt('COM_KB')">
  @slot('sidebar')
    @if(Component::isEnabled('com_answers'))
      <x-sidebar-card :title="Lang::txt('COM_KB_COMMUNITY')">
        <p class="text-sm text-base-content/60">
          {{ Lang::txt('COM_KB_COMMUNITY_CANT_FIND') }}
          {!! Lang::txt(
              'COM_KB_COMMUNITY_TRY_ANSWERS',
              '<a class="link" href="' . Route::url('index.php?option=com_answers', false) . '">'
                  . Lang::txt('COM_ANSWERS') . '</a>'
          ) !!}
        </p>
      </x-sidebar-card>
    @endif

    @if(Component::isEnabled('com_wishlist'))
      <x-sidebar-card :title="Lang::txt('COM_KB_FEATURE_REQUEST')">
        <p class="text-sm text-base-content/60">
          {{ Lang::txt('COM_KB_HAVE_A_FEATURE_REQUEST') }}
          <a class="link" href="{{ Route::url('index.php?option=com_wishlist', false) }}">
            {{ Lang::txt('COM_KB_FEATURE_TELL_US') }}
          </a>
        </p>
      </x-sidebar-card>
    @endif

    @if(Component::isEnabled('com_support'))
      <x-sidebar-card :title="Lang::txt('COM_KB_TROUBLE_REPORT')">
        <p class="text-sm text-base-content/60">
          {{ Lang::txt('COM_KB_TROUBLE_FOUND_BUG') }}
          <a class="link"
             href="{{ Route::url('index.php?option=com_support&controller=tickets&task=new', false) }}">
            {{ Lang::txt('COM_KB_TROUBLE_TELL_US') }}
          </a>
        </p>
      </x-sidebar-card>
    @endif
  @endslot

  {{-- Search --}}
  <x-search-bar
      :action="$allUrl"
      query=""
      :placeholder="Lang::txt('COM_KB_SEARCH_PLACEHOLDER')"
      :label="Lang::txt('COM_KB_SEARCH_LABEL')"
      :buttonLabel="Lang::txt('COM_KB_SEARCH')"
      :clearUrl="$allUrl"
      name="search"
  />

  {{-- Popular & Recent Articles --}}
  @php
    $popular = $archive->articles()
        ->whereIn('access', $viewLevels)
        ->whereEquals('state', 1)
        ->order('helpful', 'desc')
        ->limit(5)
        ->rows();

    $recent = $archive->articles()
        ->whereIn('access', $viewLevels)
        ->whereEquals('state', 1)
        ->order('modified', 'desc')
        ->order('created', 'desc')
        ->limit(5)
        ->rows();
  @endphp

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h3 class="card-title text-sm">
          <a class="link link-hover"
             href="{{ Route::url('index.php?option=' . $option . '&section=all&sort=popularity', false) }}">
            {{ Lang::txt('COM_KB_POPULAR_ARTICLES') }}
          </a>
        </h3>
        @if($popular->count() > 0)
          <ul class="space-y-1">
            @foreach($popular as $row)
              <li>
                <a class="link link-hover text-sm"
                   href="{{ Route::url($row->link(), false) }}">
                  {{ $row->get('title') }}
                </a>
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-sm text-base-content/50">{{ Lang::txt('COM_KB_NO_ARTICLES') }}</p>
        @endif
      </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h3 class="card-title text-sm">
          <a class="link link-hover"
             href="{{ Route::url('index.php?option=' . $option . '&section=all&sort=recent', false) }}">
            {{ Lang::txt('COM_KB_RECENT_ARTICLES') }}
          </a>
        </h3>
        @if($recent->count() > 0)
          <ul class="space-y-1">
            @foreach($recent as $row)
              <li>
                <a class="link link-hover text-sm"
                   href="{{ Route::url($row->link(), false) }}">
                  {{ $row->get('title') }}
                </a>
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-sm text-base-content/50">{{ Lang::txt('COM_KB_NO_ARTICLES') }}</p>
        @endif
      </div>
    </div>
  </div>

  {{-- Categories --}}
  @php
    $categories = $archive->categories([
        'state'  => 1,
        'access' => $viewLevels,
    ]);
  @endphp

  <h2 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_KB_CATEGORIES') }}</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($categories as $cat)
      @php
        $catArticles = $cat->articles()
            ->whereEquals('state', 1)
            ->whereIn('access', $viewLevels)
            ->order('modified', 'desc')
            ->order('created', 'desc')
            ->limit(3)
            ->rows();
      @endphp
      @if($catArticles->count() > 0)
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-sm">
              <a class="link link-hover" href="{{ Route::url($cat->link(), false) }}">
                {{ $cat->get('title') }}
              </a>
              <span class="badge badge-sm badge-ghost">{{ $cat->get('articles', 0) }}</span>
            </h3>
            <ul class="space-y-1">
              @foreach($catArticles as $article)
                @php
                  $article->set('calias', $cat->get('path'));
                @endphp
                <li>
                  <a class="link link-hover text-sm"
                     href="{{ Route::url($article->link(), false) }}">
                    {{ $article->get('title') }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif
    @endforeach
  </div>

</x-page-container>
