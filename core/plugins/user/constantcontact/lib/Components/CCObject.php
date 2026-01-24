<?php

// phpcs:disable PSR1.Methods.CamelCapsMethodName, PSR1.Classes.ClassDeclaration, Squiz.Classes.ValidClassName

abstract class CCObject
{
    /**
     * Validate an object to check that all required fields have been supplied
     *
     * @params  array  $params  object property names to reference for validation before HTTP requests
     * @return  void
     */
    protected function validate(array $params)
    {
        try {
            foreach ($params as $field) {
                if (empty($this->$field)) {
                    throw new CTCTException(
                        "Constant Contact " . get_class($this) . " Error: '" . $field
                        . "' was required but not supplied"
                    );
                }
            }
        } catch (CTCTException $e) {
            $e->generateError();
        }
    }
}
