<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Prüft, ob der User Projekte einsehen darf.
     * Nur Users mit mindestens einer Projekt-Zuweisung dürfen die Liste sehen.
     */
    public function viewAny(User $user): bool
    {
        // Alle authentifizierten Admins dürfen Projekte sehen
        // Später kann hier eine globale Admin-Prüfung hinzugefügt werden
        return true;
    }

    /**
     * Prüft, ob der User ein bestimmtes Projekt einsehen darf (Viewer-Rolle oder höher).
     * Vorläufig: Alle authentifizierten Users dürfen alle Projekte sehen.
     */
    public function view(User $user, Project $project): bool
    {
        // Vorläufig: Alle authentifizierten Users dürfen Projekte bearbeiten
        // Später: return $user->hasAccessToProject($project);
        return true;
    }

    /**
     * Prüft, ob der User ein neues Projekt erstellen darf.
     * Nur globale Admins (später) oder Users mit Admin-Rolle in mindestens einem Projekt.
     */
    public function create(User $user): bool
    {
        // Vorläufig: Alle authentifizierten Users dürfen Projekte erstellen
        // Später: Nur globale Admins
        return true;
    }

    /**
     * Prüft, ob der User ein Projekt bearbeiten darf (Editor-Rolle oder höher).
     * Vorläufig: Alle authentifizierten Users dürfen alle Projekte bearbeiten.
     */
    public function update(User $user, Project $project): bool
    {
        // Vorläufig: Alle authentifizierten Users
        // Später: return $user->canEditProject($project);
        return true;
    }

    /**
     * Prüft, ob der User ein Projekt löschen darf (nur Admin-Rolle).
     * Vorläufig: Alle authentifizierten Users dürfen alle Projekte löschen.
     */
    public function delete(User $user, Project $project): bool
    {
        // Vorläufig: Alle authentifizierten Users
        // Später: return $user->isProjectAdmin($project);
        return true;
    }

    /**
     * Prüft, ob der User Begriffe eines Projekts bearbeiten darf (Editor oder Admin).
     * Vorläufig: Alle authentifizierten Users.
     */
    public function editTerms(User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Prüft, ob der User Screenshots eines Projekts verwalten darf (Editor oder Admin).
     * Vorläufig: Alle authentifizierten Users.
     */
    public function manageScreenshots(User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Prüft, ob der User User-Zuweisungen eines Projekts verwalten darf (nur Admin).
     * Vorläufig: Alle authentifizierten Users.
     */
    public function manageUsers(User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Prüft, ob der User die Inhaltsfreigabe (Guides/Tutorials) eines Projekts verwalten darf (Admin).
     * Vorläufig: Alle authentifizierten Users.
     */
    public function manageContent(User $user, Project $project): bool
    {
        return true;
    }
}
