<?php
session_start();
unset($_SESSION['keranjang']);
echo "<h2>Terima kasih sudah memesan jamu!</h2><a href='index.php'>Kembali ke awal</a>";
