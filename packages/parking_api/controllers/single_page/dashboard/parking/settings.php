<?php

namespace Concrete\Package\ParkingApi\Controller\SinglePage\Dashboard\Parking;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap\ParkingMapDaoImpl;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots\ParkingSlotsDaoImpl;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMap;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;

/**
 * Class Settings
 * @package Concrete\Package\ParkingApi\Controller\SinglePage\Dashboard\Parking
 */
class Settings extends DashboardPageController
{
    /** @var ParkingMapService $parkingMapService */
    protected $parkingMapService;
    /** @var ParkingSlotsService $parkingSlotsService */
    protected $parkingSlotsService;

    public function view()
    {
        $parkingMapDao = new ParkingMapDaoImpl();
        $this->parkingMapService = new ParkingMapService($parkingMapDao);

        $parkingSlotsDao = new ParkingSlotsDaoImpl();
        $this->parkingSlotsService = new ParkingSlotsService($parkingSlotsDao);

        if ($this->post()) {
            $this->handleForm();
        }

        $this->set('numberOfEntryExitPoints', $this->parkingMapService->getEntryOrExitQuantity() ?: ParkingMap::ENTRY_OR_EXIT_QUANTITY_DEFAULT);

        $parkingSlots = $this->parkingSlotsService->getParkingSlots()->toArray();
        if ($parkingSlots) {
            $parkingSlots = json_encode($parkingSlots);
        } else {
            $parkingSlots = '[{"distancePoints":[1,2,3],"type":"SP"},{"distancePoints":[2,3,1],"type":"MP"},{"distancePoints":[3,1,2],"type":"LP"}]';
        }

        $this->set('parkingSlots', $parkingSlots);
    }

    private function handleForm()
    {
        $errors = [];

        if ($this->parkingMapService->isValidEntryOrExitQuantityInput($this->post('entry-exit-points'))) {
            $errors[] = 'Please enter a valid entry/exit quantity. Should be an integer, minimum 3.';
        }

        $inputParkingSlots = json_decode($this->post('parking-slots'), true);

        if (!$inputParkingSlots || !is_array($inputParkingSlots)) {
            $errors[] = 'Please enter a properly formatted json for parking slots.';
        }

        if ($inputParkingSlots) {
            foreach ($inputParkingSlots as $inputParkingSlot) {
                $parkingSlot = new ParkingSlot($inputParkingSlot);
                if (!$parkingSlot->hasValidType()) {
                    $errors[] = 'Please make sure that the type values are valid.';
                    break;
                }

                if (count($parkingSlot->getDistancePoints()) != $this->post('entry-exit-points')) {
                    $errors[] = 'Please make sure that the number of distance points are matched with the number of entry/exit quantity.';
                    break;
                }

                if (!is_array($parkingSlot->getDistancePoints())) {
                    $errors[] = 'Please make sure that the distance points are arrays.';
                    break;
                }

                foreach ($parkingSlot->getDistancePoints() as $distancePoint) {
                    if (!is_int($distancePoint)) {
                        $errors[] = 'Please make sure that the values in distance points are integers.';
                        break 2;
                    }
                }
            }
        }

        if (count($errors)) {
            $this->set('notify', [
                    'icon' => 'error',
                    'title' => implode(' ', $errors),
                ]
            );

            return;
        }

        $this->parkingMapService->saveEntryOrExitQuantity($this->post('entry-exit-points'));
        $this->parkingSlotsService->save($inputParkingSlots);

        $this->set('notify', [
                'icon' => 'check',
                'title' => 'Saved changes',
            ]
        );
    }

}