<?php
session_start();
session_destroy();

header("Location: ../produk/index.php");
exit;