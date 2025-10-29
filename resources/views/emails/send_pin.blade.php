@component('mail::message')
    {{-- Header Email dengan Nama Aplikasi --}}
    @component('mail::header', ['url' => config('app.url')])
        {{ config('app.name') }}
    @endcomponent

    Verifikasi Reset Kata Sandi

    Halo,

    Kami menerima permintaan untuk mereset kata sandi akun Anda.

    Untuk melanjutkan proses reset, silakan gunakan Kode PIN verifikasi satu kali di bawah ini. Kode ini hanya berlaku
    selama 10 menit.

    {{-- Panel yang Didesain Ulang untuk PIN --}}
    @component('mail::panel')
        <p
            style="text-align: center; font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #059669; margin: 0; padding: 10px 0;">
            {{ $pin }}
        </p>
    @endcomponent

    Penting:
    Jika Anda tidak merasa meminta reset kata sandi, mohon abaikan email ini. Tidak ada perubahan yang akan dilakukan pada
    akun Anda.

    Terima kasih,




    Tim {{ config('app.name') }}

    {{-- Subcopy untuk Footer --}}
    @component('mail::subcopy')
        Jika Anda mengalami masalah, silakan hubungi tim dukungan kami.
    @endcomponent

@endcomponent
