<?php

use Livewire\Volt\Volt;

// Volt::route('/', 'users.index');
Volt::route('/', 'antrian.index');
Volt::route('/antrian/poli/{kd_dokter}/{kd_poli}', 'antrian.poli');
