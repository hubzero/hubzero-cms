{{--
  Review comment list — recursive container for comments.

  Variables (from parent view):
    $parent   — int: parent comment ID
    $cls      — string: alternating class (odd/even)
    $depth    — int: current nesting depth
    $option   — string: component option
    $resource — object: resource model
    $comments — collection: comment objects
    $config   — object: plugin config
    $base     — string: base URL

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<ol class="comments" id="t{{ $parent ?? '0' }}">
  @if($comments)
    @php
      $cls = isset($cls) ? (($cls == 'odd') ? 'even' : 'odd') : 'odd';
      $depth++;
    @endphp

    @foreach($comments as $comment)
      @php
        $__view->view('_comment')
            ->set('option', $option)
            ->set('comment', $comment)
            ->set('config', $config)
            ->set('depth', $depth)
            ->set('resource', $resource)
            ->set('cls', $cls)
            ->set('base', $base)
            ->display();
      @endphp
    @endforeach
  @endif
</ol>
