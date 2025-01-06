<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'content',
        'user_id', 
        'is_approved', 
    ];

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

}
