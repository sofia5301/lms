<x-student-layout :title="__('Kegemaran Saya')">
    <style>
        .fav-wrap { display:flex; flex-direction:column; gap:20px; max-width:1180px; width:100%; margin:0 auto; }

        /* Header */
        .fav-head { display:flex; align-items:flex-start; gap:16px; }
        .fav-htile { width:52px; height:52px; border-radius:15px; background:{{ ($theme ?? 'light') === 'dark' ? 'rgba(23,144,123,.2)' : '#DCF2EE' }}; color:#0F7A68; display:grid; place-items:center; flex-shrink:0; }

        /* Group: header bar + rows share one bordered, rounded card. */
        .fav-group { display:flex; flex-direction:column; border:1px solid var(--wl-line); border-radius:16px; overflow:hidden; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .fav-gbar { display:flex; align-items:center; gap:12px; padding:12px 16px; cursor:pointer; user-select:none; }
        .fav-gtile { width:38px; height:38px; border-radius:11px; display:grid; place-items:center; flex-shrink:0; }
        .fav-gname { font-family:'Geist',sans-serif; font-size:15.5px; font-weight:800; color:var(--wl-ink); }
        .fav-gcount { font-size:12.5px; font-weight:700; color:var(--wl-muted); }
        .fav-chev { margin-left:auto; color:var(--wl-muted); display:inline-flex; transition:transform .18s; }

        /* Rows: no gap, divided by a line, sitting directly under the header bar. */
        .fav-rows { display:flex; flex-direction:column; background:var(--wl-surface); border-top:1px solid var(--wl-line); }
        .fav-row { display:flex; align-items:center; gap:16px; background:var(--wl-surface); padding:12px 16px; }
        .fav-row + .fav-row { border-top:1px solid var(--wl-line); }
        .fav-thumb { position:relative; width:150px; height:84px; flex-shrink:0; border-radius:12px; overflow:hidden; display:grid; place-items:center; text-decoration:none; }
        .fav-thumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .fav-dur { position:absolute; right:7px; bottom:7px; background:rgba(40,56,74,.88); color:#fff; font-size:11px; font-weight:700; border-radius:7px; padding:2px 7px; z-index:1; }
        .fav-mid { flex:1; min-width:0; display:flex; flex-direction:column; gap:9px; }
        .fav-title { font-family:'Geist',sans-serif; font-weight:800; font-size:16px; color:var(--wl-ink); line-height:1.3; text-decoration:none; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .fav-meta { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .fav-pill { border-radius:999px; padding:4px 12px; font-family:'Geist',sans-serif; font-size:12px; font-weight:800; }
        .fav-sub { font-size:13px; font-weight:700; color:var(--wl-muted); }
        .fav-views { display:inline-flex; align-items:center; gap:5px; font-size:13px; font-weight:700; color:var(--wl-muted); }

        .fav-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
        .fav-btn { width:40px; height:40px; border-radius:50%; border:none; cursor:pointer; background:transparent; display:grid; place-items:center; }
        .fav-kebab { width:36px; height:36px; border-radius:50%; border:none; cursor:pointer; background:transparent; color:var(--wl-muted); display:grid; place-items:center; }
        .fav-kebab:hover { background:var(--wl-line); }
        .fav-menu { position:absolute; top:42px; right:0; z-index:20; min-width:190px; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:14px; box-shadow:0 12px 30px rgba(46,44,80,.16); padding:6px; display:flex; flex-direction:column; }
        .fav-mitem { display:flex; align-items:center; gap:10px; border:none; background:transparent; cursor:pointer; text-align:left; width:100%; border-radius:10px; padding:10px 12px; font-family:'Nunito',sans-serif; font-size:13.5px; font-weight:700; color:var(--wl-ink); text-decoration:none; }
        .fav-mitem:hover { background:var(--wl-line); }
        .fav-mitem.danger { color:#C24936; }

        @media (max-width:760px) {
            .fav-thumb { width:112px; height:64px; }
            .fav-title { font-size:14px; }
            .fav-meta { gap:8px; }
            .fav-pill { font-size:11px; padding:3px 10px; }
        }
    </style>

    <div class="fav-wrap">
        <div class="fav-head">
            <span class="fav-htile"><x-icon name="heart" style="width:26px;height:26px" /></span>
            <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                <h2 style="margin:0;font-family:'Geist',sans-serif;font-size:24px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ __('Kegemaran Saya') }}</h2>
                <span style="font-size:14px;font-weight:700;color:var(--wl-muted)">{{ $lessons->count() }} {{ __('video') }}</span>
                <span style="font-size:14px;color:var(--wl-muted)">{{ __('Video yang anda tandakan sebagai kegemaran.') }}</span>
            </div>
        </div>

        @if ($lessons->isEmpty())
            <div style="background:var(--wl-surface);border:1px dashed var(--wl-line-3);border-radius:22px;padding:56px;display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center">
                <x-icon name="heart" class="h-9 w-9" style="color:#EB5E5A;fill:currentColor" />
                <h3 style="margin:0;font-family:'Geist',sans-serif;font-size:19px;font-weight:800;color:var(--wl-ink)">{{ __('Tiada kegemaran lagi') }}</h3>
                <p style="margin:0;font-size:14.5px;color:var(--wl-muted);max-width:360px">{{ __('Klik ikon ♡ pada mana-mana video untuk menyimpannya di sini.') }}</p>
            </div>
        @else
            @foreach ($lessons->groupBy(fn ($l) => $l->chapter->subject->id) as $items)
                @php($sub = $items->first()->chapter->subject)
                @php($col = $sub->color ?: '#17907B')
                @php($barBg = "color-mix(in oklab, {$col} var(--pill-bw), var(--pill-bb))")
                @php($barFg = "color-mix(in oklab, {$col} var(--pill-fw), var(--pill-fb))")
                @php($tintBar = "color-mix(in oklab, {$col} 14%, var(--wl-surface))")
                <div class="fav-group" x-data="{ open: true }">
                    <div class="fav-gbar" style="background:{{ $tintBar }}" @click="open = !open">
                        <span class="fav-gtile" style="background:{{ $barBg }};color:{{ $barFg }}"><x-subject-icon :subject="$sub" :size="18" /></span>
                        <span class="fav-gname">{{ $sub->displayName() }}</span>
                        <span class="fav-gcount">{{ $items->count() }} {{ __('video') }}</span>
                        <span class="fav-chev" :style="open ? 'transform:rotate(180deg)' : ''"><x-icon name="chevron-down" class="h-5 w-5" /></span>
                    </div>

                    <div class="fav-rows" x-show="open">
                        @foreach ($items as $lesson)
                            @php($thumb = $lesson->thumbnailUrl())
                            @php($lcol = $lesson->chapter->subject->color ?: '#17907B')
                            @php($grad = "linear-gradient(135deg, color-mix(in oklab, {$lcol} 30%, #fff), color-mix(in oklab, {$lcol} 12%, #fff))")
                            @php($pillBg = "color-mix(in oklab, {$lcol} var(--pill-bw), var(--pill-bb))")
                            @php($pillFg = "color-mix(in oklab, {$lcol} var(--pill-fw), var(--pill-fb))")
                            <div class="fav-row">
                                <a href="{{ route('video.show', $lesson) }}" class="fav-thumb" style="background:{{ $grad }}">
                                    @if ($thumb)
                                        <img src="{{ $thumb }}" alt="" loading="lazy">
                                    @else
                                        <x-icon name="play" class="h-6 w-6" style="color:#fff;position:relative;z-index:1" />
                                    @endif
                                    @if ($lesson->durationLabel())
                                        <span class="fav-dur">{{ $lesson->durationLabel() }}</span>
                                    @endif
                                </a>

                                <div class="fav-mid">
                                    <a href="{{ route('video.show', $lesson) }}" class="fav-title">{{ $lesson->title }}</a>
                                    <div class="fav-meta">
                                        <span class="fav-pill" style="background:{{ $pillBg }};color:{{ $pillFg }}">{{ $sub->displayName() }}</span>
                                        <span class="fav-sub">{{ __('Bab :n', ['n' => $lesson->chapter->number]) }}</span>
                                        <span class="fav-views"><img src="{{ asset('images/eye.png') }}" alt="" style="width:16px;height:16px;object-fit:contain">{{ $lesson->views_count }} {{ __('tontonan') }}</span>
                                    </div>
                                </div>

                                <div class="fav-actions">
                                    <button type="button" class="fav-btn" data-fav="true" title="{{ __('Kegemaran') }}" aria-label="{{ __('Kegemaran') }}"
                                            data-add="{{ route('kegemaran.simpan', $lesson) }}" data-remove="{{ route('kegemaran.padam', $lesson) }}"
                                            onclick="wlToggleFav(event, this)" style="color:#EB5E5A"><x-icon name="heart" class="h-[27px] w-[27px]" style="fill:currentColor" /></button>
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
            function wlToggleFav(e, btn) {
                e.preventDefault();
                e.stopPropagation();
                if (btn.dataset.busy) return;
                const on = btn.dataset.fav === 'true';
                const url = on ? btn.dataset.remove : btn.dataset.add;
                const svg = btn.querySelector('svg');
                btn.dataset.busy = '1';
                btn.dataset.fav = on ? 'false' : 'true';
                if (svg) svg.style.fill = on ? 'none' : 'currentColor';
                btn.style.color = on ? '#6C6F87' : '#EB5E5A';
                const token = document.querySelector('meta[name=csrf-token]')?.content;
                fetch(url, { method: on ? 'DELETE' : 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('failed'); if (on) { const row = btn.closest('.fav-row'); if (row) { row.style.transition = 'opacity .2s'; row.style.opacity = '.45'; } } })
                    .catch(() => {
                        btn.dataset.fav = on ? 'true' : 'false';
                        if (svg) svg.style.fill = on ? 'currentColor' : 'none';
                        btn.style.color = on ? '#EB5E5A' : '#6C6F87';
                    })
                    .finally(() => { delete btn.dataset.busy; });
            }
        </script>
    @endpush
</x-student-layout>
