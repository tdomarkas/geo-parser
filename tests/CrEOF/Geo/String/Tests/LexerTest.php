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

namespace CrEOF\Geo\String\Tests;

use CrEOF\Geo\String\Lexer;
use Doctrine\Common\Lexer\Token;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class LexerTest extends TestCase
{
    #[DataProvider('getCases')]
    public function testLexer(string $input, array $expectedTokens): void
    {
        $lexer = new Lexer();
        $lexer->setInput($input);
        $index = 0;

        while (null !== $actual = $lexer->peek()) {
            self::assertEqualToken($expectedTokens[$index++], $actual);
        }
    }

    public function testReusedLexer(): void
    {
        $lexer = new Lexer();

        foreach ($this->getCases() as $data) {
            $input          = $data['input'];
            $expectedTokens = $data['expectedTokens'];
            $index          = 0;

            $lexer->setInput($input);

            while (null !== $actual = $lexer->peek()) {
                self::assertEqualToken($expectedTokens[$index++], $actual);
            }
        }
    }

    /**
     * @return array[]
     */
    public static function getCases(): array
    {
        return [
            [
                'input'          => '15',
                'expectedTokens' => [
                    ['value' => 15, 'type' => Lexer::T_INTEGER, 'position' => 0],
                ]
            ],
            [
                'input'          => '1E5',
                'expectedTokens' => [
                    ['value' => 100000.0, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ]
            ],
            [
                'input'          => '1e5',
                'expectedTokens' => [
                    ['value' => 100000.0, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ]
            ],
            [
                'input'          => '1.5E5',
                'expectedTokens' => [
                    ['value' => 150000.0, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ]
            ],
            [
                'input'          => '1E-5',
                'expectedTokens' => [
                    ['value' => 0.00001, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ]
            ],
            [
                'input'          => '40° 26\' 46" N',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 5],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 7],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 9],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 11],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 13]
                ]
            ],
            [
                'input'          => '40° 26\' 46" N 79° 58\' 56" W',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 5],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 7],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 9],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 11],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 13],
                    ['value' => 79, 'type' => Lexer::T_INTEGER, 'position' => 15],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 17],
                    ['value' => 58, 'type' => Lexer::T_INTEGER, 'position' => 20],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 22],
                    ['value' => 56, 'type' => Lexer::T_INTEGER, 'position' => 24],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 26],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 28]
                ]
            ],
            [
                'input'          => '40°26\'46"N 79°58\'56"W',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 4],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 6],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 7],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 9],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 10],
                    ['value' => 79, 'type' => Lexer::T_INTEGER, 'position' => 12],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 14],
                    ['value' => 58, 'type' => Lexer::T_INTEGER, 'position' => 16],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 18],
                    ['value' => 56, 'type' => Lexer::T_INTEGER, 'position' => 19],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 21],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 22]
                ]
            ],
            [
                'input'          => '40°26\'46"N, 79°58\'56"W',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 4],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 6],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 7],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 9],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 10],
                    ['value' => ',', 'type' => Lexer::T_COMMA, 'position' => 11],
                    ['value' => 79, 'type' => Lexer::T_INTEGER, 'position' => 13],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 15],
                    ['value' => 58, 'type' => Lexer::T_INTEGER, 'position' => 17],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 19],
                    ['value' => 56, 'type' => Lexer::T_INTEGER, 'position' => 20],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 22],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 23]
                ]
            ],
            [
                'input'          => '40.4738° N, 79.553° W',
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 10],
                    ['value' => ',', 'type' => Lexer::T_COMMA, 'position' => 11],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 13],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 19],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 22]
                ]
            ],
            [
                'input'          => '40.4738°, 79.553°',
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => ',', 'type' => Lexer::T_COMMA, 'position' => 9],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 11],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 17],
                ]
            ],
            [
                'input'          => '40.4738° -79.553°',
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => '-', 'type' => Lexer::T_MINUS, 'position' => 10],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 11],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 17],
                ]
            ],
            [
                'input'          => "40.4738° \t -79.553°",
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => '-', 'type' => Lexer::T_MINUS, 'position' => 12],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 13],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 19],
                ]
            ]
        ];
    }

    private static function assertEqualToken(array $expectedToken, Token $actual): void
    {
        self::assertEqualsWithDelta($expectedToken['value'], $actual->value, 0.000_000_001);
        self::assertSame($expectedToken['type'], $actual->type);
        self::assertSame($expectedToken['position'], $actual->position);
    }
}
