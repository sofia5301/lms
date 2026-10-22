<?php

namespace App\Console\Commands;

use App\Models\LiveStream;
use App\Models\YoutubeChannel;
use App\Services\YoutubeApi;
use Illuminate\Console\Command;
use Throwable;

/**
 * Sweep every verified teacher channel and record which ones are live right now, so the student
 * home can show a "Siaran Langsung" section without ever hitting the YouTube API on a page load.
 *
 * Quota: each channel costs one search.list call (100 units). Keep the channel count and the
 * schedule interval sane, or YouTube's daily quota will run out. No API key set → it no-ops.
 */
class RefreshLiveStreams extends Command
{
    protected $signature = 'live:refresh';

    protected $description = 'Check verified teacher YouTube channels for live streams and cache the results.';

    public function handle(YoutubeApi $api): int
    {
        if (! config('services.youtube.key')) {
            $this->warn('YOUTUBE_API_KEY not set; skipping live-stream refresh.');

            return self::SUCCESS;
        }

        $channels = YoutubeChannel::whereNotNull('verified_at')->get();
        $liveChannelIds = [];

        foreach ($channels as $channel) {
            try {
                $live = $api->liveForChannel($channel->channel_id);
            } catch (Throwable $e) {
                // One flaky channel/API hiccup must not abort the whole sweep.
                report($e);
                continue;
            }

            if (! $live) {
                continue;
            }

            $liveChannelIds[] = $channel->channel_id;

            $stream = LiveStream::firstOrNew(['channel_id' => $channel->channel_id]);
            $stream->teacher_id = $channel->teacher_id;
            $stream->video_id = $live['video_id'];
            $stream->title = $live['title'];
            $stream->thumbnail_url = $live['thumbnail_url'];
            $stream->started_at = $stream->started_at ?? now();
            $stream->checked_at = now();
            $stream->save();
        }

        // Anything no longer live is dropped so the home section clears itself.
        LiveStream::whereNotIn('channel_id', $liveChannelIds)->delete();

        $this->info(count($liveChannelIds).' channel(s) live.');

        return self::SUCCESS;
    }
}
