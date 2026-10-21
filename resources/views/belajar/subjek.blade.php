<x-dynamic-component :component="auth()->user()->isTeacher() ? 'app-layout' : 'student-layout'" :title="$subject->name.' '.$grade->displayName()">
    @php($col = $subject->color ?: '#17907B')
    @php($selGrad = "linear-gradient(135deg, color-mix(in oklab, {$col} 30%, #fff), color-mix(in oklab, {$col} 12%, #fff))")
    @php($totalVideos = $chapters->sum('lessons_count'))
    @php($totalBahan = $chapters->sum('materials_count'))
    @php($totalKuiz = $chapters->sum('quizzes_count'))

    <style>
        /* Header content summary (totals across the subject's chapters). */
        .ss-sum { display:flex; align-items:center; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:16px; padding:8px 10px; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .ss-s { display:flex; align-items:center; gap:13px; padding:6px 30px; }
        .ss-s .ic { width:40px; height:40px; border-radius:50%; display:grid; place-items:center; flex-shrink:0; }
        .ss-s .n { font-family:'Geist',sans-serif; font-size:22px; font-weight:800; color:var(--wl-ink); line-height:1; }
        .ss-s .l { font-size:12px; font-weight:700; color:var(--wl-muted); margin-top:3px; white-space:nowrap; }
        .ss-div { width:1px; align-self:stretch; background:var(--wl-line); margin:8px 0; }
        @media (max-width:620px) { .ss-sum { width:100%; } .ss-s { flex:1; justify-content:center; padding:6px 8px; } .ss-s .l { font-size:11px; } }

        /* Chapter row extras: a tinted icon block + a chevron. */
        /* Decorative pastel shade: a pastel panel filling the right, with a ROUNDED (circular) left
           edge, plus a deeper translucent circle for the two-tone shade. Per-unit colours come in
           via --p (pastel) and --c (deeper pastel). No icon. */
        .ss-ico { width:200px; align-self:stretch; margin:-14px 0; flex-shrink:0; position:relative; overflow:hidden; }
        .ss-ico::before { content:''; position:absolute; top:0; bottom:0; left:44px; right:0; background:var(--p); border-radius:999px 0 0 999px; }
        .ss-ico::after  { content:''; position:absolute; width:96px; height:96px; border-radius:50%; left:20px; bottom:-24px; background:var(--c); opacity:.45; }
        @media (max-width:900px) { .ss-ico { display:none; } }
        .ss-prog { display:flex; flex-direction:column; gap:6px; width:160px; flex-shrink:0; }
        @media (max-width:560px) { .ss-prog { width:110px; } }
        .ss-chev { width:40px; height:40px; border-radius:50%; display:grid; place-items:center; flex-shrink:0; }

        /* Year dropdown (custom, opens downward, matches the app). */
        .yr-wrap { position:relative; }
        .yr-trigger { display:inline-flex; align-items:center; justify-content:space-between; gap:10px; min-width:150px; min-height:44px; border:1px solid var(--wl-line-2); border-radius:12px; padding:0 14px; background:var(--wl-surface); font-family:'Geist',sans-serif; font-weight:700; font-size:14px; color:var(--wl-ink); cursor:pointer; }
        .yr-trigger svg { transition:transform .15s; color:var(--wl-muted); flex-shrink:0; }
        .yr-panel { position:absolute; top:calc(100% + 6px); left:0; min-width:170px; background:var(--wl-surface); border:1px solid var(--wl-line-2); border-radius:12px; box-shadow:0 12px 32px rgba(46,44,80,.16); padding:6px; z-index:50; }
        .yr-opt { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:9px 12px; border-radius:9px; font-family:'Geist',sans-serif; font-weight:700; font-size:14px; color:var(--wl-ink); text-decoration:none; white-space:nowrap; }
        .yr-opt:hover, .yr-opt.is-sel { background:rgba(23,144,123,.1); }
    </style>

    <div style="display:flex;flex-direction:column;gap:20px">
        <a href="{{ route('subjek.index', ['tahun' => $grade->level]) }}" class="wl-back" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Semua subjek') }}</a>

        <div class="wl-subjhead" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <span style="width:56px;height:56px;border-radius:16px;background:{{ $selGrad }};display:grid;place-items:center"><x-subject-icon :subject="$subject" :size="26" /></span>
            <div style="display:flex;flex-direction:column;gap:2px;margin-right:auto">
                <h2 style="margin:0;font-family:'Geist',sans-serif;font-size:26px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ $subject->name }}</h2>
                <span style="font-size:14px;font-weight:700;color:var(--wl-muted)">{{ $grade->displayName() }}</span>
            </div>

            {{-- Content totals for this subject + year. --}}
            <div class="ss-sum">
                <div class="ss-s">
                    <span class="ic" style="background:#DCF2EE;color:#0F7A68"><x-icon name="play" class="h-5 w-5" /></span>
                    <div><div class="n">{{ $totalVideos }}</div><div class="l">{{ __('Jumlah video') }}</div></div>
                </div>
                <div class="ss-div"></div>
                <div class="ss-s">
                    <span class="ic" style="background:#E4EEF9;color:#2E6CA8"><x-icon name="file" class="h-5 w-5" /></span>
                    <div><div class="n">{{ $totalBahan }}</div><div class="l">{{ __('Jumlah bahan') }}</div></div>
                </div>
                <div class="ss-div"></div>
                <div class="ss-s">
                    <span class="ic" style="background:#EDE7F9;color:#7C5CBF"><x-icon name="help-circle" class="h-5 w-5" /></span>
                    <div><div class="n">{{ $totalKuiz }}</div><div class="l">{{ __('Jumlah kuiz') }}</div></div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:5px">
                <span style="font-family:'Geist',sans-serif;font-size:12.5px;font-weight:800;color:var(--wl-ink)">{{ __('Tukar Tahun') }}</span>
                <div class="yr-wrap" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                    <button type="button" class="yr-trigger" @click="open = ! open" :aria-expanded="open">
                        <span>{{ $grade->displayName() }}</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" :style="open ? 'transform:rotate(180deg)' : ''"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="yr-panel" x-show="open" x-cloak>
                        @foreach ($grades as $option)
                            <a href="{{ url('/belajar/'.$subject->slug.'/'.$option->level) }}" @class(['yr-opt', 'is-sel' => $option->level === $grade->level])>
                                {{ $option->displayName() }}
                                @if ($option->level === $grade->level)<x-icon name="check" class="h-4 w-4" style="color:#0F7A68" />@endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if ($chapters->isEmpty())
            <div style="background:var(--wl-surface);border:1px solid var(--wl-line);border-radius:18px;padding:44px;display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center">
                <span style="width:44px;height:44px;border-radius:50%;background:#F1F0E8;display:grid;place-items:center;font-size:18px">📚</span>
                <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:15px;color:var(--wl-ink)">{{ __('Belum ada bab untuk subjek ini') }}</span>
                <span style="font-size:13.5px;color:var(--wl-muted)">{{ __('Cikgu belum menyediakan bab untuk :subject :grade.', ['subject' => $subject->name, 'grade' => $grade->displayName()]) }}</span>
            </div>
        @else
            {{-- A colour cycles per chapter (number badge, icon block, progress bar). The whole card
                 is the link; a watch-progress bar shows once a chapter has videos. --}}
            @php($palette = [
                ['accent' => '#17907B', 'tint' => '#DCF2EE'],
                ['accent' => '#2E6CA8', 'tint' => '#E4EEF9'],
                ['accent' => '#7C5CBF', 'tint' => '#EDE7F9'],
                ['accent' => '#D9862B', 'tint' => '#FBEAD3'],
                ['accent' => '#D9548A', 'tint' => '#FBE0EC'],
            ])
            {{-- Soft pastel backgrounds for the decorative shade area (p = pastel, c = deeper
                 pastel for the circular shapes), one per unit, cycling. --}}
            @php($shadeColors = [
                ['p' => '#E8F8F4', 'c' => '#C9EFE6'],
                ['p' => '#EAF3FF', 'c' => '#CFE2FA'],
                ['p' => '#F3EBFF', 'c' => '#E0CFF6'],
                ['p' => '#FFF2E5', 'c' => '#FBE0C2'],
                ['p' => '#FDEBF3', 'c' => '#F8D2E2'],
                ['p' => '#E5F8F3', 'c' => '#C4EFE4'],
                ['p' => '#EAF3FF', 'c' => '#CFE2FA'],
                ['p' => '#F2EBFC', 'c' => '#DFCFF3'],
            ])
            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach ($chapters as $chapter)
                    @php($total = $chapter->lessons_count)
                    @php($watched = (int) ($watchedByChapter[$chapter->id] ?? 0))
                    @php($wpct = $total > 0 ? (int) round($watched / $total * 100) : 0)
                    @php($c = $palette[$loop->index % count($palette)])
                    @php($sh = $shadeColors[$loop->index % count($shadeColors)])
                    <a href="{{ route('bab.show', $chapter) }}" class="wl-row-lift wl-chaprow"
                       style="background:var(--wl-surface);border:1px solid var(--wl-line);border-radius:16px;padding:14px 18px;display:flex;align-items:center;gap:18px;box-shadow:0 3px 12px rgba(46,44,80,.04);cursor:pointer;text-decoration:none;position:relative;overflow:hidden">
                        <span style="width:52px;height:52px;border-radius:14px;background:{{ $c['tint'] }};color:{{ $c['accent'] }};display:grid;place-items:center;font-family:'Geist',sans-serif;font-weight:800;font-size:20px;flex-shrink:0">{{ $chapter->number }}</span>
                        <div style="display:flex;flex-direction:column;gap:7px;margin-right:auto;min-width:0">
                            <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:17px;color:var(--wl-ink)">{{ $chapter->title }}</span>
                            <div style="display:flex;align-items:center;gap:18px;font-size:13px;font-weight:700;color:var(--wl-muted-2);flex-wrap:wrap">
                                <span style="display:inline-flex;align-items:center;gap:6px"><x-icon name="video" style="width:16px;height:16px;color:#17907B" />{{ $chapter->lessons_count }} video</span>
                                <span style="display:inline-flex;align-items:center;gap:6px"><x-icon name="file" style="width:16px;height:16px;color:#2E6CA8" />{{ $chapter->materials_count }} {{ __('bahan') }}</span>
                                <span style="display:inline-flex;align-items:center;gap:6px"><x-icon name="help-circle" style="width:16px;height:16px;color:#7C5CBF" />{{ $chapter->quizzes_count }} {{ __('kuiz') }}</span>
                            </div>
                        </div>

                        {{-- Decorative pastel shade (gradient + circular shapes via CSS, no icon). --}}
                        <span class="ss-ico" style="--p:{{ $sh['p'] }};--c:{{ $sh['c'] }}"></span>

                        @if (auth()->user()->isStudent() && $total > 0)
                            <div class="ss-prog">
                                <div style="display:flex;justify-content:space-between;font-family:'Geist',sans-serif;font-size:12.5px;font-weight:800;color:var(--wl-muted-2)">
                                    <span>{{ __('Ditonton') }}</span><span>{{ $watched }}/{{ $total }}</span>
                                </div>
                                <div style="height:6px;border-radius:999px;background:#EFEEE6;overflow:hidden">
                                    <div style="height:100%;border-radius:999px;background:{{ $c['accent'] }};width:{{ $wpct }}%"></div>
                                </div>
                            </div>
                        @endif

                        <span class="ss-chev" style="background:{{ $c['tint'] }};color:{{ $c['accent'] }}"><x-icon name="chevron-right" class="h-5 w-5" /></span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-dynamic-component>
