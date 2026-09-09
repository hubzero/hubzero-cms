{{--
  Single review comment — displays comment with voting, edit/reply forms, and nested replies.

  Variables (from _list partial):
    $option   — string: component option
    $comment  — object: review/comment model
    $config   — object: plugin config
    $depth    — int: current nesting depth
    $resource — object: resource model
    $cls      — string: alternating class (odd/even)
    $base     — string: base URL

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $name = Lang::txt('JANONYMOUS');

  if (!$comment->get('anonymous')) {
      $name = e(stripslashes($comment->creator->get('name', '')));
      if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
          $name = '<a href="' . Route::url($comment->creator->link()) . '">' . $name . '</a>';
      }
  }

  $comment->set('item_type', 'review');

  if ($comment->isReported()) {
      $commentBody = '<p class="warning">' . Lang::txt('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED') . '</p>';
  } else {
      $commentBody = $comment->content;
  }

  $isReview = (bool) $comment->get('resource_id');

  if ($isReview) {
      $comment->set('created_by', $comment->get('user_id'));
      $comment->set('item_id', $comment->get('id'));
      $comment->set('parent', 0);

      $ratingVal = $comment->get('rating', 0);
      $ratingClasses = [
          '0.5' => 'half-stars', '1' => 'one-stars', '1.5' => 'onehalf-stars',
          '2' => 'two-stars', '2.5' => 'twohalf-stars', '3' => 'three-stars',
          '3.5' => 'threehalf-stars', '4' => 'four-stars', '4.5' => 'fourhalf-stars',
          '5' => 'five-stars',
      ];
      $class = ' ' . ($ratingClasses[(string) $ratingVal] ?? 'no-stars');
  }

  $maxDepth = $config->get('comments_depth', 3);
  $showVoting = !$comment->isReported() && $isReview && $config->get('voting');
  $canManage = User::get('id') == $comment->get('created_by')
      || User::authorise('core.manage', 'com_resources');
  $isEditing = Request::getWord('action') == 'edit'
      && Request::getInt('comment') == $comment->get('id');
  $isReplying = Request::getInt('reply', 0) == $comment->get('id');
@endphp

<li class="comment {{ $cls }}" id="c{{ $comment->get('id') }}">
  <p class="comment-member-photo">
    <img src="{{ $comment->creator->picture($comment->get('anonymous', 0)) }}" alt="" />
  </p>
  <div class="comment-content">
    @if($showVoting)
      <p class="comment-voting voting" id="answers_{{ $comment->get('id') }}">
        @php
          $comment->set('helpful', $comment->votes()->whereEquals('vote', 1)->total());
          $comment->set('nothelpful', $comment->votes()->whereEquals('vote', -1)->total());

          if (!User::isGuest() && $comment->get('created_by') == User::get('id')) {
              $comment->set('vote', $comment->ballot(User::get('id'), Request::ip())->get('vote'));
          }

          $__view->view('_rateitem')
              ->set('option', $option)
              ->set('item', $comment)
              ->set('type', 'review')
              ->display();
        @endphp
      </p>
    @endif

    <p class="comment-title">
      <strong>{!! $name !!}</strong>
      @php
        $noDate = !$comment->created() || $comment->created() == '0000-00-00 00:00:00';
        $createdDatetime = $comment->created();
      @endphp
      <a class="permalink"
         href="{{ Route::url($base . '#c' . $comment->get('id')) }}"
         title="{{ Lang::txt('PLG_RESOURCES_REVIEWS_PERMALINK') }}">
        @if($noDate)
          <span class="comment-date-unknown">{{ Lang::txt('PLG_RESOURCES_REVIEWS_UNKNOWN') }}</span>
        @else
          <span class="comment-date-at">@</span>
          <span class="time">
            <time datetime="{{ $createdDatetime }}">{{ $comment->created('time') }}</time>
          </span>
          <span class="comment-date-on">{{ Lang::txt('PLG_RESOURCES_REVIEWS_ON') }}</span>
          <span class="date">
            <time datetime="{{ $createdDatetime }}">{{ $comment->created('date') }}</time>
          </span>
        @endif
      </a>
    </p>

    @if($isReview)
      <p>
        @php
          $ratingText = Lang::txt('PLG_RESOURCES_REVIEWS_OUT_OF_5_STARS', $comment->get('rating', 0));
        @endphp
        <span class="avgrating{{ $class }}">
          <span>{{ $ratingText }}</span>
        </span>
      </p>
    @endif

    @if($isEditing)
      <form id="cform{{ $comment->get('id') }}"
            class="comment-edit"
            action="{{ Route::url($base) }}"
            method="post"
            enctype="multipart/form-data">
        <fieldset>
          <legend><span>{{ Lang::txt('PLG_RESOURCES_REVIEWS_EDIT') }}</span></legend>

          <input type="hidden" name="comment[id]" value="{{ $comment->get('id') }}" />
          <input type="hidden" name="comment[item_type]" value="{{ $comment->get('item_type') }}" />
          <input type="hidden" name="comment[item_id]" value="{{ $comment->get('item_id') }}" />
          <input type="hidden" name="comment[parent]" value="{{ $comment->get('parent') }}" />
          <input type="hidden" name="comment[created]" value="{{ $comment->get('created') }}" />
          <input type="hidden" name="comment[created_by]" value="{{ $comment->get('created_by') }}" />
          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="id" value="{{ $resource->id }}" />
          <input type="hidden" name="active" value="reviews" />
          <input type="hidden" name="action" value="savereply" />

          {!! Html::input('token') !!}

          @php $editFieldId = 'comment_' . $comment->get('id') . '_content'; @endphp
          <label for="{{ $editFieldId }}">
            <span class="label-text">{{ Lang::txt('PLG_RESOURCES_REVIEWS_ENTER_COMMENTS') }}</span>
            @php
              echo $__view->editor(
                  'comment[content]',
                  $comment->get('content'),
                  35, 4, $editFieldId,
                  ['class' => 'minimal no-footer']
              );
            @endphp
          </label>

          <label id="comment-anonymous-label" for="comment-anonymous">
            <input class="option" type="checkbox" name="comment[anonymous]"
                   id="comment-anonymous" value="1"
                   {{ $comment->get('anonymous') ? 'checked' : '' }} />
            {{ Lang::txt('PLG_RESOURCES_REVIEWS_POST_COMMENT_ANONYMOUSLY') }}
          </label>

          <p class="submit">
            <input type="submit" value="{{ Lang::txt('PLG_RESOURCES_REVIEWS_SUBMIT') }}" />
          </p>
        </fieldset>
      </form>
    @else
      <div class="comment-body">
        {!! $commentBody !!}
      </div>

      <p class="comment-options">
        @if(!$comment->isReported() && !stristr($commentBody, 'class="warning"'))
          @if($canManage)
            @php
              $commentId = $comment->get('id');
              $deleteAction = '&action=delete' . ($isReview ? 'review' : 'reply') . '&comment=' . $commentId;
              $deleteUrl = Route::url($base . $deleteAction);
              $confirmTxt = Lang::txt('PLG_RESOURCES_REVIEWS_CONFIRM_DELETE');
              $editAction = '&action=edit' . ($isReview ? 'review' : '')
                  . '&comment=' . $commentId . ($isReview ? '#commentform' : '');
              $editUrl = Route::url($base . $editAction);
            @endphp
            <a class="icon-delete delete"
               data-txt-confirm="{{ $confirmTxt }}"
               href="{{ $deleteUrl }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_DELETE') }}</a>
            <a class="icon-edit edit"
               href="{{ $editUrl }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_EDIT') }}</a>
          @endif

          @if(!$comment->get('reports'))
            @if($depth < $maxDepth)
              @if($isReplying)
                <a class="icon-reply reply active"
                   data-txt-active="{{ Lang::txt('JCANCEL') }}"
                   data-txt-inactive="{{ Lang::txt('PLG_RESOURCES_REVIEWS_REPLY') }}"
                   href="{{ Route::url($comment->link()) }}"
                   data-rel="comment-form{{ $comment->get('id') }}">{{ Lang::txt('JCANCEL') }}</a>
              @else
                <a class="icon-reply reply"
                   data-txt-active="{{ Lang::txt('JCANCEL') }}"
                   data-txt-inactive="{{ Lang::txt('PLG_RESOURCES_REVIEWS_REPLY') }}"
                   href="{{ Route::url($comment->link('reply')) }}"
                   data-rel="comment-form{{ $comment->get('id') }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_REPLY') }}</a>
              @endif
            @endif
            <a class="icon-abuse abuse"
               data-txt-flagged="{{ Lang::txt('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED') }}"
               href="{{ Route::url($comment->link('report')) }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_REPORT_ABUSE') }}</a>
          @endif
        @endif
      </p>

      @if($depth < $maxDepth)
        <div class="addcomment comment-add{{ !$isReplying ? ' hide' : '' }}"
             id="comment-form{{ $comment->get('id') }}">
          @if(User::isGuest())
            <p class="warning">
              @php
                $loginReturn = base64_encode(Route::url($base, false, true));
                $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $loginReturn);
                $loginLink = '<a href="' . $loginUrl . '">' . Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN') . '</a>';
              @endphp
              {!! Lang::txt('PLG_RESOURCES_REVIEWS_PLEASE_LOGIN_TO_ANSWER', $loginLink) !!}
            </p>
          @else
            @php
              $replyToName = !$comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS');
              $replyLegend = Lang::txt('PLG_RESOURCES_REVIEWS_REPLYING_TO', $replyToName);
              $replyFieldId = 'comment_' . $comment->get('id') . '_content';
            @endphp
            <form id="cform{{ $comment->get('id') }}"
                  action="{{ Route::url($base) }}"
                  method="post"
                  enctype="multipart/form-data">
              <fieldset>
                <legend><span>{!! $replyLegend !!}</span></legend>

                <input type="hidden" name="comment[id]" value="0" />
                <input type="hidden" name="comment[item_type]" value="{{ $comment->get('item_type') }}" />
                <input type="hidden" name="comment[item_id]" value="{{ $comment->get('item_id') }}" />
                <input type="hidden" name="comment[parent]"
                       value="{{ $isReview ? 0 : $comment->get('id') }}" />
                <input type="hidden" name="comment[created]" value="" />
                <input type="hidden" name="comment[created_by]" value="{{ User::get('id') }}" />
                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="id" value="{{ $resource->id }}" />
                <input type="hidden" name="active" value="reviews" />
                <input type="hidden" name="action" value="savereply" />

                {!! Html::input('token') !!}

                <label for="{{ $replyFieldId }}">
                  <span class="label-text">{{ Lang::txt('PLG_RESOURCES_REVIEWS_ENTER_COMMENTS') }}</span>
                  @php
                    echo $__view->editor(
                        'comment[content]', '', 35, 4, $replyFieldId,
                        ['class' => 'minimal no-footer']
                    );
                  @endphp
                </label>

                <label id="comment-anonymous-label" for="comment-anonymous">
                  <input class="option" type="checkbox" name="comment[anonymous]"
                         id="comment-anonymous" value="1" />
                  {{ Lang::txt('PLG_RESOURCES_REVIEWS_POST_COMMENT_ANONYMOUSLY') }}
                </label>

                <p class="submit">
                  <input type="submit" value="{{ Lang::txt('PLG_RESOURCES_REVIEWS_SUBMIT') }}" />
                </p>
              </fieldset>
            </form>
          @endif
        </div>
      @endif
    @endif
  </div>

  @if($depth < $maxDepth)
    @php
      $replies = $comment->replies()
          ->whereIn('state', [
              \Components\Resources\Models\Review\Comment::STATE_PUBLISHED,
              \Components\Resources\Models\Review\Comment::STATE_FLAGGED,
          ])
          ->ordered()
          ->rows();

      $__view->view('_list')
          ->set('parent', $comment->get('id'))
          ->set('resource', $resource)
          ->set('option', $option)
          ->set('comments', $replies)
          ->set('config', $config)
          ->set('depth', $depth)
          ->set('cls', $cls)
          ->set('base', $base)
          ->display();
    @endphp
  @endif
</li>
