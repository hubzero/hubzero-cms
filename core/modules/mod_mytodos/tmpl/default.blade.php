{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div{!! ($params->get('moduleclass')) ? ' class="' . $params->get('moduleclass') . '"' : '' !!}>
    <h4>
        <a href="{{ Route::url('index.php?option=com_projects') }}">
            {{ Lang::txt('MOD_MYTODOS_ASSIGNED') }}
        </a>
    </h4>

    @if (count($rows) <= 0)
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYTODOS_NO_TODOS') }}</em></p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows as $row)
                @php
                    $todoUrl = Route::url('index.php?option=com_projects&alias=' . $row->alias . '&active=todo/view/?todoid=' . $row->id);
                    $projUrl = Route::url('index.php?option=com_projects&alias=' . $row->alias . '&active=todo');
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $todoUrl }}">{{ stripslashes($row->content) }}</a>
                        <span class="text-xs text-base-content/60">
                            {{ Lang::txt('MOD_MYTODOS_PROJECT') }}:
                            <a href="{{ $projUrl }}">{{ stripslashes($row->title) }}</a>
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
