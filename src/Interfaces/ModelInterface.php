<?php

namespace ⌬\Database\Interfaces;

interface ModelInterface
{
    public static function factory();

    public function save();

    public function isDirty(): bool;

    public function destroy();

    public function destroyThoroughly();

    public function getListOfProperties();

    public function __toPublicArray(): array;

    public function __fromPublicArray(array $publicArray): self;
}
