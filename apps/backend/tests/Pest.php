<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Bind the Feature and Unit suites to the application's TestCase so Pest
| can run the existing Laravel test classes (and any new Pest-style tests).
|
*/

uses(TestCase::class)->in('Feature');
