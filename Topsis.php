<?php 
/**
* Main class which implements TOPSIS method.
*
* @package    Topsis
* @author     Azhary Arliansyah
* @version    1.0
*/

require_once('./Criteria.php');

class Topsis
{
	public function __construct()
	{
		$criteria = new Criteria();
		$data = [
			[
				'ruko'							=> 'Jl Mangkunegara',
				'biaya_sewa'					=> 50000000,
				'luas_bangunan'					=> 48,
				'akses_menuju_lokasi'			=> 'Semuanya',
				'pusat_keramaian'				=> [ 'Pusat Belanja (Mall / Pasar)', 'Sekolah / Kampus' ],
				'zona_parkir'					=> 7,
				'jumlah_pesaing_serupa'			=> 7,
				'tingkat_konsumtif_masyarakat'	=> 'Sangat Tinggi',
				'lingkungan_lokasi_ruko'		=> 'Dekat Perumahan'
			],
			[
				'ruko'							=> 'Jl Angkatan 66',
				'biaya_sewa'					=> 50000000,
				'luas_bangunan'					=> 48,
				'akses_menuju_lokasi'			=> [ 'Kendaraan Mobil', 'Kendaraan Motor' ],
				'pusat_keramaian'				=> [ 'Pusat Belanja (Mall / Pasar)', 'Sekolah / Kampus' ],
				'zona_parkir'					=> 4,
				'jumlah_pesaing_serupa'			=> 7,
				'tingkat_konsumtif_masyarakat'	=> 'Sangat Tinggi',
				'lingkungan_lokasi_ruko'		=> 'Dekat Perumahan'
			]
		];

		var_dump($criteria->fit($data, [ 'ruko' ]));
	}

	public function fit($data)
	{

	}

}

$topsis = new Topsis();