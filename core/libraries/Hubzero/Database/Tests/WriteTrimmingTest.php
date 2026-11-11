<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Drivers\Sqlite\SqliteDriver;
use Hubzero\Database\JoinBuilder;
use Hubzero\Database\Query;
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\WhitespaceNote;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Where the query builder trims the strings it writes, and where it does not
 *
 * NOTE: these tests record the behaviour as it is, not as it ought to be. The
 * builder has trimmed leading and trailing whitespace from every string it
 * writes since 2015 (2.4 did the same), while reads, the legacy Table path and
 * raw SQL keep whitespace. That costs something: wiki text whose first line is
 * a list item (" * x") loses the first bullet, Markdown that opens with an
 * indented code block loses it, a body of blank lines passes "notempty" and is
 * saved empty, and a value written through a model cannot be found by an
 * untrimmed lookup on SQLite or PostgreSQL. It was left alone on purpose
 * (2026-11-10). If an intentional pass changes it - for instance trimming only
 * single-line values - update these tests with it; a failure here otherwise
 * means the behaviour changed by accident.
 */
class WriteTrimmingTest extends TestCase
{
    /**
     * Padded text, as a user might type it: the leading indentation and the
     * trailing newline are both lost on write
     */
    private const PADDED = "    indented first line\n    second line\n";

    /**
     * What the builder stores for it
     */
    private const TRIMMED = "indented first line\n    second line";

    /**
     * @var  SqliteDriver
     */
    private $db;

    /**
     * An in-memory database with a notes table and a tags table
     *
     * @return  void
     */
    protected function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE notes (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT UNIQUE, body TEXT)');
        $pdo->exec('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT, note_id INTEGER, type TEXT)');

        $this->db = $this
            ->getMockBuilder(SqliteDriver::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->db->setConnection($pdo)->setPrefix('');

        Relational::setDefaultConnection($this->db);
        Query::purgeCache();
    }

    /**
     * A model save trims, on insert and on update
     *
     * @return  void
     */
    public function testModelSaveTrims()
    {
        $note = WhitespaceNote::blank();
        $note->set(['title' => ' Padded title ', 'body' => self::PADDED]);
        $this->assertNotFalse($note->save());
        $this->assertSame(['Padded title', self::TRIMMED], $this->row());

        $note->set('body', "  edited  ");
        $this->assertNotFalse($note->save());
        $this->assertSame('edited', $this->row()[1]);
    }

    /**
     * Every query builder write trims: insert values (keyed, by column and
     * several rows), insert with set(), update set() in both forms,
     * insertMany(), insertManyIgnore() and upsert()
     *
     * @return  void
     */
    public function testQueryBuilderWritesTrim()
    {
        $this->db->getQuery()->insert('notes')->values(['title' => ' a ', 'body' => self::PADDED])->execute();
        $this->db->getQuery()->insert('notes')->columns(['title', 'body'])->values([' b ', ' x '])->execute();
        $this->db->getQuery()->insert('notes')->set(['title' => ' c ', 'body' => ' x '])->execute();
        $this->db
            ->getQuery()
            ->insert('notes')
            ->columns(['title', 'body'])
            ->values([[' d ', ' x '], [' e ', ' x ']])
            ->execute();
        $this->db->getQuery()->insertMany('notes', [['title' => ' f ', 'body' => ' x ']]);
        $this->db->getQuery()->insertManyIgnore('notes', [['title' => ' g ', 'body' => ' x ']]);
        $this->db
            ->getQuery()
            ->upsert('notes', ['title' => ' h ', 'body' => ' x '], ['body'], ['title'])
            ->execute();

        $this->assertSame(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'], $this->titles());
        $this->assertSame([self::TRIMMED], $this->column("SELECT DISTINCT body FROM notes WHERE title = 'a'"));
        $this->assertSame(['x'], $this->column("SELECT DISTINCT body FROM notes WHERE title != 'a'"));

        $this->db->getQuery()->update('notes')->set(['body' => ' y '])->whereEquals('title', 'b')->execute();
        $this->db->getQuery()->update('notes')->set('body', ' z ')->whereEquals('title', 'c')->execute();
        $this->assertSame(['y', 'z'], $this->column("SELECT body FROM notes WHERE title IN ('b', 'c') ORDER BY title"));
    }

    /**
     * Values compared in HAVING and in a join's conditions are trimmed too
     *
     * @return  void
     */
    public function testHavingAndJoinValuesTrim()
    {
        $this->pdo()->exec("INSERT INTO notes (title, body) VALUES ('a', 'x')");
        $this->pdo()->exec("INSERT INTO tags (note_id, type) VALUES (1, 'x')");

        $having = $this->db
            ->getQuery()
            ->select('body')
            ->from('notes')
            ->group('body')
            ->having('body', '=', ' x ')
            ->loadColumn();
        $this->assertSame(['x'], $having);

        $joined = $this->db
            ->getQuery()
            ->select('notes.title')
            ->from('notes')
            ->join('tags', function (JoinBuilder $join) {
                $join->on('tags.note_id', '=', 'notes.id')->where('tags.type', '=', ' x ');
            })
            ->loadColumn();
        $this->assertSame(['a'], $joined);
    }

    /**
     * WHERE values, the legacy Table path (insertObject, updateObject) and
     * raw SQL with quote() keep whitespace
     *
     * @return  void
     */
    public function testReadsLegacyWritesAndRawSqlKeepWhitespace()
    {
        $object = (object) ['title' => ' kept ', 'body' => self::PADDED];
        $this->assertNotFalse($this->db->insertObject('notes', $object, 'id'));
        $this->assertSame([' kept ', self::PADDED], $this->row());

        $object->body = ' still kept ';
        $this->assertNotFalse($this->db->updateObject('notes', $object, 'id'));
        $this->assertSame(' still kept ', $this->row()[1]);

        $this->db->setQuery('INSERT INTO notes (title, body) VALUES (' . $this->db->quote(' raw ') . ", 'x')");
        $this->db->query();
        $this->assertContains(' raw ', $this->titles());

        // A value written through a model is stored trimmed, but a lookup is
        // not, so the padded value no longer finds its row (on SQLite and
        // PostgreSQL; MySQL and MariaDB ignore trailing spaces in comparisons
        // under their PAD SPACE collations)
        WhitespaceNote::blank()->set(['title' => 'physics ', 'body' => 'x'])->save();
        $this->assertSame(0, WhitespaceNote::all()->whereEquals('title', 'physics ')->total());
        $this->assertSame(1, WhitespaceNote::all()->whereEquals('title', 'physics')->total());
    }

    /**
     * A body of blank lines passes "notempty", then is saved empty
     *
     * @return  void
     */
    public function testBlankBodyPassesValidationAndIsSavedEmpty()
    {
        $note = WhitespaceNote::blank();
        $note->set(['title' => 'blank', 'body' => "\n\n"]);

        $this->assertNotFalse($note->save());
        $this->assertSame('', $this->row()[1]);
    }

    /**
     * @return  PDO
     */
    private function pdo()
    {
        return $this->db->getConnection();
    }

    /**
     * Title and body of the most recent note
     *
     * @return  array
     */
    private function row()
    {
        return $this->pdo()->query('SELECT title, body FROM notes ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_NUM);
    }

    /**
     * @return  array
     */
    private function titles()
    {
        return $this->column('SELECT title FROM notes ORDER BY id');
    }

    /**
     * @param   string  $sql
     * @return  array
     */
    private function column($sql)
    {
        return $this->pdo()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }
}
