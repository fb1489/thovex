<?php declare(strict_types=1);

namespace App\Test\Maps;

use App\Maps\MapMarker;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class MapMarkerTest extends TestCase
{
    #[Test]
    #[DataProvider('valid_coordinates')]
    public function it_creates_a_MapMarker_from_valid_values(float $latitude, float $longitude): void
    {
        $this->assertInstanceOf(MapMarker::class, new MapMarker($latitude, $longitude));
    }

    public static function valid_coordinates(): array
    {
        return [
            "Latitude and longitude without decimals" => [1, 1],
            "Latitude and longitude with a single decimal" => [1.1, 1.1],
            "Latitude and longitude with 2 decimals" => [1.11, 1.11],
            "Latitude and longitude with 16 decimals" => [1.1972608850516138, 1.1972608850516138],
            "Latitude and longitude with negative values without decimals" => [-1, -1],
            "Latitude and longitude with negative values and 16 decimals" => [-1.1972608850516138, -1.1972608850516138],
            "Latitude and longitude at max values without decimals" => [90, 180],
            "Latitude and longitude at min values without decimals" => [-90, -180],
            "Latitude and longitude at max values with decimals" => [90.999999, 180.999999],
            "Latitude and longitude at min values with decimals" => [-90.999999, -180.999999],
            "Latitude at max value and longitude at min value" => [90, -180],
            "Latitude at min value and longitude at max value" => [-90, 180],
        ];
    }
    
    #[Test]
    #[DataProvider('invalid_coordinates')]
    public function it_throws_when_creating_a_MapMarker_from_invalid_values(float $latitude, float $longitude): void
    {
        $this->expectException(\RuntimeException::class);
        $this->assertInstanceOf(MapMarker::class, new MapMarker($latitude, $longitude));
    }
    
    public static function invalid_coordinates(): array
    {
        return [
            "Latitude and longitude above max without decimals" => [91, 181],
            "Latitude and longitude above max with decimals" => [91.01902, 181.19202],
            "Latitude and longitude below min without decimals" => [-91, -181],
            "Latitude and longitude below min with decimals" => [-91.01902, -181.19202],
            "Latitude and longitude at php max float value" => [PHP_FLOAT_MAX , PHP_FLOAT_MAX],
            "Latitude and longitude at negative php max float value" => [-PHP_FLOAT_MAX , -PHP_FLOAT_MAX],
        ];
    }
}