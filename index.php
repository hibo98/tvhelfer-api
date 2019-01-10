<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

require_once 'classes/class.country.php';
require_once 'classes/class.location.php';
require_once 'classes/class.state.php';
require_once 'classes/class.transmitter.php';
require_once 'classes/class.transmitter_dab.php';

$app = new \Slim\App;
$app->get("/countries", function (Request $request, Response $response, array $args) {
    $result = array();
    $countries = Country::getAll();

    foreach ($countries as $country) {
        array_push($result, $country->id);
    }

    return $response->withJson($result);
});
$app->get('/countries/{id}', function (Request $request, Response $response, array $args) {
    $country = Country::getById($args['id']);

    if ($country != null) {
        $result = array();

        $result['id'] = $country->id;
        $result['country'] = $country->country;
        $result['name'] = $country->name;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Country not found'), 404);
    }
});
$app->get('/countries/{id}/states', function (Request $request, Response $response, array $args) {
    $result = array();
    $countryStates = State::getByCountry($args['id']);

    foreach ($countryStates as $state) {
        array_push($result, $state->id);
    }

    return $response->withJson($result);
});
$app->get('/states/{id}', function (Request $request, Response $response, array $args) {
    $state = State::getById($args['id']);

    if ($state != null) {
        $result = array();

        $result['id'] = $state->id;
        $result['country'] = $state->country->id;
        $result['state'] = $state->state;
        $result['name'] = $state->name;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'State not found'), 404);
    }
});
$app->get('/states/{id}/locations', function (Request $request, Response $response, array $args) {
    $result = array();
    $stateLocations = Location::getLocationsByStateId($args['id']);

    foreach ($stateLocations as $location) {
        array_push($result, $location->id);
    }

    return $response->withJson($result);
});
$app->get('/locations', function (Request $request, Response $response, array $args) {
    $result = array();
    $locations = Location::getAll();

    foreach ($locations as $location) {
        array_push($result, $location->id);
    }

    return $response->withJson($result);
});
$app->get('/locations/{id}', function (Request $request, Response $response, array $args) {
    $location = Location::getLocation($args['id']);

    if ($location != null) {
        $result = array();

        $result['id'] = $location->id;
        $result['country'] = $location->state->country->id;
        $result['state'] = $location->state->id;
        $result['name'] = $location->name;
        $result['operator'] = $location->operator;
        $result['status'] = $location->status;
        
        $loc = array();
        $loc['long'] = $location->longitude;
        $loc['lat'] = $location->latitude;
        $loc['aboveSeaLevel'] = $location->aboveSeaLevel;
        $result['location'] = $loc;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Location not found'), 404);
    }
});
$app->get('/locations/{id}/dvbt/transmitters', function (Request $request, Response $response, array $args) {
    $location = Location::getLocation($args['id']);

    if ($location != null) {
        $result = array();
        $transmitters = Transmitter::getTransmittersByLocation($args['id']);

        foreach ($transmitters as $transmitter) {
            array_push($result, $transmitter->id);
        }

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Location not found'), 404);
    }
});
$app->get('/locations/{id}/dvbt2/transmitters', function (Request $request, Response $response, array $args) {
    $location = Location::getLocation($args['id']);

    if ($location != null) {
        $result = array();
        $transmitters = Transmitter::getTransmittersByLocation($args['id'], true);

        foreach ($transmitters as $transmitter) {
            array_push($result, $transmitter->id);
        }

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Location not found'), 404);
    }
});
$app->get('/locations/{id}/dab/transmitters', function (Request $request, Response $response, array $args) {
    $location = Location::getLocation($args['id']);

    if ($location != null) {
        $result = array();
        $transmitters = DABTransmitter::getTransmittersByLocation($args['id']);

        foreach ($transmitters as $transmitter) {
            array_push($result, $transmitter->id);
        }

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Location not found'), 404);
    }
});
$app->get('/dvbt/bouquets', function (Request $request, Response $response, array $args) {
    $result = array();
    $bouquets = Programmpaket::getPakets();
    
    foreach ($bouquets as $bouquet) {
        array_push($result, $bouquet->id);
    }

    return $response->withJson($result);
});
$app->get('/dvbt/bouquets/{id}', function (Request $request, Response $response, array $args) {
    $bouquet = Programmpaket::getPaket($args['id']);

    if ($bouquet != null) {
        $result = array();

        $result['id'] = $bouquet->id;
        $result['name'] = $bouquet->name;
        $result['status'] = $bouquet->status;
        $result['fec'] = $bouquet->FEC;
        $result['fft'] = $bouquet->FFT;
        $result['guardintervall'] = $bouquet->guardintervall;
        $result['modulation'] = $bouquet->modulation;
        $result['bandwidth'] = $bouquet->bandwidth;
        $result['parent'] = $bouquet->parent->id;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->get('/dvbt/bouquets/{id}/programs', function (Request $request, Response $response, array $args) {
    $bouquet = Programmpaket::getPaket($args['id']);

    if ($bouquet != null) {
        $result = array();
        
        foreach ($bouquet->programms as $prog) {
            array_push($result, $prog->id);
        }

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->get('/dvbt/bouquets/{b}/programs/{id}', function (Request $request, Response $response, array $args) {
    $bouquet = Programmpaket::getPaket($args['b']);

    if ($bouquet != null) {
        $result = array();
        $prog = $bouquet->programms[$args['id']];
        
        $result['id'] = $prog->id;
        $result['name'] = $prog->name;
        $result['langs'] = $prog->langs;
        $result['bandwidth'] = $prog->bandwidth;
        $result['compression'] = $prog->compression;
        $result['crypt'] = (bool) $prog->crypt;
        $result['region'] = $prog->region;
        $result['type'] = $prog->type;
        $result['px2'] = array();
        $result['px2']['name'] = $prog->px2;
        $result['px2']['region'] = $prog->px2Region;
        $result['px2']['time'] = $prog->px2Time;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->get('/dvbt2/bouquets', function (Request $request, Response $response, array $args) {
    $result = array();
    $bouquets = Programmpaket::getPakets(true);
    
    foreach ($bouquets as $bouquet) {
        array_push($result, $bouquet->id);
    }

    return $response->withJson($result);
});
$app->get('/dvbt2/bouquets/{id}', function (Request $request, Response $response, array $args) {
    $bouquet = Programmpaket::getPaket($args['id'], true);

    if ($bouquet != null) {
        $result = array();

        $result['id'] = $bouquet->id;
        $result['name'] = $bouquet->name;
        $result['status'] = $bouquet->status;
        $result['fec'] = $bouquet->FEC;
        $result['fft'] = $bouquet->FFT;
        $result['guardintervall'] = $bouquet->guardintervall;
        $result['modulation'] = $bouquet->modulation;
        $result['bandwidth'] = $bouquet->bandwidth;
        $result['parent'] = $bouquet->parent->id;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->get('/dvbt2/bouquets/{id}/programs', function (Request $request, Response $response, array $args) {
    $bouquet = Programmpaket::getPaket($args['id'], true);

    if ($bouquet != null) {
        $result = array();
        
        foreach ($bouquet->programms as $prog) {
            array_push($result, $prog->id);
        }

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->get('/dvbt2/bouquets/{b}/programs/{id}', function (Request $request, Response $response, array $args) {
    $bouquet = Programmpaket::getPaket($args['b'], true);

    if ($bouquet != null) {
        $result = array();
        $prog = $bouquet->programms[$args['id']];
        
        $result['id'] = $prog->id;
        $result['name'] = $prog->name;
        $result['region'] = $prog->region;
        $result['langs'] = $prog->langs;
        $result['bandwidth'] = $prog->bandwidth;
        $result['compression'] = $prog->compression;
        $result['crypt'] = (bool) $prog->crypt;
        $result['type'] = $prog->type;
        $result['px2'] = array();
        $result['px2']['name'] = $prog->px2;
        $result['px2']['region'] = $prog->px2Region;
        $result['px2']['time'] = $prog->px2Time;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->get('/dab/ensembles', function (Request $request, Response $response, array $args) {
    $result = array();
    $ensembles = DABProgrammpaket::getPakets();
    
    foreach ($ensembles as $ensemble) {
        array_push($result, $ensemble->id);
    }

    return $response->withJson($result);
});
$app->get('/dab/ensembles/{id}', function (Request $request, Response $response, array $args) {
    $ensemble = DABProgrammpaket::getPaket($args['id']);

    if ($ensemble != null) {
        $result = array();

        $result['id'] = $ensemble->id;
        $result['name'] = $ensemble->name;
        $result['status'] = $ensemble->status;
        $result['parent'] = $ensemble->parent->id;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Ensemble not found'), 404);
    }
});
$app->get('/dab/ensembles/{id}/programs', function (Request $request, Response $response, array $args) {
    $ensemble = DABProgrammpaket::getPaket($args['id']);

    if ($ensemble != null) {
        $result = array();
        
        foreach ($ensemble->programms as $prog) {
            array_push($result, $prog->id);
        }

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->get('/dab/ensembles/{b}/programs/{id}', function (Request $request, Response $response, array $args) {
    $ensemble = DABProgrammpaket::getPaket($args['b']);

    if ($ensemble != null) {
        $result = array();
        $prog = $ensemble->programms[$args['id']];
        
        $result['id'] = $prog->id;
        $result['name'] = $prog->name;
        $result['region'] = $prog->region;
        $result['langs'] = $prog->langs;
        $result['bandwidth'] = $prog->bandwidth;
        $result['codec'] = $prog->codec;
        $result['type'] = $prog->anstalt;
        $result['status'] = $prog->status;

        return $response->withJson($result);
    } else {
        return $response->withJson(array('error' => 404, 'msg' => 'Bouquet not found'), 404);
    }
});
$app->run();
