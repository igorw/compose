<?php

namespace igorw;

/** @api */
function compose() {
    if (!func_num_args()) {
        throw new \InvalidArgumentException('You must provide at least one function to compose().');
    }

    foreach (func_get_args() as $i => $fn) {
        if (!is_callable($fn)) {
            throw new \InvalidArgumentException(
                sprintf('Argument %d to compose() is not callable.', $i));
        }
    }

    $fns = func_get_args();
    $f = new \ReflectionFunction($fns[count($fns)-1]);
    $args = implode(',', array_map(function ($p) {
        return (($c = $p->getClass()) ? '\\'.$c->getName() : '').' $'.$p->getName();
    }, $f->getParameters()));

    $prev = array_shift($fns);

    foreach ($fns as $fn) {
        $prev = function ($x) use ($fn, $prev) {
            $args = func_get_args();
            return $prev(compose\apply($fn, $args));
        };
        $fnSrc = 'return function ('.$args.') use ($prev) { return call_user_func_array($prev, func_get_args());};';
        $prev = eval($fnSrc);
    }

    return $prev;
}

/** @api */
function pipeline() {
    return compose\apply('igorw\compose', array_reverse(func_get_args()));
}

namespace igorw\compose;

function apply($fn, $args) {
    return call_user_func_array($fn, $args);
}
