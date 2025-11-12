<?php declare(strict_types=1);

namespace App\Maps;

class MapMarker {
    public function __construct(
        private float $latitude,
        private float $longitude,
    ) {
        /**
         * Safeguards for the values, as per the documention mentions on this link:
         * https://support.google.com/maps/answer/18539?hl=en-GB&co=GENIE.Platform%3DDesktop
         */
        if ($latitude >= 91 || $latitude <= -91) {
            throw new \RuntimeException("Latitude must be a value between -91 and 91");
        }

        if ($longitude >= 181 || $longitude <= -181) {
            throw new \RuntimeException("Longitude must be a value between -181 and 181");
        }
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }
}