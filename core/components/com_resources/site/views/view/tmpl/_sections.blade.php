{{--
  Resource tab content sections — renders plugin-provided HTML for each tab.

  Variables:
    $option   — component option string
    $sections — array of plugin sections (each has 'area', 'html')
    $resource — Entry model
    $active   — string, currently active tab name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@foreach($sections as $section)
  @if(!$section['html'])
    @continue
  @endif
  <div class="{{ $section['area'] == $active ? '' : 'hidden' }}"
       id="{{ $section['area'] }}-section"
       role="tabpanel">
    {!! $section['html'] !!}
  </div>
@endforeach
