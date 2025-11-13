<?php declare(strict_types=1);

namespace App\Test\Maps;

use App\Maps\SaveMapMarkerRequestValidator;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SaveMapMarkerRequestValidatorTest extends TestCase
{
    #[Test]
    public function it_validates_if_given_a_valid_set_of_coordinates(): void
    {
        $request = new ServerRequest([
            'post' => [
                'latitude' => 12.134654,
                'longitude' => -32.753645,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertTrue($validatorResponse->isValid(), "Form validator returned response as non-valid for valid data");
    }
    
    #[Test]
    public function it_fails_to_validate_if_request_does_not_have_latitude_and_longitude(): void
    {
        $request = new ServerRequest([
            'post' => [
                // missing 'latitude' and 'longitude' keys
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertFalse($validatorResponse->isValid(), "Form validator returned response as valid for non-valid data");

        $expectedErrors = [
            SaveMapMarkerRequestValidator::LATITUDE_NOT_PRESENT_ERROR,
            SaveMapMarkerRequestValidator::LONGITUDE_NOT_PRESENT_ERROR,
        ];
        $this->assertEquals($expectedErrors, $validatorResponse->errors(), "Form validator errors do not match expected errors");
    }
    
    #[Test]
    public function it_fails_to_validate_if_request_does_not_have_latitude(): void
    {
        $request = new ServerRequest([
            'post' => [
                // missing 'latitude' key
                'longitude' => -32.753645,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertFalse($validatorResponse->isValid(), "Form validator returned response as valid for non-valid data");

        $expectedErrors = [
            SaveMapMarkerRequestValidator::LATITUDE_NOT_PRESENT_ERROR,
        ];
        $this->assertEquals($expectedErrors, $validatorResponse->errors(), "Form validator errors do not match expected errors");
    }
    
    #[Test]
    public function it_fails_to_validate_if_request_does_not_have_longitude(): void
    {
        $request = new ServerRequest([
            'post' => [
                // missing 'longitude' key
                'latitude' => 12.134654,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertFalse($validatorResponse->isValid(), "Form validator returned response as valid for non-valid data");

        $expectedErrors = [
            SaveMapMarkerRequestValidator::LONGITUDE_NOT_PRESENT_ERROR,
        ];
        $this->assertEquals($expectedErrors, $validatorResponse->errors(), "Form validator errors do not match expected errors");
    }
    
    #[Test]
    #[DataProvider('invalid_coordinate_values')]
    public function it_fails_to_validate_if_latitude_is_not_a_valid_value(mixed $invalidCoordinateValue): void
    {
        $request = new ServerRequest([
            'post' => [
                'latitude' => $invalidCoordinateValue,
                'longitude' => -32.753645,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertFalse($validatorResponse->isValid(), "Form validator returned response as valid for non-valid data");

        $expectedErrors = [
            SaveMapMarkerRequestValidator::LATITUDE_IS_INVALID_ERROR,
        ];
        $this->assertEquals($expectedErrors, $validatorResponse->errors(), "Form validator errors do not match expected errors");
    }

    #[Test]
    #[DataProvider('invalid_coordinate_values')]
    public function it_fails_to_validate_if_longitude_is_not_a_valid_value(mixed $invalidCoordinateValue): void
    {
        $request = new ServerRequest([
            'post' => [
                'latitude' => 12.134654,
                'longitude' => $invalidCoordinateValue,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertFalse($validatorResponse->isValid(), "Form validator returned response as valid for non-valid data");

        $expectedErrors = [
            SaveMapMarkerRequestValidator::LONGITUDE_IS_INVALID_ERROR,
        ];
        $this->assertEquals($expectedErrors, $validatorResponse->errors(), "Form validator errors do not match expected errors");
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
    
    #[Test]
    #[DataProvider('invalid_latitude_values')]
    public function it_fails_validation_if_latitude_is_a_value_that_exceeds_the_latitude_value_bound(float $invalidLatitudeValue): void
    {
        $request = new ServerRequest([
            'post' => [
                'latitude' => $invalidLatitudeValue,
                'longitude' => -32.753645,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertFalse($validatorResponse->isValid(), "Form validator returned response as valid for non-valid data");

        $expectedErrors = [
            SaveMapMarkerRequestValidator::LATITUDE_IS_INVALID_ERROR,
        ];
        $this->assertEquals($expectedErrors, $validatorResponse->errors(), "Form validator errors do not match expected errors");
    }

    public static function invalid_latitude_values(): array
    {
        return [
            "Value above the maximum bound" => [100.123456],
            "Value that is exactly the maximum bound" => [91],
            "Value below the minimum bound" => [-100.123456],
            "Value that is exactly the minimum bound" => [-91],
        ];
    }
    
    #[Test]
    #[DataProvider('invalid_longitude_values')]
    public function it_fails_validation_if_longitude_is_a_value_that_exceeds_the_longitude_value_bound(float $invalidLongitudeValue): void
    {
        $request = new ServerRequest([
            'post' => [
                'latitude' => 12.134654,
                'longitude' => $invalidLongitudeValue,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertFalse($validatorResponse->isValid(), "Form validator returned response as valid for non-valid data");

        $expectedErrors = [
            SaveMapMarkerRequestValidator::LONGITUDE_IS_INVALID_ERROR,
        ];
        $this->assertEquals($expectedErrors, $validatorResponse->errors(), "Form validator errors do not match expected errors");
    }

    public static function invalid_longitude_values(): array
    {
        return [
            "Value above the maximum bound" => [200.123456],
            "Value that is exactly the maximum bound" => [181],
            "Value below the minimum bound" => [-200.123456],
            "Value that is exactly the minimum bound" => [-181],
        ];
    }
    
    #[Test]
    #[DataProvider('valid_latitude_values')]
    public function it_validates_if_latitude_is_a_value_within_the_latitude_value_bounds(float $validLatitudeValue): void
    {
        $request = new ServerRequest([
            'post' => [
                'latitude' => $validLatitudeValue,
                'longitude' => -32.753645,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertTrue($validatorResponse->isValid(), "Form validator returned response as non-valid for valid data");
    }
    
    public static function valid_latitude_values(): array
    {
        return [
            "0.0 coordinate" => [0],
            "Positive coordinate with 6 decimal places" => [12.342561],
            "Positive coordinate with 10 decimal places" => [12.3425615673],
            "Positive coordinate at the edge of the upper bound" => [90.999999],
            "Negative coordinate with 6 decimal places" => [-12.342561],
            "Negative coordinate with 10 decimal places" => [-12.3425615673],
            "Negative coordinate at the edge of the upper bound" => [-90.999999],
        ];
    }
    
    #[Test]
    #[DataProvider('valid_longitude_values')]
    public function it_validates_if_longitude_is_a_value_within_the_longitude_value_bounds(float $validLongitudeValue): void
    {
        $request = new ServerRequest([
            'post' => [
                'latitude' => 12.134654,
                'longitude' => $validLongitudeValue,
            ],
        ]);

        $validator = new SaveMapMarkerRequestValidator();
        $validatorResponse = $validator->validate($request);

        $this->assertTrue($validatorResponse->isValid(), "Form validator returned response as non-valid for valid data");
    }
    
    public static function valid_longitude_values(): array
    {
        return [
            "0.0 coordinate" => [0],
            "Positive coordinate with 6 decimal places" => [42.342561],
            "Positive coordinate with 10 decimal places" => [112.3425615673],
            "Positive coordinate at the edge of the upper bound" => [180.999999],
            "Negative coordinate with 6 decimal places" => [-42.342561],
            "Negative coordinate with 10 decimal places" => [-112.3425615673],
            "Negative coordinate at the edge of the upper bound" => [-180.999999],
        ];
    }
}