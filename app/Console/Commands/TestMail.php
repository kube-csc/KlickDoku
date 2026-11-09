<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

/**
 * Testet die E-Mail-Konfiguration.
 * Sendet eine Test-E-Mail an die angegebene Adresse oder an MAIL_FROM_ADDRESS.
 */
class TestMail extends Command
{
    protected $signature = 'mail:test {--to= : Empfänger-Adresse (optional)}';
    protected $description = 'Sendet eine Test-E-Mail, um die Konfiguration zu überprüfen';

    public function handle(): int
    {
        $to = $this->option('to') ?? config('mail.from.address');

        if (empty($to)) {
            $this->error('Keine Empfänger-Adresse angegeben.');
            $this->info('Verwenden Sie: php artisan mail:test --to=empfaenger@example.com');
            $this->info('Oder setzen Sie MAIL_FROM_ADDRESS in .env.');
            return Command::FAILURE;
        }

        // Konfiguration anzeigen
        $this->info('--- E-Mail-Konfiguration ---');
        $this->table(
            ['Einstellung', 'Wert'],
            [
                ['Mailer', config('mail.mailers.smtp.transport') ?? config('mail.mailers.smtp.driver') ?? 'smtp'],
                ['Host', config('mail.mailers.smtp.host') ?? 'Nicht konfiguriert'],
                ['Port', config('mail.mailers.smtp.port') ?? 'Nicht konfiguriert'],
                ['Von', config('mail.from.address')],
                ['Name', config('mail.from.name')],
                ['An', $to],
            ]
        );

        $this->newLine();
        $this->info("Sende Test-E-Mail an {$to} ...");

        try {
            Mail::raw(
                "Dies ist eine Test-E-Mail von der Vereins-Anleitungs-Website.\n\n"
                . "Wenn Sie diese E-Mail erhalten, ist die Konfiguration korrekt.\n\n"
                . "Zeit: " . now()->format('d.m.Y H:i:s') . "\n"
                . "Server: " . php_uname('n') . "\n"
                . "App-Name: " . config('app.name') . "\n"
                . "App-URL: " . config('app.url'),
                function ($message) use ($to) {
                    $message->to($to)
                        ->subject('[Test] Vereins-Anleitung E-Mail-Konfiguration');
                }
            );

            $this->newLine();
            $this->success("Test-E-Mail erfolgreich gesendet an {$to}!");

            if (config('mail.mailers.smtp.transport') === 'log' || config('mail.mailers.smtp.driver') === 'log') {
                $this->warn('Hinweis: MAIL_MAILER=log – E-Mails werden nur im Log gespeichert, nicht gesendet.');
                $this->info('Log-Datei: storage/logs/laravel.log');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('Fehler beim Senden: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Mögliche Ursachen:');
            $this->info('- SMTP-Server ist nicht erreichbar');
            $this->info('- Falsche Anmeldedaten (MAIL_USERNAME / MAIL_PASSWORD)');
            $this->info('- Firewall blockiert den Port');
            $this->info('- Zertifikatsprobleme (versuchen Sie MAIL_SCHEME=tls oder null)');
            return Command::FAILURE;
        }
    }
}
