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
        /* Centre every column except Video (header + data), so titles sit centred in their column. */
        .vt-head > :not(:first-child), .vt-row > :not(.vt-video) { justify-self:center; text-align:center; }

        /* Stat + filters on one row, split by a divider (stacks on small screens). */
        .bf-card { display:flex; align-items:stretch; flex-wrap:wrap; }
        .bf-stat { display:flex; align-items:center; gap:22px; padding:24px 80px 24px 36px; flex-shrink:0; }
        .bf-div { width:1px; background:var(--tp-line); align-self:stretch; margin:18px 0; flex-shrink:0; }
        .bf-filters { flex:1; min-width:280px; display:flex; align-items:center; padding:18px 28px; }
        .bf-filters .ysf-form-cikgu { flex:1; min-width:0; }
        .bf-filters .tp-field { flex:1 1 0; min-width:150px; }
        .bf-filters .ss-wrap { display:block !important; width:100% !important; min-width:0 !important; }
        .bf-filters .ss-trigger { width:100%; }
        .bf-filters select.tp-filter-select { width:100%; min-width:0; }
        @media (max-width:860px) {
            .bf-div { display:none; }
            .bf-stat { width:100%; border-bottom:1px solid var(--tp-line); }
            .bf-filters { width:100%; }
        }
    </style>

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
            <x-year-subject-filter :subjects="$subjects" :grades="$grades" :filter="$filter" with-chapter :action="route('cikgu.video.index')" />
        </div>
    </div>

    {{-- Clicking a video opens a preview here instead of navigating to the watch page - the same
         look the admin content page uses. A look, not a lesson: no view is counted. --}}
    <div x-data="{
             lesson: null,
             open(data) { this.lesson = data; document.body.classList.add('overflow-hidden'); },
             close() { this.lesson = null; document.body.classList.remove('overflow-hidden'); },
         }"
         @keydown.escape.window="close()">

    @if ($lessons->isEmpty())
        <div class="tp-empty">
            <span style="font-size:30px">🎬</span>
            <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Belum ada video') }}</h3>
            <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:380px">{{ __('Muat naik rakaman kelas anda, atau tampal pautan YouTube dari akaun anda sendiri.') }}</p>
            <a href="{{ route('cikgu.video.create') }}" class="tp-btn" style="margin-top:6px">{{ __('Tambah Video Pertama') }}</a>
        </div>
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
</x-cikgu-layout>
