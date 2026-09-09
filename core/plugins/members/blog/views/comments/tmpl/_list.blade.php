{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

<ol class="space-y-4 {{ $depth > 0 ? 'ml-8 mt-2' : '' }}" id="t{{ $parent ?? '0' }}">
  @if ($comments && $comments->count())
    @php
      $cls = isset($cls) ? $cls : 'odd';
      $cls = ($cls == 'odd') ? 'even' : 'odd';
      $depth++;
    @endphp
    @foreach ($comments as $comment)
      @php
        $__view->view('_comment')
            ->set('option', $option)
            ->set('comment', $comment)
            ->set('config', $config)
            ->set('depth', $depth)
            ->set('cls', $cls)
            ->set('base', $base)
            ->set('member', $member)
            ->display();
      @endphp
    @endforeach
  @endif
</ol>
