<x-student-layout :title="__('Simpanan Offline')">
    <div style="padding:20px;max-width:900px;margin:0 auto">
        <h1 style="font-family:'Geist',sans-serif;font-weight:800;color:var(--wl-ink)">Simpanan Offline (debug)</h1>
        <p style="color:var(--wl-muted)">lessons: {{ $lessons->count() }} · material groups: {{ $materialsByChapter->count() }} · subjects: {{ $subjects->count() }} · grades: {{ $grades->count() }}</p>
    </div>
</x-student-layout>
