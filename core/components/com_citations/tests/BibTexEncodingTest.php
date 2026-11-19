<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Tests;

use Components\Citations\Helpers\BibTex;
use PHPUnit\Framework\TestCase;

/**
 * BibTeX files exported by older tools are often Windows-1252; their text has to arrive as UTF-8 or it is lost
 * when escaped and refused by the database
 */
class BibTexEncodingTest extends TestCase
{
    /**
     * @return  void
     */
    public function testWindows1252FileIsReadAsUtf8(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'bib');
        file_put_contents(
            (string) $file,
            "@article{mosasaur2026,\n  title = {\x93Mosasaur\x94 jaws \x96 a caf\xe9 study},\n"
            . "  journal = {Pal\xe4ontologische Zeitschrift},\n  year = {2026}\n}\n",
        );

        $bibtex = new BibTex();
        $bibtex->loadFile((string) $file);
        $bibtex->parse();
        unlink((string) $file);

        $this->assertSame('“Mosasaur” jaws – a café study', $bibtex->data[0]['title']);
        $this->assertSame('Paläontologische Zeitschrift', $bibtex->data[0]['journal']);
    }

    /**
     * A UTF-8 line stays as it is, even in a file with Windows-1252 lines
     *
     * @return  void
     */
    public function testUtf8LinesAreKept(): void
    {
        $bibtex = new BibTex();
        $bibtex->addContent("@article{a,\n  title = {Déjà “vu”},\n  journal = {Caf\xe9},\n}\n");
        $bibtex->parse();

        $this->assertSame('Déjà “vu”', $bibtex->data[0]['title']);
        $this->assertSame('Café', $bibtex->data[0]['journal']);
    }
}
