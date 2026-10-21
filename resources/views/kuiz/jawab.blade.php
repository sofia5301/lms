<x-student-layout :title="$quiz->localizedTitle()">
    @php($isDark = ($theme ?? 'light') === 'dark')
    @php($questionCount = $questions->count())
    @php($maxScore = $questions->sum('points'))
    @php($selBg = $isDark ? 'rgba(23,144,123,.16)' : '#E8F5F1')

    <style>
        .qr-wrap { max-width:980px; margin:16px auto 0; width:100%; display:flex; flex-direction:column; gap:18px; }
        /* One big outer card holding every section as its own sub-card. */
        .qr-shell { background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:24px; box-shadow:0 12px 34px var(--wl-line); padding:28px; display:flex; flex-direction:column; gap:22px; }

        /* Header */
        .qr-headrow { display:flex; align-items:flex-start; gap:18px; }
        .qr-ic { width:58px; height:58px; border-radius:16px; flex-shrink:0; display:grid; place-items:center; background:{{ $isDark ? 'rgba(124,92,191,.18)' : '#EDE7F9' }}; color:#7C5CBF; }
        .qr-kuiz { font-family:'Geist',sans-serif; font-weight:800; font-size:13px; letter-spacing:.02em; color:#7C5CBF; }
        .qr-title { margin:2px 0 0; font-family:'Geist',sans-serif; font-size:27px; font-weight:800; letter-spacing:-.01em; color:var(--wl-ink); line-height:1.15; }
        .qr-desc { margin:8px 0 0; font-size:15px; color:var(--wl-muted); line-height:1.55; max-width:680px; }

        .qr-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
        @media (max-width:560px) { .qr-stats { grid-template-columns:1fr; } }
        .qr-stat { border:1px solid var(--wl-line); border-radius:16px; padding:14px 18px; display:flex; align-items:center; gap:13px; }
        .qr-stat .ic { width:46px; height:46px; border-radius:13px; display:grid; place-items:center; flex-shrink:0; }
        .qr-stat .l { font-size:12.5px; font-weight:700; color:var(--wl-muted); }
        .qr-stat .v { font-family:'Geist',sans-serif; font-size:21px; font-weight:800; color:var(--wl-ink); line-height:1; margin-top:4px; }

        /* Progress sub-card */
        .qr-prog { border:1px solid var(--wl-line); border-radius:16px; padding:18px 22px; }
        .qr-progtop { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
        .qr-progl { font-family:'Geist',sans-serif; font-weight:800; font-size:15px; color:var(--wl-ink); }
        .qr-progr { margin-left:auto; font-size:13.5px; font-weight:700; color:var(--wl-muted); }
        .qr-bar { height:12px; border-radius:999px; background:{{ $isDark ? 'var(--wl-line-2)' : '#E8EDF2' }}; overflow:hidden; }
        .qr-barfill { height:100%; border-radius:999px; background:linear-gradient(90deg,#17907B,#2BB39B); transition:width .25s ease-out; }
        .qr-timer { display:inline-flex; align-items:center; gap:7px; border-radius:999px; padding:7px 14px; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; }

        /* Question: a tinted sub-block (number + text), options stacked under it. */
        .qr-q { display:flex; flex-direction:column; gap:14px; }
        .qr-qhead { display:flex; align-items:center; gap:13px; border-radius:15px; padding:13px 16px; background:{{ $isDark ? 'rgba(23,144,123,.12)' : 'linear-gradient(180deg,#E7F4EF,#F2F9F6)' }}; border:1px solid {{ $isDark ? 'rgba(23,144,123,.18)' : '#DCEEE7' }}; }
        .qr-qnum { width:42px; height:42px; border-radius:12px; flex-shrink:0; display:grid; place-items:center; background:{{ $isDark ? 'rgba(23,144,123,.22)' : '#D8EEE6' }}; color:#0F7A68; font-family:'Geist',sans-serif; font-weight:800; font-size:17px; }
        .qr-qtext { margin:0; font-family:'Geist',sans-serif; font-size:17px; font-weight:800; line-height:1.3; color:var(--wl-ink); }
        .qr-qsub { margin:3px 0 0; font-size:13px; color:var(--wl-muted); }

        .qr-opts { display:flex; flex-direction:column; gap:12px; margin-top:2px; }
        .qopt { position:relative; display:flex; align-items:center; gap:16px; border:1.5px solid var(--wl-line-2); border-radius:16px; padding:15px 18px; cursor:pointer; background:var(--wl-surface); transition:border-color .12s, background .12s, box-shadow .12s; }
        .qopt:hover { border-color:#9FD8CB; }
        .qopt input { position:absolute; opacity:0; width:0; height:0; }
        .qopt:has(input:checked) { border-color:#17907B; background:{{ $selBg }}; box-shadow:0 6px 18px rgba(23,144,123,.12); }
        .qr-radio { width:26px; height:26px; flex-shrink:0; border-radius:50%; border:2px solid var(--wl-line-2); background:var(--wl-surface); display:grid; place-items:center; transition:all .12s; }
        .qopt.check .qr-radio { border-radius:8px; }
        .qopt:has(input:checked) .qr-radio { border-color:#17907B; background:#17907B; }
        .qr-radio .dot { width:11px; height:11px; border-radius:50%; background:#fff; opacity:0; transform:scale(.4); transition:all .12s; }
        .qopt.check .qr-radio .dot { border-radius:3px; }
        .qopt:has(input:checked) .qr-radio .dot { opacity:1; transform:scale(1); }
        .qr-letter { width:38px; height:38px; border-radius:11px; flex-shrink:0; display:grid; place-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:15px; }
        .qr-otext { flex:1; min-width:0; font-family:'Geist',sans-serif; font-weight:800; font-size:15.5px; color:var(--wl-ink); }

        /* Footer: its own card below the big card. */
        .qr-footcard { display:flex; align-items:center; gap:14px; background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:20px; box-shadow:0 8px 24px var(--wl-line); padding:18px 24px; }
        .qr-navbtn { min-height:56px; cursor:pointer; border-radius:16px; font-family:'Geist',sans-serif; font-weight:800; font-size:16px; padding:0 26px; display:inline-flex; align-items:center; justify-content:center; gap:10px; }
        .qr-prev { border:1.5px solid var(--wl-line-2); background:var(--wl-surface); color:var(--wl-ink); }
        .qr-next { border:none; background:#17907B; color:#fff; margin-left:auto; }

        @media (max-width:900px) {
            .qr-shell { padding:18px !important; gap:16px !important; }
            .qr-title { font-size:22px !important; }
            .qr-qhead { padding:16px !important; }
            .qr-qtext { font-size:18px !important; }
            .qr-qnum { width:44px !important; height:44px !important; font-size:17px !important; }
            .qr-letter { width:32px !important; height:32px !important; font-size:13px !important; }
            .qr-otext { font-size:14px !important; }
            .qr-navbtn { min-height:48px !important; font-size:14px !important; padding:0 18px !important; }
        }
    </style>

    <div class="qr-wrap"
         x-data="quizRunner({ total: {{ $questionCount }}, secondsLeft: {{ $secondsLeft === null ? 'null' : $secondsLeft }}, labels: { answered: @js(__('dijawab')) } })"
         x-init="start()">

        <noscript>
            <div style="background:#FEF0CE;border-radius:14px;padding:14px 18px;font-weight:700;font-size:14px;color:#8A6A12">{{ __('JavaScript perlu dihidupkan untuk menjawab kuiz ini.') }}</div>
        </noscript>

        <form method="POST" action="{{ route('kuiz.hantar', $attempt) }}" x-ref="form" @submit="submitting = true"
              style="display:flex;flex-direction:column;gap:18px">
            @csrf
            <div class="qr-shell">
        {{-- Header --}}
        <div class="qr-headrow">
            <span class="qr-ic"><x-icon name="help-circle" style="width:30px;height:30px" /></span>
            <div style="flex:1;min-width:0">
                <div class="qr-kuiz">{{ __('Kuiz') }}</div>
                <h1 class="qr-title">{{ $quiz->localizedTitle() }}</h1>
                @if ($quiz->localizedDescription())
                    <p class="qr-desc">{{ $quiz->localizedDescription() }}</p>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        <div class="qr-stats">
            <div class="qr-stat">
                <span class="ic" style="background:#DCF2EE;color:#0F7A68"><x-icon name="file-text" class="h-5 w-5" /></span>
                <div><div class="l">{{ __('Jumlah soalan') }}</div><div class="v">{{ $questionCount }}</div></div>
            </div>
            <div class="qr-stat">
                <span class="ic" style="background:#FBEAD3;color:#D9862B"><x-icon name="trophy" class="h-5 w-5" /></span>
                <div><div class="l">{{ __('Markah penuh') }}</div><div class="v">{{ $maxScore }}</div></div>
            </div>
            <div class="qr-stat">
                <span class="ic" style="background:#EDE7F9;color:#7C5CBF"><x-icon name="clock" class="h-5 w-5" /></span>
                <div><div class="l">{{ __('Masa') }}</div><div class="v">{{ $quiz->duration_minutes ? $quiz->duration_minutes.' '.__('minit') : __('Bebas') }}</div></div>
            </div>
        </div>

        {{-- Progress --}}
        <div class="qr-prog">
            <div class="qr-progtop">
                <span class="qr-progl">{{ __('Soalan') }} <span x-text="current + 1">1</span> {{ __('daripada') }} {{ $questionCount }}</span>
                @if ($secondsLeft !== null)
                    <span class="qr-timer" :style="secondsLeft < 60 ? { background: '#FDE7E0', color: '#C24936' } : { background: '#FBEECB', color: '#8A6A12' }"><x-icon name="clock" style="width:16px;height:16px" /><span x-text="clock()">{{ gmdate('i:s', $secondsLeft) }}</span></span>
                @endif
                <span class="qr-progr"><span x-text="answeredCount()">0</span>/{{ $questionCount }} {{ __('dijawab') }}</span>
            </div>
            <div class="qr-bar"><div class="qr-barfill" :style="{ width: ((current + 1) / total * 100) + '%' }" style="width:{{ $questionCount ? round(1 / $questionCount * 100, 1) : 0 }}%"></div></div>
        </div>

                {{-- Questions --}}
                @foreach ($questions as $index => $question)
                <section x-show="current === {{ $index }}" @if ($index > 0) x-cloak @endif class="qr-q">
                    <div class="qr-qhead">
                        <span class="qr-qnum">{{ $index + 1 }}</span>
                        <div style="flex:1;min-width:0">
                            <h3 class="qr-qtext">{{ $question->localizedText() }}</h3>
                            <p class="qr-qsub">{{ $question->isMultiple() ? __('Pilih semua jawapan yang betul. Semua mesti betul untuk mendapatkan markah.') : __('Pilih satu jawapan sahaja.') }}</p>
                        </div>
                    </div>

                    <div class="qr-opts">
                        @foreach ($question->options as $oIndex => $option)
                            {{-- Letter badge hue by position: green / blue / purple / orange. --}}
                            @php($pal = ($isDark ? [
                                ['bg' => 'rgba(23,144,123,.22)', 'fg' => '#4FD1B8'],
                                ['bg' => 'rgba(62,134,201,.22)', 'fg' => '#7BB7E8'],
                                ['bg' => 'rgba(124,92,191,.22)', 'fg' => '#B49CE6'],
                                ['bg' => 'rgba(217,154,40,.22)', 'fg' => '#E8B968'],
                            ] : [
                                ['bg' => '#E3F3EE', 'fg' => '#17907B'],
                                ['bg' => '#DCEBF8', 'fg' => '#3E86C9'],
                                ['bg' => '#EDE7F9', 'fg' => '#7C5CBF'],
                                ['bg' => '#FBF0D9', 'fg' => '#D9862B'],
                            ])[$oIndex % 4])
                            <label class="qopt {{ $question->isMultiple() ? 'check' : 'radio' }}">
                                <input type="{{ $question->isMultiple() ? 'checkbox' : 'radio' }}" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" @change="touch({{ $index }})">
                                <span class="qr-radio"><span class="dot"></span></span>
                                <span class="qr-letter" style="background:{{ $pal['bg'] }};color:{{ $pal['fg'] }}">{{ chr(65 + $oIndex) }}</span>
                                <span class="qr-otext">{{ $option->localizedText($question->source_locale) }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>
                @endforeach
            </div>

            {{-- Footer card: prev (left) + next / submit (right) --}}
            <div class="qr-footcard">
                <button type="button" @click="previous()" class="qr-navbtn qr-prev" :style="{ visibility: current === 0 ? 'hidden' : 'visible' }"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Soalan sebelumnya') }}</button>

                <button type="submit" x-show="current === total - 1" x-cloak :disabled="submitting" class="qr-navbtn qr-next">
                    <span x-show="! submitting">{{ __('Hantar Jawapan') }}</span><span x-show="submitting" x-cloak>{{ __('Menghantar...') }}</span>
                </button>
                <button type="button" x-show="current < total - 1" @click="next()" class="qr-navbtn qr-next">{{ __('Soalan seterusnya') }} <x-icon name="arrow-right" class="h-4 w-4" /></button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function quizRunner({ total, secondsLeft, labels }) {
                return {
                    total, current: 0, secondsLeft, labels, submitting: false,
                    answered: Array(total).fill(false), timer: null,
                    start() {
                        this.syncAnswered();
                        if (this.secondsLeft === null) return;
                        this.timer = setInterval(() => {
                            this.secondsLeft -= 1;
                            if (this.secondsLeft <= 0) { clearInterval(this.timer); this.autoSubmit(); }
                        }, 1000);
                    },
                    autoSubmit() { if (this.submitting) return; this.submitting = true; this.$refs.form.submit(); },
                    clock() {
                        const left = Math.max(0, this.secondsLeft ?? 0);
                        return String(Math.floor(left / 60)).padStart(2, '0') + ':' + String(left % 60).padStart(2, '0');
                    },
                    syncAnswered() {
                        this.$refs.form.querySelectorAll('section').forEach((section, index) => {
                            this.answered[index] = section.querySelectorAll('input:checked').length > 0;
                        });
                    },
                    touch(index) { this.$nextTick(() => this.syncAnswered()); },
                    answeredCount() { return this.answered.filter(Boolean).length; },
                    go(index) { this.current = Math.min(Math.max(index, 0), this.total - 1); window.scrollTo({ top: 0, behavior: 'smooth' }); },
                    next() { this.go(this.current + 1); },
                    previous() { this.go(this.current - 1); },
                };
            }
        </script>
    @endpush
</x-student-layout>
