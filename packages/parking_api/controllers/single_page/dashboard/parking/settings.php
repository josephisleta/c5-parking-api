<?php

namespace Concrete\Package\ParkingApi\Controller\SinglePage\Dashboard\Parking;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMap;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;

class Settings extends DashboardPageController
{
    public function view()
    {
        $parkingMapService = new ParkingMapService();
        $parkingSlotsService = new ParkingSlotsService();

        if ($this->post()) {

            $parkingMapService->saveEntryOrExitQuantity($this->post('entry-exit-points'));

            $inputParkingSlots = json_decode($this->post('parking-slots'), true);

            $parkingSlotsService->save($inputParkingSlots);

            $this->set('notify',
                [
                    'icon' => 'check',
                    'title' => 'Saved changes',
                ]
            );
        }

        $this->set('numberOfEntryExitPoints', $parkingMapService->getEntryOrExitQuantity() ?: ParkingMap::ENTRY_OR_EXIT_QUANTITY_DEFAULT);

        $parkingSlots = $parkingSlotsService->getParkingSlotsArray();
        if ($parkingSlots) {
            $parkingSlots = json_encode($parkingSlots);
        } else {
            $parkingSlots = '[{"distancePoints":[1,2,3],"type":"S"},{"distancePoints":[2,3,1],"type":"M"},{"distancePoints":[3,1,2],"type":"L"}]';
        }

        $this->set('parkingSlots', $parkingSlots);
    }

}