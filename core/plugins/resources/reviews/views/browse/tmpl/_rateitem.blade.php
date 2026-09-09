{{--
  Rate item — helpful/not helpful voting buttons.

  Variables (from parent view):
    $option — string: component option
    $item   — object: review/comment with voting data
    $type   — string: item type (e.g., 'review')

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $dcls = '';
  $lcls = '';

  if ($item->get('user_id') == User::get('id')) {
      $item->set('vote', null);
  }

  if ($vote = $item->get('vote')) {
      switch ($vote) {
          case 1:
          case 'yes':
          case 'positive':
          case 'like':
              $lcls = ' chosen';
              break;
          case -1:
          case 'no':
          case 'negative':
          case 'dislike':
              $dcls = ' chosen';
              break;
      }
  } else {
      $item->set('vote', null);
  }

  if (!User::isGuest()) {
      $like_title    = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_UP', $item->get('helpful', 0));
      $dislike_title = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DOWN', $item->get('nothelpful', 0));
  } else {
      $like_title    = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_UP_LOGIN');
      $dislike_title = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DOWN_LOGIN');
  }

  $cls = ' tooltips';
  $likeBtnCls = ($item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls;
  $dislikeBtnCls = ($item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls;
  $resourceId = $item->get('resource_id');
  $refId = $item->get('id');
  $voteYesUrl = Route::url(
      'index.php?option=' . $option . '&id=' . $resourceId
      . '&active=reviews&action=rateitem&refid=' . $refId . '&vote=yes'
  );
  $voteNoUrl = Route::url(
      'index.php?option=' . $option . '&id=' . $resourceId
      . '&active=reviews&action=rateitem&refid=' . $refId . '&vote=no'
  );
  $likeTxt = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $item->get('helpful', 0));
  $dislikeTxt = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $item->get('nothelpful', 0));
@endphp

@if(!$item->get('vote'))
  @if(User::isGuest() || $item->get('user_id') == User::get('id'))
    <span class="vote-like{{ $lcls }}">
      <span class="vote-button {{ $likeBtnCls }}" title="{{ $like_title }}">{{ $likeTxt }}</span>
    </span>
    <span class="vote-dislike{{ $dcls }}">
      <span class="vote-button {{ $dislikeBtnCls }}" title="{{ $dislike_title }}">{{ $dislikeTxt }}</span>
    </span>
  @else
    <span class="vote-like{{ $lcls }}">
      <a class="vote-button {{ $likeBtnCls }}" href="{{ $voteYesUrl }}"
         title="{{ $like_title }}">{{ $likeTxt }}</a>
    </span>
    <span class="vote-dislike{{ $dcls }}">
      <a class="vote-button {{ $dislikeBtnCls }}" href="{{ $voteNoUrl }}"
         title="{{ $dislike_title }}">{{ $dislikeTxt }}</a>
    </span>
  @endif
@else
  @if(trim($lcls) == 'chosen')
    <span class="vote-like{{ $lcls }}">
      <span class="vote-button {{ $likeBtnCls }}" title="{{ $like_title }}">{{ $likeTxt }}</span>
    </span>
    <span class="vote-dislike{{ $dcls }}">
      <a class="vote-button {{ $dislikeBtnCls }}" href="{{ $voteNoUrl }}"
         title="{{ $dislike_title }}">{{ $dislikeTxt }}</a>
    </span>
  @else
    <span class="vote-like{{ $lcls }}">
      <a class="vote-button {{ $likeBtnCls }}" href="{{ $voteYesUrl }}"
         title="{{ $like_title }}">{{ $likeTxt }}</a>
    </span>
    <span class="vote-dislike{{ $dcls }}">
      <span class="vote-button {{ $dislikeBtnCls }}" title="{{ $dislike_title }}">{{ $dislikeTxt }}</span>
    </span>
  @endif
@endif
