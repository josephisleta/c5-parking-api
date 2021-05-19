<?php

namespace Concrete\Package\ParkingApi\Controller\SinglePage\Dashboard\Parking;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMap;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;

class Settings extends DashboardPageController
{
    public function view()
    {
        $parkingMapService = new ParkingMapService();

        if ($this->post()) {

            $parkingMapService->saveEntryOrExitQuantity($this->post('entry-exit-points'));

            $this->set('notify',
                [
                    'icon' => 'check',
                    'title' => 'Saved changes',
                ]
            );
        }

        $this->set('numberOfEntryExitPoints', $parkingMapService->getEntryOrExitQuantity() ?: ParkingMap::ENTRY_OR_EXIT_QUANTITY_DEFAULT);

        $parkingSlots = '[{"distance":[1,2,3],"size":"S"},{"distance":[2,3,1],"size":"M"},{"distance":[3,1,2],"size":"L"}]';
        $this->set('parkingSlots', $parkingSlots);
    }

}