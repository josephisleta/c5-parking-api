<?php

namespace Concrete\Package\ParkingApi\Controller\Route;

use Concrete\Core\Controller\Controller;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;

/**
 * Class Parking
 * Holds all parking routes
 * @package Concrete\Package\ParkingApi\Controller\Route
 */
class Parking extends Controller
{
    public function getParkingInfo()
    {
        $parkingMapService = new ParkingMapService();
        $parkingMapService->getEntryOrExitQuantity();

        $data = $parkingMapService->getEntryOrExitQuantity();

        echo json_encode($data);
        exit();
    }

    public function getParkingSlots()
    {
        echo 'slots';
        exit();
    }

    public function enterParking()
    {
        echo 'enter';
        exit();
    }

    public function exitParking()
    {
        echo 'exit';
        exit();
    }
}