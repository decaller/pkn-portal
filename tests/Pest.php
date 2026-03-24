<?php

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->in('Feature', 'Browser');

pest()->browser()->timeout(30000);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function assertMatchesApiResult(TestResponse $response, string $path): void
{
    $fullPath = base_path("react native dev guide/api_result/{$path}");

    if (! file_exists($fullPath)) {
        test()->fail("API result file not found at: {$fullPath}");
    }

    $expectedContent = file_get_contents($fullPath);
    $appUrl = rtrim(config('app.url'), '/');
    $normalizedExpected = json_decode(str_replace('http://localhost', $appUrl, $expectedContent), true);

    assertArrayStructure($normalizedExpected, $response->json());
}

function assertArrayStructure(array $expected, array $actual, string $path = ''): void
{
    foreach ($expected as $key => $value) {
        Assert::assertArrayHasKey($key, $actual, 'Missing key: '.($path ? "{$path}.{$key}" : $key));

        if (is_array($value) && ! empty($value)) {
            $currentPath = $path ? "{$path}.{$key}" : $key;

            if ($actual[$key] === null) {
                continue;
            }

            if (array_is_list($value)) {
                Assert::assertIsArray($actual[$key], "Key {$currentPath} is not an array");
                if (isset($value[0]) && is_array($value[0])) {
                    foreach ($actual[$key] as $index => $item) {
                        assertArrayStructure($value[0], $item, "{$currentPath}[{$index}]");
                    }
                }
            } else {
                Assert::assertIsArray($actual[$key], "Key {$currentPath} is not an array (value is ".gettype($actual[$key]).')');
                assertArrayStructure($value, $actual[$key], $currentPath);
            }
        }
    }
}
