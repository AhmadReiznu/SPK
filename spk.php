<?php
// Langkah 1: Identifikasi kriteria
$kriteria = ["Kriteria1", "Kriteria2", "Kriteria3"];
$bobot = [0.4, 0.3, 0.3]; // Bobot kriteria (total harus 1)

// Alternatif dan nilai matriks keputusan
$alternatif = [
    "Alternatif1" => [4, 7, 6],
    "Alternatif2" => [8, 5, 9],
    "Alternatif3" => [6, 8, 7],
];

// Langkah 2: Normalisasi Matriks Keputusan
$matriksNormalisasi = [];
foreach ($alternatif as $nama => $nilai) {
    foreach ($nilai as $i => $v) {
        $sum = sqrt(array_sum(array_map(fn($alt) => $alt[$i] ** 2, $alternatif)));
        $matriksNormalisasi[$nama][$i] = $v / $sum;
    }
}

// Langkah 3: Matriks Ternormalisasi Bobot
$matriksTernormalisasiBobot = [];
foreach ($matriksNormalisasi as $nama => $nilai) {
    foreach ($nilai as $i => $v) {
        $matriksTernormalisasiBobot[$nama][$i] = $v * $bobot[$i];
    }
}

// Langkah 4: Solusi Ideal Positif dan Negatif
$solusiPositif = [];
$solusiNegatif = [];
for ($i = 0; $i < count($kriteria); $i++) {
    $column = array_column($matriksTernormalisasiBobot, $i);
    $solusiPositif[$i] = max($column);
    $solusiNegatif[$i] = min($column);
}

// Langkah 5: Perhitungan Jarak
$jarakPositif = [];
$jarakNegatif = [];
foreach ($matriksTernormalisasiBobot as $nama => $nilai) {
    $jarakPositif[$nama] = sqrt(array_sum(array_map(fn($v, $i) => ($v - $solusiPositif[$i]) ** 2, $nilai, array_keys($nilai))));
    $jarakNegatif[$nama] = sqrt(array_sum(array_map(fn($v, $i) => ($v - $solusiNegatif[$i]) ** 2, $nilai, array_keys($nilai))));
}

// Langkah 6: Perhitungan Skor Kesesuaian
$preferensi = [];
foreach ($alternatif as $nama => $nilai) {
    $preferensi[$nama] = $jarakNegatif[$nama] / ($jarakPositif[$nama] + $jarakNegatif[$nama]);
}

// Langkah 7: Pemeringkatan
arsort($preferensi);

echo "Hasil Pemeringkatan:\n";
foreach ($preferensi as $nama => $nilai) {
    echo "$nama: " . round($nilai, 4) . "\n";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Metode TOPSIS</title>
</head>
<body>
    <h1>Sistem Pendukung Keputusan - Metode TOPSIS</h1>
    <form method="post">
        <h3>Input Kriteria dan Bobot</h3>
        <label>Kriteria 1 (Bobot): <input type="number" name="bobot[]" step="0.01" required></label><br>
        <label>Kriteria 2 (Bobot): <input type="number" name="bobot[]" step="0.01" required></label><br>
        <label>Kriteria 3 (Bobot): <input type="number" name="bobot[]" step="0.01" required></label><br>

        <h3>Input Alternatif dan Nilai Matriks Keputusan</h3>
        <div id="alternatif-section">
            <div>
                <label>Alternatif 1: </label>
                <input type="text" name="alternatif[]" placeholder="Nama Alternatif" required>
                <input type="number" name="nilai[0][]" step="0.01" placeholder="Kriteria 1" required>
                <input type="number" name="nilai[0][]" step="0.01" placeholder="Kriteria 2" required>
                <input type="number" name="nilai[0][]" step="0.01" placeholder="Kriteria 3" required>
            </div>
        </div>
        <button type="button" onclick="addAlternatif()">Tambah Alternatif</button>
        <br><br>
        <button type="submit">Hitung</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
        <?php
        // Ambil input data
        $bobot = array_map('floatval', $_POST['bobot']);
        $alternatif = $_POST['alternatif'];
        $nilaiAlternatif = $_POST['nilai'];

        // Langkah 2: Normalisasi Matriks Keputusan
        $matriksNormalisasi = [];
        foreach ($nilaiAlternatif as $key => $nilai) {
            foreach ($nilai as $i => $v) {
                $sum = sqrt(array_sum(array_map(fn($alt) => $alt[$i] ** 2, $nilaiAlternatif)));
                $matriksNormalisasi[$key][$i] = $v / $sum;
            }
        }

        // Langkah 3: Matriks Ternormalisasi Bobot
        $matriksTernormalisasiBobot = [];
        foreach ($matriksNormalisasi as $key => $nilai) {
            foreach ($nilai as $i => $v) {
                $matriksTernormalisasiBobot[$key][$i] = $v * $bobot[$i];
            }
        }

        // Langkah 4: Solusi Ideal Positif dan Negatif
        $solusiPositif = [];
        $solusiNegatif = [];
        for ($i = 0; $i < count($bobot); $i++) {
            $column = array_column($matriksTernormalisasiBobot, $i);
            $solusiPositif[$i] = max($column);
            $solusiNegatif[$i] = min($column);
        }

        // Langkah 5: Perhitungan Jarak
        $jarakPositif = [];
        $jarakNegatif = [];
        foreach ($matriksTernormalisasiBobot as $key => $nilai) {
            $jarakPositif[$key] = sqrt(array_sum(array_map(fn($v, $i) => ($v - $solusiPositif[$i]) ** 2, $nilai, array_keys($nilai))));
            $jarakNegatif[$key] = sqrt(array_sum(array_map(fn($v, $i) => ($v - $solusiNegatif[$i]) ** 2, $nilai, array_keys($nilai))));
        }

        // Langkah 6: Perhitungan Skor Kesesuaian
        $preferensi = [];
        foreach ($alternatif as $key => $nama) {
            $preferensi[$nama] = $jarakNegatif[$key] / ($jarakPositif[$key] + $jarakNegatif[$key]);
        }

        // Langkah 7: Pemeringkatan
        arsort($preferensi);
        ?>
        <h3>Hasil Pemeringkatan</h3>
        <table border="1">
            <tr>
                <th>Alternatif</th>
                <th>Nilai Preferensi</th>
            </tr>
            <?php foreach ($preferensi as $nama => $nilai) : ?>
                <tr>
                    <td><?= htmlspecialchars($nama) ?></td>
                    <td><?= round($nilai, 4) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <script>
        function addAlternatif() {
            const section = document.getElementById('alternatif-section');
            const index = section.children.length;

            const div = document.createElement('div');
            div.innerHTML = `
                <label>Alternatif ${index + 1}: </label>
                <input type="text" name="alternatif[]" placeholder="Nama Alternatif" required>
                <input type="number" name="nilai[${index}][]" step="0.01" placeholder="Kriteria 1" required>
                <input type="number" name="nilai[${index}][]" step="0.01" placeholder="Kriteria 2" required>
                <input type="number" name="nilai[${index}][]" step="0.01" placeholder="Kriteria 3" required>
            `;
            section.appendChild(div);
        }
    </script>
</body>
</html>
