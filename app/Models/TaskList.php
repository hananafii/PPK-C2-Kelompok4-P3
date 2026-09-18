<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    /**
     * The owner of this task list.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Team members invited to collaborate on this task list.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_list_user')->withTimestamps();
    }

    /**
     * Tasks belonging to this task list.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Check if user is the owner of this task list.
     */
    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && (int) $this->user_id === (int) $user->id;
    }

    /**
     * Check if user is a member of this task list.
     */
    public function hasMember(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $this->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if user can view/access this task list.
     */
    public function canAccess(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (! empty($user->is_admin))
            || $this->isOwnedBy($user)
            || $this->hasMember($user);
    }

    /**
     * Calculate task completion percentage.
     */
    public function progressPercentage(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->tasks()->whereIn('status', ['Completed', 'completed'])->count();

        return (int) round(($completed / $total) * 100);
    }
}
