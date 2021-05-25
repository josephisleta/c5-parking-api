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
    /**
     * API controller for /api/parking
     * Response (json):
     *  entry/exit quantity
     *  parking slots
     */
    public function getParkingInfo()
    {
        $request = new GetInfoRequest($this->request('entryPoint'));

        $parkingMapDao = new ParkingMapDaoImpl();
        $parkingSlotsDao = new ParkingSlotsDaoImpl();

        $getInfo = new GetInfoAction($parkingMapDao, $parkingSlotsDao);

        $response = $getInfo->process($request);

        echo json_encode($response->get());
        exit();
    }

    /**
     * API controller for /api/parking/enter
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
        $request = new ParkRequest(
            $this->request('entryPoint'),
            $this->request('plateNumber'),
            $this->request('type'),
            $this->request('color'));

        $parkingMapDao = new ParkingMapDaoImpl();
        $parkingSlotsDao = new ParkingSlotsDaoImpl();
        $vehiclesDao = new VehiclesDaoImpl();
        $parkingSlipsDao = new ParkingSlipsDaoImpl();

        $park = new ParkAction($parkingMapDao, $parkingSlotsDao, $vehiclesDao, $parkingSlipsDao);

        $response = $park->process($request);

        echo json_encode($response->get());
        exit();
    }

    /**
     * API controller for /api/parking/exit
     * Required params:
     *  parkingSlotId
     * Response (json):
     *  parking fee
     */
    public function exitParking()
    {
        $request = new UnParkRequest($this->request('parkingSlotId'));

        $parkingMapDao = new ParkingMapDaoImpl();
        $parkingSlotsDao = new ParkingSlotsDaoImpl();
        $vehiclesDao = new VehiclesDaoImpl();
        $parkingSlipsDao = new ParkingSlipsDaoImpl();

        $unPark = new UnParkAction($parkingMapDao, $parkingSlotsDao, $vehiclesDao, $parkingSlipsDao);

        $response = $unPark->process($request);

        echo json_encode($response->get());
        exit();
    }
}