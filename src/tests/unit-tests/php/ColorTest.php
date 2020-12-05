<?php

declare(strict_types=1);

namespace randomhost\Image;

use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * Unit test for Color.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2020 random-host.tv
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 * @see       https://github.random-host.tv/image/
 *
 * @internal
 * @covers \randomhost\Image\Color
 */
class ColorTest extends TestCase
{
    /**
     * Data provider for color values.
     *
     * @return array
     */
    public function providerColor()
    {
        return [
            // color, exception, type error
            [128, false, false],
            [0, false, false],
            [255, false, false],
            [0x80, false, false],
            [0x00, false, false],
            [0xFF, false, false],
            [-1, true, false],
            [256, true, false],
            [0x100, true, false],
            [128.5, false, true],
            ['128', false, true],
            ['notanumber', false, true],
        ];
    }

    /**
     * Data provider for alpha values.
     *
     * @return array
     */
    public function providerAlpha()
    {
        return [
            // alpha, exception
            [64, false, false],
            [0, false, false],
            [127, false, false],
            [-1, true, false],
            [128, true, false],
            [64.0, false, true],
            ['64', false, true],
            ['notanumber', false, true],
        ];
    }

    /**
     * Tests Color::setRed() and Color::getRed().
     *
     * @param mixed $red       Red component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testSetGetRed($red, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException('\InvalidArgumentException');
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(TypeError::class);
        }

        $this->assertSame($color, $color->setRed($red));

        $this->assertSame($red, $color->getRed());
    }

    /**
     * Tests Color::setGreen() and Color::getGreen().
     *
     * @param mixed $green     Green component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testSetGetGreen($green, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException('\InvalidArgumentException');
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(TypeError::class);
        }

        $this->assertSame($color, $color->setGreen($green));

        $this->assertSame($green, $color->getGreen());
    }

    /**
     * Tests Color::setBlue() and Color::getBlue().
     *
     * @param mixed $blue      Blue component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testSetGetBlue($blue, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException('\InvalidArgumentException');
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(TypeError::class);
        }

        $this->assertSame($color, $color->setBlue($blue));

        $this->assertSame($blue, $color->getBlue());
    }

    /**
     * Tests Color::setAlpha() and Color::getAlpha().
     *
     * @param mixed $alpha     Alpha value (0-127).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerAlpha
     */
    public function testSetGetAlpha($alpha, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException('\InvalidArgumentException');
            $this->expectExceptionMessage(
                'Alpha is expected to be an integer value between 0 and 127'
            );
        }

        if ($typeError) {
            $this->expectException(TypeError::class);
        }

        $this->assertSame($color, $color->setAlpha($alpha));

        $this->assertSame($alpha, $color->getAlpha());
    }

    /**
     * Tests Color::validateColor().
     *
     * @param mixed $color     Color component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testValidateColor($color, bool $exception, bool $typeError)
    {
        if ($exception) {
            $this->expectException('\InvalidArgumentException');
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(TypeError::class);
        }

        $this->assertTrue(Color::validateColor($color));
    }

    /**
     * Tests Color::validateAlpha().
     *
     * @param mixed $alpha     Alpha value (0-127).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerAlpha
     */
    public function testValidateAlpha($alpha, bool $exception, bool $typeError)
    {
        if ($exception) {
            $this->expectException('\InvalidArgumentException');
            $this->expectExceptionMessage(
                'Alpha is expected to be an integer value between 0 and 127'
            );
        }

        if ($typeError) {
            $this->expectException(TypeError::class);
        }

        $this->assertTrue(Color::validateAlpha($alpha));
    }
}
