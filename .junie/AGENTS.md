# GitHub Branch-Strategie – KlickDoku

## Übersicht

Dieses Dokument definiert die empfohlene Branch-Strategie für das KlickDoku-Projekt. Sie basiert auf dem **GitFlow**-Modell mit Anpassungen für moderne CI/CD-Praktiken.

---

## Branch-Typen

| Branch | Zweck | Schutz |
|---|---|---|
| `main` | Produktionsreife, stabile Version | Geschützt – nur per Merge-Request |
| `dev` | Beta-Test-Umgebung, Integration aller Features | Geschützt – nur per Merge-Request von Feature-/Fix-Branches |
| `feature/*` | Neue Features oder größere Änderungen | Temporär – nach Merge löschen |
| `bugfix/*` | Nicht-kritische Bugfixes (aus dev) | Temporär – nach Merge löschen |
| `hotfix/*` | Kritische Bugfixes in Produktion | Temporär – in main **und** dev mergen |
| `release/*` | Vorbereitung eines Releases mit Finalisierung | Temporär – nach Tagging löschen |

---

## Git-Baum – Visualisierung

```
                                                    Commit
                                                     │
                                                     ▼
main:      ○────────────────────────○───────────────○───────────────○───────────►
           ↑                        ↑               ↑               ↑
           │                        │               │               │
           │                        │        Release v1.1.0        │
           │                        │        (von release/ gemerged)│
           │                        │               │               │
           │                        │               │        Hotfix gemerged
           │                        │               │               │
           │                        │               │               ▼
           │                        │               │          (hotfix/ in main)
           │                        │               │
           │                        │        ┌──────┘
           │                        │        │
           │                        │   release/v1.1.0
           │                        │   ○────────○
           │                        │    ↑       ↑
           │                        │    │       │ (Finalisierung, Changelog)
           │                        │    │
           │                        │    └─── von dev abgezweigt
           │                        │
           │                 ┌──────┘
           │                 │
           │            Merge dev → main
           │            (nach Beta-Test)
           │
           │  v1.0.0 (Tag)
           │
dev:       ○────────○────────○────────○────────────────────────────────────────►
           ↑        ↑        ↑        ↑
           │        │        │        │
           │        │        │   feature/auth gemerged
           │        │        │
           │        │   bugfix/login gemerged
           │        │
           │   feature/dashboard gemerged
           │
           │  (Initialer dev-Branch)
           │
           ▼
feature/dashboard:  ○──○──○ (Commits)
feature/auth:           ○──○──○──○ (Commits)
bugfix/login:              ○──○ (Commits)
hotfix/security:                        ○──○ (in main + dev)
release/v1.1.0:                                  ○──○ (von dev)
```

### Vereinfachte Merge-Pfade

```
feature/* ──merge──► dev ──merge──► main
bugfix/*  ──merge──► dev ──merge──► main
hotfix/*  ──merge──► main ──cherry-pick──► dev
release/* ──merge──► main (+ Tag) ──merge──► dev
```

---

## Arbeitsabläufe

### 1. Feature-Entwicklung

```bash
# Von dev abzweigen
git checkout dev
git pull origin dev
git checkout -b feature/neues-feature

# Arbeit leisten, Commits erstellen
git add .
git commit -m "feat: Beschreibung der Änderung"

# Zurück zu dev und mergen
git checkout dev
git pull origin dev
git merge --no-ff feature/neues-feature
git push origin dev

# Feature-Branch aufräumen
git branch -d feature/neues-feature
git push origin --delete feature/neues-feature
```

**Regeln:**
- Immer von `dev` abzweigen.
- `--no-ff` (no fast-forward) verwenden, um den Merge-Commit und die Feature-Historie zu erhalten.
- Vor dem Merge: Code-Review und CI-Tests müssen erfolgreich sein.
- Feature-Branch nach erfolgreichem Merge löschen.

### 2. Bugfix (nicht-kritisch)

```bash
# Von dev abzweigen
git checkout dev
git pull origin dev
git checkout -b bugfix/beschreibung-des-bugs

# Fix implementieren
git add .
git commit -m "fix: Bugbeschreibung korrigiert"

# Zurück zu dev und mergen
git checkout dev
git merge --no-ff bugfix/beschreibung-des-bugs
git push origin dev
```

**Regeln:**
- Bugfixes, die noch nicht in Produktion sind, werden von `dev` abgezweigt.
- Nach dem Merge in `dev` gelangen sie beim nächsten Release automatisch in `main`.

### 3. Hotfix (kritisch – direkt in Produktion)

```bash
# Von main abzweigen
git checkout main
git pull origin main
git checkout -b hotfix/kritischer-fix

# Fix implementieren
git add .
git commit -m "hotfix: Kritischer Fehler behoben"

# In main mergen
git checkout main
git merge --no-ff hotfix/kritischer-fix
git tag -a v1.0.1 -m "Hotfix v1.0.1"
git push origin main --tags

# Nach dev cherry-picken
git checkout dev
git cherry-pick <commit-hash>
git push origin dev

# Hotfix-Branch aufräumen
git branch -d hotfix/kritischer-fix
git push origin --delete hotfix/kritischer-fix
```

**Regeln:**
- Hotfixes sind die **einzige** Ausnahme: Sie werden direkt von `main` abgezweigt.
- Sofort in `main` mergen und taggen.
- Den Fix anschließend mit `cherry-pick` nach `dev` übernehmen.
- Nur für echte Produktions-Notfälle verwenden.

### 4. Release-Vorbereitung

```bash
# Von dev abzweigen
git checkout dev
git pull origin dev
git checkout -b release/v1.1.0

# Finalisierung: Version, Changelog, Konfiguration
git add .
git commit -m "chore: Release v1.1.0 vorbereiten"

# In main mergen und taggen
git checkout main
git merge --no-ff release/v1.1.0
git tag -a v1.1.0 -m "Release v1.1.0"
git push origin main --tags

# Nach dev mergen (um Sync zu halten)
git checkout dev
git merge --no-ff release/v1.1.0
git push origin dev

# Release-Branch aufräumen
git branch -d release/v1.1.0
git push origin --delete release/v1.1.0
```

**Regeln:**
- Release-Branch dient der Finalisierung (Changelog, Versionsnummern, Konfiguration).
- Keine neuen Features mehr auf dem Release-Branch – nur Bugfixes und Vorbereitung.
- Nach dem Merge in `main` einen **annotierten Tag** erstellen.
- Release-Branch auch nach `dev` mergen, um die Historie synchron zu halten.

---

## Best Practices

### Branch-Namenskonvention

| Prefix | Beispiel | Verwendung |
|---|---|---|
| `feature/` | `feature/user-authentication` | Neue Features |
| `bugfix/` | `bugfix/login-timeout` | Nicht-kritische Bugfixes |
| `hotfix/` | `hotfix/sql-injection-patch` | Kritische Produktionsfixes |
| `release/` | `release/v2.0.0` | Release-Vorbereitung |

- Immer **kleingeschrieben**, Wörter mit Bindestrich (`-`) trennen.
- Kurz und beschreibend halten.

### Commit-Namenskonvention (Conventional Commits)

```
<typ>: <Beschreibung>

typ ∈ {feat, fix, docs, style, refactor, test, chore, hotfix}
```

Beispiele:
- `feat: Benutzerregistrierung mit E-Mail-Verifizierung`
- `fix: Login-Timeout bei inaktiven Sessions`
- `docs: API-Endpunkte dokumentiert`
- `hotfix: SQL-Injection-Lücke geschlossen`

### Branch-Schutzregeln (GitHub Settings)

- **`main`**:
  - ✅ Require pull request reviews (mindestens 1 Approver)
  - ✅ Require status checks to pass before merging
  - ✅ Require signed commits
  - ✅ Do not allow merge commits (squash or rebase only)
  - ✅ Include administrators
  - ✅ Restrict who can push

- **`dev`**:
  - ✅ Require pull request reviews
  - ✅ Require status checks to pass before merging
  - ✅ Do not allow force pushes

### Versionierung & Tagging

- **Semantische Versionierung (SemVer)** verwenden: `vMAJOR.MINOR.PATCH`
  - `MAJOR`: Inkompatible Änderungen
  - `MINOR`: Neue Features (rückwärtskompatibel)
  - `PATCH`: Bugfixes (rückwärtskompatibel)
- Immer **annotierte Tags** mit Beschreibung erstellen:
  ```bash
  git tag -a v1.2.0 -m "Release v1.2.0: Neue Dashboard-Features, Bugfixes"
  ```

### CI/CD-Integration

- Jeder Push auf `dev` löst die **Beta-Build-Pipeline** aus.
- Jeder Merge in `main` löst die **Produktions-Deploy-Pipeline** aus.
- Feature-Branches sollten automatisch **CI-Checks** (Tests, Linting) auslösen.

---

## Schnellreferenz – Wer, wann, welcher Branch?

| Szenario | Von welchem Branch? | Merge nach? |
|---|---|---|
| Neues Feature entwickeln | `dev` → `feature/*` | `dev` |
| Bug in Beta finden & fixen | `dev` → `bugfix/*` | `dev` |
| Kritischer Produktionsfehler | `main` → `hotfix/*` | `main` + `dev` (cherry-pick) |
| Release vorbereiten | `dev` → `release/*` | `main` (+ Tag) + `dev` |
| Beta → Produktion fördern | `dev` direkt → `main` | `main` |

---

## Zusammenfassung der Merge-Richtlinien

1. **`main` ist heilig** – nur freigegebene, getestete Versionen.
2. **`dev` ist die Integrationsumgebung** – alles fließt zuerst hierher.
3. **Feature-Branches leben kurz** – entwickeln, mergen, löschen.
4. **Hotfixes sind die einzige direkte main-Interaktion** – alles andere läuft über `dev`.
5. **Releases werden vorbereitet** – kein direktes Mergen von `dev` nach `main` ohne Release-Phase für größere Versionen.
6. **Alles dokumentieren** – Commits, Tags, Changelogs.
