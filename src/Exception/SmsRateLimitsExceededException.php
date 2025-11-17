<?php

namespace App\Exception;

use Exception;

class SmsRateLimitsExceededException extends Exception
{
    protected $message = 'Rate limits have been exceeded for the SMS gateway.';
}