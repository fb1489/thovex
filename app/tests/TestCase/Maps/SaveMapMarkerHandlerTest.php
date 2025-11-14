<?php declare(strict_types=1);

namespace App\Test\Maps;

use App\Maps\MapMarker;
use App\Maps\SaveMapMarker;
use App\Maps\SaveMapMarkerHandler;
use App\Maps\Repository;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\FunctionExpression;
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

    #[Test]
    public function it_does_not_create_a_duplicate_entry_on_the_database_if_a_marker_already_existed_on_the_same_coordinates(): void
    {
        $latitude = 51.520128225389065;
        $longitude = -3.200732454703261;
        
        $this->saveMapMarkerWithCoordinates($latitude, $longitude);

        $this->createHandler()->handle(new SaveMapMarker(
            new MapMarker($latitude, $longitude)
        ));

        $numberOfSavedMapMarkers = TableRegistry::getTableLocator()
            ->get('MapMarkers')
            ->find()
            ->all()
            ->count();

        $this->assertEquals(1, $numberOfSavedMapMarkers, "A duplicate map marker was created on the database");
    }

    #[Test]
    public function it_creates_a_MapMarker_with_a_title(): void
    {
        $latitude = 51.520128225389065;
        $longitude = -3.200732454703261;
        $title = 'This is a sample title';

        $this->createHandler()->handle(new SaveMapMarker(
            new MapMarker($latitude, $longitude, $title)
        ));

        $mapMarker = TableRegistry::getTableLocator()
            ->get('MapMarkers')
            ->find()
            ->first();

        $this->assertEquals($title, $mapMarker->title, "Map marker was not created on the database with the expected title");
    }

    private function saveMapMarkerWithCoordinates(float $latitude, float $longitude): void
    {
        $markersTable = TableRegistry::getTableLocator()->get('MapMarkers');
        $emptyMapMarker = $markersTable->newEmptyEntity();
        $emptyMapMarker->coordinates = new FunctionExpression('POINT', [$latitude, $longitude]);
        $markersTable->save($emptyMapMarker);
    }

    private function createHandler(): SaveMapMarkerHandler
    {
        return new SaveMapMarkerHandler(new Repository());
    }
}