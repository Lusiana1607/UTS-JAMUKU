<?php
session_start();
$keranjang = $_SESSION['keranjang'] ?? [];
$total = 0;
?>
<h2>Keranjang Belanja</h2>
<?php if (empty($keranjang)) : ?>
    <p>Keranjang kosong</p>
<?php else: ?>
    <ul>
        <?php foreach ($keranjang as $id => $item) :
            $subtotal = $item['data']['harga'] * $item['porsi'];
            $total += $subtotal;
        ?>
        <li>
            <?= $item['data']['nama'] ?> - <?= $item['porsi'] ?> porsi (<?= $item['data']['harga'] ?>) = <?= $subtotal ?>
            <a href="hapus.php?id=<?= $id ?>">Hapus</a>
        </li>
        <?php endforeach; ?>
    </ul>
    <p><strong>Total Harga: <?= $total ?></strong></p>
    <a href="index.php">Tambah lagi</a> | 
    <a href="selesai.php">Bayar</a>
<?php endif; ?>
