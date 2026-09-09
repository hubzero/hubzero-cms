{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if (User::isGuest())
    <div role="alert" class="alert alert-warning">{{ Lang::txt('MOD_MYSUBMISSIONS_WARNING') }}</div>
@else
    @php
        $laststep = (count($steps) - 1);
    @endphp

    @if ($rows->count())
        @foreach ($rows as $row)
            <div class="card bg-base-100 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h4 class="card-$title text-base">
                        {{ stripslashes($row->title) }}
                        <a class="btn btn-xs btn-outline ml-2"
                            href="{{ Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $row->id) }}"
                        >{{ Lang::txt('JACTION_EDIT') }}</a>
                    </h4>

                    <ul class="list bg-base-100 rounded-box">
                        <li class="list-row">
                            <div class="list-col font-semibold">{{ Lang::txt('MOD_MYSUBMISSIONS_TYPE') }}</div>
                            <div class="list-col grow">{{ $row->type->get('type') }}</div>
                        </li>
                        @php $stepchecks = []; @endphp
                        @for ($i = 1, $n = count($steps); $i < $n; $i++)
                            @if ($i != $laststep)
                                @php
                                    $check = 'step' . $steps[$i] . 'Check';
                                    $stepchecks[$steps[$i]] = $__module->$check($row);
                                @endphp
                                <li class="list-row">
                                    <div class="list-col font-semibold">{{ $steps[$i] }}</div>
                                    <div class="list-col grow">
                                        @if ($stepchecks[$steps[$i]])
                                            <span class="badge badge-success badge-sm">{{ Lang::txt('MOD_MYSUBMISSIONS_COMPLETED') }}</span>
                                        @else
                                            <span class="badge badge-warning badge-sm">{{ Lang::txt('MOD_MYSUBMISSIONS_NOT_COMPLETED') }}</span>
                                        @endif
                                    </div>
                                    <div class="list-col">
                                        <a class="btn btn-xs btn-outline"
                                            href="{{ Route::url('index.php?option=com_resources&task=draft&step=' . $i . '&id=' . $row->id) }}"
                                        >{{ Lang::txt('JACTION_EDIT') }}</a>
                                    </div>
                                </li>
                            @endif
                        @endfor
                    </ul>

                    <div class="card-actions justify-between mt-2">
                        <a class="btn btn-sm btn-error btn-outline"
                            href="{{ Route::url('index.php?option=com_resources&task=discard&id=' . $row->id) }}"
                        >{{ Lang::txt('MOD_MYSUBMISSIONS_DELETE') }}</a>
                        <a class="btn btn-sm btn-primary"
                            href="{{ Route::url('index.php?option=com_resources&task=draft&step=' . $laststep . '&id=' . $row->id) }}"
                        >{{ Lang::txt('MOD_MYSUBMISSIONS_REVIEW_SUBMIT') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-base-content/60">{{ Lang::txt('MOD_MYSUBMISSIONS_NONE') }}</p>
    @endif
@endif
