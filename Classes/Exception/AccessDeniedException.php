<?php

namespace FelixNagel\T3extblog\Exception;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class AccessDeniedException extends Exception
{
    protected $code = 1583972224;

    protected $message = 'Access denied!';
}
