<?php
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function today(): string
{
    return date('Y-m-d');
}