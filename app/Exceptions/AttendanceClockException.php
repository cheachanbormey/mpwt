<?php

namespace App\Exceptions;

use Exception;

/**
 * Raised when a clock action is refused.
 *
 * The message is written for the person who pressed the button, so the
 * controller can show it as-is instead of inventing wording at the call site.
 */
class AttendanceClockException extends Exception
{
    //
}
