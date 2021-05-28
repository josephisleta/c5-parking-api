<?php

namespace Concrete\Package\ParkingApi\Controller\SinglePage\Dashboard\Parking;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap\ParkingMapDaoImpl;
use Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots\ParkingSlotsDaoImpl;
use Concrete\Package\ParkingApi\Src\Application\Parking\Dashboard\Settings as ApplicationParkingSettings;

/**
 * Class Settings
 * @package Concrete\Package\ParkingApi\Controller\SinglePage\Dashboard\Parking
 */
class Settings extends DashboardPageController
{
    private $parkingSettings;

    /**
     * Settings constructor.
     * @param Page $c
     */
    public function __construct(Page $c)
    {
        parent::__construct($c);

        $parkingMapDao = new ParkingMapDaoImpl();
        $parkingSlotsDao = new ParkingSlotsDaoImpl();
        $this->parkingSettings = new ApplicationParkingSettings($parkingMapDao, $parkingSlotsDao);
    }

    public function view()
    {
        $this->set('numberOfEntryExitPoints', $this->parkingSettings->getNumberOfEntryExitPoints());
        $this->set('parkingSlots', $this->parkingSettings->getParkingSlots());
        $this->set('parkingSlotsSample', $this->parkingSettings->getSampleParkingSlotsJson());
    }

    public function submit()
    {
        if ($this->post()) {
            $errors = $this->parkingSettings->validateForm($this->post('entry-exit-points'), $this->post('parking-slots'));

            if (count($errors)) {
                $this->error->add(implode(' ', $errors));
            } else {
                $this->set('success', 'Successfully saved changes');
            }
        }

        $this->view();
    }
}