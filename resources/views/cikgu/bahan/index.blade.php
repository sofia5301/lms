<x-cikgu-layout
    :title="__('Bahan Bantu Mengajar')"
    :heading="__('Bahan Bantu Mengajar')"
    heading-icon="file"
    :sub="__('Slaid, PDF dan lembaran kerja yang menyokong video anda')">

    <style>
        /* Materials as a table: Bahan / Subjek / Tahun·Bab / Jenis / Saiz / Muat Turun / Tindakan.
           Keeps a min-width and scrolls sideways inside .bt-wrap on narrow screens. */
        .bt-wrap { overflow-x:auto; }
        .bt-table { min-width:940px; }
        .bt-head, .bt-row { display:grid; grid-template-columns:minmax(0,2.4fr) minmax(0,1.1fr) minmax(0,1fr) minmax(0,0.75fr) minmax(0,0.75fr) minmax(0,0.9fr) 140px; gap:14px; align-items:center; }
        .bt-head { padding:13px 22px; }
        .bt-colh { font-family:'Geist',sans-serif; font-size:11px; font-weight:800; letter-spacing:.05em; text-transform:uppercase; color:var(--tp-muted); }
        .bt-row { padding:12px 22px; border-top:1px solid var(--tp-line); }
        .bt-item { display:flex; align-items:center; gap:14px; min-width:0; }
        .bt-ico { width:46px; height:46px; border-radius:12px; display:grid; place-items:center; border:none; padding:0; cursor:pointer; flex-shrink:0; }
        .bt-title { text-align:left; background:none; border:none; padding:0; cursor:pointer; font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:var(--tp-ink); line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .bt-cell { font-size:13px; font-weight:700; color:var(--tp-muted-2); min-width:0; }
        .bt-type { background:#EAEBFB; color:#4F46E5; border-radius:999px; padding:4px 12px; font-family:'Geist',sans-serif; font-size:11.5px; font-weight:800; }
        .bt-tind { display:flex; align-items:center; gap:6px; }
        /* Centre every column except Bahan (header + data), so titles sit centred in their column. */
        .bt-head > :not(:first-child), .bt-row > :not(.bt-item) { justify-self:center; text-align:center; }

        /* Options bar above the table: file-type tabs (left) + sort (right). */
        .bt-optbar { display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap; border-bottom:1px solid var(--tp-line); margin:18px 0 6px; }
        .bt-tabs { display:flex; align-items:center; gap:2px; flex-wrap:wrap; }
        .bt-tab { font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:var(--tp-muted); text-decoration:none; padding:10px 14px; border-bottom:2px solid transparent; margin-bottom:-1px; white-space:nowrap; }
        .bt-tab:hover { color:var(--tp-ink); }
        .bt-tab.is-on { color:var(--tp-teal); border-bottom-color:var(--tp-teal); }
        .bt-sort { margin-left:auto; display:flex; align-items:center; gap:10px; padding-bottom:8px; }
        .bt-sortlabel { font-size:13px; font-weight:700; color:var(--tp-muted); white-space:nowrap; }
    </style>

    {{-- Total materials uploaded by this teacher (all-time, not the filtered count). --}}
    <div class="tp-stat" style="max-width:340px;margin-bottom:18px">
        <div style="display:flex;align-items:center;gap:10px">
            <span class="tp-stat-ico" style="background:#DCF2EE"><x-icon name="file" class="h-[18px] w-[18px]" style="color:#0F7A68" /></span>
            <span class="tp-stat-label">{{ __('Bahan Saya') }}</span>
        </div>
        <span class="tp-stat-value">{{ number_format($totalMaterials) }}</span>
        <span style="font-size:12.5px;font-weight:700;color:var(--tp-muted)">{{ __('Bahan pengajaran') }}</span>
    </div>

    <x-year-subject-filter :subjects="$subjects" :grades="$grades" :filter="$filter" with-chapter :action="route('cikgu.bahan.index')" />

    @if ($counts['semua'] === 0)
        <div class="tp-empty">
            <x-icon name="file" class="h-8 w-8" style="color:var(--tp-muted)" />
            <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Belum ada bahan') }}</h3>
            <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:380px">{{ __('Muat naik slaid, PDF atau lembaran kerja untuk menyokong pembelajaran murid.') }}</p>
            <a href="{{ route('cikgu.bahan.create') }}" class="tp-btn" style="margin-top:6px">{{ __('Muat Naik Bahan') }}</a>
        </div>
    @else
        {{-- File-type tabs + sort. Tabs preserve the Tahun/Subjek/Bab filter and the sort; counts
             reflect that filter. Together Slaid + PDF + Lembaran Kerja cover every material. --}}
        @php($base = request()->except(['jenis', 'page']))
        @php($tabs = [
            ['key' => null, 'label' => __('Semua'), 'count' => $counts['semua']],
            ['key' => 'slaid', 'label' => __('Slaid'), 'count' => $counts['slaid']],
            ['key' => 'pdf', 'label' => 'PDF', 'count' => $counts['pdf']],
            ['key' => 'lembaran', 'label' => __('Lembaran Kerja'), 'count' => $counts['lembaran']],
        ])
        <div class="bt-optbar">
            <div class="bt-tabs">
                @foreach ($tabs as $tab)
                    @php($tabUrl = $tab['key'] ? route('cikgu.bahan.index', array_merge($base, ['jenis' => $tab['key']])) : route('cikgu.bahan.index', $base))
                    <a href="{{ $tabUrl }}" @class(['bt-tab', 'is-on' => $kind === $tab['key']])>{{ $tab['label'] }} ({{ $tab['count'] }})</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('cikgu.bahan.index') }}" class="bt-sort">
                @foreach (request()->except(['isih', 'page']) as $k => $v)
                    @unless (is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endunless
                @endforeach
                <label for="isih" class="bt-sortlabel">{{ __('Isih mengikut') }}</label>
                <select id="isih" name="isih" class="tp-filter-select" style="min-width:160px" onchange="this.form.submit()">
                    <option value="terbaru" @selected($sort === 'terbaru')>{{ __('Terbaru') }}</option>
                    <option value="terlama" @selected($sort === 'terlama')>{{ __('Terlama') }}</option>
                    <option value="muat-turun" @selected($sort === 'muat-turun')>{{ __('Paling Dimuat Turun') }}</option>
                    <option value="nama" @selected($sort === 'nama')>{{ __('Nama (A-Z)') }}</option>
                </select>
            </form>
        </div>

        {{-- Clicking a material's name opens a preview here. PDFs and images render in place;
             anything the browser can't show offers a download. --}}
        <div x-data="{
                 item: null,
                 open(data) { this.item = data; document.body.classList.add('overflow-hidden'); },
                 close() { this.item = null; document.body.classList.remove('overflow-hidden'); },
             }"
             @keydown.escape.window="close()">

            @if ($materials->isEmpty())
                <div class="tp-card" style="padding:40px 22px;text-align:center;color:var(--tp-muted);font-size:14px;font-weight:600">{{ __('Tiada bahan untuk pilihan ini.') }}</div>
            @else
                <div class="tp-card">
                    <div class="bt-wrap">
                        <div class="bt-table">
                            <div class="bt-head">
                                <span class="bt-colh">{{ __('Bahan') }}</span>
                                <span class="bt-colh">{{ __('Subjek') }}</span>
                                <span class="bt-colh">{{ __('Tahun · Bab') }}</span>
                                <span class="bt-colh">{{ __('Jenis') }}</span>
                                <span class="bt-colh">{{ __('Saiz') }}</span>
                                <span class="bt-colh">{{ __('Muat Turun') }}</span>
                                <span class="bt-colh">{{ __('Tindakan') }}</span>
                            </div>

                            @foreach ($materials as $material)
                                @php($subject = $material->chapter->subject)
                                @php($preview = [
                                    'title' => $material->title,
                                    'subtitle' => collect([$subject->name, $material->chapter->grade->displayName(), __('Bab :n', ['n' => $material->chapter->number])])->implode(' · '),
                                    'kind' => $material->previewKind(),
                                    'src' => $material->fileUrl(),
                                    'name' => $material->original_name,
                                    'type' => strtoupper($material->extension()),
                                    'size' => $material->humanSize(),
                                    'downloadUrl' => route('muat-turun.bahan', $material),
                                ])
                                <div class="bt-row">
                                    {{-- BAHAN: file-type icon (click to preview) + title --}}
                                    <div class="bt-item">
                                        <button type="button" @click="open(@js($preview))" title="{{ __('Lihat bahan') }}" class="bt-ico" style="background:rgb({{ $subject->rgb }} / .14);color:rgb({{ $subject->rgb }})"><x-icon :name="$material->iconName()" class="h-6 w-6" /></button>
                                        <button type="button" @click="open(@js($preview))" class="bt-title">{{ $material->title }}</button>
                                    </div>

                                    {{-- SUBJEK --}}
                                    <div><span class="tp-tag" style="background:rgb({{ $subject->rgb }} / .14);color:rgb({{ $subject->rgb }})">{{ $subject->name }}</span></div>

                                    {{-- TAHUN · BAB --}}
                                    <div class="bt-cell">{{ $material->chapter->grade->displayName() }} · {{ __('Bab :n', ['n' => $material->chapter->number]) }}</div>

                                    {{-- JENIS --}}
                                    <div><span class="bt-type">{{ strtoupper($material->extension()) }}</span></div>

                                    {{-- SAIZ --}}
                                    <div class="bt-cell">{{ $material->humanSize() }}</div>

                                    {{-- MUAT TURUN --}}
                                    <div class="bt-cell"><span style="display:inline-flex;align-items:center;gap:6px"><x-icon name="download" class="h-4 w-4" style="color:var(--tp-muted-2)" />{{ $material->download_count }}</span></div>

                                    {{-- TINDAKAN: download + edit + delete --}}
                                    <div class="bt-tind">
                                        <a href="{{ route('muat-turun.bahan', $material) }}" class="tp-icon-action" style="color:#0F7A68" title="{{ __('Muat turun') }}">
                                            <x-icon name="download" class="h-[17px] w-[17px]" />
                                            <span class="sr-only">{{ __('Muat turun :title', ['title' => $material->title]) }}</span>
                                        </a>
                                        <a href="{{ route('cikgu.bahan.edit', $material) }}" class="tp-icon-action" title="{{ __('Sunting') }}">
                                            <x-icon name="pencil" class="h-[17px] w-[17px]" />
                                            <span class="sr-only">{{ __('Sunting :title', ['title' => $material->title]) }}</span>
                                        </a>
                                        @php($delMsg = __('Padam bahan ":title"? Fail juga akan dipadam.', ['title' => $material->title]))
                                        <x-confirm-modal id="del-bahan-{{ $material->id }}" :action="route('cikgu.bahan.destroy', $material)" :title="__('Padam bahan?')" :message="$delMsg">
                                            <button type="button" class="tp-icon-action tp-icon-danger" title="{{ __('Padam') }}">
                                                <x-icon name="trash" class="h-[17px] w-[17px]" />
                                                <span class="sr-only">{{ __('Padam :title', ['title' => $material->title]) }}</span>
                                            </button>
                                        </x-confirm-modal>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="margin-top:16px">{{ $materials->links('pagination.tp') }}</div>
            @endif

            {{-- Preview modal: PDFs and images render in place, other files show a document card with a
                 download. Shared admin shell so both surfaces stay in step. --}}
            <template x-if="item">
                <x-content-preview obj="item">
                    <template x-if="item.kind === 'pdf'">
                        <iframe style="width:100%;height:min(72vh,620px);border:0;display:block;background:#000" :src="item.src" :title="item.title"></iframe>
                    </template>

                    <template x-if="item.kind === 'image'">
                        <div style="overflow:auto;height:min(72vh,620px);display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg,#EDF3FA,#F6F5F0);padding:20px">
                            <img :src="item.src" :alt="item.title" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;box-shadow:0 8px 28px rgba(46,44,80,.14)">
                        </div>
                    </template>

                    <template x-if="item.kind === 'none'">
                        <div style="overflow-y:auto;padding:28px;background:linear-gradient(180deg,#EDF3FA,#F6F5F0);display:flex;flex-direction:column;align-items:center;gap:18px">
                            <div style="width:min(440px,100%);aspect-ratio:1/1.28;background:#fff;border:1px solid rgba(46,44,80,.1);border-radius:10px;box-shadow:0 8px 28px rgba(46,44,80,.14);padding:32px 28px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;position:relative">
                                <span style="position:absolute;top:18px;right:18px;background:#3E86C9;color:#fff;border-radius:8px;padding:5px 11px;font-family:'Geist',sans-serif;font-size:11.5px;font-weight:800" x-text="item.type"></span>
                                <x-icon name="file" class="h-12 w-12" style="color:#6C6F87" />
                                <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:15px;color:#28293F;text-align:center;word-break:break-word" x-text="item.name"></span>
                                <span style="font-size:12.5px;color:#6C6F87;font-weight:700"><span x-text="item.type"></span> · <span x-text="item.size"></span></span>
                                <a :href="item.downloadUrl" style="margin-top:6px;display:inline-flex;align-items:center;gap:8px;min-height:42px;border-radius:12px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:13.5px;padding:0 18px;text-decoration:none"><x-icon name="download" class="h-4 w-4" />{{ __('Muat Turun') }}</a>
                            </div>
                            <span style="font-size:12.5px;color:#6C6F87;font-weight:700">{{ __('Fail ini tidak boleh dipaparkan dalam pelayar. Muat turun untuk membukanya.') }}</span>
                        </div>
                    </template>
                </x-content-preview>
            </template>
        </div>
    @endif
</x-cikgu-layout>
