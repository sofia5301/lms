<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot of which verified teacher channels are live right now. One row per currently-live
 * channel, rewritten by the `live:refresh` scheduled sweep. The student home reads this table
 * (never the YouTube API directly), so the page stays instant and within API quota.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('channel_id')->unique();
            $table->string('video_id');
            $table->string('title')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->index('channel_id', 'live_streams_channel_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_streams');
    }
};
