<?php declare(strict_types=1);

namespace App\Test\Controller;

use App\Maps\MapMarker;
use App\Maps\SaveMapMarker;
use App\Maps\SaveMapMarkerHandler;
use App\Model\Entity\MapMarker as MapMarkerEntity;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
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
    
    /**
     * @covers ::save_marker
     */
    #[Test]
    public function it_returns_failed_response_when_trying_to_save_a_marker_and_not_providing_latitude_and_longitude(): void
    {
        $this->enableCsrfToken();
        
        $this->post('/maps/save_marker', [
            // missing 'latitude' and 'longitude' keys
        ]);

        $this->assertResponseCode(400);
        $this->assertNull($this->getLastInsertedMarker());
    }
    
    /**
     * @covers ::save_marker
     */
    #[Test]
    public function it_returns_failed_response_when_trying_to_save_a_marker_and_not_providing_latitude(): void
    {
        $this->enableCsrfToken();
        
        $this->post('/maps/save_marker', [
                // missing 'latitude' key
                'longitude' => -32.753645,
        ]);

        $this->assertResponseCode(400);
        $this->assertNull($this->getLastInsertedMarker());
    }
    
    /**
     * @covers ::save_marker
     */
    #[Test]
    public function it_returns_failed_response_when_trying_to_save_a_marker_and_not_providing_longitude(): void
    {
        $this->enableCsrfToken();
        
        $this->post('/maps/save_marker', [
                // missing 'longitude' key
                'latitude' => 12.134654,
        ]);

        $this->assertResponseCode(400);
        $this->assertNull($this->getLastInsertedMarker());
    }
    
    /**
     * @covers ::save_marker
     */
    #[Test]
    #[DataProvider('invalid_coordinate_values')]
    public function it_returns_failed_response_when_trying_to_save_a_marker_and_providing_an_invalid_latitude(mixed $invalidCoordinateValue): void
    {
        $this->enableCsrfToken();
        
        $this->post('/maps/save_marker', [
                'latitude' => $invalidCoordinateValue,
                'longitude' => -32.753645,
        ]);

        $this->assertResponseCode(400);
        $this->assertNull($this->getLastInsertedMarker());
    }
    
    /**
     * @covers ::save_marker
     */
    #[Test]
    #[DataProvider('invalid_coordinate_values')]
    public function it_returns_failed_response_when_trying_to_save_a_marker_and_providing_an_invalid_longitude(mixed $invalidCoordinateValue): void
    {
        $this->enableCsrfToken();
        
        $this->post('/maps/save_marker', [
                'latitude' => 12.134654,
                'longitude' => $invalidCoordinateValue,
        ]);

        $this->assertResponseCode(400);
        $this->assertNull($this->getLastInsertedMarker());
    }

    public static function invalid_coordinate_values(): array
    {
        return [
            "NULL value" => [null],
            "An empty value" => [''],
            "A non-numeric string" => ['A random string'],
            "A json object value" => ['{"value": 1.23}'],
            "An malformed numeric value" => ['12.12.12'],
        ];
    }
    
    /**
     * @covers ::removeAllMarkers
     */
    #[Test]
    public function it_removes_all_saved_map_markers(): void
    {
        $this->enableCsrfToken();

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

        $this->delete('/maps/remove_all_markers');
        $this->assertResponseCode(200);

        $mapMarkers = TableRegistry::getTableLocator()->get('MapMarkers')->find()->all()->toArray();
        $this->assertEmpty($mapMarkers, "Did not delete the map markers");
    }

    private function saveMapMarkerWithCoordinates(float $latitude, float $longitude): void
    {
        $markersTable = TableRegistry::getTableLocator()->get('MapMarkers');
        $mapMarker = $markersTable->newEmptyEntity();
        $mapMarker->coordinates = new FunctionExpression('POINT', [$latitude, $longitude]);
        $markersTable->save($mapMarker);
    }

    private function getLastInsertedMarker(): ?MapMarkerEntity
    {
        return TableRegistry::getTableLocator()
            ->get('MapMarkers')
            ->find()
            ->select(['coordinates' => new FunctionExpression('ST_AsText', [new IdentifierExpression('coordinates')])])
            ->all()
            ->last();
    }
}