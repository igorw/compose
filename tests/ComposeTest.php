<?php

namespace igorw;

class ComposeTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException InvalidArgumentException
     */
    function testComposeWithoutArgs() {
        compose();
    }

    function testComposeWithSingleFunc() {
        $id = function ($x) { return $x; };
        $composed = compose($id);
        $this->assertNull($composed(null));
        $this->assertTrue($composed(true));
        $this->assertFalse($composed(false));
        $this->assertSame('foo', $composed('foo'));
    }

    function testComposeWithMultipleFuncs() {
        $composed = compose(
            function ($x) { return "baz($x)"; },
            function ($x) { return "bar($x)"; },
            function ($x) { return "foo($x)"; }
        );
        $this->assertSame('baz(bar(foo(x)))', $composed('x'));
    }

    function testComposeWithMultipleArgs() {
        $composed = compose(
            function ($x) { return "bar($x)"; },
            function ($a, $b, $c) { return "foo($a, $b, $c)"; }
        );
        $this->assertSame('bar(foo(a, b, c))', $composed('a', 'b', 'c'));
    }

    function testPipelineWithMultipleFuncs() {
        $composed = pipeline(
            function ($x) { return "foo($x)"; },
            function ($x) { return "bar($x)"; },
            function ($x) { return "baz($x)"; }
        );
        $this->assertSame('baz(bar(foo(x)))', $composed('x'));
    }
}
