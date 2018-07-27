<?php

namespace spec;

use ParseData;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParseDataSpec extends ObjectBehavior
{
    function  it_takes_exception_with_invalid_file()
    {
        $this->shouldThrow('Exception')->duringReadFile("sssss");
    }
    function it_is_initializable()
    {
        $this->shouldHaveType(ParseData::class);
    }

    function  it_return_array_10_10_7_for_max_length()
    {
        $this->setData( [['last'=>"Zarnecki",'first'=>'Geoffrey','salary'=>'56500']]);
        $size = array(10,10,7);
        $this->getMaxSizeColumns()->shouldReturn($size);
    }
}
