<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Test tidak bergantung pada hasil `npm run build`.
        $this->withoutVite();

        // Pesan validasi diuji dalam Bahasa Indonesia, apa pun isi .env.
        app()->setLocale('id');
    }
}
