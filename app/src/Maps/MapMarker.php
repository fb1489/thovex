<?php declare(strict_types=1);

namespace App\Maps;

class MapMarker {
    public function __construct(
        private float $latitude,
        private float $longitude,
    ) {}

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }
}