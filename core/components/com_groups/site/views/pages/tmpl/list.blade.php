{{--
  Recursive page list partial.

  Variables (passed via $__view->view):
    $level      — int: nesting level (0 = root)
    $pages      — array: page tree nodes
    $categories — collection of Category models
    $group      — Group object
    $config     — Registry: component config (at level 0)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $cls    = '';
  $params = '';
  if ($level == 0) {
      $cls    = 'item-list pages';
      $reorderUrl = Route::url('index.php?option=com_groups&cn=' . $group->get('cn')
          . '&controller=pages&task=reorder&no_html=1');
      $maxDepth = ($config->get('page_depth', 5) + 1);
      $params = 'data-url="' . $reorderUrl . '" data-max-depth="' . $maxDepth . '"';
  }
@endphp

<ul class="{{ $cls }}" {!! $params !!}>
  @if(count($pages) > 0)
    @foreach($pages as $page)
      @php
        $category = $categories->fetch('id', $page->get('category'));
        $version  = $page->versions(['limit' => 1])->first();
        $itemCls  = ($page->get('home') == 1) ? ' root' : '';
        $checkout = \Components\Groups\Helpers\Pages::getCheckout($page->get('id'));
      @endphp
      <li id="{{ $page->get('id') }}" class="{{ $itemCls }}">
        {!! $__view->view('item')
             ->set('page', $page)
             ->set('category', $category)
             ->set('group', $group)
             ->set('version', $version)
             ->set('checkout', $checkout)
             ->display() !!}

        @if($children = $page->get('children'))
          {!! $__view->view('list')
               ->set('level', 10)
               ->set('pages', $children)
               ->set('categories', $categories)
               ->set('group', $group)
               ->display() !!}
        @endif
      </li>
    @endforeach

    @if($level == 0)
      <div class="item-list-loader"></div>
    @endif
  @elseif($level == 0)
    <li class="no-results">
      <x-empty-state :title="Lang::txt('COM_GROUPS_PAGES_NO_PAGES')" />
    </li>
  @endif
</ul>
