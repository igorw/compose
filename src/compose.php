<?php

namespace igorw;

/** @api */
function compose() {
    $fns = func_get_args();
    $prev = array_shift($fns);

    if (!$prev) {
        throw new \InvalidArgumentException('You must provide at least one function to compose().');
    }

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
