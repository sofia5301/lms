<x-student-layout :title="__('Sambung Menonton')">
    @php($isDark = ($theme ?? 'light') === 'dark')
    @php($user = auth()->user())

    <style>
        .sm-wrap { display:flex; flex-direction:column; gap:22px; max-width:1240px; width:100%; margin:0 auto; }
        .sm-head { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
        .sm-htile { width:50px; height:50px; border-radius:14px; background:{{ $isDark ? 'rgba(23,144,123,.22)' : '#17907B' }}; color:#fff; display:grid; place-items:center; flex-shrink:0; }
        .sm-htxt { display:flex; flex-direction:column; gap:2px; min-width:0; }
        .sm-tools { margin-left:auto; display:flex; align-items:center; gap:10px; }
        .sm-search { position:relative; }
        .sm-search input { min-height:44px; border:1.5px solid var(--wl-line-2); border-radius:12px; background:var(--wl-surface); color:var(--wl-ink); font-family:'Nunito',sans-serif; font-size:16px; font-weight:600; padding:0 16px 0 40px; width:min(320px,60vw); }
        .sm-search .ic { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--wl-muted-2); pointer-events:none; }
        .sm-toggle { display:flex; border:1.5px solid var(--wl-line-2); border-radius:12px; overflow:hidden; flex-shrink:0; }
        .sm-tgl { width:44px; height:44px; display:grid; place-items:center; cursor:pointer; border:none; background:var(--wl-surface); color:var(--wl-muted); }
        .sm-tgl.on { background:#17907B; color:#fff; }

        .sm-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:18px; }
        @media (max-width:1100px) { .sm-grid { grid-template-columns:repeat(3,minmax(0,1fr)); } }
        @media (max-width:820px) { .sm-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:520px) { .sm-grid { grid-template-columns:1fr; } }
        .sm-list { display:flex; flex-direction:column; gap:14px; }

        .sm-card { display:flex; flex-direction:column; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:18px; overflow:hidden; box-shadow:0 4px 16px rgba(46,44,80,.05); }
        .sm-thumb { position:relative; aspect-ratio:16/10; background:linear-gradient(135deg,#C4DCF2,#A5C9EA); display:block; text-decoration:none; flex-shrink:0; }
        .sm-thumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .sm-play { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:44px; height:44px; border-radius:50%; background:rgba(255,255,255,.9); display:grid; place-items:center; color:#2E6CA8; z-index:1; }
        .sm-time { position:absolute; right:9px; bottom:9px; background:rgba(20,28,38,.82); color:#fff; font-size:11px; font-weight:700; border-radius:7px; padding:3px 9px; z-index:1; }
        .sm-yt { position:absolute; left:9px; bottom:9px; display:inline-flex; align-items:center; color:#FF0000; background:#fff; border-radius:6px; padding:2px 5px; z-index:1; }
        .sm-fav { position:absolute; right:10px; top:10px; width:34px; height:34px; border-radius:50%; border:none; cursor:pointer; background:rgba(255,255,255,.92); display:grid; place-items:center; z-index:2; box-shadow:0 2px 8px var(--wl-line-3); }
        .sm-body { padding:13px 15px 15px; display:flex; flex-direction:column; gap:9px; flex:1; }
        .sm-prog { display:flex; align-items:center; gap:10px; }
        .sm-bar { flex:1; height:7px; border-radius:999px; background:{{ $isDark ? 'var(--wl-line-2)' : '#E8EDF2' }}; overflow:hidden; }
        .sm-bar > i { display:block; height:100%; border-radius:999px; background:#17907B; }
        .sm-pct { font-size:11.5px; font-weight:700; color:var(--wl-muted); white-space:nowrap; }
        .sm-title { font-family:'Geist',sans-serif; font-weight:800; font-size:15px; color:var(--wl-ink); line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; text-decoration:none; }
        .sm-meta { margin-top:auto; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .sm-sub { border-radius:999px; padding:4px 12px; font-family:'Geist',sans-serif; font-size:11.5px; font-weight:800; }
        .sm-bab { background:var(--wl-chip,#F1F0E8); color:var(--wl-muted); border-radius:999px; padding:4px 12px; font-family:'Geist',sans-serif; font-size:11.5px; font-weight:800; }

        /* List view: horizontal card */
        .sm-list .sm-card { flex-direction:row; }
        .sm-list .sm-thumb { width:240px; aspect-ratio:16/9; }
        @media (max-width:560px) { .sm-list .sm-thumb { width:130px; } }
    </style>

    <div class="sm-wrap" x-data="{ q: '' }">
        <div class="sm-head">
            <span class="sm-htile"><x-icon name="play" style="width:24px;height:24px;margin-left:2px" /></span>
            <div class="sm-htxt">
                <h1 style="margin:0;font-family:'Geist',sans-serif;font-size:24px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ __('Sambung Menonton') }}</h1>
                <span style="font-size:14px;color:var(--wl-muted)">{{ __('Video yang belum habis anda tonton.') }}</span>
            </div>
            @if ($lessons->isNotEmpty())
                <div class="sm-tools">
                    <div class="sm-search">
                        <span class="ic"><x-icon name="search" class="h-[18px] w-[18px]" /></span>
                        <input type="text" x-model="q" placeholder="{{ __('Cari dalam senarai ini...') }}" aria-label="{{ __('Cari') }}">
                    </div>
                </div>
            @endif
        </div>

        @if ($lessons->isEmpty())
            <div style="background:var(--wl-surface);border:1px dashed var(--wl-line-3);border-radius:22px;padding:56px;display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center;max-width:520px;margin:0 auto">
                <x-icon name="video" class="h-9 w-9" style="color:var(--wl-muted)" />
                <h3 style="margin:0;font-family:'Geist',sans-serif;font-size:19px;font-weight:800;color:var(--wl-ink)">{{ __('Tiada video untuk disambung') }}</h3>
                <p style="margin:0;font-size:14.5px;color:var(--wl-muted);max-width:360px">{{ __('Mula tonton sesuatu, dan ia akan muncul di sini supaya anda boleh sambung kemudian.') }}</p>
                <a href="{{ route('belajar.index') }}" class="wl-btn-primary" style="margin-top:6px;min-height:46px;display:inline-flex;align-items:center;border-radius:12px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:14.5px;padding:0 22px;text-decoration:none">{{ __('Cari Video') }}</a>
            </div>
        @else
            <div class="sm-grid">
                @foreach ($lessons as $lesson)
                    @php($lsub = $lesson->chapter->subject)
                    @php($lcol = $lsub->color ?: '#17907B')
                    @php($prog = $lesson->progress->first())
                    @php($pct = $prog && $prog->percent ? min(100, $prog->percent) : 0)
                    @php($fav = $lesson->isFavouritedBy($user))
                    <div class="sm-card" data-title="{{ \Illuminate\Support\Str::lower($lesson->title) }}" x-show="q === '' || $el.dataset.title.includes(q.toLowerCase())">
                        <a href="{{ route('video.show', $lesson) }}" class="sm-thumb">
                            @if ($lesson->thumbnailUrl())
                                <img src="{{ $lesson->thumbnailUrl() }}" alt="" loading="lazy">
                            @endif
                            <span class="sm-play"><x-icon name="play" class="h-[17px] w-[17px]" style="margin-left:2px" /></span>
                            @if ($lesson->isYoutube())
                                <span class="sm-yt"><x-icon name="youtube" class="h-[15px] w-[15px]" /></span>
                            @endif
                            @if ($prog)
                                <span class="sm-time">{{ $prog->positionLabel() }} / {{ $prog->durationLabel() }}</span>
                            @elseif ($lesson->durationLabel())
                                <span class="sm-time">{{ $lesson->durationLabel() }}</span>
                            @endif
                        </a>
                        <button type="button" class="sm-fav fav-btn" data-fav="{{ $fav ? 'true' : 'false' }}"
                                data-add="{{ route('kegemaran.simpan', $lesson) }}" data-remove="{{ route('kegemaran.padam', $lesson) }}"
                                onclick="wlToggleFav(event, this)" style="color:{{ $fav ? '#EB5E5A' : '#6C6F87' }}" aria-label="{{ __('Kegemaran') }}"><x-icon name="heart" class="h-[18px] w-[18px]" style="fill:{{ $fav ? 'currentColor' : 'none' }}" /></button>

                        <div class="sm-body">
                            <div class="sm-prog">
                                <div class="sm-bar"><i style="width:{{ max(3, $pct) }}%"></i></div>
                                <span class="sm-pct">{{ $pct }}% {{ __('ditonton') }}</span>
                            </div>
                            <a href="{{ route('video.show', $lesson) }}" class="sm-title">{{ $lesson->title }}</a>
                            <div class="sm-meta">
                                <span class="sm-sub" style="background:color-mix(in oklab, {{ $lcol }} var(--pill-bw), var(--pill-bb));color:color-mix(in oklab, {{ $lcol }} var(--pill-fw), var(--pill-fb))">{{ $lsub->displayName() }}</span>
                                <span class="sm-bab">{{ __('Bab :n', ['n' => $lesson->chapter->number]) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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
                    .then(r => { if (!r.ok) throw new Error('failed'); })
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
