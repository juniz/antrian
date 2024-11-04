<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

new class extends Component {
    public function getAntrian()
    {
        $hari = Carbon::now()->format('l');
        switch($hari) {
            case 'Monday':
                $hari = 'SENIN';
                break;
            case 'Tuesday':
                $hari = 'SELASA';
                break;
            case 'Wednesday':
                $hari = 'RABU';
                break;
            case 'Thursday':
                $hari = 'KAMIS';
                break;
            case 'Friday':
                $hari = 'JUMAT';
                break;
            case 'Saturday':
                $hari = 'SABTU';
                break;
            case 'Sunday':
                $hari = 'AKHAD';
                break;
        }
        $data = DB::table('jadwal')
            ->join('dokter', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'jadwal.kd_poli', '=', 'poliklinik.kd_poli')
            ->select('dokter.nm_dokter','poliklinik.nm_poli','jadwal.jam_mulai','jadwal.jam_selesai','poliklinik.kd_poli', 
            'dokter.kd_dokter')
            ->where('jadwal.hari_kerja', $hari)
            ->get();
        // dd($data);
        return $data;
    }

    public function encrypData($data)
    {
        // dd($data);
        return Crypt::encryptString($data);
    }

    public function with()
    {
        return [
            'data' => $this->getAntrian()
        ];
    }
}; ?>

<div>
    <x-card>
        @forelse($data as $item)
        <x-list-item :item="$item" link='/antrian/poli/{{$this->encrypData($item->kd_dokter)}}/{{$this->encrypData($item->kd_poli)}}'>
            <x-slot:value>
                <span class="text-2xl">{{ $item->nm_dokter }}</span>
            </x-slot:value>
            <x-slot:sub-value>
                {{ $item->nm_poli }}
            </x-slot:sub-value>
            <x-slot:actions>
                <x-card class="bg-green-500 text-white text-center">
                    <div>
                        <span class="font-bold text-md">Jam Mulai</span>
                    </div>
                    <span>
                        {{ $item->jam_mulai }}
                    </span>
                </x-card>
                <x-card class="bg-red-500 text-white text-center">
                    <div>
                        <span class="font-bold">Jam Selesai</span>
                    </div>
                    <span>
                        {{ $item->jam_selesai }}
                    </span>
                </x-card>
            </x-slot:actions>
        </x-list-item>
        @empty
        <div class="text-center">
            <p class="text-lg">Tidak ada data</p>
        </div>
        @endforelse
    </x-card>
</div>
