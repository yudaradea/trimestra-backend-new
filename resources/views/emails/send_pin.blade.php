<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Password</title>
    <style>
        /* Meskipun kita inline, beberapa style dasar di head membantu klien email tertentu */
        body,
        table,
        td,
        p {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4; width: 100% !important;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <!-- Kontainer Utama -->
                <table width="600" border="0" cellspacing="0" cellpadding="0"
                    style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

                    <!-- Header Aplikasi -->
                    <tr>
                        <td align="center" style="padding: 40px 40px 20px 40px; border-bottom: 1px solid #eeeeee;">
                            <h1 style="color: #333333; margin: 0; font-size: 28px; font-weight: 700;">
                                {{ config('app.name') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Konten Body -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; color: #555555; font-size: 16px; line-height: 1.6em;">
                            <p style="margin: 0 0 20px 0;">Halo,</p>
                            <p style="margin: 0 0 20px 0;">Kami menerima permintaan untuk mereset kata sandi akun Anda.
                            </p>
                            <p style="margin: 0 0 30px 0;">Untuk melanjutkan proses reset, silakan gunakan Kode PIN
                                verifikasi satu kali di bawah ini. Kode ini hanya berlaku selama <strong>10
                                    menit</strong>.</p>

                            <!-- Kotak PIN -->
                            <table border="0" cellspacing="0" cellpadding="0" width="100%"
                                style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="background-color: #F0FDF4; border: 1px dashed #10B981; border-radius: 8px; padding: 25px 20px;">
                                            <p
                                                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 42px; font-weight: bold; color: #059669; margin: 0; letter-spacing: 8px; line-height: 1.2;">
                                                {{ $pin }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-weight: bold; margin-top: 30px; margin-bottom: 10px;">Penting:</p>
                            <p style="margin: 0; font-size: 14px; color: #777777;">Jika Anda tidak merasa meminta reset
                                kata sandi, mohon abaikan email ini. Tidak ada perubahan yang akan dilakukan pada akun
                                Anda.</p>
                        </td>
                    </tr>

                    <!-- Penutup -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; color: #555555; font-size: 16px; line-height: 1.6em;">
                            Terima kasih,<br>
                            Tim {{ config('app.name') }}
                        </td>
                    </tr>
                </table>

                <!-- Footer Email -->
                <table width="600" border="0" cellspacing="0" cellpadding="0"
                    style="width: 100%; max-width: 600px; margin: 0 auto;">
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; color: #999999; font-size: 12px;">
                            <p style="margin: 0 0 10px 0;">Jika Anda mengalami masalah, silakan hubungi tim dukungan
                                kami.</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. Hak cipta
                                dilindungi undang-undang.</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>


</body>

</html>
