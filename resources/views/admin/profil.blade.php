<x-admin-layout :title="__('Profil Saya')"
                :heading="__('Profil Saya')"
                heading-icon="user"
                :sub="__('Kemas kini butiran akaun dan kata laluan anda')">
    @php
        $user = auth()->user();
        $card = "background:var(--tp-surface);border:1px solid var(--tp-line);border-radius:18px;padding:24px;display:flex;flex-direction:column;box-shadow:0 2px 10px rgba(46,44,80,.04)";
        $h2 = "margin:0;font-family:'Geist',sans-serif;font-size:17px;font-weight:800;color:var(--tp-ink)";
        $label = "font-family:'Geist',sans-serif;font-size:12.5px;font-weight:800;color:var(--tp-muted-2)";
        $input = "min-height:46px;border:1.5px solid var(--tp-line-2);border-radius:12px;padding:0 14px;background:var(--tp-input);font-family:'Nunito',sans-serif;font-size:14.5px;color:var(--tp-ink);box-sizing:border-box;width:100%;";
        $field = "display:flex;flex-direction:column;gap:6px";
        $ftile = "width:36px;height:36px;border-radius:11px;background:#ECE9F7;color:#7C5CBF;display:grid;place-items:center;flex-shrink:0;margin-top:2px";
        $frow = "display:flex;gap:13px;align-items:flex-start";
        $fcol = "display:flex;flex-direction:column;gap:6px;flex:1;min-width:0";
        $primary = "min-height:46px;border:none;cursor:pointer;border-radius:12px;background:#17907B;color:#fff;font-family:'Geist',sans-serif;font-weight:800;font-size:14px;padding:0 22px;display:inline-flex;align-items:center;gap:9px";
        $err = "margin:0;font-size:12.5px;font-weight:700;color:#C24936";
        $note = "font-size:12.5px;color:var(--tp-muted-2)";
        $saveIcon = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>';
    @endphp

    <style>
        .wl-primary:hover { background:#2BB39B; }
        .wl-primary:active { transform:scale(.98); }
        .wl-avatarbtn:hover { background:#2BB39B; }
        .pf-tophead { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; }
        .pf-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:18px 24px; align-items:start; }
        .pf-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:20px 24px; align-items:start; }
        .pf-tile  { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; flex-shrink:0; }
        .pf-head  { display:flex; align-items:center; gap:16px; }
        .pf-eye   { position:absolute; right:7px; top:50%; transform:translateY(-50%); width:32px; height:32px; border:none; background:transparent; cursor:pointer; color:var(--tp-muted-2); display:grid; place-items:center; }
        .pf-avatar { position:relative; width:92px; height:92px; flex-shrink:0; cursor:pointer; }
        .pf-avatarinner { position:absolute; inset:0; border-radius:18px; overflow:hidden; background:#DCF2EE; color:#0F7A68; display:flex; align-items:center; justify-content:center; font-family:'Geist',sans-serif; font-weight:800; font-size:26px; }
        .pf-cam { position:absolute; right:-5px; bottom:-5px; width:30px; height:30px; border-radius:50%; background:#17907B; color:#fff; display:grid; place-items:center; border:3px solid var(--tp-surface); z-index:2; }
        @media (max-width:900px) { .pf-grid2, .pf-grid3 { grid-template-columns:1fr; } }
    </style>

    <div style="display:flex;flex-direction:column;gap:20px;max-width:1280px;margin:48px auto 0;width:100%">

        {{-- Account details --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="{{ $card }};gap:20px">
            @csrf
            @method('PATCH')

            {{-- Header: avatar + title (left), upload box (right). --}}
            <div class="pf-tophead" x-data="{ name: '', preview: '' }">
                <div class="pf-head">
                    <label for="avatar" class="pf-avatar" title="{{ $user->avatarUrl() ? __('Tukar gambar profil') : __('Tambah gambar profil') }}">
                        <span class="pf-avatarinner">
                            <span x-show="! preview">{{ $user->initials() }}</span>
                            @if ($user->avatarUrl())
                                <img src="{{ $user->avatarUrl() }}" alt="" x-show="! preview" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                            @endif
                            <template x-if="preview">
                                <img :src="preview" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                            </template>
                        </span>
                        <span class="pf-cam"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg></span>
                    </label>
                    <div style="display:flex;flex-direction:column;gap:3px">
                        <h2 style="{{ $h2 }}">{{ __('Butiran akaun') }}</h2>
                        <span style="{{ $note }}">{{ __('Maklumat akaun pentadbir anda.') }}</span>
                    </div>
                </div>

                <label for="avatar" style="display:flex;align-items:center;gap:13px;border:1.5px solid var(--tp-line);border-radius:14px;padding:12px 18px;cursor:pointer;min-width:280px">
                    <span class="wl-avatarbtn" style="width:44px;height:44px;border-radius:12px;background:#17907B;color:#fff;display:grid;place-items:center;flex-shrink:0;transition:background .15s">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 20h16"/></svg>
                    </span>
                    <div style="display:flex;flex-direction:column;gap:2px;min-width:0">
                        <span style="font-family:'Geist',sans-serif;font-weight:800;font-size:14.5px;color:var(--tp-ink)">{{ $user->avatarUrl() ? __('Tukar gambar') : __('Tambah gambar') }}</span>
                        <span style="{{ $note }}" x-text="name || '{{ __('JPG atau PNG. Klik bulatan atau butang untuk pilih gambar.') }}'"></span>
                    </div>
                </label>

                <input type="file" id="avatar" name="avatar" accept="image/*" class="sr-only"
                       x-on:change="name = $event.target.files[0]?.name || ''; preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : ''"
                       @error('avatar') aria-invalid="true" @enderror>
            </div>
            @error('avatar')<p style="{{ $err }}">{{ $message }}</p>@enderror

            <div style="border-top:1px solid var(--tp-line)"></div>

            <div class="pf-grid2">
                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="user" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <label for="name" style="{{ $label }}">{{ __('Nama penuh') }}</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required style="{{ $input }}">
                        @error('name')<p style="{{ $err }}">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="user" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <label for="username" style="{{ $label }}">{{ __('Nama pengguna') }}</label>
                        <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required style="{{ $input }}">
                        @error('username')<p style="{{ $err }}">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="{{ $frow }}">
                    <span style="{{ $ftile }}"><x-icon name="mail" class="h-[18px] w-[18px]" /></span>
                    <div style="{{ $fcol }}">
                        <label for="email" style="{{ $label }}">{{ __('E-mel') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" style="{{ $input }}">
                        @error('email')<p style="{{ $err }}">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:24px">
                <button type="submit" class="wl-primary" style="{{ $primary }}">{!! $saveIcon !!} {{ __('Simpan') }}</button>
            </div>
        </form>

        {{-- Change password: collapsible, opens automatically when a change failed validation. --}}
        <div style="{{ $card }};gap:18px" x-data="{ open: {{ $errors->updatePassword->isNotEmpty() ? 'true' : 'false' }} }">
            <button type="button" @click="open = ! open" :aria-expanded="open" style="display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;background:transparent;border:none;cursor:pointer;padding:0;text-align:left">
                <span style="display:flex;align-items:center;gap:14px;min-width:0">
                    <span class="pf-tile" style="background:#DCF2EE;color:#0F7A68"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    <span style="display:flex;flex-direction:column;gap:2px;min-width:0">
                        <span style="{{ $h2 }}">{{ __('Tukar kata laluan') }}</span>
                        <span style="{{ $note }}">{{ __('Tetapkan kata laluan baharu anda untuk keselamatan akaun.') }}</span>
                    </span>
                </span>
                <x-icon name="chevron-down" class="h-5 w-5" ::style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .15s;color:var(--tp-muted);flex-shrink:0" />
            </button>

            <form method="POST" action="{{ route('password.update') }}" x-show="open" x-cloak style="display:flex;flex-direction:column;gap:20px;margin-top:22px">
                @csrf
                @method('PUT')
                <div class="pf-grid3">
                    @foreach ([
                        ['id' => 'current_password', 'label' => __('Kata laluan semasa'), 'ph' => __('Masukkan kata laluan semasa'), 'ac' => 'current-password', 'icon' => 'key'],
                        ['id' => 'password', 'label' => __('Kata laluan baharu'), 'ph' => __('Masukkan kata laluan baharu'), 'ac' => 'new-password', 'icon' => 'lock'],
                        ['id' => 'password_confirmation', 'label' => __('Sahkan kata laluan baharu'), 'ph' => __('Masukkan semula kata laluan baharu'), 'ac' => 'new-password', 'icon' => 'lock'],
                    ] as $pw)
                        <div style="{{ $frow }}" x-data="{ show: false }">
                            <span style="{{ $ftile }}"><x-icon :name="$pw['icon']" class="h-[18px] w-[18px]" /></span>
                            <div style="{{ $fcol }}">
                                <label for="{{ $pw['id'] }}" style="{{ $label }}">{{ $pw['label'] }}</label>
                                <div style="position:relative">
                                    <input id="{{ $pw['id'] }}" name="{{ $pw['id'] }}" :type="show ? 'text' : 'password'" placeholder="{{ $pw['ph'] }}" autocomplete="{{ $pw['ac'] }}" style="{{ $input }};padding-right:46px">
                                    <button type="button" class="pf-eye" @click="show = ! show" :aria-label="show ? '{{ __('Sembunyikan') }}' : '{{ __('Papar') }}'">
                                        <span x-show="! show"><x-icon name="eye" class="h-[18px] w-[18px]" /></span>
                                        <span x-show="show" x-cloak><x-icon name="eye-off" class="h-[18px] w-[18px]" /></span>
                                    </button>
                                </div>
                                @error($pw['id'], 'updatePassword')<p style="{{ $err }}">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:34px">
                    <button type="submit" class="wl-primary" style="{{ $primary }}"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> {{ __('Tukar kata laluan') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
