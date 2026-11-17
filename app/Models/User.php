<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Projekte zugewiesen zu diesem User (Many-to-Many).
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Prüft ob der User eine bestimmte Rolle in einem Projekt hat.
     */
    public function hasProjectRole(Project $project, string $role): bool
    {
        // Direkten DB-Query verwenden, um zuverlässig den Pivot-Wert zu lesen
        $record = \DB::table('project_user')
            ->where('project_id', $project->id)
            ->where('user_id', $this->id)
            ->first();
        if (!$record) {
            return false;
        }
        return $record->role === $role;
    }

    /**
     * Prüft ob der User Admin-Rechte in einem Projekt hat.
     */
    public function isProjectAdmin(Project $project): bool
    {
        return $this->hasProjectRole($project, 'admin');
    }

    /**
     * Prüft ob der User Editor- oder Admin-Rechte in einem Projekt hat.
     */
    public function canEditProject(Project $project): bool
    {
        return $this->hasProjectRole($project, 'admin') || $this->hasProjectRole($project, 'editor');
    }

    /**
     * Prüft ob der User Zugriff auf ein Projekt hat (jede Rolle).
     */
    public function hasAccessToProject(Project $project): bool
    {
        return $this->projects()->where('projects.id', $project->id)->exists();
    }
}
