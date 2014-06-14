<?php

namespace Unit\Entity;

use OroCRM\Bundle\ZendeskBundle\Entity\TicketStatus;

class TicketStatusTest extends \PHPUnit_Framework_TestCase
{
    protected $name = TicketStatus::STATUS_CLOSED;

    /**
     * @var TicketStatus
     */
    protected $target;

    public function setUp()
    {
        $this->target = new TicketStatus($this->name);
    }

    public function testName()
    {
        $actual = $this->target->getName();
        $this->assertEquals($actual, $this->name);
    }

    public function testIsEqualTo()
    {
        $other = new TicketStatus(TicketStatus::STATUS_HOLD);
        $this->assertFalse($this->target->isEqualTo($other));
        $other = new TicketStatus(TicketStatus::STATUS_CLOSED);
        $this->assertTrue($this->target->isEqualTo($other));
    }
    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $method = 'set' . ucfirst($property);
        $result = $this->target->$method($value);

        $this->assertInstanceOf(get_class($this->target), $result);
        $this->assertEquals($value, $this->target->{'get' . $property}());
    }

    /**
     * @return array
     */
    public function settersAndGettersDataProvider()
    {
        return array(
            array('label', 'test label'),
            array('locale', 'test locale')
        );
    }
}
