<x-dynamic-component :component="auth()->user()->isTeacher() ? 'app-layout' : 'student-layout'" :title="$quiz->localizedTitle()">
    @php($col = $subject->color ?: '#17907B')
    @php($isDark = ($theme ?? 'light') === 'dark')
    @php($tagBg = "color-mix(in oklab, {$col} var(--pill-bw), var(--pill-bb))")
    @php($tagColor = "color-mix(in oklab, {$col} var(--pill-fw), var(--pill-fb))")
    @php($backToQuizzes = request('from') === 'quizzes')
    @php($last = $myAttempts->first())
    @php($panelBg = $isDark ? 'rgba(23,144,123,.07)' : 'rgba(23,144,123,.045)')

    <style>
        .qi-wrap { max-width:1160px; margin:0 auto; width:100%; display:flex; flex-direction:column; gap:22px; }
        .qi-crumb { display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:var(--wl-muted); }
        .qi-crumb .cur { color:var(--wl-ink); } .qi-crumb .sep { color:var(--wl-muted-2); display:inline-flex; }

        .qi-hero { display:flex; align-items:center; gap:24px; flex-wrap:wrap; }
        .qi-heroimg { width:220px; height:150px; flex-shrink:0; border-radius:18px; position:relative; overflow:hidden; display:grid; place-items:center; background:linear-gradient(135deg, rgba(23,144,123,.16), rgba(23,144,123,.04)); }
        @media (max-width:680px) { .qi-heroimg { display:none; } }

        .qi-grid { display:grid; grid-template-columns:minmax(0,1.7fr) minmax(0,1fr); gap:20px; align-items:stretch; }
        @media (max-width:900px) { .qi-side .qi-grow { flex:0 1 auto; } }
        .qi-grow { flex:1 1 auto; }
        @media (max-width:900px) { .qi-grid { grid-template-columns:1fr; } }
        .qi-main, .qi-side { display:flex; flex-direction:column; gap:18px; min-width:0; }

        .qi-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
        @media (max-width:560px) { .qi-stats { grid-template-columns:1fr; } }
        .qi-stat { background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:16px; padding:15px 18px; display:flex; align-items:center; gap:13px; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .qi-stat .ic { width:46px; height:46px; border-radius:50%; display:grid; place-items:center; flex-shrink:0; }
        .qi-stat .l { font-size:12.5px; font-weight:700; color:var(--wl-muted); }
        .qi-stat .v { font-family:'Geist',sans-serif; font-size:21px; font-weight:800; color:var(--wl-ink); line-height:1; margin-top:4px; }

        .qi-card { background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:18px; padding:22px; box-shadow:0 3px 12px rgba(46,44,80,.04); }
        .qi-sech { display:flex; align-items:center; gap:10px; font-family:'Geist',sans-serif; font-weight:800; font-size:16px; color:var(--wl-ink); margin-bottom:16px; }

        .qi-rule { display:flex; gap:14px; align-items:flex-start; }
        .qi-rule + .qi-rule { margin-top:16px; }
        .qi-rnum { width:36px; height:36px; border-radius:50%; background:#DCF2EE; color:#0F7A68; display:grid; place-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; flex-shrink:0; }
        .qi-rt { font-family:'Geist',sans-serif; font-weight:800; font-size:14.5px; color:var(--wl-ink); }
        .qi-rd { font-size:13.5px; color:var(--wl-muted); line-height:1.55; margin-top:2px; }

        .qi-inforow { display:flex; align-items:center; gap:12px; }
        .qi-inforow + .qi-inforow { margin-top:14px; padding-top:14px; border-top:1px solid var(--wl-line); }
        .qi-inforow .nm { display:inline-flex; align-items:center; gap:10px; font-size:13.5px; font-weight:700; color:var(--wl-muted); flex:1; }
        .qi-inforow .vl { font-family:'Geist',sans-serif; font-weight:800; font-size:13.5px; color:var(--wl-ink); text-align:right; }
    </style>

    <div class="qi-wrap">
        {{-- Back + breadcrumb --}}
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <a href="{{ $backToQuizzes ? route('kuiz-saya.index') : route('bab.show', $chapter) }}" class="wl-back" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ $backToQuizzes ? __('Kuiz Saya') : __('Kembali') }}</a>
            <span class="qi-crumb">
                <span style="display:inline-flex;align-items:center;gap:6px;color:var(--wl-ink)"><x-subject-icon :subject="$subject" :size="16" />{{ $subject->name }}</span>
                <span class="sep"><x-icon name="chevron-right" class="h-4 w-4" /></span>{{ $chapter->grade->displayName() }}
                <span class="sep"><x-icon name="chevron-right" class="h-4 w-4" /></span>{{ __('Kuiz') }}
                <span class="sep"><x-icon name="chevron-right" class="h-4 w-4" /></span><span class="cur">{{ $quiz->localizedTitle() }}</span>
            </span>
        </div>

        @if ($isPreview)
            <div style="background:#FEF0CE;border-radius:14px;padding:14px 18px;font-weight:700;font-size:14px;color:#8A6A12">{{ __('Anda melihat kuiz ini sebagai cikgu. Guru tidak boleh mencuba kuiz, hanya menyemak.') }}</div>
        @endif

        {{-- Hero: tag + title + description, with an illustration. --}}
        <div class="qi-hero">
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:10px">
                <span style="align-self:flex-start;background:{{ $tagBg }};color:{{ $tagColor }};border-radius:999px;padding:5px 14px;font-family:'Geist',sans-serif;font-size:12.5px;font-weight:800;display:inline-flex;align-items:center;gap:6px"><x-subject-icon :subject="$subject" :size="15" />{{ $subject->name }} · {{ $chapter->grade->displayName() }}</span>
                <h2 style="margin:0;font-family:'Geist',sans-serif;font-size:28px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ $quiz->localizedTitle() }}</h2>
                @if ($quiz->localizedDescription())
                    <p style="margin:0;font-size:15px;color:var(--wl-muted);line-height:1.55;max-width:620px">{{ $quiz->localizedDescription() }}</p>
                @endif
            </div>
        </div>

        <div class="qi-grid">
            <div class="qi-main">
                {{-- Stat cards --}}
                <div class="qi-stats">
                    <div class="qi-stat">
                        <span class="ic" style="background:#DCF2EE;color:#0F7A68"><x-icon name="file-text" class="h-5 w-5" /></span>
                        <div><div class="l">{{ __('Jumlah soalan') }}</div><div class="v">{{ $questionCount }}</div></div>
                    </div>
                    <div class="qi-stat">
                        <span class="ic" style="background:#FBEAD3;color:#D9862B"><x-icon name="trophy" class="h-5 w-5" /></span>
                        <div><div class="l">{{ __('Markah penuh') }}</div><div class="v">{{ $maxScore }}</div></div>
                    </div>
                    <div class="qi-stat">
                        <span class="ic" style="background:#EDE7F9;color:#7C5CBF"><x-icon name="clock" class="h-5 w-5" /></span>
                        <div><div class="l">{{ __('Masa') }}</div><div class="v">{{ $quiz->duration_minutes ? $quiz->duration_minutes.' '.__('minit') : __('Bebas') }}</div></div>
                    </div>
                </div>

                {{-- Instructions & rules --}}
                <div class="qi-card">
                    <div class="qi-sech"><x-icon name="file-text" class="h-5 w-5" style="color:#0F7A68" />{{ __('Arahan dan peraturan kuiz') }}</div>
                    <div style="background:{{ $panelBg }};border-radius:14px;padding:20px 22px">
                        <div class="qi-rule">
                            <span class="qi-rnum">1</span>
                            <div><div class="qi-rt">{{ __('Soalan pilihan') }}</div><div class="qi-rd">{{ __('Pilih satu jawapan sahaja untuk setiap soalan.') }}</div></div>
                        </div>
                        <div class="qi-rule">
                            <span class="qi-rnum">2</span>
                            <div><div class="qi-rt">{{ __('Semak jawapan') }}</div><div class="qi-rd">{{ __('Pilih semua jawapan yang betul. Semua mesti betul untuk mendapatkan markah.') }}</div></div>
                        </div>
                        <div class="qi-rule">
                            <span class="qi-rnum">3</span>
                            <div><div class="qi-rt">{{ __('Percubaan pertama') }}</div><div class="qi-rd">{{ __('Hanya percubaan pertama dikira untuk ranking. Percubaan seterusnya adalah latihan sahaja dan tidak menjejaskan mata anda.') }}</div></div>
                        </div>
                        @if ($quiz->duration_minutes)
                            <div class="qi-rule">
                                <span class="qi-rnum" style="background:#FBEAD3;color:#D9862B"><x-icon name="clock" class="h-4 w-4" /></span>
                                <div><div class="qi-rt">{{ __('Had masa') }}</div><div class="qi-rd">{{ __('Anda ada :minutes minit. Jawapan dihantar secara automatik apabila masa tamat.', ['minutes' => $quiz->duration_minutes]) }}</div></div>
                            </div>
                        @endif
                    </div>

                    @if ($rankedAttempt)
                        <div style="display:flex;align-items:center;gap:8px;background:{{ $isDark ? 'rgba(45,212,191,.12)' : '#DCF2EE' }};border:1px solid {{ $isDark ? 'rgba(45,212,191,.3)' : 'rgba(23,144,123,.25)' }};border-radius:14px;padding:14px 18px;margin-top:16px;font-family:'Geist',sans-serif;font-size:13.5px;font-weight:700;color:{{ $isDark ? '#5EEAD4' : '#0F7A68' }}"><x-icon name="trophy" style="width:18px;height:18px;flex-shrink:0" />{{ __('Percubaan pertama anda: :score/:max mata. Percubaan baharu adalah latihan.', ['score' => $rankedAttempt->score, 'max' => $rankedAttempt->max_score]) }}</div>
                    @endif
                </div>

                @unless ($isPreview)
                    <form method="POST" action="{{ route('kuiz.mula', $quiz) }}">
                        @csrf
                        <button type="submit" class="wl-btn-primary" style="width:100%;min-height:56px;display:inline-flex;align-items:center;justify-content:center;gap:10px;border:none;cursor:pointer;border-radius:16px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:16px"><x-icon name="play" class="h-5 w-5" />{{ $rankedAttempt ? __('Cuba Lagi (Latihan)') : __('Mula Kuiz') }}</button>
                    </form>
                @endunless
            </div>

            <aside class="qi-side">
                {{-- Your progress: only when there is an attempt. --}}
                @if ($last)
                    @php($dpct = $last->max_score > 0 ? (int) round($last->score / $last->max_score * 100) : 0)
                    @php($circ = 2 * M_PI * 48)
                    @php($dash = round($dpct / 100 * $circ, 1))
                    <div class="qi-card">
                        <div class="qi-sech"><x-icon name="chart" class="h-5 w-5" style="color:#0F7A68" />{{ __('Kemajuan anda') }}</div>
                        <div style="background:{{ $panelBg }};border-radius:14px;padding:22px;display:grid;place-items:center">
                            <svg width="124" height="124" viewBox="0 0 124 124" aria-hidden="true">
                                <circle cx="62" cy="62" r="48" fill="none" stroke="{{ $isDark ? 'rgba(255,255,255,.1)' : '#CFE7DE' }}" stroke-width="9"/>
                                <circle cx="62" cy="62" r="48" fill="none" stroke="#17907B" stroke-width="9" stroke-linecap="round" stroke-dasharray="{{ $dash }} {{ round($circ, 1) }}" transform="rotate(-90 62 62)"/>
                                <text x="62" y="59" text-anchor="middle" font-family="Geist, sans-serif" font-size="19" font-weight="800" fill="var(--wl-ink)">{{ $last->score }}/{{ $last->max_score }}</text>
                                <text x="62" y="76" text-anchor="middle" font-family="Nunito, sans-serif" font-size="9.5" font-weight="700" fill="var(--wl-muted)">{{ __('Markah terakhir') }}</text>
                            </svg>
                        </div>
                        <div style="margin-top:16px">
                            <div class="qi-inforow">
                                <span class="nm"><x-icon name="calendar" class="h-[18px] w-[18px]" style="color:var(--wl-muted)" />{{ __('Tarikh percubaan') }}</span>
                                <span class="vl">{{ $last->completed_at->translatedFormat('d M Y, g:ia') }}</span>
                            </div>
                            <div class="qi-inforow">
                                <span class="nm"><x-icon name="chart" class="h-[18px] w-[18px]" style="color:var(--wl-muted)" />{{ __('Status') }}</span>
                                <span style="display:inline-flex;align-items:center;gap:6px;background:#DCF2EE;color:#0F7A68;border-radius:999px;padding:4px 12px;font-family:'Geist',sans-serif;font-weight:800;font-size:12.5px"><x-icon name="check-circle" class="h-4 w-4" />{{ __('Selesai') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Additional information --}}
                <div class="qi-card qi-grow">
                    <div class="qi-sech"><x-icon name="info-circle" class="h-5 w-5" style="color:#0F7A68" />{{ __('Maklumat tambahan') }}</div>
                    <div class="qi-inforow">
                        <span class="nm"><x-icon name="file-text" class="h-[18px] w-[18px]" style="color:var(--wl-muted)" />{{ __('Topik') }}</span>
                        <span class="vl">{{ $chapter->title }}</span>
                    </div>
                    <div class="qi-inforow">
                        <span class="nm"><x-icon name="book" class="h-[18px] w-[18px]" style="color:var(--wl-muted)" />{{ __('Mata pelajaran') }}</span>
                        <span class="vl">{{ $subject->name }}</span>
                    </div>
                    <div class="qi-inforow">
                        <span class="nm"><x-icon name="users" class="h-[18px] w-[18px]" style="color:var(--wl-muted)" />{{ __('Tahun') }}</span>
                        <span class="vl">{{ $chapter->grade->displayName() }}</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-dynamic-component>
