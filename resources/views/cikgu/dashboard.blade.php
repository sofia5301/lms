@php($teacher = auth()->user())

<x-cikgu-layout
    :title="__('Utama')"
    :heading="__('Selamat datang, Cikgu :name', ['name' => $teacher->username])"
    heading-icon="wave.png"
    :sub="__('Ringkasan kelas anda pada hari ini, :date', ['date' => now()->translatedFormat('l, j F Y')])">

    <style>
        /* The work on the left, the read-only panels on the right. */
        .dash-grid { display:grid; grid-template-columns:minmax(0,2.1fr) minmax(0,1fr); gap:20px; align-items:start; }
        .dash-col  { display:flex; flex-direction:column; gap:20px; min-width:0; }
        /* Engagement summary: one unified strip - round pastel icon + label-over-number, thin dividers. */
        .dash-strip { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); background:var(--tp-surface); border:1px solid var(--tp-line); border-radius:16px; box-shadow:var(--tp-shadow); overflow:hidden; }
        .dash-stat { display:flex; align-items:center; gap:14px; padding:20px 22px; min-width:0; }
        .dash-stat + .dash-stat { border-left:1px solid var(--tp-line); }
        .dash-stat-ico { width:46px; height:46px; border-radius:50%; display:grid; place-items:center; flex-shrink:0; }
        .dash-stat-text { display:flex; flex-direction:column; gap:2px; min-width:0; }
        .dash-stat-label { font-size:12.5px; font-weight:700; color:var(--tp-muted); line-height:1.25; }
        .dash-stat-value { font-family:'Geist',sans-serif; font-size:26px; font-weight:800; color:var(--tp-ink); line-height:1; }
        .dash-pair { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:20px; align-items:start; }
        @media (max-width: 1180px) {
            .dash-grid { grid-template-columns:1fr; }
        }
        @media (max-width: 900px) {
            .dash-pair { grid-template-columns:1fr; }
            /* Phones: the strip folds to a 2x2 grid with a cross divider, same look otherwise. */
            .dash-strip { grid-template-columns:repeat(2,minmax(0,1fr)); margin-top:12px !important; }
            .dash-stat { padding:16px 14px; gap:11px; }
            .dash-stat + .dash-stat { border-left:none; }
            .dash-stat:nth-child(even) { border-left:1px solid var(--tp-line); }
            .dash-stat:nth-child(n+3) { border-top:1px solid var(--tp-line); }
            .dash-stat-ico { width:40px; height:40px; }
            .dash-stat-ico svg { width:17px; height:17px; }
            .dash-stat-label { font-size:11.5px; }
            .dash-stat-value { font-size:22px; }
            /* Recent-videos rows: views/date/badge drop to their own line so nothing overlaps. */
            .tp-vidrow { flex-wrap:wrap; row-gap:10px; padding:14px 16px; }
            /* Indent the meta row so it lines up with the title/subject (past the 64px thumb + 16px gap). */
            .tp-vidmeta { flex:1 1 100% !important; gap:14px; flex-wrap:wrap; margin-left:80px; }
            .tp-vidmeta .tp-badge { padding:4px 10px; font-size:10.5px; }
            /* Chapter + type always on their own line below the subject pill. */
            .tp-metasep { display:none; }
            .tp-metasub { display:block; margin-top:3px; }
        }

        /* "Prestasi Kandungan": one card holding three tabbed leaderboards. */
        .dash-perf-head { display:flex; align-items:flex-start; gap:14px; padding:20px 22px 0; }
        .dash-perf-ico { color:#17907B; flex-shrink:0; margin-top:2px; }
        .dash-perf-titles { display:flex; flex-direction:column; gap:2px; min-width:0; flex:1; }
        .dash-perf-titles h2 { margin:0; font-family:'Geist',sans-serif; font-size:19px; font-weight:800; color:var(--tp-ink); }
        .dash-perf-titles .sub { font-size:12.5px; color:var(--tp-muted); }
        .dash-perf-all { display:inline-flex; align-items:center; gap:6px; flex-shrink:0; font-family:'Geist',sans-serif; font-size:13px; font-weight:800; color:#17907B; text-decoration:none; white-space:nowrap; }
        .dash-perf-tabs { display:flex; gap:10px; flex-wrap:wrap; padding:16px 22px; }
        .dash-tab { min-height:44px; cursor:pointer; border-radius:999px; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; padding:0 22px; border:1.5px solid var(--tp-line-2); background:var(--tp-surface); color:var(--tp-ink); transition:all .15s; }
        .dash-tab:hover { border-color:var(--tp-teal); color:var(--tp-teal); }
        .dash-tab:focus-visible { outline:2px solid var(--tp-teal); outline-offset:2px; }
        .dash-tab.is-on { background:var(--tp-teal); border-color:var(--tp-teal); color:#fff; }
        .dash-perf-row { display:flex; align-items:center; gap:14px; padding:13px 22px; border-top:1px solid var(--tp-line); }
        .dash-perf-row .rank { width:28px; flex-shrink:0; text-align:center; font-family:'Geist',sans-serif; font-weight:800; font-size:12px; color:var(--tp-muted); }
        .dash-perf-row .rank img { width:28px; height:28px; object-fit:contain; display:block; margin:0 auto; }
        .dash-perf-row .subjico { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; flex-shrink:0; }
        .dash-perf-row .rmid { display:flex; align-items:center; gap:8px 10px; flex-wrap:wrap; min-width:0; flex:1; }
        .dash-perf-row .rtitle { font-family:'Geist',sans-serif; font-weight:800; font-size:14px; color:var(--tp-ink); }
        .dash-perf-row .rpill { border-radius:999px; padding:2px 10px; font-family:'Geist',sans-serif; font-weight:800; font-size:11px; }
        .dash-perf-row .rbab { font-size:12.5px; color:var(--tp-muted); }
        .dash-perf-row .rvalue { margin-left:auto; flex-shrink:0; padding-left:10px; font-family:'Geist',sans-serif; font-weight:800; font-size:14.5px; color:var(--tp-ink); }
        @media (max-width: 640px) {
            .dash-perf-head { padding:16px 16px 0; }
            .dash-perf-tabs { padding:14px 16px; gap:8px; }
            .dash-tab { padding:0 16px; font-size:12.5px; }
            .dash-perf-row { padding:12px 16px; gap:11px; }
        }

        /* Bahan leaderboard beside the pass/fail donut. */
        .dash-split { display:grid; grid-template-columns:minmax(0,1.3fr) minmax(0,1fr); gap:20px; align-items:start; }
        .pf-head { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
        .pf-ico { color:#17907B; display:grid; place-items:center; flex-shrink:0; }
        .pf-head h2 { margin:0; font-family:'Geist',sans-serif; font-size:16px; font-weight:800; color:var(--tp-ink); }
        .pf-body { display:flex; align-items:center; justify-content:center; gap:24px; flex-wrap:wrap; }
        .pf-donut { width:176px; height:176px; border-radius:50%; display:grid; place-items:center; flex-shrink:0; }
        .pf-donut-center { width:118px; height:118px; border-radius:50%; background:var(--tp-surface); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1px; }
        .pf-donut-num { font-family:'Geist',sans-serif; font-size:28px; font-weight:800; color:var(--tp-ink); line-height:1; }
        .pf-donut-sub { font-size:11.5px; color:var(--tp-muted); text-align:center; max-width:92px; line-height:1.2; }
        .pf-legend { display:flex; flex-direction:column; gap:16px; flex:1; min-width:160px; }
        .pf-leg-row { display:flex; align-items:center; gap:10px; }
        .pf-dot { width:14px; height:14px; border-radius:50%; flex-shrink:0; }
        .pf-leg-label { font-family:'Geist',sans-serif; font-weight:800; font-size:15px; color:var(--tp-ink); }
        .pf-leg-val { margin-left:auto; font-family:'Geist',sans-serif; font-weight:800; font-size:16px; }
        .pf-pct { color:var(--tp-muted); font-weight:700; font-size:14px; }
        @media (max-width: 900px) {
            .dash-split { grid-template-columns:1fr; }
        }
    </style>

    {{-- Engagement summary: one strip of totals (views, favourites, downloads, quiz attempts). --}}
    <div class="dash-strip" style="margin-top:26px">
        @foreach ($summary as $s)
            <div class="dash-stat">
                <span class="dash-stat-ico" style="background:{{ $s['tint'] }};color:{{ $s['ink'] }}">
                    <x-icon :name="$s['icon']" class="h-5 w-5" />
                </span>
                <div class="dash-stat-text">
                    <span class="dash-stat-label">{{ $s['label'] }}</span>
                    <span class="dash-stat-value">{{ $s['value'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Content performance: most-watched video, most-favourited video, most-attempted quiz -
         one tabbed card, full width like the summary strip above. --}}
    @php($perfTabs = [$lists[0], $lists[1], $lists[3]])
    <div class="tp-card" style="overflow:hidden;margin-top:20px"
         x-data="{ tab: 0, hrefs: @js([route('cikgu.video.index'), route('cikgu.video.index'), route('cikgu.kuiz.index')]) }">
                <div class="dash-perf-head">
                    <span class="dash-perf-ico"><x-icon name="chart" style="width:26px;height:26px" /></span>
                    <div class="dash-perf-titles">
                        <h2>{{ __('Prestasi Kandungan') }}</h2>
                        <span class="sub">{{ __('Tontonan, tanda suka dan percubaan kuiz oleh murid anda.') }}</span>
                    </div>
                    <a :href="hrefs[tab]" class="dash-perf-all">{{ __('Lihat semua') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>

                <div class="dash-perf-tabs" role="tablist">
                    @foreach ($perfTabs as $ti => $t)
                        <button type="button" role="tab" class="dash-tab" :class="{ 'is-on': tab === {{ $ti }} }"
                                :aria-selected="tab === {{ $ti }} ? 'true' : 'false'" @click="tab = {{ $ti }}">{{ $t['title'] }}</button>
                    @endforeach
                </div>

                @foreach ($perfTabs as $ti => $t)
                    <div x-show="tab === {{ $ti }}" x-cloak role="tabpanel">
                        @forelse ($t['items'] as $i => $item)
                            <div class="dash-perf-row">
                                <span class="rank">
                                    @if ($i < 3)
                                        <img src="{{ asset('images/medal'.($i + 1).'.png') }}" alt="{{ $i + 1 }}">
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </span>
                                <span class="subjico" style="background:rgb({{ $item['subject']->rgb }} / .14)"><x-icon :name="$item['subject']->iconName()" class="h-[18px] w-[18px]" style="color:rgb({{ $item['subject']->rgb }})" /></span>
                                <div class="rmid">
                                    <span class="rtitle">{{ $item['title'] }}</span>
                                    <span class="rpill" style="background:color-mix(in oklab, {{ $item['subject']->color ?: '#17907B' }} var(--pill-bw), var(--pill-bb));color:color-mix(in oklab, {{ $item['subject']->color ?: '#17907B' }} var(--pill-fw), var(--pill-fb))">{{ $item['subject']->name }}</span>
                                    <span class="rbab">{{ $item['detail'] }}</span>
                                </div>
                                <span class="rvalue">{{ $item['value'] }}</span>
                            </div>
                        @empty
                            <div style="padding:26px 22px;text-align:center;display:flex;flex-direction:column;gap:4px">
                                <span style="font-size:13.5px;color:var(--tp-muted)">{{ __('Belum ada data.') }}</span>
                                <span style="font-size:12.5px;color:var(--tp-muted)">{{ $t['empty'] ?? '' }}</span>
                            </div>
                        @endforelse
                    </div>
                @endforeach
    </div>

    {{-- Materials downloads (left) beside the pass/fail donut (right). --}}
    @php($passPct = (int) round($passFail['passed'] / max(1, $passFail['total']) * 100))
    <div class="dash-split" style="margin-top:20px">
        @include('cikgu.partials.dashboard-list', ['list' => $lists[2]])

        {{-- Pass/fail: a donut with the total in the hole and a side legend. --}}
        <div class="tp-card" style="padding:22px">
            <div class="pf-head">
                <span class="pf-ico"><x-icon name="chart" style="width:24px;height:24px" /></span>
                <h2>{{ __('Lulus / Gagal Kuiz') }}</h2>
            </div>
            @if ($passFail['total'] === 0)
                <p style="text-align:center;color:var(--tp-muted);padding:24px 0;font-weight:700">{{ __('Belum ada percubaan kuiz selesai lagi.') }}</p>
            @else
                <div class="pf-body">
                    <div class="pf-donut" style="background:conic-gradient(#0F7A68 0 {{ $passPct }}%, #C24936 {{ $passPct }}% 100%)">
                        <div class="pf-donut-center">
                            <span class="pf-donut-num">{{ number_format($passFail['total']) }}</span>
                            <span class="pf-donut-sub">{{ __('percubaan selesai') }}</span>
                        </div>
                    </div>
                    <div class="pf-legend">
                        <div class="pf-leg-row">
                            <span class="pf-dot" style="background:#0F7A68"></span>
                            <span class="pf-leg-label">{{ __('Lulus') }}</span>
                            <span class="pf-leg-val" style="color:#0F7A68">{{ number_format($passFail['passed']) }} <span class="pf-pct">({{ $passPct }}%)</span></span>
                        </div>
                        <div class="pf-leg-row">
                            <span class="pf-dot" style="background:#C24936"></span>
                            <span class="pf-leg-label">{{ __('Gagal') }}</span>
                            <span class="pf-leg-val" style="color:#C24936">{{ number_format($passFail['failed']) }} <span class="pf-pct">({{ 100 - $passPct }}%)</span></span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent videos --}}
    <div class="tp-card" style="overflow:hidden;margin-top:20px">
                <div style="display:flex;align-items:center;gap:12px;padding:18px 22px;border-bottom:1px solid var(--tp-line)">
                    <h2 class="tp-g" style="font-size:17px;font-weight:800;color:var(--tp-ink);flex:1">{{ __('Video Terbaru Saya') }}</h2>
                    <a href="{{ route('cikgu.video.index') }}" class="tp-g" style="font-size:13px;font-weight:800">{{ __('Lihat semua') }}</a>
                </div>

                @forelse ($recentLessons as $lesson)
                    <div class="tp-row tp-vidrow">
                        <span style="width:64px;height:42px;border-radius:9px;overflow:hidden;background:#E4EEF9;display:grid;place-items:center;color:rgba(66,118,174,.8);flex-shrink:0">
                            @if ($lesson->thumbnailUrl())
                                <img src="{{ $lesson->thumbnailUrl() }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover">
                            @else
                                <x-icon name="play" class="h-4 w-4" />
                            @endif
                        </span>
                        <div style="display:flex;flex-direction:column;gap:2px;min-width:0;flex:1">
                            <a href="{{ route('video.show', $lesson) }}" class="tp-g" style="font-weight:800;font-size:14.5px;color:var(--tp-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $lesson->title }}</a>
                            <span style="font-size:12.5px;color:var(--tp-muted)"><span style="display:inline-block;vertical-align:middle;background:color-mix(in oklab, {{ $lesson->chapter->subject->color ?: '#17907B' }} var(--pill-bw), var(--pill-bb));color:color-mix(in oklab, {{ $lesson->chapter->subject->color ?: '#17907B' }} var(--pill-fw), var(--pill-fb));border-radius:999px;padding:2px 9px;font-weight:800;font-size:11px">{{ $lesson->chapter->subject->name }}</span> · {{ $lesson->chapter->grade->displayName() }} · {{ __('Bab :n', ['n' => $lesson->chapter->number]) }}</span>
                        </div>
                        <div class="tp-vidmeta" style="display:flex;align-items:center;gap:16px;flex-shrink:0">
                            <span class="tp-meta" style="display:inline-flex;align-items:center;gap:4px"><img src="{{ asset('images/eye.png') }}" alt="" style="width:15px;height:15px;object-fit:contain">{{ $lesson->views_count }}</span>
                            <span class="tp-meta">{{ $lesson->updated_at->translatedFormat('j M Y') }}</span>
                            <span class="tp-badge {{ $lesson->is_published ? 'tp-badge-ok' : 'tp-badge-draft' }}">{{ $lesson->is_published ? __('Diterbitkan') : __('Draf') }}</span>
                        </div>
                    </div>
                @empty
                    <div style="padding:28px 22px;text-align:center;color:var(--tp-muted);font-size:14px">{{ __('Belum ada video. Muat naik video pertama anda.') }}</div>
                @endforelse
            </div>

    {{-- Recent quizzes --}}
    <div class="tp-card" style="overflow:hidden;margin-top:20px">
                <div style="display:flex;align-items:center;gap:12px;padding:18px 22px;border-bottom:1px solid var(--tp-line)">
                    <h2 class="tp-g" style="font-size:17px;font-weight:800;color:var(--tp-ink);flex:1">{{ __('Kuiz Saya') }}</h2>
                    <a href="{{ route('cikgu.kuiz.index') }}" class="tp-g" style="font-size:13px;font-weight:800">{{ __('Lihat semua') }}</a>
                </div>

                @forelse ($recentQuizzes as $quiz)
                    @php($pct = $totalStudents > 0 ? min(100, round($quiz->taken_students_count / $totalStudents * 100)) : 0)
                    <div class="tp-row">
                        <span style="width:40px;height:40px;border-radius:11px;background:{{ $quiz->isInteractive() ? '#DCF2EE' : '#E4EEF9' }};display:grid;place-items:center;color:{{ $quiz->isInteractive() ? '#0F7A68' : '#2E6CA8' }};flex-shrink:0">
                            <x-icon name="quiz" class="h-[18px] w-[18px]" />
                        </span>
                        <div style="display:flex;flex-direction:column;gap:2px;min-width:0;flex:1">
                            <span class="tp-g" style="font-weight:800;font-size:14.5px;color:var(--tp-ink)">{{ $quiz->title }}</span>
                            <span style="font-size:12.5px;color:var(--tp-muted)"><span style="display:inline-block;vertical-align:middle;background:color-mix(in oklab, {{ $quiz->chapter->subject->color ?: '#17907B' }} var(--pill-bw), var(--pill-bb));color:color-mix(in oklab, {{ $quiz->chapter->subject->color ?: '#17907B' }} var(--pill-fw), var(--pill-fb));border-radius:999px;padding:2px 9px;font-weight:800;font-size:11px">{{ $quiz->chapter->subject->name }}</span><span class="tp-metasep"> · </span><span class="tp-metasub">{{ __('Bab :n', ['n' => $quiz->chapter->number]) }} · {{ $quiz->isInteractive() ? __('Interaktif') : __('Bercetak') }}</span></span>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px;flex-shrink:0">
                            <span class="tp-meta">{{ __(':taken/:total murid', ['taken' => $quiz->taken_students_count, 'total' => $totalStudents]) }}</span>
                            <div style="width:120px;height:7px;border-radius:999px;background:var(--tp-line);overflow:hidden">
                                <div style="height:100%;border-radius:999px;background:#17907B;width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="padding:28px 22px;text-align:center;color:var(--tp-muted);font-size:14px">{{ __('Belum ada kuiz. Cipta kuiz pertama anda.') }}</div>
                @endforelse
            </div>

</x-cikgu-layout>
