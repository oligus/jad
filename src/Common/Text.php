<?php declare(strict_types=1);

namespace Jad\Common;

/**
 * Class Text
 * @package Jad\Common
 */
class Text
{
    public static function kebabify(string $str): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('-', $ret);
    }

    public static function deKebabify(string $str): string
    {
        /**
         * @param array<string> $result
         */
        return preg_replace_callback('!-[a-z]!', function (array $result): string {
            $char = ltrim($result[0], '-');
            return strtoupper($char);
        }, $str);
    }
}
