{{--
  Single comment with recursive replies.

  Variables (passed via view('_comment')->set(...)):
    $comment   — Comment model instance
    $row       — Parent blog entry model
    $option    — Component option string
    $config    — Component params (Registry)
    $maxDepth  — Maximum nesting depth
    $depth     — Current nesting level (0 = top-level)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $name     = Lang::txt('JANONYMOUS');
  $nameLink = null;

  if (!$comment->get('anonymous')) {
      $name = e(stripslashes($comment->creator->get('name', $name)));
      if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
          $nameLink = Route::url($comment->creator->link());
      }
  }

  $isReported  = $comment->isReported();
  $commentBody = $isReported
      ? '<p class="text-warning">' . Lang::txt('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE') . '</p>'
      : $comment->content();

  $canEdit = $config->get('access-edit-comment')
      || User::get('id') == $comment->get('created_by');

  $permalinkUrl = Route::url($row->link() . '#c' . $comment->get('id'));
  $commentId    = $comment->get('id');

  $replies = ($depth < $maxDepth)
      ? $comment->replies()
          ->whereIn('state', [
              \Components\Blog\Models\Comment::STATE_PUBLISHED,
              \Components\Blog\Models\Comment::STATE_FLAGGED,
          ])
          ->ordered()
          ->rows()
      : collect();

  $depthClass = $depth > 0 ? ' comment-reply' : '';
@endphp

<article class="comment{{ $depthClass }}" id="c{{ $commentId }}">
  <div class="avatar">
    <div class="rounded-full">
      <img src="{{ $comment->creator->picture($comment->get('anonymous')) }}"
           alt="" loading="lazy" />
    </div>
  </div>
  <div class="flex-1">
    <div class="comment-meta">
      <span class="font-medium">
        @if($nameLink)
          <a class="link link-hover" href="{{ $nameLink }}">{{ $name }}</a>
        @else
          {{ $name }}
        @endif
      </span>
      <a class="comment-time link link-hover"
         href="{{ $permalinkUrl }}"
         title="{{ Lang::txt('COM_BLOG_PERMALINK') }}">
        <time datetime="{{ $comment->get('created') }}">
          {{ $comment->created('date') }} {{ Lang::txt('COM_BLOG_AT') }} {{ $comment->created('time') }}
        </time>
        @if($comment->wasModified())
          <span class="italic">&mdash; {{ Lang::txt('COM_BLOG_EDITED') }}</span>
        @endif
      </a>
    </div>

    <div class="comment-body">{!! $commentBody !!}</div>

    <div class="comment-actions">
      @if(!$isReported)
        @if($canEdit)
          <a class="btn btn-xs btn-ghost"
             href="{{ Route::url($row->link() . '&action=editcomment&comment=' . $commentId) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" />
            </svg>
            {{ Lang::txt('JACTION_EDIT') }}
          </a>
        @endif
        @if($depth < $maxDepth)
          <a class="btn btn-xs btn-ghost"
             href="{{ Route::url($row->link() . '&reply=' . $commentId) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            {{ Lang::txt('COM_BLOG_REPLY') }}
          </a>
        @endif
        <a class="btn btn-xs btn-ghost"
           href="{{ Route::url('index.php?option=com_support&task=reportabuse&category=blogcomment&id=' . $commentId . '&parent=' . $comment->get('entry_id')) }}">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
          </svg>
          {{ Lang::txt('COM_BLOG_REPORT_ABUSE') }}
        </a>
      @endif
      @if($config->get('access-delete-comment'))
        <a class="btn btn-xs btn-ghost text-error"
           data-confirm="{{ Lang::txt('COM_BLOG_CONFIRM_DELETE') }}"
           href="{{ Route::url($row->link() . '&action=deletecomment&comment=' . $commentId) }}">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
          </svg>
          {{ Lang::txt('JACTION_DELETE') }}
        </a>
      @endif
    </div>

    @if($depth < $maxDepth && count($replies) > 0)
      <div class="comment-replies">
        @foreach($replies as $reply)
          {!! $__view->view('_comment')
                ->set('comment', $reply)
                ->set('row', $row)
                ->set('config', $config)
                ->set('maxDepth', $maxDepth)
                ->set('depth', $depth + 1)
                ->loadTemplate() !!}
        @endforeach
      </div>
    @endif
  </div>
</article>
