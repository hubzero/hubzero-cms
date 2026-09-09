{{--
  Resource license display.

  Variables:
    $license — license object with name, url, title properties

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Route;

  $name = $license->name ?? '';
@endphp

@if(!empty($name))
  @if(substr($name, 0, 6) != 'custom')
    @if($license->url)
      <a target="_blank"
         rel="noopener noreferrer license"
         href="{{ $license->url }}"
         title="{{ e($license->title) }}">
        <p class="{{ $name }} license"></p>
      </a>
    @else
      <p class="{{ $name }} license"></p>
    @endif
  @else
    @php
      $licenseUrl = Route::url(
          'index.php?option=com_resources&task=license&resource='
          . substr($name, 6) . '&no_html=1'
      );
    @endphp
    <a target="_blank"
       rel="noopener noreferrer license"
       class="popup"
       href="{{ $licenseUrl }}">
      <p class="{{ $name }} license"></p>
    </a>
  @endif
@endif
