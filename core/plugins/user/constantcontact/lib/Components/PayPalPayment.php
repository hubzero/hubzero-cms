<?php

// phpcs:disable PSR1.Methods.CamelCapsMethodName, PSR1.Classes.ClassDeclaration, Squiz.Classes.ValidClassName

class PayPalPayment extends PaymentOption
{
    public $type;
    public $payPalEmail;

    /**
     * Constructor
     * @param string $paypalAddress - Paypal email address
     */
    public function __construct($paypalAddress)
    {
        $this->type = "PAYPAL";
        $this->payPalEmail = $paypalAddress;
    }
}
