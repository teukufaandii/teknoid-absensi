<?php

if (isset($_POST['isClosed'])) {
    $_SESSION['isClosed'] = $_POST['isClosed'] === 'true';
}
?>
