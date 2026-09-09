{{--
  Single forum comment/post with likes, attachments, and recursive replies.

  Variables (passed via $__view->view('_comment')->set(...)):
    $comment  — Post model instance
    $like     — Array of like objects for this specific post
    $likes    — Full likes array (passed through for recursion)
    $thread   — Parent thread Post model
    $config   — Component params (Registry)
    $depth    — Current nesting depth
    $cls      — CSS class (odd/even)
    $filters  — Filter array
    $category — Category model

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('like.css');
  $__view->js('like.js');

  $likeArray = $like;
  $countLike = count($likeArray);
  $currentUserId = User::get('id');

  $userLikesComment = false;
  $userNameLikesArray = '';
  foreach ($likeArray as $likeObj) {
      if ($currentUserId == $likeObj->userId) {
          $userLikesComment = true;
      }
      $userNameLikesArray .= '/' . $likeObj->userName . '#' . $likeObj->userId;
  }
  $userNameLikesArray = substr($userNameLikesArray, 1);

  $comment->set('section', $filters['section']);
  $comment->set('category', $category->get('alias'));

  // Author
  $name = Lang::txt('JANONYMOUS');
  $nameUrl = '';
  if (!$comment->get('anonymous')) {
      $name = e(stripslashes($comment->creator->get('name', $name)));
      if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
          $nameUrl = Route::url($comment->creator->link(), false);
      }
  }

  $cls = isset($cls) ? $cls : 'odd';

  $commentBody = $comment->isReported()
      ? '<p class="text-warning">' . Lang::txt('COM_FORUM_CONTENT_FLAGGED') . '</p>'
      : $comment->comment;

  $commentId  = $comment->get('id');
  $anchorUrl  = Route::url($comment->link('anchor'), false);
  $isParent   = !$comment->get('parent');
  $isReply    = (bool) $comment->get('parent');
  $isCreator  = $comment->get('created_by') == User::get('id');

  $canManage       = $config->get('access-manage-thread');
  $canDeleteThread = $config->get('access-delete-thread');
  $canEditThread   = $config->get('access-edit-thread');
  $canDeletePost   = $config->get('access-delete-post');
  $canEditPost     = $config->get('access-edit-post');

  $canDelete = ($isParent && $canDeleteThread) || ($isReply && $canDeletePost);
  $canEdit   = ($isParent && $canEditThread)   || ($isReply && $canEditPost);

  $showOptions = $canManage
      || ($isParent && $isCreator && ($canDeleteThread || $canEditThread))
      || ($isReply  && $isCreator && ($canDeletePost   || $canEditPost));

  $threadingConfig = $config->get('threading', 'list');
  $maxDepth        = $config->get('threading_depth', 3);
  $canReply        = !$thread->get('closed')
                     && $threadingConfig == 'tree'
                     && $depth < $maxDepth;
@endphp

<li class="comment {{ $cls }}{{ !$comment->get('parent') ? ' start' : '' }}" id="c{{ $commentId }}">
  <p class="comment-member-photo">
    <img src="{{ $comment->creator->picture($comment->get('anonymous')) }}" alt="" />
  </p>
  <div class="comment-content">
    {{-- Header --}}
    <p class="comment-title">
      <strong>{!! $nameUrl ? '<a href="' . $nameUrl . '">' . $name . '</a>' : $name !!}</strong>
      <a class="permalink" href="{{ $anchorUrl }}" title="{{ Lang::txt('COM_FORUM_PERMALINK') }}">
        <span class="comment-date-at">{{ Lang::txt('COM_FORUM_AT') }}</span>
        <span class="time">
          <time datetime="{{ $comment->created() }}">{{ $comment->created('time') }}</time>
        </span>
        <span class="comment-date-on">{{ Lang::txt('COM_FORUM_ON') }}</span>
        <span class="date">
          <time datetime="{{ $comment->created() }}">{{ $comment->created('date') }}</time>
        </span>
        @if($comment->wasModified())
          &mdash; {{ Lang::txt('COM_FORUM_EDITED') }}
          <span class="comment-date-at">{{ Lang::txt('COM_FORUM_AT') }}</span>
          <span class="time">
            <time datetime="{{ $comment->modified() }}">{{ $comment->modified('time') }}</time>
          </span>
          <span class="comment-date-on">{{ Lang::txt('COM_FORUM_ON') }}</span>
          <span class="date">
            <time datetime="{{ $comment->modified() }}">{{ $comment->modified('date') }}</time>
          </span>
        @endif
      </a>
    </p>

    {{-- Body --}}
    <div class="comment-body">
      {!! $commentBody !!}

      {{-- Likes --}}
      @if(!User::isGuest())
        <div class="elementInline likeContainer">
          <a class="icon-heart like {{ $userLikesComment ? 'userLiked' : '' }}" href="#"
             data-thread="{{ $thread->get('id') }}"
             data-post="{{ $commentId }}"
             data-user="{{ User::get('id') }}"
             data-user-name="{{ User::get('name') }}"
             data-likes-list="{{ $userNameLikesArray }}"
             data-count="{{ $countLike }}"></a>
          <span class="likesStat {{ $countLike == 0 ? 'noLikes' : '' }}">
            {{ $countLike > 0 ? Lang::txt('COM_FORUM_LIKES_VIEW', $countLike) : Lang::txt('COM_FORUM_LIKES_NONE') }}
          </span>
        </div>
        <div class="clear"></div>

        <div class="whoLikedPost">
          @if(strlen($userNameLikesArray) > 0)
            <div class="names">
              @php
                $nameEntries = preg_split('#/#', $userNameLikesArray);
                $likeLinks = [];
                foreach ($nameEntries as $nameString) {
                    $parts = explode('#', $nameString);
                    $likeUserName = $parts[0];
                    $likeUserId   = $parts[1];
                    $likeLinks[] = '<a href="/members/' . $likeUserId . '/profile" target="_blank">'
                        . e($likeUserName) . '</a>';
                }
                echo implode(', ', $likeLinks) . ' ' . Lang::txt('COM_FORUM_LIKES_LIKED_THIS');
              @endphp
            </div>
          @endif
        </div>
      @endif
    </div>

    {{-- Attachments --}}
    <div class="comment-attachments">
      @php
        $publishedState = \Components\Forum\Models\Attachment::STATE_PUBLISHED;
        $commentAttachments = $comment->attachments()->whereEquals('state', $publishedState)->rows();
      @endphp
      @foreach($commentAttachments as $att)
        @if(!trim($att->get('description')))
          @php $att->set('description', $att->get('filename')); @endphp
        @endif
        @if($att->exists())
          @php $attUrl = Route::url($att->link(), false); @endphp
          @if($att->isImage())
            @if($att->width() > 400)
              <p><a href="{{ $attUrl }}" rel="external"><img src="{{ $attUrl }}" alt="{{ e($att->get('description')) }}" width="400" /></a></p>
            @else
              <p><img src="{{ $attUrl }}" alt="{{ e($att->get('description')) }}" /></p>
            @endif
          @else
            @php $ext = Filesystem::extension($att->get('filename')); @endphp
            <a class="attachment {{ $ext }}" href="{{ $attUrl }}" title="{{ e($att->get('description')) }}">
              <p class="attachment-description">{{ $att->get('description') }}</p>
              <p class="attachment-meta">
                <span class="attachment-size">{{ \Hubzero\Utility\Number::formatBytes($att->size()) }}</span>
                <span class="attachment-action">{{ Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD') }}</span>
              </p>
            </a>
          @endif
        @else
          @php $ext = Filesystem::extension($att->get('filename')); @endphp
          <div class="attachment {{ $ext }}" title="{{ e($att->get('description')) }}">
            <p class="attachment-description">{{ $att->get('description') }}</p>
            <p class="attachment-meta">
              <span class="attachment-size">{{ $att->get('filename') }}</span>
              <span class="attachment-action">{{ Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND') }}</span>
            </p>
          </div>
        @endif
      @endforeach
    </div>

    {{-- Actions --}}
    @if($showOptions)
      <p class="comment-options">
        @if($canDelete)
          <a class="icon-delete delete"
             data-txt-confirm="{{ Lang::txt('COM_FORUM_CONFIRM_DELETE') }}"
             data-id="c{{ $commentId }}"
             href="{{ Route::url($comment->link('delete'), false) }}">{{ Lang::txt('JACTION_DELETE') }}</a>
        @endif
        @if($canEdit)
          <a class="icon-edit edit"
             data-id="c{{ $commentId }}"
             href="{{ Route::url($comment->link('edit'), false) }}">{{ Lang::txt('JACTION_EDIT') }}</a>
        @endif
        @if(!$comment->isReported())
          @if($canReply)
            @php
              $replyId = Request::getInt('reply', 0);
              $relAttr = 'comment-form' . $commentId;
            @endphp
            @if($replyId == $commentId)
              <a class="icon-reply reply active"
                 data-txt-active="{{ Lang::txt('JCANCEL') }}"
                 data-txt-inactive="{{ Lang::txt('COM_FORUM_REPLY') }}"
                 href="{{ Route::url($comment->link(), false) }}"
                 rel="{{ $relAttr }}">{{ Lang::txt('JCANCEL') }}</a>
            @else
              <a class="icon-reply reply"
                 data-txt-active="{{ Lang::txt('JCANCEL') }}"
                 data-txt-inactive="{{ Lang::txt('COM_FORUM_REPLY') }}"
                 href="{{ Route::url($comment->link('reply'), false) }}"
                 rel="{{ $relAttr }}">{{ Lang::txt('COM_FORUM_REPLY') }}</a>
            @endif
          @endif
          <a class="icon-abuse abuse"
             data-txt-flagged="{{ Lang::txt('COM_FORUM_CONTENT_FLAGGED') }}"
             href="{{ Route::url($comment->link('abuse'), false) }}">{{ Lang::txt('COM_FORUM_REPORT_ABUSE') }}</a>
        @endif
      </p>
    @endif

    {{-- Inline reply form --}}
    @if(!User::isGuest() && $canReply)
      @php
        $replyId = Request::getInt('reply', 0);
        $hideClass = ($replyId != $commentId) ? ' hide' : '';
        $threadUrl = Route::url($thread->link(), false);
      @endphp
      <div class="addcomment comment-add{{ $hideClass }}" id="comment-form{{ $commentId }}">
        <form id="cform{{ $commentId }}" action="{{ $threadUrl }}" method="post"
              enctype="multipart/form-data">
          <fieldset>
            @php
              $replyToName = !$comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS');
            @endphp
            <legend><span>{{ Lang::txt('COM_FORUM_REPLYING_TO', $replyToName) }}</span></legend>

            <input type="hidden" name="fields[id]" value="0" />
            <input type="hidden" name="fields[state]" value="1" />
            <input type="hidden" name="fields[access]" value="{{ $thread->get('access', 0) }}" />
            <input type="hidden" name="fields[scope]" value="{{ $thread->get('scope') }}" />
            <input type="hidden" name="fields[category_id]" value="{{ $thread->get('category_id') }}" />
            <input type="hidden" name="fields[scope_id]" value="{{ $thread->get('scope_id') }}" />
            <input type="hidden" name="fields[scope_sub_id]" value="{{ $thread->get('scope_sub_id') }}" />
            <input type="hidden" name="fields[object_id]" value="{{ $thread->get('object_id') }}" />
            <input type="hidden" name="fields[parent]" value="{{ $commentId }}" />
            <input type="hidden" name="fields[thread]" value="{{ $comment->get('thread') }}" />
            <input type="hidden" name="fields[created]" value="" />
            <input type="hidden" name="fields[created_by]" value="{{ User::get('id') }}" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="task" value="save" />
            {!! Html::input('token') !!}

            <div class="form-group">
              @php $fieldId = 'field_' . $commentId . '_comment'; @endphp
              <label for="{{ $fieldId }}">
                <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_COMMENTS') }}</span>
                {!! $__view->editor('fields[comment]', '', 35, 4, $fieldId, ['class' => 'form-control minimal no-footer']) !!}
              </label>
            </div>

            <div class="form-group">
              @php $fileFieldId = 'comment-' . $commentId . '-file'; @endphp
              <label class="upload-label" for="{{ $fileFieldId }}">
                <span class="label-text">{{ Lang::txt('COM_FORUM_ATTACH_FILE') }}:</span>
                <input type="file" class="form-control-file" name="upload" id="{{ $fileFieldId }}" />
              </label>
            </div>

            @if($config->get('allow_anonymous'))
              <div class="form-group">
                <div class="form-check">
                  @php $anonFieldId = 'comment-' . $commentId . '-anonymous'; @endphp
                  <label class="form-check-label reply-anonymous-label" for="{{ $anonFieldId }}">
                    <input class="option form-check-input" type="checkbox"
                           name="fields[anonymous]" id="{{ $anonFieldId }}" value="1" />
                    {{ Lang::txt('COM_FORUM_FIELD_ANONYMOUS') }}
                  </label>
                </div>
              </div>
            @endif

            <p class="submit">
              <input type="submit" class="btn" value="{{ Lang::txt('JSUBMIT') }}" />
            </p>
          </fieldset>
        </form>
      </div>
    @endif
  </div>

  {{-- Recursive replies (tree threading) --}}
  @if($threadingConfig == 'tree' && $depth < $maxDepth)
    {!! $__view->view('_list')
         ->set('comments', $comment->get('replies'))
         ->set('thread', $thread)
         ->set('likes', $likes)
         ->set('parent', $comment->get('id'))
         ->set('config', $config)
         ->set('depth', $depth)
         ->set('cls', $cls)
         ->set('filters', $filters)
         ->set('category', $category)
         ->loadTemplate() !!}
  @endif
</li>
