<?php

namespace HubzeroPER\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff as SquizSniff;

/**
 * Squiz.Functions.MultiLineFunctionDeclaration, except that a function or
 * method with nothing in it may keep its {} on the line of the signature.
 */
class MultiLineFunctionDeclarationSniff extends SquizSniff
{
    /**
     * Check a declaration whose signature is on one line.
     *
     * @param   File   $phpcsFile  The file being scanned
     * @param   int    $stackPtr   The function keyword
     * @param   array  $tokens     The file's tokens
     * @return  void
     */
    public function processSingleLineDeclaration(File $phpcsFile, int $stackPtr, array $tokens)
    {
        if ($tokens[$stackPtr]['code'] === T_CLOSURE || !$this->isAbbreviated($phpcsFile, $stackPtr)) {
            parent::processSingleLineDeclaration($phpcsFile, $stackPtr, $tokens);

            return;
        }

        // The parent would ask for the brace on its own line, which is the one
        // rule that does not hold here. What is left of its checks follows.
        $opener = $tokens[$stackPtr]['parenthesis_opener'];
        $closer = $tokens[$stackPtr]['parenthesis_closer'];
        $last   = $phpcsFile->findPrevious(T_WHITESPACE, $closer - 1, $opener, true);

        if ($tokens[$last]['line'] !== $tokens[$closer]['line']) {
            $phpcsFile->addError(
                'There must not be a newline before the closing parenthesis of a single-line function declaration',
                $closer,
                'CloseBracketNewLine',
            );
        }
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
