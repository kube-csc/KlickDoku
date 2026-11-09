<?php

namespace Tests\Dusk;

use Laravel\Dusk\Browser;
use Tests\Dusk\TestCase;

class LoginTest extends TestCase
{
    /** @test */
    public function login_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Anmelden')
                ->assertInputPresent('email')
                ->assertInputPresent('password');
        });
    }

    /** @test */
    public function user_can_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@verein.local')
                ->type('password', 'password')
                ->press('Anmelden')
                ->assertPathIs('/dashboard')
                ->assertSee('Willkommen');
        });
    }

    /** @test */
    public function user_can_access_admin_panel()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@verein.local')
                ->type('password', 'password')
                ->press('Anmelden')
                ->pause(1000)
                ->clickLink('Admin-Panel')
                ->assertPathIs('/admin')
                ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function user_can_logout()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@verein.local')
                ->type('password', 'password')
                ->press('Anmelden')
                ->pause(1000)
                ->press('Logout')
                ->assertPathIs('/login');
        });
    }
}
