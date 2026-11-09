<?php

namespace Tests\Dusk;

use Laravel\Dusk\Browser;
use Tests\Dusk\TestCase;

class SearchTest extends TestCase
{
    /** @test */
    public function search_returns_results()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/de/suche?q=Fördermittel')
                ->assertSee('Fördermittel-Antrag erstellen');
        });
    }

    /** @test */
    public function search_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/de/suche?q=')
                ->assertSee('Suche');
        });
    }
}
