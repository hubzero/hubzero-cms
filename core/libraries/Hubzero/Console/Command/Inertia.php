<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

/**
 * Inertia developer tooling command.
 */
class Inertia extends Base implements CommandInterface
{
    /**
     * @return  void
     */
    public function execute()
    {
        $this->help();
    }

    /**
     * Scaffold Inertia boilerplate by delegating to scaffolding type.
     *
     * @return  void
     */
    public function scaffold()
    {
        $component = $this->arguments->getOpt(3);
        if ($component && !$this->arguments->getOpt(4)) {
            $this->arguments->setOpt(4, $component);
        }

        $this->arguments->setOpt(3, 'inertia');
        \Hubzero\Facades\App::get('client')->call('scaffolding', 'create', $this->arguments, $this->output);
    }

    /**
     * Lint Inertia templates for common issues.
     *
     * @return  void
     */
    public function lint()
    {
        $path = $this->arguments->getOpt('path', $this->arguments->getOpt(3, getcwd()));
        $path = (string) $path;
        if (!is_dir($path)) {
            $this->output->error('Error: path not found: ' . $path);
        }

        $strict = (bool) $this->arguments->getOpt('strict', false);
        $warnings = array();

        $files = $this->findTemplateFiles($path);
        foreach ($files as $file) {
            $contents = (string) @file_get_contents($file);
            if ($contents === '') {
                continue;
            }

            $warnings = array_merge($warnings, $this->lintFile($file, $contents));
        }

        if (empty($warnings)) {
            $this->output->addLine('Inertia lint passed with no warnings.', 'success');
            return;
        }

        foreach ($warnings as $warning) {
            $location = $warning['line'] > 0 ? $warning['file'] . ':' . $warning['line'] : $warning['file'];
            $this->output->addLine('[warn] ' . $location . ' - ' . $warning['message'], 'warning');
        }

        $this->output->addLine('Total warnings: ' . count($warnings), 'warning');
        if ($strict) {
            $this->output->error('Inertia lint failed in strict mode.');
        }
    }

    /**
     * @return  void
     */
    public function help()
    {
        $this->output
            ->addOverview('Inertia developer tooling. Includes scaffold and template lint commands.')
            ->addArgument(
                'scaffold [component]',
                'Generate Inertia controller/view boilerplate inside an existing component.',
                'Example: muse inertia scaffold com_projects --view=dashboard --controller=projects --with-test'
            )
            ->addArgument(
                'lint [path]',
                'Warn about common Inertia template issues (missing root mount node, hardcoded routes).',
                'Example: muse inertia lint core/components/com_polls2/site/views --strict'
            );
    }

    /**
     * @param   string  $root
     * @return  array
     */
    protected function findTemplateFiles(string $root): array
    {
        $files = array();
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iter as $info) {
            if (!$info instanceof \SplFileInfo || !$info->isFile()) {
                continue;
            }

            $ext = strtolower((string) $info->getExtension());
            if (!in_array($ext, array('php', 'html', 'htm'), true)) {
                continue;
            }

            $files[] = $info->getPathname();
        }

        sort($files);

        return $files;
    }

    /**
     * @param   string  $file
     * @param   string  $contents
     * @return  array
     */
    protected function lintFile(string $file, string $contents): array
    {
        $warnings = array();
        $lower = strtolower($contents);

        $hasInertiaBehavior = strpos($lower, "behavior('inertia") !== false
            || strpos($lower, 'behavior("inertia') !== false;
        $hasRootNode = strpos($contents, 'inertiaRootNode(') !== false
            || strpos($contents, 'Inertia::renderRootNode(') !== false
            || strpos($lower, 'data-page=') !== false;
        if ($hasInertiaBehavior && !$hasRootNode) {
            $warnings[] = array(
                'file' => $file,
                'line' => 1,
                'message' => 'Template appears to use Inertia without a root mount'
                    . ' node (`inertiaRootNode` / `Inertia::renderRootNode`).'
            );
        }

        if (
            preg_match_all(
                '/(href|action|hx-get|hx-post|hx-put|hx-patch|hx-delete)=["\']([^"\']+)["\']/i',
                $contents,
                $routeMatches,
                PREG_OFFSET_CAPTURE
            )
        ) {
            foreach ($routeMatches[2] as $match) {
                $url = trim((string) $match[0]);
                $offset = (int) $match[1];

                if (
                    preg_match('/(^|[?&])option=com_[^&]+(&|$)/i', $url)
                    && preg_match('/(^|[?&])task=[^&]+(&|$)/i', $url)
                ) {
                    $warnings[] = array(
                        'file' => $file,
                        'line' => $this->lineNumberFromOffset($contents, $offset),
                        'message' => 'Hardcoded component/task route found;'
                            . ' prefer Inertia/route helper generation.'
                    );
                }
            }
        }

        return $warnings;
    }

    /**
     * @param   string  $contents
     * @param   int     $offset
     * @return  int
     */
    protected function lineNumberFromOffset(string $contents, int $offset): int
    {
        return substr_count(substr($contents, 0, max(0, $offset)), "\n") + 1;
    }
}
