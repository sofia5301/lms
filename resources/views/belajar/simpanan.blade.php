<x-student-layout :title="__('Simpanan Offline')">
    @php($isDark = ($theme ?? 'light') === 'dark')
    @php
        // Merge the uploaded lessons and supporting materials into one Subject -> Bab tree, so a
        // chapter shows its videos and its materials together.
        $tree = [];
        foreach ($lessons as $l) {
            $sid = $l->chapter->subject->id;
            $cid = $l->chapter->id;
            $tree[$sid]['subject'] = $l->chapter->subject;
            $tree[$sid]['chapters'][$cid]['chapter'] = $l->chapter;
            $tree[$sid]['chapters'][$cid]['videos'][] = $l;
        }
        foreach ($materialsByChapter as $mats) {
            $chapter = $mats->first()->chapter;
            $sid = $chapter->subject->id;
            $cid = $chapter->id;
            $tree[$sid]['subject'] = $chapter->subject;
            $tree[$sid]['chapters'][$cid]['chapter'] = $chapter;
            foreach ($mats as $m) {
                $tree[$sid]['chapters'][$cid]['materials'][] = $m;
            }
        }
        // Order subjects by their configured sort order, chapters by number.
        $order = $subjects->pluck('sort_order', 'id');
        uksort($tree, fn ($a, $b) => ($order[$a] ?? 999) <=> ($order[$b] ?? 999));
        foreach ($tree as &$grp) {
            uasort($grp['chapters'], fn ($a, $b) => $a['chapter']->number <=> $b['chapter']->number);
        }
        unset($grp);
    @endphp

    <style>
        .off-wrap { display:flex; flex-direction:column; gap:22px; max-width:1240px; width:100%; margin:0 auto; }

        /* Header */
        .off-head { display:flex; align-items:flex-start; gap:16px; }
        .off-htile { width:54px; height:54px; border-radius:15px; background:{{ $isDark ? 'rgba(23,144,123,.2)' : '#DCF2EE' }}; color:#0F7A68; display:grid; place-items:center; flex-shrink:0; }

        /* Subject accordion (outer) */
        .off-sub { border:1px solid var(--wl-line); border-radius:18px; overflow:hidden; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .off-sbar { display:flex; align-items:center; gap:14px; padding:16px 20px; cursor:pointer; user-select:none; }
        .off-stile { width:44px; height:44px; border-radius:12px; display:grid; place-items:center; flex-shrink:0; }
        .off-sname { font-family:'Geist',sans-serif; font-size:18px; font-weight:800; color:var(--wl-ink); }
        .off-scount { font-size:13px; font-weight:700; color:var(--wl-muted); }
        .off-chev { margin-left:auto; color:var(--wl-muted); display:inline-flex; transition:transform .18s; }
        .off-sbody { padding:14px 16px; display:flex; flex-direction:column; gap:12px; background:{{ $isDark ? 'rgba(255,255,255,.02)' : '#FAFBFC' }}; border-top:1px solid var(--wl-line); }

        /* Bab accordion (inner) */
        .off-bab { border:1px solid var(--wl-line); border-radius:14px; overflow:hidden; background:var(--wl-surface); }
        .off-bbar { display:flex; align-items:center; gap:12px; padding:12px 16px; cursor:pointer; user-select:none; }
        .off-btile { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; flex-shrink:0; }
        .off-bname { font-family:'Geist',sans-serif; font-size:14.5px; font-weight:800; color:var(--wl-ink); }
        .off-bcount { font-size:12.5px; font-weight:700; color:var(--wl-muted); }
        .off-brows { border-top:1px solid var(--wl-line); }

        /* Item row */
        .off-row { display:flex; align-items:center; gap:16px; padding:12px 16px; }
        .off-row + .off-row { border-top:1px solid var(--wl-line); }
        .off-thumb { position:relative; width:128px; height:74px; flex-shrink:0; border-radius:12px; overflow:hidden; display:grid; place-items:center; }
        .off-thumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .off-dur { position:absolute; right:6px; bottom:6px; background:rgba(40,56,74,.88); color:#fff; font-size:10.5px; font-weight:700; border-radius:7px; padding:2px 7px; z-index:1; }
        .off-mid { flex:1; min-width:0; display:flex; flex-direction:column; gap:5px; }
        .off-title { font-family:'Geist',sans-serif; font-weight:800; font-size:15.5px; color:var(--wl-ink); text-decoration:none; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .off-meta { font-size:13px; font-weight:600; color:var(--wl-muted); }
        .off-act { flex-shrink:0; display:inline-flex; align-items:center; gap:8px; min-height:44px; padding:0 20px; border-radius:12px; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; text-decoration:none; }
        .off-dl { background:var(--wl-surface); border:1.5px solid #9FD8CB; color:#0F7A68; }
        .off-dl:hover { background:#F0FAF7; }
        .off-online { background:{{ $isDark ? 'rgba(255,255,255,.04)' : '#F4F5F7' }}; border:1.5px solid var(--wl-line-2); color:var(--wl-muted); cursor:default; }

        @media (max-width:760px) {
            .off-thumb { width:92px; height:56px; }
            .off-title { font-size:14px; }
            .off-act { padding:0 14px; min-height:40px; font-size:12.5px; }
            .off-act span.lbl { display:none; }
        }
    </style>

    <div class="off-wrap" x-data="offlineDownloads()">
        {{-- Header --}}
        <div class="off-head">
            <span class="off-htile"><x-icon name="download" style="width:28px;height:28px" /></span>
            <div style="display:flex;flex-direction:column;gap:4px;min-width:0">
                <h1 style="margin:0;font-family:'Geist',sans-serif;font-size:26px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ __('Simpanan Offline') }}</h1>
                <p style="margin:0;max-width:72ch;font-size:14.5px;font-weight:600;color:var(--wl-muted);line-height:1.5">{{ __('Video dan bahan pembelajaran yang dimuat naik oleh guru untuk ditonton atau dimuat turun.') }}</p>
            </div>
        </div>

        {{-- Dependent Tahun -> Subjek -> Bab filter. --}}
        <x-year-subject-filter variant="student" :all-years="false" with-chapter
            :action="route('simpanan.index')" :grades="$grades" :subjects="$subjects" :filter="$filter" />

        @if (empty($tree))
            <div style="background:var(--wl-surface);border:1px dashed var(--wl-line-3);border-radius:22px;padding:56px;display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center">
                <x-icon name="download" class="h-9 w-9" style="color:var(--wl-muted)" />
                <h3 style="margin:0;font-family:'Geist',sans-serif;font-size:19px;font-weight:800;color:var(--wl-ink)">{{ __('Tiada kandungan untuk Tahun ini lagi.') }}</h3>
                <p style="margin:0;font-size:14.5px;color:var(--wl-muted);max-width:360px">{{ __('Cuba tukar Tahun, subjek atau bab di atas.') }}</p>
            </div>
        @else
            @foreach ($tree as $grp)
                @php($sub = $grp['subject'])
                @php($col = $sub->color ?: '#17907B')
                @php($barBg = "color-mix(in oklab, {$col} var(--pill-bw), var(--pill-bb))")
                @php($barFg = "color-mix(in oklab, {$col} var(--pill-fw), var(--pill-fb))")
                @php($tintBar = "color-mix(in oklab, {$col} 13%, var(--wl-surface))")
                @php($vCount = collect($grp['chapters'])->sum(fn ($c) => count($c['videos'] ?? [])))
                @php($mCount = collect($grp['chapters'])->sum(fn ($c) => count($c['materials'] ?? [])))
                <div class="off-sub" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    <div class="off-sbar" style="background:{{ $tintBar }}" @click="open = !open">
                        <span class="off-stile" style="background:{{ $barBg }};color:{{ $barFg }}"><x-subject-icon :subject="$sub" :size="22" /></span>
                        <span class="off-sname">{{ $sub->displayName() }}</span>
                        <span class="off-scount">{{ $vCount }} {{ __('video') }} · {{ $mCount }} {{ __('bahan') }}</span>
                        <span class="off-chev" :style="open ? 'transform:rotate(180deg)' : ''"><x-icon name="chevron-down" class="h-5 w-5" /></span>
                    </div>

                    <div class="off-sbody" x-show="open">
                        @foreach ($grp['chapters'] as $c)
                            @php($chapter = $c['chapter'])
                            @php($videos = $c['videos'] ?? [])
                            @php($materials = $c['materials'] ?? [])
                            @php($babTint = "color-mix(in oklab, {$col} 9%, var(--wl-surface))")
                            <div class="off-bab" x-data="{ bopen: {{ $loop->parent->first && $loop->first ? 'true' : 'false' }} }">
                                <div class="off-bbar" style="background:{{ $babTint }}" @click="bopen = !bopen">
                                    <span class="off-btile" style="background:{{ $barBg }};color:{{ $barFg }}"><x-icon name="briefcase" class="h-[18px] w-[18px]" /></span>
                                    <span class="off-bname">{{ __('Bab :n', ['n' => $chapter->number]) }} — {{ $chapter->title }}</span>
                                    <span class="off-bcount">{{ count($videos) }} {{ __('video') }} · {{ count($materials) }} {{ __('bahan') }}</span>
                                    <span class="off-chev" :style="bopen ? 'transform:rotate(180deg)' : ''"><x-icon name="chevron-down" class="h-5 w-5" /></span>
                                </div>

                                <div class="off-brows" x-show="bopen">
                                    {{-- Videos --}}
                                    @foreach ($videos as $lesson)
                                        @php($grad = "linear-gradient(135deg, color-mix(in oklab, {$col} 30%, #fff), color-mix(in oklab, {$col} 12%, #fff))")
                                        <div class="off-row">
                                            <a href="{{ route('video.show', $lesson) }}" class="off-thumb" style="background:{{ $grad }}">
                                                @if ($lesson->thumbnailUrl())
                                                    <img src="{{ $lesson->thumbnailUrl() }}" alt="" loading="lazy">
                                                @else
                                                    <x-subject-icon :subject="$sub" :size="28" />
                                                @endif
                                                @if ($lesson->durationLabel())
                                                    <span class="off-dur">{{ $lesson->durationLabel() }}</span>
                                                @endif
                                            </a>
                                            <div class="off-mid">
                                                <a href="{{ route('video.show', $lesson) }}" class="off-title">{{ $lesson->title }}</a>
                                                <span class="off-meta">{{ __('Video') }}@if ($lesson->durationLabel()) · {{ $lesson->durationLabel() }}@endif @if ($lesson->teacher)· {{ __('Guru: :name', ['name' => $lesson->teacher->name]) }}@endif</span>
                                            </div>
                                            @if ($lesson->isUpload())
                                                <a href="{{ route('muat-turun.video', $lesson) }}" @click="remember(@js($lesson->title))" class="off-act off-dl"><x-icon name="download" style="width:17px;height:17px" /><span class="lbl">{{ __('Muat Turun') }}</span></a>
                                            @else
                                                <span class="off-act off-online"><x-icon name="lock" style="width:16px;height:16px" /><span class="lbl">{{ __('Dalam talian sahaja') }}</span></span>
                                            @endif
                                        </div>
                                    @endforeach

                                    {{-- Materials --}}
                                    @foreach ($materials as $material)
                                        <div class="off-row">
                                            <span class="off-thumb" style="background:{{ $isDark ? 'rgba(224,86,78,.14)' : '#FDECEA' }};color:#E0564E"><x-icon :name="$material->iconName()" style="width:30px;height:30px" /></span>
                                            <div class="off-mid">
                                                <span class="off-title">{{ $material->title }}</span>
                                                <span class="off-meta">{{ __('Bahan sokongan') }} · {{ $material->humanSize() }}@if ($material->teacher) · {{ __('Guru: :name', ['name' => $material->teacher->name]) }}@endif</span>
                                            </div>
                                            <a href="{{ route('muat-turun.bahan', $material) }}" @click="remember(@js($material->title))" class="off-act off-dl"><x-icon name="download" style="width:17px;height:17px" /><span class="lbl">{{ __('Muat Turun') }}</span></a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @push('scripts')
        <script>
            function offlineDownloads() {
                return {
                    downloads: JSON.parse(localStorage.getItem('my-downloads') || '[]'),
                    remember(title) {
                        const entry = { title, at: new Date().toLocaleDateString() };
                        this.downloads = [entry, ...this.downloads.filter(d => d.title !== title)].slice(0, 50);
                        localStorage.setItem('my-downloads', JSON.stringify(this.downloads));
                    },
                    clearAll() {
                        this.downloads = [];
                        localStorage.removeItem('my-downloads');
                    },
                };
            }
        </script>
    @endpush
</x-student-layout>
