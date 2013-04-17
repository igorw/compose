# igorw/compose

Function composition.

Allows you to stitch functions together to form a pipeline. This can be useful
if you have to transform data in many steps and you want to describe those
steps on a high level.

## compose

Generally, function composition means taking two functions `f` and `g`, and
producing a new function `z`, which applies `f` to the result of `g`.

    z = compose(f, g)
    ; z(x) => f(g(x))

This library provides a `compose` function that does just this.

    $z = igorw\compose($f, $g);
    var_dump($z($x));

It supports an arbitrary number of functions to be composed via varargs.

    $z = igorw\compose($f, $g, $h, $i);

The innermost function (the last one in the list) can take an arbitrary number
of arguments, whereas the others may only take a single argument.

    $z = igorw\compose($f, $g);
    $z('a', 'b', 'c');
    // => $f($g('a', 'b', 'c'))

## pipeline

`pipeline` is the same as `compose`, but the arguments are reversed. This is
more easy to read in some cases, because you can list the functions in the
order they will be called.

It is quite similar to a unix pipe in that regard.

## Examples

    function transform_data($data) {
        return [
            'name' => $data['firstname'].' '.$data['lastname'],
        ];
    }

    $transformJson = igorw\pipeline(
        function ($json) { return json_decode($json, true); },
        'transform_data',
        'json_encode'
    );

    $json = <<<EOF
    {"firstname": "Igor", "lastname": "Wiedler"}
    {"firstname": "Beau", "lastname": "Simensen"}
    EOF;

    $list = explode("\n", $json);
    $newList = array_map($transformJson, $list);
    $newJson = implode("\n", $newList);
