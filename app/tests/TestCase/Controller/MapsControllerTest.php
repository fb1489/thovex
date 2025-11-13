<?php declare(strict_types=1);

namespace App\Test\Controller;

use App\Maps\SaveMapMarker;
use App\Maps\SaveMapMarkerHandler;
use App\Model\Entity\MapMarker as MapMarkerEntity;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

/**
 * @coversDefaultClass \App\Controller\MapsController
 */
class MapsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public array $fixtures = [
        'app.MapMarkers',
    ];
    
    /**
     * @covers ::save_marker
     */
    #[Test]
    public function it_saves_a_marker(): void
    {
        $this->enableCsrfToken();
        
        $latitude = 51.520128;
        $longitude = -3.200732;

        $mock = Mockery::mock(SaveMapMarkerHandler::class);

        $mock->shouldReceive('handle')
            ->with(Mockery::on(function ($saveMapMarker) use ($latitude, $longitude) {
                if (!$saveMapMarker instanceof SaveMapMarker) {
                    return false;
                }

                return $saveMapMarker->mapMarker()->latitude() === $latitude
                    && $saveMapMarker->mapMarker()->longitude() === $longitude;
            }))
            ->once();

        $this->mockService(SaveMapMarkerHandler::class, fn() => $mock);

        $this->post('/maps/save_marker', [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $this->assertResponseCode(200);
    }
    
    /**
     * @covers ::save_marker
     */
    #[Test]
    public function it_saves_a_marker_in_the_database_with_the_given_coordinates(): void
    {
        $this->enableCsrfToken();
        
        $latitude = 51.520128;
        $longitude = -3.200732;

        $this->post('/maps/save_marker', [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $this->assertResponseCode(200);
        
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
}