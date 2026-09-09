{{--
  Admin toolbar — daisyUI button rendering

  Receives $buttons array from Toolbar::getButtons().
  Each entry: [Type, ...$params] matching the button's fetchButton() signature.

  Button types:
    Standard: [type, icon, textKey, task, listSelect]
    Confirm:  [type, confirmMsg, icon, textKey, task, listSelect]
    Link:     [type, icon, textKey, url, target?]
    Help:     [type, url, width, height]
    Popup:    [type, icon, textKey, url, width?, height?]
    Separator: [type, name, width?]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
  // SVG icons keyed by toolbar icon name
  $icons = [
      'save'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
      'apply'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />',
      'save-new'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />',
      'save-copy' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />',
      'new'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />',
      'publish'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
      'unpublish' => '<circle cx="12" cy="12" r="9"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
      'refresh'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />',
      'delete'    => '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />',
      'trash'     => '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />',
      'cancel'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />',
      'back'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />',
      'edit'      => '<path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />',
      'options'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
      'help'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />',
      'checkin'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />',
      'default'   => '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />',
      'assign'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />',
      'archive'   => '<path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />',
      'unarchive' => '<path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />',
      'copy'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />',
  ];

  // Map icon names to button $style variant
  // 'outline-X' = colored border, 'neutral' = subtle border, 'ghost' = text-only
  $btnVariant = [
      'save'      => 'outline-primary',
      'apply'     => 'outline-primary',
      'save-new'  => 'outline-primary',
      'save-copy' => 'neutral',
      'new'       => 'outline-primary',
      'publish'   => 'outline-success',
      'unpublish' => 'outline-warning',
      'delete'    => 'outline-error',
      'trash'     => 'outline-error',
      'cancel'    => 'ghost',
      'back'      => 'ghost',
      'edit'      => 'neutral',
      'options'   => 'ghost',
      'help'      => 'ghost',
      'checkin'   => 'neutral',
      'default'   => 'neutral',
      'assign'    => 'neutral',
      'archive'   => 'neutral',
      'unarchive' => 'neutral',
      'refresh'   => 'neutral',
      'copy'      => 'neutral',
  ];
@endphp

@php
  /**
   * Render a toolbar button as HTML.
   */
  $renderBtn = function (string $id, string $text, string $icon, string $variant, array $attrs = []) use ($icons) {
      $svg = isset($icons[$icon])
          ? '<svg class="admin-tb-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">' . $icons[$icon] . '</svg>'
          : '';

      // Build CSS classes from variant
      switch ($variant) {
          case 'outline-primary':
              $cls = 'admin-tb-btn admin-tb-outline-primary';
              break;
          case 'outline-success':
              $cls = 'admin-tb-btn admin-tb-outline-success';
              break;
          case 'outline-warning':
              $cls = 'admin-tb-btn admin-tb-outline-warning';
              break;
          case 'outline-error':
              $cls = 'admin-tb-btn admin-tb-outline-error';
              break;
          case 'neutral':
              $cls = 'admin-tb-btn admin-tb-neutral';
              break;
          case 'ghost':
          default:
              $cls = 'admin-tb-btn admin-tb-ghost';
              break;
      }

      // Merge extra classes from attrs
      if (isset($attrs['class-extra'])) {
          $cls .= ' ' . $attrs['class-extra'];
          unset($attrs['class-extra']);
      }

      // Build attribute string — values from Lang::txt() and Route::url(, false)
      // are already HTML-encoded, so use them directly to avoid double-encoding.
      $attrStr = '';
      foreach ($attrs as $k => $v) {
          $attrStr .= ' ' . $k . '="' . $v . '"';
      }

      return '<a id="' . e($id) . '" class="' . $cls . '" aria-label="' . e($text) . '"' . $attrStr . '>'
           . $svg . '<span>' . $text . '</span></a>';
  };
@endphp

<div class="admin-tb-group">
  @foreach($buttons as $btn)
    @php $type = $btn[0] ?? ''; @endphp

    @if($type === 'Separator')
      <div class="admin-tb-sep" aria-hidden="true"></div>
      @continue
    @endif

    @if($type === 'Standard')
      @php
        $icon    = $btn[1] ?? '';
        $textKey = $btn[2] ?? '';
        $task    = $btn[3] ?? '';
        $list    = $btn[4] ?? false;
        $text    = Lang::txt($textKey);
        $variant = $btnVariant[$icon] ?? 'neutral';

        $attrs = [
            'href'       => '#',
            'data-$title' => $text,
            'data-task'  => $task,
        ];
        $cls = 'toolbar toolbar-submit';
        if ($list) {
            $cls .= ' toolbar-list';
            $attrs['data-$message'] = Lang::txt('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
        }
        $attrs['class-extra'] = $cls;
      @endphp
      {!! $renderBtn('toolbar-' . $icon, $text, $icon, $variant, $attrs) !!}

    @elseif($type === 'Confirm')
      @php
        $confirmMsg = Lang::txt($btn[1] ?? '', true);
        $icon       = $btn[2] ?? '';
        $textKey    = $btn[3] ?? '';
        $task       = $btn[4] ?? '';
        $list       = $btn[5] ?? true;
        $text       = Lang::txt($textKey);
        $variant    = $btnVariant[$icon] ?? 'neutral';

        $attrs = [
            'href'         => '#',
            'data-$title'   => $text,
            'data-task'    => $task,
            'data-confirm' => $confirmMsg,
        ];
        $cls = 'toolbar toolbar-confirm';
        if ($list) {
            $cls .= ' toolbar-list';
            $attrs['data-$message'] = Lang::txt('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
        }
        $attrs['class-extra'] = $cls;
      @endphp
      {!! $renderBtn('toolbar-' . $icon, $text, $icon, $variant, $attrs) !!}

    @elseif($type === 'Link')
      @php
        $icon    = $btn[1] ?? '';
        $textKey = $btn[2] ?? '';
        $url     = $btn[3] ?? '#';
        $target  = $btn[4] ?? null;
        $text    = Lang::txt($textKey);
        $variant = $btnVariant[$icon] ?? 'neutral';

        $attrs = ['href' => $url, 'data-$title' => $text];
        if ($target) {
            $attrs['target'] = $target;
        }
      @endphp
      {!! $renderBtn('toolbar-' . $icon, $text, $icon, $variant, $attrs) !!}

    @elseif($type === 'Help')
      @php
        $helpUrl = $btn[1] ?? '#';
        $width   = $btn[2] ?? 700;
        $height  = $btn[3] ?? 500;
        $text    = Lang::txt('JTOOLBAR_HELP');

        if (
            strpos($helpUrl, '?') === false
            && strpos($helpUrl, '&') === false
            && substr($helpUrl, 0, 4) !== 'http'
        ) {
            $helpUrl = Route::url(
                'index.php?option=com_help&component='
                . Request::getCmd('option')
                . '&page=' . $helpUrl, false
            );
        }

        $attrs = [
            'href'        => '#',
            'data-href'   => $helpUrl,
            'data-$title'  => $text,
            'data-width'  => $width,
            'data-height' => $height,
            'rel'         => 'help',
            'class-extra' => 'toolbar toolbar-popup',
        ];
      @endphp
      {!! $renderBtn('toolbar-help', $text, 'help', 'ghost', $attrs) !!}

    @elseif($type === 'Popup')
      @php
        $icon    = $btn[1] ?? '';
        $textKey = $btn[2] ?? '';
        $popUrl  = $btn[3] ?? '#';
        $width   = $btn[4] ?? 640;
        $height  = $btn[5] ?? 480;
        $text    = Lang::txt($textKey);
        $variant = $btnVariant[$icon] ?? 'neutral';

        $attrs = [
            'href'        => '#',
            'data-href'   => $popUrl,
            'data-$title'  => $text,
            'data-width'  => $width,
            'data-height' => $height,
            'class-extra' => 'toolbar toolbar-popup',
        ];
      @endphp
      {!! $renderBtn('toolbar-' . $icon, $text, $icon, $variant, $attrs) !!}
    @endif
  @endforeach
</div>
