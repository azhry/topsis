<?php 
/**
* Main class which implements TOPSIS method.
*
* @package    Topsis
* @author     Azhary Arliansyah
* @version    1.0
*/

require_once(__DIR__ . '/Criteria.php');

class Topsis
{
	private $data;
	private $result;
	private $normalized_result;
	private $weighted_result;
	private $distance_result;
	private $normalizer;
	private $weights;

	public $criteria;

	public function __construct()
	{
		$this->criteria 	= new Criteria();
		$this->normalizer 	= [];
		$this->weights 		= [];

		foreach ($this->criteria->config as $key => $value)
		{
			$this->weights[$key] = $value['weight'];
		}
	}

	public function fit($data, $exclude_key = [])
	{
		$this->data = $data;
		$this->result = $this->criteria->fit($data, $exclude_key);
		
		foreach ($this->weights as $key => $value)
		{
			$this->normalizer[$key] = $this->euclidean_distance(array_column($this->result, $key));
		}
		return $this->result;
	}

	private function normalize()
	{
		$this->normalized_result = array_map(function($row) {
			$result = [];
			foreach ($row as $key => $value)
			{
				$result[$key] = $this->normalizer[$key] == 0 ? 0 : $value / $this->normalizer[$key];
			}
			return $result;
		}, $this->result);

		return $this->normalized_result;
	}

	public function weight()
	{
		$this->normalize();

		$this->weighted_result = array_map(function($row) {
			$result = [];
			foreach ($row as $key => $value)
			{
				$result[$key] = $value * $this->weights[$key];
			}
			return $result;
		}, $this->normalized_result);

		return $this->weighted_result;
	}

	public function solution_distance()
	{
		$solution_matrix = $this->solution_matrix($this->weighted_result);
		$this->distance_result = array_map(function($row) use ($solution_matrix) {
			$positive_sum = $negative_sum = 0;
			foreach ($row as $key => $value)
			{
				$positive_sum += pow($value - $solution_matrix['positive'][$key], 2);
				$negative_sum += pow($value - $solution_matrix['negative'][$key], 2);
			}
			return [
				'positive'	=> sqrt($positive_sum),
				'negative'	=> sqrt($negative_sum)
			];
		}, $this->weighted_result);

		return $this->distance_result;
	}

	public function result()
	{
		$result = array_map(function($row) {
			return $this->preference($row['positive'], $row['negative']);
		}, $this->distance_result);
		
		return $result;
	}

	public function rank($order = 'desc')
	{
		$result = $this->result();
		$data = $this->data;
		array_multisort($result, $order == 'desc' ? SORT_DESC : SORT_ASC, $data);
		return $data;
	}

	private function preference($x, $y)
	{
		return ($x + $y) == 0 ?  0 : $y / ($x + $y);
	}

	private function solution_matrix($matrix)
	{
		$solution = ['positive' => [], 'negative' => []];
		foreach ($this->weights as $key => $value)
		{
			$col = array_column($matrix, $key);
			$len_col = count($col);
			$solution['positive'][$key] = $len_col > 0 ? max($col) : 0;
			$solution['negative'][$key] = $len_col > 0 ? min($col) : 0;
		}

		return $solution;
	}

	private function euclidean_distance($vector)
	{
		$powered_vect = array_map(function($x) { return $x * $x; }, $vector);
		$sum = array_sum($powered_vect);
		return sqrt($sum);
	}

}

// $data = [
// 	[
// 		'ruko'							=> 'Jl Mangkunegara',
// 		'biaya_sewa'					=> 50000000,
// 		'luas_bangunan'					=> 48,
// 		'akses_menuju_lokasi'			=> 'Semuanya',
// 		'pusat_keramaian'				=> [ 'Pusat Belanja (Mall / Pasar)', 'Sekolah / Kampus' ],
// 		'zona_parkir'					=> 7,
// 		'jumlah_pesaing_serupa'			=> 7,
// 		'tingkat_konsumtif_masyarakat'	=> 'Sangat Tinggi',
// 		'lingkungan_lokasi_ruko'		=> 'Dekat Perumahan'
// 	],
// 	[
// 		'ruko'							=> 'Jl Angkatan 66',
// 		'biaya_sewa'					=> 50000000,
// 		'luas_bangunan'					=> 48,
// 		'akses_menuju_lokasi'			=> [ 'Kendaraan Mobil', 'Kendaraan Motor' ],
// 		'pusat_keramaian'				=> [ 'Pusat Belanja (Mall / Pasar)', 'Sekolah / Kampus' ],
// 		'zona_parkir'					=> 4,
// 		'jumlah_pesaing_serupa'			=> 7,
// 		'tingkat_konsumtif_masyarakat'	=> 'Sangat Tinggi',
// 		'lingkungan_lokasi_ruko'		=> 'Dekat Perumahan'
// 	]
// ];

// $topsis = new Topsis();