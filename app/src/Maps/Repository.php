<?php declare(strict_types=1);

namespace App\Maps;

use App\Model\Entity\MapMarker as MapMarkerEntity;
use App\Model\Table\MapMarkersTable;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\ORM\TableRegistry;

class Repository
{
    private MapMarkersTable $mapMarkers;

    public function __construct()
    {
        $this->mapMarkers = TableRegistry::getTableLocator()->get('MapMarkers');
    }

    public function saveMarker(MapMarker $mapMarker): void
    {
        $mapMarkerEntity = $this->mapMarkers->newEmptyEntity();
        $mapMarkerEntity->coordinates = new FunctionExpression('POINT', [$mapMarker->latitude(), $mapMarker->longitude()]);

        $saved = $this->mapMarkers->save($mapMarkerEntity);

        if (!$saved) {
            throw new \RuntimeException('MapMarkers Model was unable to save map marker.');
        }
    }

    public function getAllSavedMarkers(): MapMarkerList
    {
        $data = $this->mapMarkers
            ->find()
            ->select([
                'latitude' => new FunctionExpression('ST_X', [new IdentifierExpression('coordinates')]),
                'longitude' => new FunctionExpression('ST_Y', [new IdentifierExpression('coordinates')]),
            ])
            ->all()
            ->toArray();

        $mapMarkers = array_map(
            fn (MapMarkerEntity $dataset) => new MapMarker(
                $dataset['latitude'],
                $dataset['longitude']
            ),
            $data
        );

        return new MapMarkerList($mapMarkers);
    }
}