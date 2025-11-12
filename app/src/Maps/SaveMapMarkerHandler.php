<?php declare(strict_types=1);

namespace App\Maps;

class SaveMapMarkerHandler {
    public function __construct(
        private Repository $repository
    ) {}

    public function handle(SaveMapMarker $command): void
    {
        $this->repository->saveMarker($command->mapMarker());
    }
}