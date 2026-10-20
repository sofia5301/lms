@php
    $editing = $quiz->exists;
    $type = old('type', $quiz->type);
@endphp

<x-cikgu-layout :title="$editing ? __('Sunting Kuiz') : __('Kuiz Baru')"
    :heading="$editing ? __('Sunting Kuiz') : __('Kuiz Baru')"
    heading-icon="quiz"
    :sub="__('Kuiz interaktif yang menanda sendiri, dan kuiz bercetak')">

    <form method="POST"
          action="{{ $editing ? route('cikgu.kuiz.update', $quiz) : route('cikgu.kuiz.store') }}"
          enctype="multipart/form-data" class="tp-formwrap" style="max-width:1140px;margin-left:auto;margin-right:auto;width:100%"
          x-data="quizForm({{ Js::from([
              'type' => $type,
              'translate' => [
                  'enabled' => $translatorEnabled,
                  'url' => route('cikgu.kuiz.terjemah-meta'),
                  'failed' => __('Terjemahan gagal. Sila cuba lagi.'),
                  'needTitle' => __('Isi tajuk dahulu.'),
                  'en2ms' => __('English → Bahasa Melayu'),
                  'ms2en' => __('Bahasa Melayu → English'),
                  'hint' => __('Terjemah tajuk & penerangan. Semak sebelum simpan.'),
              ],
              'titleT' => old('title_translated', $quiz->title_translated),
              'descriptionT' => old('description_translated', $quiz->description_translated),
              'sourceLocale' => old('source_locale', $quiz->source_locale),
          ]) }})"
          @input="refresh()" @change="refresh()" @chapter-changed="refresh()">
        @csrf
        @if ($editing) @method('PUT') @endif
        <input type="hidden" name="type" :value="type">

        {{-- Back: to the chapter it came from (when created from a Bab), else the quiz-mode step or
             the quizzes list. --}}
        @if (! $editing && ($chapter ?? null))
            <a href="{{ route('cikgu.bab.show', $chapter) }}" class="tp-back" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Bab :number', ['number' => $chapter->number]) }}</a>
        @else
            <a href="{{ $editing ? route('cikgu.kuiz.index') : route('cikgu.kuiz.mod') }}" class="tp-back" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Kembali') }}</a>
        @endif

        @if ($editing && ($hasAttempts ?? false))
            <div style="display:flex;gap:10px;background:#FEF0CE;border:1px solid rgba(138,106,18,.25);border-radius:14px;padding:14px 18px;font-size:13.5px;color:#8A6A12">
                <x-icon name="alert" class="h-5 w-5" style="flex-shrink:0;margin-top:1px" />
                <div>{{ __('Kuiz ini sudah ada percubaan murid. Menukar soalan akan menggantikan semua soalan lama, dan semakan jawapan percubaan lama tidak lagi dapat dipaparkan. Mata dan ranking yang sudah diperoleh murid kekal tidak berubah.') }}</div>
            </div>
        @endif

        {{-- Progress rail (visual) across the four sections of the form. --}}
        <div class="kf-steps">
            <div class="kf-step" :class="{ 'is-on': active === 1, 'is-done': f1 }"><span class="n"><span x-show="! f1">1</span><x-icon name="check" class="h-[15px] w-[15px]" x-show="f1" x-cloak /></span><span class="l">{{ __('Jenis kuiz') }}</span></div>
            <div class="kf-step" :class="{ 'is-on': active === 2, 'is-done': f2 }"><span class="n"><span x-show="! f2">2</span><x-icon name="check" class="h-[15px] w-[15px]" x-show="f2" x-cloak /></span><span class="l">{{ __('Butiran kuiz') }}</span></div>
            <div class="kf-step" :class="{ 'is-on': active === 3, 'is-done': f3 }"><span class="n"><span x-show="! f3">3</span><x-icon name="check" class="h-[15px] w-[15px]" x-show="f3" x-cloak /></span><span class="l">{{ __('Lokasi kuiz') }}</span></div>
            <div class="kf-step" :class="{ 'is-on': active === 4, 'is-done': f4 }"><span class="n"><span x-show="! f4">4</span><x-icon name="check" class="h-[15px] w-[15px]" x-show="f4" x-cloak /></span><span class="l">{{ __('Tetapan') }}</span></div>
        </div>

        <div class="kf-grid">
            {{-- ── Left column: type + details ─────────────────────────────────────────── --}}
            <div class="kf-col">
                {{-- 1. Quiz type --}}
                <div class="kf-card kf-c1">
                    <div class="kf-band">
                        <span class="kf-badge">1</span>
                        <div><h2 class="kf-title tp-g">{{ __('Jenis kuiz') }}</h2><span class="kf-sub">{{ __('Pilih jenis kuiz yang ingin anda buat.') }}</span></div>
                    </div>

                    <div class="kf-types">
                        <button type="button" @click="type = 'interactive'; refresh()" class="kf-type" :class="{ 'is-on': type === 'interactive' }" :aria-pressed="type === 'interactive'">
                            <span class="kf-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                            <span class="kf-type-row">
                                <x-icon name="quiz" class="h-[19px] w-[19px]" style="color:#17907B;flex-shrink:0" />
                                <span class="kf-type-title tp-g">{{ __('Kuiz Interaktif') }}</span>
                            </span>
                            <span class="kf-type-desc">{{ __('Ditanda secara automatik. Memberi mata ranking.') }}</span>
                        </button>

                        <button type="button" @click="type = 'file'; refresh()" class="kf-type" :class="{ 'is-on': type === 'file' }" :aria-pressed="type === 'file'">
                            <span class="kf-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                            <span class="kf-type-row">
                                <x-icon name="file-text" class="h-[19px] w-[19px]" style="color:#2E6CA8;flex-shrink:0" />
                                <span class="kf-type-title tp-g">{{ __('Kuiz Bercetak') }}</span>
                            </span>
                            <span class="kf-type-desc">{{ __('Fail untuk dimuat turun. Tiada mata.') }}</span>
                        </button>
                    </div>
                    @error('type') <span class="tp-error">{{ $message }}</span> @enderror
                </div>

                {{-- 2. Details --}}
                <div class="kf-card kf-c2">
                    <div class="kf-band">
                        <span class="kf-badge">2</span>
                        <div><h2 class="kf-title tp-g">{{ __('Butiran kuiz') }}</h2><span class="kf-sub">{{ __('Masukkan maklumat kuiz anda.') }}</span></div>
                        <span class="kf-bandic"><x-icon name="pencil" class="h-6 w-6" /></span>
                    </div>

                    {{-- A single shared title for an interactive quiz (and when editing any quiz). Printed
                         quizzes created in a batch are titled per file in the drop zone below instead. --}}
                    <div class="tp-field" x-show="type === 'interactive' || {{ $editing ? 'true' : 'false' }}" @unless ($editing) x-cloak @endunless>
                        <label for="title" class="tp-label">{{ __('Tajuk') }}</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $quiz->title) }}" class="tp-input" @error('title') aria-invalid="true" @enderror>
                        @error('title') <span class="tp-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="tp-field">
                        <label for="description" class="tp-label">{{ __('Penerangan (pilihan)') }}</label>
                        <textarea id="description" name="description" rows="3" class="tp-textarea">{{ old('description', $quiz->description) }}</textarea>
                        @error('description') <span class="tp-error">{{ $message }}</span> @enderror
                    </div>

                    @if ($translatorEnabled)
                        {{-- Auto-translate the title + description for the language toggle. Editable so a
                             wrong machine translation is corrected before students see it; runs on save
                             too, so it is optional. --}}
                        <div x-show="type === 'interactive' || {{ $editing ? 'true' : 'false' }}" @unless ($editing) x-cloak @endunless
                             style="border-top:1px solid var(--tp-line);padding-top:14px;display:flex;flex-direction:column;gap:12px">
                            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                                <div style="flex:1;min-width:180px;display:flex;flex-direction:column;gap:2px">
                                    <span class="tp-g" style="font-size:14px;font-weight:800;color:var(--tp-ink)">{{ __('Terjemahan automatik') }}</span>
                                    <span style="font-size:12.5px;color:var(--tp-muted)"
                                          x-text="sourceLocale ? (sourceLocale === 'en' ? translate.en2ms : translate.ms2en) : translate.hint"></span>
                                </div>
                                <span style="font-size:12.5px;font-weight:700;color:#C24936" x-show="translateError" x-cloak x-text="translateError"></span>
                                <button type="button" class="tp-btn-outline tp-btn-sm" @click="translateMeta()" :disabled="translating">
                                    <span x-show="! translating" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="sparkles" class="h-4 w-4" /> {{ __('Terjemah automatik') }}</span>
                                    <span x-show="translating" x-cloak>{{ __('Menterjemah...') }}</span>
                                </button>
                            </div>

                            <input type="hidden" name="source_locale" :value="sourceLocale || ''">

                            <div class="tp-field">
                                <label for="title_translated" class="tp-label">{{ __('Tajuk (terjemahan)') }}</label>
                                <input id="title_translated" name="title_translated" type="text" x-model="titleT" class="tp-input" placeholder="{{ __('Terjemahan tajuk') }}">
                            </div>
                            <div class="tp-field">
                                <label for="description_translated" class="tp-label">{{ __('Penerangan (terjemahan)') }}</label>
                                <textarea id="description_translated" name="description_translated" rows="3" x-model="descriptionT" class="tp-textarea" placeholder="{{ __('Terjemahan penerangan') }}"></textarea>
                            </div>
                        </div>
                    @endif

                    {{-- Interactive only: time limit --}}
                    <div x-show="type === 'interactive'" x-cloak class="tp-field">
                        <label for="duration_minutes" class="tp-label">{{ __('Had masa dalam minit (pilihan)') }}</label>
                        <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="180"
                               value="{{ old('duration_minutes', $quiz->duration_minutes) }}" class="tp-input" aria-describedby="duration-help" @error('duration_minutes') aria-invalid="true" @enderror>
                        <p id="duration-help" class="tp-hint">{{ __('Biarkan kosong untuk kuiz tanpa had masa. Jika ditetapkan, jawapan murid dihantar secara automatik apabila masa tamat.') }}</p>
                        @error('duration_minutes') <span class="tp-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- File only --}}
                    @if ($editing)
                        {{-- Editing replaces the one file this quiz points at, so it stays single. --}}
                        <div x-show="type === 'file'" x-cloak class="tp-field">
                            <label for="file" class="tp-label">{{ __('Fail kuiz') }}</label>
                            <x-file-input id="file" name="file" accept=".pdf,.doc,.docx" aria-describedby="quiz-file-help" @error('file') aria-invalid="true" @enderror />
                            <p id="quiz-file-help" class="tp-hint">
                                {{ __('PDF, DOC atau DOCX. Had saiz :size MB.', ['size' => config('lms.quiz_file_max_mb')]) }}
                                @if ($quiz->file_path) {{ __('Biarkan kosong untuk mengekalkan fail sedia ada (:name).', ['name' => $quiz->original_name]) }} @endif
                            </p>
                            @error('file') <span class="tp-error">{{ $message }}</span> @enderror
                        </div>
                    @else
                        {{-- Creating takes many files at once, each becoming its own printed quiz, titled
                             per row - the same drop zone the material and video uploads use. --}}
                        <div x-show="type === 'file'" x-cloak>
                            <x-file-dropzone
                                name="files[]"
                                title-name="titles[]"
                                :extensions="config('lms.quiz_file_mimes')"
                                :accept="collect(config('lms.quiz_file_mimes'))->map(fn ($e) => '.'.$e)->implode(',')"
                                :max-mb="config('lms.quiz_file_max_mb')"
                                :max-files="\App\Http\Requests\QuizRequest::MAX_FILES"
                                :hint="__('PDF, DOC atau DOCX. Had saiz :size MB setiap fail.', ['size' => config('lms.quiz_file_max_mb')])"
                                :title-label="__('Tajuk kuiz (untuk pelajar)')" />

                            @error('files') <span class="tp-error">{{ $message }}</span> @enderror
                            @foreach ($errors->get('files.*') as $messages)
                                <span class="tp-error">{{ $messages[0] }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Right column: location + settings ───────────────────────────────────── --}}
            <div class="kf-col">
                {{-- 3. Location --}}
                <div class="kf-card kf-c3">
                    <div class="kf-band">
                        <span class="kf-badge">3</span>
                        <div>
                            <h2 class="kf-title tp-g">{{ __('Lokasi kuiz') }}</h2>
                            <span class="kf-sub">{{ __('Kuiz ini akan dipaparkan pada halaman Bab tersebut.') }}</span>
                        </div>
                        <span class="kf-bandic"><x-icon name="map-pin" class="h-6 w-6" /></span>
                    </div>
                    <x-chapter-picker :subjects="$subjects" :grades="$grades" :chapter="$chapter" />
                </div>

                {{-- 4. Settings --}}
                <div class="kf-card kf-c4">
                    <div class="kf-band">
                        <span class="kf-badge">4</span>
                        <div><h2 class="kf-title tp-g">{{ __('Tetapan') }}</h2><span class="kf-sub">{{ __('Tetapkan pilihan tambahan untuk kuiz ini.') }}</span></div>
                        <span class="kf-bandic"><x-icon name="settings" class="h-6 w-6" /></span>
                    </div>

                    <label for="shuffle_options" class="kf-setrow" x-show="type === 'interactive'" x-cloak>
                        <input id="shuffle_options" name="shuffle_options" type="checkbox" value="1" @checked(old('shuffle_options', $quiz->shuffle_options ?? false)) style="width:20px;height:20px;margin-top:1px;accent-color:#17907B;flex-shrink:0">
                        <span style="display:flex;flex-direction:column;gap:2px">
                            <span class="tp-g" style="font-weight:800;font-size:14.5px;color:var(--tp-ink)">{{ __('Acak susunan jawapan') }}</span>
                            <span style="font-size:12.5px;color:var(--tp-muted)">{{ __('Setiap murid melihat pilihan jawapan dalam susunan rawak.') }}</span>
                        </span>
                    </label>

                    <label for="is_published" class="kf-setrow">
                        <input id="is_published" name="is_published" type="checkbox" value="1" @checked(old('is_published', $quiz->is_published ?? true)) style="width:20px;height:20px;margin-top:1px;accent-color:#17907B;flex-shrink:0">
                        <span style="display:flex;flex-direction:column;gap:2px">
                            <span class="tp-g" style="font-weight:800;font-size:14.5px;color:var(--tp-ink)">{{ __('Terbitkan kepada murid') }}</span>
                            <span style="font-size:12.5px;color:var(--tp-muted)">{{ __('Nyahtanda untuk simpan sebagai draf.') }}</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Action bar spanning both columns. --}}
        <div class="kf-actionbar">

            @if ($editing && $quiz->isInteractive())
                <a href="{{ route('cikgu.kuiz.soalan', $quiz) }}" class="tp-btn-outline" style="min-height:48px;display:inline-flex;align-items:center;gap:8px"><x-icon name="pencil" class="h-4 w-4" /> {{ __('Sunting Soalan') }}</a>
            @endif

            <a href="{{ route('cikgu.kuiz.index') }}" class="tp-btn-outline" style="min-height:48px">{{ __('Batal') }}</a>

            <button type="submit" class="tp-btn" style="min-height:48px;display:inline-flex;align-items:center;gap:9px">
                <span x-show="type === 'interactive' && ! {{ $editing ? 'true' : 'false' }}" x-cloak>{{ __('Seterusnya: Tambah Soalan') }}</span>
                <span x-show="type === 'file' || {{ $editing ? 'true' : 'false' }}" @unless ($editing) x-cloak @endunless>{{ $editing ? __('Simpan Perubahan') : __('Simpan Kuiz') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </div>
    </form>

    @once
        <style>
            /* Top progress rail. */
            .kf-steps { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin:0 auto 26px; max-width:1040px; width:100%; }
            .kf-step { flex:1; display:flex; flex-direction:column; align-items:center; gap:10px; position:relative; }
            .kf-step .l { white-space:nowrap; }
            .kf-step:not(:last-child)::after { content:''; position:absolute; top:17px; left:50%; width:100%; height:2px; background:var(--tp-line); z-index:0; }
            .kf-step .n { position:relative; z-index:1; width:36px; height:36px; border-radius:50%; background:var(--tp-surface); border:1.5px solid var(--tp-line-2); color:var(--tp-muted); display:grid; place-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; }
            /* Done: green ring + tick. Active (current section): solid teal - wins when a step is both. */
            .kf-step.is-done .n { background:#DCF2EE; color:#0F7A68; border-color:#0F7A68; }
            .kf-step.is-on .n { background:var(--tp-teal); color:#fff; border-color:var(--tp-teal); }
            .kf-step .l { font-family:'Geist',sans-serif; font-weight:800; font-size:13px; color:var(--tp-muted); text-align:center; }
            .kf-step.is-on .l, .kf-step.is-done .l { color:var(--tp-teal); }
            @media (max-width:640px) { .kf-step .l { font-size:11px; white-space:normal; } }

            /* Coloured header band per card. */
            .kf-band { display:flex; align-items:center; gap:13px; padding:14px 16px; border-radius:14px; }
            .kf-band > div { min-width:0; }
            .kf-band .kf-badge { width:36px; height:36px; font-size:15px; }
            .kf-bandic { margin-left:auto; flex-shrink:0; }
            .kf-c1 .kf-band { background:rgba(23,144,123,.1); } .kf-c1 .kf-badge { background:#DCF2EE; color:#0F7A68; } .kf-c1 .kf-bandic { color:#0F7A68; }
            .kf-c2 .kf-band { background:rgba(217,134,43,.1); } .kf-c2 .kf-badge { background:#F7E6C6; color:#BE922F; } .kf-c2 .kf-bandic { color:#BE922F; }
            .kf-c3 .kf-band { background:rgba(46,108,168,.1); } .kf-c3 .kf-badge { background:#E4EEF9; color:#2E6CA8; } .kf-c3 .kf-bandic { color:#2E6CA8; }
            .kf-c4 .kf-band { background:rgba(23,144,123,.1); } .kf-c4 .kf-badge { background:#DCF2EE; color:#0F7A68; } .kf-c4 .kf-bandic { color:#0F7A68; }

            /* Four cards on a 2x2 grid, filled column-by-column (card1/card2 left, card3/card4 right).
               Each grid row stretches its two cards to equal height: card1=card3, card2=card4. */
            .kf-grid { display:grid; grid-template-columns:1fr 1fr; grid-template-rows:auto auto; grid-auto-flow:column; gap:20px; }
            .kf-col  { display:contents; }
            .kf-card {
                background:var(--tp-surface); border:1px solid var(--tp-line); border-radius:18px;
                padding:22px 24px; display:flex; flex-direction:column; gap:16px; min-width:0;
                box-shadow:0 2px 10px rgba(46,44,80,.04);
            }
            .kf-head  { display:flex; align-items:flex-start; gap:12px; }
            .kf-badge {
                width:28px; height:28px; flex-shrink:0; border-radius:50%; background:#DCF2EE;
                color:#0F7A68; display:grid; place-items:center;
                font-family:'Geist',sans-serif; font-weight:800; font-size:13px;
            }
            .kf-title { margin:0; font-size:17px; font-weight:800; color:var(--tp-ink); }
            .kf-sub   { display:block; font-size:13px; color:var(--tp-muted); margin-top:2px; }

            .kf-types { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
            .kf-type  {
                position:relative; text-align:left; border:1.5px solid var(--tp-line-2); border-radius:16px;
                padding:18px 40px 16px 18px; background:var(--tp-surface); cursor:pointer;
                display:flex; flex-direction:column; gap:8px;
                transition:border-color .15s, background .15s;
            }
            .kf-type:hover  { border-color:#B9D9D0; }
            .kf-type.is-on  { border-color:#17907B; background:#F1FAF7; }
            /* Dark mode: the light-green selected fill hid the light ink; use a deep teal tint. */
            html.theme-dark .kf-type.is-on { border-color:#2BB39B; background:rgba(43,179,155,.14); }
            html.theme-dark .kf-type:hover { border-color:#2BB39B; }
            .kf-type-row    { display:flex; align-items:center; gap:9px; }
            .kf-type-title  { font-weight:800; font-size:14.5px; color:var(--tp-ink); }
            .kf-type-desc   { font-size:12.5px; color:var(--tp-muted-2); line-height:1.45; }
            .kf-mark {
                position:absolute; top:15px; right:15px; width:22px; height:22px; border-radius:50%;
                border:2px solid var(--tp-line-2); background:var(--tp-surface);
                display:grid; place-items:center; transition:border-color .15s, background .15s;
            }
            .kf-mark svg { width:12px; height:12px; color:#fff; opacity:0; transition:opacity .15s; }
            .kf-type.is-on .kf-mark      { border-color:#17907B; background:#17907B; }
            .kf-type.is-on .kf-mark svg  { opacity:1; }

            .kf-setrow {
                display:flex; align-items:flex-start; gap:12px; border:1.5px solid var(--tp-line-2);
                border-radius:14px; padding:15px 16px; cursor:pointer; transition:border-color .15s;
            }
            .kf-setrow:hover { border-color:#B9D9D0; }

            .kf-actionbar {
                position:relative; overflow:hidden; display:flex; justify-content:flex-end;
                align-items:center; gap:12px; flex-wrap:wrap; background:var(--tp-surface);
                border:1px solid var(--tp-line); border-radius:18px; padding:16px 22px;
                box-shadow:0 2px 10px rgba(46,44,80,.04);
            }
            .kf-actionleaf {
                position:absolute; left:-20px; bottom:-26px; width:132px; height:112px; z-index:0;
                background:url('{{ asset('images/leaf.png') }}') center / contain no-repeat;
                opacity:.42; pointer-events:none;
            }
            .kf-actionbar > a, .kf-actionbar > button { position:relative; z-index:1; }

            @media (max-width:820px) {
                .kf-grid  { grid-template-columns:1fr; grid-template-rows:none; grid-auto-flow:row; }
                .kf-types { grid-template-columns:1fr; }
            }
            @media (max-width:900px) {
                .kf-title { font-size:15px; }
                .kf-type-title { font-size:13px; }
                .kf-type-desc { font-size:11.5px; }
                .kf-setrow .tp-g { font-size:13px !important; }
                .kf-setrow [style*="font-size:12.5px"] { font-size:11.5px !important; }
                /* All action buttons share the row equally (text wraps inside if needed) so up to
                   three sit side by side on one line. */
                .kf-actionbar { gap:6px !important; padding:12px 12px !important; flex-wrap:nowrap !important; overflow:visible !important; align-items:stretch !important; }
                .kf-actionbar .tp-btn, .kf-actionbar .tp-btn-outline { flex:1 1 0 !important; min-width:0 !important; min-height:36px !important; font-size:10px !important; padding:0 7px !important; gap:4px !important; text-align:center; line-height:1.15; }
                .kf-actionbar .tp-btn svg, .kf-actionbar .tp-btn-outline svg { width:13px !important; height:13px !important; }
            }
        </style>
    @endonce

    @push('scripts')
        <script>
            function quizForm({ type, translate, titleT, descriptionT, sourceLocale }) {
                return {
                    type,
                    translate,
                    titleT: titleT ?? '',
                    descriptionT: descriptionT ?? '',
                    sourceLocale: sourceLocale ?? '',
                    translating: false,
                    translateError: '',

                    // Step rail: which step is current (1-4) and which are complete. Display only -
                    // these never gate submission.
                    active: 1, f1: false, f2: false, f3: false, f4: false,

                    init() {
                        this.$nextTick(() => this.refresh());
                        setTimeout(() => this.refresh(), 500);
                    },

                    fileChosen() {
                        return Array.from(document.querySelectorAll('input[type=file]')).some((i) => i.files && i.files.length);
                    },

                    /** Recompute step completion. 1 Jenis (a type is chosen), 2 Butiran (title for an
                     *  interactive quiz, or a file for a printed one), 3 Lokasi (a Bab is picked),
                     *  4 Tetapan (ready once 1-3 are done). Active = the first step not yet complete. */
                    refresh() {
                        const val = (id) => (document.getElementById(id)?.value || '').trim();
                        this.f1 = !! this.type;
                        this.f2 = this.type === 'interactive' ? !! val('title') : this.fileChosen();
                        this.f3 = !! val('chapter_id');
                        this.f4 = this.f1 && this.f2 && this.f3;
                        this.active = ! this.f1 ? 1 : (! this.f2 ? 2 : (! this.f3 ? 3 : 4));
                    },

                    csrf() { return document.querySelector('input[name="_token"]')?.value ?? ''; },
                    async translateMeta() {
                        if (! this.translate.enabled || this.translating) return;
                        const title = document.getElementById('title')?.value?.trim() ?? '';
                        const description = document.getElementById('description')?.value?.trim() ?? '';
                        if (! title) { this.translateError = this.translate.needTitle; return; }
                        this.translateError = '';
                        this.translating = true;
                        try {
                            const res = await fetch(this.translate.url, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                                body: JSON.stringify({ title, description }),
                            });
                            if (! res.ok) {
                                const err = await res.json().catch(() => ({}));
                                throw new Error(err.message || this.translate.failed);
                            }
                            const data = await res.json();
                            this.titleT = data.title ?? '';
                            this.descriptionT = data.description ?? '';
                            this.sourceLocale = data.source_locale ?? '';
                        } catch (e) {
                            this.translateError = e.message || this.translate.failed;
                        } finally {
                            this.translating = false;
                        }
                    },
                };
            }
        </script>
    @endpush
</x-cikgu-layout>
