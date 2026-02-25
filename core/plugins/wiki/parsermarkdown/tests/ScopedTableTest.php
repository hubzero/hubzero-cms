<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Hubzero\Facades\Facade;
use Hubzero\Facades\Html;
use Plugins\Wiki\Parsermarkdown\MarkdownParser;
use Plugins\Wiki\Parsermarkdown\Markdown\GithubMarkdown;
use Plugins\Wiki\Parsermarkdown\Markdown\MarkdownExtra;

/**
 * The wiki's markdown flavours mark table header cells as column headers
 */
class ScopedTableTest extends TestCase
{
    /**
     * A pipe table in the two flavours that support tables
     *
     * @var  string
     */
    private $table = "| Name | Count |\n|:-----|------:|\n| alpha | 1 |\n| beta | 22 |\n";

    /**
     * Parsers under test
     *
     * @return  array
     */
    public static function parsers(): array
    {
        return [
            'GitHub flavour' => [GithubMarkdown::class],
            'Extra flavour'  => [MarkdownExtra::class],
        ];
    }

    #[DataProvider('parsers')]
    public function testHeaderCellsCarryScope(string $class): void
    {
        $html = (new $class())->parse($this->table);

        $this->assertSame(2, substr_count($html, '<th scope="col"'), $class);
        $this->assertStringNotContainsString('<th>', $html);
        $this->assertStringNotContainsString('<th align', $html);
    }

    #[DataProvider('parsers')]
    public function testDataCellsAndStructureAreUnchanged(string $class): void
    {
        $html = (new $class())->parse($this->table);

        $this->assertStringContainsString("<table>\n<thead>\n", $html);
        $this->assertStringContainsString("</thead>\n<tbody>\n", $html);
        $this->assertStringContainsString('<th scope="col" align="left">Name</th>', $html);
        $this->assertStringContainsString('<th scope="col" align="right">Count</th>', $html);
        $this->assertStringContainsString('<td align="left">alpha</td><td align="right">1</td>', $html);
        $this->assertStringNotContainsString('scope="col" align="left">alpha', $html);
    }

    /**
     * The plugin's own parse() path, style setting and all, ends in a scoped table.
     *
     * parse() runs the math pass, which asks the Html builder for a behaviour;
     * the test container has no builder, so one is stubbed for this test only.
     */
    public function testPluginParseRendersScopedTable(): void
    {
        $app = Facade::getApplication();
        $this->assertNotNull($app, 'the test bootstrap wires the facades');

        Html::swap(new class {
            public function __call($method, $args)
            {
                return '';
            }
        });

        try {
            foreach (['GithubMarkdown', 'MarkdownExtra'] as $style) {
                $html = (new MarkdownParser(['style' => $style]))->parse($this->table);

                $this->assertSame(2, substr_count($html, '<th scope="col"'), $style);
                $this->assertStringContainsString('<td align="left">alpha</td>', $html, $style);
            }

            $html = (new MarkdownParser(['style' => 'Markdown']))->parse($this->table);
            $this->assertStringNotContainsString('<table>', $html);

            // An unknown style falls back to plain Markdown rather than
            // building a class name from the setting
            $html = (new MarkdownParser(['style' => '..\\Nope']))->parse($this->table);
            $this->assertStringNotContainsString('<table>', $html);
        } finally {
            $app->forget('html.builder');
        }
    }

    /**
     * Plain Markdown has no tables and is used unchanged from the package
     */
    public function testPlainMarkdownLeavesPipesAlone(): void
    {
        $html = (new \cebe\markdown\Markdown())->parse($this->table);

        $this->assertStringNotContainsString('<table>', $html);
    }
}
