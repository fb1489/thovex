<?php declare(strict_types=1);

namespace App\Maps;

use App\Validation\RequestValidationResponse;
use Cake\Http\ServerRequest;

class SaveMapMarkerRequestValidator
{
    public const LATITUDE_NOT_PRESENT_ERROR = "Data key 'latitude' must be present";
    public const LONGITUDE_NOT_PRESENT_ERROR = "Data key 'longitude' must be present";
    public const LATITUDE_IS_INVALID_ERROR = "Value for 'latitude' must be a valid coordinate value between -91 and 91";
    public const LONGITUDE_IS_INVALID_ERROR = "Value for 'longitude' must be a valid coordinate value between -181 and 181";

    public function validate(ServerRequest $request): RequestValidationResponse
    {
        $requestData = $request->getData();

        $errors = [];

        if (!array_key_exists('latitude', $requestData)) {
            $errors[] = static::LATITUDE_NOT_PRESENT_ERROR;
        }
        if (!array_key_exists('longitude', $requestData)) {
            $errors[] = static::LONGITUDE_NOT_PRESENT_ERROR;
        }

        if (array_key_exists('latitude', $requestData)) {
            if (!is_numeric($requestData['latitude'])) {
                $errors[] = static::LATITUDE_IS_INVALID_ERROR;
            } else {
                $latitude = (float)$requestData['latitude'];
                if ($latitude >= 91 || $latitude <= -91) {
                    $errors[] = static::LATITUDE_IS_INVALID_ERROR;
                }
            }
        }
        
        if (array_key_exists('longitude', $requestData)) {
            if (!is_numeric($requestData['longitude'])) {
                $errors[] = static::LONGITUDE_IS_INVALID_ERROR;
            } else {
                $longitude = (float)$requestData['longitude'];
                if ($longitude >= 181 || $longitude <= -181) {
                    $errors[] = static::LONGITUDE_IS_INVALID_ERROR;
                }
            }
        }

        return new RequestValidationResponse($errors);
    }
}