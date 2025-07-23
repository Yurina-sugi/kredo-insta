<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    const ADMIN_ROLE_ID = 1;
    const USER_ROLE_ID = 2;

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

    #To get all the posts of a user
    public function posts()
    {
        return $this->hasMany(Post::class)->latest();
    }

    #To get all the followers of a user
    public function followers()
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function isFollowed()
    {
        return $this->followers()->where('follower_id', Auth::user()->id)->exists();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relationship with notifications received by the user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id')->latest();
    }

    /**
     * Relationship with notifications sent by the user
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    /**
     * Get unread notifications
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Get count of unread notifications
     */
    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Create a notification
     */
    public function createNotification($type, $recipientId, $notifiable = null, $data = [])
    {
        return Notification::create([
            'sender_id' => $this->id,
            'recipient_id' => $recipientId,
            'type' => $type,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'data' => $data
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $this->unreadNotifications()->update(['read_at' => now()]);
    }
}
