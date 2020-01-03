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
}
