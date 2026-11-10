<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Tests;

use Hubzero\Content\Migration;
use Hubzero\Facades\Facade;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Picking out one extension's migrations, as `muse migration -e` does
 */
class MigrationFindTest extends TestCase
{
    /**
     * @var  string
     */
    private $root;

    /**
     * The database the test bootstrap registered, put back afterwards
     *
     * @var  mixed
     */
    private $db = null;

    /**
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . '/hz-migrations-' . getmypid() . '-' . uniqid();

        // The constructor checks for the migrations table; give it one
        $app = Facade::getApplication();
        if ($app->has('db')) {
            $this->db = $app->raw('db');
            $app->forget('db');
        }
        $app['db'] = fn() => new class {
            public function connected()
            {
                return true;
            }
            public function getTableList()
            {
                return ['migrations'];
            }
            public function getPrefix()
            {
                return 'jos_';
            }
        };
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        $app = Facade::getApplication();
        $app->forget('db');
        if ($this->db !== null) {
            $app['db'] = $this->db;
        }

        exec('rm -rf ' . escapeshellarg($this->root));

        parent::tearDown();
    }

    /**
     * @return  array
     */
    public static function suffixes(): array
    {
        return [
            'component'            => ['com_blog', 'ComBlog'],
            'module with words'    => ['mod_articles_latest', 'ModArticlesLatest'],
            'plugin'               => ['plg_groups_forum', 'PlgGroupsForum'],
            'hyphenated group'     => ['plg_editors-xtd_article', 'PlgEditorsXtdArticle'],
            'core'                 => ['core', 'Core'],
        ];
    }

    /**
     * @param   string  $extension
     * @param   string  $suffix
     * @return  void
     */
    #[DataProvider('suffixes')]
    public function testSuffix(string $extension, string $suffix): void
    {
        $this->assertSame($suffix, Migration::extensionSuffix($extension));
    }

    /**
     * A plugin in a hyphenated group is found by its directory, though no
     * class name can spell the hyphen
     *
     * @return  void
     */
    public function testFindsAHyphenatedGroupsPluginByItsDirectory(): void
    {
        $found = $this->find('plg_editors-xtd_article', [
            'plugins/editors-xtd/article' => ['Migration20170831000000PlgEditorsextdArticle.php'],
            'plugins/editors-xtd/image'   => ['Migration20170831000000PlgEditorsextdImage.php'],
        ]);

        $this->assertSame(['Migration20170831000000PlgEditorsextdArticle.php'], $found);
    }

    /**
     * An extension's migrations are found by name in the core directory and
     * by place in its own
     *
     * @return  void
     */
    public function testFindsByNameAndByDirectory(): void
    {
        $found = $this->find('mod_articles_latest', [
            ''                             => [
                'Migration20140101000000ModArticlesLatest.php',
                'Migration20140101000000ModArticlesLatestExtra.php',
                'Migration20140101000000ComBlog.php',
            ],
            'modules/mod_articles_latest'  => ['Migration20190109000000ModArticlesLatest.php'],
            'modules/mod_articles_archive' => ['Migration20190109000000ModArticlesArchive.php'],
        ]);

        $this->assertSame([
            'Migration20140101000000ModArticlesLatest.php',
            'Migration20190109000000ModArticlesLatest.php',
        ], $found);
    }

    /**
     * Build a tree of migrations and find one extension's
     *
     * @param   string  $extension
     * @param   array   $tree       Directory => migration file names
     * @return  array   File names found
     */
    private function find(string $extension, array $tree): array
    {
        $migration = null;

        foreach ($tree as $dir => $files) {
            $base = rtrim($this->root . '/' . $dir, '/');
            mkdir($base . '/migrations', 0700, true);

            foreach ($files as $file) {
                touch($base . '/migrations/' . $file);
            }

            if (!$migration) {
                $migration = new Migration($base);
            } else {
                $migration->addSearchPath($base);
            }
        }

        $migration->find($extension);

        $files = new \ReflectionProperty(Migration::class, 'files')->getValue($migration);

        return array_values(array_map('basename', $files));
    }
}
