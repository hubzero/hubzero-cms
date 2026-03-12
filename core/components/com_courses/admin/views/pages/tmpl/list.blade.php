{{--
  Course Pages — File list (tmpl=component, rendered in iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div id="attachments">
  <form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
        method="post"
        id="filelist"
        name="filelist">
    <table>
      <tbody>
        @if(count($docs) == 0)
          <tr>
            <td>{{ Lang::txt('No files found.') }}</td>
          </tr>
        @else
          @php
            $docKeys = array_keys($docs);
          @endphp
          @foreach($docKeys as $docName)
            @php
              $fileName = $docs[$docName];
              $deleteUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=deletefile&delFile=' . $fileName
                  . '&listdir=' . $listdir
                  . '&tmpl=component&subdir=' . $subdir
                  . '&course=' . $course_id
                  . '&' . Session::getFormToken() . '=1', false
              );
              $confirmMsg = Lang::txt('Are you sure you want to delete the file "%s"?', $fileName);
            @endphp
            <tr>
              <td>{{ $fileName }}</td>
              <td>
                <a class="delete-file"
                   href="{{ $deleteUrl }}"
                   data-confirm="{{ $confirmMsg }}"
                   title="{{ Lang::txt('DELETE') }}">
                  <img src="{{ Request::base(true) }}/core/components/{{ $option }}/admin/assets/img/trash.png"
                       width="15"
                       height="15"
                       alt="{{ Lang::txt('DELETE') }}" />
                </a>
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </form>
</div>
