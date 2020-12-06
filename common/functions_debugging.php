<?php

function dd($something) {
    echo "<pre><code>";
    var_dump($something);
    echo "</code></pre>";
    exit;
}
