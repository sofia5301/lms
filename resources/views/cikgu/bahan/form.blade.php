@php($editing = $material->exists)
@php($accept = collect(config('lms.material_mimes'))->map(fn ($e) => '.'.$e)->implode(','))

<x-cikgu-layout :title="$editing ? __('Sunting Bahan') : __('Bahan Baru')"
    :heading="$editing ? __('Sunting Bahan') : __('Bahan Baru')"
    heading-icon="file"
    :sub="__('Slaid, PDF dan lembaran kerja yang menyokong video anda')">

    <style>
        /* Add-material layout: the form (left) + a guide sidebar (right). Stacks on small screens. */
        .bm-layout { display:grid; grid-template-columns:minmax(0,1fr) 340px; gap:22px; align-items:start; }
        .bm-layout .tp-formwrap { max-width:none; }
        @media (max-width:960px) { .bm-layout { grid-template-columns:1fr; } .bm-guide { order:2; } }

        /* Numbered green badge beside each section heading. */
        .bm-secnum { display:inline-grid; place-items:center; width:26px; height:26px; border-radius:50%; background:var(--tp-teal); color:#fff; font-family:'Geist',sans-serif; font-weight:800; font-size:13px; flex-shrink:0; }

        /* Guide sidebar. */
        .bm-guide { display:flex; flex-direction:column; gap:16px; position:sticky; top:14px; }
        .bm-ghero { border-radius:16px; overflow:hidden; border:1px solid var(--tp-line); background:var(--tp-surface); }
        .bm-ghero .banner { height:140px; display:flex; align-items:center; justify-content:center; gap:12px; background:linear-gradient(135deg, rgba(23,144,123,.14), rgba(23,144,123,.03)); }
        .bm-ghero .banner .fg { width:50px; height:58px; border-radius:11px; display:grid; place-items:center; background:var(--tp-surface); box-shadow:0 6px 16px rgba(46,44,80,.1); }
        .bm-ghero .body { padding:15px 18px; }
        .bm-ghero .t { font-family:'Geist',sans-serif; font-weight:800; font-size:15px; color:var(--tp-ink); }
        .bm-ghero .s { font-size:13px; color:var(--tp-muted); margin-top:5px; line-height:1.5; }
        .bm-chips { display:flex; flex-wrap:wrap; gap:8px; margin-top:14px; }
        .bm-chip { display:flex; flex-direction:column; align-items:center; gap:5px; flex:1; min-width:54px; }
        .bm-chip .sq { width:40px; height:40px; border-radius:11px; display:grid; place-items:center; }
        .bm-chip .cl { font-size:10.5px; font-weight:800; color:var(--tp-muted); }
        .bm-gcard { border-radius:16px; border:1px solid var(--tp-line); padding:16px 18px; }
        .bm-gcard.g2 { background:rgba(46,108,168,.1); }
        .bm-gcard.g3 { background:rgba(217,134,43,.1); }
        .bm-gh { display:flex; align-items:center; gap:9px; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; color:var(--tp-ink); margin-bottom:12px; }
        .bm-gh .gi { width:30px; height:30px; border-radius:9px; display:grid; place-items:center; flex-shrink:0; }
        .bm-gli { display:flex; align-items:flex-start; gap:9px; font-size:12.5px; font-weight:700; color:var(--tp-ink); line-height:1.45; }
        .bm-gli + .bm-gli { margin-top:10px; }
        .bm-gli svg { flex-shrink:0; margin-top:1px; }
    </style>

    <a href="{{ route('cikgu.bahan.index') }}" class="tp-back" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:16px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Bahan Bantu Mengajar') }}</a>

    <div class="bm-layout">
        <form method="POST"
              action="{{ $editing ? route('cikgu.bahan.update', $material) : route('cikgu.bahan.store') }}"
              enctype="multipart/form-data" class="tp-formwrap">
            @csrf
            @if ($editing) @method('PUT') @endif

            {{-- 1. Maklumat Bahan --}}
            <div class="tp-panelform">
                <div style="display:flex;flex-direction:column;gap:3px">
                    <h2 class="tp-g" style="display:flex;align-items:center;gap:10px;font-size:17px;font-weight:800;color:var(--tp-ink)"><span class="bm-secnum">1</span>{{ __('Maklumat Bahan') }}</h2>
                    <span style="font-size:13px;color:var(--tp-muted)">{{ __('Masukkan maklumat asas bahan anda.') }}</span>
                </div>

                <x-chapter-picker :subjects="$subjects" :grades="$grades" :chapter="$chapter" />

                <div class="tp-field">
                    <label for="title" class="tp-label">{{ __('Tajuk Bahan') }}</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $material->title) }}" class="tp-input" placeholder="{{ __('Masukkan tajuk bahan') }}" maxlength="100" @error('title') aria-invalid="true" @enderror>
                    @error('title') <span class="tp-error">{{ $message }}</span> @enderror
                </div>

                <div class="tp-field">
                    <label for="description" class="tp-label">{{ __('Penerangan (pilihan)') }}</label>
                    <textarea id="description" name="description" rows="4" class="tp-textarea" maxlength="500" placeholder="{{ __('Terangkan kandungan bahan ini. Contoh: Lembaran kerja aktiviti, slaid pembelajaran, nota ringkas...') }}">{{ old('description', $material->description) }}</textarea>
                    @error('description') <span class="tp-error">{{ $message }}</span> @enderror
                </div>

                {{-- Attach to a video (optional). The list loads the chosen Bab's own videos. --}}
                <div class="tp-field" style="border-top:1px solid var(--tp-line);padding-top:16px"
                     x-data="videoAttach({
                         selected: {{ old('lesson_id', $material->lesson_id) ?: 'null' }},
                         preset: @js($lessons->map(fn ($l) => ['id' => $l->id, 'title' => $l->title])->values()),
                         endpoint: '{{ route('api.bab.video') }}',
                         labels: { loading: @js(__('Memuatkan video...')), none: @js(__('Tiada. Papar pada halaman Bab sahaja.')) },
                     })"
                     @chapter-changed.window="onChapter($event.detail.chapter)">
                    <label for="lesson_id" class="tp-label">{{ __('Lampirkan pada video (pilihan)') }}</label>
                    <select id="lesson_id" name="lesson_id" class="tp-select js-styled-select" x-model.number="selected" :disabled="loading">
                        <option value="" x-text="loading ? labels.loading : labels.none"></option>
                        <template x-for="v in videos" :key="v.id">
                            <option :value="v.id" x-text="v.title"></option>
                        </template>
                    </select>
                    <p class="tp-hint" x-show="! loading && videos.length === 0" x-cloak>{{ __('Tiada video dalam bab ini lagi. Pilih bab yang mempunyai video, atau biarkan kosong.') }}</p>
                    <p class="tp-hint">{{ __('Bahan yang dilampirkan dipaparkan di bawah pemain video, dalam bahagian "Bahan sokongan".') }}</p>
                    @error('lesson_id') <span class="tp-error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- 2. Muat Naik Fail --}}
            <div class="tp-panelform">
                <div style="display:flex;flex-direction:column;gap:3px">
                    <h2 class="tp-g" style="display:flex;align-items:center;gap:10px;font-size:17px;font-weight:800;color:var(--tp-ink)"><span class="bm-secnum">2</span>{{ __('Muat Naik Fail') }}</h2>
                    <span style="font-size:13px;color:var(--tp-muted)">{{ __('Sokongan fail: PDF, PowerPoint, Word, Excel, imej (JPG, PNG). Maksimum saiz :max MB setiap fail.', ['max' => config('lms.material_max_mb')]) }}</span>
                </div>

                @if ($editing)
                    {{-- The current file for this material. Read-only; the replace field below swaps it. --}}
                    <div class="tp-field">
                        <label class="tp-label">{{ __('Fail semasa') }}</label>
                        <div class="tp-filecard" style="display:flex;align-items:center;gap:14px;background:var(--tp-surface);border:1px solid var(--tp-line-2);border-radius:16px;padding:14px 16px">
                            <span style="width:46px;height:46px;border-radius:13px;background:#FBE4ED;color:#B84A75;display:grid;place-items:center;flex-shrink:0">
                                <x-icon :name="$material->iconName()" class="h-[22px] w-[22px]" />
                            </span>
                            <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:2px">
                                <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:14px;color:var(--tp-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $material->original_name }}</span>
                                <span style="font-size:12.5px;color:var(--tp-muted-2);font-weight:700">{{ strtoupper(pathinfo($material->original_name, PATHINFO_EXTENSION)) }} · {{ $material->humanSize() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Replace the file (optional). Leaving it empty keeps the current file. --}}
                    <div class="tp-field" x-data="{ dragging: false, picked: '', pick(f) { if (! f) return; const dt = new DataTransfer(); dt.items.add(f); this.$refs.file.files = dt.files; this.picked = f.name; } }">
                        <label class="tp-label">{{ __('Ganti fail (pilihan)') }}</label>
                        <div class="tp-dropzone" :class="dragging && 'is-dragging'" role="button" tabindex="0"
                             @click="$refs.file.click()" @keydown.enter.prevent="$refs.file.click()" @keydown.space.prevent="$refs.file.click()"
                             @dragover.prevent="dragging = true" @dragenter.prevent="dragging = true" @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; pick($event.dataTransfer?.files?.[0])">
                            <x-icon name="upload" class="h-7 w-7" style="color:var(--tp-teal)" />
                            <span class="tp-g" x-show="! picked" style="font-weight:800;font-size:14.5px;color:var(--tp-ink)">{{ __('Seret & lepaskan fail di sini') }}</span>
                            <span class="tp-g" x-show="picked" x-text="picked" x-cloak style="font-weight:800;font-size:14.5px;color:var(--tp-ink);word-break:break-all"></span>
                            <span class="tp-hint">{{ __('atau') }}</span>
                            <span class="tp-btn-outline" style="min-height:38px;padding:0 16px;font-size:13px;pointer-events:none"><x-icon name="file" class="h-4 w-4" /> {{ __('Pilih Fail') }}</span>
                        </div>
                        <input type="file" id="file" name="file" x-ref="file" class="sr-only" accept="{{ $accept }}" @change="picked = $event.target.files[0]?.name || ''">
                        <p class="tp-hint">{{ __('Biarkan kosong untuk kekalkan fail semasa. Fail baharu akan menggantikan fail lama. Had saiz :max MB.', ['max' => config('lms.material_max_mb')]) }}</p>
                        @error('file') <span class="tp-error">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div class="tp-field" x-data="{ dragging: false, picked: '', pick(f) { if (! f) return; const dt = new DataTransfer(); dt.items.add(f); this.$refs.file.files = dt.files; this.picked = f.name; } }">
                        <div class="tp-dropzone" :class="dragging && 'is-dragging'" role="button" tabindex="0"
                             @click="$refs.file.click()" @keydown.enter.prevent="$refs.file.click()" @keydown.space.prevent="$refs.file.click()"
                             @dragover.prevent="dragging = true" @dragenter.prevent="dragging = true" @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; pick($event.dataTransfer?.files?.[0])">
                            <x-icon name="upload" class="h-7 w-7" style="color:var(--tp-teal)" />
                            <span class="tp-g" x-show="! picked" style="font-weight:800;font-size:14.5px;color:var(--tp-ink)">{{ __('Seret & lepaskan fail di sini') }}</span>
                            <span class="tp-g" x-show="picked" x-text="picked" x-cloak style="font-weight:800;font-size:14.5px;color:var(--tp-ink);word-break:break-all"></span>
                            <span class="tp-hint" x-show="! picked">{{ __('atau') }}</span>
                            <span class="tp-btn-outline" style="min-height:38px;padding:0 16px;font-size:13px;pointer-events:none"><x-icon name="file" class="h-4 w-4" /> {{ __('Pilih Fail') }}</span>
                        </div>
                        <input type="file" id="file" name="file" x-ref="file" class="sr-only" accept="{{ $accept }}" @change="picked = $event.target.files[0]?.name || ''">
                        @error('file') <span class="tp-error">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            <div class="tp-formfooter" style="display:flex;gap:12px">
                <button type="submit" class="tp-btn" style="min-height:48px">{{ $editing ? __('Simpan Perubahan') : __('Muat Naik Bahan') }}</button>
                <a href="{{ route('cikgu.bahan.index') }}" class="tp-btn-outline" style="min-height:48px">{{ __('Batal') }}</a>
            </div>
        </form>

        {{-- Guide sidebar. --}}
        <aside class="bm-guide">
            <div class="bm-ghero">
                <div class="banner"><img src="{{ asset('images/document.png') }}" alt="" style="width:100%;height:100%;object-fit:cover"></div>
                <div class="body">
                    <div class="t">{{ __('Jenis Bahan yang Disokong') }}</div>
                    <div class="s">{{ __('Muat naik slaid, lembaran kerja, nota atau bahan tambahan yang berkaitan dengan video pembelajaran anda.') }}</div>
                    <div class="bm-chips">
                        <div class="bm-chip"><span class="sq" style="background:#FBE4EA;color:#D23B3B"><x-icon name="file-pdf" class="h-5 w-5" /></span><span class="cl">PDF</span></div>
                        <div class="bm-chip"><span class="sq" style="background:#FBE8D6;color:#C76A2B"><x-icon name="presentation" class="h-5 w-5" /></span><span class="cl">PowerPoint</span></div>
                        <div class="bm-chip"><span class="sq" style="background:#E4EEF9;color:#2E6CA8"><x-icon name="file-text" class="h-5 w-5" /></span><span class="cl">Word</span></div>
                        <div class="bm-chip"><span class="sq" style="background:#DCF2EE;color:#1E7A58"><x-icon name="table" class="h-5 w-5" /></span><span class="cl">Excel</span></div>
                        <div class="bm-chip"><span class="sq" style="background:#EDE7F9;color:#7C5CBF"><x-icon name="photo" class="h-5 w-5" /></span><span class="cl">{{ __('Imej') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="bm-gcard g2">
                <div class="bm-gh"><span class="gi" style="background:#E4EEF9;color:#2E6CA8"><x-icon name="bulb" class="h-4 w-4" /></span>{{ __('Tips Memuat Naik Bahan') }}</div>
                <div class="bm-gli"><x-icon name="check" class="h-4 w-4" style="color:#2E6CA8" />{{ __('Pastikan fail tidak melebihi :max MB.', ['max' => config('lms.material_max_mb')]) }}</div>
                <div class="bm-gli"><x-icon name="check" class="h-4 w-4" style="color:#2E6CA8" />{{ __('Gunakan nama fail yang jelas dan mudah difahami.') }}</div>
                <div class="bm-gli"><x-icon name="check" class="h-4 w-4" style="color:#2E6CA8" />{{ __('Pastikan kandungan bahan sesuai dengan bab yang dipilih.') }}</div>
                <div class="bm-gli"><x-icon name="check" class="h-4 w-4" style="color:#2E6CA8" />{{ __('Bahan yang dimuat naik akan dipaparkan di halaman Bab untuk murid.') }}</div>
            </div>

            <div class="bm-gcard g3">
                <div class="bm-gh"><span class="gi" style="background:#FBEAD3;color:#D9862B"><x-icon name="alert" class="h-4 w-4" /></span>{{ __('Perkara yang perlu dielakkan') }}</div>
                <div class="bm-gli"><x-icon name="x-circle" class="h-4 w-4" style="color:#C24936" />{{ __('Jangan muat naik fail yang mengandungi maklumat peribadi murid.') }}</div>
                <div class="bm-gli"><x-icon name="x-circle" class="h-4 w-4" style="color:#C24936" />{{ __('Elakkan bahan yang melanggar hak cipta.') }}</div>
                <div class="bm-gli"><x-icon name="x-circle" class="h-4 w-4" style="color:#C24936" />{{ __('Pastikan kandungan sesuai untuk murid sekolah rendah.') }}</div>
            </div>
        </aside>
    </div>

    @push('scripts')
        <script>
            function videoAttach({ selected, preset, endpoint, labels }) {
                return {
                    selected,
                    videos: preset ?? [],
                    endpoint,
                    labels,
                    loading: false,

                    onChapter(chapterId) {
                        if (! chapterId) { this.videos = []; return; }

                        const keep = this.selected;
                        this.loading = true;

                        fetch(`${this.endpoint}?chapter=${chapterId}`, { headers: { 'Accept': 'application/json' } })
                            .then((response) => response.ok ? response.json() : [])
                            .then((data) => {
                                this.videos = data;
                                // Keep the current pick only if it belongs to the new Bab.
                                this.$nextTick(() => {
                                    this.selected = data.some((v) => v.id === keep) ? keep : null;
                                });
                            })
                            .catch(() => { this.videos = []; })
                            .finally(() => { this.loading = false; });
                    },
                };
            }
        </script>
    @endpush
</x-cikgu-layout>
