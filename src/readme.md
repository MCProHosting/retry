# retry

Retry is a useful class to allow you to retry tasks until they work, optionally setting a delay between iterations, including flexible conditioning and readable syntax.

![RETRY](http://cdn.smosh.com/sites/default/files/bloguploads/cwolf-22.jpg)

## Usage

```php

// This retries running the given function until "working" is returned. It retries up to ten times, pausing for
// 1000 ms (one second) in between attempts.

Retry::running(function () {
    if (doSomething()) {
        return 'working';
    } else {
        return 'broken';
    }
})->whileIsnt('working')
  ->delay(1000)
  ->go(10);

// Passing a callable in "soLongAs" allows you to transform the output before testing its value. Retry will also return
// the last value gotten from running(), if it did ever success, or the boolean false if it did not.
$arrayThing = Retry::running(function () {
    return complicatedArrayThing();
})->soLongAs(function ($arrayThing) {
    return transformToString($arrayThing);
})->is(false)->go(5);

if ($arrayThing === false) {
    echo "We're out of ArrayThings today!";
} else {
    echo "Here's your ArrayThing!";
    var_dump($arrayThing);
}

```
