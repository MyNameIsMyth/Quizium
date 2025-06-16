<?php
session_start();
session_destroy();
header('Location: vhod.php');
exit();
?> 