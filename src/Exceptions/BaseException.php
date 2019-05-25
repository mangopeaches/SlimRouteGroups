<?php
namespace SlimRouteGroups\Exceptions;

use Exception;

/**
 * Base exception for all exceptions to extend.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class BaseException extends Exception
{
    /**
     * Instantiate a new instance.
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Represent object as string.
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
