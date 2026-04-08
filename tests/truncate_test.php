<?php

$name = "هذا نص طويل جداً لتجربة عملية القص التي طلبها المستخدم للتأكد من أنها تعمل بشكل صحيح مع الحروف العربية";
$truncated = mb_substr($name, 0, 46);

echo "Original length: " . mb_strlen($name) . "\n";
echo "Truncated length: " . mb_strlen($truncated) . "\n";
echo "Truncated text: " . $truncated . "\n";

if (mb_strlen($truncated) <= 46) {
    echo "SUCCESS: Truncation worked correctly.\n";
} else {
    echo "FAILURE: Truncation failed.\n";
}
