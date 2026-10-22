@php($editing = $user->exists)

<x-admin-layout :title="$editing ? __('Sunting Pengguna') : __('Pengguna Baharu')"
                :heading="$editing ? __('Sunting Pengguna') : __('Pengguna Baharu')"
                heading-icon="user"
                :sub="__('Akaun cikgu atau murid')">

    <style>
        .pu-wrap [x-cloak] { display:none !important; }
        .pu-grid { display:grid; grid-template-columns:minmax(0,1fr) 340px; gap:22px; align-items:start; max-width:1560px; }
        @media (max-width:1040px) { .pu-grid { grid-template-columns:1fr; } }

        .pu-form { background:var(--tp-surface); border:1px solid var(--tp-line); border-radius:20px; padding:28px; display:flex; flex-direction:column; gap:30px; box-shadow:0 2px 10px rgba(46,44,80,.04); }
        .pu-step { display:flex; flex-direction:column; gap:16px; }
        .pu-stephead { display:flex; align-items:flex-start; gap:14px; }
        .pu-num { width:30px; height:30px; border-radius:50%; background:#17907B; color:#fff; display:grid; place-items:center; font-family:'Geist',sans-serif; font-weight:800; font-size:14px; flex-shrink:0; }
        .pu-stitle { margin:0; font-family:'Geist',sans-serif; font-size:16px; font-weight:800; color:var(--tp-ink); }
        .pu-ssub { font-size:13px; color:var(--tp-muted-2); }
        .pu-divider { height:1px; background:var(--tp-line); }

        .pu-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px 20px; }
        @media (max-width:640px) { .pu-grid2 { grid-template-columns:1fr; } }
        .pu-field { display:flex; flex-direction:column; gap:6px; min-width:0; }
        .pu-frow { display:flex; gap:10px; align-items:center; }
        .pu-ftile { width:46px; height:46px; border-radius:12px; background:#ECE9F7; color:#7C5CBF; display:grid; place-items:center; flex-shrink:0; }
        .pu-field .tp-input { flex:1; min-width:0; }
        .pu-field .tp-filter-select { flex:1; min-width:0; width:100%; }

        .pu-roles { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        @media (max-width:640px) { .pu-roles { grid-template-columns:1fr; } }
        .pu-rolecard { display:flex; align-items:center; gap:14px; border:1.5px solid var(--tp-line-2); border-radius:14px; padding:16px; cursor:pointer; background:var(--tp-surface); text-align:left; }
        .pu-rolecard.is-on { border-color:#17907B; background:#E6F5F1; }
        .pu-roletile { width:48px; height:48px; border-radius:50%; background:#DCF2EE; color:#0F7A68; display:grid; place-items:center; flex-shrink:0; }
        .pu-rolename { font-family:'Geist',sans-serif; font-weight:800; font-size:15px; color:var(--tp-ink); }
        .pu-roledesc { font-size:12.5px; color:var(--tp-muted-2); line-height:1.45; }
        .pu-radio { width:22px; height:22px; border-radius:50%; border:2px solid var(--tp-line-2); margin-left:auto; flex-shrink:0; display:grid; place-items:center; }
        .pu-rolecard.is-on .pu-radio { border-color:#17907B; }
        .pu-rolecard.is-on .pu-radio::after { content:''; width:11px; height:11px; border-radius:50%; background:#17907B; }

        .pu-subjgrid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px 18px; border:1.5px solid var(--tp-line-2); border-radius:14px; padding:18px; }
        @media (max-width:860px) { .pu-subjgrid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:520px) { .pu-subjgrid { grid-template-columns:1fr; } }
        .pu-subj { display:flex; align-items:center; gap:8px; font-size:13.5px; color:var(--tp-ink); cursor:pointer; }

        .pu-set { display:flex; align-items:flex-start; gap:11px; cursor:pointer; }
        .pu-settxt { display:flex; flex-direction:column; gap:2px; }
        .pu-err { margin:0; font-size:12.5px; font-weight:700; color:#C24936; }

        .pu-guide { background:color-mix(in oklab, #17907B 7%, var(--tp-surface)); border:1px solid var(--tp-line); border-radius:20px; padding:24px; position:sticky; top:16px; }
        .pu-guidehero { border-radius:16px; overflow:hidden; margin-bottom:16px; aspect-ratio:3/2; }
        .pu-guidehero img { width:100%; height:100%; object-fit:cover; display:block; }
    </style>

    <div class="pu-wrap" style="max-width:1560px;margin-left:auto;margin-right:auto;width:100%">
        <a href="{{ route('admin.pengguna') }}" class="tp-btn-outline"
           style="align-self:flex-start;min-height:40px;border-radius:11px;font-size:13px;padding:0 14px;border-width:1.5px;margin-bottom:16px;display:inline-flex;align-items:center;gap:6px"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Semua pengguna') }}</a>

        <div class="pu-grid"
             x-data="{
                 role: '{{ old('role', $user->role ?: 'teacher') }}',
                 schoolId: '{{ old('school_id', $user->school_id) }}',
                 gradeLevel: '{{ old('grade_level', $user->grade?->level) }}',
                 schoolClass: '{{ old('school_class_id', $user->school_class_id) }}',
                 autoPassword: {{ old('auto_password', $editing ? 0 : 1) ? 'true' : 'false' }},
                 classes: {{ \Illuminate\Support\Js::from($allClasses) }},
                 gradeMap: {{ \Illuminate\Support\Js::from($grades->pluck('id', 'level')) }},
                 get gradeId() { return this.gradeMap[this.gradeLevel] ?? null; },
                 get availableClasses() { return this.classes.filter(c => String(c.school_id) === String(this.schoolId) && String(c.grade_id) === String(this.gradeId)); },
                 onContextChange() { if (this.schoolClass && ! this.availableClasses.some(c => String(c.id) === String(this.schoolClass))) this.schoolClass = ''; },
             }">

            <form method="POST" action="{{ $editing ? route('admin.pengguna.update', $user) : route('admin.pengguna.store') }}" class="pu-form">
                @csrf
                @if ($editing) @method('PUT') @endif

                {{-- Step 1: Role --}}
                <div class="pu-step">
                    <div class="pu-stephead">
                        <span class="pu-num">1</span>
                        <div style="display:flex;flex-direction:column;gap:2px">
                            <h3 class="pu-stitle">{{ __('Peranan') }}</h3>
                            <span class="pu-ssub">{{ __('Pilih jenis pengguna yang ingin didaftarkan.') }}</span>
                        </div>
                    </div>
                    @if ($editing)
                        <span style="align-self:flex-start;border-radius:999px;padding:6px 14px;font-family:'Geist',sans-serif;font-size:12.5px;font-weight:800;{{ $user->isTeacher() ? 'background:#E4EEF9;color:#2E6CA8' : 'background:#DCF2EE;color:#0F7A68' }}">{{ $user->isTeacher() ? __('Cikgu') : __('Murid') }}</span>
                        <span class="pu-ssub">{{ __('Peranan tidak boleh ditukar selepas akaun dicipta.') }}</span>
                    @else
                        <input type="hidden" name="role" :value="role">
                        <div class="pu-roles">
                            <button type="button" class="pu-rolecard" :class="{ 'is-on': role === 'teacher' }" @click="role = 'teacher'">
                                <span class="pu-roletile"><x-icon name="presentation" class="h-6 w-6" /></span>
                                <span style="display:flex;flex-direction:column;gap:2px;min-width:0">
                                    <span class="pu-rolename">{{ __('Cikgu') }}</span>
                                    <span class="pu-roledesc">{{ __('Akaun untuk guru yang mengajar di sekolah.') }}</span>
                                </span>
                                <span class="pu-radio"></span>
                            </button>
                            <button type="button" class="pu-rolecard" :class="{ 'is-on': role === 'student' }" @click="role = 'student'">
                                <span class="pu-roletile"><x-icon name="graduation" class="h-6 w-6" /></span>
                                <span style="display:flex;flex-direction:column;gap:2px;min-width:0">
                                    <span class="pu-rolename">{{ __('Murid') }}</span>
                                    <span class="pu-roledesc">{{ __('Akaun untuk pelajar di sekolah.') }}</span>
                                </span>
                                <span class="pu-radio"></span>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="pu-divider"></div>

                {{-- Step 2: Basic info --}}
                <div class="pu-step">
                    <div class="pu-stephead">
                        <span class="pu-num">2</span>
                        <div style="display:flex;flex-direction:column;gap:2px">
                            <h3 class="pu-stitle">{{ __('Maklumat asas') }}</h3>
                            <span class="pu-ssub">{{ __('Masukkan maklumat pengguna dengan lengkap.') }}</span>
                        </div>
                    </div>

                    <div class="pu-grid2">
                        <div class="pu-field">
                            <label for="name" class="tp-label">{{ __('Nama penuh') }} <span style="color:#C24936">*</span></label>
                            <div class="pu-frow"><span class="pu-ftile"><x-icon name="user" class="h-[18px] w-[18px]" /></span><input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" placeholder="{{ __('Masukkan nama penuh') }}" required class="tp-input"></div>
                            @error('name')<p class="pu-err">{{ $message }}</p>@enderror
                        </div>
                        <div class="pu-field">
                            <label for="username" class="tp-label">{{ __('Nama pengguna (nama paparan)') }} <span style="color:#C24936">*</span></label>
                            <div class="pu-frow"><span class="pu-ftile"><x-icon name="user" class="h-[18px] w-[18px]" /></span><input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" placeholder="{{ __('Masukkan nama pengguna') }}" required class="tp-input"></div>
                            <span class="tp-hint">{{ __('Dipaparkan di papan pemuka mereka, dan boleh ditukar sendiri. Log masuk menggunakan emel, bukan nama ini.') }}</span>
                            @error('username')<p class="pu-err">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="pu-field">
                        <label for="email" class="tp-label">{{ __('Emel') }} <span style="font-weight:600;color:var(--tp-muted)">({{ __('untuk log masuk') }})</span> <span style="color:#C24936">*</span></label>
                        <div class="pu-frow"><span class="pu-ftile"><x-icon name="mail" class="h-[18px] w-[18px]" /></span><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="contoh: nama@moe.gov.my" required class="tp-input"></div>
                        <span class="tp-hint">{{ __('Pengguna log masuk dengan emel ini dan tidak boleh menukarnya sendiri.') }}</span>
                        @error('email')<p class="pu-err">{{ $message }}</p>@enderror
                    </div>

                    {{-- School (both roles) --}}
                    <div class="pu-grid2">
                        {{-- Teacher phone sits next to School; for students School pairs with Tahun below. --}}
                        <div class="pu-field" x-show="role === 'teacher'" x-cloak>
                            <label for="phone" class="tp-label">{{ __('Nombor telefon') }}</label>
                            <div class="pu-frow"><span class="pu-ftile"><x-icon name="phone" class="h-[18px] w-[18px]" /></span><input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" placeholder="{{ __('Masukkan nombor telefon') }}" class="tp-input"></div>
                            @error('phone')<p class="pu-err">{{ $message }}</p>@enderror
                        </div>
                        <div class="pu-field">
                            <label for="school_id" class="tp-label">{{ __('Sekolah') }}</label>
                            <div class="pu-frow"><span class="pu-ftile"><x-icon name="building" class="h-[18px] w-[18px]" /></span>
                                <select id="school_id" name="school_id" class="tp-filter-select" x-model="schoolId" @change="onContextChange()">
                                    <option value="">{{ __('Tiada / Belum ditetapkan') }}</option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}" @selected((int) old('school_id', $user->school_id) === $school->id)>{{ $school->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('school_id')<p class="pu-err">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Teacher: position + homeroom class --}}
                    <template x-if="role === 'teacher'">
                        <div class="pu-grid2">
                            <div class="pu-field">
                                <label for="position" class="tp-label">{{ __('Jawatan') }}</label>
                                <div class="pu-frow"><span class="pu-ftile"><x-icon name="briefcase" class="h-[18px] w-[18px]" /></span><input id="position" name="position" type="text" value="{{ old('position', $user->position) }}" placeholder="{{ __('Masukkan jawatan') }}" class="tp-input"></div>
                                @error('position')<p class="pu-err">{{ $message }}</p>@enderror
                            </div>
                            @php($homeroomId = old('homeroom_class_id', $user->homeroomClass?->id))
                            <div class="pu-field">
                                <label for="homeroom_class_id" class="tp-label">{{ __('Kelas guru kelas') }}</label>
                                <div class="pu-frow"><span class="pu-ftile"><x-icon name="users" class="h-[18px] w-[18px]" /></span>
                                    <select id="homeroom_class_id" name="homeroom_class_id" class="tp-filter-select">
                                        <option value="">{{ __('Bukan guru kelas') }}</option>
                                        @foreach ($allClasses as $c)
                                            <option value="{{ $c['id'] }}" @selected((int) $homeroomId === (int) $c['id'])>{{ $c['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="tp-hint">{{ __('Setiap kelas hanya boleh ada seorang guru kelas.') }}</span>
                                @error('homeroom_class_id')<p class="pu-err">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </template>

                    {{-- Student: Tahun + class + guardian --}}
                    <div class="pu-grid2" x-show="role === 'student'" x-cloak>
                        <div class="pu-field">
                            <label for="grade_level" class="tp-label">{{ __('Tahun') }}</label>
                            <div class="pu-frow"><span class="pu-ftile"><x-icon name="graduation" class="h-[18px] w-[18px]" /></span>
                                <select id="grade_level" name="grade_level" class="tp-filter-select" x-model="gradeLevel" @change="onContextChange()">
                                    <option value="">{{ __('Pilih Tahun') }}</option>
                                    @foreach ($grades as $grade)
                                        <option value="{{ $grade->level }}" @selected(old('grade_level', $user->grade?->level) == $grade->level)>{{ $grade->displayName() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('grade_level')<p class="pu-err">{{ $message }}</p>@enderror
                        </div>
                        <div class="pu-field">
                            <label for="school_class_id" class="tp-label">{{ __('Kelas') }} <span style="color:#C24936">*</span></label>
                            <div class="pu-frow"><span class="pu-ftile"><x-icon name="users" class="h-[18px] w-[18px]" /></span>
                                <select id="school_class_id" name="school_class_id" class="tp-filter-select"
                                        x-model="schoolClass" x-bind:disabled="! schoolId || ! gradeLevel"
                                        x-init="$nextTick(() => { if (schoolClass) $el.value = schoolClass })">
                                    <option value="">{{ __('Sila pilih kelas') }}</option>
                                    <template x-for="c in availableClasses" :key="c.id">
                                        <option :value="c.id" x-text="c.label"></option>
                                    </template>
                                </select>
                            </div>
                            <p class="tp-hint" x-show="! schoolId || ! gradeLevel" x-cloak>{{ __('Pilih sekolah dan tahun dahulu.') }}</p>
                            <p class="tp-hint" x-show="schoolId && gradeLevel && availableClasses.length === 0" x-cloak style="color:#C24936">{{ __('Tiada kelas untuk sekolah dan tahun ini. Cipta kelas itu dahulu.') }}</p>
                            @error('school_class_id')<p class="pu-err">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <template x-if="role === 'student'">
                        <div class="pu-grid2">
                            <div class="pu-field">
                                <label for="guardian_name" class="tp-label">{{ __('Nama penjaga') }}</label>
                                <div class="pu-frow"><span class="pu-ftile"><x-icon name="user" class="h-[18px] w-[18px]" /></span><input id="guardian_name" name="guardian_name" type="text" value="{{ old('guardian_name', $user->guardian_name) }}" class="tp-input"></div>
                                @error('guardian_name')<p class="pu-err">{{ $message }}</p>@enderror
                            </div>
                            <div class="pu-field">
                                <label for="guardian_phone" class="tp-label">{{ __('Telefon penjaga') }}</label>
                                <div class="pu-frow"><span class="pu-ftile"><x-icon name="phone" class="h-[18px] w-[18px]" /></span><input id="guardian_phone" name="guardian_phone" type="tel" value="{{ old('guardian_phone', $user->guardian_phone) }}" class="tp-input"></div>
                                @error('guardian_phone')<p class="pu-err">{{ $message }}</p>@enderror
                            </div>
                            <div class="pu-field" style="grid-column:1/-1">
                                <label for="guardian_email" class="tp-label">{{ __('E-mel penjaga') }} <span style="font-weight:600;color:var(--tp-muted)">({{ __('untuk butiran log masuk') }})</span> <span style="color:#C24936">*</span></label>
                                <div class="pu-frow"><span class="pu-ftile"><x-icon name="mail" class="h-[18px] w-[18px]" /></span><input id="guardian_email" name="guardian_email" type="email" value="{{ old('guardian_email', $user->guardian_email) }}" required class="tp-input"></div>
                                <span class="tp-hint">{{ __('Nama pengguna dan kata laluan murid dihantar ke alamat ini.') }}</span>
                                @error('guardian_email')<p class="pu-err">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Step 3: Subjects (teacher) --}}
                <template x-if="role === 'teacher'">
                    <div style="display:flex;flex-direction:column;gap:30px">
                    <div class="pu-divider"></div>
                    <div class="pu-step">
                        <div class="pu-stephead">
                            <span class="pu-num">3</span>
                            <div style="display:flex;flex-direction:column;gap:2px">
                                <h3 class="pu-stitle">{{ __('Subjek diajar') }}</h3>
                                <span class="pu-ssub">{{ __('Pilih semua subjek yang berkaitan. (untuk cikgu)') }}</span>
                            </div>
                        </div>
                        @php($chosen = old('subjects', $user->exists ? $user->subjects->pluck('id')->all() : []))
                        <div class="pu-subjgrid">
                            @foreach ($subjects as $subject)
                                <label class="pu-subj">
                                    <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" @checked(in_array($subject->id, $chosen)) style="width:17px;height:17px;accent-color:#17907B">
                                    {{ $subject->displayName() }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    </div>
                </template>

                <div class="pu-divider"></div>

                {{-- Step 4: Account settings --}}
                <div class="pu-step">
                    <div class="pu-stephead">
                        <span class="pu-num" x-text="role === 'teacher' ? '4' : '3'">4</span>
                        <div style="display:flex;flex-direction:column;gap:2px">
                            <h3 class="pu-stitle">{{ __('Tetapan akaun') }}</h3>
                        </div>
                    </div>

                    @if ($editing)
                        @php($ownsPassword = $user->password_changed_at !== null)
                        <div style="border-radius:12px;padding:12px 14px;display:flex;flex-direction:column;gap:3px;{{ $ownsPassword ? 'background:#DCF2EE;border:1px solid rgba(15,122,104,.25)' : 'background:#FEF0CE;border:1px solid rgba(138,106,18,.25)' }}">
                            <span style="font-family:'Geist',sans-serif;font-size:13px;font-weight:800;display:inline-flex;align-items:center;gap:6px;color:{{ $ownsPassword ? '#0F7A68' : '#8A6A12' }}"><x-icon name="{{ $ownsPassword ? 'check' : 'clock' }}" class="h-4 w-4" />{{ $ownsPassword ? __('Kata laluan sendiri') : __('Masih guna kata laluan yang anda beri') }}</span>
                            <span style="font-size:12.5px;color:var(--tp-muted-2)">@if ($ownsPassword){{ __('Ditetapkan sendiri oleh pengguna pada :date.', ['date' => $user->password_changed_at->translatedFormat('j F Y, g:ia')]) }}@else{{ __('Pengguna akan diminta menetapkan kata laluan sendiri pada log masuk seterusnya.') }}@endif</span>
                        </div>
                    @endif

                    <div class="pu-field" x-show="! autoPassword" x-cloak>
                        <label for="password" class="tp-label">{{ __('Kata laluan') }}</label>
                        <div class="pu-frow"><span class="pu-ftile"><x-icon name="lock" class="h-[18px] w-[18px]" /></span><input id="password" name="password" type="password" class="tp-input" autocomplete="new-password" @if ($editing) placeholder="••••••••" @endif></div>
                        @if ($editing)<span class="tp-hint">{{ __('Biarkan kosong untuk mengekalkan kata laluan semasa. Jika diisi, pengguna perlu menetapkan kata laluan sendiri semula pada log masuk seterusnya.') }}</span>@endif
                        @error('password')<p class="pu-err">{{ $message }}</p>@enderror
                    </div>

                    <div class="pu-grid2">
                        <label class="pu-set">
                            <input type="checkbox" name="auto_password" value="1" x-model="autoPassword" style="width:20px;height:20px;accent-color:#17907B;margin-top:1px;flex-shrink:0">
                            <span class="pu-settxt">
                                <span style="font-family:'Geist',sans-serif;font-size:13.5px;font-weight:700;color:var(--tp-ink)">{{ $editing ? __('Set semula kata laluan') : __('Jana kata laluan secara automatik') }}</span>
                                <span class="pu-ssub">{{ __('Kata laluan baharu akan dijana dan dihantar kepada pengguna atau penjaga.') }}</span>
                            </span>
                        </label>
                        <label class="pu-set">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true)) style="width:20px;height:20px;accent-color:#17907B;margin-top:1px;flex-shrink:0">
                            <span class="pu-settxt">
                                <span style="font-family:'Geist',sans-serif;font-size:13.5px;font-weight:700;color:var(--tp-ink)">{{ __('Akaun aktif (boleh log masuk)') }}</span>
                                <span class="pu-ssub">{{ __('Pengguna boleh log masuk ke sistem selepas akaun dicipta.') }}</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div style="display:flex;gap:12px;margin-top:4px">
                    <button type="submit" class="tp-btn" style="min-height:48px;display:inline-flex;align-items:center;gap:8px"><x-icon name="plus" class="h-4 w-4" /> {{ $editing ? __('Simpan Perubahan') : __('Cipta Akaun') }}</button>
                    <a href="{{ route('admin.pengguna') }}" class="tp-btn-outline" style="min-height:48px">{{ __('Batal') }}</a>
                </div>
            </form>

            {{-- Guide --}}
            <aside class="pu-guide">
                <div class="pu-guidehero"><img src="{{ asset('images/add-user.png') }}" alt=""></div>
                <h3 style="margin:0 0 8px;font-family:'Geist',sans-serif;font-size:17px;font-weight:800;color:var(--tp-ink)">{{ $editing ? __('Sunting pengguna') : __('Tambah pengguna baharu') }}</h3>
                <p style="margin:0;font-size:13.5px;color:var(--tp-muted-2);line-height:1.6">{{ __('Cipta akaun untuk cikgu atau murid supaya mereka boleh mengakses kandungan pembelajaran di WeLearn.') }}</p>
            </aside>
        </div>
    </div>
</x-admin-layout>
