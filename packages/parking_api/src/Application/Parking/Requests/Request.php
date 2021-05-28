<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Requests;

/**
 * Interface Request
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Requests
 */
interface Request
{
    /**
     * Request constructor.
     * @param array $request
     */
    public function __construct($request);
}