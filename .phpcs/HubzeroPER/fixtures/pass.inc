<?php

namespace T;

class Marker extends \Exception {}

interface Thing {}

trait Shared {}

abstract class A
{
    public function __construct(private int $a) {}

    public function __clone() {}

    public function long(
        int $first,
        int $second,
    ): void {}

    abstract public function later(): void;

    public function comment(): void
    {
        // deliberately nothing
    }

    public function run(): object
    {
        $f = function () {};
        $f();

        return new class {};
    }
}
