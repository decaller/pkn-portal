<?php

test('basic navigation', function () {
    $page = visit('/');
    $page->assertNoJavaScriptErrors()->assertNoConsoleLogs();
});
