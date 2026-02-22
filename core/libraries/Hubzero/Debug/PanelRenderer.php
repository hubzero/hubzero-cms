<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug;

/**
 * Shared renderer for HTMX/Inertia debug panel markup.
 */
class PanelRenderer
{
    /**
     * Render debug panel wrapper and controls for Alpine-powered debug UI.
     *
     * @param   string  $protocol
     * @param   array   $snapshot
     * @param   array   $options
     * @param   string  $defaultId
     * @param   string  $defaultTitle
     * @param   string  $defaultStoragePrefix
     * @param   string  $panelClass
     * @param   string  $barClass
     * @param   string  $actionsClass
     * @return  string
     */
    public static function render(
        string $protocol,
        array $snapshot,
        array $options,
        string $defaultId,
        string $defaultTitle,
        string $defaultStoragePrefix,
        string $panelClass,
        string $barClass,
        string $actionsClass
    ): string {
        $id = trim((string) ($options['id'] ?? $defaultId));
        if ($id === '') {
            $id = $defaultId;
        }

        $title = (string) ($options['title'] ?? $defaultTitle);
        $class = trim((string) ($options['class'] ?? ''));

        $storageKey = trim((string) ($options['storageKey'] ?? ($defaultStoragePrefix . $id)));
        if ($storageKey === '') {
            $storageKey = $defaultStoragePrefix . $id;
        }

        $json = json_encode($snapshot, JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) {
            $json = '{}';
        }

        $safeId = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeClass = $class !== '' ? ' ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') : '';
        $safeJson = htmlspecialchars($json, ENT_NOQUOTES, 'UTF-8');
        $safeStorageKey = htmlspecialchars($storageKey, ENT_QUOTES, 'UTF-8');
        $safeProtocol = htmlspecialchars(strtolower(trim($protocol)), ENT_QUOTES, 'UTF-8');

        return '<div id="' . $safeId . '" class="' . $panelClass . $safeClass . '"'
            . ' x-data="window.__hzDebugPanel(' . "'" . $safeProtocol . "', '" . $safeStorageKey . "'" . ')"'
            . ' x-init="init($refs.payload.textContent || \'{}\')"'
            . '>'
            . '<script type="application/json" x-ref="payload">' . $safeJson . '</script>'
            . '<div class="' . $barClass . '">'
            . '<strong>' . $safeTitle . '</strong>'
            . '<div class="' . $actionsClass . '">'
            . '<label><span>Mode</span>'
            . '<select x-model="mode">'
            . '<option value="timeline">Timeline</option>'
            . '<option value="snapshot">Snapshot</option>'
            . '</select></label>'
            . '<label x-show="mode === \'timeline\'"><span>Timeline</span>'
            . '<select x-model="timelineKind">'
            . '<option value="request">Request</option>'
            . '<option value="profile">Profile</option>'
            . '<option value="snapshot">Snapshot</option>'
            . '</select></label>'
            . '<label><input type="checkbox" x-model="autoscroll" /> Auto-scroll</label>'
            . '<button type="button" x-on:click="clear()">Clear</button>'
            . '<button type="button" x-on:click="open = !open" '
            . 'x-text="open ? \'Hide debug\' : \'Show debug\'"></button>'
            . '</div>'
            . '</div>'
            . '<pre x-show="open" x-ref="pre" x-text="JSON.stringify(panelData(), null, 2)"></pre>'
            . '</div>';
    }
}
