<?php

namespace App\Services;

class MonteCarloPrediction
{
  public function predict(array $data, int $a, int $c, int $m, int $x0, int $iterations = 5)
  {
    // Total jumlah
    $total = array_sum($data);

    // Probabilitas
    $probabilities = [];
    if ($total == 0) {
      // Jika total adalah 0, set semua probabilitas menjadi 0
      foreach ($data as $year => $value) {
        $probabilities[$year] = 0;
      }
    } else {
      // Jika total tidak 0, hitung probabilitas secara normal
      foreach ($data as $year => $value) {
        $probabilities[$year] = $value / $total;
      }
    }

    // Probabilitas kumulatif
    $cumulative = [];
    $sum = 0;
    foreach ($probabilities as $year => $prob) {
      $sum += $prob;
      $cumulative[$year] = $sum;
    }

    // Interval
    $intervals = [];
    $last = 0;
    foreach ($cumulative as $year => $cum) {
      $intervals[$year] = [$last, round($cum * 1000)];
      $last = round($cum * 1000) + 1;
    }

    // Generate angka acak
    $results = [];
    for ($i = 0; $i < $iterations; $i++) {
      $x0 = ($a * $x0 + $c) % $m;
      $formattedValue = $x0 < 100 ? $x0 * 10 : $x0;
      $random = $formattedValue;

      foreach ($intervals as $year => $range) {
        if ($random >= $range[0] && $random <= $range[1]) {
          $results[] = $data[$year];
          break;
        }
      }
    }
    // Rata-rata hasil simulasi
    return round(array_sum($results) / count($results));
  }
}
