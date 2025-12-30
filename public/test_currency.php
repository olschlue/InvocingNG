<?php
require_once '../config/config.php';

echo "CURRENCY_SYMBOL value: " . CURRENCY_SYMBOL . "<br>";
echo "Numeric value: " . ord(CURRENCY_SYMBOL) . "<br>";
echo "As hex: ";
for ($i = 0; $i < strlen(CURRENCY_SYMBOL); $i++) {
    echo dechex(ord(CURRENCY_SYMBOL[$i])) . " ";
}
