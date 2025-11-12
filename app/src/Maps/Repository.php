<?php declare(strict_types=1);

namespace App\Maps;

use App\Model\Table\MapMarkersTable;
use Cake\Database\Expression\FunctionExpression;
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
}