# Webseitevereinsverwaltung — Konzeptplanung

## 1. Projektübersicht

Die **Webseitevereinsverwaltung** ist ein Dokumentations-CMS, das parallel zur Entwicklung der 5 Hauptprojekte des Vereinsprojekt-Workspaces entsteht. Sie dient als zentrale Anlaufstelle für Nutzer, die verstehen möchten, wofür die Software da ist und wie sie funktioniert.

### Zielsetzung

- **Nutzer ansprechen:** Potenzielle Nutzer sollen die Software verstehen und ausprobieren können.
- **Anleitung bieten:** Schritt-für-Schritt-Anleitungen mit Screenshots zeigen, wie jede Funktion genutzt wird.
- **Interaktiv unterstützen:** Eigenes interaktives Tutorial-System — ähnlich wie Scribe, aber kostenlos: Screenshots mit markierten Klick-Bereichen, Schritt-für-Schritt-Navigation und Fortschrittsspeicherung.
- **Community aufbauen:** Benutzerkonten, Kommentare und Feedback schaffen einen Austausch zwischen Nutzern und Entwicklern.

### Zielgruppen

| Zielgruppe | Bedarf |
|---|---|
| **Endanwender** | Verstehen, was die Software kann; Anleitungen zur Nutzung |
| **Entwickler** | Technische Dokumentation, Architektur, API-Referenz |
| **Interessenten** | Ersteindruck, Marketing-Inhalte, Module-Übersicht |

---

## 2. Funktionen im Überblick

### 2.1 Projektbeschreibung (Marketing)

Die Startseite und die Module-Seiten präsentieren die Software als Ganzes und zeigen die einzelnen Module im Überblick.

**Inhalte:**
- Willkommensseite mit Kurzübersicht des Vereinsprojekts
- Module-Übersicht: Vereinskern, Regattaview, Helferplanung, Kurse, Regattamanager
- Jedes Modul wird mit kurzer Beschreibung und Screenshot vorgestellt
- Download- oder Zugriffslink zu den jeweiligen Projekten

**Seitenstruktur (mit Sprach-Präfix):**

Die Startseite `/` zeigt eine **Sprachauswahl-Seite**, auf der Besucher zwischen Deutsch und Englisch wählen können. Nach der Auswahl werden sie zur entsprechenden Startseite (`/de/` oder `/en/`) weitergeleitet. Die Sprache kann später jederzeit über den Sprachumschalter in der Navigation geändert werden.

```
/                    — Sprachauswahl (Language Selection)
/{locale}/           — Startseite mit Sprachauswahl
/{locale}/projekt    — Projektbeschreibung
/{locale}/module     — Alle Module im Überblick
/{locale}/module/{slug}  — Detailseite pro Modul
```

**Beispiel:** `http://127.0.0.1:8000/` zeigt die Sprachauswahl. Nach Klick auf „Deutsch" gelangt man zu `http://127.0.0.1:8000/de/`.

### 2.2 Online-Anleitung (Dokumentation)

Strukturierte, durchsuchbare Dokumentation mit Schritt-für-Schritt-Anleitungen und Screenshots.

**Zugriff: Öffentlich, ohne Login**

Die Anleitung ist für jeden Nutzer ohne Benutzerkonto und ohne Login zugänglich. Jeder Besucher kann alle Anleitungen lesen, durchsuchen und durchblättern — ein Benutzerkonto ist nicht erforderlich. Nur interaktive Funktionen wie Kommentare, Bewertungen und Tutorial-Fortschrittsspeicherung erfordern eine Anmeldung.

**Inhalte:**
- Anleitungen nach Funktionsbereichen gegliedert (entspricht `docs/`-Struktur der Hauptprojekte)
- Jede Anleitung enthält: Titel, Beschreibung, Screenshots, Schritt-für-Schritt-Text
- **Schritt-Bilder:** Jeder Schritt kann eigene Bilder/Screenshots enthalten, die direkt zum Schritt gehören
- **Globale Bilder:** Zusätzlich können Bilder auf Anleitungsebene (global) hinzugefügt werden, die im Header-Bereich angezeigt werden
- Kategorien und Tags für einfache Navigation
- Versionsangabe (auf welche Softwareversion sich die Anleitung bezieht)

**Seitenstruktur:**
```
/{locale}/anleitungen          — Übersicht aller Anleitungen
/{locale}/anleitungen/{slug}   — Einzelne Anleitung mit Screenshots
/{locale}/anleitungen/{slug}/pdf — PDF-Export der Anleitung
```

**Zugriff für Gäste (ohne Login):**

Ein Besucher ohne Benutzerkonto ruft einfach die URL auf:
- `http://127.0.0.1:8000/de/anleitungen` — Anleitung-Übersicht
- `http://127.0.0.1:8000/de/anleitungen/{slug}` — Einzelne Anleitung

Von der Startseite `/de/` aus führt der Navigationslink „Anleitungen" direkt zur Übersicht. Kein Login erforderlich.

**Beispiel: Anleitung „Fördermittel-Antrag erstellen"**
```
Titel: Fördermittel-Antrag erstellen
Kategorie: Anträge / Fördermittel
Version: 1.0 (vereinsverwaltung_dev)
Globale Bilder:
  - antrag_uebersicht.png (wird im Header angezeigt)
Schritte:
  1. Login und Navigation zum Anträge-Modul
     Bilder: login_screenshot.png
  2. Neuen Antrag anlegen
     Bilder: neuer_antrag_button.png
  3. Daten ausfüllen
     Bilder: datenformular.png
     Tipp: Alle Pflichtfelder sind mit * markiert
  4. Anhänge hochladen
     Bilder: anhaenge_upload.png
     Warnung: Maximale Dateigröße: 10 MB
  5. Antrag absenden
     Bilder: absenden_button.png
```

### 2.3 Interaktive Tutorials (Eigenes System)

Statt kostenpflichtiger Dienste wie Scribehow.com wird ein **eigenes interaktives Tutorial-System** implementiert, das ähnliche Funktionen bietet: Bildschirm-basierte, klickbare Tutorials mit Screenshots, hervorgehobenen Klick-Bereichen und Schritt-für-Schritt-Navigation.

**Funktionsumfang (ähnlich Scribe):**
- **Tutorial-Aufnahme:** Redakteure erstellen Tutorials durch Screenshots mit markierten Klick-Bereichen (Hotspots)
- **Interaktive Wiedergabe:** Nutzer klicken sich durch die Schritte — jeder Schritt zeigt einen Screenshot mit hervorgehobenen Bereichen
- **Cursor-Positionen:** Mauszeiger und Klick-Positionen werden auf dem Screenshot angezeigt
- **Annotations:** Jeder Schritt kann Text-Erklärungen, Tipps und Warnungen enthalten
- **Fortschrittsanzeige:** Nutzer sehen, wie weit sie im Tutorial sind
- **Abschluss-Zertifikat:** Optionaler Abschluss-Bildschirm nach erfolgreichem Durchlauf

**Aufnahme-Workflow (Admin-Panel):**
1. Redakteur erstellt ein neues Tutorial
2. Für jeden Schritt wird ein Screenshot hochgeladen
3. Auf dem Screenshot werden Klick-Bereiche (Hotspots) markiert — als Koordinaten (x, y) und Radius
4. Optional: Cursor-Position und Maustastentyp (Links-/Rechtsklick) werden gespeichert
5. Text-Erklärung, Titel und Tipps werden hinzugefügt
6. **SEO-Einstellungen konfigurieren** (optional):
   - SEO-Titel eingeben (wird automatisch aus dem Titel generiert, wenn leer gelassen)
   - SEO-Beschreibung eingeben (wird automatisch aus der Kurzbeschreibung generiert, wenn leer gelassen)
   - **Vorschau-Bild für Social Media** hochladen (Datei-Upload, max. 5MB, empfohlen: 1200×630px)
   - Das Bild wird automatisch komprimiert gespeichert (max. 1920px Breite, 80% Qualität)
   - Live-Vorschau des ausgewählten Bilds im Admin-Panel
7. Tutorial wird veröffentlicht

**Vorschau-Bild (og_image):**
Das Vorschau-Bild wird angezeigt, wenn ein Tutorial-Link auf Social-Media-Plattformen (Facebook, WhatsApp, Slack, LinkedIn etc.) geteilt wird. Es wird als OpenGraph-Metatag (`og:image`) und Twitter-Card (`twitter:image`) in der Tutorial-Detailseite eingebettet. Auf der Tutorial-Übersichtsseite (`/de/tutorials`) wird das Vorschau-Bild als Kartenbild angezeigt – falls kein OG-Bild vorhanden ist, wird das Thumbnail oder ein Platzhalter verwendet.

**Wiedergabe-Workflow (Frontend):**
1. Nutzer öffnet ein Tutorial
2. Interaktiver Player zeigt den aktuellen Schritt mit Screenshot
3. Hervorgehobene Hotspots sind klickbar — Nutzer klickt weiter zum nächsten Schritt
4. Alternativ: Vor-/Zurück-Buttons für manuelle Navigation
5. Fortschritt wird gespeichert (für eingeloggte Nutzer)

**Seitenstruktur:**
```
/{locale}/tutorials          — Übersicht aller Tutorials
/{locale}/tutorials/{slug}   — Interaktiver Tutorial-Player
```

**Admin-Panel (nur mit Login):**
```
/admin/tutorials        — Tutorial-Liste (Admin)
/admin/tutorials/create — Tutorial erstellen (Admin)
/admin/tutorials/{id}/edit — Tutorial bearbeiten (Admin)
```

**Zugriff für Gäste (ohne Login):**

Tutorials sind wie Anleitungen öffentlich lesbar:
- `http://127.0.0.1:8000/de/tutorials` — Tutorial-Übersicht
- `http://127.0.0.1:8000/de/tutorials/{slug}` — Tutorial ansehen

Nur Fortschrittsspeicherung erfordert ein Benutzerkonto.

### 2.4 Benutzerkonten

Nutzer können sich registrieren, einloggen und ihr Profil verwalten. Ein Benutzerkonto ist optional: Alle Anleitungen und Tutorials sind auch ohne Konto vollständig lesbar.

**Funktionen:**
- Registrierung mit E-Mail-Verifizierung
- Login/Logout (Laravel  Jetstream)
- Passwort-Zurücksetzen
- Profilbearbeitung (Name, Avatar, Bio)
- Rollenverwaltung:
  - **Gast (ohne Konto)** — Anleitungen lesen, Tutorials ansehen, suchen
  - **Nutzer** — Lesen, kommentieren, Feedback geben, Tutorial-Fortschritt speichern
  - **Redakteur** — Anleitungen erstellen und bearbeiten
  - **Administrator** — Vollzugriff, Benutzerverwaltung

**Was ohne Konto möglich ist:**
- Alle Anleitungen lesen und durchsuchen
- Alle Tutorials ansehen und durchklicken
- Projekt- und Module-Seiten besuchen
- PDF-Export von Anleitungen herunterladen

**Was ein Konto erfordert:**
- Kommentare schreiben
- Bewertungen abgeben
- Feedback einreichen
- Tutorial-Fortschritt speichern
- Admin-Panel nutzen (Redakteur/Admin)

**Datenbank-Modelle:**
```
users
  - id
  - name
  - email (unique, verified)
  - password (hashed)
  - role (enum: guest, user, editor, admin)
  - avatar (nullable)
  - bio (nullable, text)
  - email_verified_at (nullable)
  - remember_token (nullable)
  - created_at, updated_at

password_reset_tokens
  - email (primary)
  - token
  - created_at

sessions
  - id (primary)
  - user_id (foreign, nullable)
  - ip_address
  - user_agent
  - payload (text)
  - last_activity (integer)
```

### 2.5 Kommentare und Feedback

Nutzer können Anleitungen und Tutorials kommentieren und Feedback geben.

**Funktionen:**
- Kommentare unter jeder Anleitung und jedem Tutorial
- Verschachtelte Kommentare (Antworten auf Kommentare)
- Bewertungssystem (Daumen hoch/runter pro Anleitung)
- Feedback-Formular für Fehlermeldungen oder Verbesserungsvorschläge
- Moderation: Administratoren können Kommentare bearbeiten oder löschen

**Datenbank-Modelle:**
```
comments
  - id
  - user_id (foreign, nullable — anonyme Kommentare möglich)
  - commentable_id (polymorphic)
  - commentable_type (polymorphic: Anleitung, Tutorial)
  - parent_id (foreign, nullable — für Antworten)
  - body (text)
  - status (enum: pending, approved, rejected)
  - created_at, updated_at

feedback
  - id
  - user_id (foreign, nullable)
  - feedbackable_id (polymorphic)
  - feedbackable_type (polymorphic: Anleitung, Tutorial, Seite)
  - type (enum: bug, suggestion, praise)
  - subject (string)
  - body (text)
  - status (enum: open, in_progress, resolved, closed)
  - created_at, updated_at

ratings
  - id
  - user_id (foreign)
  - rateable_id (polymorphic)
  - rateable_type (polymorphic: Anleitung, Tutorial)
  - rating (integer, 1–5)
  - created_at, updated_at
```

### 2.6 Mehrsprachigkeit

Alle Inhalte (Anleitungen, Tutorials, Seiten, Kategorien) können in mehreren Sprachen verwaltet werden. Das System verwendet ein **polymorphes Übersetzungsmodell**, das Übersetzungen für alle Inhaltstypen in einer einzigen Tabelle speichert.

**Funktionsumfang:**
- **Standardsprache:** Konfiguriert in `.env` (`APP_LOCALE=de`). Die Standardsprache wird direkt im Hauptmodell gespeichert.
- **Zielsprachen:** Konfiguriert in `config/app.php` (`supported_locales`). Alle Nicht-Standard-Sprachen werden in `content_translations` gespeichert.
- **Admin-Panel:** Zentrale Übersetzungsverwaltung mit Übersichtsseite pro Inhaltstyp
- **Modal-basierte Bearbeitung:** Übersetzungen werden in einem Modal bearbeitet, das alle Zielsprachen gleichzeitig anzeigt
- **KI-Übersetzung:** Optionale automatische Übersetzung einzelner Felder per KI (OpenAI API)
- **Status-Anzeige:** Farbcodierte Anzeige, welche Inhalte bereits übersetzt sind (✓ grün) und welche noch fehlen (⚠ gelb)

**Admin-Panel-Struktur:**
```
/admin/translations           — Übersetzungs-Übersicht (Statistiken pro Typ/Sprache)
/admin/translations/{type}    — Übersetzungen für einen Inhaltstyp (guides, tutorials, pages, categories)
```

**Technische Umsetzung:**
- **Controller:** `AdminTranslationController` mit `show()`, `store()`, `destroy()` Methoden
- **Model:** `ContentTranslation` mit polymorpher Beziehung (`morphTo`/`morphMany`)
- **View:** `admin/translations/show.blade.php` mit Modal und Multiple-Form-Ansatz
- **Eager Loading:** `$modelClass::with('translations')` im Controller
- **Daten-Übertragung:** Übersetzungsdaten werden im Controller als Array berechnet und per `@json()` an die View übergeben
- **Multiple-Form-Ansatz:** Jedes Formular pro Sprache sendet unabhängig, um Datenkonflikte zu vermeiden

**Best Practices:**
- **Keine Closures in Blade:** Vermeide `@json($items->mapWithKeys(function(...)))` in Blade-Views — berechne Arrays im Controller
- **@stack('scripts'):** Stelle sicher, dass das Layout `@stack('scripts')` vor `</body>` enthält, sonst werden `@push('scripts')`-Blöcke nicht gerendert
- **Formular-Isolation:** Verwende separate Formulare pro Sprache mit `form`-Attribut auf Buttons, um korrekten Submit zu garantieren
- **JSON-Daten:** Verwende `@json($variable)` für sichere JSON-Serialisierung von PHP-Arrays an JavaScript

### 2.7 Suchfunktion

Volltextsuche durch alle Anleitungsinhalte, Tutorials und Kommentare.

**Funktionen:**
- Suchfeld in der Navigation (immer sichtbar)
- Suche durchsucht: Titel, Beschreibung, Schritte, Kommentare
- Filter nach Kategorie, Typ (Anleitung/Tutorial), Bewertung
- Suchergebnisseite mit Vorschau und Highlight der Suchbegriffe
- Vorschläge während der Eingabe (Autocomplete)

**Technische Umsetzung:**
- Laravel Scout mit MySQL Full-Text-Index oder Meilisearch (optional)
- Suchindex wird bei Änderungen automatisch aktualisiert
- Paginierte Suchergebnisse

---

## 3. Datenbank-Architektur

Die Webseitevereinsverwaltung verwendet eine **eigene, separate Datenbank**, die unabhängig von der gemeinsamen Datenbank der 5 Hauptprojekte ist. Die Datenbank kann auf einem entfernten Server liegen.

### 3.1 Tabellenübersicht

| Tabelle | Zweck | Zeilen (geschätzt) |
|---|---|---|
| `users` | Benutzerkonten (Laravel Jetstream) | 100–1000 |
| `cache` | Laravel-Cache | — |
| `cache_locks` | Cache-Sperren | — |
| `failed_jobs` | Fehlgeschlagene Jobs | — |
| `jobs` | Warteschlangen-Jobs | — |
| `migrations` | Laravel-Migrations-Tracking | — |
| `personal_access_tokens` | API-Tokens (Laravel Sanctum) | — |
| `passkeys` | Passkeys für 2FA (Jetstream) | — |
| `categories` | Anleitungskategorien | 10–50 |
| `guides` | Anleitungen (Dokumentation) | 50–500 |
| `guide_steps` | Schritte innerhalb einer Anleitung | 200–2000 |
| `guide_images` | Screenshots und Bilder | 500–5000 |
| `tutorials` | Interaktive Tutorials (Meta-Daten) | 20–100 |
| `tutorial_steps` | Schritte eines interaktiven Tutorials | 100–1000 |
| `tutorial_hotspots` | Klick-Bereiche auf Screenshots | 200–2000 |
| `tutorial_progress` | Nutzer-Fortschritt pro Tutorial | 200–5000 |
| `comments` | Kommentare (mit Moderation) | 500–5000 |
| `feedback` | Nutzer-Feedback | 100–1000 |
| `ratings` | Bewertungen (polymorph) | 500–5000 |
| `pages` | Statische Seiten (Marketing) | 10–50 |
| `tags` | Tags für Kategorien | 50–200 |
| `taggables` | Pivot-Tabelle für Tags | — |
| `content_translations` | Inhalt-Übersetzungen (polymorph) | 500–5000 |
| `step_translations` | Schritt-Übersetzungen (Guide + Tutorial) | 500–10000 |
| `projects` | Projektkonfiguration (Multi-Projekt) | 5–50 |
| `project_terms` | Platzhalter-Ersetzungen pro Projekt | 50–500 |
| `guide_project` | Pivot: Guide ↔ Project | — |
| `tutorial_project` | Pivot: Tutorial ↔ Project | — |
| `project_guide_images` | Projekt-spezifische Screenshots (Guide) | 100–2000 |
| `project_tutorial_images` | Projekt-spezifische Screenshots (Tutorial) | 100–2000 |
| `placeholder_definitions` | Globaler Platzhalter-Katalog | ~20 |
| `project_user` | Pivot: Project ↔ User (mit Rollen) | — |

### 3.2 Vollständige Modelldefinitionen

```
categories
  - id
  - name (string, unique)
  - slug (string, unique)
  - description (nullable, text)
  - parent_id (foreign, nullable — für Unter Kategorien)
  - sort_order (integer, default 0)
  - created_at, updated_at

guides
  - id
  - category_id (foreign, nullable)
  - title (string)
  - slug (string, unique)
  - description (text)
  - content (longtext, nullable — Markdown oder HTML)
  - version (string, nullable — z. B. "1.0")
  - project_ref (string, nullable — Verweis auf Hauptprojekt, z. B. "vereinsverwaltung_dev")
  - status (enum: draft, published, archived)
  - author_id (foreign, users.id)
  - published_at (nullable)
  - view_count (integer, default 0)
  - created_at, updated_at

guide_steps
  - id
  - guide_id (foreign)
  - step_number (integer)
  - title (string)
  - description (text)
  - tip (nullable, text — optionaler Tipp/Hinweis)
  - warning (nullable, text — optionale Warnung)
  - created_at, updated_at

guide_images
  - id
  - guide_step_id (foreign, nullable — wenn gesetzt, gehört das Bild zu einem spezifischen Schritt)
  - guide_id (foreign — Verweis auf die Anleitung)
  - path (string — Speicherpfad des Bildes im Storage-Verzeichnis)
  - caption (nullable, string — optionale Bildunterschrift)
  - alt_text (nullable, string — alternativer Text für Barrierefreiheit)
  - sort_order (integer, default 0 — Reihenfolge bei mehreren Bildern)
  - created_at, updated_at

**Bild-Zuordnung:**
- **Schritt-Bilder:** `guide_step_id` ist gesetzt → Bild wird bei diesem Schritt angezeigt
- **Globale Bilder:** `guide_step_id` ist NULL → Bild wird im Header-Bereich der Anleitung angezeigt

tutorials
  - id
  - category_id (foreign, nullable)
  - title (string)
  - slug (string, unique)
  - description (text)
  - thumbnail_path (nullable, string — Vorschaubild)
  - estimated_duration_minutes (nullable, integer)
  - status (enum: draft, published, archived)
  - author_id (foreign, users.id)
  - view_count (integer, default 0)
  - completion_count (integer, default 0 — wie oft abgeschlossen)
  - meta_title (nullable, string — SEO-Titel, optional; wird automatisch aus dem Titel generiert)
  - meta_description (nullable, text — SEO-Beschreibung, optional; wird automatisch aus der Kurzbeschreibung generiert)
  - og_image (nullable, string — Vorschau-Bild für Social Media; Datei-Upload mit Komprimierung)
  - created_at, updated_at

tutorial_steps
  - id
  - tutorial_id (foreign)
  - step_number (integer)
  - title (string — z. B. "Klicken Sie auf 'Neuer Antrag'")
  - instruction (text — Erklärung, was der Nutzer tun soll)
  - screenshot_path (string — Pfad zum Screenshot dieses Schritts)
  - screenshot_width (integer — Breite des Screenshots in Pixel)
  - screenshot_height (integer — Höhe des Screenshots in Pixel)
  - tip (nullable, text — optionaler Tipp)
  - warning (nullable, text — optionale Warnung)
  - auto_advance (boolean, default false — automatisch weiter zum nächsten Schritt)
  - auto_advance_delay_ms (integer, default 2000 — Verzögerung bei Auto-Advance)
  - created_at, updated_at

tutorial_hotspots
  - id
  - tutorial_step_id (foreign)
  - hotspot_type (enum: click, hover, drag, type, wait)
  - position_x (integer — X-Koordinate auf dem Screenshot)
  - position_y (integer — Y-Koordinate auf dem Screenshot)
  - radius (integer, default 20 — Radius des hervorgehobenen Bereichs in Pixel)
  - label (nullable, string — z. B. "Hier klicken")
  - cursor_type (enum: arrow, pointer, hand, text, context_menu, default 'pointer')
  - click_count (integer, default 1 — wie oft geklickt werden soll)
  - is_clickable (boolean, default true — ob der Hotspot den nächsten Schritt auslöst)
  - sort_order (integer, default 0 — Reihenfolge bei mehreren Hotspots pro Schritt)
  - created_at, updated_at

tutorial_progress
  - id
  - user_id (foreign)
  - tutorial_id (foreign)
  - current_step_id (foreign, nullable — aktuell erreichter Schritt)
  - completed (boolean, default false)
  - completed_at (nullable)
  - started_at (nullable)
  - last_visited_at (nullable)
  - created_at, updated_at

pages
  - id
  - title (string)
  - slug (string, unique)
  - content (longtext)
  - meta_description (nullable, text)
  - meta_keywords (nullable, text)
  - status (enum: draft, published)
  - author_id (foreign, users.id)
  - sort_order (integer, default 0)
  - created_at, updated_at

tags
  - id
  - name (string, unique)
  - slug (string, unique)
  - created_at, updated_at

taggables
  - tag_id (foreign)
  - taggable_id (polymorphic)
  - taggable_type (polymorphic: Guide, Tutorial, Page)

content_translations
  - id
  - translatable_type (string — polymorph: Guide, Tutorial, Page, Category)
  - translatable_id (integer — Verweis auf das übersetzbare Modell)
  - locale (string, z. B. "en", "fr")
  - title (string, nullable)
  - description (text, nullable)
  - content (longtext, nullable)
  - meta_description (text, nullable)
  - meta_keywords (text, nullable)
  - created_at, updated_at

**Übersetzungs-Architektur:**
- **Polymorphes Design:** Eine Tabelle speichert Übersetzungen für alle Inhaltstypen
- **Standardsprache:** Die Standardsprache (konfiguriert in `.env`, z. B. `de`) wird direkt im Hauptmodell gespeichert
- **Zielsprachen:** Alle Nicht-Standard-Sprachen werden in `content_translations` gespeichert
- **Eager Loading:** Übersetzungen sollten immer mit `with('translations')` geladen werden, um N+1-Abfragen zu vermeiden
- **UpdateOrCreate:** Beim Speichern wird `updateOrCreate()` verwendet, um bestehende Übersetzungen zu aktualisieren oder neue zu erstellen

**Tutorial-Modell – Wichtige Methoden:**
- `getRouteKeyName()`: Gibt `'slug'` zurück — Route Model Binding verwendet standardmäßig den Slug statt der ID
- `Searchable` Trait: Ermöglicht Volltextsuche via Laravel Scout
- `projects()`: belongsToMany-Beziehung zum Multi-Projekt-System
- `scopeForProject()`: Filtert Tutorials nach Projekt-Zuweisung
```

### 3.4 Route Model Binding

Da das Tutorial-Modell `getRouteKeyName() = 'slug'` verwendet, bindet Laravel bei allen Routen mit `{tutorial}` automatisch nach dem Slug statt nach der ID.

**Problem:** Routen, die explizit die ID benötigen (z. B. Screenshot-Uploads, Schritt-Übersetzungen), müssen mit `{tutorial:id}` deklariert werden, um das Model Binding nach ID zu erzwingen.

**Betroffene Routen (mit `{tutorial:id}`):**
- `POST /tutorials/{tutorial:id}/screenshot` — Screenshot-Upload
- `DELETE /tutorials/{tutorial:id}/steps/{step}/screenshot` — Screenshot löschen
- `POST /tutorials/{tutorial:id}/steps/{stepId}/translations` — Schritt-Übersetzung speichern
- `DELETE /tutorials/{tutorial:id}/steps/{stepId}/translations/{locale}` — Schritt-Übersetzung löschen
- `DELETE /tutorials/{tutorial:id}/og-image` — OG-Bild (Vorschau-Bild für Social Media) löschen

**Controller-Anpassung:** Die entsprechenden Controller-Methoden verwenden statt `Tutorial $tutorial` (automatisches Model Binding) manuelles `Tutorial::findOrFail($tutorialId)`, da das automatische Binding über den Slug erfolgen würde.

### 3.3 Beziehungen

```
Category hasMany Guide
Category hasMany Tutorial
Category belongsTo Category (parent_id — selbstreferenzierend)

Guide belongsTo Category
Guide belongsTo User (author)
Guide hasMany GuideStep
Guide hasMany GuideImage (über guide_id)
Guide belongsToMany Tag (via taggables)
Guide morphMany Comment (commentable)
Guide morphMany Rating (rateable)
Guide morphMany Feedback (feedbackable)

GuideStep belongsTo Guide
GuideStep hasMany GuideImage

Tutorial belongsTo Category
Tutorial belongsTo User (author)
Tutorial hasMany TutorialStep
Tutorial belongsToMany Tag (via taggables)
Tutorial morphMany Comment (commentable)
Tutorial morphMany Rating (rateable)
Tutorial morphMany Feedback (feedbackable)
Tutorial hasMany TutorialProgress

TutorialStep belongsTo Tutorial
TutorialStep hasMany TutorialHotspot

TutorialHotspot belongsTo TutorialStep

TutorialProgress belongsTo User
TutorialProgress belongsTo Tutorial
TutorialProgress belongsTo TutorialStep (current_step)

Page belongsTo User (author)
Page belongsToMany Tag (via taggables)

User hasMany Guide (author)
User hasMany Tutorial (author)
User hasMany Page (author)
User hasMany Comment
User hasMany Feedback
User hasMany Rating

Guide morphMany ContentTranslation (translatable)
Tutorial morphMany ContentTranslation (translatable)
Page morphMany ContentTranslation (translatable)
Category morphMany ContentTranslation (translatable)

ContentTranslation morphTo translatable
```

---

## 4. Frontend-Architektur

### 4.1 Layout-Struktur

```
resources/views/
├── layouts/
│   ├── app.blade.php          — Hauptlayout (Navigation, Footer)
│   ├── guest.blade.php        — Gast-Layout (Login, Registrierung)
│   └── admin.blade.php        — Admin-Panel-Layout
├── pages/
│   ├── welcome.blade.php      — Startseite
│   ├── project.blade.php      — Projektbeschreibung
│   └── modules/
│       ├── index.blade.php    — Module-Übersicht
│       └── show.blade.php     — Modul-Detailseite
├── guides/
│   ├── index.blade.php        — Anleitung-Übersicht
│   ├── category.blade.php     — Anleitungen nach Kategorie
│   └── show.blade.php         — Einzelne Anleitung
├── tutorials/
│   ├── index.blade.php        — Tutorial-Übersicht
│   └── show.blade.php         — Interaktiver Tutorial-Player
├── admin/
│   ├── guides/
│   │   ├── index.blade.php    — Anleitung-Liste
│   │   ├── create.blade.php   — Anleitung erstellen
│   │   └── edit.blade.php     — Anleitung bearbeiten (mit Schritten und Bildern)
│   ├── tutorials/
│   │   ├── index.blade.php    — Tutorial-Liste
│   │   ├── create.blade.php   — Tutorial erstellen
│   │   ├── edit.blade.php     — Tutorial bearbeiten
│   │   └── steps/
│   │       ├── edit.blade.php     — Schritt bearbeiten
│   │       └── hotspots.blade.php — Hotspots auf Screenshot markieren
│   ├── translations/
│   │   ├── index.blade.php    — Übersetzungs-Übersicht (Statistiken)
│   │   └── show.blade.php     — Übersetzungen pro Inhaltstyp (Modal-basiert)
├── search/
│   └── results.blade.php      — Suchergebnisse
├── components/
│   ├── guide-card.blade.php   — Anleitungskarte (Vorschau)
│   ├── tutorial-card.blade.php — Tutorial-Karte
│   ├── tutorial-player.blade.php — Interaktiver Tutorial-Player (Frontend)
│   ├── hotspot-marker.blade.php  — Hotspot-Markierung auf Screenshot
│   ├── progress-bar.blade.php    — Fortschrittsanzeige
│   ├── comment-form.blade.php — Kommentarformular
│   ├── comment-list.blade.php — Kommentarliste
│   ├── rating-stars.blade.php — Bewertungssterne
│   ├── feedback-form.blade.php — Feedback-Formular
│   └── search-bar.blade.php   — Suchleiste
└── auth/
    ├── login.blade.php
    ├── register.blade.php
    ├── verify-email.blade.php
    └── passwords/
        ├── forgot.blade.php
        └── reset.blade.php
```

### 4.2 Navigation

```
Hauptnavigation:
  - Startseite
  - Projekt
  - Module
  - Anleitung (Dropdown nach Kategorien)
  - Tutorials
  - Suche (Suchfeld)
  - Login / Registrieren (oder Benutzer-Menü)

Footer:
  - Über das Projekt
  - Impressum
  - Datenschutz
  - Kontakt
  - Social Media Links
```

### 4.3 Design-Richtlinien

- **Framework:** Tailwind CSS (konsistent mit Laravel-Breeze/Jetstream)
- **Farbschema:** Zu definieren — sollte zu den Hauptprojekten passen
- **Typografie:** Klare, lesbare Schrift für Dokumentation
- **Responsiv:** Mobile-first, alle Geräte unterstützt
- **Barrierefreiheit:** ARIA-Labels, Kontrastverhältnis, Tastaturnavigation

### 4.4 Guide-Editor (Admin-Panel)

Der Guide-Editor (`/admin/guides/{id}/edit`) ermöglicht das Erstellen und Bearbeiten von Anleitungen mit Schritten und Bildern.

**Funktionen:**
- **Schritt-Verwaltung:** Schritte können dynamisch hinzugefügt, entfernt und verschoben werden (JavaScript-basiert)
  - **Verschieben:** ↑/↓-Buttons tauschen benachbarte Schritte und aktualisieren alle Formularfeld-Namen, `data-step`-Attribute, IDs und KI-Button-Handler lückenlos neu
- **Schritt-Bilder:** Jeder Schritt hat einen eigenen Bild-Upload-Bereich mit AJAX-Upload
- **Globale Bilder:** Bilder auf Anleitungsebene werden im Header-Bereich angezeigt
- **Mehrsprachigkeit:** Schritt-Titel, -Beschreibungen, -Tipps und -Warnungen können übersetzt werden
- **Speichern:** Änderungen werden mit einem "Änderungen speichern"-Button am Ende der Seite gespeichert

**Bild-Upload-Workflow:**
1. **Schritt-Bilder:**
   - Bild auswählen (Datei-Input im Schritt)
   - Optional: Beschriftung und Alt-Text eingeben
   - "Bild hochladen"-Button klicken → AJAX-Upload an `admin.guides.images.step.store`
   - Bei Erfolg: Seite wird neu geladen, Bild erscheint beim Schritt
   - CSRF-Schutz über Meta-Tag `name="csrf-token"`
   
2. **Globale Bilder:**
   - Bild auswählen im globalen Bild-Upload-Bereich
   - Formular wird per normaler POST-Anfrage gesendet
   - Bild wird ohne Schritt-Zuordnung gespeichert (`guide_step_id = NULL`)

**Bild-Löschung:**
- Bilder können per AJAX gelöscht werden (kein Formular-Submit)
- Bestätigungsdialog vor dem Löschen
- Nach erfolgreichem Löschen: Seite wird neu geladen

**Schritt-Update-Logik:**
- Bestehende Schritte werden aktualisiert (nicht gelöscht und neu erstellt)
- Neue Schritte (ohne ID) werden erstellt
- Entfernte Schritte werden gelöscht
- Schritt-IDs werden als Hidden-Fields übermittelt, um bestehende Schritte zu erkennen

### 4.5 Tutorial-Editor (Admin-Panel)

Der Tutorial-Editor (`/admin/tutorials/create` und `/admin/tutorials/{id}/edit`) ermöglicht das Erstellen und Bearbeiten von interaktiven Tutorials mit SEO-Einstellungen.

**Funktionen:**
- **Basisdaten:** Titel, Beschreibung, Kategorie, Status, geschätzte Dauer
- **Schritt-Verwaltung:** Schritte können dynamisch hinzugefügt, entfernt und verschoben werden
  - **Verschieben:** ↑/↓-Buttons tauschen benachbarte Schritte und aktualisieren alle Formularfeld-Namen, `data-step`-Attribute, IDs und KI-Button-Handler lückenlos neu
- **Screenshot-Upload:** Jeder Schritt hat einen eigenen Bild-Upload-Bereich mit AJAX-Upload
- **Hotspot-Markierung:** Klick-Bereiche auf Screenshots markieren (Canvas-basiert)
- **SEO-Einstellungen** (optionaler Kollaps-Bereich):
  - SEO-Titel (wird automatisch aus dem Titel generiert, wenn leer gelassen)
  - SEO-Beschreibung (wird automatisch aus der Kurzbeschreibung generiert, wenn leer gelassen)
  - **Vorschau-Bild für Social Media** (Datei-Upload, `<input type="file">`)
  - Live-Vorschau des aktuellen OG-Bilds (wenn bereits vorhanden)
  - Live-Vorschau beim Datei-Auswahl (JavaScript FileReader API)
  - Beschreibung: "Wird angezeigt, wenn der Tutorial-Link auf Facebook, WhatsApp, Slack etc. geteilt wird"
  - Empfohlene Bildgröße: 1200×630px, max. 5MB

**OG-Bild-Upload-Workflow:**
1. Formular mit `enctype="multipart/form-data"` für Datei-Uploads
2. OG-Bild wird zusammen mit dem Tutorial gespeichert (POST/PUT-Anfrage)
3. Serverseitig: Bild wird mit Intervention Image komprimiert (max. 1920px Breite, 80% Qualität)
4. Gespeichert in `storage/app/public/tutorials/{id}/{uuid}.{extension}`
5. Pfad wird in der Datenbank (`og_image`-Spalte) gespeichert
6. Bei Update: Altes OG-Bild wird automatisch gelöscht, bevor das neue gespeichert wird
7. Fehlerbehandlung: Bei Upload-Fehlern wird eine benutzerfreundliche Meldung angezeigt

**OG-Bild-Lösch-Workflow:**
Das OG-Bild kann direkt im Admin-Panel gelöscht werden — analog zum Löschen von Schritt-Screenshots:
1. Bei vorhandenem OG-Bild erscheint eine Vorschau mit einem ✕-Button (roter Kreis, oben rechts)
2. Klick auf ✕ öffnet einen Bestätigungsdialog: "Vorschau-Bild wirklich löschen?"
3. Bei Bestätigung wird ein AJAX-DELETE-Request an `/admin/tutorials/{id}/og-image` gesendet
4. Serverseitig: Bilddatei wird aus dem Storage gelöscht, `og_image`-Spalte auf `NULL` gesetzt
5. Bei Erfolg wird die Seite neu geladen, das Bild ist entfernt
6. Die Route verwendet `{tutorial:id}` für ID-basiertes Model Binding (wegen `getRouteKeyName() = 'slug'`)

**Tutorial-Übersicht (Frontend):**
- `/de/tutorials` zeigt alle Tutorials als Kartenansicht
- Jede Karte zeigt ein Vorschau-Bild mit Priorität: `og_image` → `thumbnail_path` → Platzhalter
- OG-Bilder werden über `asset('storage/' . $tutorial->og_image)` korrekt aufgelöst
- Bilder bleiben beim richtigen Schritt, weil die Schritt-ID erhalten bleibt

**Technische Details:**
- AJAX-Upload verwendet `fetch()` mit `FormData` und CSRF-Token
- JavaScript-Funktion `uploadStepImage(stepId)` sammelt Bild, Beschriftung und Alt-Text
- JavaScript-Funktion `saveStepTranslation(stepId, locale, url)` für Übersetzungen
- "Schritt hinzufügen"-Button ist am Ende der Schritt-Liste platziert (nicht oben)

---

## 5. Backend-Architektur

### 5.1 Model-Struktur

```
app/Models/
├── User.php
├── Category.php
├── Guide.php
├── GuideStep.php
├── GuideStepTranslation.php  — Schritt-Übersetzungen (polymorph)
├── GuideImage.php
├── Tutorial.php
├── TutorialStep.php
├── TutorialStepTranslation.php — Schritt-Übersetzungen (polymorph)
├── TutorialHotspot.php
├── TutorialProgress.php
├── Page.php
├── Comment.php
├── Feedback.php
├── Rating.php
├── Tag.php
├── ContentTranslation.php    — Inhalt-Übersetzungen (polymorph)
├── Project.php               — Projektkonfiguration (Multi-Projekt-System)
├── ProjectTerm.php           — Platzhalter-Ersetzung pro Projekt
├── ProjectGuideImage.php     — Projekt-spezifische Screenshots (Guide)
├── ProjectTutorialImage.php  — Projekt-spezifische Screenshots (Tutorial)
├── PlaceholderDefinition.php — Globaler Platzhalter-Katalog
├── GuideProject.php          — Pivot: Guide ↔ Project
└── TutorialProject.php       — Pivot: Tutorial ↔ Project
```

### 5.2 Controller-Struktur

```
app/Http/Controllers/
├── Controller.php                  — Basisklasse
├── GuideController.php             — Anleitungen (öffentlich)
├── TutorialController.php          — Tutorials (öffentlich, inkl. Fortschritt)
├── PageController.php              — Statische Seiten (öffentlich)
├── CommentController.php           — Kommentare (öffentlich + Admin-Moderation)
├── SearchController.php            — Suchfunktion (öffentlich)
└── HealthController.php            — Health Check Endpunkte (Monitoring)

app/Http/Controllers/Admin/
├── AdminCategoryController.php     — Kategorien verwalten (CRUD)
├── AdminGuideController.php        — Anleitungen verwalten (CRUD)
├── AdminTutorialController.php     — Tutorials verwalten (CRUD, OG-Bild)
├── AdminPageController.php         — Seiten verwalten (CRUD)
├── AdminImageController.php        — Bild-Upload und -Verwaltung
├── AdminTranslationController.php  — Übersetzungen verwalten (polymorph)
├── AdminStepTranslationController.php — Schritt-Übersetzungen (AJAX)
├── AdminAIController.php           — KI-Beschreibung/Übersetzung/Inhalt generieren
├── AdminStatisticsController.php   — Dashboard-Statistiken
├── AdminProjectController.php      — Multi-Projekt-Verwaltung (CRUD, Begriffe,
│                                   Inhaltsfreigabe, Screenshots, User-Zuweisungen)
```

**AdminTranslationController:**
- `index()`: Zeigt Statistiken pro Inhaltstyp und Sprache (Anzahl übersetzte Items)
- `show($type)`: Zeigt alle Items eines Typs mit Übersetzungs-Status (guides, tutorials, pages, categories)
- `store($request, $type, $id)`: Speichert/aktualisiert Übersetzung via `updateOrCreate()`
- `destroy($type, $id, $locale)`: Löscht spezifische Übersetzung
- **Eager Loading:** `$modelClass::with('translations')` vermeidet N+1-Abfragen
- **Daten-Berechnung:** Übersetzungsdaten und Sprachen-Liste werden im Controller als Arrays berechnet und an die View übergeben

**AdminStepTranslationController:**
- `storeGuideStep()`: Speichert/aktualisiert Schritt-Übersetzungen für Guide-Schritte (AJAX)
- `destroyGuideStep()`: Löscht spezifische Schritt-Übersetzung
- `storeTutorialStep()`: Speichert/aktualisiert Schritt-Übersetzungen für Tutorial-Schritte (AJAX)
- `destroyTutorialStep()`: Löscht spezifische Schritt-Übersetzung

**AdminAIController:**
- `generateDescription()`: Generiert Beschreibung per KI (OpenAI API)
- `generateTranslation()`: Generiert Übersetzung einzelner Felder per KI
- `generateContent()`: Generiert vollständigen Anleitungsentwurf per KI

**AdminImageController:**
- `storeGuideImage()`: Speichert Bilder für Anleitungen, entweder global oder einem Schritt zugeordnet (`step_id`)
- `storeGuideGlobalImage()`: Speichert globale Bilder ohne Schritt-Zuordnung
- `destroyGuideImage()`: Löscht Guide-Bilder
- `storeGuideScreenshot()`: Speichert Guide-Screenshot (Header-Bild)
- `storeTutorialScreenshot()`: Speichert Screenshots für Tutorial-Schritte (mit `{tutorial:id}` Route Model Binding)
- `destroyTutorialScreenshot()`: Löscht Screenshots von Tutorial-Schritten
- Unterstützt AJAX-Anfragen mit JSON-Antwort und Redirect-Optionen
- Validiert `step_id`, `guide_id`, `image`, `caption`, `alt_text`

**AdminTutorialController:**
- `store()`: Erstellt neues Tutorial mit SEO-Feldern (meta_title, meta_description, og_image)
- `update()`: Aktualisiert Tutorial; OG-Bild wird bei Upload komprimiert gespeichert (Intervention Image, max. 1920px Breite, 80% Qualität)
- `destroyOgImage()`: Löscht das OG-Bild eines Tutorials — Bilddatei aus Storage entfernen, `og_image` auf `NULL` setzen, Cache leeren. Unterstützt AJAX-Anfragen (JSON-Antwort) und normale Redirects. Route verwendet `{tutorial:id}` für ID-basiertes Model Binding.
- `compressAndStore()`: Komprimiert hochgeladene Bilder und speichert sie im Storage-Verzeichnis (`tutorials/{id}/`)
- OG-Bild-Validierung: `nullable|image|max:5120` (max. 5MB, alle Bildformate)
- Altes OG-Bild wird beim Update automatisch gelöscht
- Fehlerbehandlung mit try-catch und benutzerfreundlichen Fehlermeldungen
- SEO-Titel und -Beschreibung werden automatisch aus Titel/Beschreibung generiert, wenn leer gelassen

**AdminProjectController:**
- `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`: Projekt-CRUD
- `updateTerms()`: Aktualisiert projekt-spezifische Begriffe (Platzhalter-Ersetzungen)
- `importTerms()`: Importiert Begriffe aus Language Files
- `suggestTerms()`: Liefert Begriffs-Vorschläge aus Hauptprojekten (JSON)
- `exportTermsJson()` / `exportTermsCsv()`: Exportiert Begriffe als JSON/CSV
- `importTermsJson()` / `importTermsCsv()`: Importiert Begriffe aus JSON/CSV-Dateien
- `assignGuides()` / `assignTutorials()`: Inhaltsfreigabe (Opt-in Zuweisung)
- `getGuideScreenshots()` / `getTutorialScreenshots()`: Liefert Guide/Tutorial-Schritte mit Screenshot-Status (JSON)
- `storeGuideScreenshot()` / `storeTutorialScreenshot()`: Speichert projekt-spezifische Screenshots
- `destroyGuideScreenshot()` / `destroyTutorialScreenshot()`: Löscht projekt-spezifische Screenshots
- `assignUsers()`: Weist Users Projekten zu (mit Rollen)
- `removeUser()`: Entfernt User aus Projekt

**CommentController:**
- `store()`: Speichert Kommentar (authentifizierte Benutzer)
- `destroy()`: Löscht Kommentar
- `index()`: Admin-Übersicht aller Kommentare
- `approve()`: Genehmigt Kommentar
- `reject()`: Ablehnt Kommentar

**HealthController:**
- `check()`: Detaillierter Health Check (Datenbank, Cache, Queue)
- `ping()`: Einfacher Ping-Endpunkt für Load Balancer

### 5.3 Middleware

```
app/Http/Middleware/
├── SetLocale.php             — Setzt die App-Sprache basierend auf dem Route-Parameter {locale}
├── SetProject.php            — Erkennt und setzt aktuelles Projekt (Multi-Projekt-System)
│                             — Verwendet ProjectResolver: Subdomain → Query-Param → Domain
└── SetCacheHeaders.php       — Setzt Cache-Header für öffentliche Seiten (Caching-Strategie)
```

**SetProject Middleware:**
- Wird bei allen öffentlichen Routen mit `{locale}`-Präfix angewendet
- Erkennt das aktuelle Projekt via `ProjectResolver::resolveFromRequest()`
- Speichert das erkannte Projekt als Request-Attribut (`$request->attributes->set('project')`)
- Stellt das Projekt über den Helper `currentProject()` global verfügbar
- Reservierte Subdomains (`www`, `api`, `admin`, `mail`, `ftp`, `smtp`, `pop`, `imap`, `webmail`, `cdn`, `static`) werden ignoriert

**SetLocale Middleware:**
- Extrahiert `{locale}` aus der Route und setzt `App::setLocale()`
- Validiert gegen `config('app.supported_locales')`
- Fallback auf `APP_LOCALE` aus `.env`

**SetCacheHeaders Middleware:**
- Setzt `Cache-Control`-Header für öffentliche Seiten
- Ermöglicht effizientes Browser-Caching und CDN-Caching

### 5.4 Routes

```php
// ============================================
// Öffentliche Routen
// ============================================

// Startseite (ohne Locale-Präfix – Sprachauswahl)
Route::get('/', function () {
    return view('language-select');
})->name('home');

// Öffentliche Routen mit Sprach-Präfix
Route::prefix('{locale}')->where(['locale' => implode('|', $locales)])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home.locale');

    // Anleitungen
    Route::get('/anleitungen', [GuideController::class, 'index'])->name('guides.index');
    Route::get('/anleitungen/{slug}/pdf', [GuideController::class, 'pdf'])->name('guides.pdf');
    Route::get('/anleitungen/{slug}', [GuideController::class, 'show'])->name('guides.show');

    // Tutorials
    Route::get('/tutorials', [TutorialController::class, 'index'])->name('tutorials.index');
    Route::get('/tutorials/{slug}', [TutorialController::class, 'show'])->name('tutorials.show');

    // Statische Seiten
    Route::get('/seite/{slug}', [PageController::class, 'show'])->name('pages.show');

    // Suche
    Route::get('/suche', [SearchController::class, 'search'])->name('search');

    // Kommentare (authentifizierte Benutzer)
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::post('/anleitungen/{guide:slug}/kommentare', [CommentController::class, 'store'])
            ->name('guides.comments.store');
        Route::delete('/kommentare/{comment}', [CommentController::class, 'destroy'])
            ->name('comments.destroy');
    });

    // Tutorial-Fortschritt (authentifizierte Benutzer)
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::post('/tutorials/{slug}/progress', [TutorialController::class, 'updateProgress'])
            ->name('tutorials.progress');
        Route::post('/tutorials/{slug}/complete', [TutorialController::class, 'complete'])
            ->name('tutorials.complete');
    });
});

// Dashboard (Jetstream Login-Redirect Ziel)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// ============================================
// Admin-Panel (geschützt, ohne Sprach-Präfix)
// ============================================

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('admin')->name('admin.')->group(function () {

    // Dashboard mit Statistiken
    Route::get('/dashboard', [AdminStatisticsController::class, 'index'])->name('dashboard');

    // CRUD-Ressourcen
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('guides', AdminGuideController::class);
    Route::resource('tutorials', AdminTutorialController::class);
    Route::resource('pages', AdminPageController::class);

    // Bild-Upload
    Route::post('guides/{guide:id}/images/step', [AdminImageController::class, 'storeGuideImage']);
    Route::post('guides/{guide:id}/images', [AdminImageController::class, 'storeGuideGlobalImage']);
    Route::delete('guide-images/{image}', [AdminImageController::class, 'destroyGuideImage']);
    Route::post('guides/{guide:id}/screenshot', [AdminImageController::class, 'storeGuideScreenshot']);
    Route::post('tutorials/{tutorial:id}/screenshot', [AdminImageController::class, 'storeTutorialScreenshot']);
    Route::delete('tutorials/{tutorial:id}/steps/{step}/screenshot', [AdminImageController::class, 'destroyTutorialScreenshot']);
    Route::delete('tutorials/{tutorial:id}/og-image', [AdminTutorialController::class, 'destroyOgImage']);

    // Kommentar-Moderation
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');

    // Übersetzungs-Verwaltung
    Route::get('/translations', [AdminTranslationController::class, 'index'])->name('translations.index');
    Route::get('/translations/{type}', [AdminTranslationController::class, 'show'])->name('translations.show');
    Route::post('/translations/{type}/{id}/store', [AdminTranslationController::class, 'store'])->name('translations.store');
    Route::delete('/translations/{type}/{id}/{locale}/destroy', [AdminTranslationController::class, 'destroy'])->name('translations.destroy');

    // Schritt-Übersetzungen (AJAX)
    Route::post('guides/{guide}/steps/{stepId}/translations', [AdminStepTranslationController::class, 'storeGuideStep']);
    Route::delete('guides/{guide}/steps/{stepId}/translations/{locale}', [AdminStepTranslationController::class, 'destroyGuideStep']);
    Route::post('tutorials/{tutorial:id}/steps/{stepId}/translations', [AdminStepTranslationController::class, 'storeTutorialStep']);
    Route::delete('tutorials/{tutorial:id}/steps/{stepId}/translations/{locale}', [AdminStepTranslationController::class, 'destroyTutorialStep']);

    // KI-Integration (OpenAI API)
    Route::post('/ai/generate-description', [AdminAIController::class, 'generateDescription']);
    Route::post('/ai/generate-translation', [AdminAIController::class, 'generateTranslation']);
    Route::post('/ai/generate-content', [AdminAIController::class, 'generateContent']);

    // Multi-Projekt: Projekte verwalten
    Route::resource('projects', AdminProjectController::class);
    Route::patch('projects/{project}/terms', [AdminProjectController::class, 'updateTerms']);
    Route::post('projects/{project}/terms/import', [AdminProjectController::class, 'importTerms']);
    Route::get('projects/{project}/terms/suggest', [AdminProjectController::class, 'suggestTerms']);
    Route::get('projects/{project}/terms/export/json', [AdminProjectController::class, 'exportTermsJson']);
    Route::get('projects/{project}/terms/export/csv', [AdminProjectController::class, 'exportTermsCsv']);
    Route::post('projects/{project}/terms/import/json', [AdminProjectController::class, 'importTermsJson']);
    Route::post('projects/{project}/terms/import/csv', [AdminProjectController::class, 'importTermsCsv']);
    Route::post('projects/{project}/guides', [AdminProjectController::class, 'assignGuides']);
    Route::post('projects/{project}/tutorials', [AdminProjectController::class, 'assignTutorials']);

    // Projekt-spezifische Screenshots
    Route::get('projects/{project}/guide-screenshots', [AdminProjectController::class, 'getGuideScreenshots']);
    Route::get('projects/{project}/tutorial-screenshots', [AdminProjectController::class, 'getTutorialScreenshots']);
    Route::post('projects/{project}/guide-screenshots', [AdminProjectController::class, 'storeGuideScreenshot']);
    Route::post('projects/{project}/tutorial-screenshots', [AdminProjectController::class, 'storeTutorialScreenshot']);
    Route::delete('projects/{project}/guide-screenshots/{guideStepId}', [AdminProjectController::class, 'destroyGuideScreenshot']);
    Route::delete('projects/{project}/tutorial-screenshots/{tutorialStepId}', [AdminProjectController::class, 'destroyTutorialScreenshot']);

    // Projekt-User-Zuweisungen
    Route::post('projects/{project}/users', [AdminProjectController::class, 'assignUsers']);
    Route::delete('projects/{project}/users/{user}', [AdminProjectController::class, 'removeUser']);
});

// ============================================
// API-Endpunkte (öffentlich, JSON)
// ============================================

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/guides', [ApiController::class, 'guides']);
    Route::get('/guides/{guide:slug}', [ApiController::class, 'guideShow']);
    Route::get('/tutorials', [ApiController::class, 'tutorials']);
    Route::get('/tutorials/{tutorial:slug}', [ApiController::class, 'tutorialShow']);
    Route::get('/categories', [ApiController::class, 'categories']);
    Route::get('/search', [ApiController::class, 'search']);
});

// Health Check (Monitoring, Load Balancer)
Route::get('/health', [HealthController::class, 'check'])->name('health.check');
Route::get('/ping', [HealthController::class, 'ping'])->name('health.ping');
```

**Wichtige Route-Muster:**
- `{guide:id}` und `{tutorial:id}` erzwingen ID-basiertes Model Binding (wegen `getRouteKeyName() = 'slug'`)
- `{guide:slug}` und `{tutorial:slug}` verwenden Slug-basiertes Model Binding
- Admin-Routes verwenden Jetstream-Auth-Middleware (`auth:sanctum`, `verified`)
- Öffentliche Routen mit `{locale}`-Präfix unterstützen Mehrsprachigkeit
- API-Endpunkte sind öffentlich und liefern JSON-Antworten mit Projekt-Filter

---

## 6. Implementierungsplan

### Phase 1: Grundgerüst (Abgeschlossen ✓)

**Ziel:** Laravel-Projekt mit Basis-Struktur und Authentifizierung

- [x] Laravel-Installation in `Webseitevereinsverwaltung/`
- [x] Datenbank-Konfiguration (`.env` für entfernten Server vorbereiten)
- [x] Laravel Jetstream für Authentifizierung (mit Teams, 2FA, Passkeys)
- [x] Basis-Layout mit Navigation und Footer (Tailwind CSS)
- [x] Rollen-Middleware implementieren
- [x] Migrationen für Guide-Tabellen erstellen (guides, guide_steps, guide_images)
- [x] Migrationen für Tutorial-Tabellen erstellen (tutorials, tutorial_steps, tutorial_hotspots)
- [x] Migrationen für alle restlichen Tabellen (comments, feedback, ratings, etc.)
- [x] Seeder für Demo-Kategorien und Admin-Benutzer (DefaultSeeder)

### Phase 2: Inhaltverwaltung (Abgeschlossen ✓)

**Ziel:** CRUD für Anleitungen, Tutorials und Seiten

- [x] Model-Definitionen mit Beziehungen (Guide, GuideStep, GuideImage)
- [x] Admin-Panel für Guide-CRUD (Erstellen, Bearbeiten, Löschen)
- [x] Schritt-Verwaltung in Anleitungen (dynamisch hinzufügen/entfernen/verschieben)
  - [x] Schritte verschieben mit ↑/↓-Buttons (DOM-Tausch + `updateStepNumbers()`)
- [x] Schritt-Update-Logik (bestehende Schritte aktualisieren, nicht löschen/neu erstellen)
- [x] Bild-Upload für Screenshots (Storage-Verzeichnis)
- [x] Schritt-Bilder (Bilder pro Schritt mit AJAX-Upload)
- [x] Globale Bilder (Bilder auf Anleitungsebene)
- [x] Bild-Löschung per AJAX
- [x] Mehrsprachige Schritt-Übersetzungen (AJAX-basiert, AdminStepTranslationController)
- [x] Übersetzungssystem für Inhalte (polymorphes ContentTranslation-Modell)
- [x] Admin-Panel für Übersetzungen (Modal-basiert, Multiple-Form-Ansatz)
- [x] KI-Übersetzung einzelner Felder (OpenAI API Integration via AdminAIController)
- [x] Admin-Panel für Tutorial-CRUD (inkl. SEO-Einstellungen, OG-Bild)
- [x] Admin-Panel für Tutorial-Schritte
- [x] **Screenshot-Upload mit Hotspot-Markierung** (Canvas-basiert)
- [x] Hotspot-Koordinaten speichern (x, y, radius, type) — TutorialHotspot-Modell
- [x] Tutorial-Vorschau im Admin-Panel
- [x] Admin-Panel für Page-CRUD
- [ ] Tag-System implementieren
- [ ] Kategorien-Hierarchie (Parent-Child)

### Phase 3: Öffentliches Frontend (Teilweise abgeschlossen)

**Ziel:** Alle öffentlichen Seiten und Suchfunktion

- [x] Startseite mit Sprachauswahl
- [x] Projektbeschreibung-Seite
- [x] Module-Übersicht und Detailseiten
- [x] Anleitung-Übersicht mit Filter und Kategorien
- [x] Einzelne Anleitung mit Screenshots und Schritten
- [x] Schritt-Bilder werden bei jedem Schritt angezeigt
- [x] Globale Bilder werden im Header-Bereich der Anleitung angezeigt
- [x] PDF-Export der Anleitung
- [x] Tutorial-Übersicht mit Kartenansicht (OG-Bilder als Vorschau, Fallback auf Thumbnail/Platzhalter)
- [ ] Tutorial-Seite mit **interaktivem Tutorial-Player**
- [ ] JavaScript-Player: Screenshot-Rendering mit Hotspot-Overlay
- [ ] Klick-Erkennung auf Hotspots
- [ ] Fortschrittsanzeige und Vor-/Zurück-Navigation
- [ ] Suchfunktion mit Laravel Scout
- [ ] Autocomplete-Vorschläge

### Phase 4: Interaktion (Teilweise abgeschlossen)

**Ziel:** Kommentare, Bewertungen und Feedback

- [x] Kommentar-System mit Moderation (Genehmigen/Ablehnen)
- [ ] Verschachtelte Kommentare (Antworten auf Kommentare)
- [ ] Bewertungs-System (Sterne)
- [ ] Feedback-Formular
- [ ] E-Mail-Benachrichtigungen bei neuen Kommentaren (optional)

### Phase 5: Feinschliff (In Arbeit)

**Ziel:** Design, Performance und Deployment-Vorbereitung

- [ ] Responsive Design testen und anpassen
- [ ] Barrierefreiheit prüfen
- [x] SEO-Meta-Tags und Open Graph (Tutorials: meta_title, meta_description, og_image mit Datei-Upload)
- [ ] Caching-Strategie implementieren (SetCacheHeaders Middleware vorhanden)
- [x] Demo-Seeder mit Beispiel-Inhalten (DefaultSeeder + DemoContentSeeder)
- [ ] Deployment-Dokumentation

---

## 7. Parallelität mit Hauptprojekten

Die Webseite soll **parallel zur Projektentwicklung** aktualisiert werden. Folgender Workflow wird empfohlen:

### Synchronisations-Workflow

1. **Neue Funktion in Hauptprojekt entwickelt** → Konzept-Dokument in `docs/` des Hauptprojekts erstellt
2. **Agent liest Konzept-Dokument** → Erstellt daraus Anleitungsentwurf in Webseitevereinsverwaltung
3. **Screenshots werden hinzugefügt** → Entweder manuell oder automatisch generiert
4. **Anleitung wird veröffentlicht** → Nutzer können die neue Funktion nachlesen

### Automatisierungsmöglichkeiten

- **Watch-Script:** Überwacht `docs/`-Ordner der Hauptprojekte auf neue Dateien
- **Import-Command:** Artisan-Befehl, der Konzept-Dokumente als Entwurf importiert
- **Markdown-zu-Guide-Konverter:** Wandelt Markdown-Konzepte in Guide-Modelle um

---

## 8. Technologie-Stack

| Komponente | Technologie |
|---|---|
| **Framework** | Laravel 12 (PHP 8.3) |
| **Authentifizierung** | Laravel Jetstream (mit Teams, 2FA, Passkeys) |
| **API-Auth** | Laravel Sanctum |
| **Frontend** | Blade + Tailwind CSS + Alpine.js |
| **Datenbank** | MySQL 8+ (kann auf entferntem Server liegen) |
| **Suche** | Laravel Scout + MySQL Full-Text (oder Meilisearch) |
| **Bild-Upload** | Laravel Storage (lokaler Disk oder S3) |
| **Bild-Komprimierung** | Intervention Image (OG-Bilder: max. 1920px Breite, 80% Qualität) |
| **Interaktive Tutorials** | Eigenes System — Screenshots mit Hotspots, Klick-Tracking, Fortschrittsspeicherung |
| **Tutorial-Recorder** | Canvas-basierte Hotspot-Markierung auf Screenshots (Admin-Panel) |
| **Tutorial-Player** | JavaScript-Player mit Screenshot-Overlay, Klick-Erkennung, Fortschrittsanzeige |
| **Cache** | Laravel Cache (File oder Redis) |
| **Queue** | Laravel Queue für E-Mail-Benachrichtigungen |
| **KI-Integration** | OpenAI API (Beschreibung, Übersetzung, Inhaltsgenerierung) |
| **Multi-Projekt** | Eigenes System — Platzhalter-Ersetzung, Domain/Subdomain-Erkennung, projekt-spezifische Screenshots |
| **Health Check** | HealthController für Monitoring und Load Balancer |

---

## 9. Konfiguration für entfernten Server

Die Datenbank kann auf einem anderen Server liegen. Konfiguration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=dein-datenbank-server.de
DB_PORT=3306
DB_DATABASE=webseitevereinsverwaltung
DB_USERNAME=benutzername
DB_PASSWORD=geheimes_passwort

# Optional: Verschlüsselte Verbindung
# DB_SSL=true
# DB_SSL_CA=/path/to/ca-cert.pem
```

Storage kann ebenfalls extern konfiguriert werden:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=eu-central-1
AWS_BUCKET=webseitevereinsverwaltung-storage
```

---

## 10. Anhang: Verweise auf Hauptprojekte

Die Webseite referenziert die 5 Hauptprojekte des Vereinsprojekt-Workspaces:

| Projekt | Rolle | Konzept-Dokumente |
|---|---|---|
| **vereinsverwaltung_dev** | Backend + Frontend — Hauptprojekt | `vereinsverwaltung_dev/docs/antraege/Konzept.md` |
| **regattaview** | Nur Frontend — Regatta-Ansicht | Noch zu erstellen |
| **helferplanung** | Backend + Frontend — Helferplanung | Noch zu erstellen |
| **Kurse** | Backend + Frontend — Kursverwaltung | Noch zu erstellen |
| **regattamanger** | Backend + Frontend — Regatta-Management | Noch zu erstellen |

Jedes Hauptprojekt soll seine Konzept-Dokumente im eigenen `docs/`-Ordner nach Funktionsbereichen strukturiert vorhalten. Die Webseitevereinsverwaltung importiert oder verweist auf diese Dokumente, um die Online-Anleitung zu erstellen.

---

## 11. Multi-Projekt-System (Implementiert ✓)

Aus der bestehenden KlickDoku-Anwendung (einzelne Dokumentations-App) wurde ein **zentrales Multi-Projekt-Dokumentationssystem** erstellt, das aus einer einzigen Master-Dokumentation mehrere projektspezifische Dokumentationen bereitstellt – dynamisch, ohne separate Installationen.

### 11.1 Überblick

**Zielgruppen:**
- **Dokumentations-Verwalter**: Pflegen Master-Inhalte und Projektkonfigurationen im Admin-Panel
- **Projekt-Admins**: Konfigurieren Begriffe, Design und Inhaltsfreigabe für ihr Projekt
- **Endnutzer**: Sehen projektspezifische Dokumentation unter ihrer Domain/Subdomain

**Funktionalität:**

| Feature | Beschreibung |
|---|---|
| **Platzhalter-System** | `{{app_name}}`, `{{club_name}}`, `{{events}}`, etc. in Titeln, Beschreibungen, Schritten |
| **Projekt-Konfiguration** | DB-gestützte Verwaltung von Projekten mit eigenen Begriffen, Design und Inhaltsfreigabe |
| **Dynamische Multi-Tenant-App** | Eine App, die basierend auf Domain/Subdomain/URL-Parameter automatisch das richtige Projekt anzeigt |
| **Inhaltsfreigabe (Opt-in)** | Explizite Zuweisung: Jeder Guide/Tutorial muss Projekten zugewiesen werden |
| **Screenshot-Fallback** | Master-Screenshot als Standard, projekt-spezifischer Screenshot überschreibt |
| **Begriffsvorschläge** | API-Import aus Hauptprojekten als Vorschlag, manuelle Bestätigung/Überschreibung |
| **Design-Anpassung** | Projekt-spezifische Farben, Logos und Branding |

### 11.2 Projekt-Erkennung

Die App erkennt das aktuelle Projekt automatisch in dieser Reihenfolge:

1. **Subdomain** (gleicher Server): `projekt-a.klickdoku.de` → Middleware erkennt Subdomain
2. **URL-Parameter** (unterschiedliche Server): `klickdoku.de?project=projekt-a` → Query-Parameter
3. **Domain**: `projekt-a.de` → exakte Domain-Match
4. **Fallback**: Kein Projekt → Master-Dokumentation (Standardverhalten)

Reservierte Subdomains (`www`, `api`, `admin`, `mail`, `ftp`, `smtp`, `pop`, `imap`, `webmail`, `cdn`, `static`) werden nicht als Projekt interpretiert.

### 11.3 Platzhalter-Katalog

| Platzhalter | Beschreibung | Beispiel |
|---|---|---|
| `{{app_name}}` | Name der Anwendung | "Vereinsverwaltung" |
| `{{club_name}}` | Name des Vereins/Clubs | "Sportverein Datteln" |
| `{{member}}` | Singular: Mitglied | "Mitglied" / "Teilnehmer" |
| `{{members}}` | Plural: Mitglieder | "Mitglieder" / "Teilnehmer" |
| `{{event}}` | Singular: Veranstaltung | "Veranstaltung" / "Termin" |
| `{{events}}` | Plural: Veranstaltungen | "Veranstaltungen" / "Termine" |
| `{{news}}` | Nachrichten | "Nachrichten" / "Aktuelles" |
| `{{calendar}}` | Kalender | "Kalender" / "Planer" |
| `{{tutorial}}` | Tutorial | "Tutorial" / "Anleitung" |
| `{{comment}}` | Kommentar | "Kommentar" / "Feedback" |
| `{{board}}` | Vorstand/Brett | "Vorstand" / "Team" |
| `{{team}}` | Team | "Team" / "Mannschaft" |
| `{{guide}}` | Anleitung | "Anleitung" / "Guide" |
| `{{category}}` | Kategorie | "Kategorie" / "Bereich" |
| `{{dashboard}}` | Dashboard | "Dashboard" / "Übersicht" |
| `{{settings}}` | Einstellungen | "Einstellungen" / "Konfiguration" |
| `{{profile}}` | Profil | "Profil" / "Mein Konto" |
| `{{search}}` | Suche | "Suche" / "Finden" |
| `{{help}}` | Hilfe | "Hilfe" / "Support" |
| `{{admin}}` | Admin | "Admin" / "Verwaltung" |

### 11.4 Datenbank-Tabellen (Neu)

| Tabelle | Zweck |
|---|---|
| `projects` | Projektkonfiguration (name, slug, subdomain, domain, app_name, club_name, Farben) |
| `project_terms` | Platzhalter-Ersetzungen pro Projekt (project_id, placeholder_key, value_de, value_en) |
| `guide_project` | Pivot: Guide ↔ Project (Opt-in Inhaltsfreigabe) |
| `tutorial_project` | Pivot: Tutorial ↔ Project (Opt-in Inhaltsfreigabe) |
| `project_guide_images` | Projekt-spezifische Screenshots für Guide-Schritte |
| `project_tutorial_images` | Projekt-spezifische Screenshots für Tutorial-Schritte |
| `placeholder_definitions` | Globaler Platzhalter-Katalog (Referenz) |
| `project_user` | Pivot: Project ↔ User (Many-to-Many mit Rollen) |

### 11.5 Implementierte Komponenten

**Modelle:**
- `Project.php` – Projektkonfiguration mit Beziehungen zu terms, guides, tutorials, images
- `ProjectTerm.php` – Platzhalter-Ersetzung pro Projekt
- `GuideProject.php` – Pivot: Guide ↔ Project
- `TutorialProject.php` – Pivot: Tutorial ↔ Project
- `ProjectGuideImage.php` – Projekt-spezifischer Screenshot (Guide)
- `ProjectTutorialImage.php` – Projekt-spezifischer Screenshot (Tutorial)
- `PlaceholderDefinition.php` – Globaler Platzhalter-Katalog

**Services:**
- `PlaceholderService.php` – Zentrale Platzhalter-Ersetzung mit Caching
- `ProjectResolver.php` – Erkennt aktuelles Projekt aus Anfrage (Subdomain, Query-Param, Domain)
- `ProjectScreenshotService.php` – Liefert korrekten Screenshot (Master oder projekt-spezifisch)
- `TermImportService.php` – Importiert Begriffe aus Language Files oder API als Vorschlag

**Middleware:**
- `SetProject.php` – Erkennt und setzt aktuelles Projekt in jeder Anfrage

**Policy:**
- `ProjectPolicy.php` – Rollen-basierte Berechtigungsprüfung für Projekt-Admin-Panel

**Helper-Funktionen:**
- `currentProject()` – Gibt das aktuelle Projekt zurück
- `replacePlaceholders($text)` – Ersetzt Platzhalter im aktuellen Projekt-Kontext

**Admin-Controller:**
- `AdminProjectController.php` – Projekte CRUD, Begriffe verwalten, Inhaltsfreigabe, User-Zuweisungen (assignUsers, removeUser)

**Admin-Views (Blade-Templates):**
- `admin/projects/index.blade.php` – Projektliste mit Status, Begriffs-, Guide-, Tutorial- und User-Anzahl
- `admin/projects/create.blade.php` – Formular für neues Projekt (Grunddaten, Domain, Design)
- `admin/projects/edit.blade.php` – Bearbeiten mit Tabs: Grunddaten, Begriffe, Inhaltsfreigabe, Screenshots, Users
- Sidebar-Navigation in `admin/layouts/app.blade.php` um "Projekte"-Link erweitert

**Öffentliche Views – Projekt-spezifisches Branding:**
- `layouts/public.blade.php` – Dynamisches Branding: App-Name, Logo, Primär- und Sekundärfarbe werden aus dem aktuellen Projekt geladen
- Navigation, Buttons, Language Switcher und Footer verwenden projekt-spezifische Farben via CSS-Variablen (`--primary`, `--primary-dark`, `--primary-light`)
- SEO/OpenGraph/Twitter Meta-Tags verwenden projekt-spezifischen App-Namen und Logo
- Footer zeigt projekt-spezifischen App-Namen und Vereinsname

**Blade-Directive:**
- `@placeholders($text)` in `AppServiceProvider.php` – Ersetzt Platzhalter direkt in Blade-Templates

**Artisan-Befehle:**
- `docs:import-terms` – Begriffe aus Hauptprojekt importieren
- `docs:seed-placeholders` – Platzhalter-Katalog initial befüllen
- `docs:verify-placeholders` – Unersetzte Platzhalter in Inhalten finden

**Erweiterte bestehende Modelle:**
- `Guide.php` – `projects()` belongsToMany + `scopeForProject()`
- `Tutorial.php` – `projects()` belongsToMany + `scopeForProject()`
- `GuideStep.php` – `projectImages(Project)` Methode
- `TutorialStep.php` – `projectScreenshot(Project)` Methode
- `User.php` – `projects()` belongsToMany + `hasProjectRole()`, `isProjectAdmin()`, `canEditProject()`
- `Project.php` – `users()` belongsToMany mit Rollen-Unterstützung

### 11.6 Screenshot-Bereichsauswahl (Implementiert ✓)

Auf der Projekt-Bearbeitungsseite (`/admin/projects/{id}/edit`, Tab "Screenshots") kann statt dem klassischen Datei-Upload ein **Screenshot mit Bereichsauswahl** erstellt werden. Die Funktionalität nutzt die Browser Screen Capture API und ermöglicht es, einen Bereich des Bildschirms visuell auszuwählen und direkt als projekt-spezifischen Screenshot hochzuladen.

**Funktionsumfang:**
- **Screen Capture API:** `navigator.mediaDevices.getDisplayMedia()` erfasst den Bildschirm als Videostream
- **Visuelle Bereichsauswahl:** Nutzer zieht ein Rechteck auf dem Canvas – der ausgewählte Bereich wird als Screenshot gespeichert
- **Steuerung:** Start/Stop-Buttons, Timer-Anzeige, Bereichsauswahl-Modus mit Maus-Events (mousedown, mousemove, mouseup)
- **Upload via fetch:** Asynchroner Upload ohne Seiten-Neuladen – bleibt auf dem Tab "Screenshots"
- **Support für Guides und Tutorials:** Separate Funktionen für Guide-Schritte (`startProjectGuideScreenshotCapture`) und Tutorial-Schritte (`startProjectTutorialScreenshotCapture`)

**JavaScript-Komponenten:**
- `screenshot-capture.js` – Zentrale Datei mit allen Screenshot-Funktionen:
  - `startProjectGuideScreenshotCapture(projectId, guideId, stepId)` – Startet Screen Capture für Guide-Schritt
  - `uploadProjectGuideScreenshotFromBlob(blob, projectId, guideId, stepId)` – Upload des Guide-Screenshots
  - `startProjectTutorialScreenshotCapture(projectId, tutorialId, stepId)` – Startet Screen Capture für Tutorial-Schritt
  - `uploadProjectTutorialScreenshotFromBlob(blob, projectId, tutorialId, stepId)` – Upload des Tutorial-Screenshots
  - `startGuideScreenshotCapture(guideId, stepId)` – Bestehende Funktion für Master-Guide-Screenshots
  - `startTutorialScreenshotCapture(tutorialId, stepId)` – Bestehende Funktion für Master-Tutorial-Screenshots

**Controller-Methoden:**
- `AdminProjectController@storeGuideScreenshot()` – Speichert projekt-spezifischen Guide-Screenshot, gibt JSON mit `url`, `guide_id`, `step_id` zurück
- `AdminProjectController@storeTutorialScreenshot()` – Speichert projekt-spezifischen Tutorial-Screenshot, gibt JSON mit `url`, `tutorial_id`, `step_id` zurück
- `$project->load('guides')` / `$project->load('tutorials')` vor der `contains()`-Prüfung, um Lazy-Loading-Problem zu vermeiden

**Admin-View-Integration:**
- In `admin/projects/edit.blade.php` (Tab "Screenshots") neben jedem klassischen Datei-Upload ein grüner Button "📸 Screenshot mit Bereichsauswahl"
- Nach erfolgreichem Upload wird die Seite mit `?tab=screenshots` neu geladen, sodass der Benutzer auf dem Screenshots-Tab bleibt

### 11.7 Projekt-User-Zuordnung (Rollen-basiert)

Jedes Projekt kann einem oder mehreren Users zugeordnet werden. Ein User kann mehrere Projekte verwalten. Die Zuordnung erfolgt über die Pivot-Tabelle `project_user` mit drei Rollen:

| Rolle | Berechtigungen | Beschreibung |
|---|---|---|
| **Admin** | Vollzugriff | Kann alle Aspekte des Projekts verwalten: Begriffe, Design, Inhaltsfreigabe, Screenshots, User |
| **Editor** | Inhalt bearbeiten | Kann Begriffe und Screenshots bearbeiten, aber keine User verwalten oder Design ändern |
| **Viewer** | Nur lesen | Kann die Projektkonfiguration einsehen, aber nichts ändern |

**Use Cases:**
- **Dokumentations-Verwalter** (Admin): Hat Zugriff auf alle Projekte und kann die Master-Inhalte pflegen
- **Projekt-Admin** (Admin): Verwaltet die Konfiguration eines spezifischen Projekts
- **Redakteur** (Editor): Kann Begriffe und Screenshots für ein Projekt pflegen
- **Beobachter** (Viewer): Kann die Projektdaten einsehen, z.B. für Reporting-Zwecke

**Admin-Panel:**
- Neuer Tab "Users" in `admin/projects/edit.blade.php`
- Anzeige der zugewiesenen Users mit Rollen-Badges (Admin=rot, Editor=blau, Viewer=grau)
- User hinzufügen/entfernen mit Rollenauswahl
- User-Count in der Projektliste (`admin/projects/index.blade.php`)

**Berechtigungsprüfung (ProjectPolicy):**
- `ProjectPolicy.php` – Zentrale Policy mit 7 Methoden: `view`, `update`, `delete`, `editTerms`, `manageScreenshots`, `manageUsers`, `manageContent`
- In `AppServiceProvider.php` via `Gate::policy()` registriert
- Alle 20 Methoden im `AdminProjectController` mit `$this->authorize()` gesichert
- Admin-Views verwenden `@can`-Direktiven: Tabs, Buttons und Formulare sind nur bei Berechtigung sichtbar
- 25 Unit-Tests decken alle Rollen-Kombinationen ab

**User-Modell – Hilfsmethoden:**
- `hasProjectRole(Project, string)` – Prüft eine bestimmte Rolle
- `isProjectAdmin(Project)` – Prüft Admin-Rechte
- `canEditProject(Project)` – Prüft Editor- oder Admin-Rechte
- `hasAccessToProject(Project)` – Prüft jeden Zugriff

**Erweiterte bestehende Controller:**
- `GuideController.php` – Projekt-Filter + projekt-spezifisches Caching
- `TutorialController.php` – Projekt-Filter + Screenshot-Fallback via `ProjectScreenshotService`
- `ApiController.php` – Projekt-Filter in allen Endpunkten (guides, tutorials, search) + Projekt-Zuweisung prüfen

### 11.8 Architektur-Diagramm

```
Request: projekt-a.de
       ↓
SetProject Middleware
       ↓
ProjectResolver
  ├─ Subdomain → projects DB
  ├─ Query Param → projects DB
  └─ Domain → projects DB
       ↓
Project Model
  ├─ PlaceholderService → project_terms DB
  ├─ ProjectScreenshotService → project_guide_images / project_tutorial_images DB
  ├─ guide_project Pivot → Guides gefiltert
  └─ tutorial_project Pivot → Tutorials gefiltert
       ↓
View mit Platzhaltern ersetzt + projekt-spezifischen Screenshots
```

### 11.9 Caching-Strategie

- Alle Projekt-Terms werden mit projekt-spezifischen Cache-Keys gecacht: `project_terms_{slug}_{locale}`
- Guide- und Tutorial-Listen verwenden projekt-spezifische Cache-Keys: `webseite:guides_index_{project_slug}_...`
- Bei Änderungen an Projekt-Terms wird der Cache automatisch invalidiert
- Cache-TTL: 30 Minuten für Inhalte, 60 Minuten für Projekt-Terms

### 11.10 Status

**Abgeschlossen ✓:**
- [x] Datenbank-Tabellen (8 Migrationen, inkl. project_user)
- [x] Modelle (7 neue Modelle + 6 erweiterte bestehende, inkl. User + Project)
- [x] Services (PlaceholderService, ProjectResolver, ProjectScreenshotService, TermImportService)
- [x] Middleware SetProject + Helper-Funktionen
- [x] Admin-Controller (AdminProjectController)
- [x] Admin-Views (index, create, edit mit Tabs)
- [x] Blade-Directive `@placeholders()`
- [x] Artisan-Befehle (import-terms, seed-placeholders, verify-placeholders)
- [x] Seeders (PlaceholderDefinitionsSeeder, DemoProjectsSeeder, DemoContentSeeder)
- [x] DefaultSeeder – Minimale Stammdaten bei jeder Installation (Admin-User, Kategorien, Tags)
- [x] GuideController & TutorialController mit Projekt-Filter
- [x] API-Controller mit Projekt-Filter
- [x] Projekt-spezifisches Branding in `layouts/public.blade.php` (Logo, Farben, App-Name)
- [x] GuideResource & TutorialResource mit Platzhalter-Ersetzung und projekt-spezifischen Screenshots
- [x] Unit-Tests für PlaceholderService (11 Tests)
- [x] Unit-Tests für ProjectResolver (11 Tests)
- [x] Feature-Tests für SetProject Middleware (7 Tests)
- [x] API-Tests mit Projekt-Kontext (11 Tests)
- [x] Unit-Tests für ProjectPolicy (25 Tests)
- [x] JavaScript-Tabs in `admin/projects/edit.blade.php`
- [x] DemoContentSeeder – 4 Beispiel-Guides und 2 Beispiel-Tutorials mit Platzhaltern
- [x] Screenshot-Upload-UI im Admin-Panel – projekt-spezifische Screenshots pro Guide- und Tutorial-Schritt
- [x] Screenshot-Bereichsauswahl – Screen Capture API mit visuellem Rechteck-Auswahl-Tool auf Projekt-Bearbeitungsseite
- [x] Screenshot-Inline-Upload – Upload via fetch ohne Seiten-Neuladen, bleibt auf Tab "Screenshots"
- [x] Projekt-User-Zuordnung – Many-to-Many mit Rollen (Admin, Editor, Viewer)
- [x] ProjectPolicy – Rollen-basierte Berechtigungsprüfung (view, update, delete, editTerms, manageScreenshots, manageUsers, manageContent)
- [x] Policy-Integration in AdminProjectController – alle 20 Methoden mit `$this->authorize()` gesichert
- [x] @can-Direktiven in Admin-Views – Tabs, Buttons und Formulare nur bei Berechtigung sichtbar
- [x] Unit-Tests für ProjectPolicy (25 Tests) – alle Rollen gegen alle Policy-Methoden getestet

**Offen:**
- *(Alle geplanten Tasks abgeschlossen)*

### 11.11 Installation & Seed-Modi

Das System unterstützt zwei Installationsmodi, gesteuert durch die Umgebungsvariable `DOCS_DEMO` in der `.env`:

#### Standard-Installation (`DOCS_DEMO=false` oder nicht gesetzt)

Bei einer frischen Installation (`php artisan migrate:fresh --seed`) werden nur die **minimalen Stammdaten** angelegt:

- **Admin-User**: `admin@klickdoku.local` / Passwort: `admin`
- **Basis-Kategorien**: Allgemein, Mitglieder, Veranstaltungen, Einstellungen
- **Basis-Tags**: Einsteiger, Fortgeschritten, Mitglieder, Veranstaltungen, Einstellungen, Wichtig
- **Platzhalter-Definitionen**: 20 Platzhalter für das Multi-Projekt-System

Keine Demo-Projekte, keine Demo-Guides, keine Demo-Tutorials.

#### Demo-Installation (`DOCS_DEMO=true`)

Zusätzlich zu den Stammdaten werden Demo-Daten für Entwicklung und Testing erstellt:

- **2 Demo-Projekte**: "Sportverein Datteln" und "RegattaPortal"
- **Projekt-begriffe**: Je 10 projekt-spezifische Platzhalter-Ersetzungen
- **4 Demo-Guides** mit Platzhaltern (z.B. `{{events}}`, `{{members}}`)
- **2 Demo-Tutorials** mit Platzhaltern
- **User-Projekt-Zuordnungen**: Demo-Users mit verschiedenen Rollen
- **Opt-in Zuweisungen**: Guides und Tutorials den Projekten zugewiesen

#### Verwendung

```bash
# Standard-Installation (Produktion)
php artisan migrate:fresh --seed

# Demo-Installation (Entwicklung/Testing)
# Setze in .env: DOCS_DEMO=true
php artisan migrate:fresh --seed
```

Die `DatabaseSeeder.php` orchestriert die Seeders in dieser Reihenfolge:
1. `DefaultSeeder` – Immer, idempotent (nur bei leerer Datenbank)
2. `PlaceholderDefinitionsSeeder` – Immer
3. `DemoProjectsSeeder`, `DemoContentSeeder`, `DemoSeeder` – Nur wenn `DOCS_DEMO=true`

---

### 11.12 Hotspot-Link-Anpassung (Implementiert ✓)

Hotspot-Links in Tutorials werden automatisch an das aktuelle Projekt angepasst. Der Redakteur trägt im Admin-Panel konkrete Links ein (z. B. `https://www.ssv-datteln.de/login`), und der Code wandelt sie automatisch in projekt-spezifische Links um.

#### Zwei Anpassungs-Mechanismen

| Mechanismus | Beschreibung | Beispiel |
|---|---|---|
| **Domain-Rewriting** | Überträgt den Pfad aus dem Hotspot-Link auf die `source_domain` des Projekts | `https://www.ssv-datteln.de/login` + `source_domain=https://antraege.ssv-datteln.de` → `https://antraege.ssv-datteln.de/login` |
| **Automatische Platzhalter-Erkennung** | Erkennt Werte, die mit Platzhalter-Default-Werten übereinstimmen, und wandelt sie in Platzhalter um | `https://example.de/Veranstaltungen` → `https://example.de/{{events}}` → projekt-spezifisch |

#### Domain-Rewriting

**Konfiguration:** Im Projekt-Edit-Formular (`/admin/projects/{id}/edit`, Tab "Grunddaten", Bereich "Domain & Subdomain") trägt der Admin die **Quelldomain** (`source_domain`) ein — die URL der externen Anwendung, **auf die der Pfad übertragen werden soll**.

**Ablauf (Pfad-Übernahme):**
1. Redakteur trägt Hotspot-Link ein: `https://www.ssv-datteln.de/login`
2. Projekt hat `source_domain` = `https://antraege.ssv-datteln.de`
3. Beim Anzeigen des Tutorials extrahiert `PlaceholderService::rewriteDomain()` den Pfad `/login` aus dem Hotspot-Link
4. Der Pfad wird auf die `source_domain` übertragen
5. Ergebnis: `https://antraege.ssv-datteln.de/login`

**Zwei Modi von `rewriteDomain()`:**
1. **Exakte Übereinstimmung:** Wenn der Hotspot-Link mit der `source_domain` beginnt, wird der Pfad direkt extrahiert und auf die Ziel-Domain übertragen
2. **Pfad-Übernahme (Standard):** Der Pfad, Query-Parameter und Fragment werden aus jeder absoluten URL (http/https) extrahiert und auf die `source_domain` übertragen. Relative Pfade (`/login`) bleiben unverändert.

**Protokoll- und Subdomain-Unterstützung:**
Das Domain-Rewriting berücksichtigt automatisch http/https-Protokolle und Subdomains:

| Hotspot-Link (eingetragen) | `source_domain` (Projekt) | Ergebnis |
|---|---|---|
| `https://www.ssv-datteln.de/login` | `https://antraege.ssv-datteln.de` | `https://antraege.ssv-datteln.de/login` |
| `http://www.ssv-datteln.de/login` | `https://antraege.ssv-datteln.de` | `https://antraege.ssv-datteln.de/login` (Protokollwechsel) |
| `https://www.ssv-datteln.de/login` | `http://antraege.ssv-datteln.de` | `http://antraege.ssv-datteln.de/login` (Protokollwechsel) |
| `http://portal.ssv-datteln.de/antraege` | `https://antraege.ssv-datteln.de` | `https://antraege.ssv-datteln.de/antraege` (Subdomain) |
| `https://www.ssv-datteln.de/login` | `https://portal.antraege.ssv-datteln.de` | `https://portal.antraege.ssv-datteln.de/login` (Subdomain-Ziel) |
| `/login` | `https://antraege.ssv-datteln.de` | `/login` (relativ, unverändert) |

Der Protokoll des Hotspot-Links wird durch das Protokoll der `source_domain` ersetzt. Subdomains im Hotspot-Link werden ignoriert – nur der Pfad, Query-Parameter und Fragment werden übernommen.

**Fallback:** Wenn keine `source_domain` gesetzt ist, wird die berechnete Projekt-Domain verwendet:
- Bei **Subdomain**: `subdomain` + Hauptdomain aus `APP_URL` (z. B. `sportverein.127.0.0.1:8000`)
- Bei **Domain**: Direkt aus `project.domain` (z. B. `klickdoc.ssv-datteln.de`)

**Methoden in `PlaceholderService`:**
- `rewriteDomain($url, $sourceDomain, $targetDomain)` — Ersetzt Domain in einer URL (zwei Modi: exakte Übereinstimmung + Pfad-Übernahme)
- `rewriteHotspotDomains($hotspotData, $project)` — Wendet Domain-Rewriting auf Hotspot-Links an
- `getProjectTargetDomain($project)` — Berechnet die Fallback-Ziel-Domain eines Projekts

#### Automatische Platzhalter-Erkennung

**Funktionsweise:** Beim Speichern eines Hotspot-Links im Admin-Panel erkennt `PlaceholderService::autoDetectPlaceholders()` automatisch Werte, die mit den Default-Werten der Platzhalter-Definitionen übereinstimmen, und wandelt sie in Platzhalter um.

**Beispiel:**
1. Redakteur trägt ein: `https://example.de/Veranstaltungen`
2. System erkennt "Veranstaltungen" als Default-Wert von `{{events}}`
3. Gespeichert wird: `https://example.de/{{events}}`
4. Beim Anzeigen im Tutorial wird `{{events}}` zum projekt-spezifischen Wert (z. B. "Events" im englischen Projekt)

**Wichtige Regeln:**
- Bereits bestehende Platzhalter (`{{events}}`) werden nicht doppelt verarbeitet
- Längere Default-Werte werden zuerst ersetzt, um Teilstring-Kollisionen zu vermeiden (z. B. "Veranstaltungen" vor "Veranstaltung")
- Die Erkennung ist locale-sensitiv: "Veranstaltungen" (de) und "Events" (en) werden beide erkannt

**Methoden in `PlaceholderService`:**
- `autoDetectPlaceholders($text, $locale)` — Erkennt Default-Werte und wandelt sie in Platzhalter um
- `autoDetectHotspotPlaceholders($hotspotData, $locale)` — Wendet autoDetect auf link, label, description an
- `resolveWithDefaults($text, $locale)` — Wandelt Platzhalter zurück zu Default-Werten (für Admin-Anzeige)
- `resolveHotspotWithDefaults($hotspotData, $locale)` — Wandelt Hotspot-Platzhalter zurück zu Default-Werten

#### Validierung (`ValidHotspotLink` Rule)

Die Custom Validation Rule `App\Rules\ValidHotspotLink` stellt sicher, dass Hotspot-Links gültig sind:

| Format | Beispiel | Gültig? |
|---|---|---|
| Absolute URL (https) | `https://www.ssv-datteln.de/login` | ✓ |
| Absolute URL (http) | `http://example.com/seite` | ✓ |
| Relativer Pfad | `/login` | ✓ |
| Query-Parameter | `?status=offen` | ✓ |
| Fragment | `#section` | ✓ |
| Protokoll-unabhängig | `//example.com/seite` | ✓ |
| Platzhalter-URL | `https://example.de/{{events}}` | ✓ |
| Leerwert | `(leer)` | ✓ (nullable) |
| Ungültig | `login` (ohne `/`) | ✗ |

#### Datenfluss (End-to-End)

```
Redakteur trägt Link ein
        ↓
AdminTutorialController::store/update()
        ↓
ValidHotspotLink Rule prüft Format
        ↓
PlaceholderService::autoDetectHotspotPlaceholders()
  (erkennt Default-Werte → Platzhalter)
        ↓
Speichern in DB (z.B. "https://example.de/{{events}}")
        ↓
[Bei Anzeige im Admin-Panel]
PlaceholderService::resolveHotspotWithDefaults()
  (Platzhalter → Default-Werte für Anzeige)
        ↓
[Bei Anzeige im Tutorial]
PlaceholderService::resolve() + rewriteHotspotDomains()
  (Platzhalter → projekt-spezifische Werte + Domain-Rewriting)
        ↓
Nutzer sieht projekt-spezifischen Link
```

#### Datenbank

- `projects.source_domain` — Quelldomain für Domain-Rewriting (nullable string)
- `tutorial_hotspots.link` — Hotspot-Link (max 2048 Zeichen, unterstützt Platzhalter)

---

### 11.13 Umsetzungsplan

Der Umsetzungsplan beschreibt die schrittweise Einführung des Multi-Projekt-Systems in der Produktionsumgebung.

#### Phase 1: Grundlage (Abgeschlossen ✓)

| Schritt | Beschreibung | Status |
|---|---|---|
| 1.1 | Datenbank-Migrationen ausführen | ✓ |
| 1.2 | Modelle und Beziehungen erstellen | ✓ |
| 1.3 | Services implementieren | ✓ |
| 1.4 | Middleware und Helper einrichten | ✓ |
| 1.5 | Platzhalter-Katalog initial befüllen | ✓ |

#### Phase 2: Admin-Panel (Abgeschlossen ✓)

| Schritt | Beschreibung | Status |
|---|---|---|
| 2.1 | Admin-Controller für Projekte | ✓ |
| 2.2 | Admin-Views (CRUD + Begriffe + Inhaltsfreigabe) | ✓ |
| 2.3 | Routes registrieren | ✓ |
| 2.4 | Sidebar-Navigation erweitern | ✓ |

#### Phase 3: Öffentliche Views (Abgeschlossen ✓)

| Schritt | Beschreibung | Status |
|---|---|---|
| 3.1 | GuideController mit Projekt-Filter erweitern | ✓ |
| 3.2 | TutorialController mit Projekt-Filter erweitern | ✓ |
| 3.3 | API-Controller mit Projekt-Filter erweitern | ✓ |
| 3.4 | Blade-Directive `@placeholders()` implementieren | ✓ |
| 3.5 | Projekt-spezifisches Branding in public.blade.php | ✓ |

#### Phase 4: Testing & Qualitätssicherung (Abgeschlossen ✓)

| Schritt | Beschreibung | Status |
|---|---|---|
| 4.1 | Unit-Tests für PlaceholderService | ✓ |
| 4.2 | Unit-Tests für ProjectResolver | ✓ |
| 4.3 | Feature-Tests für SetProject Middleware | ✓ |
| 4.4 | API-Tests mit Projekt-Kontext | ✓ |
| 4.5 | Demo-Projekte mit Beispiel-Inhalten befüllen | ✓ |
| 4.6 | Screenshot-Upload-UI im Admin-Panel | ✓ |
| 4.7 | Screenshot-Bereichsauswahl – Screen Capture API auf Projekt-Bearbeitungsseite | ✓ |
| 4.8 | ProjectPolicy – Rollen-basierte Berechtigungsprüfung | ✓ |
| 4.9 | Policy-Tests (25 Tests) | ✓ |

#### Phase 5: Produktionseinführung (Nächste Schritte)

| Schritt | Beschreibung | Priorität |
|---|---|---|
| 5.1 | Ersten echten Projekt anlegen und konfigurieren | Hoch |
| 5.2 | Master-Guides mit Platzhaltern aktualisieren | Hoch |
| 5.3 | Guides/Tutorials Projekten zuweisen | Hoch |
| 5.4 | Subdomain/Domain-Konfiguration einrichten | Hoch |
| 5.5 | Projekt-spezifische Screenshots hinzufügen | Mittel |
| 5.6 | Begriffe aus Hauptprojekt importieren | Mittel |
| 5.7 | `docs:verify-placeholders` ausführen und korrigieren | Mittel |
| 5.8 | API-Tests mit Projekt-Kontext | Niedrig |
| 5.9 | Guide/Tutorial Resource-Klassen erweitern | Niedrig |

#### Rollout-Strategie

1. **Interne Testing-Phase:** Demo-Projekte mit verschiedenen Subdomains testen
2. **Pilot-Projekt:** Erstes reales Projekt schrittweise einführen
3. **Monitoring:** `docs:verify-placeholders` regelmäßig ausführen, um unersetzte Platzhalter zu finden
4. **Skalierung:** Weitere Projekte hinzufügen, Cache-Performance überwachen

#### Wichtige Hinweise für das Rollout

- **Platzhalter in bestehenden Inhalten:** Vor dem Rollout sollten alle bestehenden Guides und Tutorials auf Platzhalter überprüft werden. Verwenden Sie `php artisan docs:verify-placeholders`, um unersetzte Platzhalter zu finden.
- **Subdomain-Konfiguration:** Stellen Sie sicher, dass die Webserver-Konfiguration (Nginx/Apache) Subdomains an die KlickDoku-App weiterleitet.
- **Cache-Invalidation:** Bei Änderungen an Projekt-Terms wird der Cache automatisch invalidiert. Überwachen Sie die Cache-Trefferquote nach dem Rollout.
- **Backup:** Vor dem Ausführen der Migrationen in Produktion ein vollständiges Datenbank-Backup erstellen.
