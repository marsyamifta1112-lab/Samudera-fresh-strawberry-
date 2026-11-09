<?php

namespace Tests\Feature;

use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    public function test_login_form_uses_https_when_forwarded_by_railway(): void
    {
        $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'samudera-fresh-strawberry-production.up.railway.app',
        ])->get('http://samudera-fresh-strawberry-production.up.railway.app/login')
            ->assertOk()
            ->assertSee(
                'action="https://samudera-fresh-strawberry-production.up.railway.app/login"',
                false
            );
    }
}
