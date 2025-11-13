<?php declare(strict_types=1);

namespace App\Controller;

use App\Maps\MapMarker;
use App\Maps\Repository;
use App\Maps\SaveMapMarker;
use App\Maps\SaveMapMarkerHandler;
use Cake\Http\Response;

class MapsController extends AppController
{
    public function index(Repository $repository): void
    {
        $this->set('mapMarkerList', $repository->getAllSavedMarkers());
    }

    public function saveMarker(SaveMapMarkerHandler $saveMapMarkerHandler): Response
    {
        $this->request->allowMethod('post');

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