<?php

// phpcs:disable PSR1.Methods.CamelCapsMethodName, PSR1.Classes.ClassDeclaration, Squiz.Classes.ValidClassName

class DoorPayment extends PaymentOption
{
    public $type;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = "DOOR";
    }
}
