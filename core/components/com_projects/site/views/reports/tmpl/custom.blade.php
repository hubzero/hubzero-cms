{{--
 * Custom reports: CSV export form with date range and column selection
 *
 * Variables:
 *   $option     - Component option string
 *   $controller - Controller name string
 *   $title      - Page title
 *   $msg        - Status message string
 *   $tags       - Tags filter string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Event;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    $__view->css('reports')
        ->css('jquery.ui', 'system')
        ->js('reports');

    $data   = Request::getArray('data', [], 'post');
    $from   = Request::getString('fromdate', Date::of('-1 month')->toLocal('Y-m'));
    $to     = Request::getString('todate', Date::of('now')->toLocal('Y-m'));
    $filter = Request::getString('searchterm', '');

    $formAction = Route::url('index.php?option=' . $option);
    $fromPlaceholder = e(Date::of('-1 month')->toLocal('Y-m'));
    $toPlaceholder   = e(Date::of('now')->toLocal('Y-m'));
@endphp

<header id="content-header" class="reports mb-6">
    <h2>{{ $title }}</h2>
</header>

<section class="main section custom-reports" id="custom-reports">
    @if ($__view->getError() || $msg)
        @if ($__view->getError())
            <div role="alert" class="alert alert-error mb-4">{{ $__view->getError() }}</div>
        @elseif ($msg)
            <div class="alert alert-info mb-4">{{ $msg }}</div>
        @endif
    @endif

    <div class="card bg-base-100 shadow-sm p-6">
        <form id="hubForm" class="full" method="post" action="{{ $formAction }}">
            <fieldset>
                <legend class="text-lg font-semibold">{{ Lang::txt('Download publication data:') }}</legend>

                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="controller" value="{{ $controller }}" />
                <input type="hidden" name="task" value="generate" />
                <input type="hidden" name="no_html" value="1" />

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3 form-group">
                        <label for="from-date">
                            {{ Lang::txt('From') }}:
                            <input
                                type="text"
                                class="input input-bordered w-full datepicker"
                                value="{{ e($from) }}"
                                id="from-date"
                                name="fromdate"
                                placeholder="{{ $fromPlaceholder }}"
                                maxlength="7"
                            />
                        </label>
                    </div>
                    <div class="md:col-span-3 form-group">
                        <label for="to-date">
                            {{ Lang::txt('To') }}:
                            <input
                                type="text"
                                class="input input-bordered w-full datepicker"
                                value="{{ e($to) }}"
                                id="to-date"
                                name="todate"
                                placeholder="{{ $toPlaceholder }}"
                                maxlength="7"
                            />
                        </label>
                    </div>
                    <div class="md:col-span-6 form-group">
                        <label for="searchterm">
                            {{ Lang::txt('Filter by tag') }}:
                            @php
                                $tf = Event::trigger(
                                    'hubzero.onGetMultiEntry',
                                    [['tags', 'searchterm', 'searchterm', '', $filter]]
                                );
                                $tf = implode('', $tf);

                                if (empty($tf)) {
                                    $tf = '<textarea name="searchterm" id="searchterm"'
                                        . ' class="input input-bordered w-full" rows="6" cols="35">'
                                        . e($tags)
                                        . '</textarea>' . "\n";
                                }
                            @endphp
                            {!! $tf !!}
                        </label>
                    </div>
                </div>

                <fieldset class="mt-4">
                    <legend class="text-lg font-semibold">{{ Lang::txt('Include the following information:') }}</legend>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-6">
                            @foreach (['id' => 'Publication ID', 'title' => 'Publication title', 'author' => 'First author', 'version' => 'Version label', 'doi' => 'DOI url'] as $val => $label)
                                <div class="form-group form-check">
                                    <label for="choice-{{ $val }}" class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            class="checkbox checkbox-primary"
                                            name="data[]"
                                            value="{{ $val }}"
                                            id="choice-{{ $val }}"
                                            checked="checked"
                                        />
                                        {{ Lang::txt($label) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="md:col-span-6">
                            @foreach (['downloads' => 'Number of downloads', 'views' => 'Number of page views', 'citations' => 'Number of citations'] as $val => $label)
                                <div class="form-group form-check">
                                    <label for="choice-{{ $val }}" class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            class="checkbox checkbox-primary"
                                            name="data[]"
                                            value="{{ $val }}"
                                            id="choice-{{ $val }}"
                                            checked="checked"
                                        />
                                        {{ Lang::txt($label) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </fieldset>
            </fieldset>

            <div class="flex gap-2 mt-6">
                <input
                    type="submit"
                    class="btn btn-primary icon-download-alt"
                    value="{{ Lang::txt('Download report (CSV)') }}"
                />
            </div>
        </form>
    </div>
</section>
