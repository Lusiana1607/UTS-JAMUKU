<?php
session_start();
$db = new SQLite3('db/jamuku.db');
$bahan = $db->query("SELECT * FROM bahan ORDER BY jenis");

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}
?>
<h2>Pilih Bahan Jamu</h2>
<form method="POST" action="tambah.php">
    <label>Porsi: <input type="number" name="porsi" min="1" value="1" required></label><br><br>
    <?php while ($row = $bahan->fetchArray(SQLITE3_ASSOC)) : ?>
        <label>
            <input type="checkbox" name="bahan[]
