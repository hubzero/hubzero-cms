<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Language\Tests;

use Hubzero\Language\Translator;

/**
 * A Translator subclass that lets us inject paths and bypass App dependencies.
 *
 * The real Translator constructor calls \App::get('config') and uses PATH_APP/PATH_CORE
 * constants, which are set by the project's autoloader. To test the Translator in isolation
 * we subclass it, skip the constructor, and inject state directly.
 */
class TestableTranslator extends Translator
{
    /**
     * Create a Translator without triggering the real constructor.
     * This avoids all filesystem and App facade dependencies.
     */
    public static function make(
        string $lang = 'en-GB',
        bool $debug = false,
        string $client = 'site',
    ): self {
        // Bypass parent constructor entirely
        $ref = new \ReflectionClass(self::class);
        $instance = $ref->newInstanceWithoutConstructor();

        $instance->strings = [];
        $instance->override = [];
        $instance->overridesMerged = false;
        $instance->lang = $lang;
        $instance->default = 'en-GB';
        $instance->debug = $debug;
        $instance->client = $client;
        $instance->paths = [];
        $instance->orphans = [];
        $instance->used = [];
        $instance->errorfiles = [];

        return $instance;
    }

    public function setStrings(array $strings): void
    {
        $this->strings = $strings;
    }

    public function getStrings(): array
    {
        return $this->strings;
    }

    public function setOverrideStrings(array $override): void
    {
        $this->override = $override;
    }

    /**
     * Override load() to bypass the \App::get('config')->get('debug_lang') call.
     * This makes the method testable without the App facade.
     */
    public function load(
        $extension = 'hubzero',
        $basePath = null,
        $lang = null,
        $reload = false,
        $default = true,
    ) {
        if (!$lang) {
            $lang = $this->lang;
        }

        $path = self::getLanguagePath($basePath, $lang);

        $internal = $extension == 'hubzero' || $extension == '';
        $filename = $internal ? $lang : $lang . '.' . $extension;
        $filename = "$path/$filename.ini";

        $result = false;

        if (isset($this->paths[$extension][$filename]) && !$reload) {
            $result = $this->paths[$extension][$filename];
        } else {
            $result = $this->loadLanguage($filename, $extension);
        }

        return $result;
    }
}
