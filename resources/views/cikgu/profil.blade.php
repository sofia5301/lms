<x-cikgu-layout :title="__('Profil')" :heading="__('Profil')" :sub="__('Urus akaun dan tetapan anda')" heading-icon="user">
    @php
        // Exact WeLearn Teacher design tokens.
        $card = "background:var(--tp-surface);border:1px solid var(--tp-line);border-radius:18px;padding:24px;display:flex;flex-direction:column;box-shadow:0 2px 10px rgba(46,44,80,.04)";
        $h2 = "margin:0;font-family:'Geist',sans-serif;font-size:17px;font-weight:800;color:var(--tp-ink)";
        $label = "font-family:'Geist',sans-serif;font-size:12.5px;font-weight:800;color:var(--tp-muted-2)";
        // Trailing semicolon matters: these get concatenated below, and "width:100%display:flex"
        // is one invalid declaration that takes both properties down with it.
        $input = "min-height:46px;border:1.5px solid var(--tp-line-2);border-radius:12px;padding:0 14px;background:var(--tp-input);font-family:'Nunito',sans-serif;font-size:14.5px;color:var(--tp-ink);box-sizing:border-box;width:100%;";
        $field = "display:flex;flex-direction:column;gap:6px";
        // Lavender icon tile to the LEFT of each field (icon | label+input), see the image.
        $ftile = "width:36px;height:36px;border-radius:11px;background:#ECE9F7;color:#7C5CBF;display:grid;place-items:center;flex-shrink:0;margin-top:2px";
        $frow = "display:flex;gap:13px;align-items:flex-start";
        $fcol = "display:flex;flex-direction:column;gap:6px;flex:1;min-width:0";
        $primary = "align-self:flex-start;min-height:46px;border:none;cursor:pointer;border-radius:12px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:14px;padding:0 22px;display:inline-flex;align-items:center;gap:9px";
        $err = "margin:0;font-size:12.5px;font-weight:700;color:#C24936";
        // Admin-maintained fields. Styled like an input - same box, same ink - because the teacher
        // still needs to read these; they are simply not theirs to edit. Rendered as text rather
        // than a disabled <input>: there is nothing to submit, and a real input invites tabbing in.
        $locked = $input."display:flex;align-items:center;cursor:default";
        $note = "font-size:12.5px;color:var(--tp-muted-2)";
        // Save icon reused on both primary buttons.
        $saveIcon = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>';
    @endphp

    <style>
        .wl-primary:hover { background:#2BB39B; }
        .wl-primary:active { transform:scale(.98); }
        .wl-avatarbtn { transition:background .15s; }
        .wl-avatarbtn:hover { background:#2BB39B; }
        .pf-tophead { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; flex-wrap:wrap; }
        .pf-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:18px 24px; align-items:start; }
        .pf-cards { display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:stretch; }
        .pf-tile  { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; flex-shrink:0; }
        .pf-head  { display:flex; align-items:center; gap:12px; }
        .pf-eye   { position:absolute; right:7px; top:50%; transform:translateY(-50%); width:32px; height:32px; border:none; background:transparent; cursor:pointer; color:var(--tp-muted-2); display:grid; place-items:center; }
        @media (max-width:820px) { .pf-grid2, .pf-cards { grid-template-columns:1fr; } }
        @media (max-width:900px) {
            .wl-primary { min-height:40px !important; font-size:12.5px !important; padding:0 16px !important; gap:7px !important; }
            .wl-primary svg { width:15px !important; height:15px !important; }
        }
    </style>

    <div style="display:flex;flex-direction:column;gap:20px;max-width:1280px">

        {{-- Account details. Only the display name, photo and phone are editable; the rest is the
             school's record, shown for reference and changed by the admin. --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="{{ $card }};gap:20px">
            @csrf
            @method('PATCH')

            {{-- Header: title (left) + avatar & upload box (right). Clicking the circle or the box
                 opens the one hidden input; the chosen image previews live in the circle. --}}
            <div class="pf-tophead" x-data="{ name: '', preview: '' }">
                <div class="pf-head" style="align-items:flex-start">
                    <span class="pf-tile" style="background:#DCF2EE;color:#0F7A68"><x-icon name="user" class="h-5 w-5" /></span>
                    <div style="display:flex;flex-direction:column;gap:2px">
                        <h2 style="{{ $h2 }}">{{ __('Butiran akaun') }}</h2>
                        <span style="{{ $note }}">{{ __('Maklumat peribadi dan profesional anda.') }}</span>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
                    <label for="avatar" title="{{ $user->avatarUrl() ? __('Tukar gambar profil') : __('Tambah gambar profil') }}"
                           style="width:64px;height:64px;border-radius:50%;background:#DCF2EE;color:#0F7A68;display:flex;align-items:center;justify-content:center;font-family:'Geist',sans-serif;font-weight:800;font-size:20px;line-height:1;flex-shrink:0;overflow:hidden;cursor:pointer;position:relative">
                        <span x-show="! preview">{{ $user->initials() }}</span>
                        @if ($user->avatarUrl())
                            <img src="{{ $user->avatarUrl() }}" alt="" x-show="! preview" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                        @endif
                        <template x-if="preview">
                            <img :src="preview" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                        </template>
                    </label>

                    <label for="avatar" style="display:flex;align-items:center;gap:13px;border:1.5px dashed var(--tp-line-2);border-radius:14px;padding:11px 16px;cursor:pointer;min-width:250px">
                        <span class="wl-avatarbtn" style="width:44px;height:44px;border-radius:12px;background:#17907B;color:#fff;display:grid;place-items:center;flex-shrink:0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 20h16"/></svg>
                        </span>
                        <div style="display:flex;flex-direction:column;gap:2px;min-width:0">
                            <span class="tp-g" style="font-weight:800;font-size:14px;color:var(--tp-ink)">{{ $user->avatarUrl() ? __('Tukar gambar') : __('Tambah gambar') }}</span>
                            <span style="{{ $note }}" x-text="name || '{{ __('JPG atau PNG. Klik bulatan atau butang untuk pilih gambar.') }}'"></span>
                        </div>
                    </label>
                </div>

                <input type="file" id="avatar" name="avatar" accept="image/*" class="sr-only"
                       x-on:change="name = $event.target.files[0]?.name || ''; preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : ''"
                       @error('avatar') aria-invalid="true" @enderror>
            </div>
            @error('avatar')<p style="{{ $err }}">{{ $message }}</p>@enderror

            <div style="border-top:1px solid var(--tp-line)"></div>

            <div class="pf-grid2">
                {{-- Read-only: the school's record of the teacher's name. --}}
                {{-- Read-only: the school's record of the teacher's name. --}}
                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="user" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <span style="{{ $label }}">{{ __('Nama penuh') }}</span>
                        <span style="{{ $locked }}">{{ $user->name }}</span>
                    </div>
                </div>

                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="user" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <label for="username" style="{{ $label }}">{{ __('Nama pengguna (nama paparan)') }}</label>
                        <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required style="{{ $input }}">
                        <span style="{{ $note }}">{{ __('Nama ini dipaparkan di papan pemuka anda. Anda boleh menukarnya bila-bila masa.') }}</span>
                        @error('username')<p style="{{ $err }}">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Read-only: email is the sign-in identifier, set by the admin. --}}
                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="mail" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <span style="{{ $label }}">{{ __('E-mel (untuk log masuk)') }}</span>
                        <span style="{{ $locked }}">{{ $user->email }}</span>
                        <span style="{{ $note }}">{{ __('Emel log masuk anda tidak boleh diubah. Hubungi pentadbir sekolah jika ia perlu ditukar.') }}</span>
                    </div>
                </div>

                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="phone" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <label for="phone" style="{{ $label }}">{{ __('Nombor telefon') }}</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" placeholder="+60 12-345 6789" style="{{ $input }}">
                        @error('phone')<p style="{{ $err }}">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="briefcase" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <span style="{{ $label }}">{{ __('Jawatan') }}</span>
                        <span style="{{ $locked }}">{{ $user->position ?: __('Belum ditetapkan') }}</span>
                    </div>
                </div>

                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="building" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <span style="{{ $label }}">{{ __('Sekolah') }}</span>
                        <span style="{{ $locked }}">{{ $user->school?->name ?: __('Belum ditetapkan') }}</span>
                    </div>
                </div>

                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="users" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <span style="{{ $label }}">{{ __('Kelas guru kelas') }}</span>
                        <span style="{{ $locked }}">{{ $user->homeroomClass?->label() ?: __('Bukan guru kelas') }}</span>
                    </div>
                </div>

                {{-- Subjects taught: the ones assigned, as coloured chips. --}}
                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="book" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <span style="{{ $label }}">{{ __('Subjek diajar') }}</span>
                        @if ($user->subjects->isEmpty())
                            <span style="{{ $locked }}">{{ __('Belum ditetapkan') }}</span>
                        @else
                            <div style="display:flex;flex-wrap:wrap;gap:8px">
                                @foreach ($user->subjects as $subject)
                                    <span style="display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:6px 13px;font-family:'Nunito',sans-serif;font-size:13.5px;background:rgb({{ $subject->rgb }} / .12);color:rgb({{ $subject->rgb }})">{{ $subject->displayName() }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;background:rgba(23,144,123,.08);border:1px solid rgba(23,144,123,.18);border-radius:14px;padding:14px 18px">
                <x-icon name="info-circle" class="h-5 w-5" style="color:#0F7A68;flex-shrink:0" />
                <span style="flex:1;min-width:180px;font-size:13px;color:var(--tp-muted-2);line-height:1.5">{{ __('Butiran sekolah di atas diselenggara oleh pentadbir. Hubungi mereka jika ada yang perlu ditukar.') }}</span>
                <button type="submit" class="wl-primary" style="{{ $primary }};align-self:auto">{!! $saveIcon !!} {{ __('Simpan') }}</button>
            </div>
        </form>

        {{-- YouTube connect + change password, side by side. --}}
        <div class="pf-cards">
            {{-- Connect YouTube --}}
            @php($channels = $user->youtubeChannels()->latest('verified_at')->get())
            <div style="{{ $card }};gap:28px">
                <div class="pf-head">
                    <span class="pf-tile" style="background:#FBE4EA;color:#D23B4E"><x-icon name="youtube" class="h-5 w-5" /></span>
                    <h2 style="{{ $h2 }}">{{ __('Sambung YouTube') }}</h2>
                </div>
                <p style="margin:0;font-size:13.5px;color:var(--tp-muted-2);line-height:1.55">{{ __('Sahkan pemilikan saluran YouTube anda supaya video YouTube anda sendiri dikira dalam skor bakat. Kami hanya membaca senarai saluran anda - tiada token disimpan.') }}</p>

                @if (\App\Http\Controllers\YoutubeConnectController::isConfigured())
                    <a href="{{ route('oauth.youtube.redirect') }}" class="wl-primary" style="{{ $primary }};padding:0 20px;text-decoration:none"><x-icon name="play" class="h-4 w-4" /> {{ $channels->isEmpty() ? __('Sambung Akaun') : __('Sambung Lagi') }}</a>
                @endif

                @if ($channels->isEmpty())
                    <div style="display:flex;align-items:flex-start;gap:10px;background:var(--tp-input);border-radius:12px;padding:14px 16px">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0F7A68" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <span style="font-size:13px;color:var(--tp-muted-2);line-height:1.5">{{ __('Tiada saluran disambungkan lagi. Video YouTube anda dikira sebagai rujukan sehingga anda menyambung.') }}</span>
                    </div>
                @else
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach ($channels as $channel)
                            <div style="display:flex;align-items:center;gap:12px;background:var(--tp-input);border-radius:12px;padding:12px 14px">
                                @if ($channel->thumbnail_url)
                                    <img src="{{ $channel->thumbnail_url }}" alt="" loading="lazy" style="width:40px;height:40px;border-radius:50%;flex-shrink:0">
                                @else
                                    <span style="width:40px;height:40px;border-radius:50%;flex-shrink:0;background:#FBE4EA;color:#D23B4E;display:grid;place-items:center"><x-icon name="play" class="h-4 w-4" /></span>
                                @endif
                                <span style="min-width:0;flex:1">
                                    <span style="display:block;font-family:'Geist',sans-serif;font-weight:800;font-size:14px;color:var(--tp-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $channel->title }}</span>
                                    <span style="display:block;font-size:12px;color:var(--tp-muted)">{{ __('Disahkan :date', ['date' => $channel->verified_at->translatedFormat('d M Y')]) }}</span>
                                </span>
                                <x-confirm-modal id="yt-disconnect-{{ $channel->id }}" :action="route('oauth.youtube.disconnect', $channel)"
                                    icon="x" :confirm="__('Putuskan')"
                                    :title="__('Putuskan sambungan?')"
                                    :message="__('Putuskan sambungan saluran ini? Video YouTube dari saluran ini tidak akan lagi dikira untuk skor bakat anda.')">
                                    <button type="button" style="flex-shrink:0;min-height:38px;border:1.5px solid var(--tp-line-2);cursor:pointer;border-radius:10px;background:var(--tp-surface);color:#C24936;font-family:'Geist',sans-serif;font-weight:800;font-size:13px;padding:0 14px">{{ __('Putuskan') }}</button>
                                </x-confirm-modal>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Change password: collapsed behind a button; drops the form down on click. Opens
                 automatically when a password change failed validation, so the errors are visible. --}}
            <div style="{{ $card }};gap:16px" x-data="{ open: {{ $errors->updatePassword->isNotEmpty() ? 'true' : 'false' }} }">
                {{-- The whole card header is the toggle: lock + title (left), chevron (right). --}}
                <button type="button" @click="open = ! open" :aria-expanded="open" style="display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;background:transparent;border:none;cursor:pointer;padding:0;text-align:left">
                    <span style="display:flex;align-items:center;gap:12px;min-width:0">
                        <span class="pf-tile" style="background:#DCF2EE;color:#0F7A68"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                        <span style="{{ $h2 }}">{{ __('Tukar kata laluan') }}</span>
                    </span>
                    <x-icon name="chevron-down" class="h-5 w-5" ::style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .15s;color:var(--tp-muted);flex-shrink:0" />
                </button>

                <form method="POST" action="{{ route('profile.password') }}" x-show="open" x-cloak style="display:flex;flex-direction:column;gap:26px;margin-top:6px">
                @csrf
                @method('PUT')
                <span style="{{ $note }}">{{ __('Kemas kini kata laluan anda secara berkala untuk keselamatan akaun.') }}</span>
                @foreach ([
                    ['id' => 'current_password', 'label' => __('Kata laluan semasa'), 'bag' => true],
                    ['id' => 'password', 'label' => __('Kata laluan baru'), 'bag' => true],
                    ['id' => 'password_confirmation', 'label' => __('Ulang kata laluan baru'), 'bag' => false],
                ] as $pw)
                    <div style="{{ $field }}" x-data="{ show: false }">
                        <label for="{{ $pw['id'] }}" style="{{ $label }}">{{ $pw['label'] }}</label>
                        <div style="position:relative">
                            <input id="{{ $pw['id'] }}" name="{{ $pw['id'] }}" :type="show ? 'text' : 'password'" style="{{ $input }};padding-right:46px">
                            <button type="button" class="pf-eye" @click="show = ! show" :aria-label="show ? '{{ __('Sembunyikan') }}' : '{{ __('Papar') }}'">
                                <span x-show="! show"><x-icon name="eye" class="h-[18px] w-[18px]" /></span>
                                <span x-show="show" x-cloak><x-icon name="eye-off" class="h-[18px] w-[18px]" /></span>
                            </button>
                        </div>
                        @if ($pw['bag'])
                            @error($pw['id'], 'updatePassword')<p style="{{ $err }}">{{ $message }}</p>@enderror
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="wl-primary" style="{{ $primary }};margin-top:10px"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> {{ __('Tukar Kata Laluan') }}</button>
                </form>
            </div>
        </div>

    </div>
</x-cikgu-layout>
