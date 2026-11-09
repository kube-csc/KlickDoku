<?php

namespace Tests\Dusk;

use Laravel\Dusk\Browser;
use Tests\Dusk\TestCase;

class TutorialTest extends TestCase
{
    /** @test */
    public function tutorials_index_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/de/tutorials')
                ->assertSee('Tutorials');
        });
    }

    /** @test */
    public function tutorial_detail_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/de/tutorials/erste-schritte')
                ->assertSee('Erste Schritte')
                ->assertSee('Schritte');
        });
    }
}
