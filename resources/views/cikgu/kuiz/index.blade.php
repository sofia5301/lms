<x-cikgu-layout
    :title="__('Kuiz Saya')"
    :heading="__('Kuiz Saya')"
    heading-icon="quiz"
    :sub="__('Kuiz interaktif yang menanda sendiri, dan kuiz bercetak')">

    <style>
        /* Quiz list as a table: Kuis / Subjek / Tahun·Bab / Soalan / Percubaan / Tempoh / Tindakan.
           Keeps a min-width and scrolls sideways inside .qt-wrap on narrow screens. */
        .qt-wrap { overflow-x:auto; }
        .qt-table { min-width:1040px; }
        .qt-head, .qt-row { display:grid; grid-template-columns:minmax(0,2.6fr) minmax(0,1.05fr) minmax(0,1fr) minmax(0,0.72fr) minmax(0,0.95fr) minmax(0,0.62fr) 288px; gap:14px; align-items:center; }
        .qt-head { padding:13px 22px; }
        .qt-colh { font-family:'Geist',sans-serif; font-size:11px; font-weight:800; letter-spacing:.05em; text-transform:uppercase; color:var(--tp-muted); }
        .qt-row { padding:14px 22px; border-top:1px solid var(--tp-line); }
        .qt-kuis { display:flex; align-items:center; gap:14px; min-width:0; }
        .qt-ico { width:52px; height:52px; border-radius:14px; display:grid; place-items:center; flex-shrink:0; }
        .qt-titlewrap { display:flex; align-items:center; gap:9px; flex-wrap:wrap; min-width:0; }
        .qt-title { font-family:'Geist',sans-serif; font-weight:800; font-size:14.5px; color:var(--tp-ink); line-height:1.3; }
        .qt-cell { font-size:13px; font-weight:700; color:var(--tp-muted-2); min-width:0; }
        .qt-tind { display:flex; align-items:center; gap:10px; }
        .qt-tind .tp-icon-action { border:1.5px solid var(--tp-line-2); }
        .qt-tind .tp-icon-danger { border-color:rgba(194,73,54,.45); }
        /* Centre every column except Kuis (header + data), so values sit centred in their column. */
        .qt-head > :not(:first-child), .qt-row > :not(.qt-kuis) { justify-self:center; text-align:center; }

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
    </style>

    {{-- Search quizzes by title. Carries the current filters as hidden fields so searching keeps them. --}}
    <form method="GET" action="{{ route('cikgu.kuiz.index') }}" style="margin-bottom:18px">
        @foreach (request()->except(['q', 'page']) as $k => $v)
            @unless (is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endunless
        @endforeach
        <div style="position:relative;max-width:560px">
            <span style="position:absolute;left:16px;top:0;bottom:0;display:flex;align-items:center;color:var(--tp-muted);pointer-events:none"><x-icon name="search" class="h-5 w-5" /></span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari kuiz...') }}" aria-label="{{ __('Cari kuiz...') }}"
                   style="width:100%;min-height:48px;padding:0 18px 0 46px;border:1.5px solid var(--tp-line-2);border-radius:14px;background:var(--tp-surface);color:var(--tp-ink);font-family:'Nunito',sans-serif;font-size:15px">
        </div>
    </form>

    {{-- Stat (total quizzes, all-time) beside the Tahun/Subjek/Bab filters, split by a divider. --}}
    <div class="tp-card bf-card" style="margin-bottom:18px">
        <div class="bf-stat">
            <span style="width:52px;height:52px;border-radius:14px;background:#FEF0CE;color:#8A6A12;display:grid;place-items:center;flex-shrink:0"><x-icon name="quiz" class="h-6 w-6" /></span>
            <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                <span style="font-size:12.5px;font-weight:700;color:var(--tp-muted);white-space:nowrap">{{ __('Kuiz Saya') }}</span>
                <span style="font-family:'Geist',sans-serif;font-size:30px;font-weight:800;color:var(--tp-ink);line-height:1.05">{{ number_format($totalQuizzes) }}</span>
            </div>
        </div>

        <div class="bf-div"></div>

        <div class="bf-filters">
            <x-year-subject-filter :subjects="$subjects" :grades="$grades" :filter="$filter" with-chapter :action="route('cikgu.kuiz.index')">
                <x-slot:search><input type="hidden" name="q" value="{{ request('q') }}"></x-slot:search>
            </x-year-subject-filter>
        </div>
    </div>

    <div x-data="{ quiz: null, open(data) { this.quiz = data }, close() { this.quiz = null } }">

    @if ($quizzes->isEmpty())
        <div class="tp-empty">
            <span style="font-size:30px">📝</span>
            <h3 class="tp-g" style="font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Belum ada kuiz') }}</h3>
            <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:420px">{{ __('Bina kuiz interaktif yang menyemak jawapan sendiri, atau muat naik kuiz bercetak.') }}</p>
            <a href="{{ route('cikgu.kuiz.mod') }}" class="tp-btn" style="margin-top:6px">{{ __('Cipta Kuiz Pertama') }}</a>
        </div>
    @else
        <div class="tp-card">
            <div class="qt-wrap">
                <div class="qt-table">
                    <div class="qt-head">
                        <span class="qt-colh">{{ __('Kuiz') }}</span>
                        <span class="qt-colh">{{ __('Subjek') }}</span>
                        <span class="qt-colh">{{ __('Tahun · Bab') }}</span>
                        <span class="qt-colh">{{ __('Soalan') }}</span>
                        <span class="qt-colh">{{ __('Percubaan') }}</span>
                        <span class="qt-colh">{{ __('Tempoh') }}</span>
                        <span class="qt-colh">{{ __('Tindakan') }}</span>
                    </div>

                    @foreach ($quizzes as $quiz)
                        @php($subject = $quiz->chapter->subject)
                        <div class="qt-row">
                            {{-- KUIS: subject icon square + title + type/draft tags --}}
                            <div class="qt-kuis">
                                <span class="qt-ico" style="background:rgb({{ $subject->rgb }} / .14);color:rgb({{ $subject->rgb }})"><x-icon :name="$subject->iconName()" class="h-6 w-6" /></span>
                                <div class="qt-titlewrap">
                                    <span class="qt-title">{{ $quiz->localizedTitle() }}</span>
                                    @if ($quiz->isInteractive())
                                        <span class="tp-tag" style="background:#DCF2EE;color:#0F7A68">{{ __('Interaktif') }}</span>
                                    @else
                                        <span class="tp-tag" style="background:#E4EEF9;color:#2E6CA8">{{ __('Bercetak') }}</span>
                                    @endif
                                    @unless ($quiz->is_published)
                                        <span class="tp-tag" style="background:#FEF0CE;color:#8A6A12">{{ __('Draf') }}</span>
                                    @endunless
                                    @if ($quiz->isInteractive() && $quiz->questions_count === 0)
                                        <span class="tp-tag" style="background:#FDECEC;color:#C24936">{{ __('Tiada soalan') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- SUBJEK --}}
                            <div><span class="tp-tag" style="background:rgb({{ $subject->rgb }} / .14);color:rgb({{ $subject->rgb }})">{{ $subject->name }}</span></div>

                            {{-- TAHUN · BAB --}}
                            <div class="qt-cell">{{ $quiz->chapter->grade->displayName() }} · {{ __('Bab :n', ['n' => $quiz->chapter->number]) }}</div>

                            {{-- SOALAN --}}
                            <div class="qt-cell">{{ $quiz->isInteractive() ? __(':count soalan', ['count' => $quiz->questions_count]) : '—' }}</div>

                            {{-- PERCUBAAN --}}
                            <div class="qt-cell">{{ $quiz->isInteractive() ? __(':count percubaan', ['count' => $quiz->completed_attempts_count]) : '—' }}</div>

                            {{-- TEMPOH --}}
                            <div class="qt-cell">{{ $quiz->isInteractive() && $quiz->duration_minutes ? __(':count min', ['count' => $quiz->duration_minutes]) : '—' }}</div>

                            {{-- TINDAKAN: Lihat Soalan / Fail + statistik + edit + delete --}}
                            <div class="qt-tind">
                                @if ($quiz->isInteractive())
                                    <button type="button" class="tp-lihatbtn" style="flex-shrink:0;display:inline-flex;align-items:center;min-height:38px;border-radius:11px;border:1.5px solid #0F7A68;background:var(--tp-surface);color:#0F7A68;font-family:'Geist',sans-serif;font-weight:800;font-size:12.5px;padding:0 14px;cursor:pointer" @click="open(@js([
                                        'title' => $quiz->localizedTitle(),
                                        'subtitle' => collect([$quiz->chapter->subject->displayName(), $quiz->chapter->grade->displayName(), __(':count soalan', ['count' => $quiz->questions_count])])->filter()->implode(' · '),
                                        'questions' => $quiz->questions->map(fn ($question) => [
                                            'text' => $question->localizedText(),
                                            'points' => $question->points,
                                            'options' => $question->options->map(fn ($option) => [
                                                'letter' => $option->letter(),
                                                'text' => $option->localizedText($question->source_locale),
                                                'correct' => (bool) $option->is_correct,
                                            ])->all(),
                                        ])->all(),
                                    ]))">{{ __('Lihat Soalan') }}</button>
                                    <a href="{{ route('cikgu.kuiz.statistik', $quiz) }}" class="tp-icon-action" title="{{ __('Statistik') }}">
                                        <x-icon name="chart" class="h-[17px] w-[17px]" />
                                        <span class="sr-only">{{ __('Statistik') }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('muat-turun.kuiz', $quiz) }}" class="tp-btn-ghost" style="flex-shrink:0;display:inline-flex;align-items:center;gap:6px;min-height:38px">
                                        <x-icon name="download" class="h-4 w-4" />{{ __('Fail') }}
                                    </a>
                                @endif

                                <a href="{{ route('cikgu.kuiz.edit', $quiz) }}" class="tp-icon-action" title="{{ __('Sunting') }}">
                                    <x-icon name="pencil" class="h-[17px] w-[17px]" />
                                    <span class="sr-only">{{ __('Sunting :title', ['title' => $quiz->title]) }}</span>
                                </a>

                                @php($delMsg = __('Padam kuiz ":title"? Semua soalan dan percubaan murid akan dipadam sekali. Tindakan ini tidak boleh dibatalkan.', ['title' => $quiz->title]))
                                <x-confirm-modal id="del-kuiz-{{ $quiz->id }}" :action="route('cikgu.kuiz.destroy', $quiz)"
                                    :title="__('Padam kuiz?')" :message="$delMsg">
                                    <button type="button" class="tp-icon-action tp-icon-danger" title="{{ __('Padam') }}">
                                        <x-icon name="trash" class="h-[17px] w-[17px]" />
                                        <span class="sr-only">{{ __('Padam :title', ['title' => $quiz->title]) }}</span>
                                    </button>
                                </x-confirm-modal>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="margin-top:16px">{{ $quizzes->links('pagination.tp') }}</div>
    @endif

        {{-- Read-only question preview (WeLearn Admin design: gradient header + green body) --}}
        <template x-if="quiz">
            <x-content-preview obj="quiz">
                <div style="overflow-y:auto;background:linear-gradient(180deg,#E9F7F2,#FAF9F5)">
                    <template x-if="! quiz.questions.length">
                        <p style="text-align:center;color:#6C6F87;padding:44px 0;font-weight:700">{{ __('Kuiz ini belum ada soalan.') }}</p>
                    </template>

                    <template x-if="quiz.questions.length">
                        <div class="qb-preview" style="padding:24px;display:flex;flex-direction:column;gap:16px">
                            <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #B7E3D8;border-radius:12px;padding:10px 16px;align-self:flex-start">
                                <span style="width:16px;height:16px;border-radius:5px;background:#E9F7F2;border:1.5px solid #0F7A68;display:inline-block;flex-shrink:0"></span>
                                <span style="font-size:12.5px;font-weight:800;color:#0F7A68;font-family:'Geist',sans-serif">{{ __('Jawapan betul ditanda hijau') }}</span>
                            </div>

                            <template x-for="(question, index) in quiz.questions" :key="index">
                                <div style="background:#fff;border:1px solid rgba(46,44,80,.08);border-radius:14px;padding:18px 20px;display:flex;flex-direction:column;gap:12px;box-shadow:0 2px 8px rgba(46,44,80,.04)">
                                    <div style="display:flex;gap:10px;align-items:flex-start">
                                        <span style="flex-shrink:0;width:26px;height:26px;border-radius:8px;background:#E6F5F1;color:#0F7A68;display:grid;place-items:center;font-family:'Geist',sans-serif;font-weight:800;font-size:13px" x-text="index + 1"></span>
                                        <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:15px;color:#28293F;line-height:1.4" x-text="question.text"></span>
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:8px;padding-left:36px">
                                        <template x-for="(option, oIndex) in question.options" :key="oIndex">
                                            <div :style="(option.correct ? 'border:1px solid #17907B;background:#E6F5F1' : 'border:1px solid rgba(46,44,80,.08);background:#F6F5F0') + ';display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:10px'">
                                                <span :style="(option.correct ? 'background:#17907B;color:#fff' : 'background:#EDECE4;color:#8B8AA3') + ';width:24px;height:24px;flex-shrink:0;border-radius:50%;display:grid;place-items:center;font-family:\'Geist\',sans-serif;font-weight:800;font-size:11.5px'" x-text="option.letter"></span>
                                                <span style="font-size:13.5px;font-weight:700;color:#4A4B63" x-text="option.text"></span>
                                                <template x-if="option.correct">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0F7A68" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto;flex-shrink:0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </x-content-preview>
        </template>
    </div>
</x-cikgu-layout>
