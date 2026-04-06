<?php
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function today(): string
{
    return date('Y-m-d');
}

if (!function_exists('array_is_list')) {
    function array_is_list(array $array): bool
    {
        $expected = 0;
        foreach ($array as $key => $_) {
            // clé peut être int ou string numérique
            if ($key !== $expected) {
                if (!is_int($key)) {
                    // si la clé est une string numérique, la convertir
                    if (!ctype_digit((string)$key) || (int)$key !== $expected) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            $expected++;
        }
        return true;
    }
}
