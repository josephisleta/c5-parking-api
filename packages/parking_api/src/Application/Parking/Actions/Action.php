<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Actions;

use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\Request;

/**
 * Interface Action
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Actions
 */
interface Action
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function process(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function validate(Request $request);
}