{{--
  Publications content display partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;

  $database = App::get('db');
@endphp

@if(!$pub->_attachments)
  <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_CONTENT') }}</p>
@else
  @php
    $prime  = $pub->_attachments[1] ?? null;
    $second = $pub->_attachments[2] ?? null;
  @endphp

  @if(isset($pub->_curationModel))
    @php
      $prime   = $pub->_curationModel->getElements(1);
      $second  = $pub->_curationModel->getElements(2);
      $gallery = $pub->_curationModel->getElements(3);
      $attModel = new \Components\Publications\Models\Attachments($database);

      foreach ($prime  as $i => $elm) { $prime[$i]->manifest->params->typeParams->multiZip  = 0; }
      foreach ($second as $i => $elm) { $second[$i]->manifest->params->typeParams->multiZip = 0; }
      foreach ($gallery as $i => $elm) { $gallery[$i]->manifest->params->typeParams->multiZip = 0; }

      $primaryList = $attModel->listItems($prime, $pub, 'administrator');
      $secondList  = $attModel->listItems($second, $pub, 'administrator');
      $galleryList = $attModel->listItems($gallery, $pub, 'administrator');
    @endphp

    <h5 class="font-semibold mb-1">{{ Lang::txt('COM_PUBLICATIONS_PRIMARY_CONTENT') }}</h5>
    @if($primaryList)
      {!! $primaryList !!}
    @else
      <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_CONTENT') }}</p>
    @endif

    <h5 class="font-semibold mt-3 mb-1">{{ Lang::txt('COM_PUBLICATIONS_SUPPORTING_CONTENT') }}</h5>
    @if($secondList)
      {!! $secondList !!}
    @else
      <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_CONTENT') }}</p>
    @endif

    <h5 class="font-semibold mt-3 mb-1">{{ Lang::txt('COM_PUBLICATIONS_GALLERY') }}</h5>
    @if($galleryList)
      {!! $galleryList !!}
    @else
      <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_CONTENT') }}</p>
    @endif

  @else
    <h5 class="font-semibold mb-1">{{ Lang::txt('COM_PUBLICATIONS_PRIMARY_CONTENT') }}</h5>
    @if($prime)
      <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($prime as $att)
          @php
            $type  = $att->type;
            $ext   = explode('.', $att->path);
            if ($att->type == 'file') { $type = strtoupper(end($ext)); }
            $title = $att->title ?: $att->path;
          @endphp
          <li>
            <span class="badge badge-ghost badge-sm">{{ $type }}</span>
            {{ $title }}
            @if($att->title && $att->title != $att->path)
              <span class="block text-xs text-muted-foreground ml-4">{{ $att->path }}</span>
            @endif
          </li>
        @endforeach
      </ul>
    @else
      <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_CONTENT') }}</p>
    @endif

    <h5 class="font-semibold mt-3 mb-1">{{ Lang::txt('COM_PUBLICATIONS_SUPPORTING_CONTENT') }}</h5>
    @if($second)
      <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($second as $att)
          @php
            $type = $att->type;
            $ext  = explode('.', $att->path);
            if ($att->type == 'file') { $type = strtoupper(end($ext)); }
          @endphp
          <li>
            <span class="badge badge-ghost badge-sm">{{ $type }}</span>
            {{ $att->title ?: $att->path }}
            @if($att->title && $att->title != $att->path)
              <span class="block text-xs text-muted-foreground ml-4">{{ $att->path }}</span>
            @endif
          </li>
        @endforeach
      </ul>
    @else
      <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_CONTENT') }}</p>
    @endif
  @endif
@endif
