<?php
/**
 * Copyright (C) 2016 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\Geo\String;

use Doctrine\Common\Lexer\AbstractLexer;

/**
 * Tokenize geographic coordinates
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class Lexer extends AbstractLexer
{
    public const T_NONE         = 1;
    public const T_INTEGER      = 2;
    public const T_FLOAT        = 4;
    public const T_CARDINAL_LAT = 5;
    public const T_CARDINAL_LON = 6;
    public const T_COMMA        = 7;
    public const T_PLUS         = 8;
    public const T_MINUS        = 9;
    public const T_PERIOD       = 10;
    public const T_COLON        = 11;
    public const T_APOSTROPHE   = 12;
    public const T_QUOTE        = 13;
    public const T_DEGREE       = 14;

    /**
     * @param string &$value
     *
     * @return int
     */
    protected function getType(&$value): int
    {
        if (is_numeric($value)) {
            $value += 0;

            if (is_int($value)) {
                return self::T_INTEGER;
            }

            return self::T_FLOAT;
        }

        return match ($value) {
            ':' => self::T_COLON,
            '\'', "\xe2\x80\xb2" => self::T_APOSTROPHE,
            '"', "\xe2\x80\xb3" => self::T_QUOTE,
            ',' => self::T_COMMA,
            '-' => self::T_MINUS,
            '+' => self::T_PLUS,
            '°' => self::T_DEGREE,
            'N', 'S' => self::T_CARDINAL_LAT,
            'E', 'W' => self::T_CARDINAL_LON,
            default => self::T_NONE,
        };
    }

    /**
     * @return string[]
     */
    protected function getCatchablePatterns(): array
    {
        return ['[nesw\'",]', '(?:[0-9]+)(?:[\.][0-9]+)?(?:e[+-]?[0-9]+)?'];
    }

    /**
     * @return string[]
     */
    protected function getNonCatchablePatterns(): array
    {
        return ['\s+'];
    }
}
