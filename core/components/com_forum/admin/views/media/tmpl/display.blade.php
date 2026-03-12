{{--
  Forum Media — Admin attachments view (loaded in iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $attachments = $post->attachments;

  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&id=' . $post->get('id'),
      false, false
  );
@endphp

<div id="attachments">
  <form action="{{ $formAction }}" method="post" id="filelist">
    @if(count($attachments) == 0)
      <p>{{ Lang::txt('COM_FORUM_NO_FILES_FOUND') }}</p>
    @else
      <table class="admin-table">
        <tbody>
          @foreach($attachments as $attachment)
            @php
              $downloadUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&id=' . $post->get('id')
                  . '&task=download&attachment=' . $attachment->get('id')
                  . '&' . Session::getFormToken() . '=1',
                  false, false
              );

              $deleteUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=delete&attachment=' . $attachment->get('id')
                  . '&id=' . $post->get('id')
                  . '&tmpl=component&' . Session::getFormToken() . '=1',
                  false, false
              );

              $fileExt = Filesystem::extension($attachment->get('filename'));
              $confirmMsg = Lang::txt('COM_FORUM_MEDIA_DELETE_FILE', $attachment->get('filename'));
            @endphp
            <tr>
              <td class="w-full">
                <a download="download"
                   href="{{ $downloadUrl }}"
                   class="file {{ $fileExt }}">
                  {{ trim($attachment->get('filename'), '/\\') }}
                </a>
              </td>
              <td>
                <a class="btn btn-sm btn-error btn-outline"
                   target="media"
                   href="{{ $deleteUrl }}"
                   data-file="{{ $attachment->get('filename') }}"
                   data-confirm="{{ $confirmMsg }}"
                   title="{{ Lang::txt('JACTION_DELETE') }}">
                  {{ Lang::txt('JACTION_DELETE') }}
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    {!! Html::input('token') !!}
  </form>
</div>
