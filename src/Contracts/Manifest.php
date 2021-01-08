<?php

namespace Kynda\Contracts;

interface Manifest
{
    public function get($key): Asset;
}
