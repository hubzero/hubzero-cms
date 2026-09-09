{{--
  Comment list partial — iterates comments and renders each via _comment.

  Variables (passed via $__view->view('_list')->set(...)):
    $comments — Array/collection of Post models
    $likes    — Full likes array for the thread
    $thread   — Parent thread Post model
    $config   — Component params (Registry)
    $parent   — Parent comment ID (0 for root level)
    $depth    — Current nesting depth
    $cls      — CSS class (odd/even toggle)
    $filters  — Filter array
    $category — Category model

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  // Build likes hash map keyed by postId
  $hashMap = [];
  foreach ($likes as $like) {
      $postId = $like->postId;
      if (isset($hashMap[$postId])) {
          $hashMap[$postId][] = $like;
      } else {
          $hashMap[$postId] = [$like];
      }
  }
@endphp

<ol class="comments" id="t{{ $parent }}">
@if($comments)
  @php
    $cls = isset($cls) ? (($cls == 'odd') ? 'even' : 'odd') : 'odd';
    $depth++;
  @endphp
  @foreach($comments as $commentItem)
    @php
      $postId = $commentItem->get('id');
      $likesByPostId = isset($hashMap[$postId]) ? $hashMap[$postId] : [];
    @endphp
    {!! $__view->view('_comment')
         ->set('comment', $commentItem)
         ->set('like', $likesByPostId)
         ->set('likes', $likes)
         ->set('thread', $thread)
         ->set('config', $config)
         ->set('depth', $depth)
         ->set('cls', $cls)
         ->set('filters', $filters)
         ->set('category', $category)
         ->loadTemplate() !!}
  @endforeach
@endif
</ol>
