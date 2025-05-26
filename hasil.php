<?php
session_start();
$db = new SQLite3('jamuku.db');

// Inisialisasi tabel dan data awal jika belum ada
$db->exec("
CREATE TABLE IF NOT EXISTS bahan (
  id INTEGER PRIMARY KEY,
  nama TEXT NOT NULL,
  deskripsi TEXT NOT NULL,
  harga INTEGER NOT NULL,
  jenis TEXT NOT NULL
)");

if ($db->querySingle("SELECT COUNT(*) FROM bahan") == 0) {
    $db->exec("
    INSERT INTO bahan (nama, jenis, deskripsi, harga) VALUES
    ('Kunyit','Bahan utama','Antioksidan, antiradang, meningkatkan sistem imun, meredakan nyeri haid',1500),
    ('Jahe','Bahan utama','Menghangatkan tubuh, meredakan nyeri otot, meningkatkan imun, mencegah mual',1200),
    ('Temulawak','Bahan utama','Melindungi hati, antiinflamasi, meningkatkan nafsu makan',2000),
    ('Kencur','Bahan utama','Meredakan nyeri, antibakteri, melancarkan pencernaan, meningkatkan nafsu makan',1500),
    ('Serai','Bahan utama','Meredakan demam, melancarkan pencernaan, mengurangi stres',800),
    ('Daun Pepaya','Bahan utama','Meningkatkan nafsu makan, membantu pencernaan dengan enzim papain',600),
    ('Mengkudu','Bahan utama','Mengelola tekanan darah, pereda nyeri, memperbaiki pencernaan',2100),
    ('Daun Beluntas','Bahan utama','Antibakteri, detoksifikasi, menghilangkan bau badan',800),
    ('Asam Jawa','Bahan utama','Menurunkan suhu badan, menyegarkan, mendukung kesehatan hati',1000),
    ('Cengkeh','Rempah tambahan','Mengatasi sakit kepala, antibakteri',800),
    ('Kayu Manis','Rempah tambahan','Menurunkan gula darah, meningkatkan metabolisme',800),
    ('Daun Pandan','Rempah tambahan','Memberi aroma harum, membantu pencernaan',800),
    ('Kapulaga','Rempah tambahan','Melancarkan peredaran darah, meningkatkan nafsu makan',500),
    ('Bunga Lawang','Rempah tambahan','Memberi aroma khas, membantu pencernaan',500),
    ('Daun Sirih','Rempah tambahan','Antiseptik, kesehatan mulut dan organ kewanitaan',500),
    ('Gula Merah','Pemanis','Menambah rasa manis alami, sumber energi',1000),
    ('Madu','Pemanis','Meningkatkan imun, mempercepat penyembuhan, menambah rasa manis',2000),
    ('Tebu','Pemanis','Menambah rasa manis alami, mempercepat penyembuhan',1000),
    ('Lemon','Bahan tambahan','Menambah rasa segar, sumber vitamin C',1200),
    ('Delima','Bahan tambahan','Antioksidan, meningkatkan stamina',3400),
    ('Soda','Bahan tambahan','Memberi sensasi segar dan rasa modern pada jamu',1000),
    ('Mint','Bahan tambahan','Memberi sensasi segar, antibakteri',800),
    ('Stevia','Pemanis','Menambah rasa manis alami, sumber energi',2000)
    ");
}

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah ke keranjang
if (isset($_POST['tambah']) && isset($_POST['bahan'])) {
    $porsi = intval($_POST['porsi']);
    foreach ($_POST['bahan'] as $id) {
        $id = intval($id);
        if (!isset($_SESSION['keranjang'][$id])) {
            $bahan = $db->querySingle("SELECT * FROM bahan WHERE id = $id", true);
            $_SESSION['keranjang'][$id] = ['data' => $bahan, 'porsi' => $porsi];
        } else {
            $_SESSION['keranjang'][$id]['porsi'] += $porsi;
        }
    }
    header("Location: ?page=keranjang");
    exit;
}

// Hapus bahan
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    unset($_SESSION['keranjang'][$id]);
    header("Location: ?page=keranjang");
    exit;
}

// Selesai transaksi
if (isset($_GET['selesai'])) {
    $_SESSION['keranjang'] = [];
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Jamuku</title><style>
        body{text-align:center;font-family:sans-serif;margin:50px}
        a{color:blue;text-decoration:none}
    </style></head><body>";
    echo "<h2>Terima kasih sudah memesan jamu!</h2><a href='jamuku.php'>← Kembali ke Awal</a>";
    echo "</body></html>";
    exit;
}

// Mulai HTML
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Jamuku</title><style>
body {
  font-family: Arial, sans-serif;
  margin: 40px auto;
  padding: 20px;
  max-width: 800px;
  background: #fffef9;
  color: #333;
  border: 1px solid #ddd;
  border-radius: 10px;
}
h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 20px;
}
form {
  margin-bottom: 30px;
}
label {
  display: block;
  margin: 10px 0;
}
input[type='number'] {
  width: 60px;
  padding: 5px;
  margin-left: 10px;
}
button {
  padding: 10px 20px;
  background-color: #27ae60;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}
button:hover {
  background-color: #219150;
}
a {
  text-decoration: none;
  color: #2980b9;
  margin-right: 10px;
}
a:hover {
  text-decoration: underline;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  margin: 10px 0;
  padding: 8px;
  border-bottom: 1px solid #ddd;
}
</style></head><body>";

// Halaman Keranjang
if (isset($_GET['page']) && $_GET['page'] == 'keranjang') {
    $keranjang = $_SESSION['keranjang'];
    $total = 0;
    echo "<h2>Keranjang Belanja</h2>";
    if (empty($keranjang)) {
        echo "<p>Keranjang kosong</p>";
    } else {
        echo "<ul>";
        foreach ($keranjang as $id => $item) {
            $subtotal = $item['data']['harga'] * $item['porsi'];
            $total += $subtotal;
            echo "<li>{$item['data']['nama']} - {$item['porsi']} porsi ({$item['data']['harga']}/porsi) = <strong>$subtotal</strong> 
                  <a href='?hapus=$id'>[Hapus]</a></li>";
        }
        echo "</ul>";
        echo "<p><strong>Total Harga: $total</strong></p>";
    }
    echo "<a href='jamuku.php'>← Tambah lagi</a> | <a href='?selesai=1'>Bayar</a>";
    echo "</body></html>";
    exit;
}

// Halaman Utama
echo "<h2>Racik Jamu Anda</h2>";
echo "<form method='POST'>
    <label>Porsi: <input type='number' name='porsi' min='1' value='1' required></label><br><br>";
$bahan = $db->query("SELECT * FROM bahan ORDER BY jenis, nama");
while ($row = $bahan->fetchArray(SQLITE3_ASSOC)) {
    echo "<label><input type='checkbox' name='bahan[]' value='{$row['id']}'> 
          {$row['nama']} ({$row['harga']})</label>";
}
echo "<br><button type='submit' name='tambah'>Tambah ke Keranjang</button></form>";
echo "<br><a href='?page=keranjang'>Lihat Keranjang</a>";
echo "</body></html>";
?>
