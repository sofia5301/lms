<x-student-layout :title="__('Simpanan Offline')">
    @php($isDark = ($theme ?? 'light') === 'dark')
    <style>
        .off-probe { color:{{ $isDark ? '#fff' : '#000' }}; background:{{ $isDark ? 'rgba(23,144,123,.2)' : '#DCF2EE' }}; }
        .off-probe2 { background:{{ $isDark ? 'rgba(255,255,255,.02)' : '#FAFBFC' }}; }
        @media (max-width:760px) {
            .off-probe { font-size:14px; }
        }
    </style>
    @php
        $tree = [];
        foreach ($lessons as $l) {
            $chapter = $l->chapter;
            $subject = $chapter ? $chapter->subject : null;
            if (! $subject) { continue; }
            $sid = $subject->id;
            $cid = $chapter->id;
            $tree[$sid]['subject'] = $subject;
            $tree[$sid]['chapters'][$cid]['chapter'] = $chapter;
            $tree[$sid]['chapters'][$cid]['videos'][] = $l;
        }
        foreach ($materialsByChapter as $mats) {
            $first = $mats->first();
            $chapter = $first ? $first->chapter : null;
            $subject = $chapter ? $chapter->subject : null;
            if (! $subject) { continue; }
            $sid = $subject->id;
            $cid = $chapter->id;
            $tree[$sid]['subject'] = $subject;
            $tree[$sid]['chapters'][$cid]['chapter'] = $chapter;
            foreach ($mats as $mm) {
                $tree[$sid]['chapters'][$cid]['materials'][] = $mm;
            }
        }
    @endphp

    <div style="padding:20px;max-width:1000px;margin:0 auto">
        <h1 style="font-family:'Geist',sans-serif;font-weight:800;color:var(--wl-ink)">VERSION 13 — testing style + isDark</h1>

        @foreach ($tree as $grp)
            @php($sub = $grp['subject'])
            <div style="border:1px solid var(--wl-line);border-radius:12px;margin-top:12px;overflow:hidden">
                <div style="padding:14px;font-weight:800;color:var(--wl-ink);display:flex;align-items:center;gap:8px">
                    <x-subject-icon :subject="$sub" :size="20" />{{ $sub->displayName() }}
                </div>
                @foreach ($grp['chapters'] as $c)
                    @php($chapter = $c['chapter'])
                    @php($videos = isset($c['videos']) ? $c['videos'] : [])
                    @php($materials = isset($c['materials']) ? $c['materials'] : [])
                    <div style="padding:10px 14px;border-top:1px solid var(--wl-line)">
                        <div style="font-weight:700;color:var(--wl-ink)">Bab {{ $chapter->number }}: {{ $chapter->title }}</div>
                        @foreach ($videos as $lesson)
                            <div style="font-size:13px;color:var(--wl-muted);padding:3px 0">
                                <a href="{{ route('video.show', $lesson) }}">VID: {{ $lesson->title }}</a>
                                · thumb:{{ $lesson->thumbnailUrl() ? 'y' : 'n' }} · dur:{{ $lesson->durationLabel() ?? '-' }} · upload:{{ $lesson->isUpload() ? 'y' : 'n' }}
                                @if ($lesson->isUpload()) · <a href="{{ route('muat-turun.video', $lesson) }}">dl</a> @endif
                            </div>
                        @endforeach
                        @foreach ($materials as $material)
                            <div style="font-size:13px;color:var(--wl-muted);padding:3px 0">
                                <x-icon :name="$material->iconName()" style="width:16px;height:16px" /> MAT: {{ $material->title }}
                                · <a href="{{ route('muat-turun.bahan', $material) }}">dl</a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</x-student-layout>
