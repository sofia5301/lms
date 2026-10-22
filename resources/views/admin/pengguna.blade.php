<x-admin-layout :title="__('Pengurusan Pengguna')"
                :heading="__('Pengurusan Pengguna')"
                heading-icon="users"
                :sub="__('Cipta, sunting dan urus akaun cikgu dan murid')">

    {{-- When the admin filters to teachers, the "Year" column (a student attribute) becomes "Position". --}}
    @php($teacherView = $role === 'teacher')
    {{-- The Year/Position column only earns its place once a single role is chosen: in the combined
         "Semua peranan" list it is half a column of Tahun and half a column of "-", so it is dropped
         there. Teacher view keeps Jawatan; student view keeps Tahun. --}}
    @php($showAttr = filled($role))
    {{-- One geometry per view. The fourth column is the widest of the two it has to hold (Jawatan,
         not Tahun); the combined list runs the same layout minus that column. --}}
    @php($cols = $showAttr
        ? 'grid-template-columns:38px minmax(200px,2.1fr) 112px minmax(190px,2fr) minmax(140px,1.3fr) 112px 100px'
        : 'grid-template-columns:38px minmax(200px,2.1fr) 112px minmax(190px,2fr) 112px 100px')
    @php($avPalette = [['#ECE9F7','#7C5CBF'],['#E4EEF9','#2E6CA8'],['#DCF2EE','#0F7A68'],['#FCE4EC','#D23B6E'],['#FBEAD3','#C8821E'],['#E6F0FB','#3E86C9'],['#FBE4E4','#C24936']])

    {{-- The success banner is rendered once by <x-flash /> in the admin layout. --}}
    <div style="display:flex;flex-direction:column;gap:18px">

        {{-- Summary cards: round icon tile + big number, with a large filled illustration on the right. --}}
        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px">
            <div style="position:relative;overflow:hidden;background:var(--tp-surface);border:1px solid var(--tp-line);border-radius:18px;padding:18px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 10px rgba(46,44,80,.04)">
                <span style="width:66px;height:66px;border-radius:50%;background:#E4EEF9;color:#2E6CA8;display:grid;place-items:center;flex-shrink:0"><x-icon name="user" class="h-7 w-7" /></span>
                <div style="display:flex;flex-direction:column;gap:3px">
                    <span style="font-family:'Geist',sans-serif;font-size:13px;font-weight:700;color:#2E6CA8">{{ __('Cikgu') }}</span>
                    <span style="font-family:'Geist',sans-serif;font-size:28px;font-weight:800;color:var(--tp-ink);line-height:1">{{ number_format($counts['teacher']) }}</span>
                </div>
                <svg viewBox="0 0 24 24" fill="#A9C9F2" width="86" height="86" style="position:absolute;right:20px;top:50%;transform:translateY(-50%);z-index:0;pointer-events:none;opacity:.5"><circle cx="6.5" cy="9" r="2"/><path d="M6.5 11.5c-2 0-3.5 1.2-3.5 2.8V16h7v-1.7c0-1.6-1.5-2.8-3.5-2.8z"/><circle cx="17.5" cy="9" r="2"/><path d="M17.5 11.5c-2 0-3.5 1.2-3.5 2.8V16h7v-1.7c0-1.6-1.5-2.8-3.5-2.8z"/><circle cx="12" cy="8" r="3"/><path d="M12 11.5c-2.6 0-4.6 1.6-4.6 3.6V17.5h9.2V15.1c0-2-2-3.6-4.6-3.6z"/></svg>
            </div>
            <div style="position:relative;overflow:hidden;background:var(--tp-surface);border:1px solid var(--tp-line);border-radius:18px;padding:18px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 10px rgba(46,44,80,.04)">
                <span style="width:66px;height:66px;border-radius:50%;background:#DCF2EE;color:#0F7A68;display:grid;place-items:center;flex-shrink:0"><x-icon name="graduation" class="h-7 w-7" /></span>
                <div style="display:flex;flex-direction:column;gap:3px">
                    <span style="font-family:'Geist',sans-serif;font-size:13px;font-weight:700;color:#0F7A68">{{ __('Murid') }}</span>
                    <span style="font-family:'Geist',sans-serif;font-size:28px;font-weight:800;color:var(--tp-ink);line-height:1">{{ number_format($counts['student']) }}</span>
                </div>
                <svg viewBox="0 0 24 24" fill="#9ED8C8" width="86" height="86" style="position:absolute;right:20px;top:50%;transform:translateY(-50%);z-index:0;pointer-events:none;opacity:.5"><path d="M12 3 1 8l11 5 9-4.1V14h2V8z"/><path d="M5 11.1V15c0 1.5 3.1 3 7 3s7-1.5 7-3v-3.9l-7 3.2z"/></svg>
            </div>
            <div style="position:relative;overflow:hidden;background:var(--tp-surface);border:1px solid var(--tp-line);border-radius:18px;padding:18px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 10px rgba(46,44,80,.04)">
                <span style="width:66px;height:66px;border-radius:50%;background:#FBE4E4;color:#C24936;display:grid;place-items:center;flex-shrink:0"><x-icon name="clock" class="h-7 w-7" /></span>
                <div style="display:flex;flex-direction:column;gap:3px">
                    <span style="font-family:'Geist',sans-serif;font-size:13px;font-weight:700;color:#C24936">{{ __('Tidak aktif') }}</span>
                    <span style="font-family:'Geist',sans-serif;font-size:28px;font-weight:800;color:var(--tp-ink);line-height:1">{{ number_format($counts['inactive']) }}</span>
                </div>
                <svg viewBox="0 0 24 24" fill="#EBB1A8" width="86" height="86" style="position:absolute;right:20px;top:50%;transform:translateY(-50%);z-index:0;pointer-events:none;opacity:.5"><circle cx="6.5" cy="9" r="2"/><path d="M6.5 11.5c-2 0-3.5 1.2-3.5 2.8V16h7v-1.7c0-1.6-1.5-2.8-3.5-2.8z"/><circle cx="17.5" cy="9" r="2"/><path d="M17.5 11.5c-2 0-3.5 1.2-3.5 2.8V16h7v-1.7c0-1.6-1.5-2.8-3.5-2.8z"/><circle cx="12" cy="8" r="3"/><path d="M12 11.5c-2.6 0-4.6 1.6-4.6 3.6V17.5h9.2V15.1c0-2-2-3.6-4.6-3.6z"/></svg>
            </div>
        </div>

        {{-- Toolbar: search + filters + add --}}
        <div style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">
            <form method="GET" action="{{ route('admin.pengguna') }}" style="display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;flex:1;min-width:0">
                <div class="tp-field" style="flex:1;min-width:200px">
                    <label class="tp-label">{{ __('Cari') }}</label>
                    <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Nama, nama pengguna atau emel') }}" class="tp-input">
                </div>
                <div class="tp-field">
                    <label class="tp-label">{{ __('Peranan') }}</label>
                    <select name="role" class="tp-filter-select" style="min-width:140px" onchange="this.form.submit()">
                        <option value="">{{ __('Semua peranan') }}</option>
                        <option value="teacher" @selected($role === 'teacher')>{{ __('Cikgu') }}</option>
                        <option value="student" @selected($role === 'student')>{{ __('Murid') }}</option>
                    </select>
                </div>
                <div class="tp-field">
                    <label class="tp-label">{{ __('Status') }}</label>
                    <select name="status" class="tp-filter-select" style="min-width:140px" onchange="this.form.submit()">
                        <option value="">{{ __('Semua status') }}</option>
                        <option value="active" @selected($status === 'active')>{{ __('Aktif') }}</option>
                        <option value="inactive" @selected($status === 'inactive')>{{ __('Tidak aktif') }}</option>
                    </select>
                </div>
            </form>
            <a href="{{ route('admin.pengguna.create') }}" class="tp-btn" style="min-height:46px;white-space:nowrap">+ {{ __('Tambah Pengguna') }}</a>
        </div>

        @if ($users->isEmpty())
            <div class="tp-empty">
                <x-icon name="users" class="h-8 w-8" style="color:var(--tp-muted)" />
                <h3 style="margin:0;font-family:'Geist',sans-serif;font-size:19px;font-weight:800;color:var(--tp-ink)">{{ __('Tiada pengguna') }}</h3>
                <p style="margin:0;font-size:14.5px;color:var(--tp-muted);max-width:380px">{{ __('Tiada akaun yang sepadan. Laraskan carian anda atau tambah pengguna baharu.') }}</p>
            </div>
        @else
            <div style="background:var(--tp-surface);border:1px solid var(--tp-line);border-radius:18px;overflow:hidden;box-shadow:0 2px 10px rgba(46,44,80,.04)">
                <div style="overflow-x:auto">
                    <div style="min-width:900px">
                        <div style="display:grid;{{ $cols }};gap:12px;padding:14px 20px;border-bottom:1px solid var(--tp-line);background:color-mix(in oklab, #17907B 5%, var(--tp-surface))">
                            <span style="display:grid;place-items:center"><input type="checkbox" onclick="document.querySelectorAll('.pg-check').forEach(c => c.checked = this.checked)" style="width:16px;height:16px;cursor:pointer;accent-color:#17907B"></span>
                            {{-- Everything but Name is centred; names keep a common left edge to scan down. --}}
                            @foreach (array_filter(['Nama', 'Peranan', 'Emel', $showAttr ? ($teacherView ? 'Jawatan' : 'Tahun') : null, 'Status', 'Tindakan']) as $h)
                                <span style="display:inline-flex;align-items:center;gap:3px;font-family:'Geist',sans-serif;font-size:12px;font-weight:800;color:var(--tp-muted);{{ $loop->first ? '' : 'justify-content:center' }}">{{ __($h) }}@unless ($h === 'Tindakan')<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.45"><path d="M8 9l4-4 4 4"/><path d="M16 15l-4 4-4-4"/></svg>@endunless</span>
                            @endforeach
                        </div>

                        @foreach ($users as $u)
                            @php($av = $avPalette[$u->id % count($avPalette)])
                            <div class="tp-tr" style="display:grid;{{ $cols }};gap:12px;align-items:center;padding:12px 20px;border-bottom:1px solid var(--tp-line)">
                                <span style="display:grid;place-items:center"><input type="checkbox" class="pg-check" style="width:16px;height:16px;cursor:pointer;accent-color:#17907B"></span>
                                <div style="display:flex;align-items:center;gap:12px;min-width:0">
                                    <span style="width:40px;height:40px;border-radius:50%;background:{{ $av[0] }};color:{{ $av[1] }};display:grid;place-items:center;font-family:'Geist',sans-serif;font-weight:800;font-size:13px;flex-shrink:0">{{ $u->initials() }}</span>
                                    <div style="display:flex;flex-direction:column;gap:1px;min-width:0">
                                        <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:13.5px;color:var(--tp-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $u->name }}</span>
                                        <span style="font-size:11.5px;color:var(--tp-muted)">{{ '@'.$u->username }}</span>
                                    </div>
                                </div>

                                @if ($u->isTeacher())
                                    <span style="justify-self:center;background:#E4EEF9;color:#2E6CA8;border-radius:999px;padding:4px 11px;font-family:'Geist',sans-serif;font-size:11.5px;font-weight:800">{{ __('Cikgu') }}</span>
                                @else
                                    <span style="justify-self:center;background:#DCF2EE;color:#0F7A68;border-radius:999px;padding:4px 11px;font-family:'Geist',sans-serif;font-size:11.5px;font-weight:800">{{ __('Murid') }}</span>
                                @endif

                                <span style="font-size:13px;font-weight:600;color:var(--tp-muted-2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-align:center">{{ $u->email ?: '-' }}</span>
                                @if ($showAttr)
                                    <span style="font-size:13px;font-weight:700;color:var(--tp-muted-2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-align:center">{{ $teacherView ? ($u->position ?: '-') : ($u->grade?->displayName() ?? '-') }}</span>
                                @endif

                                {{-- Read-only: the account is switched on or off from its edit form,
                                     so the list cannot deactivate someone on a stray click. The pill
                                     carries a background, so it centres by justify-self rather than
                                     text-align - otherwise it would stretch the whole column. --}}
                                <span style="justify-self:center;display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:5px 11px;font-family:'Geist',sans-serif;font-size:11.5px;font-weight:800;{{ $u->is_active ? 'background:#DCF2EE;color:#0F7A68' : 'background:#F1F0E8;color:var(--tp-muted)' }}">
                                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $u->is_active ? '#17907B' : '#B9B8C6' }}"></span>
                                    {{ $u->is_active ? __('Aktif') : __('Tidak aktif') }}
                                </span>

                                <div style="display:flex;align-items:center;gap:6px;justify-self:center">
                                    <a href="{{ route('admin.pengguna.edit', $u) }}" title="{{ __('Sunting') }}"
                                       style="width:34px;height:34px;border-radius:9px;border:1.5px solid var(--tp-line-2);background:var(--tp-surface);display:grid;place-items:center;color:#4A5A6B;text-decoration:none">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    </a>
                                    @php($delMsg = __('Padam akaun ":name"? Tindakan ini kekal. Jika ini akaun cikgu, semua video, bahan dan kuiz mereka turut dipadam.', ['name' => $u->name]))
                                    <x-confirm-modal id="del-user-{{ $u->id }}" :action="route('admin.pengguna.destroy', $u)"
                                        :title="__('Padam akaun?')" :message="$delMsg">
                                        <button type="button" title="{{ __('Padam') }}"
                                                style="width:34px;height:34px;border-radius:9px;border:1.5px solid rgba(194,73,54,.25);background:var(--tp-surface);cursor:pointer;display:grid;place-items:center;color:#C24936">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </x-confirm-modal>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>{{ $users->links() }}</div>
        @endif
    </div>
</x-admin-layout>
