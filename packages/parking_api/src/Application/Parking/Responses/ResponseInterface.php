<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Responses;

/**
 * Interface ResponseInterface
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Responses
 */
interface ResponseInterface
{
    /**
     * @return mixed
     */
    public function getErrorCode();

    /**
     * @param $errorCode
     * @return mixed
     */
    public function setErrorCode($errorCode);

    /**
     * @return string
     */
    public function getErrorMessage();

    /**
     * @param string $errorMessage
     * @return string
     */
    public function setErrorMessage($errorMessage);

    /**
     * @return array
     */
    public function getSuccessBody();

    /**
     * @return array
     */
    public function getErrorBody();

    /**
     * @return array
     */
    public function getBody();

    /**
     * @return false|string
     */
    public function toJson();
}