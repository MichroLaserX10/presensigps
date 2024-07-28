<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IzinsakitController extends Controller
{
    public function create() 
    {
        return view('sakit.create');
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin_dari = $request->tgl_izin_dari;
        $tgl_izin_sampai = $request->tgl_izin_sampai;
        $status = "s";
        $keterangan = $request->keterangan;

        $bulan = date("m", strtotime($tgl_izin_dari));
        $tahun = date("Y", strtotime($tgl_izin_dari));
        // dd($tahun);
        $thn = substr($tahun, 2, 2);
        $lastizin = DB::table('pengajuan_izin')
            ->whereRaw('MONTH(tgl_izin_dari)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_izin_dari)="' . $tahun . '"')
            ->orderBy('kode_izin', 'desc')
            ->first();
        $lastkodeizin = $lastizin != null ? $lastizin->kode_izin : "";
        $format = "IZ" . $bulan . $thn;
        $kode_izin = buatkode($lastkodeizin, $format, 3 );

        if ($request->hasFile('skd')) {
            $skd = $kode_izin . "." . $request->file('skd')->getClientOriginalExtension();
        } else {
            $skd = null;
        }
        $data = [
            'kode_izin' => $kode_izin,
            'nik' => $nik,
            'tgl_izin_dari' => $tgl_izin_dari,
            'tgl_izin_sampai' => $tgl_izin_sampai,
            'status' => $status,
            'keterangan' => $keterangan,
            'doc_skd' => $skd
        ];

        //Cek sudah absen / belum

        $cekpresensi = DB::table('presensi')
        ->whereBetween('tgl_presensi', [$tgl_izin_dari, $tgl_izin_sampai])
        ->where('nik', $nik);

        //Cek sudah melakukan pengajuan lain / belum
        $cekpengajuan = DB::table('pengajuan_izin')
        ->where('nik', $nik)
        ->whereRaw('"' . $tgl_izin_dari . '" BETWEEN tgl_izin_dari AND tgl_izin_sampai');

        $datapresensi = $cekpresensi->get();

        if ($cekpresensi->count() > 0) {
            $blacklistdate = "";
            foreach($datapresensi as $d) {
                $blacklistdate .= date('d-m-Y', strtotime($d->tgl_presensi)) . ",";
            }
            return redirect('/presensi/izin')->with(['error'=>'Tidak bisa melakukan pengajuan pada tanggal, ' . $blacklistdate . 
            ' tanggal sudah digunakan pada pengajuan lain / sudah melakukan presensi, silahkan ganti ke tanggal yang lain']);
        } else if ($cekpengajuan->count() > 0) {
            return redirect('/presensi/izin')->with(['error'=>'Tidak bisa melakukan pengajuan pada tanggal tersebut, 
            tanggal sudah digunakan sebelumnya']);
        } else {
            $simpan = DB::table('pengajuan_izin')->insert($data);
    
            if($simpan) {
                if ($request->hasFile('skd')) {
                    $skd = $kode_izin . "." . $request->file('skd')->getClientOriginalExtension();
                    $folderPath = "public/uploads/skd/";
                    $request->file('skd')->storeAs($folderPath, $skd);
                  }
                return redirect('/presensi/izin')->with(['success'=>'Data berhasil disimpan']);
            } else {
                return redirect('/presensi/izin')->with(['error'=>'Data gagal disimpan']);
            }
        }

    }

    public function edit($kode_izin) 
    {
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        return view('sakit.edit', compact('dataizin'));
    }

    public function update($kode_izin, Request $request)
    {
        $tgl_izin_dari = $request->tgl_izin_dari;
        $tgl_izin_sampai = $request->tgl_izin_sampai;
        $keterangan = $request->keterangan;

        if ($request->hasFile('skd')) {
            $skd = $kode_izin . "." . $request->file('skd')->getClientOriginalExtension();
        } else {
            $skd = null;
        }
        $data = [
            'tgl_izin_dari' => $tgl_izin_dari,
            'tgl_izin_sampai' => $tgl_izin_sampai,
            'keterangan' => $keterangan,
            'doc_skd' => $skd
        ];

        try {
            DB::table('pengajuan_izin')
            ->where('kode_izin', $kode_izin)
            ->update($data);
            if ($request->hasFile('skd')) {
                $skd = $kode_izin . "." . $request->file('skd')->getClientOriginalExtension();
                $folderPath = "public/uploads/skd/";
                $request->file('skd')->storeAs($folderPath, $skd);
            }
            return redirect('/presensi/izin')->with(['success'=>'Data berhasil di update']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error'=>'Data gagal di update']);
        }
    }
}
