<x-dynamic-component :component="auth()->user()->isTeacher() ? 'app-layout' : 'student-layout'" :title="__('Bab :number: :title', ['number' => $chapter->number, 'title' => $chapter->title])">
    @php($col = $subject->color ?: '#17907B')
    @php($grad = "linear-gradient(135deg, color-mix(in oklab, {$col} 30%, #fff), color-mix(in oklab, {$col} 12%, #fff))")
    @php($tagBg = "color-mix(in oklab, {$col} var(--pill-bw), var(--pill-bb))")
    @php($tagColor = "color-mix(in oklab, {$col} var(--pill-fw), var(--pill-fb))")
    @php($doneCount = $lessons->filter(fn ($l) => $watchedIds->contains($l->id))->count())
    @php($totalItems = $lessons->count() + $materials->count() + $quizzes->count())
    @php($progPct = $totalItems > 0 ? (int) round($doneCount / $totalItems * 100) : 0)

    <style>
        .bh-crumb { display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:var(--wl-muted); }
        .bh-crumb .cur { color:var(--wl-ink); }
        .bh-crumb .sep { color:var(--wl-muted-2); display:inline-flex; }

        .bh-hero { position:relative; overflow:hidden; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:18px; padding:20px 24px; display:flex; align-items:center; gap:18px; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .bh-hero .blob { position:absolute; border-radius:50%; background:#DCF2EE; opacity:.5; z-index:0; }
        .bh-hero > * { position:relative; z-index:1; }
        .bh-num { width:60px; height:60px; border-radius:16px; background:#DCF2EE; color:#0F7A68; display:grid; place-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:24px; flex-shrink:0; }
        .bh-prog { width:300px; max-width:36%; flex-shrink:0; }
        @media (max-width:760px) { .bh-prog { width:100%; max-width:none; } .bh-hero { flex-wrap:wrap; } }

        .bh-tabs { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .bh-tab { display:inline-flex; align-items:center; gap:9px; min-height:46px; padding:0 20px; border-radius:999px; border:1.5px solid var(--wl-line-2); background:var(--wl-surface); font-family:'Geist',sans-serif; font-weight:800; font-size:14px; color:var(--wl-ink); cursor:pointer; }
        .bh-tab .ic, .bh-tab .ct { color:var(--wl-muted); }
        .bh-tab.is-on { background:#17907B; border-color:#17907B; color:#fff; }
        .bh-tab.is-on .ic, .bh-tab.is-on .ct { color:#fff; }

        .bh-sec { background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:18px; padding:20px 22px; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .bh-sechead { display:flex; align-items:center; gap:12px; margin-bottom:16px; }
        .bh-sechead .tile { width:38px; height:38px; border-radius:11px; display:grid; place-items:center; flex-shrink:0; }
        .bh-sechead .t { font-family:'Geist',sans-serif; font-weight:800; font-size:17px; color:var(--wl-ink); }
        .bh-seeall { margin-left:auto; display:inline-flex; align-items:center; gap:6px; font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:#0F7A68; background:none; border:none; cursor:pointer; }
        .bh-vidgrid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
        @media (max-width:820px) { .bh-vidgrid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:560px) { .bh-vidgrid { grid-template-columns:1fr; } }

        /* Full-width item row with a pastel shade on the right (--p pastel, --c deeper). */
        .bh-item { position:relative; overflow:hidden; display:flex; align-items:center; gap:14px; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:16px; padding:14px 18px; }
        .bh-item > * { position:relative; z-index:1; }
        .bh-shade { position:absolute; top:0; right:0; bottom:0; width:320px; max-width:48%; z-index:0; pointer-events:none; }
        .bh-shade::before { content:''; position:absolute; width:150px; height:150px; border-radius:50%; right:-6px; top:50%; margin-top:-75px; background:var(--p); }
        .bh-shade::after  { content:''; position:absolute; width:120px; height:120px; border-radius:50%; right:92px; bottom:-30px; background:var(--c); opacity:.5; }
        @media (max-width:560px) { .bh-shade { display:none; } }
    </style>

    <div style="display:flex;flex-direction:column;gap:22px">
        {{-- Back + breadcrumb --}}
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <a href="{{ route('belajar.subjek', ['subject' => $subject->slug, 'grade' => $grade->level]) }}" class="wl-back" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Kembali') }}</a>
            <span class="bh-crumb">
                <span style="display:inline-flex;align-items:center;gap:6px;color:var(--wl-ink)"><x-subject-icon :subject="$subject" :size="16" />{{ $subject->name }}</span>
                <span class="sep"><x-icon name="chevron-right" class="h-4 w-4" /></span>
                {{ $grade->displayName() }}
                <span class="sep"><x-icon name="chevron-right" class="h-4 w-4" /></span>
                <span class="cur">{{ __('Bab :number: :title', ['number' => $chapter->number, 'title' => $chapter->title]) }}</span>
            </span>
        </div>

        {{-- Hero: number + title + description, with chapter progress. --}}
        <div class="bh-hero">
            <span class="blob" style="width:150px;height:150px;right:60px;top:-60px"></span>
            <span class="blob" style="width:110px;height:110px;right:-20px;bottom:-50px;opacity:.35"></span>
            <span class="bh-num">{{ sprintf('%02d', $chapter->number) }}</span>
            <div style="display:flex;flex-direction:column;gap:4px;margin-right:auto;min-width:0">
                <h2 style="margin:0;font-family:'Geist',sans-serif;font-size:24px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ __('Bab :number: :title', ['number' => $chapter->number, 'title' => $chapter->title]) }}</h2>
                @if ($chapter->description)
                    <span style="font-size:14px;color:var(--wl-muted)">{{ $chapter->description }}</span>
                @endif
            </div>
            <div class="bh-prog">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <span style="font-family:'Geist',sans-serif;font-size:13px;font-weight:800;color:var(--wl-muted)">{{ __('Kemajuan bab') }}</span>
                    <span style="font-family:'Geist',sans-serif;font-size:13px;font-weight:800;color:var(--wl-ink)">{{ $doneCount }}/{{ $totalItems }}</span>
                </div>
                <div style="height:8px;border-radius:999px;background:#EFEEE6;overflow:hidden">
                    <div style="height:100%;border-radius:999px;background:#17907B;width:{{ $progPct }}%"></div>
                </div>
            </div>
        </div>

        {{-- Filter tabs + content. --}}
        <div x-data="{ tab: 'semua' }" style="display:flex;flex-direction:column;gap:22px">
            <div class="bh-tabs">
                <button type="button" class="bh-tab" :class="tab==='semua' ? 'is-on' : ''" @click="tab='semua'"><span class="ic"><x-icon name="list" class="h-[18px] w-[18px]" /></span>{{ __('Semua') }} <span class="ct">({{ $totalItems }})</span></button>
                <button type="button" class="bh-tab" :class="tab==='video' ? 'is-on' : ''" @click="tab='video'"><span class="ic"><x-icon name="play" class="h-[18px] w-[18px]" /></span>{{ __('Video') }} <span class="ct">({{ $lessons->count() }})</span></button>
                <button type="button" class="bh-tab" :class="tab==='bahan' ? 'is-on' : ''" @click="tab='bahan'"><span class="ic"><x-icon name="book" class="h-[18px] w-[18px]" /></span>{{ __('Bahan Sokongan') }} <span class="ct">({{ $materials->count() }})</span></button>
                <button type="button" class="bh-tab" :class="tab==='kuiz' ? 'is-on' : ''" @click="tab='kuiz'"><span class="ic"><x-icon name="help-circle" class="h-[18px] w-[18px]" /></span>{{ __('Kuiz') }} <span class="ct">({{ $quizzes->count() }})</span></button>
            </div>

            {{-- Video pelajaran --}}
            <div class="bh-sec" x-show="tab==='semua' || tab==='video'" x-cloak>
                <div class="bh-sechead">
                    <span class="tile" style="background:#DCF2EE;color:#0F7A68"><x-icon name="play" class="h-5 w-5" /></span>
                    <span class="t">{{ __('Video pelajaran') }} ({{ $lessons->count() }})</span>
                    @if ($lessons->isNotEmpty())
                        <button type="button" class="bh-seeall" @click="tab='video'">{{ __('Tonton semua') }} <x-icon name="arrow-right" class="h-4 w-4" /></button>
                    @endif
                </div>
                @if ($lessons->isEmpty())
                    <div style="padding:30px;display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center">
                        <span style="width:44px;height:44px;border-radius:50%;background:#F1F0E8;display:grid;place-items:center;color:var(--wl-muted-2)"><x-icon name="video" style="width:22px;height:22px" /></span>
                        <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:15px;color:var(--wl-ink)">{{ __('Tiada video untuk bab ini lagi') }}</span>
                        <span style="font-size:13.5px;color:var(--wl-muted)">{{ __('Cikgu belum memuat naik sebarang video untuk bab ini.') }}</span>
                    </div>
                @else
                    <div class="bh-vidgrid">
                        @foreach ($lessons as $lesson)
                            <a href="{{ route('video.show', $lesson) }}" class="vid-card"
                               style="display:block;text-decoration:none;background:var(--wl-surface);border:1px solid var(--wl-line);border-radius:18px;overflow:hidden;box-shadow:0 4px 16px rgba(46,44,80,.04);cursor:pointer">
                                <div style="height:130px;background:{{ $grad }};display:grid;place-items:center;position:relative">
                                    @if ($lesson->thumbnailUrl())
                                        <img src="{{ $lesson->thumbnailUrl() }}" alt="" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                                    @endif
                                    @if ($watchedIds->contains($lesson->id))
                                        <span style="position:absolute;top:10px;left:10px;background:#17907B;color:#fff;border-radius:999px;padding:4px 12px;font-family:'Geist',sans-serif;font-size:11.5px;font-weight:800;z-index:2;display:inline-flex;align-items:center;gap:5px"><x-icon name="check" class="h-[13px] w-[13px]" /> {{ __('Ditonton') }}</span>
                                    @endif
                                    <span style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.9);display:grid;place-items:center;color:#4276AE;z-index:1"><x-icon name="play" class="h-[15px] w-[15px]" style="margin-left:2px" /></span>
                                    @if ($lesson->durationLabel())
                                        <span style="position:absolute;right:10px;bottom:10px;background:rgba(66,118,174,.85);color:#fff;font-size:11px;font-weight:700;border-radius:999px;padding:3px 9px">{{ $lesson->durationLabel() }}</span>
                                    @endif
                                </div>
                                <div style="padding:14px 16px;display:flex;flex-direction:column;gap:4px">
                                    <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:15px;color:var(--wl-ink)">{{ $lesson->title }}</span>
                                    <span style="font-size:12.5px;color:var(--wl-muted)">{{ $lesson->teacher->name }}</span>
                                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:700;color:var(--wl-muted)"><img src="{{ asset('images/eye.png') }}" alt="" style="width:15px;height:15px;object-fit:contain">{{ $lesson->views_count }} {{ __('tontonan') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Bahan sokongan --}}
            <div class="bh-sec" x-show="tab==='semua' || tab==='bahan'" x-cloak>
                <div class="bh-sechead">
                    <span class="tile" style="background:#FDE7E0;color:#B84A75"><x-icon name="book" class="h-5 w-5" /></span>
                    <span class="t">{{ __('Bahan Sokongan') }} ({{ $materials->count() }})</span>
                </div>
                @if ($materials->isEmpty())
                    <div style="padding:30px;display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center">
                        <span style="width:44px;height:44px;border-radius:50%;background:#F1F0E8;display:grid;place-items:center;color:var(--wl-muted-2)"><x-icon name="file" style="width:22px;height:22px" /></span>
                        <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:15px;color:var(--wl-ink)">{{ __('Tiada bahan untuk bab ini lagi') }}</span>
                        <span style="font-size:13.5px;color:var(--wl-muted)">{{ __('Cikgu belum memuat naik slaid, PDF atau lembaran kerja untuk bab ini.') }}</span>
                    </div>
                @else
                    <div style="display:flex;flex-direction:column;gap:12px">
                        @foreach ($materials as $material)
                            <div class="bh-item">
                                <span class="bh-shade" style="--p:#FDEBF3;--c:#F8D2E2"></span>
                                <span style="width:44px;height:44px;border-radius:12px;background:#FDE7E0;color:#B84A75;display:grid;place-items:center;flex-shrink:0"><x-icon :name="$material->iconName()" style="width:21px;height:21px" /></span>
                                <div style="display:flex;flex-direction:column;gap:2px;min-width:0;flex:1">
                                    <span class="wl-cardtitle" style="font-family:'Geist',sans-serif;font-weight:800;font-size:14.5px;color:var(--wl-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $material->title }}</span>
                                    <span style="font-size:12.5px;color:var(--wl-muted)">{{ $material->humanSize() }}</span>
                                </div>
                                <a href="{{ route('muat-turun.bahan', $material) }}" title="{{ __('Muat turun') }}" style="width:42px;height:42px;border-radius:11px;background:#DCF2EE;color:#0F7A68;display:grid;place-items:center;text-decoration:none;flex-shrink:0"><x-icon name="download" style="width:20px;height:20px" /></a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Kuiz --}}
            <div class="bh-sec" x-show="tab==='semua' || tab==='kuiz'" x-cloak>
                <div class="bh-sechead">
                    <span class="tile" style="background:#EDE7F9;color:#7C5CBF"><x-icon name="help-circle" class="h-5 w-5" /></span>
                    <span class="t">{{ __('Kuiz') }} ({{ $quizzes->count() }})</span>
                </div>
                @if ($quizzes->isEmpty())
                    <div style="padding:30px;display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center">
                        <span style="width:44px;height:44px;border-radius:50%;background:#F1F0E8;display:grid;place-items:center;color:var(--wl-muted-2)"><x-icon name="quiz" style="width:22px;height:22px" /></span>
                        <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:15px;color:var(--wl-ink)">{{ __('Tiada kuiz untuk bab ini lagi') }}</span>
                        <span style="font-size:13.5px;color:var(--wl-muted)">{{ __('Tonton video dahulu. Kuiz akan muncul di sini apabila cikgu menyediakannya.') }}</span>
                    </div>
                @else
                    <div style="display:flex;flex-direction:column;gap:12px">
                        @foreach ($quizzes as $quiz)
                            <div class="bh-item">
                                <span class="bh-shade" style="--p:#F2EBFC;--c:#DFCFF3"></span>
                                <span style="width:44px;height:44px;border-radius:12px;background:#EDE7F9;color:#7C5CBF;display:grid;place-items:center;flex-shrink:0"><x-icon name="{{ $quiz->isFile() ? 'file' : 'help-circle' }}" style="width:21px;height:21px" /></span>
                                <div style="display:flex;flex-direction:column;gap:4px;min-width:0;flex:1">
                                    <span style="background:{{ $tagBg }};color:{{ $tagColor }};border-radius:999px;padding:3px 11px;font-family:'Geist',sans-serif;font-size:11.5px;font-weight:800;align-self:flex-start">{{ $subject->displayName() }}</span>
                                    <span class="wl-quiztitle" style="font-family:'Geist',sans-serif;font-weight:800;font-size:15.5px;color:var(--wl-ink)">{{ $quiz->localizedTitle() }}</span>
                                    <div style="display:flex;align-items:center;gap:12px;font-size:12.5px;font-weight:700;color:var(--wl-muted);flex-wrap:wrap">
                                        @if ($quiz->my_attempts_count > 0)
                                            <span>{{ __('Dicuba :count kali', ['count' => $quiz->my_attempts_count]) }}</span>
                                        @endif
                                        @if ($quiz->isInteractive() && ! is_null($quiz->questions_count))
                                            @if ($quiz->my_attempts_count > 0)<span style="color:var(--wl-line-2)">|</span>@endif
                                            <span>{{ __(':count soalan', ['count' => $quiz->questions_count]) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('kuiz.intro', ['quiz' => $quiz, 'from' => 'chapter']) }}" class="wl-btn-primary" style="min-height:46px;display:inline-flex;align-items:center;gap:8px;border-radius:12px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:14px;padding:0 20px;text-decoration:none;flex-shrink:0">{{ $quiz->isFile() ? __('Lihat Kuiz') : __('Cuba Kuiz') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>
