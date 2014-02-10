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

    /**
     * @expectedException InvalidArgumentException
     */
    function testComposeWithInvalidArg() {
        compose('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testComposeWithJustOneInvalidArg() {
        $fn = function () {};
        compose($fn, 'foo', $fn);
    }

    function testPipelineWithMultipleFuncs() {
        $composed = pipeline(
            function ($x) { return "foo($x)"; },
            function ($x) { return "bar($x)"; },
            function ($x) { return "baz($x)"; }
        );
        $this->assertSame('baz(bar(foo(x)))', $composed('x'));
    }

    function testComposeReflectTypeHints() {
        $composed = compose(
            function (Foo $x) { return $x; },
            function (Foo $x, Bar $y) { return $x; }
        );

        $f = new \ReflectionFunction($composed);

        $this->assertEquals('igorw\Foo', $f->getParameters()[0]->getClass()->getName());
        $this->assertEquals('igorw\Bar', $f->getParameters()[1]->getClass()->getName());
    }
}

class Foo {};
class Bar {};
