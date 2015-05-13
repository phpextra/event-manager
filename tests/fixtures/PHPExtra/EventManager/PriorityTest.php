<?php

namespace fixtures\PHPExtra\EventManager;

use PHPExtra\EventManager\Priority;
use PHPExtra\EventManager\PriorityResolver;

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
     * @return array
     */
    public function docCommentToValue()
    {
        return array(
            array('@priority   lowest',     Priority::LOWEST),
            array('@priority   lower',      Priority::LOWER),
            array('@priority   normal',     Priority::NORMAL),
            array('@priority   high',       Priority::HIGH),
            array('@priority   higher',     Priority::HIGHER),
            array('@priority   highest',    Priority::HIGHEST),
            array('@priority   monitor',    Priority::MONITOR),

            array('@priority   -1000',      Priority::LOWEST),
            array('@priority   1000',       Priority::HIGHEST),
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
        $value = PriorityResolver::getPriorityByName($name);
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
        $name = PriorityResolver::getPriorityName($value);
        $this->assertEquals($expectedName, $name);
    }

    /**
     * @dataProvider docCommentToValue
     *
     * @param string $comment
     * @param int $expectedValue
     */
    public function testGivenDocCommentReturnValidPriorityValue($comment, $expectedValue)
    {
        $value = PriorityResolver::getPriorityFromDocComment($comment, null);
        $this->assertEquals($expectedValue, $value);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGivenInvalidNonEmptyDocCommentThrowInvalidArgumentException()
    {
        PriorityResolver::getPriorityFromDocComment('@priority awda4tw4tw', null);
    }

    public function testGivenInvalidEmptyDocCommentThrowInvalidArgumentException()
    {
        $value = PriorityResolver::getPriorityFromDocComment('@priority', null);
        $this->assertNull($value);
    }
}
 