{{--
  Thread display — posts/replies with likes, attachments, comment form.

  Variables from controller (displayTask):
    $thread   — Post model (the parent thread)
    $category — Category model
    $section  — Section model
    $filters  — Array with limit, start, section, category, parent, thread, state, access
    $config   — Component params (Registry)
    $forum    — Manager model
    $likes    — Array of like objects (threadId, postId, userId, userName, userEmail)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();
  $__view->js();

  $category->set('section_alias', $filters['section']);
  $thread->set('section', $filters['section']);
  $thread->set('category', $category->get('alias'));

  $categoryUrl = Route::url($category->link(), false);
  $threadUrl   = Route::url($thread->link(), false);

  $threading = $config->get('threading', 'list');

  $posts = $thread->thread()
      ->whereIn('state', $filters['state'])
      ->whereIn('access', $filters['access'])
      ->order(($threading == 'tree' ? 'lft' : 'id'), 'asc')
      ->paginated()
      ->rows();

  $pageNav = $posts->pagination;

  if ($threading == 'tree' && $posts->count() > 0) {
      $posts = $thread->toTree($posts);
  }

  $now = Date::of('now')->toSql();
@endphp

<x-page-container :title="e(stripslashes($thread->get('title')))">
  @slot('actions')
    <a class="btn" href="{{ $categoryUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
      </svg>
      {{ Lang::txt('COM_FORUM_ALL_DISCUSSIONS') }}
    </a>
  @endslot

  @slot('sidebar')
    {{-- Tags --}}
    <x-sidebar-card :title="Lang::txt('COM_FORUM_ALL_TAGS')">
      @if($thread->tags('cloud'))
        {!! $thread->tags('cloud') !!}
      @else
        <p class="text-sm opacity-50">{{ Lang::txt('COM_FORUM_NONE') }}</p>
      @endif
    </x-sidebar-card>

    {{-- Participants --}}
    @php
      $participants = $thread->participants()
          ->whereIn('state', $filters['state'])
          ->whereIn('access', $filters['access'])
          ->rows();
    @endphp
    @if($participants->count() > 0)
      <x-sidebar-card :title="Lang::txt('COM_FORUM_PARTICIPANTS')">
        <ul class="space-y-1">
          @php $anon = false; @endphp
          @foreach($participants as $participant)
            @if(!$participant->get('anonymous'))
              <li>
                <a class="link link-hover text-sm"
                   href="{{ Route::url('index.php?option=com_members&id=' . $participant->get('created_by'), false) }}">
                  {{ e(stripslashes($participant->get('name'))) }}
                </a>
              </li>
            @elseif(!$anon)
              @php $anon = true; @endphp
              <li class="text-sm opacity-60">{{ Lang::txt('JANONYMOUS') }}</li>
            @endif
          @endforeach
        </ul>
      </x-sidebar-card>
    @endif

    {{-- Attachments --}}
    @php
      $attachments = \Components\Forum\Models\Attachment::all()
          ->whereEquals('parent', $thread->get('thread'))
          ->whereIn('state', $filters['state'])
          ->rows();
    @endphp
    @if($attachments->count() > 0)
      <x-sidebar-card :title="Lang::txt('COM_FORUM_ATTACHMENTS')">
        <ul class="space-y-1">
          @foreach($attachments as $attachment)
            @if($attachment->get('status') != 2)
              @php
                $attTitle = trim($attachment->get('description', $attachment->get('filename')));
                $attTitle = $attTitle ?: $attachment->get('filename');
                $attTitle = (strlen($attTitle) > 25) ? substr($attTitle, 0, 22) . '...' : $attTitle;
                $attUrl = Route::url(
                    $thread->link() . '&post=' . $attachment->get('post_id')
                    . '&file=' . $attachment->get('filename'),
                    false
                );
              @endphp
              <li>
                <a class="link link-hover text-sm" href="{{ $attUrl }}">
                  {{ e(stripslashes($attTitle)) }}
                </a>
              </li>
            @endif
          @endforeach
        </ul>
      </x-sidebar-card>
    @endif
  @endslot

  {{-- Posts --}}
  @if($posts->count() > 0)
    {!! $__view->view('_list')
         ->set('comments', $posts)
         ->set('thread', $thread)
         ->set('likes', $likes)
         ->set('parent', 0)
         ->set('config', $config)
         ->set('depth', 0)
         ->set('cls', 'odd')
         ->set('filters', $filters)
         ->set('category', $category)
         ->loadTemplate() !!}
  @else
    <ol class="comments">
      <li><p>{{ Lang::txt('COM_FORUM_NO_REPLIES_FOUND') }}</p></li>
    </ol>
  @endif

  {{-- Pagination --}}
  @php
    $pageNav->setAdditionalUrlParam('section', $filters['section']);
    $pageNav->setAdditionalUrlParam('category', $category->get('alias'));
    $pageNav->setAdditionalUrlParam('thread', $thread->get('id'));
  @endphp
  {!! $pageNav !!}

  {{-- Comment form --}}
  @if(!$thread->get('closed'))
    <h3 class="text-lg font-semibold mt-8 mb-4">{{ Lang::txt('COM_FORUM_ADD_COMMENT') }}</h3>

    <form action="{{ $threadUrl }}" method="post" id="commentform"
          enctype="multipart/form-data">
      <div class="flex gap-4">
        <div class="shrink-0">
          <div class="avatar">
            <div class="w-10 rounded-full">
              <img src="{{ User::picture(User::isGuest() ? 1 : 0) }}"
                   alt="{{ Lang::txt('COM_FORUM_USER_PHOTO') }}" />
            </div>
          </div>
        </div>

        <div class="flex-1 space-y-4">
          @if(User::isGuest())
            <x-auth-gate
                :returnUrl="Route::url($thread->link(), false)"
                message="{{ Lang::txt('COM_FORUM_LOGIN_COMMENT_NOTICE') }}" />
          @elseif($config->get('access-create-post'))
            <p class="text-sm">
              <strong>
                <a href="{{ Route::url('index.php?option=com_members&id=' . User::get('id'), false) }}">
                  {{ e(stripslashes(User::get('name'))) }}
                </a>
              </strong>
            </p>

            {{-- Comment editor --}}
            <div class="form-field">
              <label class="form-field-label" for="fieldcomment">
                {{ Lang::txt('COM_FORUM_FIELD_COMMENTS') }}
              </label>
              {!! $__view->editor(
                  'fields[comment]', '', 35, 15, 'fieldcomment',
                  [
                      'class' => 'minimal no-footer',
                      'mentions' => [[
                          'minChars' => 0,
                          'feed' => '/api/members/mentions/list?search={encodedQuery}',
                          'itemTemplate' => '<li data-id="{id}"><img class="photo" src="{picture}" /><strong class="username">{username}</strong><span class="fullname">{name}</span></li>',
                          'outputTemplate' => '<a href="/members/{id}" data-user-id="{id}" target="_blank">@{username}</a>&nbsp;&nbsp;',
                      ]]
                  ]
              ) !!}
            </div>

            {{-- Tags --}}
            <div class="form-field">
              <label class="form-field-label" for="actags">
                {{ Lang::txt('COM_FORUM_FIELD_YOUR_TAGS') }}
              </label>
              {!! $__view->autocompleter('tags', 'tags', e($thread->tags('string')), 'actags') !!}
            </div>

            {{-- Attachments --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="form-field">
                <label class="form-field-label" for="upload">
                  {{ Lang::txt('COM_FORUM_FIELD_FILE') }}
                </label>
                <input type="file" name="upload" id="upload" class="file-input file-input-bordered w-full" />
              </div>
              <div class="form-field">
                <label class="form-field-label" for="field-description">
                  {{ Lang::txt('COM_FORUM_FIELD_DESCRIPTION') }}
                </label>
                <input type="text" name="description" id="field-description" class="input w-full" value="" />
              </div>
            </div>

            {{-- Anonymous --}}
            @if($config->get('allow_anonymous'))
              <label class="checkbox-label">
                <input type="checkbox" class="checkbox" name="fields[anonymous]"
                       id="field-anonymous" value="1" />
                <span>{{ Lang::txt('COM_FORUM_FIELD_ANONYMOUS') }}</span>
              </label>
            @endif

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">{{ Lang::txt('JSUBMIT') }}</button>
            </div>
          @else
            <p class="text-warning">{{ Lang::txt('COM_FORUM_PERMISSION_DENIED') }}</p>
          @endif
        </div>
      </div>

      {{-- Hidden fields --}}
      <input type="hidden" name="fields[category_id]" value="{{ $thread->get('category_id') }}" />
      <input type="hidden" name="fields[parent]" value="{{ $thread->get('id') }}" />
      <input type="hidden" name="fields[state]" value="1" />
      <input type="hidden" name="fields[access]" value="{{ $thread->get('access', 0) }}" />
      <input type="hidden" name="fields[id]" value="" />
      <input type="hidden" name="fields[scope]" value="site" />
      <input type="hidden" name="fields[scope_id]" value="0" />
      <input type="hidden" name="fields[thread]" value="{{ $thread->get('id') }}" />
      <input type="hidden" name="fields[scope_sub_id]" value="{{ $thread->get('scope_sub_id') }}" />
      <input type="hidden" name="fields[object_id]" value="{{ $thread->get('object_id') }}" />
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="threads" />
      <input type="hidden" name="task" value="save" />
      {!! Html::input('token') !!}
    </form>
  @else
    <div class="alert alert-warning mt-6">
      {{ Lang::txt('COM_FORUM_CATEGORY_CLOSED') }}
    </div>
  @endif
</x-page-container>
