<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected Project $project;
    protected User $adminUser;
    protected User $editorUser;
    protected User $viewerUser;
    protected User $noAccessUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::create([
            'name' => 'Test Projekt',
            'slug' => 'test-projekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $this->adminUser = User::factory()->create(['name' => 'Admin User']);
        $this->editorUser = User::factory()->create(['name' => 'Editor User']);
        $this->viewerUser = User::factory()->create(['name' => 'Viewer User']);
        $this->noAccessUser = User::factory()->create(['name' => 'No Access User']);

        // Directly insert into pivot table to ensure it works with RefreshDatabase
        \DB::table('project_user')->insert([
            'project_id' => $this->project->id,
            'user_id' => $this->adminUser->id,
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('project_user')->insert([
            'project_id' => $this->project->id,
            'user_id' => $this->editorUser->id,
            'role' => 'editor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('project_user')->insert([
            'project_id' => $this->project->id,
            'user_id' => $this->viewerUser->id,
            'role' => 'viewer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // --------------------------------------------------
    // view() – Vorläufig: Alle Users dürfen sehen
    // --------------------------------------------------

    public function test_viewer_can_view(): void
    {
        $this->assertTrue($this->viewerUser->can('view', $this->project));
    }

    public function test_editor_can_view(): void
    {
        $this->assertTrue($this->editorUser->can('view', $this->project));
    }

    public function test_admin_can_view(): void
    {
        $this->assertTrue($this->adminUser->can('view', $this->project));
    }

    public function test_no_access_user_can_view_temporarily(): void
    {
        // Vorläufig: Alle Users dürfen sehen
        $this->assertTrue($this->noAccessUser->can('view', $this->project));
    }

    // --------------------------------------------------
    // update() – Vorläufig: Alle Users dürfen bearbeiten
    // --------------------------------------------------

    public function test_viewer_can_update_temporarily(): void
    {
        $this->assertTrue($this->viewerUser->can('update', $this->project));
    }

    public function test_editor_can_update(): void
    {
        $this->assertTrue($this->editorUser->can('update', $this->project));
    }

    public function test_admin_can_update(): void
    {
        $this->assertTrue($this->adminUser->can('update', $this->project));
    }

    // --------------------------------------------------
    // delete() – Vorläufig: Alle Users dürfen löschen
    // --------------------------------------------------

    public function test_viewer_can_delete_temporarily(): void
    {
        $this->assertTrue($this->viewerUser->can('delete', $this->project));
    }

    public function test_editor_can_delete_temporarily(): void
    {
        $this->assertTrue($this->editorUser->can('delete', $this->project));
    }

    public function test_admin_can_delete(): void
    {
        $this->assertTrue($this->adminUser->can('delete', $this->project));
    }

    // --------------------------------------------------
    // editTerms() – Vorläufig: Alle Users
    // --------------------------------------------------

    public function test_viewer_can_edit_terms_temporarily(): void
    {
        $this->assertTrue($this->viewerUser->can('editTerms', $this->project));
    }

    public function test_editor_can_edit_terms(): void
    {
        $this->assertTrue($this->editorUser->can('editTerms', $this->project));
    }

    public function test_admin_can_edit_terms(): void
    {
        $this->assertTrue($this->adminUser->can('editTerms', $this->project));
    }

    // --------------------------------------------------
    // manageScreenshots() – Vorläufig: Alle Users
    // --------------------------------------------------

    public function test_viewer_can_manage_screenshots_temporarily(): void
    {
        $this->assertTrue($this->viewerUser->can('manageScreenshots', $this->project));
    }

    public function test_editor_can_manage_screenshots(): void
    {
        $this->assertTrue($this->editorUser->can('manageScreenshots', $this->project));
    }

    public function test_admin_can_manage_screenshots(): void
    {
        $this->assertTrue($this->adminUser->can('manageScreenshots', $this->project));
    }

    // --------------------------------------------------
    // manageUsers() – Vorläufig: Alle Users
    // --------------------------------------------------

    public function test_viewer_can_manage_users_temporarily(): void
    {
        $this->assertTrue($this->viewerUser->can('manageUsers', $this->project));
    }

    public function test_editor_can_manage_users_temporarily(): void
    {
        $this->assertTrue($this->editorUser->can('manageUsers', $this->project));
    }

    public function test_admin_can_manage_users(): void
    {
        $this->assertTrue($this->adminUser->can('manageUsers', $this->project));
    }

    // --------------------------------------------------
    // manageContent() – Vorläufig: Alle Users
    // --------------------------------------------------

    public function test_viewer_can_manage_content_temporarily(): void
    {
        $this->assertTrue($this->viewerUser->can('manageContent', $this->project));
    }

    public function test_editor_can_manage_content_temporarily(): void
    {
        $this->assertTrue($this->editorUser->can('manageContent', $this->project));
    }

    public function test_admin_can_manage_content(): void
    {
        $this->assertTrue($this->adminUser->can('manageContent', $this->project));
    }

    // --------------------------------------------------
    // User helper methods
    // --------------------------------------------------

    public function test_has_project_role(): void
    {
        $this->assertTrue($this->adminUser->hasProjectRole($this->project, 'admin'));
        $this->assertTrue($this->editorUser->hasProjectRole($this->project, 'editor'));
        $this->assertTrue($this->viewerUser->hasProjectRole($this->project, 'viewer'));
        $this->assertFalse($this->adminUser->hasProjectRole($this->project, 'editor'));
        $this->assertFalse($this->noAccessUser->hasProjectRole($this->project, 'admin'));
    }

    public function test_is_project_admin(): void
    {
        $this->assertTrue($this->adminUser->isProjectAdmin($this->project));
        $this->assertFalse($this->editorUser->isProjectAdmin($this->project));
        $this->assertFalse($this->viewerUser->isProjectAdmin($this->project));
    }

    public function test_can_edit_project(): void
    {
        $this->assertTrue($this->adminUser->canEditProject($this->project));
        $this->assertTrue($this->editorUser->canEditProject($this->project));
        $this->assertFalse($this->viewerUser->canEditProject($this->project));
    }
}
