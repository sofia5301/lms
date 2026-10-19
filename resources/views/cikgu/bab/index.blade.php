<x-cikgu-layout
    :title="__('Pengurusan Bab')"
    :heading="__('Bab')"
    heading-icon="book"
    :sub="__('Bab dikongsi oleh semua guru mengikut sukatan Kurikulum 2027.')">

    <style>
        /* Top bar: the Tahun/Subjek filter on the left, the Bab count + list/grid toggle on the right. */
        .bab-top { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; flex-wrap:wrap; }
        .bab-topright { display:flex; align-items:center; gap:16px; padding-bottom:4px; }
        .bab-count { display:inline-flex; align-items:center; gap:7px; font-size:13.5px; font-weight:700; color:var(--tp-muted); white-space:nowrap; }
        .bab-count .dot { width:8px; height:8px; border-radius:50%; background:#2BB39B; flex-shrink:0; }
        .bab-toggle { display:flex; align-items:center; gap:4px; background:var(--tp-surface); border:1.5px solid var(--tp-line-2); border-radius:12px; padding:4px; }
        .bab-toggle button { display:grid; place-items:center; width:40px; height:36px; border:none; border-radius:9px; background:transparent; color:var(--tp-muted); cursor:pointer; }
        .bab-toggle button.is-on { background:#DCF2EE; color:#0F7A68; }

        /* Master-detail split: chapter list (left) + chapter detail (right). Stacks on small screens. */
        .bab-split { display:grid; grid-template-columns:minmax(0,0.95fr) minmax(0,1.35fr); gap:18px; align-items:start; }
        @media (max-width:980px) { .bab-split { grid-template-columns:1fr; } }

        /* Left: chapter list. In grid mode it becomes two compact columns. */
        .bab-list { display:flex; flex-direction:column; gap:10px; }
        .bab-list.is-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        @media (max-width:560px) { .bab-list.is-grid { grid-template-columns:1fr; } }
        .bab-item { display:flex; align-items:center; gap:14px; width:100%; text-align:left; background:var(--tp-surface); border:1.5px solid var(--tp-line); border-radius:16px; padding:14px 16px; cursor:pointer; transition:border-color .12s, box-shadow .12s; }
        .bab-item:hover { border-color:var(--tp-line-2); }
        .bab-item.is-on { border-color:#2BB39B; box-shadow:0 0 0 3px rgba(43,179,155,.14); }
        .bab-num { width:52px; height:52px; border-radius:14px; display:grid; place-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:20px; flex-shrink:0; }
        .bab-itbody { display:flex; flex-direction:column; gap:7px; min-width:0; flex:1; }
        .bab-ittitle { display:flex; flex-wrap:wrap; align-items:center; gap:8px; font-family:'Geist',sans-serif; font-weight:800; font-size:16px; color:var(--tp-ink); line-height:1.25; }
        .bab-itmeta { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
        .bab-m { display:inline-flex; align-items:center; gap:5px; font-size:12.5px; font-weight:700; color:var(--tp-muted-2); }
        .bab-chev { color:var(--tp-muted); flex-shrink:0; }

        /* Right: chapter detail card. */
        .bab-detail { background:var(--tp-surface); border:1.5px solid var(--tp-line); border-radius:20px; padding:26px 28px; position:sticky; top:18px; }
        @media (max-width:980px) { .bab-detail { position:static; } }
        .bab-hero { position:relative; overflow:hidden; border-radius:16px; margin:18px 0 22px; min-height:150px; display:grid; place-items:center; }
        .bab-hero .ic { opacity:.22; }
        .bab-tag { display:inline-flex; align-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:12px; padding:5px 12px; border-radius:999px; }
        .bab-title { font-family:'Geist',sans-serif; font-size:28px; font-weight:800; letter-spacing:-.01em; color:var(--tp-ink); line-height:1.15; margin:12px 0 0; }
        .bab-headmeta { display:flex; align-items:center; gap:16px; flex-wrap:wrap; margin-top:12px; }
        .bab-sec-h { font-family:'Geist',sans-serif; font-size:17px; font-weight:800; color:var(--tp-ink); margin:0 0 10px; }
        .bab-about { font-size:14px; line-height:1.6; color:var(--tp-muted); margin:0 0 22px; }
        .bab-stats { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:24px; }
        @media (max-width:560px) { .bab-stats { grid-template-columns:1fr; } }
        .bab-stat { display:flex; align-items:center; gap:13px; background:var(--tp-surface-2); border:1px solid var(--tp-line); border-radius:14px; padding:14px 16px; }
        .bab-stat .sq { width:42px; height:42px; border-radius:11px; display:grid; place-items:center; flex-shrink:0; }
        .bab-stat .n { font-family:'Geist',sans-serif; font-size:22px; font-weight:800; color:var(--tp-ink); line-height:1; }
        .bab-stat .l { font-size:12px; font-weight:700; color:var(--tp-muted); margin-top:3px; }
        .bab-listhdr { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:10px; }
        .bab-seeall { display:inline-flex; align-items:center; gap:6px; font-family:'Geist',sans-serif; font-weight:800; font-size:13px; color:#0F7A68; text-decoration:none; }
        .bab-crow { display:flex; align-items:center; gap:13px; border:1px solid var(--tp-line); border-radius:14px; padding:14px 16px; text-decoration:none; margin-bottom:10px; }
        .bab-crow:hover { border-color:var(--tp-line-2); }
        .bab-crow .nm { display:inline-flex; align-items:center; gap:11px; font-size:14px; font-weight:700; color:var(--tp-ink); flex:1; min-width:0; }
        .bab-crow .ct { font-size:13px; font-weight:700; color:var(--tp-muted); display:inline-flex; align-items:center; gap:8px; white-space:nowrap; }
        .bab-cta { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; min-height:56px; margin-top:22px; border-radius:16px; background:linear-gradient(135deg,#1E7A58,#176B4C); color:#fff; font-family:'Geist',sans-serif; font-weight:800; font-size:16px; text-decoration:none; }
        .bab-cta:hover { filter:brightness(1.05); }
    </style>

    @php($palette = [
        ['accent' => '#17907B', 'tint' => '#DCF2EE'],
        ['accent' => '#2E6CA8', 'tint' => '#E4EEF9'],
        ['accent' => '#7C5CBF', 'tint' => '#EDE7F9'],
        ['accent' => '#D9862B', 'tint' => '#FBEAD3'],
        ['accent' => '#D9548A', 'tint' => '#FBE0EC'],
    ])
    @php($chaptersData = $chapters->values()->map(fn ($ch) => [
        'number' => $ch->number,
        'title' => $ch->title,
        'description' => $ch->description,
        'active' => (bool) $ch->is_active,
        'videos' => $ch->lessons_count,
        'bahan' => $ch->materials_count,
        'kuiz' => $ch->quizzes_count,
        'showUrl' => route('cikgu.bab.show', $ch),
    ])->all())

    <div style="display:flex;flex-direction:column;gap:18px"
         x-data="{
             view: 'list',
             selected: 0,
             chapters: @js($chaptersData),
             get sel() { return this.chapters[this.selected] ?? null; },
         }">

        {{-- Top bar: filter (left) + count & list/grid toggle (right). --}}
        <div class="bab-top">
            <x-year-subject-filter
                :action="route('cikgu.bab.index')"
                :grades="$grades"
                :subjects="$subjects"
                :filter="$filter"
                :all-years="false" />

            @if ($subject && $grade && $chapters->isNotEmpty())
                <div class="bab-topright">
                    <span class="bab-count"><span class="dot"></span>{{ __(':count bab tersedia', ['count' => $chapters->count()]) }}</span>
                    <div class="bab-toggle" role="group" aria-label="{{ __('Papar sebagai') }}">
                        <button type="button" @click="view = 'list'" :class="view === 'list' ? 'is-on' : ''" title="{{ __('Senarai') }}" aria-label="{{ __('Senarai') }}"><x-icon name="list" class="h-[18px] w-[18px]" /></button>
                        <button type="button" @click="view = 'grid'" :class="view === 'grid' ? 'is-on' : ''" title="{{ __('Grid') }}" aria-label="{{ __('Grid') }}"><x-icon name="grid" class="h-[18px] w-[18px]" /></button>
                    </div>
                </div>
            @endif
        </div>

        @if ($subject && $grade)
            @unless ($isOffered)
                <div style="display:flex;gap:10px;background:#FEF0CE;border:1px solid rgba(138,106,18,.25);border-radius:14px;padding:14px 18px;font-size:13.5px;color:#8A6A12">
                    <x-icon name="info-circle" class="h-5 w-5" style="flex-shrink:0;margin-top:1px" />
                    <div>{{ __(':subject tidak ditawarkan untuk :grade dalam Kurikulum 2027. Anda tidak boleh menambah bab baharu di sini. Bab lama yang masih mengandungi kandungan ditandakan tidak aktif - sila pindahkan kandungannya ke Tahun yang betul.', ['subject' => $subject->name, 'grade' => $grade->displayName()]) }}</div>
                </div>
            @endunless

            @if ($chapters->isEmpty())
                <div class="tp-empty">
                    <span style="font-size:30px">📚</span>
                    <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Belum ada bab') }}</h3>
                    <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:420px">{{ __('Tiada bab untuk :subject :grade lagi.', ['subject' => $subject->name, 'grade' => $grade->displayName()]) }}</p>
                </div>
            @else
                <div class="bab-split">
                    {{-- LEFT: chapter list (single column, or two in grid mode). --}}
                    <div class="bab-list" :class="view === 'grid' ? 'is-grid' : ''">
                        @foreach ($chapters as $chapter)
                            @php($c = $palette[$loop->index % count($palette)])
                            <button type="button" class="bab-item" :class="selected === {{ $loop->index }} ? 'is-on' : ''"
                                    @click="selected = {{ $loop->index }}" style="{{ $chapter->is_active ? '' : 'opacity:.7' }}">
                                <span class="bab-num" style="background:{{ $c['tint'] }};color:{{ $c['accent'] }}">{{ sprintf('%02d', $chapter->number) }}</span>
                                <span class="bab-itbody">
                                    <span class="bab-ittitle">
                                        {{ $chapter->title }}
                                        @unless ($chapter->is_active)
                                            <span class="tp-tag" style="background:#FEF0CE;color:#8A6A12">{{ __('Tidak aktif') }}</span>
                                        @endunless
                                    </span>
                                </span>
                                <span class="bab-chev"><x-icon name="chevron-right" class="h-5 w-5" /></span>
                            </button>
                        @endforeach
                    </div>

                    {{-- RIGHT: detail of the selected chapter (reactive). --}}
                    <template x-if="sel">
                    <div class="bab-detail" style="--acc:rgb({{ $subject->rgb }});--acc-tint:rgb({{ $subject->rgb }} / .14)">
                        <span class="bab-tag" style="background:var(--acc-tint);color:var(--acc)">{{ __('Bab') }} <span x-text="sel.number" style="margin-left:4px"></span></span>
                        <h2 class="bab-title" x-text="sel.title"></h2>

                        <div class="bab-headmeta">
                            <span class="tp-tag" style="background:var(--acc-tint);color:var(--acc)">{{ $subject->name }}</span>
                            <span class="bab-m"><x-icon name="video" class="h-4 w-4" style="color:#17907B" /><span x-text="sel.videos"></span> video</span>
                            <span class="bab-m"><x-icon name="file" class="h-4 w-4" style="color:#2E6CA8" /><span x-text="sel.bahan"></span> {{ __('bahan') }}</span>
                            <span class="bab-m"><x-icon name="help-circle" class="h-4 w-4" style="color:#7C5CBF" /><span x-text="sel.kuiz"></span> {{ __('kuiz') }}</span>
                        </div>

                        {{-- Hero banner: a subject-tinted panel with the subject glyph (no illustration). --}}
                        <div class="bab-hero" style="background:linear-gradient(135deg, rgb({{ $subject->rgb }} / .18), rgb({{ $subject->rgb }} / .06))">
                            <x-icon :name="$subject->iconName()" class="ic h-20 w-20" style="color:rgb({{ $subject->rgb }})" />
                        </div>

                        <h3 class="bab-sec-h">{{ __('Tentang Bab Ini') }}</h3>
                        <p class="bab-about" x-text="sel.description || '{{ __('Tiada penerangan untuk bab ini.') }}'"></p>

                        <h3 class="bab-sec-h">{{ __('Kandungan Bab') }}</h3>
                        <div class="bab-stats">
                            <div class="bab-stat">
                                <span class="sq" style="background:#DCF2EE;color:#17907B"><x-icon name="video" class="h-5 w-5" /></span>
                                <div><div class="n" x-text="sel.videos"></div><div class="l">{{ __('Video pembelajaran') }}</div></div>
                            </div>
                            <div class="bab-stat">
                                <span class="sq" style="background:#E4EEF9;color:#2E6CA8"><x-icon name="file" class="h-5 w-5" /></span>
                                <div><div class="n" x-text="sel.bahan"></div><div class="l">{{ __('Bahan bantuan mengajar') }}</div></div>
                            </div>
                            <div class="bab-stat">
                                <span class="sq" style="background:#EDE7F9;color:#7C5CBF"><x-icon name="help-circle" class="h-5 w-5" /></span>
                                <div><div class="n" x-text="sel.kuiz"></div><div class="l">{{ __('Kuiz interaktif') }}</div></div>
                            </div>
                        </div>

                        <div class="bab-listhdr">
                            <h3 class="bab-sec-h" style="margin:0">{{ __('Senarai Kandungan') }}</h3>
                            <a class="bab-seeall" :href="sel.showUrl">{{ __('Lihat semua') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                        </div>
                        <a class="bab-crow" :href="sel.showUrl">
                            <span class="nm"><x-icon name="video" class="h-5 w-5" style="color:#17907B" />{{ __('Video Pembelajaran') }}</span>
                            <span class="ct"><span x-text="sel.videos"></span> video <x-icon name="arrow-right" class="h-4 w-4" style="color:var(--tp-muted)" /></span>
                        </a>
                        <a class="bab-crow" :href="sel.showUrl">
                            <span class="nm"><x-icon name="file" class="h-5 w-5" style="color:#2E6CA8" />{{ __('Bahan Bantuan Mengajar') }}</span>
                            <span class="ct"><span x-text="sel.bahan"></span> {{ __('bahan') }} <x-icon name="arrow-right" class="h-4 w-4" style="color:var(--tp-muted)" /></span>
                        </a>
                        <a class="bab-crow" :href="sel.showUrl">
                            <span class="nm"><x-icon name="help-circle" class="h-5 w-5" style="color:#7C5CBF" />{{ __('Kuiz Interaktif') }}</span>
                            <span class="ct"><span x-text="sel.kuiz"></span> {{ __('kuiz') }} <x-icon name="arrow-right" class="h-4 w-4" style="color:var(--tp-muted)" /></span>
                        </a>

                        <a class="bab-cta" :href="sel.showUrl">{{ __('Lihat Bab') }} <x-icon name="arrow-right" class="h-5 w-5" /></a>
                    </div>
                    </template>
                </div>
            @endif
        @endif
    </div>

</x-cikgu-layout>
