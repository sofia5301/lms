<x-student-layout :title="__('Subjek')">
    <style>
        .sx-wrap { display:flex; flex-direction:column; gap:22px; max-width:1240px; width:100%; margin:0 auto; }
        .sx-head { display:flex; align-items:flex-start; gap:16px; }
        .sx-htile { width:48px; height:48px; border-radius:14px; background:{{ ($theme ?? 'light') === 'dark' ? 'rgba(23,144,123,.2)' : '#DCF2EE' }}; color:#0F7A68; display:grid; place-items:center; flex-shrink:0; }

        /* One white section card per category. */
        .sx-cat { background:var(--wl-surface); border:1px solid var(--wl-line); border-radius:22px; box-shadow:0 3px 12px rgba(46,44,80,.04); padding:20px 22px; }
        .sx-cathead { display:flex; align-items:center; gap:12px; margin-bottom:16px; }
        .sx-cattitle { font-family:'Geist',sans-serif; font-size:18px; font-weight:800; color:var(--wl-ink); }
        .sx-catcount { margin-left:auto; font-size:13px; font-weight:700; color:var(--wl-muted); }

        .sx-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
        @media (max-width:900px) { .sx-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:600px) { .sx-grid { grid-template-columns:1fr; } }

        .sx-card { display:flex; align-items:center; gap:14px; border-radius:16px; padding:14px 16px; text-decoration:none; border:1px solid transparent; transition:transform .12s ease-out, box-shadow .12s; }
        .sx-card:hover { transform:translateY(-2px); box-shadow:0 10px 22px rgba(46,44,80,.1); }
        .sx-tile { width:48px; height:48px; border-radius:13px; display:grid; place-items:center; flex-shrink:0; }
        .sx-info { flex:1; min-width:0; display:flex; flex-direction:column; gap:3px; }
        .sx-name { font-family:'Geist',sans-serif; font-weight:800; font-size:15.5px; color:var(--wl-ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .sx-meta { font-size:12.5px; font-weight:600; color:var(--wl-muted); }
        .sx-chev { color:var(--wl-muted-2); flex-shrink:0; }
    </style>

    @if ($grade && $subjectsByCategory->isNotEmpty())
        <div class="sx-wrap">
            <div class="sx-head">
                <span class="sx-htile"><x-icon name="book" style="width:24px;height:24px" /></span>
                <div style="display:flex;flex-direction:column;gap:2px;min-width:0">
                    <h2 style="margin:0;font-family:'Geist',sans-serif;font-size:22px;font-weight:800;letter-spacing:-.01em;color:var(--wl-ink)">{{ __('Subjek') }}</h2>
                    <span style="font-size:14px;font-weight:600;color:var(--wl-muted)">{{ $grade->displayName() }} · {{ __('Pilih subjek untuk melihat bab dan video') }}</span>
                </div>
            </div>

            @foreach (\App\Models\Subject::CATEGORIES as $category)
                @php($group = $subjectsByCategory[$category] ?? collect())
                @if ($group->isNotEmpty())
                    <div class="sx-cat">
                        <div class="sx-cathead">
                            <span class="sx-cattitle">{{ \App\Models\Subject::categoryLabel($category) }}</span>
                            <span class="sx-catcount">{{ $group->count() }} {{ __('subjek') }}</span>
                        </div>
                        <div class="sx-grid">
                            @foreach ($group as $subject)
                                @php($col = $subject->color ?: '#17907B')
                                <a href="{{ route('belajar.subjek', ['subject' => $subject->slug, 'grade' => $grade->level]) }}" class="sx-card"
                                   style="background:color-mix(in oklab, {{ $col }} 10%, var(--wl-surface));border-color:color-mix(in oklab, {{ $col }} 20%, var(--wl-surface))">
                                    <span class="sx-tile" style="background:color-mix(in oklab, {{ $col }} 20%, #fff);color:{{ $col }}"><x-subject-icon :subject="$subject" :size="24" /></span>
                                    <div class="sx-info">
                                        <span class="sx-name">{{ $subject->displayName() }}</span>
                                        <span class="sx-meta">{{ $subject->chapters_count }} {{ __('bab') }} · {{ $subject->lessons_count }} {{ __('video') }}</span>
                                    </div>
                                    <x-icon name="chevron-right" class="sx-chev h-5 w-5" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div style="background:var(--wl-surface);border:1px dashed var(--wl-line-3);border-radius:22px;padding:56px;display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center;max-width:520px;margin:0 auto">
            <span class="sx-htile" style="width:56px;height:56px"><x-icon name="book" style="width:26px;height:26px" /></span>
            <h3 style="margin:0;font-family:'Geist',sans-serif;font-size:19px;font-weight:800;color:var(--wl-ink)">{{ __('Tiada subjek') }}</h3>
            <p style="margin:0;font-size:14.5px;color:var(--wl-muted);max-width:360px">{{ __('Sila kemas kini profil anda dan pilih Tahun.') }}</p>
            <a href="{{ route('profile.edit') }}" class="wl-btn-primary" style="margin-top:6px;min-height:46px;display:inline-flex;align-items:center;border-radius:12px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:14.5px;padding:0 22px;text-decoration:none">{{ __('Kemas Kini Profil') }}</a>
        </div>
    @endif
</x-student-layout>
