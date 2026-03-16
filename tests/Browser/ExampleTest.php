<?php

use function Pest\Laravel\get;

test('basic navigation', function () {
    $page = visit('/');
    $page->assertNoJavaScriptErrors()->assertNoConsoleLogs();
});
