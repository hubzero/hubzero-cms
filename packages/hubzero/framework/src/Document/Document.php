<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Document;

/**
 * Page state service.
 *
 * Components push title, stylesheets, scripts, and metadata into this
 * singleton. The template rendering layer reads from it to assemble
 * the final HTML page.
 */
class Document
{
    private string $type = 'html';
    private string $title = '';
    private string $description = '';
    private array $buffers = [];
    private array $stylesheets = [];
    private array $scripts = [];
    private array $styleDeclarations = [];
    private array $scriptDeclarations = [];
    private array $metadata = [];
    private array $customTags = [];

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function addStyleSheet(
        string $url,
        string $type = 'text/css',
        string $media = null,
        array $attribs = []
    ): static {
        $this->stylesheets[$url] = [
            'url' => $url,
            'type' => $type,
            'media' => $media,
            'attribs' => $attribs,
        ];
        return $this;
    }

    public function addScript(
        string $url,
        string $type = 'text/javascript',
        bool $defer = false,
        bool $async = false
    ): static {
        $this->scripts[$url] = [
            'url' => $url,
            'type' => $type,
            'defer' => $defer,
            'async' => $async,
        ];
        return $this;
    }

    public function addStyleDeclaration(string $content, string $type = 'text/css'): static
    {
        $this->styleDeclarations[] = ['content' => $content, 'type' => $type];
        return $this;
    }

    public function addScriptDeclaration(string $content, string $type = 'text/javascript'): static
    {
        $this->scriptDeclarations[] = ['content' => $content, 'type' => $type];
        return $this;
    }

    public function setMetaData(string $name, string $content, bool $httpEquiv = false): static
    {
        $this->metadata[] = [
            'name' => $name,
            'content' => $content,
            'http_equiv' => $httpEquiv,
        ];
        return $this;
    }

    public function addCustomTag(string $tag): static
    {
        $this->customTags[] = $tag;
        return $this;
    }

    /**
     * Set a content buffer by type and name.
     */
    public function setBuffer(string $content, mixed $options = []): static
    {
        $args = func_get_args();
        if (!is_array($args[1]) && func_num_args() > 1) {
            $options = ['type' => $args[1], 'name' => $args[2] ?? null];
        }
        $this->buffers[$options['type'] ?? 'component'][$options['name'] ?? null] = $content;
        return $this;
    }

    /**
     * Get a content buffer by type and name.
     */
    public function getBuffer(?string $type = null, ?string $name = null, array $attribs = []): mixed
    {
        if ($type === null) {
            return $this->buffers;
        }
        return $this->buffers[$type][$name] ?? null;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return self for legacy compatibility.
     * The old system used Document::instance() to get the document singleton.
     */
    public function instance(): static
    {
        return $this;
    }

    /**
     * Get all collected head data for template rendering.
     */
    public function getHeadData(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'stylesheets' => $this->stylesheets,
            'scripts' => $this->scripts,
            'styleDeclarations' => $this->styleDeclarations,
            'scriptDeclarations' => $this->scriptDeclarations,
            'metadata' => $this->metadata,
            'customTags' => $this->customTags,
        ];
    }

    /**
     * Replace head data wholesale.
     *
     * Used by legacy Html\Builder\Behavior::_pushScriptTo() to reorder
     * scripts. The legacy format keys scripts by URL with values
     * containing 'mime', 'defer', 'async'. We normalise to our format.
     */
    public function setHeadData(array $data): static
    {
        if (isset($data['scripts'])) {
            $this->scripts = [];
            foreach ($data['scripts'] as $url => $attrs) {
                $this->scripts[$url] = [
                    'url' => $url,
                    'type' => $attrs['mime'] ?? $attrs['type'] ?? 'text/javascript',
                    'defer' => $attrs['defer'] ?? false,
                    'async' => $attrs['async'] ?? false,
                ];
            }
        }

        if (isset($data['stylesheets'])) {
            $this->stylesheets = [];
            foreach ($data['stylesheets'] as $url => $attrs) {
                $this->stylesheets[$url] = [
                    'url' => $url,
                    'type' => $attrs['mime'] ?? $attrs['type'] ?? 'text/css',
                    'media' => $attrs['media'] ?? null,
                    'attribs' => $attrs['attribs'] ?? [],
                ];
            }
        }

        return $this;
    }
}
