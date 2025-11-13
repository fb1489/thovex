<?php declare(strict_types=1);

namespace App\Maps;

class SaveMapMarkerHandler {
    public function __construct(
        private Repository $repository
    ) {}

    public function handle(SaveMapMarker $command): void
    {
        if ($this->repository->getMapMarkerWithCoordinates(
            $command->mapMarker()->latitude(),
            $command->mapMarker()->longitude()
        ) !== null) {
            return;
        }

        $this->repository->saveMarker($command->mapMarker());
    }
}