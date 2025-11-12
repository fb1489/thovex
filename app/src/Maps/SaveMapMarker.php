<?php declare(strict_types=1);

namespace App\Maps;

class SaveMapMarker {
    public function __construct(
        private MapMarker $mapMarker
    ) {}

    public function mapMarker(): MapMarker
    {
        return $this->mapMarker;
    }
}