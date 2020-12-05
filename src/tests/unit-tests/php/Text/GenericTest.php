<?php

declare(strict_types=1);

namespace randomhost\Image\Text;

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
 * @covers \randomhost\Image\Text\Generic
 */
class GenericTest extends TestCase
{
    /**
     * Path to test data directory.
     *
     * @var string
     */
    public const TEST_DATA_DIR = '/testdata';

    /**
     * Tests Generic::setImage() and Generic::getImage().
     */
    public function testSetGetImage()
    {
        $image = $this->getImageInstance();

        $generic = new Generic();

        $this->assertSame($generic, $generic->setImage($image));

        $this->assertSame($image, $generic->getImage());
    }

    /**
     * Tests Generic::setTextColor() and Generic::getTextColor().
     */
    public function testSetGetTextColor()
    {
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->assertSame($colorMock, $generic->getTextColor());
    }

    /**
     * Tests Generic::setTextFont() and Generic::getTextFont().
     */
    public function testSetGetTextFont()
    {
        // test value
        $font = $this->getTestDataPath('vera.ttf');

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($font, $generic->getTextFont());
    }

    /**
     * Tests Generic::setTextFont() with an invalid font path.
     */
    public function testSetTextFontInvalidFontPath()
    {
        // test value
        $font = 'doesnotexist.ttf';

        $generic = new Generic();

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage(
            'Unable to load font file at '.$font
        );

        $generic->setTextFont($font);
    }

    /**
     * Tests Generic::setTextSize() and Generic::getTextSize().
     */
    public function testSetGetTextSize()
    {
        // test value
        $size = 14.0;

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextSize($size));

        $this->assertSame($size, $generic->getTextSize());
    }

    /**
     * Tests Generic::insertText().
     */
    public function testInsertText()
    {
        // dependencies
        $image = $this->getImageInstance();
        $font = $this->getTestDataPath('vera.ttf');
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->assertSame($generic, $generic->insertText(0, 0, ''));
    }

    /**
     * Tests Generic::insertText() with an unset Image object.
     */
    public function testInsertTextMissingImageObject()
    {
        // dependencies
        $font = $this->getTestDataPath('vera.ttf');
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to render text onto invalid image resource'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests Generic::insertText() with an unset Image object image resource.
     */
    public function testInsertTextUnsetImageResource()
    {
        // dependencies
        $image = $this->getImageInstance();
        $image->image = null;
        $font = $this->getTestDataPath('vera.ttf');
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to render text onto invalid image resource'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests Generic::insertText() with an unset Color object.
     */
    public function testInsertTextMissingColorObject()
    {
        // dependencies
        $image = $this->getImageInstance();
        $font = $this->getTestDataPath('vera.ttf');

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to render text without setting a color'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests Generic::insertText() with an unset font path.
     */
    public function testInsertTextUnsetFont()
    {
        // dependencies
        $image = $this->getImageInstance();
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'No font file selected for rendering text overlay'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests Generic::insertText() with a missing font file.
     */
    public function testInsertTextMissingFontFile()
    {
        // dependencies
        $image = $this->getImageInstance();
        $font = 'vera.ttf';
        $colorMock = $this->createMock('randomhost\\Image\\Color');

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        // move font file to a temporary path
        $tmpDir = sys_get_temp_dir();
        $fontPath = realpath($tmpDir).DIRECTORY_SEPARATOR.$font;
        copy($this->getTestDataPath($font), $fontPath);

        // set font and let setTextFont() validate the path
        $this->assertSame($generic, $generic->setTextFont($fontPath));

        // remove font copy to trigger the expected exception
        unlink($fontPath);

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Failed to read font file \''.$fontPath.'\''
        );

        $generic->insertText(0, 0, '');
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

    /**
     * Returns the path to the given test data file.
     *
     * @param string $fileName Test data file name.
     *
     * @throws \Exception Thrown in case the test data file could not be read.
     *
     * @return string
     */
    protected function getTestDataPath($fileName)
    {
        $dir = APP_TESTDIR.self::TEST_DATA_DIR;
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Exception(
                sprintf(
                    'Test data directory %s not found',
                    $dir
                )
            );
        }

        $path = realpath($dir).'/'.$fileName;
        if (!is_file($path) || !is_readable($path)) {
            throw new \Exception(
                sprintf(
                    'Test feed %s not found',
                    $path
                )
            );
        }

        return realpath($path);
    }
}
