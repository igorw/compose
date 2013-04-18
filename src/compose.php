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
    $prev = array_shift($fns);

    foreach ($fns as $fn) {
        $prev = function ($x) use ($fn, $prev) {
            $args = func_get_args();
            return $prev(compose\apply($fn, $args));
        };
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
