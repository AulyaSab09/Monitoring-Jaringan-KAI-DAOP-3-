<?php

namespace Database\Seeders;

use App\Models\Stasiun;
use Illuminate\Database\Seeder;

class StasiunSeeder extends Seeder
{
    public function run(): void
    {
        $stasiuns = [
            ['nama_stasiun' => 'Stasiun Cirebon', 'kode_stasiun' => 'CN'],
            ['nama_stasiun' => 'Stasiun Cirebon Prujakan', 'kode_stasiun' => 'CNP'],
            ['nama_stasiun' => 'Stasiun Arjawinangun', 'kode_stasiun' => 'AWN'],
            ['nama_stasiun' => 'Stasiun Bangoduwa', 'kode_stasiun' => 'BDW'],
            ['nama_stasiun' => 'Stasiun Bedilan', 'kode_stasiun' => 'BED'],
            ['nama_stasiun' => 'Stasiun Brebes', 'kode_stasiun' => 'BB'],
            ['nama_stasiun' => 'Stasiun Bulakamba', 'kode_stasiun' => 'BKA'],
            ['nama_stasiun' => 'Stasiun Ciledug', 'kode_stasiun' => 'CLD'],
            ['nama_stasiun' => 'Stasiun Cipunegara', 'kode_stasiun' => 'CRA'],
            ['nama_stasiun' => 'Stasiun Haurgeulis', 'kode_stasiun' => 'HGL'],
            ['nama_stasiun' => 'Stasiun Jatibarang', 'kode_stasiun' => 'JTB'],
            ['nama_stasiun' => 'Stasiun Kadokangabus', 'kode_stasiun' => 'KAB'],
            ['nama_stasiun' => 'Stasiun Karangsuwung', 'kode_stasiun' => 'KRW'],
            ['nama_stasiun' => 'Stasiun Ketanggungan', 'kode_stasiun' => 'KGG'],
            ['nama_stasiun' => 'Stasiun Kertasemaya', 'kode_stasiun' => 'KTM'],
            ['nama_stasiun' => 'Stasiun Larangan', 'kode_stasiun' => 'LR'],
            ['nama_stasiun' => 'Stasiun Losari', 'kode_stasiun' => 'LOS'],
            ['nama_stasiun' => 'Stasiun Luwung', 'kode_stasiun' => 'LWG'],
            ['nama_stasiun' => 'Stasiun Babakan', 'kode_stasiun' => 'BBK'],
            ['nama_stasiun' => 'Stasiun Pegaden Baru', 'kode_stasiun' => 'PGB'],
            ['nama_stasiun' => 'Stasiun Sindanglaut', 'kode_stasiun' => 'SDU'],
            ['nama_stasiun' => 'Stasiun Tanjung', 'kode_stasiun' => 'TGN'],
            ['nama_stasiun' => 'Stasiun Telagasari', 'kode_stasiun' => 'TLS'],
            ['nama_stasiun' => 'Stasiun Terisi', 'kode_stasiun' => 'TIS'],
            ['nama_stasiun' => 'Stasiun Waruduwur', 'kode_stasiun' => 'WDW'],
            ['nama_stasiun' => 'Stasiun Ketanggungan Barat', 'kode_stasiun' => 'KGB'],
            ['nama_stasiun' => 'Stasiun Mundu', 'kode_stasiun' => 'MNU'],
            ['nama_stasiun' => 'Stasiun Pabuaran', 'kode_stasiun' => 'PAB'],
            ['nama_stasiun' => 'Stasiun Pasirbungur', 'kode_stasiun' => 'PAS'],
            ['nama_stasiun' => 'Stasiun Pringkasap', 'kode_stasiun' => 'PRI'],
            ['nama_stasiun' => 'Stasiun Sindanghayu', 'kode_stasiun' => 'SIY'],
            ['nama_stasiun' => 'Stasiun Songgom', 'kode_stasiun' => 'SGM'],
        ];

        foreach ($stasiuns as $stasiun) {
            Stasiun::create($stasiun);
        }
    }
}