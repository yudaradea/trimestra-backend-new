@component('mail::message')
    # Verifikasi Reset Kata Sandi

    Halo,

    Kami menerima permintaan untuk mereset kata sandi akun **TRIMESTRA** Anda.

    Untuk melanjutkan proses reset dan memverifikasi bahwa ini memang Anda, silakan gunakan **Kode PIN** verifikasi satu
    kali di bawah ini:

    @component('mail::panel')
        <div
            style="text-align: center; font-size: 32px; font-weight: 700; letter-spacing: 5px; color: #059669; padding: 15px 0; border: 2px dashed #D1FAE5; border-radius: 6px;">
            {{ $pin }}
        </div>
    @endcomponent

    **Penting:** Kode PIN ini hanya berlaku selama **10 menit** sejak email ini dikirim. Setelah waktu tersebut, Anda harus
    meminta PIN baru.

    @component('mail::subcopy')
        Jika Anda tidak pernah meminta reset kata sandi, mohon abaikan email ini. Akun Anda tetap aman dan kata sandi Anda tidak
        akan berubah.
    @endcomponent

    Terima kasih atas kerja sama Anda,<br>
    Tim Dukungan {{ config('app.name') }}
@endcomponent
