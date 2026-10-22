<x-student-layout :title="__('Simpanan Offline')">
    @php
        $tree = [];
        $treeError = null;
        try {
            foreach ($lessons as $l) {
                $chapter = $l->chapter;
                $subject = $chapter ? $chapter->subject : null;
                if (! $subject) { continue; }
                $sid = $subject->id;
                $cid = $chapter->id;
                $tree[$sid]['subject'] = $subject;
                $tree[$sid]['chapters'][$cid]['chapter'] = $chapter;
                $tree[$sid]['chapters'][$cid]['number'] = $chapter->number;
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
                $tree[$sid]['chapters'][$cid]['number'] = $chapter->number;
                foreach ($mats as $mm) {
                    $tree[$sid]['chapters'][$cid]['materials'][] = $mm;
                }
            }
            $order = $subjects->pluck('sort_order', 'id')->all();
            uksort($tree, function ($a, $b) use ($order) {
                return ($order[$a] ?? 999) <=> ($order[$b] ?? 999);
            });
            foreach (array_keys($tree) as $sid) {
                uasort($tree[$sid]['chapters'], function ($a, $b) {
                    return ($a['number'] ?? 0) <=> ($b['number'] ?? 0);
                });
                $v = 0;
                $m = 0;
                foreach ($tree[$sid]['chapters'] as $cc) {
                    $v += isset($cc['videos']) ? count($cc['videos']) : 0;
                    $m += isset($cc['materials']) ? count($cc['materials']) : 0;
                }
                $tree[$sid]['vcount'] = $v;
                $tree[$sid]['mcount'] = $m;
            }
        } catch (\Throwable $e) {
            $treeError = $e->getMessage().'  @  '.basename($e->getFile()).':'.$e->getLine();
            $tree = [];
        }
    @endphp

    <div style="padding:20px;max-width:900px;margin:0 auto">
        <h1 style="font-family:'Geist',sans-serif;font-weight:800;color:var(--wl-ink)">Simpanan Offline (debug 2)</h1>
        @if ($treeError)
            <div style="background:#FDE7E0;border:1px solid #E0564E;border-radius:10px;padding:12px;margin-top:10px;font-weight:700;color:#8A2A1C">DEBUG: {{ $treeError }}</div>
        @endif
        <p style="color:var(--wl-muted);margin-top:10px">subjects in tree: {{ count($tree) }}</p>
        @foreach ($tree as $grp)
            <div style="margin-top:8px;color:var(--wl-ink)">{{ $grp['subject']->displayName() }} — {{ $grp['vcount'] ?? 0 }} video · {{ $grp['mcount'] ?? 0 }} bahan · {{ count($grp['chapters']) }} bab</div>
        @endforeach
    </div>
</x-student-layout>
