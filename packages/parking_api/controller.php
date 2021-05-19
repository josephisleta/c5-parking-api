<?php

namespace Concrete\Package\ParkingApi;

use Package;
use Page;
use SinglePage;

class Controller extends Package
{
    /**
     * Protected data members for controlling the instance of the package
     */
    protected $pkgHandle = 'parking_api';
    protected $appVersionRequired = '5.7.5.6';
    protected $pkgVersion = '0.2';

    /**
     * This function returns the functionality description ofthe package.
     * @return string
     */
    public function getPackageDescription()
    {
        return t("A package for parking api.");
    }

    /**
     * This function returns the name of the package.
     * @return string|null
     */
    public function getPackageName()
    {
        return t("Parking API");
    }

    /**
     * This function is executed during initial installation of the package.
     */
    public function install()
    {
        $pkg = parent::install();

        // Install Single Pages
        $this->install_single_pages($pkg);
    }

    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->install_single_pages($pkg);
    }


    /**
     * This function is executed during uninstallation of the package.
     */
    public function uninstall()
    {
        $pkg = parent::uninstall();
    }

    /**
     * This function is used to install single pages.
     * @param $pkg
     */
    function install_single_pages($pkg)
    {
        $singlePages = array(
            'Parking API Settings' => '/dashboard/parking/settings'
        );

        foreach ($singlePages as $cName => $cPath) {
            if (!Page::getByPath($cPath)->cID) {
                $page = SinglePage::add($cPath, $pkg);
                $page->update(array('cName' => $cName));
            }
        }
    }

}