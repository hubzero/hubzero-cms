<?php

namespace T;

class Marker extends \Exception
{
}

interface Thing  {}

class A
{
    public function __construct(private int $a)
    {
    }

    public function noop(): void {
    }

    public function sameLine(): void {
        $this->noop();
    }

    public function closing(): void
    {
        $this->noop(); }

    public function badIndent(): void
    {
        $this->noop();
        }

    public function long(
        int $first,
        int $second,
    ): void
    {}

    public function run(): object
    {
        $f = function () {
        };
        $f();

        return new class {
        };
    }
}

class B extends A {
    public $x;
}

class C
    {
    public $y; }
