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