<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

/**
 * HTMX developer tooling command.
 */
class Htmx extends Base implements CommandInterface
{
    /**
     * @return  void
     */
    public function execute()
    {
        $this->help();
    }

    /**
     * Scaffold HTMX boilerplate by delegating to scaffolding type.
     *
     * @return  void
     */
    public function scaffold()
    {
        $component = $this->arguments->getOpt(3);
        if ($component && !$this->arguments->getOpt(4)) {
            $this->arguments->setOpt(4, $component);
        }

        $this->arguments->setOpt(3, 'htmx');
        \App::get('client')->call('scaffolding', 'create', $this->arguments, $this->output);
    }

    /**
     * Lint HTMX template files for common issues.
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
            $this->output->addLine('HTMX lint passed with no warnings.', 'success');
            return;
        }

        foreach ($warnings as $warning) {
            $location = $warning['line'] > 0 ? $warning['file'] . ':' . $warning['line'] : $warning['file'];
            $this->output->addLine('[warn] ' . $location . ' - ' . $warning['message'], 'warning');
        }

        $this->output->addLine('Total warnings: ' . count($warnings), 'warning');
        if ($strict) {
            $this->output->error('HTMX lint failed in strict mode.');
        }
    }

    /**
     * @return  void
     */
    public function help()
    {
        $this->output
            ->addOverview('HTMX developer tooling. Includes scaffold and template lint commands.')
            ->addArgument(
                'scaffold [component]',
                'Generate HTMX controller/view boilerplate inside an existing component.',
                'Example: muse htmx scaffold com_projects --view=items --controller=items --with-test'
            )
            ->addArgument(
                'lint [path]',
                'Warn about common HTMX template issues (target/swap/id/layout mistakes).',
                'Example: muse htmx lint core/components/com_todo/site/views --strict'
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

        if (
            $this->looksLikeFragmentFile($file) && (
            strpos($lower, '<!doctype html') !== false
            || strpos($lower, '<html') !== false
            || strpos($lower, '<body') !== false
            )
        ) {
            $warnings[] = array(
                'file' => $file,
                'line' => 1,
                'message' => 'Fragment-like template appears to contain full document wrappers.'
            );
        }

        if (preg_match_all('/\bid=["\']([^"\']+)["\']/i', $contents, $idMatches, PREG_OFFSET_CAPTURE)) {
            $seen = array();
            foreach ($idMatches[1] as $match) {
                $id = (string) $match[0];
                $offset = (int) $match[1];
                if (isset($seen[$id])) {
                    $warnings[] = array(
                        'file' => $file,
                        'line' => $this->lineNumberFromOffset($contents, $offset),
                        'message' => 'Duplicate id attribute "' . $id . '".'
                    );
                    continue;
                }

                $seen[$id] = true;
            }
        }

        if (preg_match_all('/\bhx-target=["\']([^"\']*)["\']/i', $contents, $targetMatches, PREG_OFFSET_CAPTURE)) {
            foreach ($targetMatches[1] as $match) {
                $target = trim((string) $match[0]);
                $offset = (int) $match[1];
                if ($target === '' || $target === '#') {
                    $warnings[] = array(
                        'file' => $file,
                        'line' => $this->lineNumberFromOffset($contents, $offset),
                        'message' => 'hx-target appears empty/invalid.'
                    );
                }
            }
        }

        if (preg_match_all('/\bhx-swap=["\']([^"\']+)["\']/i', $contents, $swapMatches, PREG_OFFSET_CAPTURE)) {
            $allowed = array(
                'innerhtml',
                'outerhtml',
                'beforebegin',
                'afterbegin',
                'beforeend',
                'afterend',
                'delete',
                'none'
            );
            foreach ($swapMatches[1] as $match) {
                $swap = trim((string) $match[0]);
                $offset = (int) $match[1];
                $token = strtolower((string) strtok($swap, ' '));
                if (!in_array($token, $allowed, true)) {
                    $warnings[] = array(
                        'file' => $file,
                        'line' => $this->lineNumberFromOffset($contents, $offset),
                        'message' => 'Unrecognized hx-swap value "' . $swap . '".'
                    );
                }
            }
        }

        if (
            preg_match_all(
                '/\bhx-(get|post|put|patch|delete)=["\']([^"\']+)["\']/i',
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
                        'message' => 'Hardcoded component/task route in hx-* attribute;'
                            . ' prefer Htmx::actionUrl(...).'
                    );
                }
            }
        }

        return $warnings;
    }

    /**
     * @param   string  $file
     * @return  bool
     */
    protected function looksLikeFragmentFile(string $file): bool
    {
        $name = strtolower(basename($file));
        if (in_array($name, array('fragment.php', 'list.php', 'partial.php'), true)) {
            return true;
        }

        return strpos(strtolower($file), '/tmpl/') !== false && strpos($name, 'default') === false;
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
