{{--
  com_media — Folder row for list (table) view

  Variables: $currentFolder (array: name, path), $option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

$tmpl       = Request::getCmd('tmpl', '');
$t          = $tmpl ? '&tmpl=' . $tmpl : '';
$token      = Session::getFormToken();
$folderPath = ltrim($currentFolder['path'], '/');
$dataFolder = '/' . $folderPath;
$folderIcon = Html::asset('image', 'assets/filetypes/folder.svg', '', null, true, true);
$escapedName = $currentFolder['name'];
$modified    = Date::of(filemtime(COM_MEDIA_BASE . $currentFolder['path']));

$folderUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=medialist' . $t
    . '&' . $token . '=1&folder=/' . $folderPath, false
);
$tdWidth = ($tmpl === 'component' && !User::authorise('core.delete', 'com_media')) ? '70' : '60';
@endphp
<tr class="media-item media-item-list">
    <td width="{{ $tdWidth }}%">
        <a class="folder-item"
            data-folder="{{ $dataFolder }}"
            href="{{ $folderUrl }}">
            <span class="media-icon">
                <img src="{{ $folderIcon }}" alt="{{ $escapedName }}" />
            </span>
            <span class="media-name">{{ $currentFolder['name'] }}</span>
        </a>
    </td>
    <td><!-- Nothing here --></td>
    @if ($tmpl !== 'component')
        <td>
            <span class="media-type">{{ Lang::txt('Folder') }}</span>
        </td>
        <td>
            <time class="media-modified"
                datetime="{{ $modified->format('Y-m-d\TH:i:s\Z') }}">
                {{ $modified->toSql() }}
            </time>
        </td>
    @endif
    @if ($tmpl !== 'component' || User::authorise('core.delete', 'com_media'))
        <td>
            <div class="media-preview-inner">
                <span class="media-options-btn"></span>
                <div class="media-options">
                    <ul>
                        @if ($tmpl !== 'component')
                            <li>
                                @php
                                $infoUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=medialist&task=info' . $t
                                    . '&' . $token . '=1'
                                    . '&folder=' . urlencode($currentFolder['path']), false
                                );
                                @endphp
                                <a class="icon-info media-opt-info"
                                    href="{{ $infoUrl }}">
                                    {{ Lang::txt('Info') }}
                                </a>
                            </li>
                        @endif
                        @if (User::authorise('core.delete', 'com_media'))
                            <li><span class="separator"></span></li>
                            <li>
                                @php
                                $deleteUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&task=delete' . $t
                                    . '&' . $token . '=1'
                                    . '&rm=' . urlencode($currentFolder['path']), false
                                );
                                @endphp
                                <a class="icon-trash media-opt-delete"
                                    href="{{ $deleteUrl }}">
                                    {{ Lang::txt('JACTION_DELETE') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </td>
    @endif
</tr>
