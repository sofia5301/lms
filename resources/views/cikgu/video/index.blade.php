<x-cikgu-layout
    :title="__('Video')"
    :heading="__('Video')"
    heading-icon="video"
    :sub="__('Rakaman kelas yang anda muat naik atau pautkan dari YouTube')">

    <style>
        /* Video list as a table: Video / Subjek / Tahun·Bab / Sumber / Tontonan / Status / Tindakan.
           The table keeps a min-width and scrolls sideways inside .vt-wrap on narrow screens. */
        .vt-wrap { overflow-x:auto; }
        .vt-table { min-width:920px; }
        .vt-head, .vt-row { display:grid; grid-template-columns:minmax(0,2.4fr) minmax(0,1.1fr) minmax(0,1fr) minmax(0,0.95fr) minmax(0,0.78fr) minmax(0,1.05fr) 100px; gap:14px; align-items:center; }
        .vt-head { padding:13px 22px; }
        .vt-colh { font-family:'Geist',sans-serif; font-size:11px; font-weight:800; letter-spacing:.05em; text-transform:uppercase; color:var(--tp-muted); }
        .vt-row { padding:12px 22px; border-top:1px solid var(--tp-line); }
        .vt-video { display:flex; align-items:center; gap:14px; min-width:0; }
        .vt-thumb { position:relative; width:84px; height:54px; border-radius:9px; overflow:hidden; border:none; padding:0; cursor:pointer; flex-shrink:0; }
        .vt-title { text-align:left; background:none; border:none; padding:0; cursor:pointer; font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:var(--tp-ink); line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .vt-cell { font-size:13px; font-weight:700; color:var(--tp-muted-2); min-width:0; }
        .vt-tind { display:flex; align-items:center; gap:8px; }
        .vt-tind .tp-icon-action { border:1.5px solid var(--tp-line-2); }
        .vt-tind .tp-icon-danger { border-color:rgba(194,73,54,.45); }
        /* Centre every column except Video (header + data), so titles sit centred in their column. */
        .vt-head > :not(:first-child), .vt-row > :not(.vt-video) { justify-self:center; text-align:center; }

        /* Stat + filters on one row, split by a divider (stacks on small screens). */
        .bf-card { display:flex; align-items:stretch; flex-wrap:wrap; }
        .bf-stat { display:flex; align-items:center; gap:22px; padding:24px 80px 24px 36px; flex-shrink:0; min-width:350px; }
        .bf-div { width:1px; background:var(--tp-line); align-self:stretch; margin:18px 0; flex-shrink:0; }
        .bf-filters { flex:1; min-width:280px; display:flex; align-items:center; padding:18px 28px; }
        .bf-filters .ysf-form-cikgu { flex:1; min-width:0; }
        .bf-filters .tp-field { flex:1 1 0; min-width:150px; }
        .bf-filters .ss-wrap { display:block !important; width:100% !important; min-width:0 !important; }
        .bf-filters .ss-trigger { width:100%; }
        .bf-filters select.tp-filter-select { width:100%; min-width:0; }
        @media (max-width:860px) {
            .bf-div { display:none; }
            .bf-stat { width:100%; min-width:0; border-bottom:1px solid var(--tp-line); }
            .bf-filters { width:100%; }
        }

        /* Options bar above the table: source tabs (left) + sort (right). */
        .bt-optbar { display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap; border-bottom:1px solid var(--tp-line); margin:18px 0 6px; }
        .bt-tabs { display:flex; align-items:center; gap:2px; flex-wrap:wrap; }
        .bt-tab { font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:var(--tp-muted); text-decoration:none; padding:10px 14px; border-bottom:2px solid transparent; margin-bottom:-1px; white-space:nowrap; }
        .bt-tab:hover { color:var(--tp-ink); }
        .bt-tab.is-on { color:var(--tp-teal); border-bottom-color:var(--tp-teal); }
        .bt-sort { margin-left:auto; display:flex; align-items:center; gap:10px; padding-bottom:8px; }
        .bt-sortlabel { font-size:13px; font-weight:700; color:var(--tp-muted); white-space:nowrap; }
    </style>

    {{-- Search videos by title. Carries the current filters as hidden fields so searching keeps them. --}}
    <form method="GET" action="{{ route('cikgu.video.index') }}" style="margin-top:-18px;margin-bottom:18px">
        @foreach (request()->except(['q', 'page']) as $k => $v)
            @unless (is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endunless
        @endforeach
        <div style="position:relative;max-width:560px">
            <span style="position:absolute;left:16px;top:0;bottom:0;display:flex;align-items:center;color:var(--tp-muted);pointer-events:none"><x-icon name="search" class="h-5 w-5" /></span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari video...') }}" aria-label="{{ __('Cari video...') }}"
                   style="width:100%;min-height:48px;padding:0 18px 0 46px;border:1.5px solid var(--tp-line-2);border-radius:14px;background:var(--tp-surface);color:var(--tp-ink);font-family:'Nunito',sans-serif;font-size:15px">
        </div>
    </form>

    {{-- Page title, moved below the search bar. The layout's own heading is hidden (its controls
         keep their top-right spot). --}}
    <style>.tp-head .tp-heading { display:none; } .tp-head .tp-topbar { margin-left:auto; }</style>
    <div class="tp-heading" style="margin-bottom:20px">
        <span class="hi-tile" style="width:48px;height:48px;border-radius:14px;background:#DCF2EE;color:#0F7A68;display:grid;place-items:center;flex-shrink:0;align-self:flex-start"><x-icon name="video" style="width:24px;height:24px" /></span>
        <div style="display:flex;flex-direction:column;gap:2px">
            <h1 class="tp-h1">{{ __('Video') }}</h1>
            <span class="tp-hsub">{{ __('Rakaman kelas yang anda muat naik atau pautkan dari YouTube') }}</span>
        </div>
    </div>

    {{-- Stat (total videos, all-time) beside the Tahun/Subjek/Bab filters, split by a divider. --}}
    <div class="tp-card bf-card" style="margin-bottom:18px">
        <div class="bf-stat">
            <span style="width:52px;height:52px;border-radius:14px;background:#E4EEF9;color:#2E6CA8;display:grid;place-items:center;flex-shrink:0"><x-icon name="video" class="h-6 w-6" /></span>
            <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                <span style="font-size:12.5px;font-weight:700;color:var(--tp-muted);white-space:nowrap">{{ __('Video Saya') }}</span>
                <span style="font-family:'Geist',sans-serif;font-size:30px;font-weight:800;color:var(--tp-ink);line-height:1.05">{{ number_format($totalVideos) }}</span>
            </div>
        </div>

        <div class="bf-div"></div>

        <div class="bf-filters">
            <x-year-subject-filter :subjects="$subjects" :grades="$grades" :filter="$filter" with-chapter :action="route('cikgu.video.index')">
                <x-slot:search><input type="hidden" name="q" value="{{ request('q') }}"></x-slot:search>
            </x-year-subject-filter>
        </div>
    </div>

    @if ($counts['semua'] === 0)
        <div class="tp-empty">
            <span style="font-size:30px">🎬</span>
            <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Belum ada video') }}</h3>
            <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:380px">{{ __('Muat naik rakaman kelas anda, atau tampal pautan YouTube dari akaun anda sendiri.') }}</p>
            <a href="{{ route('cikgu.video.create') }}" class="tp-btn" style="margin-top:6px">{{ __('Tambah Video Pertama') }}</a>
        </div>
    @else
        {{-- Source tabs + sort. Tabs preserve the Tahun/Subjek/Bab filter + search; counts reflect them. --}}
        @php($base = request()->except(['jenis', 'page']))
        @php($tabs = [
            ['key' => null, 'label' => __('Semua'), 'count' => $counts['semua']],
            ['key' => 'youtube', 'label' => 'YouTube', 'count' => $counts['youtube']],
            ['key' => 'upload', 'label' => __('Muat Naik'), 'count' => $counts['upload']],
        ])
        <div class="bt-optbar">
            <div class="bt-tabs">
                @foreach ($tabs as $tab)
                    @php($tabUrl = $tab['key'] ? route('cikgu.video.index', array_merge($base, ['jenis' => $tab['key']])) : route('cikgu.video.index', $base))
                    <a href="{{ $tabUrl }}" @class(['bt-tab', 'is-on' => $kind === $tab['key']])>{{ $tab['label'] }} ({{ $tab['count'] }})</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('cikgu.video.index') }}" class="bt-sort">
                @foreach (request()->except(['isih', 'page']) as $k => $v)
                    @unless (is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endunless
                @endforeach
                <label for="isih" class="bt-sortlabel">{{ __('Isih mengikut') }}</label>
                <select id="isih" name="isih" class="tp-filter-select" style="min-width:160px" onchange="this.form.submit()">
                    <option value="terbaru" @selected($sort === 'terbaru')>{{ __('Terbaru') }}</option>
                    <option value="terlama" @selected($sort === 'terlama')>{{ __('Terlama') }}</option>
                    <option value="tontonan" @selected($sort === 'tontonan')>{{ __('Paling Ditonton') }}</option>
                    <option value="nama" @selected($sort === 'nama')>{{ __('Nama (A-Z)') }}</option>
                </select>
            </form>
        </div>

        {{-- Clicking a video opens a preview here instead of navigating to the watch page. --}}
        <div x-data="{
                 lesson: null,
                 open(data) { this.lesson = data; document.body.classList.add('overflow-hidden'); },
                 close() { this.lesson = null; document.body.classList.remove('overflow-hidden'); },
             }"
             @keydown.escape.window="close()">

        @if ($lessons->isEmpty())
            <div class="tp-card" style="padding:40px 22px;text-align:center;color:var(--tp-muted);font-size:14px;font-weight:600">{{ __('Tiada video untuk pilihan ini.') }}</div>
        @else
        <div class="tp-card">
            <div class="vt-wrap">
                <div class="vt-table">
                    <div class="vt-head">
                        <span class="vt-colh">{{ __('Video') }}</span>
                        <span class="vt-colh">{{ __('Subjek') }}</span>
                        <span class="vt-colh">{{ __('Tahun · Bab') }}</span>
                        <span class="vt-colh">{{ __('Sumber') }}</span>
                        <span class="vt-colh">{{ __('Tontonan') }}</span>
                        <span class="vt-colh">{{ __('Status') }}</span>
                        <span class="vt-colh">{{ __('Tindakan') }}</span>
                    </div>

                    @foreach ($lessons as $lesson)
                        @php($subject = $lesson->chapter->subject)
                        @php($preview = [
                            'title' => $lesson->title,
                            'subtitle' => collect([$subject->name, $lesson->chapter->grade->displayName(), __('Bab :n', ['n' => $lesson->chapter->number])])->implode(' · '),
                            'kind' => $lesson->isYoutube() ? 'youtube' : 'upload',
                            'src' => $lesson->isYoutube() ? $lesson->embedUrl() : $lesson->videoUrl(),
                            'poster' => $lesson->thumbnailUrl(),
                        ])
                        <div class="vt-row">
                            {{-- VIDEO: thumbnail (click to preview) + title --}}
                            <div class="vt-video">
                                <button type="button" @click="open(@js($preview))" title="{{ __('Lihat video') }}" class="vt-thumb" style="background:rgb({{ $subject->rgb }} / .14)">
                                    @if ($lesson->thumbnailUrl())
                                        <img src="{{ $lesson->thumbnailUrl() }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover">
                                    @endif
                                    <span style="position:absolute;inset:0;display:grid;place-items:center">
                                        <span style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;box-shadow:0 2px 6px rgba(0,0,0,.28)"><x-icon name="play" class="h-[13px] w-[13px]" style="color:#1F2937;margin-left:2px" /></span>
                                    </span>
                                    @if ($lesson->durationLabel())
                                        <span style="position:absolute;right:4px;bottom:4px;background:rgba(0,0,0,.8);color:#fff;font-family:'Geist',sans-serif;font-weight:700;font-size:10px;padding:1px 5px;border-radius:5px">{{ $lesson->durationLabel() }}</span>
                                    @endif
                                </button>
                                <button type="button" @click="open(@js($preview))" class="vt-title">{{ $lesson->title }}</button>
                            </div>

                            {{-- SUBJEK --}}
                            <div><span class="tp-tag" style="background:rgb({{ $subject->rgb }} / .14);color:rgb({{ $subject->rgb }})">{{ $subject->name }}</span></div>

                            {{-- TAHUN · BAB --}}
                            <div class="vt-cell">{{ $lesson->chapter->grade->displayName() }} · {{ __('Bab :n', ['n' => $lesson->chapter->number]) }}</div>

                            {{-- SUMBER --}}
                            <div class="vt-cell"><span style="display:inline-flex;align-items:center;gap:7px"><x-icon name="{{ $lesson->isYoutube() ? 'youtube' : 'upload' }}" class="h-4 w-4" style="color:{{ $lesson->isYoutube() ? '#FF0000' : 'var(--tp-muted-2)' }}" />{{ $lesson->isYoutube() ? 'YouTube' : __('Muat naik') }}</span></div>

                            {{-- TONTONAN --}}
                            <div class="vt-cell"><span style="display:inline-flex;align-items:center;gap:6px"><img src="{{ asset('images/eye.png') }}" alt="" style="width:15px;height:15px;object-fit:contain">{{ $lesson->views_count }}</span></div>

                            {{-- STATUS: click to toggle publish --}}
                            <div>
                                <form method="POST" action="{{ route('cikgu.video.terbit', $lesson) }}">
                                    @csrf
                                    <button type="submit" class="tp-badge {{ $lesson->is_published ? 'tp-badge-ok' : 'tp-badge-draft' }}" style="border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                                        @if ($lesson->is_published)<x-icon name="check-circle" class="h-4 w-4" />@endif
                                        {{ $lesson->is_published ? __('Diterbitkan') : __('Draf') }}
                                    </button>
                                </form>
                            </div>

                            {{-- TINDAKAN: edit + delete --}}
                            <div class="vt-tind">
                                <a href="{{ route('cikgu.video.edit', $lesson) }}" class="tp-icon-action" title="{{ __('Sunting') }}">
                                    <x-icon name="pencil" class="h-[17px] w-[17px]" />
                                    <span class="sr-only">{{ __('Sunting :title', ['title' => $lesson->title]) }}</span>
                                </a>
                                @php($delMsg = __('Padam video ":title"? Fail video juga akan dipadam. Tindakan ini tidak boleh dibatalkan.', ['title' => $lesson->title]))
                                <x-confirm-modal id="del-video-{{ $lesson->id }}" :action="route('cikgu.video.destroy', $lesson)" :title="__('Padam video?')" :message="$delMsg">
                                    <button type="button" class="tp-icon-action tp-icon-danger" title="{{ __('Padam') }}">
                                        <x-icon name="trash" class="h-[17px] w-[17px]" />
                                        <span class="sr-only">{{ __('Padam :title', ['title' => $lesson->title]) }}</span>
                                    </button>
                                </x-confirm-modal>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="margin-top:16px">{{ $lessons->links('pagination.tp') }}</div>
    @endif

        {{-- Preview modal: gradient header + black video body, the shared admin shell. x-if so
             closing destroys the player and stops its audio. --}}
        <template x-if="lesson">
            <x-content-preview obj="lesson">
                <div style="overflow-y:auto;background:#000;height:min(72vh,620px)">
                    <template x-if="lesson.kind === 'youtube'">
                        <iframe style="width:100%;height:100%;border:0;display:block" :src="lesson.src" :title="lesson.title"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </template>

                    <template x-if="lesson.kind === 'upload'">
                        <video style="width:100%;height:100%;object-fit:contain;background:#000;display:block" controls preload="metadata"
                               :src="lesson.src" :poster="lesson.poster"></video>
                    </template>
                </div>
            </x-content-preview>
        </template>
    </div>
    @endif
</x-cikgu-layout>
