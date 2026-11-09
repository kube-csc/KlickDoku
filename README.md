# Webseitevereinsverwaltung

Dieses Projekt ist ein **Dokumentations-CMS** für das Vereinsprojekt. Es dient als zentrale Anlaufstelle für Nutzer, die die Software des Vereins verstehen und nutzen möchten.

## Zweck

Die Webseite bietet:

- **Projektbeschreibung:** Marketing-Seiten, die das Vereinsprojekt und seine Module vorstellen.
- **Online-Anleitung:** Strukturierte, durchsuchbare Dokumentation mit Schritt-für-Schritt-Anleitungen und Screenshots.
- **Interaktive Tutorials:** Klickbare Tutorials mit Screenshots, markierten Klick-Bereichen und Fortschrittsspeicherung.
- **Community:** Benutzerkonten, Kommentare und Feedback für den Austausch zwischen Nutzern und Entwicklern.

## Technologie-Stack

| Komponente | Technologie |
|---|---|
| Framework | Laravel 12 (PHP 8.2) |
| Authentifizierung | Laravel Jetstream + Livewire |
| Frontend | Blade + Livewire + Tailwind CSS |
| Datenbank | MySQL 8+ (kann auf entferntem Server liegen) |
| Suche | Laravel Scout + MySQL Full-Text |

## Hauptprojekte

Die Webseite referenziert die 5 Hauptprojekte des Vereinsprojekt-Workspaces:

| Projekt | Rolle |
|---|---|
| **vereinsverwaltung_dev** | Backend + Frontend — Hauptprojekt |
| **regattaview** | Nur Frontend — Regatta-Ansicht |
| **helferplanung** | Backend + Frontend — Helferplanung |
| **Kurse** | Backend + Frontend — Kursverwaltung |
| **regattamanger** | Backend + Frontend — Regatta-Management |

## Schnellstart

```bash
# Abhängigkeiten installieren
composer install

# Frontend-Abhängigkeiten installieren
npm install

# Umgebungsvariablen konfigurieren
cp .env.example .env
php artisan key:generate

# Datenbank migrieren und mit Demo-Daten füllen
php artisan migrate:fresh --seed

# Frontend bauen
npm run build

# Entwicklungsserver starten
php artisan serve
```

## Demo-Login

Nach dem Seeding können Sie sich mit folgenden Daten anmelden:

| Rolle | E-Mail | Passwort |
|---|---|---|
| Administrator | admin@verein.local | password |

## Datenbank-Konfiguration

Die Datenbank kann auf einem entfernten Server liegen:

```env
DB_CONNECTION=mysql
DB_HOST=dein-datenbank-server.de
DB_PORT=3306
DB_DATABASE=webseitevereinsverwaltung
DB_USERNAME=benutzername
DB_PASSWORD=geheimes_passwort
```

## Projektstruktur

```
├── app/
│   ├── Http/Controllers/    # Controller für Anleitungen, Tutorials, etc.
│   ├── Livewire/            # Livewire-Komponenten (Jetstream)
│   ├── Models/              # Eloquent-Modelle
│   │   ├── Category.php
│   │   ├── Guide.php
│   │   ├── GuideStep.php
│   │   ├── GuideImage.php
│   │   ├── Tutorial.php
│   │   ├── TutorialStep.php
│   │   ├── TutorialHotspot.php
│   │   ├── TutorialProgress.php
│   │   ├── Page.php
│   │   ├── Comment.php
│   │   ├── Feedback.php
│   │   ├── Rating.php
│   │   └── Tag.php
│   └── ...
├── database/
│   ├── migrations/          # Datenbank-Migrationen
│   ├── seeders/             # Demo-Daten
│   └── factories/           # Test-Factories
├── docs/                    # Konzept-Dokumente (Markdown)
├── resources/
│   ├── views/               # Blade-Vorlagen + Livewire-Komponenten
│   └── ...
└── routes/
    └── web.php              # Web-Routes
```

## Datenbank-Modelle

| Tabelle | Zweck |
|---|---|
| `categories` | Anleitungskategorien (hierarchisch) |
| `guides` | Anleitungen (Dokumentation) |
| `guide_steps` | Schritte innerhalb einer Anleitung |
| `guide_images` | Screenshots und Bilder |
| `tutorials` | Interaktive Tutorials |
| `tutorial_steps` | Schritte eines Tutorials |
| `tutorial_hotspots` | Klick-Bereiche auf Screenshots |
| `tutorial_progress` | Nutzer-Fortschritt pro Tutorial |
| `comments` | Kommentare (polymorph) |
| `feedback` | Nutzer-Feedback |
| `ratings` | Bewertungen (polymorph) |
| `pages` | Statische Seiten (Marketing) |
| `tags` | Tags für Kategorien |

## Konzept

Das detaillierte Konzept mit Datenbank-Architektur, Frontend/Backend-Design und Implementierungsplan findet sich in:

📄 [docs/Konzept.md](docs/Konzept.md)

## Lizenz

Zu definieren.
