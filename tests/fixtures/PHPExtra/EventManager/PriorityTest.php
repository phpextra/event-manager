<?php

namespace fixtures\PHPExtra\EventManager;

use PHPExtra\EventManager\Priority;

/**
 * The PriorityTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function nameToValue()
    {
        return array(
            array('lowest', -1000),
            array('lower', -750),
            array('low', -500),
            array('normal', 0),
            array('high', 500),
            array('higher', 750),
            array('highest', 1000),
            array('monitor', ~PHP_INT_MAX),
        );
    }

    /**
     * @dataProvider nameToValue
     *
     * @param string $name
     * @param int    $expectedValue
     */
    public function testGivenPriorityValueReturnItsName($name, $expectedValue)
    {
        $value = Priority::getPriorityByName($name);
        $this->assertEquals($expectedValue, $value);
    }

    /**
     * @dataProvider nameToValue
     *
     * @param string $expectedName
     * @param int    $value
     */
    public function testGivenValidPriorityNameReturnItsIntegerValue($expectedName, $value)
    {
        $name = Priority::getPriorityName($value);
        $this->assertEquals($expectedName, $name);
    }
}
 