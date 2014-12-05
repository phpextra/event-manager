<?php

namespace fixtures\PHPExtra\EventManager;

use PHPExtra\EventManager\Priority;
use PHPExtra\EventManager\PriorityResolver;

/**
 * The PriorityResolverTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class PriorityResolverTest extends \PHPUnit_Framework_TestCase
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

    public function testCreateNewInstance()
    {
        new PriorityResolver();
    }

    /**
     * @dataProvider nameToValue
     *
     * @param string $name
     * @param int    $expectedValue
     */
    public function testGetsPriorityNameByValue($name, $expectedValue)
    {
        $resolver = new PriorityResolver();
        $value = $resolver->getPriorityByName($name);
        $this->assertEquals($expectedValue, $value);
    }

    /**
     * @dataProvider nameToValue
     *
     * @param string $expectedName
     * @param int    $value
     */
    public function testGetsPriorityValueByName($expectedName, $value)
    {
        $resolver = new PriorityResolver();
        $name = $resolver->getPriorityName($value);
        $this->assertEquals($expectedName, $name);
    }

    /**
     * @dataProvider docCommentToValue
     *
     * @param string $comment
     * @param int $expectedValue
     */
    public function testGetsPriorityValueFromDocComment($comment, $expectedValue)
    {
        $resolver = new PriorityResolver();
        $value = $resolver->getPriorityFromDocComment($comment, null);
        $this->assertEquals($expectedValue, $value);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsInvalidArgumentExceptionOnInvalidNonEmptyDocComment()
    {
        $resolver = new PriorityResolver();
        $resolver->getPriorityFromDocComment('@priority awda4tw4tw', null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown priority given: "jhon doe"
     */
    public function testThrowsInvalidArgumentExceptionOnInvalidPriorityValue()
    {
        $resolver = new PriorityResolver();
        $resolver->getPriorityName('jhon doe');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown priority name given: "jhon doe"
     */
    public function testThrowsInvalidArgumentExceptionOnInvalidPriorityName()
    {
        $resolver = new PriorityResolver();
        $resolver->getPriorityByName('jhon doe');
    }

    public function testReturnsNullOnInvalidEmptyDocComment()
    {
        $resolver = new PriorityResolver();
        $value = $resolver->getPriorityFromDocComment('@priority', null);
        $this->assertNull($value);
    }
}
 