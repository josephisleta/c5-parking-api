<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Dashboard;

use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMap;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;

/**
 * Class Settings
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Dashboard
 */
class Settings
{
    private $parkingMapService;
    private $parkingSlotsService;

    const SAMPLE_JSON_15_PARKING_SLOTS_3 = '[{"distancePoints":[1,5,10],"type":"MP"},{"distancePoints":[2,6,11],"type":"MP"},{"distancePoints":[3,7,12],"type":"MP"},{"distancePoints":[4,8,13],"type":"MP"},{"distancePoints":[5,9,14],"type":"MP"},{"distancePoints":[6,10,15],"type":"SP"},{"distancePoints":[7,11,1],"type":"SP"},{"distancePoints":[8,12,2],"type":"SP"},{"distancePoints":[9,13,3],"type":"SP"},{"distancePoints":[10,14,4],"type":"SP"},{"distancePoints":[11,15,5],"type":"LP"},{"distancePoints":[12,1,6],"type":"LP"},{"distancePoints":[13,2,7],"type":"LP"},{"distancePoints":[14,3,8],"type":"LP"},{"distancePoints":[15,4,9],"type":"LP"}]';

    /**
     * Settings constructor.
     * @param ParkingMapDao $parkingMapDao
     * @param ParkingSlotsDao $parkingSlotsDao
     */
    public function __construct($parkingMapDao, $parkingSlotsDao)
    {
        $this->parkingMapService = new ParkingMapService($parkingMapDao);
        $this->parkingSlotsService = new ParkingSlotsService($parkingSlotsDao);
    }

    /**
     * @return int
     */
    public function getNumberOfEntryExitPoints()
    {
        return $this->parkingMapService->getEntryOrExitQuantity() ?: ParkingMap::ENTRY_OR_EXIT_QUANTITY_DEFAULT;
    }

    /**
     * @return array|false|string
     */
    public function getParkingSlots()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots()->toArray();
        return json_encode($parkingSlots);
    }

    /**
     * @return string
     */
    public function getSampleParkingSlotsJson()
    {
        return self::SAMPLE_JSON_15_PARKING_SLOTS_3;
    }

    /**
     * @param $entryPointsInput
     * @param $parkingSlotsInput
     * @return array
     */
    public function validateForm($entryPointsInput, $parkingSlotsInput)
    {
        $parkingSlotsInput = json_decode($parkingSlotsInput, true);

        $errors = [];

        if ($this->parkingSlotsService->getParkingSlots()->hasUnavailable()) {
            $errors[] = 'You cannot change the Parking API Settings if there is a vehicle parked in at least one of the parking slots.';
        }

        if ($this->parkingMapService->isValidEntryOrExitQuantityInput($entryPointsInput)) {
            $errors[] = 'Number of Entry/Exit points: Please enter a valid Number of Entry/Exit points. Should be an integer (minimum 3).';
        }

        if (!$parkingSlotsInput || !is_array($parkingSlotsInput)) {
            $errors[] = 'Parking Slots (JSON): Please enter a properly formatted json for parking slots.';
        }

        if ($parkingSlotsInput) {
            foreach ($parkingSlotsInput as $inputParkingSlot) {
                $parkingSlot = new ParkingSlot($inputParkingSlot);
                if (!$parkingSlot->hasValidType()) {
                    $errors[] = 'Parking Slots (JSON): Please make sure that the "type" values are valid.';
                    break;
                }

                if (count($parkingSlot->getDistancePoints()) != $entryPointsInput) {
                    $errors[] = 'Parking Slots (JSON): Please make sure that the quantity of "distancePoints" are matched with the Number of Entry/Exit points.';
                    break;
                }

                if (!is_array($parkingSlot->getDistancePoints())) {
                    $errors[] = 'Parking Slots (JSON): Please make sure that the "distancePoints" are arrays.';
                    break;
                }

                foreach ($parkingSlot->getDistancePoints() as $distancePoint) {
                    if (!is_int($distancePoint)) {
                        $errors[] = 'Parking Slots (JSON): Please make sure that the values in "distancePoints" are integers.';
                        break 2;
                    }
                }
            }
        }

        if (!$errors) {
            $this->parkingMapService->saveEntryOrExitQuantity($entryPointsInput);
            $this->parkingSlotsService->save($parkingSlotsInput);
        }

        return $errors;
    }
}