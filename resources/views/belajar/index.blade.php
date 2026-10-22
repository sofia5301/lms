<x-student-layout :title="__('Utama')">
    @php($isDark = ($theme ?? 'light') === 'dark')

    <style>
        .hm-top { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.32fr); gap:20px; align-items:stretch; }
        @media (max-width:980px) { .hm-top { grid-template-columns:1fr; } }

        /* Hero (left) */
        .hm-hero { display:flex; flex-direction:column; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:22px; overflow:hidden; box-shadow:0 8px 26px rgba(46,44,80,.08); }
        .hm-herothumb { position:relative; display:block; aspect-ratio:16/10; background:linear-gradient(135deg,#C4DCF2,#A5C9EA); text-decoration:none; }
        .hm-herothumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .hm-heroshade { position:absolute; inset:0; background:linear-gradient(180deg, rgba(10,16,24,0) 42%, rgba(10,16,24,.78) 100%); }
        .hm-heroplay { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,.92); display:grid; place-items:center; color:#2E6CA8; z-index:2; }
        .hm-herodur { position:absolute; right:12px; top:12px; background:rgba(20,28,38,.82); color:#fff; font-size:11.5px; font-weight:700; border-radius:8px; padding:3px 9px; z-index:2; }
        .hm-heroover { position:absolute; left:18px; right:18px; bottom:16px; z-index:2; display:flex; flex-direction:column; gap:8px; }
        .hm-heropills { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .hm-pill { background:rgba(255,255,255,.92); color:#2E6CA8; border-radius:999px; padding:4px 12px; font-family:'Geist',sans-serif; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; }
        .hm-pill.bab { color:#4A5A6B; }
        .hm-herotitle { margin:0; font-family:'Geist',sans-serif; font-size:24px; font-weight:800; color:#fff; letter-spacing:-.01em; text-shadow:0 2px 10px rgba(0,0,0,.4); }
        .hm-herobody { padding:18px 18px 26px; display:flex; flex-direction:column; gap:14px; }
        .hm-progress { height:7px; border-radius:999px; background:{{ $isDark ? 'var(--wl-line-2)' : '#E8EDF2' }}; overflow:hidden; }
        .hm-progress > i { display:block; height:100%; border-radius:999px; background:#17907B; }
        .hm-herocta { display:flex; gap:10px; flex-wrap:wrap; margin-top:24px; }
        .hm-btn { min-height:46px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:12px; font-family:'Geist',sans-serif; font-weight:800; font-size:14.5px; padding:0 20px; cursor:pointer; text-decoration:none; }
        .hm-btn-pri { flex:1; background:#17907B; color:#fff; border:none; }
        .hm-btn-sec { background:var(--wl-surface); color:var(--wl-ink); border:1.5px solid var(--wl-line-2); }

        /* Continue list (right) */
        .hm-cont { background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:22px; box-shadow:0 8px 26px rgba(46,44,80,.06); padding:18px 20px; display:flex; flex-direction:column; }
        .hm-conthead { display:flex; align-items:center; gap:10px; margin-bottom:6px; }
        .hm-conticon { width:34px; height:34px; border-radius:10px; background:{{ $isDark ? 'rgba(96,140,200,.2)' : '#EAF2FB' }}; color:#2E6CA8; display:grid; place-items:center; flex-shrink:0; }
        .hm-conttitle { font-family:'Geist',sans-serif; font-size:16.5px; font-weight:800; color:var(--wl-ink); }
        .hm-contsee { margin-left:auto; font-size:13px; font-weight:700; color:#0F7A68; text-decoration:none; display:inline-flex; align-items:center; gap:3px; }
        .hm-controw { display:flex; align-items:center; gap:14px; padding:12px 4px; text-decoration:none; border-top:1px solid var(--wl-line); }
        .hm-controw:first-of-type { border-top:none; }
        .hm-ctthumb { position:relative; width:112px; height:64px; flex-shrink:0; border-radius:10px; overflow:hidden; background:linear-gradient(135deg,#C4DCF2,#A5C9EA); }
        .hm-ctthumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .hm-ctdur { position:absolute; right:5px; bottom:5px; background:rgba(20,28,38,.82); color:#fff; font-size:10px; font-weight:700; border-radius:6px; padding:1px 6px; }
        .hm-ctmid { flex:1; min-width:0; display:flex; flex-direction:column; gap:6px; }
        .hm-cttitle { font-family:'Geist',sans-serif; font-weight:800; font-size:14px; color:var(--wl-ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .hm-ctmeta { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .hm-ctsub { border-radius:999px; padding:2px 10px; font-family:'Geist',sans-serif; font-size:11px; font-weight:800; }
        .hm-ctbab { font-size:11.5px; font-weight:700; color:var(--wl-muted); }
        .hm-ctright { flex:0 0 150px; display:flex; flex-direction:column; align-items:flex-end; gap:5px; }
        .hm-ctbar { width:100%; height:6px; border-radius:999px; background:{{ $isDark ? 'var(--wl-line-2)' : '#E8EDF2' }}; overflow:hidden; }
        .hm-ctbar > i { display:block; height:100%; border-radius:999px; background:#17907B; }
        .hm-ctpct { font-size:11.5px; font-weight:700; color:var(--wl-muted); }
        @media (max-width:560px) {
            .hm-ctright { flex-basis:84px; }
            .hm-ctthumb { width:92px; height:54px; }
        }

        /* Section rows */
        .hm-sec { display:flex; flex-direction:column; gap:16px; }
        .hm-sechead { display:flex; justify-content:space-between; align-items:center; }
        .hm-sectitle { margin:0; font-family:'Geist',sans-serif; font-size:21px; font-weight:800; color:var(--wl-ink); display:flex; align-items:center; gap:9px; }
        .hm-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
        @media (max-width:980px) { .hm-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:560px) { .hm-grid { grid-template-columns:1fr; } }

        /* Live now */
        .hm-livegrid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px; }
        @media (max-width:560px) { .hm-livegrid { grid-template-columns:1fr; } }
        .hm-livecard { display:flex; flex-direction:column; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:18px; overflow:hidden; box-shadow:0 4px 16px rgba(46,44,80,.05); text-decoration:none; transition:transform .12s ease-out, box-shadow .12s; }
        .hm-livecard:hover { transform:translateY(-2px); box-shadow:0 12px 26px rgba(46,44,80,.12); }
        .hm-livethumb { position:relative; aspect-ratio:16/9; background:linear-gradient(135deg,#2B2B3A,#1C1C27); display:block; }
        .hm-livethumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .hm-livebadge { position:absolute; left:10px; top:10px; z-index:2; display:inline-flex; align-items:center; gap:6px; background:#E0251B; color:#fff; font-family:'Geist',sans-serif; font-size:11px; font-weight:800; letter-spacing:.08em; border-radius:7px; padding:4px 9px; }
        .hm-livebadge .dot { width:7px; height:7px; border-radius:50%; background:#fff; animation:hm-pulse 1.4s ease-in-out infinite; }
        @keyframes hm-pulse { 0%,100% { opacity:1; } 50% { opacity:.3; } }
        .hm-liveplay { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:48px; height:48px; border-radius:50%; background:rgba(255,255,255,.92); display:grid; place-items:center; color:#E0251B; z-index:1; }
        .hm-livebody { padding:13px 15px 15px; display:flex; flex-direction:column; gap:5px; }
        .hm-livetitle { font-family:'Geist',sans-serif; font-weight:800; font-size:14.5px; color:var(--wl-ink); line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .hm-liveteacher { font-size:12.5px; font-weight:700; color:var(--wl-muted); display:inline-flex; align-items:center; gap:6px; }

        /* Horizontal rail with floating prev/next arrows */
        .hm-railwrap { position:relative; }
        .hm-rail { display:flex; gap:16px; overflow-x:auto; scroll-behavior:smooth; padding-bottom:4px; scroll-snap-type:x proximity; scrollbar-width:none; }
        .hm-rail::-webkit-scrollbar { display:none; }
        .hm-railcard { flex:0 0 clamp(236px, 23vw, 280px); scroll-snap-align:start; }
        .hm-arrow { position:absolute; top:calc(50% - 2px); transform:translateY(-50%); width:44px; height:44px; border-radius:50%; background:var(--wl-surface); border:1px solid var(--wl-line); box-shadow:0 6px 18px rgba(46,44,80,.16); display:grid; place-items:center; cursor:pointer; color:#2E6CA8; z-index:3; }
        .hm-arrow:hover { background:#F4F8FC; }
        .hm-arrow-r { right:-8px; }
        .hm-arrow-l { left:-8px; }
        @media (max-width:560px) { .hm-arrow { display:none; } }
    </style>

    <div class="wl-pagehead" style="display:flex;align-items:flex-start;gap:16px">
        <span class="hi-tile" style="width:48px;height:48px;border-radius:14px;background:#DCF2EE;display:grid;place-items:center;flex-shrink:0" aria-hidden="true"><img src="{{ asset('images/wave.png') }}" alt="" style="width:30px;height:30px;object-fit:contain"></span>
        <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
            <h1 style="margin:0;font-family:'Geist',sans-serif;font-size:26px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ __('Selamat datang, :name', ['name' => $user->username]) }}</h1>
            <span style="font-size:14.5px;color:var(--wl-muted)">{{ __('Ringkasan pembelajaran anda pada hari ini') }}</span>
        </div>
    </div>

    @if (! $grade)
        <div style="background:var(--wl-surface);border:1px dashed var(--wl-line-3);border-radius:22px;padding:56px;display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center;max-width:520px;margin:0 auto">
            <h3 style="margin:0;font-family:'Geist',sans-serif;font-size:19px;font-weight:800;color:var(--wl-ink)">{{ __('Tahun anda belum ditetapkan') }}</h3>
            <p style="margin:0;font-size:14.5px;color:var(--wl-muted);max-width:360px">{{ __('Sila kemas kini profil anda dan pilih Tahun supaya kami boleh tunjukkan kandungan yang betul.') }}</p>
            <a href="{{ route('profile.edit') }}" class="wl-btn-primary" style="margin-top:6px;min-height:46px;display:inline-flex;align-items:center;border-radius:12px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:14.5px;padding:0 22px;text-decoration:none">{{ __('Kemas Kini Profil') }}</a>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:28px;margin-top:22px">

            {{-- Siaran Langsung: teacher channels live right now (opens YouTube in a new tab) --}}
            @if (($liveStreams ?? collect())->isNotEmpty())
                <div class="hm-sec">
                    <h2 class="hm-sectitle"><span style="width:10px;height:10px;border-radius:50%;background:#E0251B;display:inline-block;animation:hm-pulse 1.4s ease-in-out infinite"></span> {{ __('Siaran Langsung') }}</h2>
                    <div class="hm-livegrid">
                        @foreach ($liveStreams as $live)
                            <a href="{{ $live->watchUrl() }}" target="_blank" rel="noopener noreferrer" class="hm-livecard">
                                <span class="hm-livethumb">
                                    @if ($live->thumbnail_url)
                                        <img src="{{ $live->thumbnail_url }}" alt="" loading="lazy">
                                    @endif
                                    <span class="hm-livebadge"><span class="dot"></span> LIVE</span>
                                    <span class="hm-liveplay"><x-icon name="play" class="h-5 w-5" style="margin-left:2px" /></span>
                                </span>
                                <span class="hm-livebody">
                                    <span class="hm-livetitle">{{ $live->title ?: __('Siaran Langsung') }}</span>
                                    @if ($live->teacher)
                                        <span class="hm-liveteacher"><x-icon name="user" style="width:14px;height:14px" /> {{ $live->teacher->name }}</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- TOP: hero (left) + continue-watching list (right) --}}
            @if ($hero || $continue->isNotEmpty())
                <div class="hm-top">
                    @if ($hero)
                        @php($hs = $hero->chapter->subject)
                        @php($heroFav = $hero->isFavouritedBy($user))
                        @php($heroProg = $hero->progressFor($user))
                        @php($heroPct = $heroProg && $heroProg->percent ? min(100, $heroProg->percent) : 0)
                        <div class="hm-hero">
                            <a href="{{ route('video.show', $hero) }}" class="hm-herothumb">
                                @if ($hero->thumbnailUrl())
                                    <img src="{{ $hero->thumbnailUrl() }}" alt="">
                                @endif
                                <span class="hm-heroshade"></span>
                                <span class="hm-heroplay"><x-icon name="play" class="h-5 w-5" style="margin-left:2px" /></span>
                                @if ($hero->durationLabel())
                                    <span class="hm-herodur">{{ $hero->durationLabel() }}</span>
                                @endif
                                <span class="hm-heroover">
                                    <span class="hm-heropills">
                                        <span class="hm-pill"><x-subject-icon :subject="$hs" :size="13" /> {{ $hs->displayName() }}</span>
                                        <span class="hm-pill bab">{{ __('Bab :n', ['n' => $hero->chapter->number]) }}</span>
                                    </span>
                                    <span class="hm-herotitle">{{ $hero->title }}</span>
                                </span>
                            </a>
                            <div class="hm-herobody">
                                @if ($heroPct > 0)
                                    <div class="hm-progress"><i style="width:{{ $heroPct }}%"></i></div>
                                @endif
                                <div class="hm-herocta">
                                    <a href="{{ route('video.show', $hero) }}" class="hm-btn hm-btn-pri"><x-icon name="play" class="h-4 w-4" /> {{ $heroResuming ? __('Sambung Menonton') : __('Tonton') }}</a>
                                    <button type="button" class="hm-btn hm-btn-sec"
                                            x-data="{ fav: {{ $heroFav ? 'true' : 'false' }}, busy: false, toggle() { if (this.busy) return; const was = this.fav; this.fav = ! was; this.busy = true; const token = document.querySelector('meta[name=csrf-token]')?.content; fetch(was ? '{{ route('kegemaran.padam', $hero) }}' : '{{ route('kegemaran.simpan', $hero) }}', { method: was ? 'DELETE' : 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } }).then(r => { if (! r.ok) throw new Error('failed'); }).catch(() => { this.fav = was; }).finally(() => { this.busy = false; }); } }"
                                            @click="toggle()" :aria-pressed="fav ? 'true' : 'false'">
                                        <x-icon name="heart" class="h-4 w-4" ::style="fav ? 'fill:currentColor;color:#EB5E5A' : 'fill:none'" /> {{ __('Gemari') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($continue->isNotEmpty())
                        <div class="hm-cont">
                            <div class="hm-conthead">
                                <span class="hm-conticon"><x-icon name="clock" class="h-[18px] w-[18px]" /></span>
                                <span class="hm-conttitle">{{ __('Sambung menonton') }}</span>
                                <a href="{{ route('sambung.index') }}" class="hm-contsee">{{ __('Lihat semua') }} <x-icon name="chevron-right" class="h-4 w-4" /></a>
                            </div>
                            @foreach ($continue->take(5) as $lesson)
                                @php($lsub = $lesson->chapter->subject)
                                @php($lcol = $lsub->color ?: '#17907B')
                                @php($lprog = $lesson->relationLoaded('progress') ? $lesson->progress->first() : $lesson->progressFor($user))
                                @php($lpct = $lprog && $lprog->percent ? min(100, $lprog->percent) : 0)
                                <a href="{{ route('video.show', $lesson) }}" class="hm-controw">
                                    <span class="hm-ctthumb">
                                        @if ($lesson->thumbnailUrl())
                                            <img src="{{ $lesson->thumbnailUrl() }}" alt="">
                                        @endif
                                        @if ($lesson->durationLabel())
                                            <span class="hm-ctdur">{{ $lesson->durationLabel() }}</span>
                                        @endif
                                    </span>
                                    <div class="hm-ctmid">
                                        <span class="hm-cttitle">{{ $lesson->title }}</span>
                                        <span class="hm-ctmeta">
                                            <span class="hm-ctsub" style="background:color-mix(in oklab, {{ $lcol }} var(--pill-bw), var(--pill-bb));color:color-mix(in oklab, {{ $lcol }} var(--pill-fw), var(--pill-fb))">{{ $lsub->displayName() }}</span>
                                            <span class="hm-ctbab">{{ __('Bab :n', ['n' => $lesson->chapter->number]) }}</span>
                                        </span>
                                    </div>
                                    <div class="hm-ctright">
                                        <div class="hm-ctbar"><i style="width:{{ max(3, $lpct) }}%"></i></div>
                                        <span class="hm-ctpct">{{ $lpct }}% {{ __('ditonton') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Paling popular (falls back to newest) --}}
            @if ($trending->isNotEmpty())
                <div class="hm-sec">
                    <div class="hm-sechead">
                        <h2 class="hm-sectitle"><x-icon name="flame" class="h-5 w-5" style="color:#E0564E" /> {{ $trendingFallback ? __('Baru ditambah') : __('Paling popular') }}</h2>
                        <a href="{{ route('subjek.index') }}" style="font-size:13.5px;font-weight:700">{{ __('Lihat semua') }}</a>
                    </div>
                    <div class="hm-railwrap" x-data="homeRail()" x-init="$nextTick(() => update())">
                        <div class="hm-rail" x-ref="track" @scroll.debounce.100ms="update()">
                            @foreach ($trending->take(12) as $lesson)
                                <div class="hm-railcard"><x-vid-card :lesson="$lesson" :thumbHeight="110" :showViews="! $trendingFallback" /></div>
                            @endforeach
                        </div>
                        <button type="button" class="hm-arrow hm-arrow-l" @click="nudge(-1)" x-show="! atStart" x-cloak aria-label="{{ __('Sebelum') }}"><x-icon name="chevron-left" class="h-5 w-5" /></button>
                        <button type="button" class="hm-arrow hm-arrow-r" @click="nudge(1)" x-show="! atEnd" aria-label="{{ __('Seterusnya') }}"><x-icon name="chevron-right" class="h-5 w-5" /></button>
                    </div>
                </div>
            @endif

            {{-- Baru ditambah --}}
            @if ($newest->isNotEmpty() && ! $trendingFallback)
                <div class="hm-sec">
                    <div class="hm-sechead">
                        <h2 class="hm-sectitle">{{ __('Baru ditambah') }}</h2>
                        <a href="{{ route('subjek.index') }}" style="font-size:13.5px;font-weight:700">{{ __('Lihat semua') }}</a>
                    </div>
                    <div class="hm-railwrap" x-data="homeRail()" x-init="$nextTick(() => update())">
                        <div class="hm-rail" x-ref="track" @scroll.debounce.100ms="update()">
                            @foreach ($newest->take(12) as $lesson)
                                <div class="hm-railcard"><x-vid-card :lesson="$lesson" :thumbHeight="110" :showViews="true" /></div>
                            @endforeach
                        </div>
                        <button type="button" class="hm-arrow hm-arrow-l" @click="nudge(-1)" x-show="! atStart" x-cloak aria-label="{{ __('Sebelum') }}"><x-icon name="chevron-left" class="h-5 w-5" /></button>
                        <button type="button" class="hm-arrow hm-arrow-r" @click="nudge(1)" x-show="! atEnd" aria-label="{{ __('Seterusnya') }}"><x-icon name="chevron-right" class="h-5 w-5" /></button>
                    </div>
                </div>
            @endif

            {{-- Anda mungkin suka --}}
            @if ($suggested->isNotEmpty())
                <div class="hm-sec">
                    <div class="hm-sechead">
                        <h2 class="hm-sectitle"><x-icon name="star" class="h-5 w-5" style="color:#E3A31C" /> {{ __('Anda mungkin suka') }}</h2>
                        <a href="{{ route('subjek.index') }}" style="font-size:13.5px;font-weight:700">{{ __('Lihat semua') }}</a>
                    </div>
                    <div class="hm-railwrap" x-data="homeRail()" x-init="$nextTick(() => update())">
                        <div class="hm-rail" x-ref="track" @scroll.debounce.100ms="update()">
                            @foreach ($suggested->take(12) as $lesson)
                                <div class="hm-railcard"><x-vid-card :lesson="$lesson" :thumbHeight="110" :showViews="true" /></div>
                            @endforeach
                        </div>
                        <button type="button" class="hm-arrow hm-arrow-l" @click="nudge(-1)" x-show="! atStart" x-cloak aria-label="{{ __('Sebelum') }}"><x-icon name="chevron-left" class="h-5 w-5" /></button>
                        <button type="button" class="hm-arrow hm-arrow-r" @click="nudge(1)" x-show="! atEnd" aria-label="{{ __('Seterusnya') }}"><x-icon name="chevron-right" class="h-5 w-5" /></button>
                    </div>
                </div>
            @endif
        </div>

        @if (! $hero && $continue->isEmpty() && $trending->isEmpty() && $newest->isEmpty() && $suggested->isEmpty())
            <div style="background:var(--wl-surface);border:1px dashed var(--wl-line-3);border-radius:18px;padding:44px;text-align:center;color:var(--wl-muted);font-weight:600">
                {{ __('Belum ada video untuk :grade. Sila semak semula kemudian.', ['grade' => $grade->displayName()]) }}
            </div>
        @endif
    @endif

    @push('scripts')
        <script>
            function homeRail() {
                return {
                    atStart: true,
                    atEnd: false,
                    update() {
                        const el = this.$refs.track;
                        if (! el) return;
                        this.atStart = el.scrollLeft <= 2;
                        this.atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 2;
                    },
                    nudge(direction) {
                        const el = this.$refs.track;
                        el.scrollBy({ left: direction * el.clientWidth * 0.82, behavior: 'smooth' });
                    },
                };
            }
        </script>
    @endpush
</x-student-layout>
