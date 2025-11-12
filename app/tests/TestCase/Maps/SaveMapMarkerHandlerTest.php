<?php declare(strict_types=1);

namespace App\Test\Maps;

use App\Maps\MapMarker;
use App\Maps\SaveMapMarker;
use App\Maps\SaveMapMarkerHandler;
use App\Maps\Repository;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SaveMapMarkerHandlerTest extends TestCase
{
    public array $fixtures = [
        'app.MapMarkers',
    ];

    #[Test]
    public function it_creates_a_MapMarker_on_the_database(): void
    {
        $latitude = 51.520128225389065;
        $longitude = -3.200732454703261;

        $this->createHandler()->handle(new SaveMapMarker(
            new MapMarker($latitude, $longitude)
        ));

        $mapMarker = TableRegistry::getTableLocator()
            ->get('MapMarkers')
            ->find()
            ->first();

        $this->assertNotNull($mapMarker, "Map marker was not created on the database");
    }

    private function createHandler(): SaveMapMarkerHandler
    {
        return new SaveMapMarkerHandler(new Repository());
    }
}