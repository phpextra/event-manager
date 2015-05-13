<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Exception;

/**
 * The RuntimeException class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class RuntimeException extends \RuntimeException
{
    /**
     * @var ExceptionContext
     */
    private $context;

    /**
     * @param string           $message
     * @param ExceptionContext $context
     * @param int              $code
     * @param \Exception       $previous
     */
    public function __construct($message = "", ExceptionContext $context, $code = 0, \Exception $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ExceptionContext
     */
    public function getContext()
    {
        return $this->context;
    }
}