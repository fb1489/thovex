<?php declare(strict_types=1);

namespace App\Maps;

class MapMarkerList implements \JsonSerializable
{
    /**
     * Representation of a list of map markers
     *
     * @param array<MapMarker> $items The list of map markers
     * @throws \RuntimeException if given array contains a non MapMarker item
     */
    public function __construct(private array $items) {
        $this->validateItemsAreInstancesOfMapMarker();
    }

    public function items(): array
    {
        return $this->items;
    }

    private function validateItemsAreInstancesOfMapMarker(): void
    {
        $nonMapMarkerItems = array_filter($this->items, fn ($item) => ! $item instanceof MapMarker);
        if (count($nonMapMarkerItems) > 0) {
            throw new \RuntimeException("Invalid item(s) passed in map marker list items: " . json_encode($nonMapMarkerItems));
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'items' => array_map(
                fn (MapMarker $item) => $item->jsonSerialize(),
                $this->items
            ),
        ];
    }
}
