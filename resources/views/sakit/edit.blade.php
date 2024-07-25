@extends('layouts.presensi')
@section('header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
<style>
    .datepicker-modal {
        max-height: 465px !important;
    }

    .datepicker-date-display {
        background-color: #0f3a7e !important;
    }
    
    #keterangan {
        height: 5rem !important;
    }

</style>
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Izin Sakit</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection
@section('content')
<div class="row" style="margin-top:70px">
    <div class="col">
        <form method="POST" action="/izinsakit/{{ $dataizin->kode_izin }}/update" id="frmIzin" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <input type="text" id="tgl_izin_dari" value="{{ $dataizin->tgl_izin_dari }}" autocomplete="off" name="tgl_izin_dari" 
                class="form-control datepicker" placeholder="Dari">
            </div>
            <div class="form-group">
                <input type="text" id="tgl_izin_sampai" value="{{ $dataizin->tgl_izin_sampai }}" autocomplete="off" 
                name="tgl_izin_sampai" class="form-control datepicker" placeholder="Sampai">
            </div>
            <div class="form-group">
                <input type="text" id="jml_hari" name="jml_hari" class="form-control" autocomplete="off" placeholder="Jumlah Hari"
                readonly>
            </div>
            @if ($dataizin->doc_skd != null)
                <div class="row">
                    <div class="col-12">
                        @php
                        $docskd = Storage::url('/uploads/skd/' . $dataizin->doc_skd );
                        @endphp
                        <img src="{{ url($docskd) }}" alt="" width="100px">
                    </div>
                </div>
            @endif
            <div class="custom-file-upload" id="fileUpload1" style="height: 100px !important">
                <input type="file" name="skd" id="fileuploadInput" accept=".png, .jpg, .jpeg">
                <label for="fileuploadInput">
                    <span>
                        <strong>
                            <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" 
                aria-label="cloud upload outline"></ion-icon>
                            <i>Tap to Upload SKD</i>
                        </strong>
                    </span>
                </label>
            </div>

            <div class="form-group">
                <input type="text" id="keterangan" value="{{ $dataizin->keterangan }}" name="keterangan" class="form-control" 
                autocomplete="off" placeholder="Keterangan">
            </div>
            <div class="form-group">
                <button class="btn btn-primary w-100">Kirim</button>
            </div>
        </form>
    </div>
</div>    
@endsection
@push('myscript')
<script>
    var currYear = (new Date()).getFullYear();

    $(document).ready(function() {
        $(".datepicker").datepicker({
            format: "yyyy-mm-dd"    
        });

        function loadjumlahhari() {
            var dari = $("#tgl_izin_dari").val()
            var sampai = $("#tgl_izin_sampai").val()
            var date1 = new Date(dari);
            var date2 = new Date(sampai);

            // To calculate the time difference of two dates
            var Difference_In_Time = date2.getTime() - date1.getTime();

            // To calculate the no. of days between two dates
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            if (dari=="" || sampai=="") {
                var jmlhari = 0;
            } else {
                var jmlhari = Difference_In_Days + 1;
            }
            // To display the final no. of days (result)
            $("#jml_hari").val(jmlhari + " Hari");
        }

        loadjumlahhari();

        $("#tgl_izin_dari,#tgl_izin_sampai").change(function(e) {
            loadjumlahhari();
        });

        // $("#tgl_izin").change(function(e) {
        //     var tgl_izin = $(this).val();
        //     $.ajax ({
        //         type: 'POST',
        //         url: '/presensi/cekpengajuanizin',
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //             tgl_izin: tgl_izin
        //         },
        //         cache: false,
        //         success: function(respond) {
        //             if (respond == 1) {
        //                 Swal.fire({
        //                     title: 'Oops!',
        //                     text: 'Anda sudah mengajukan izin pada tanggal tersebut',
        //                     icon: 'warning'
        //                 }).then((result) => {
        //                     $("#tgl_izin").val("");
        //                 });
        //             }
        //         }
        //     });
        // });

        $("#frmIzin").submit(function() {
            var tgl_izin_dari = $("#tgl_izin_dari").val();
            var tgl_izin_sampai = $("#tgl_izin_sampai").val();
            var keterangan = $("#keterangan").val();
            if (tgl_izin_dari == "" || tgl_izin_sampai == "") {
                Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal Wajib diisi',
                        icon: 'warning'
                });
                return false;
            } else if (keterangan == "") {
                Swal.fire({
                        title: 'Oops!',
                        text: 'Keterangan Wajib diisi',
                        icon: 'warning'
                });
                return false;
            }
        });
    });

</script>
@endpush