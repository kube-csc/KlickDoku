# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.1.0/), und dieses Projekt folgt der [Semantischen Versionierung](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Hinzugefügt - Multi-Projekt-Dokumentationssystem

Ein zentrales Dokumentationssystem, das aus einer einzigen Master-Dokumentation mehrere projektspezifische Dokumentationen dynamisch bereitstellt.

**Datenbank & Modelle:**
- 8 neue Tabellen: `projects`, `project_terms`, `guide_project`, `tutorial_project`, `project_guide_images`, `project_tutorial_images`, `placeholder_definitions`, `project_user`
- 7 neue Modelle: `Project`, `ProjectTerm`, `GuideProject`, `TutorialProject`, `ProjectGuideImage`, `ProjectTutorialImage`, `PlaceholderDefinition`
- Bestehende Modelle erweitert: `Guide`, `Tutorial`, `GuideStep`, `TutorialStep`, `User`

**Platzhalter-System:**
- 20 vordefinierte Platzhalter (`{{app_name}}`, `{{events}}`, `{{members}}`, etc.)
- `PlaceholderService` für zentrale Ersetzung mit Caching
- `@placeholders()` Blade-Directive für Views
- Projekt-spezifische Begriffe pro Sprache (de/en)

**Projekt-Erkennung:**
- `ProjectResolver` erkennt Projekt via Subdomain, Query-Parameter (`?project=slug`), oder Domain
- `SetProject` Middleware setzt aktuelles Projekt pro Anfrage
- `currentProject()` Helper-Funktion
- IP-Adressen werden nicht als Subdomain interpretiert

**Inhaltsfreigabe (Opt-in):**
- Guides und Tutorials müssen explizit Projekten zugewiesen werden
- Pivot-Tabellen `guide_project` und `tutorial_project`

**Screenshot-Fallback:**
- Master-Screenshot als Standard
- Projekt-spezifischer Screenshot überschreibt Master, wenn vorhanden
- `ProjectScreenshotService` kapselt die Fallback-Logik

**User-Rollen pro Projekt:**
- Many-to-Many Beziehung zwischen Projects und Users
- Drei Rollen: Admin, Editor, Viewer
- `ProjectPolicy` mit 7 Berechtigungs-Methoden
- `@can`-Direktiven in Admin-Views

**Admin-Panel:**
- Projekt-CRUD mit Tabs: Grunddaten, Begriffe, Inhaltsfreigabe, Screenshots, Users
- JSON/CSV Export/Import für Projektbegriffe
- Screenshot-Upload-UI mit Master-Bild-Vergleich
- User-Rollen-Verwaltung

**API-Erweiterungen:**
- Alle API-Endpunkte filtern nach Projekt und ersetzen Platzhalter
- `GuideResource` und `TutorialResource` mit Platzhalter-Ersetzung

**Artisan-Befehle:**
- `docs:import-terms` – Begriffe aus Language Files/API importieren
- `docs:seed-placeholders` – Platzhalter-Katalog initial befüllen
- `docs:verify-placeholders` – Unersetzte Platzhalter finden

**Migrationen konsolidiert:**
- SEO-Felder, Hotspot-Selector und Hotspot-Link in ursprüngliche Migrationen integriert
- Nachträgliche Migrationen gelöscht

**Tests:**
- 30 neue Tests: PlaceholderService (11), ProjectResolver (11), SetProjectMiddleware (7), ApiProjectContext (11), ProjectPolicy (25)

**Dokumentation:**
- `docs/Konzept.md` um Kapitel 11 (Multi-Projekt-System) erweitert
