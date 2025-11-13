<?php declare(strict_types=1);

namespace App\Test\Maps;

use App\Maps\MapMarker;
use App\Maps\Repository;
use App\Model\Entity\MapMarker as MapMarkerEntity;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RepositoryTest extends TestCase
{
    public array $fixtures = [
        'app.MapMarkers',
    ];

    #[Test]
    public function it_saves_a_map_marker(): void
    {
        $this->repository()->saveMarker(new MapMarker(1, 2));

        $mapMarker = TableRegistry::getTableLocator()
            ->get('MapMarkers')
            ->find()
            ->first();

        $this->assertNotNull($mapMarker, "Map marker was not created on the database");
    }
    
    #[Test]
    public function it_saves_a_map_marker_with_the_given_coordinates(): void
    {
        $latitude = 51.520128225389065;
        $longitude = -3.200732454703261;

        $this->repository()->saveMarker(new MapMarker($latitude, $longitude));
        $mapMarker = $this->getLastInsertedMarker();

        $expectedCoordinates = "POINT({$latitude} {$longitude})";
        $this->assertEquals($expectedCoordinates, $mapMarker['coordinates'], "Map marker was not created on the database with the right coordinates");
    }

    #[Test]
    public function it_retrieves_all_saved_map_markers(): void
    {
        $savedMarkers = [
            new MapMarker(51.520128, -3.200732),
            new MapMarker(13.740562, -15.205764),
            new MapMarker(83.374512, -120.102345),
            new MapMarker(-10.183456, -12.104532),
            new MapMarker(-20.100234, 12.124523),
            new MapMarker(-15.038495, 12.093845),
        ];
        
        foreach ($savedMarkers as $savedMarker) {
            $this->saveMapMarkerWithCoordinates($savedMarker->latitude(), $savedMarker->longitude());
        }

        $mapMarkerList = $this->repository()->getAllSavedMarkers();

        $this->assertNotEmpty($mapMarkerList->items(), "List of map markers is empty");
        $this->assertEquals($savedMarkers, $mapMarkerList->items(), "The list of returned map markers does not match all saved map markers");
    }
    
    #[Test]
    public function it_removes_all_saved_map_markers(): void
    {
        $savedMarkers = [
            new MapMarker(51.520128, -3.200732),
            new MapMarker(13.740562, -15.205764),
            new MapMarker(83.374512, -120.102345),
            new MapMarker(-10.183456, -12.104532),
            new MapMarker(-20.100234, 12.124523),
            new MapMarker(-15.038495, 12.093845),
        ];
        
        foreach ($savedMarkers as $savedMarker) {
            $this->saveMapMarkerWithCoordinates($savedMarker->latitude(), $savedMarker->longitude());
        }

        $this->repository()->removeAllSavedMarkers();

        $mapMarkers = TableRegistry::getTableLocator()->get('MapMarkers')->find()->all()->toArray();
        $this->assertEmpty($mapMarkers, "Did not delete the map markers");
    }
    
    #[Test]
    public function it_noops_if_there_are_no_map_markers_to_remove(): void
    {
        $this->repository()->removeAllSavedMarkers();

        $mapMarkers = TableRegistry::getTableLocator()->get('MapMarkers')->find()->all()->toArray();
        $this->assertEmpty($mapMarkers);
    }
    
    #[Test]
    public function it_returns_a_marker_with_the_given_latitude_and_longitude(): void
    {
        $latitude = 51.520128;
        $longitude = -3.200732;

        $aDifferentLatitude = 12.346722;
        $aDifferentLongitude = 42.456789;

        $anotherDifferentLatitude = -32.085632;
        $anotherDifferentLongitude = -67.345778;

        $this->saveMapMarkerWithCoordinates($latitude, $longitude);
        $this->saveMapMarkerWithCoordinates($aDifferentLatitude, $aDifferentLongitude);
        $this->saveMapMarkerWithCoordinates($anotherDifferentLatitude, $anotherDifferentLongitude);

        $marker = $this->repository()->getMapMarkerWithCoordinates($latitude, $longitude);

        $this->assertNotNull($marker, "Did not find the marker on the database");
        $this->assertEquals($latitude, $marker->latitude(), "Did not find the marker matching the latitude");
        $this->assertEquals($longitude, $marker->longitude(), "Did not find the marker matching the longitude");
    }
    
    #[Test]
    public function it_returns_null_when_no_markers_match_the_given_latitude_and_longitude(): void
    {
        $latitude = 51.520128;
        $longitude = -3.200732;

        $aDifferentLatitude = 12.346722;
        $aDifferentLongitude = 42.456789;

        $anotherDifferentLatitude = -32.085632;
        $anotherDifferentLongitude = -67.345778;

        $this->saveMapMarkerWithCoordinates($aDifferentLatitude, $aDifferentLongitude);
        $this->saveMapMarkerWithCoordinates($anotherDifferentLatitude, $anotherDifferentLongitude);

        $marker = $this->repository()->getMapMarkerWithCoordinates($latitude, $longitude);
        $this->assertNull($marker, "Incorrectly matched a marker on the database");
    }
    
    #[Test]
    public function it_does_not_return_a_marker_when_the_latitude_matches_but_the_longitude_does_not(): void
    {
        $latitude = 51.520128;
        $longitude = -3.200732;
        $aDifferentLongitude = 42.456789;

        $this->saveMapMarkerWithCoordinates($latitude, $aDifferentLongitude);

        $marker = $this->repository()->getMapMarkerWithCoordinates($latitude, $longitude);
        $this->assertNull($marker, "Incorrectly matched a partial match marker on the database");
    }
    
    #[Test]
    public function it_does_not_return_a_marker_when_the_longitude_matches_but_the_latitude_does_not(): void
    {
        $latitude = 51.520128;
        $longitude = -3.200732;
        $aDifferentLatitude = 42.456789;

        $this->saveMapMarkerWithCoordinates($aDifferentLatitude, $longitude);

        $marker = $this->repository()->getMapMarkerWithCoordinates($latitude, $longitude);
        $this->assertNull($marker, "Incorrectly matched a partial match marker on the database");
    }

    private function saveMapMarkerWithCoordinates(float $latitude, float $longitude): void
    {
        $markersTable = TableRegistry::getTableLocator()->get('MapMarkers');
        $emptyMapMarker = $markersTable->newEmptyEntity();
        $emptyMapMarker->coordinates = new FunctionExpression('POINT', [$latitude, $longitude]);
        $markersTable->save($emptyMapMarker);
    }

    private function getLastInsertedMarker(): MapMarkerEntity
    {
        return TableRegistry::getTableLocator()
            ->get('MapMarkers')
            ->find()
            ->select(['coordinates' => new FunctionExpression('ST_AsText', [new IdentifierExpression('coordinates')])])
            ->all()
            ->last();
    }

    private function repository(): Repository
    {
        return new Repository();
    }
}