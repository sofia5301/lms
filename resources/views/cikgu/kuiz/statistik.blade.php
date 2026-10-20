<x-cikgu-layout :title="__('Statistik:').' '.$quiz->localizedTitle()"
    :heading="$quiz->localizedTitle()"
    :sub="__('Prestasi murid pada kuiz ini')">

    @php
        // Accuracy donut geometry.
        $circ = 2 * M_PI * 26;
        $dash = round($averagePercent / 100 * $circ, 1);
    @endphp

    <style>
        /* Summary stat cards: tinted, icon tile + label/value + a small visual. */
        .st-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
        @media (max-width:900px) { .st-cards { grid-template-columns:1fr; } }
        .st-card { border-radius:18px; border:1px solid var(--tp-line); padding:18px 20px; display:flex; align-items:center; gap:14px; }
        .st-card.c1 { background:rgba(46,108,168,.08); }
        .st-card.c2 { background:rgba(217,134,43,.08); }
        .st-card.c3 { background:rgba(23,144,123,.08); }
        .st-ico { width:48px; height:48px; border-radius:13px; display:grid; place-items:center; flex-shrink:0; }
        .st-mid { flex:1; min-width:0; display:flex; flex-direction:column; gap:4px; }
        .st-lbl { font-size:12.5px; font-weight:700; color:var(--tp-muted); }
        .st-val { font-family:'Geist',sans-serif; font-size:30px; font-weight:800; color:var(--tp-ink); line-height:1.05; }
        .st-val small { font-size:17px; color:var(--tp-muted); font-weight:800; }
        .st-viz { flex-shrink:0; display:grid; place-items:center; }

        /* Section heading with a leading icon. */
        .st-sech { display:flex; align-items:center; gap:10px; font-family:'Geist',sans-serif; font-size:17px; font-weight:800; color:var(--tp-ink); margin:0; }
        .st-sech .shi { color:#0F7A68; display:inline-flex; }

        /* Per-question correctness cards. */
        .st-q { background:var(--tp-surface); border:1px solid var(--tp-line); border-radius:16px; padding:18px 20px; display:flex; flex-direction:column; gap:12px; box-shadow:0 2px 10px rgba(46,44,80,.03); }
        .st-qtop { display:flex; align-items:flex-start; gap:14px; }
        .st-qnum { width:36px; height:36px; flex-shrink:0; border-radius:11px; background:var(--tp-surface-2); color:var(--tp-muted-2); display:grid; place-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; }
        .st-qbody { flex:1; min-width:0; }
        .st-qsmall { margin:0; font-size:12.5px; font-weight:700; color:var(--tp-muted); }
        .st-qtext { margin:3px 0 0; font-family:'Geist',sans-serif; font-weight:800; font-size:15px; color:var(--tp-ink); line-height:1.35; }
        .st-qrate { flex-shrink:0; border-radius:999px; padding:5px 13px; font-family:'Geist',sans-serif; font-size:12px; font-weight:800; white-space:nowrap; }
        .st-qbar { height:10px; width:100%; overflow:hidden; border-radius:999px; background:var(--tp-line); }
        .st-qmeta { margin:0; font-size:13px; color:var(--tp-muted-2); }

        /* Attempts header: heading on the left, student search on the right. */
        .st-atthead { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; }
        .st-search { position:relative; max-width:300px; width:100%; }
        .st-search .si { position:absolute; left:14px; top:0; bottom:0; display:flex; align-items:center; color:var(--tp-muted); pointer-events:none; }
        .st-search input { width:100%; min-height:44px; padding:0 16px 0 42px; border:1.5px solid var(--tp-line-2); border-radius:12px; background:var(--tp-surface); color:var(--tp-ink); font-family:'Nunito',sans-serif; font-size:14px; }
    </style>

    <div style="display:flex;flex-direction:column;gap:20px">
        <a href="{{ route('cikgu.kuiz.index') }}" class="tp-back" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Kuiz Saya') }}</a>

        <span class="tp-crumb" style="align-self:flex-start;background:#E4EEF9;color:#2E6CA8;border-radius:999px;padding:5px 14px;font-family:'Geist',sans-serif;font-size:12.5px;font-weight:800;display:inline-flex;align-items:center;gap:6px"><x-icon :name="$subject->iconName()" class="h-[15px] w-[15px]" />{{ $subject->name }} – {{ $chapter->grade->displayName() }} – {{ __('Bab :n', ['n' => $chapter->number]) }}</span>

        {{-- Summary --}}
        <div class="st-cards">
            {{-- Percubaan selesai --}}
            <div class="st-card c1">
                <span class="st-ico" style="background:#E4EEF9"><x-icon name="users" class="h-6 w-6" style="color:#2E6CA8" /></span>
                <div class="st-mid">
                    <span class="st-lbl">{{ __('Percubaan selesai') }}</span>
                    <span class="st-val">{{ $completedCount }}</span>
                </div>
                <span class="st-viz">
                    <svg width="96" height="46" viewBox="0 0 96 46" fill="none" aria-hidden="true">
                        <path d="M3 40 C 18 38, 26 30, 40 28 S 66 16, 93 5" stroke="#2E6CA8" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" opacity=".85"/>
                        <path d="M3 40 C 18 38, 26 30, 40 28 S 66 16, 93 5 L 93 44 L 3 44 Z" fill="#2E6CA8" opacity=".08"/>
                    </svg>
                </span>
            </div>

            {{-- Purata markah --}}
            <div class="st-card c2">
                <span class="st-ico" style="background:#FBEAD3"><x-icon name="trophy" class="h-6 w-6" style="color:#D9862B" /></span>
                <div class="st-mid">
                    <span class="st-lbl">{{ __('Purata markah') }}</span>
                    <span class="st-val">{{ $averageScore }}<small>/{{ $averageMax }}</small></span>
                </div>
                <span class="st-viz">
                    <svg width="88" height="46" viewBox="0 0 88 46" aria-hidden="true">
                        <rect x="2" y="28" width="13" height="16" rx="3" fill="#E9C06A"/>
                        <rect x="21" y="18" width="13" height="26" rx="3" fill="#E9C06A"/>
                        <rect x="40" y="24" width="13" height="20" rx="3" fill="#E9C06A"/>
                        <rect x="59" y="10" width="13" height="34" rx="3" fill="#D9862B"/>
                    </svg>
                </span>
            </div>

            {{-- Purata ketepatan --}}
            <div class="st-card c3">
                <span class="st-ico" style="background:#DCF2EE"><x-icon name="target-arrow" class="h-6 w-6" style="color:#0F7A68" /></span>
                <div class="st-mid">
                    <span class="st-lbl">{{ __('Purata ketepatan') }}</span>
                    <span class="st-val">{{ $averagePercent }}%</span>
                </div>
                <span class="st-viz">
                    <svg width="64" height="64" viewBox="0 0 64 64" aria-hidden="true">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#DCE8E3" stroke-width="7"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#0F7A68" stroke-width="7" stroke-linecap="round"
                                stroke-dasharray="{{ $dash }} {{ round($circ, 1) }}" transform="rotate(-90 32 32)"/>
                        <text x="32" y="37" text-anchor="middle" font-family="Geist, sans-serif" font-size="14" font-weight="800" fill="var(--tp-ink)">{{ $averagePercent }}%</text>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Per-question correctness --}}
        <div style="display:flex;flex-direction:column;gap:12px;margin-top:16px">
            <h2 class="st-sech"><span class="shi"><x-icon name="chart" class="h-5 w-5" /></span>{{ __('Kadar betul setiap soalan') }}</h2>

            @if ($completedCount === 0)
                <div class="tp-empty">
                    <x-icon name="chart" class="h-[30px] w-[30px]" style="color:var(--tp-muted-2)" />
                    <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Belum ada data') }}</h3>
                    <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:420px">{{ __('Statistik akan muncul setelah murid mula menjawab kuiz ini.') }}</p>
                </div>
            @else
                @foreach ($quiz->questions as $index => $question)
                    @php
                        $stat = $perQuestion[$question->id] ?? null;
                        $answered = (int) ($stat->answered ?? 0);
                        $correct = (int) ($stat->correct ?? 0);
                        $rate = $answered > 0 ? (int) round($correct / $answered * 100) : 0;
                        $barBg = $rate >= 70 ? '#DCF2EE' : ($rate >= 40 ? '#FEF0CE' : '#FDE7E0');
                        $barFg = $rate >= 70 ? '#0F7A68' : ($rate >= 40 ? '#8A6A12' : '#C24936');
                    @endphp
                    <div class="st-q">
                        <div class="st-qtop">
                            <span class="st-qnum">{{ $index + 1 }}</span>
                            <div class="st-qbody">
                                <p class="st-qsmall">{{ __('Soalan :number', ['number' => $index + 1]) }}</p>
                                <p class="st-qtext">{{ $question->localizedText() }}</p>
                            </div>
                            <span class="st-qrate" style="background:{{ $barBg }};color:{{ $barFg }}">{{ __(':rate% betul', ['rate' => $rate]) }}</span>
                        </div>
                        <div class="st-qbar">
                            <div style="height:100%;border-radius:999px;background:{{ $barFg }};width:{{ $rate }}%"></div>
                        </div>
                        <p class="st-qmeta">
                            {{ __(':correct daripada :answered murid menjawab dengan betul.', ['correct' => $correct, 'answered' => $answered]) }}
                            @if ($rate < 40) <span style="font-weight:800;color:#C24936">{{ __('Mungkin perlu diterangkan semula.') }}</span> @endif
                        </p>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Attempts --}}
        <div style="display:flex;flex-direction:column;gap:12px;margin-top:16px">
            <div class="st-atthead">
                <h2 class="st-sech"><span class="shi"><x-icon name="users" class="h-5 w-5" /></span>{{ __('Percubaan murid') }}</h2>
                @if ($completedCount > 0)
                    <form method="GET" action="{{ route('cikgu.kuiz.statistik', $quiz) }}" class="st-search">
                        <span class="si"><x-icon name="search" class="h-5 w-5" /></span>
                        <input type="search" name="murid" value="{{ $search }}" placeholder="{{ __('Cari nama murid...') }}" aria-label="{{ __('Cari nama murid...') }}">
                    </form>
                @endif
            </div>

            @if ($attempts->isEmpty())
                <div class="tp-empty">
                    <x-icon name="users" class="h-[30px] w-[30px]" style="color:var(--tp-muted-2)" />
                    @if ($search !== '')
                        <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Tiada percubaan ditemui untuk carian ini.') }}</h3>
                    @else
                        <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Belum ada murid mencuba kuiz ini') }}</h3>
                        <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:420px">{{ __('Pastikan kuiz sudah diterbitkan dan mempunyai soalan.') }}</p>
                    @endif
                </div>
            @else
                <div class="tp-card" style="overflow:hidden">
                    <div style="overflow-x:auto">
                        <div class="tp-atttable" style="min-width:800px">
                            @php($cols = '44px minmax(0,2fr) 1fr 1fr 1fr 1fr 1fr 1.4fr')
                            <div style="display:grid;grid-template-columns:{{ $cols }};gap:12px;padding:14px 20px;border-bottom:1px solid var(--tp-line);justify-items:center;text-align:center">
                                @foreach (['#', __('Murid'), __('Tahun'), __('Markah'), __('Betul'), __('Masa'), __('Jenis'), __('Tarikh')] as $h)
                                    <span class="tp-g" style="font-size:12px;font-weight:800;color:var(--tp-muted);{{ $loop->index === 1 ? 'justify-self:start' : '' }}">{{ $h }}</span>
                                @endforeach
                            </div>
                            @foreach ($attempts as $attempt)
                                <div class="tp-row" style="display:grid;grid-template-columns:{{ $cols }};gap:12px;padding:13px 20px;justify-items:center;align-items:center;text-align:center">
                                    {{-- Continuous number across pages: page 1 is 1-10, page 2 is 11-20. --}}
                                    <span class="tp-meta" style="font-variant-numeric:tabular-nums">{{ $attempts->firstItem() + $loop->index }}</span>
                                    <div style="display:flex;align-items:center;gap:10px;min-width:0;justify-self:start">
                                        <span style="width:34px;height:34px;border-radius:9px;background:#DCF2EE;color:#0F7A68;display:grid;place-items:center;font-family:'Geist',sans-serif;font-weight:800;font-size:11px;flex-shrink:0">{{ $attempt->student->initials() }}</span>
                                        <span class="tp-g" style="font-weight:800;font-size:14px;color:var(--tp-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $attempt->student->name }}</span>
                                    </div>
                                    <span class="tp-meta">{{ $attempt->student->grade?->displayName() ?? '-' }}</span>
                                    <span><span class="tp-g" style="font-weight:800;color:var(--tp-ink)">{{ $attempt->score }}/{{ $attempt->max_score }}</span></span>
                                    <span class="tp-meta">{{ $attempt->correct_count }}/{{ $attempt->question_count }}</span>
                                    <span class="tp-meta" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="clock" class="h-4 w-4" style="color:var(--tp-muted)" />{{ $attempt->humanDuration() }}</span>
                                    <span>
                                        @if ($attempt->counts_for_ranking)
                                            <span class="tp-tag" style="background:#DCF2EE;color:#0F7A68">{{ __('Dikira') }}</span>
                                        @else
                                            <span class="tp-tag-neutral">{{ __('Latihan') }}</span>
                                        @endif
                                    </span>
                                    <span class="tp-meta" style="display:inline-flex;align-items:center;gap:6px"><x-icon name="calendar" class="h-4 w-4" style="color:var(--tp-muted)" />{{ $attempt->completed_at->format('d/m/Y, g:ia') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if ($attempts->hasPages())
                    <div style="margin-top:4px">{{ $attempts->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-cikgu-layout>
