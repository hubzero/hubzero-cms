<?php

namespace HubzeroPER\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * A class, function, method or closure body with nothing in it is written {}
 * on the line of the symbol before it, one space away (PER-CS 3, sections 3,
 * 4.4, 7 and 8).
 *
 * A body that holds only a comment is not empty and is left alone.
 */
class EmptyBodySniff implements Sniff
{
    /**
     * The tokens this sniff wants to see.
     *
     * @return  array<int|string>
     */
    public function register()
    {
        return [T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT, T_ENUM, T_FUNCTION, T_CLOSURE];
    }

    /**
     * Check one body.
     *
     * @param   File  $phpcsFile  The file being scanned
     * @param   int   $stackPtr   The token that owns the body
     * @return  void
     */
    public function process(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (!isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer'])) {
            return;
        }

        $opener = $tokens[$stackPtr]['scope_opener'];
        $closer = $tokens[$stackPtr]['scope_closer'];

        if ($phpcsFile->findNext(T_WHITESPACE, $opener + 1, $closer, true) !== false) {
            return;
        }

        if ($closer !== $opener + 1) {
            $fix = $phpcsFile->addFixableError('An empty body must be written {}', $opener, 'NotAbbreviated');

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $opener + 1; $i < $closer; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        $before = $phpcsFile->findPrevious(T_WHITESPACE, $opener - 1, null, true);

        if ($before === $opener - 2 && $tokens[$opener - 1]['content'] === ' ') {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'The {} of an empty body must follow the symbol before it on the same line, after one space',
            $opener,
            'Placement',
        );

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = $before + 1; $i < $opener; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
            $phpcsFile->fixer->addContentBefore($opener, ' ');
            $phpcsFile->fixer->endChangeset();
        }
    }
}
