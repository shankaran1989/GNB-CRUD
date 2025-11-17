<?php
class Sanitizer
{
    // sanitize a general string for storage (trim + strip control chars)
    public static function cleanString(string $v, int $maxLen = 1000): string
    {
        $v = trim($v);
        // remove ascii control characters
        $v = preg_replace('/[[:cntrl:]]/', '', $v);
        if ($maxLen > 0) $v = mb_substr($v, 0, $maxLen);
        return $v;
    }

    // sanitize for output: use htmlentities() in views instead of here
    public static function cleanNumber($v): int
    {
        return (int) $v;
    }
}
