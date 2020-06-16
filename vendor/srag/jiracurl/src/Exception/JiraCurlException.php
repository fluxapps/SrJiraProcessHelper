<?php

namespace srag\JiraCurl\SrJiraProcessHelper\Exception;

use ilException;

/**
 * Class JiraCurlException
 *
 * @package srag\JiraCurl\SrJiraProcessHelper\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class JiraCurlException extends ilException
{

    /**
     * JiraCurlException constructor
     *
     * @param string $message
     * @param int    $code
     *
     * @internal
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
