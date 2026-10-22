<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A currently-live YouTube stream from a verified teacher channel. Populated by the `live:refresh`
 * scheduled command; read by the student home to show a "Siaran Langsung" section.
 */
class LiveStream extends Model
{
    protected $fillable = [
        'teacher_id',
        'channel_id',
        'video_id',
        'title',
        'thumbnail_url',
        'started_at',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'teacher_id' => 'integer',
            'started_at' => 'datetime',
            'checked_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /** The public YouTube watch URL students open in a new tab. */
    public function watchUrl(): string
    {
        return 'https://www.youtube.com/watch?v='.$this->video_id;
    }
}
