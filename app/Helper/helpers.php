<?php
function hitunghari($tanggal_mulai, $tanggal_akhir) 
{
    $tanggal_1 = date_create($tanggal_mulai);
    $tanggal_2 = date_create($tanggal_akhir); // waktu sekarang
    $diff = date_diff($tanggal_1, $tanggal_2);
    return $diff->days + 1;
}

function buatkode($nomor_terakhir, $kunci, $jumlah_karakter = 0)
{
    /* mencari nomor baru dengan memecah nomor terakhir dan menambahkan 1
    string nomor baru dibawah ini harus dengan format XXX000000
    untuk penggunaan dalam format lain anda harus menyesuaikan sendiri */
    $nomor_baru = intval(substr($nomor_terakhir, strlen($kunci))) + 1;
    //    menambahkan nol didepan nomor baru sesuai panjang jumlah karakter
    $nomor_baru_plus_nol = str_pad($nomor_baru, $jumlah_karakter, "0", STR_PAD_LEFT);
    //    menyusun kunci dan nomor baru
    $kode = $kunci . $nomor_baru_plus_nol;
    return $kode;
}

function hitungjamkerja($jam_masuk, $jam_pulang)
    {
        $j_masuk = strtotime($jam_masuk);
        $j_pulang = strtotime($jam_pulang);
        $diff = $j_pulang - $j_masuk;
        if (empty($j_pulang)) {
            $jam = 0;
            $menit = 0;
        } else {
            $jam = floor($diff / (60 * 60));
            $m = $diff - $jam * (60 * 60);
            $menit = floor($m / 60);
        }
    
        return $jam . ":" . $menit;
    }