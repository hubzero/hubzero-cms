<?php

namespace HubzeroPER\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff as PSR2ClassDeclarationSniff;

/**
 * PSR2.Classes.ClassDeclaration, except that a class, interface, trait or enum
 * with nothing in it may keep its {} on the declaration line.
 */
class ClassDeclarationSniff extends PSR2ClassDeclarationSniff
{
    /**
     * Check one declaration.
     *
     * @param   File  $phpcsFile  The file being scanned
     * @param   int   $stackPtr   The class, interface, trait or enum keyword
     * @return  void
     */
    public function process(File $phpcsFile, int $stackPtr)
    {
        if ($this->isAbbreviated($phpcsFile, $stackPtr)) {
            // The keyword, name, extends and implements rules still apply;
            // only the two brace rules are left to HubzeroPER.WhiteSpace.EmptyBody.
            $this->processOpen($phpcsFile, $stackPtr);

            return;
        }

        parent::process($phpcsFile, $stackPtr);
    }

    /**
     * Is the body opened at this token written as {} with nothing between the braces?
     *
     * @param   File  $phpcsFile  The file being scanned
     * @param   int   $stackPtr   A token that owns a scope
     * @return  bool
     */
    private function isAbbreviated(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        if (!isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer'])) {
            return false;
        }

        return $tokens[$stackPtr]['scope_closer'] === $tokens[$stackPtr]['scope_opener'] + 1;
    }
}
