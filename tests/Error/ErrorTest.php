<?php

/*
 * This file is part of the json-schema bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Soyuka\JsonSchemaBundle\tests\Error;

use Soyuka\JsonSchemaBundle\Error\Error;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testError()
    {
        $error = new Error('1', '2', '3');

        $this->assertEquals('1', $error->getProperty());
        $this->assertEquals('2', $error->getViolation());
        $this->assertEquals('3', $error->getConstraint());
        $this->assertEquals('1: 2 (3)', (string) $error);
    }
}
