<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_id',
        'sender_id',
        'type',
        'notifiable_id',
        'notifiable_type',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    // Notification type constants
    const TYPE_LIKE = 'like';
    const TYPE_COMMENT = 'comment';
    const TYPE_FOLLOW = 'follow';
    const TYPE_MENTION = 'mention';
    const TYPE_POST = 'post';

    /**
     * Relationship with user who receives the notification
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Relationship with user who sends the notification
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Polymorphic relationship with related model
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Check if notification is unread
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Get notification message
     */
    public function getMessage()
    {
        $senderName = $this->sender ? $this->sender->name : 'Someone';

        switch ($this->type) {
            case self::TYPE_LIKE:
                return "liked your post";
            case self::TYPE_COMMENT:
                return "commented on your post";
            case self::TYPE_FOLLOW:
                return "started following you";
            case self::TYPE_MENTION:
                return "mentioned you";
            case self::TYPE_POST:
                return "posted something new";
            default:
                return "interacted with your content";
        }
    }

    /**
     * Get notification icon
     */
    public function getIcon()
    {
        switch ($this->type) {
            case self::TYPE_LIKE:
                return 'fas fa-heart text-red-500';
            case self::TYPE_COMMENT:
                return 'fas fa-comment text-blue-500';
            case self::TYPE_FOLLOW:
                return 'fas fa-user-plus text-green-500';
            case self::TYPE_MENTION:
                return 'fas fa-at text-purple-500';
            case self::TYPE_POST:
                return 'fas fa-image text-orange-500';
            default:
                return 'fas fa-bell text-gray-500';
        }
    }
}
