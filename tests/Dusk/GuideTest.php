<?php

namespace Tests\Dusk;

use Laravel\Dusk\Browser;
use Tests\Dusk\TestCase;

class GuideTest extends TestCase
{
    /** @test */
    public function guides_index_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/de/anleitungen')
                ->assertSee('Anleitungen')
                ->assertSee('Fördermittel-Antrag erstellen')
                ->assertSee('Neues Mitglied anlegen');
        });
    }

    /** @test */
    public function guide_detail_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/de/anleitungen/foerdermittel-antrag-erstellen')
                ->assertSee('Fördermittel-Antrag erstellen')
                ->assertSee('Schritte');
        });
    }

    /** @test */
    public function guide_pdf_export_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/de/anleitungen/foerdermittel-antrag-erstellen/pdf')
                ->assertResponseOk();
        });
    }
}
