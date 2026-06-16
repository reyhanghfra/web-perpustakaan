<?php
/**
 * includes/whatsapp.php
 * Helper untuk mengirim pesan WhatsApp via Fonnte API
 * Daftar gratis di: https://fonnte.com
 */

define('FONNTE_TOKEN', 'NtKDz7qAqJCBmibNHoAZ');

function kirimWhatsApp($no_hp, $pesan) {
    // Format nomor: hilangkan 0 di depan, ganti dengan 62
    $no_hp = preg_replace('/^0/', '62', $no_hp);
    $no_hp = preg_replace('/[^0-9]/', '', $no_hp); // hapus karakter selain angka

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'target'  => $no_hp,
            'message' => $pesan,
        ],
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . FONNTE_TOKEN
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}