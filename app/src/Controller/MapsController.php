<?php declare(strict_types=1);

namespace App\Controller;

use App\Maps\MapMarker;
use App\Maps\Repository;
use App\Maps\SaveMapMarker;
use App\Maps\SaveMapMarkerHandler;
use App\Maps\SaveMapMarkerRequestValidator;
use Cake\Http\Response;

class MapsController extends AppController
{
    public function index(Repository $repository): void
    {
        $this->set('mapMarkerList', $repository->getAllSavedMarkers());
    }

    public function saveMarker(SaveMapMarkerRequestValidator $saveMapMarkerRequestValidator, SaveMapMarkerHandler $saveMapMarkerHandler): Response
    {
        $this->request->allowMethod('post');

        $validationResponse = $saveMapMarkerRequestValidator->validate($this->request);

        if (!$validationResponse->isValid()) {
            return $this->response
                ->withStatus(400)
                ->withType('json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'payload' => $this->request->getQuery(),
                    'errors' => $validationResponse->errors(),
                ]));
        }

        $saveMapMarkerHandler->handle(new SaveMapMarker(
            new MapMarker(
                (float)$this->request->getData('latitude'),
                (float)$this->request->getData('longitude')
            ),
        ));
        
        return $this->response
            ->withStatus(200)
            ->withType('json')
            ->withStringBody(json_encode([
                'success' => true,
            ]));
    }
}