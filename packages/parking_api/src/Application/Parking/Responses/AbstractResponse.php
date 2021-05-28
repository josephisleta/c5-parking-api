<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Responses;

/**
 * Class AbstractResponse
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Responses
 */
abstract class AbstractResponse implements ResponseInterface
{
    protected $errorMessage;
    protected $errorCode;

    protected $status = 200;

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param mixed $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param mixed $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        if ($this->getErrorCode()) {
            return $this->getErrorBody();
        }

        return $this->getSuccessBody();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->getBody());
    }
}