<?php
    define('PROGRESS_BAR_ENABLED', true);
    require_once("progress_header.php");

?>
<html>
<head>
<title>Test barre de progression</title>
</head>
<body>

<p>Test barre de progression</p>

<?php

$n = 10;

initProgressBar($n);

for ($i=0; $i<=$n; $i++) {
    incrProgressBar($i);
    if ($i < $n) {
        sleep(1);
    }
}

endProgressBar();

?>

</body>
</html>

