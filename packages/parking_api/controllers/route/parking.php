<?php

namespace Concrete\Package\ParkingApi\Controller\Route;

use Concrete\Core\Controller\Controller;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap\ParkingMapDaoImpl;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlips\ParkingSlipsDaoImpl;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots\ParkingSlotsDaoImpl;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\Vehicles\VehiclesDaoImpl;
use Concrete\Package\ParkingApi\Src\Application\Parking\Actions\GetInfoAction;
use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\GetInfoRequest;
use Concrete\Package\ParkingApi\Src\Application\Parking\Actions\ParkAction;
use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\ParkRequest;
use Concrete\Package\ParkingApi\Src\Application\Parking\Actions\UnParkAction;
use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\UnParkRequest;

/**
 * Class Parking
 * Holds all parking routes
 * @package Concrete\Package\ParkingApi\Controller\Route
 */
class Parking extends Controller
{
    /** @var array $input */
    private $input;

    public function __construct()
    {
        parent::__construct();

        $input = file_get_contents('php://input');
        $this->input = json_decode($input, true);
    }

    /**
     * API controller for /api/parking
     * Optional param:
     *  entryPoint
     * Response (json):
     *  entry/exit quantity
     *  parking slots
     */
    public function getParkingInfo()
    {
        $request = new GetInfoRequest($_GET);

        $parkingMapDao = new ParkingMapDaoImpl();
        $parkingSlotsDao = new ParkingSlotsDaoImpl();

        $getInfo = new GetInfoAction($parkingMapDao, $parkingSlotsDao);

        $response = $getInfo->process($request);

        echo $response->toJson();
        exit();
    }

    /**
     * API controller for /api/parking/enter
     * Accepts
     *  Content-Type: application/json
     * Required params:
     *  plateNumber
     *  type
     *  entryPoint
     * Optional param:
     *  color
     * Response (json):
     *  parking slot id
     */
    public function enterParking()
    {
        $request = new ParkRequest($this->input);

        $parkingMapDao = new ParkingMapDaoImpl();
        $parkingSlotsDao = new ParkingSlotsDaoImpl();
        $vehiclesDao = new VehiclesDaoImpl();
        $parkingSlipsDao = new ParkingSlipsDaoImpl();

        $park = new ParkAction($parkingMapDao, $parkingSlotsDao, $vehiclesDao, $parkingSlipsDao);

        $response = $park->process($request);

        echo $response->toJson();
        exit();
    }

    /**
     * API controller for /api/parking/exit
     * Accepts
     *  Content-Type: application/json
     * Required params:
     *  parkingSlotId
     * Response (json):
     *  parking slip
     */
    public function exitParking()
    {
        $request = new UnParkRequest($this->input);

        $parkingMapDao = new ParkingMapDaoImpl();
        $parkingSlotsDao = new ParkingSlotsDaoImpl();
        $vehiclesDao = new VehiclesDaoImpl();
        $parkingSlipsDao = new ParkingSlipsDaoImpl();

        $unPark = new UnParkAction($parkingMapDao, $parkingSlotsDao, $vehiclesDao, $parkingSlipsDao);

        $response = $unPark->process($request);

        echo $response->toJson();
        exit();
    }
}