<?php

// @codeCoverageIgnoreStart
if (!class_exists('Config', false)) {
    eval(<<<'CONFIG'
class Config
{
    public $access = '1';
    public $api_server = '1';
    public $application_env = 'production';
    public $captcha = '0';
}
CONFIG);
}
// @codeCoverageIgnoreEnd
