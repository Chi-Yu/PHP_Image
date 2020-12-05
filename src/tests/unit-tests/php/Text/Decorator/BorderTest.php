<?php

declare(strict_types=1);

namespace randomhost\Image\Text\Decorator;

use PHPUnit\Framework\TestCase;
use randomhost\Image\Image;

/**
 * Unit test for Border.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2020 random-host.tv
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 * @see       https://github.random-host.tv/image/
 *
 * @internal
 * @covers \randomhost\Image\Text\Decorator\Border
 */
class BorderTest extends TestCase
{
    /**
     * Tests Border::setBorderColor() and Border::getBorderColor().
     */
    public function testSetGetBorderColor()
    {
        // mock dependencies
        $text = $this->createMock('randomhost\\Image\\Text\\Generic');
        $color = $this->createMock('randomhost\\Image\\Color');

        $border = new Border($text);

        $this->assertSame(
            $border,
            $border->setBorderColor($color)
        );

        $this->assertSame($color, $border->getBorderColor());
    }

    /**
     * Tests Border::setInsertText().
     */
    public function testInsertText()
    {
        // test values
        $alpha = 75;
        $xPosition = 20;
        $yPosition = 40;
        $text = 'test';

        /*
         * Create a real image object as mocking this is a little too
         * complicated for now.
         */
        $image = Image::getInstanceByCreate(100, 100);

        // mock dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');
        $textColorMock = $this->createMock('randomhost\\Image\\Color');
        $borderColorMock = $this->createMock('randomhost\\Image\\Color');

        // configure mock objects
        $borderColorMock->expects($this->once())
            ->method('getAlpha')
            ->will($this->returnValue($alpha))
        ;

        $borderColorMock->expects($this->atLeastOnce())
            ->method('setAlpha')
            ->withConsecutive([$this->identicalTo(0)], [$this->identicalTo(75)])
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextColor')
            ->will($this->returnValue($textColorMock))
        ;

        $textMock->expects($this->exactly(2))
            ->method('setTextColor')
            ->withConsecutive(
                [$this->identicalTo($borderColorMock)],
                [$this->identicalTo($textColorMock)]
            )
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->atLeastOnce())
            ->method('insertText')
            ->withConsecutive(
                [
                    $this->identicalTo($xPosition - 1),
                    $this->identicalTo($yPosition - 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition - 1),
                    $this->identicalTo($yPosition),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition - 1),
                    $this->identicalTo($yPosition + 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition),
                    $this->identicalTo($yPosition - 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition),
                    $this->identicalTo($yPosition + 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition + 1),
                    $this->identicalTo($yPosition - 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition + 1),
                    $this->identicalTo($yPosition),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition + 1),
                    $this->identicalTo($yPosition + 1),
                    $this->identicalTo($text),
                ],
            )
            ->will($this->returnSelf())
        ;

        $border = new Border($textMock);

        $border->setBorderColor($borderColorMock);

        $this->assertSame(
            $border,
            $border->insertText($xPosition, $yPosition, $text)
        );
    }

    /**
     * Tests Border::setInsertText() with an unset border color.
     */
    public function testInsertTextMissingBorderColor()
    {
        // mock dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');

        $border = new Border($textMock);

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('Attempt to render text border without setting a color');

        $border->insertText(0, 0, '');
    }
}
