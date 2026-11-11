<?php

namespace HubzeroPER\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeClosingBraceSniff as SquizScopeClosingBraceSniff;

/**
 * Squiz.WhiteSpace.ScopeClosingBrace, except that the closing brace of a
 * class, function, method or closure with nothing in it may follow the
 * opening one directly.
 */
class ScopeClosingBraceSniff extends SquizScopeClosingBraceSniff
{
    /**
     * The constructs whose empty body is written {}.
     *
     * @var  array<int|string, bool>
     */
    private const ABBREVIATED = [
        T_CLASS      => true,
        T_ANON_CLASS => true,
        T_INTERFACE  => true,
        T_TRAIT      => true,
        T_ENUM       => true,
        T_FUNCTION   => true,
        T_CLOSURE    => true,
    ];

    /**
     * Check one closing brace.
     *
     * @param   File  $phpcsFile  The file being scanned
     * @param   int   $stackPtr   The token that owns the scope
     * @return  void
     */
    public function process(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset(self::ABBREVIATED[$tokens[$stackPtr]['code']]) && $this->isAbbreviated($phpcsFile, $stackPtr)) {
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
