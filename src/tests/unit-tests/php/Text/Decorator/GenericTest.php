<?php

declare(strict_types=1);

namespace randomhost\Image\Text\Decorator;

use PHPUnit\Framework\TestCase;
use randomhost\Image\Image;

/**
 * Unit test for Generic.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2020 random-host.tv
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 * @see       https://github.random-host.tv/image/
 *
 * @internal
 * @covers \randomhost\Image\Text\Decorator\Generic
 */
class GenericTest extends TestCase
{
    /**
     * Tests Generic::setImage() and Generic::getImage().
     */
    public function testSetGetImage()
    {
        // dependencies
        $image = $this->getImageInstance();
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setImage')
            ->with($this->identicalTo($image))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getImage')
            ->will($this->returnValue($image))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$textMock]
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setImage($image)
        );

        $this->assertSame($image, $generic->getImage());
    }

    /**
     * Tests Generic::setTextColor() and Generic::getTextColor().
     */
    public function testSetGetTextColor()
    {
        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextColor')
            ->with($this->identicalTo($colorMock))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextColor')
            ->will($this->returnValue($colorMock))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$textMock]
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setTextColor($colorMock)
        );

        $this->assertSame($colorMock, $generic->getTextColor());
    }

    /**
     * Tests Generic::setTextFont() and Generic::getTextFont().
     */
    public function testSetGetTextFont()
    {
        // test value
        $font = '/path/to/font.ttf';

        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextFont')
            ->with($this->identicalTo($font))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextFont')
            ->will($this->returnValue($font))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$textMock]
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setTextFont($font)
        );

        $this->assertSame($font, $generic->getTextFont());
    }

    /**
     * Tests Generic::setTextSize() and Generic::getTextSize().
     */
    public function testSetGetTextSize()
    {
        // test value
        $size = 14.0;

        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextSize')
            ->with($this->identicalTo($size))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextSize')
            ->will($this->returnValue($size))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$textMock]
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setTextSize($size)
        );

        $this->assertSame($size, $generic->getTextSize());
    }

    /**
     * Tests Generic::insertText().
     */
    public function testInsertText()
    {
        // test values
        $xPosition = 20;
        $yPosition = 40;
        $text = 'test';

        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition),
                $this->identicalTo($yPosition),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf())
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$textMock]
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->insertText($xPosition, $yPosition, $text)
        );
    }

    /**
     * Tests Generic::providesMethod().
     */
    public function testProvidesMethod()
    {
        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$textMock]
        );

        $this->assertTrue($generic->providesMethod('insertText'));

        $this->assertFalse($generic->providesMethod('doesNotExist'));
    }

    /**
     * Tests Generic::providesMethod() with a tree of decorators.
     */
    public function testProvidesMethodWithDecoratorTree()
    {
        // test values
        $existingMethod = 'setBorderColor';
        $missingMethod = 'doesNotExist';

        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');
        $borderMock = $this
            ->getMockBuilder('randomhost\\Image\\Text\\Decorator\\Border')
            ->setConstructorArgs([$textMock])
            ->getMock()
        ;

        // configure mock objects
        $borderMock->expects($this->exactly(2))
            ->method('providesMethod')
            ->withConsecutive(
                [$this->identicalTo($existingMethod)],
                [$this->identicalTo($missingMethod)]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(true),
                $this->returnValue(false)
            )
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$borderMock]
        );

        $this->assertTrue($generic->providesMethod('setBorderColor'));

        $this->assertFalse($generic->providesMethod('doesNotExist'));
    }

    /**
     * Tests Generic::__call() with a tree of decorators.
     */
    public function testCallWithDecoratorTree()
    {
        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');
        $borderMock = $this
            ->getMockBuilder('randomhost\\Image\\Text\\Decorator\\Border')
            ->setConstructorArgs([$textMock])
            ->getMock()
        ;
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        // configure mock objects
        $borderMock->expects($this->once(0))
            ->method('setBorderColor')
            ->with($this->identicalTo($colorMock))
            ->will($this->returnSelf())
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$borderMock]
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setBorderColor($colorMock)
        );
    }

    /**
     * Tests Generic::__call() with an undefined method.
     */
    public function testCallUndefinedMethod()
    {
        // dependencies
        $textMock = $this->createMock('randomhost\\Image\\Text\\Generic');

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            [$textMock]
        );

        $this->expectException('\BadMethodCallException');
        $this->expectExceptionMessage('Failed to call');

        $generic->doesNotExist();
    }

    /**
     * Returns a real image object as mocking this is a little too complicated
     * for now.
     *
     * @return Image
     */
    protected function getImageInstance()
    {
        return Image::getInstanceByCreate(100, 100);
    }
}
