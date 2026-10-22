<x-student-layout :title="__('Simpanan Offline')">
    @php($isDark = ($theme ?? 'light') === 'dark')
    @php
        // Merge the uploaded lessons and supporting materials into one Subject -> Bab tree, so a
        // chapter shows its videos and its materials together.
        $tree = [];
        foreach ($lessons as $l) {
            $chapter = $l->chapter;
            $subject = $chapter ? $chapter->subject : null;
            if (! $subject) { continue; }
            $sid = $subject->id;
            $cid = $chapter->id;
            $tree[$sid]['subject'] = $subject;
            $tree[$sid]['chapters'][$cid]['chapter'] = $chapter;
            $tree[$sid]['chapters'][$cid]['number'] = $chapter->number;
            $tree[$sid]['chapters'][$cid]['videos'][] = $l;
        }
        foreach ($materialsByChapter as $mats) {
            $first = $mats->first();
            $chapter = $first ? $first->chapter : null;
            $subject = $chapter ? $chapter->subject : null;
            if (! $subject) { continue; }
            $sid = $subject->id;
            $cid = $chapter->id;
            $tree[$sid]['subject'] = $subject;
            $tree[$sid]['chapters'][$cid]['chapter'] = $chapter;
            $tree[$sid]['chapters'][$cid]['number'] = $chapter->number;
            foreach ($mats as $mm) {
                $tree[$sid]['chapters'][$cid]['materials'][] = $mm;
            }
        }
        $order = $subjects->pluck('sort_order', 'id')->all();
        uksort($tree, function ($a, $b) use ($order) {
            return ($order[$a] ?? 999) <=> ($order[$b] ?? 999);
        });
        foreach (array_keys($tree) as $sid) {
            uasort($tree[$sid]['chapters'], function ($a, $b) {
                return ($a['number'] ?? 0) <=> ($b['number'] ?? 0);
            });
            $v = 0;
            $m = 0;
            foreach ($tree[$sid]['chapters'] as $cc) {
                $v += isset($cc['videos']) ? count($cc['videos']) : 0;
                $m += isset($cc['materials']) ? count($cc['materials']) : 0;
            }
            $tree[$sid]['vcount'] = $v;
            $tree[$sid]['mcount'] = $m;
        }
    @endphp

    <style>
        .off-wrap { display:flex; flex-direction:column; gap:22px; max-width:1240px; width:100%; margin:0 auto; }
        .off-head { display:flex; align-items:flex-start; gap:16px; }
        .off-htile { width:54px; height:54px; border-radius:15px; background:{{ $isDark ? 'rgba(23,144,123,.2)' : '#DCF2EE' }}; color:#0F7A68; display:grid; place-items:center; flex-shrink:0; }

        .off-sub { border:1px solid var(--wl-line); border-radius:18px; overflow:hidden; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .off-sbar { display:flex; align-items:center; gap:14px; padding:16px 20px; cursor:pointer; user-select:none; }
        .off-stile { width:44px; height:44px; border-radius:12px; display:grid; place-items:center; flex-shrink:0; }
        .off-sname { font-family:'Geist',sans-serif; font-size:18px; font-weight:800; color:var(--wl-ink); }
        .off-scount { font-size:13px; font-weight:700; color:var(--wl-muted); }
        .off-chev { margin-left:auto; color:var(--wl-muted); display:inline-flex; transition:transform .18s; }
        .off-sbody { padding:14px 16px; display:flex; flex-direction:column; gap:12px; background:{{ $isDark ? 'rgba(255,255,255,.02)' : '#FAFBFC' }}; border-top:1px solid var(--wl-line); }

        .off-bab { border:1px solid var(--wl-line); border-radius:14px; overflow:hidden; background:var(--wl-surface); }
        .off-bbar { display:flex; align-items:center; gap:12px; padding:12px 16px; cursor:pointer; user-select:none; }
        .off-btile { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; flex-shrink:0; }
        .off-bname { font-family:'Geist',sans-serif; font-size:14.5px; font-weight:800; color:var(--wl-ink); }
        .off-bcount { font-size:12.5px; font-weight:700; color:var(--wl-muted); }
        .off-brows { border-top:1px solid var(--wl-line); }

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
        <div class="off-head">
            <span class="off-htile"><x-icon name="download" style="width:28px;height:28px" /></span>
            <div style="display:flex;flex-direction:column;gap:4px;min-width:0">
                <h1 style="margin:0;font-family:'Geist',sans-serif;font-size:26px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ __('Simpanan Offline') }}</h1>
                <p style="margin:0;max-width:72ch;font-size:14.5px;font-weight:600;color:var(--wl-muted);line-height:1.5">{{ __('Video dan bahan pembelajaran yang dimuat naik oleh guru untuk ditonton atau dimuat turun.') }}</p>
            </div>
        </div>

        <div style="background:#FEF0CE;border-radius:10px;padding:8px 14px;font-size:12px;font-weight:700;color:#8A6A12">DEBUG: filter removed</div>

        @if (empty($tree))
            <div style="background:var(--wl-surface);border:1px dashed var(--wl-line-3);border-radius:22px;padding:56px;display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center">
                <x-icon name="download" class="h-9 w-9" style="color:var(--wl-muted)" />
                <h3 style="margin:0;font-family:'Geist',sans-serif;font-size:19px;font-weight:800;color:var(--wl-ink)">{{ __('Tiada kandungan untuk Tahun ini lagi.') }}</h3>
                <p style="margin:0;font-size:14.5px;color:var(--wl-muted);max-width:360px">{{ __('Cuba tukar Tahun, subjek atau bab di atas.') }}</p>
            </div>
        @else
            @foreach ($tree as $grp)
                @php($subjectFirst = $loop->first)
                @php($sub = $grp['subject'])
                @php($col = $sub->color ?: '#17907B')
                <div class="off-sub" x-data="{ open: {{ $subjectFirst ? 'true' : 'false' }} }">
                    <div class="off-sbar" style="background:color-mix(in oklab, {{ $col }} 13%, var(--wl-surface))" @click="open = !open">
                        <span class="off-stile" style="background:color-mix(in oklab, {{ $col }} var(--pill-bw), var(--pill-bb));color:color-mix(in oklab, {{ $col }} var(--pill-fw), var(--pill-fb))"><x-subject-icon :subject="$sub" :size="22" /></span>
                        <span class="off-sname">{{ $sub->displayName() }}</span>
                        <span class="off-scount">{{ $grp['vcount'] ?? 0 }} {{ __('video') }} · {{ $grp['mcount'] ?? 0 }} {{ __('bahan') }}</span>
                        <span class="off-chev" :style="open ? 'transform:rotate(180deg)' : ''"><x-icon name="chevron-down" class="h-5 w-5" /></span>
                    </div>

                    <div class="off-sbody" x-show="open">
                        @foreach ($grp['chapters'] as $c)
                            @php($chapterFirst = $loop->first)
                            @php($chapter = $c['chapter'])
                            @php($videos = isset($c['videos']) ? $c['videos'] : [])
                            @php($materials = isset($c['materials']) ? $c['materials'] : [])
                            <div class="off-bab" x-data="{ bopen: {{ $subjectFirst && $chapterFirst ? 'true' : 'false' }} }">
                                <div class="off-bbar" style="background:color-mix(in oklab, {{ $col }} 9%, var(--wl-surface))" @click="bopen = !bopen">
                                    <span class="off-btile" style="background:color-mix(in oklab, {{ $col }} var(--pill-bw), var(--pill-bb));color:color-mix(in oklab, {{ $col }} var(--pill-fw), var(--pill-fb))"><x-icon name="briefcase" class="h-[18px] w-[18px]" /></span>
                                    <span class="off-bname">{{ __('Bab :n', ['n' => $chapter->number]) }} — {{ $chapter->title }}</span>
                                    <span class="off-bcount">{{ count($videos) }} {{ __('video') }} · {{ count($materials) }} {{ __('bahan') }}</span>
                                    <span class="off-chev" :style="bopen ? 'transform:rotate(180deg)' : ''"><x-icon name="chevron-down" class="h-5 w-5" /></span>
                                </div>

                                <div class="off-brows" x-show="bopen" style="padding:10px 16px;color:var(--wl-muted);font-size:13px">{{ count($videos) }} video · {{ count($materials) }} bahan (rows hidden for debug)</div>
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
                    remember() {},
                };
            }
        </script>
    @endpush
</x-student-layout>
