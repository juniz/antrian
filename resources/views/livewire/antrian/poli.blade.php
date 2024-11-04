<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

new class extends Component {
    public $kd_dokter;
    public $kd_poli;
    public $antrianPanggil;
    public $daftarAntrian;
    public function mount($kd_dokter, $kd_poli)
    {
        $this->kd_dokter = Crypt::decryptString($kd_dokter);
        $this->kd_poli = Crypt::decryptString($kd_poli);
        $this->getPanggilan();
    }

    public function getDokter()
    {
        $data = DB::table('dokter')
            ->where('kd_dokter', $this->kd_dokter)
            ->first();
        return $data?->nm_dokter;
    }

    public function getPoli()
    {
        $data = DB::table('poliklinik')
            ->where('kd_poli', $this->kd_poli)
            ->first();
        return $data?->nm_poli;
    }

    public function jmlSudahDilayani()
    {
        return DB::table('reg_periksa')
            ->where('kd_dokter', $this->kd_dokter)
            ->where('kd_poli', $this->kd_poli)
            ->where('stts', 'Sudah')
            ->where('tgl_registrasi', Carbon::now()->format('Y-m-d'))
            ->count();
    }

    public function jmlBelumDilayani()
    {
        return DB::table('reg_periksa')
            ->where('kd_dokter', $this->kd_dokter)
            ->where('kd_poli', $this->kd_poli)
            ->where('stts', '<>', 'Batal')
            ->where('tgl_registrasi', Carbon::now()->format('Y-m-d'))
            ->count();
    }

    public function getPanggilan()
    {
        // dd($this->kd_poli);
        $data = DB::table('antripoli')
            ->where('kd_dokter', $this->kd_dokter)
            ->where('kd_poli', $this->kd_poli)
            ->where('status', '0')
            ->first();
            // dd($data);
        $this->antrianPanggil = $data;
    }

    public function getNoAntrian($no_rawat)
    {
        $data = DB::table('reg_periksa')
            ->where('no_rawat', $no_rawat)
            ->first();
        return $data?->no_reg;
    }

    public function getAntrian()
    {
        $data = DB::table('reg_periksa')
            ->where('kd_dokter', $this->kd_dokter)
            ->where('kd_poli', $this->kd_poli)
            ->where('stts', 'Belum')
            ->where('tgl_registrasi', Carbon::now()->format('Y-m-d'))
            ->get();
        // dd($data);
        return $data;
    }

    public function getListAntrian()
    {
        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.kd_dokter', $this->kd_dokter)
            ->where('reg_periksa.kd_poli', $this->kd_poli)
            ->where('stts', 'Belum')
            ->where('tgl_registrasi', Carbon::now()->format('Y-m-d'))
            ->select('reg_periksa.no_rawat', 'reg_periksa.no_reg', 'pasien.nm_pasien')
            ->get();
        // dd($this->kd_dokter, $this->kd_poli, $data);
        return $data;
    }

    public function getDataPasien($no_rm)
    {
        $reg = DB::table('reg_periksa')
            ->where('no_rawat', $no_rm)
            ->first();
        // dd($reg);
        $pasien = DB::table('pasien')
            ->where('no_rkm_medis', $reg->no_rkm_medis)
            ->first();
        // dd($pasie);
        return $pasien;
    }

    public function with()
    {
        return [
            'jmlSudahDilayani' => $this->jmlSudahDilayani(),
            'jmlBelumDilayani' => $this->jmlBelumDilayani(),
            'daftarAntrianPoli' => $this->getListAntrian(),
            'dokter' => $this->getDokter(),
            'poli' => $this->getPoli(),
        ];
    }
}; ?>

<div wire:poll.1000ms class="min-h-screen">
    <h1 class="text-5xl text-center font-bold my-2">{{ $dokter }}</h1>
    <h2 class="text-3xl text-center font-bold my-2">{{ $poli }}</h2>
    <h3 class="text-center font-bold text-xl mb-4">{{ now() }}</h3>
    @if($antrianPanggil)
    <div class="grid grid-cols-4 gap-4 mb-4">
        <div class="col-span-1">
            <x-card class="text-center">
                <h2 class="text-2xl font-bold">Panggilan Antrian</h2>
                <h3 class="text-7xl font-bold my-10">{{ $this->getNoAntrian($antrianPanggil->no_rawat) }}</h3>
            </x-card>
        </div>
        <div class="col-span-3">
            <x-card class="h-full">
                <x-list-item :item='$antrianPanggil' no-separator no-hover>
                    <x-slot:value>
                        <h1 class="text-center text-6xl">{{ $this->getDataPasien($antrianPanggil->no_rawat)->nm_pasien }}</h1>
                    </x-slot:value>
                </x-list-item>
            </x-card>
        </div>
    </div>
    @else
        <x-card class="text-center text-white bg-red-500 mb-4">
            <h2 class="text-2xl font-bold">Tidak Ada Panggilan</h2>
        </x-card>
    @endif
    <div class="grid grid-cols-4 gap-4 min-h-max">
        <div class="flex flex-col gap-4 col-span-1">
            <x-card class="text-center text-white bg-green-500">
                <h2 class="text-2xl font-bold">Terlayani</h2>
                <h3 class="text-7xl font-bold my-10">{{ $jmlSudahDilayani }}</h3>
            </x-card>
            <x-card class="text-center text-white bg-red-500">
                <h2 class="text-2xl font-bold">Total Antrian</h2>
                <h3 class="text-7xl font-bold my-10">{{ $jmlBelumDilayani }}</h3>
            </x-card>
        </div>
        <div class="flex flex-col col-span-3">
            <x-card class="overflow-hidden">
                <div class="animate-marquee-vertical" style="--marquee-duration: 20000ms;">
                @forelse($daftarAntrianPoli as $item) 
                    <x-list-item :item='$item' no-hover>
                        <x-slot:avatar>
                            <span class="text-3xl font-semibold">{{ $item->no_reg }}</span>
                        </x-slot:avatar>
                        <x-slot:value>
                            <h2 class="text-3xl">{{ $item->nm_pasien }}</h2>
                        </x-slot:value>
                    </x-list-item>      
                @empty
                    <x-card class="text-center h-full">
                        <div class="align-middle my-auto">
                            <h2 class="text-7xl font-bold">Antrian Selesai</h2>
                        </div>
                    </x-card>
                @endforelse
                 </div>
            </x-card>
        </div>
    </div>
</div>
