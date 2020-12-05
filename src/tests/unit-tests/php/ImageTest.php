<?php

declare(strict_types=1);

namespace randomhost\Image;

use PHPUnit\Framework\TestCase;

/**
 * Unit test for Image.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2020 random-host.tv
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 * @see       https://github.random-host.tv/image/
 *
 * @internal
 * @covers \randomhost\Image\Image
 */
class ImageTest extends TestCase
{
    /**
     * Path to test data directory.
     *
     * @var string
     */
    private const TEST_DATA_DIR = '/testdata';

    /**
     * Data provider for test image and associated mime type values.
     *
     * @return array
     */
    public function providerImageData()
    {
        return [
            // test image, mime type, width, height
            ['test.gif', 'image/gif', 128, 128],
            ['test.jpg', 'image/jpeg', 128, 128],
            ['test.png', 'image/png', 128, 128],
        ];
    }

    /**
     * Data provider for image merging.
     *
     * @return array
     */
    public function providerMerge()
    {
        return [
            // first image name, second image name, merge strategy
            ['test.jpg', 'test.png', Image::MERGE_SCALE_DST],
            ['test.jpg', 'test.png', Image::MERGE_SCALE_DST_NO_UPSCALE],
            ['test.jpg', 'test.png', Image::MERGE_SCALE_SRC],
            ['test.png', 'test_small.png', Image::MERGE_SCALE_DST],
            [
                'test.png',
                'test_small.png',
                Image::MERGE_SCALE_DST_NO_UPSCALE,
            ],
            ['test.png', 'test_small.png', Image::MERGE_SCALE_SRC],
            ['test_small.png', 'test.png', Image::MERGE_SCALE_DST],
            [
                'test_small.png',
                'test.png',
                Image::MERGE_SCALE_DST_NO_UPSCALE,
            ],
            ['test_small.png', 'test.png', Image::MERGE_SCALE_SRC],
        ];
    }

    /**
     * Data provider for image merging with alpha value.
     *
     * @return array
     */
    public function providerMergeAlpha()
    {
        return [
            // first image name, second image name, alpha value
            ['test.jpg', 'test.png', 127],
            ['test.jpg', 'test.png', 0],
            ['test.jpg', 'test.png', 64],
        ];
    }

    /**
     * Tests Image::getInstanceByPath() with a GIF image.
     */
    public function testGetInstanceByPathGif()
    {
        $imagePath = $this->getTestDataPath('test.gif');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);
    }

    /**
     * Tests Image::getInstanceByPath() with a JPEG image.
     */
    public function testGetInstanceByPathJpeg()
    {
        $imagePath = $this->getTestDataPath('test.jpg');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);
    }

    /**
     * Tests Image::getInstanceByPath() with a PNG image.
     */
    public function testGetInstanceByPathPng()
    {
        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);
    }

    /**
     * Tests Image::getInstanceByPath() with an unsupported image format.
     */
    public function testGetInstanceByPathUnsupportedFormat()
    {
        $imagePath = $this->getTestDataPath('test.tif');

        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage(
            'Image type image/tiff not supported'
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests Image::getInstanceByPath() with an empty image file.
     */
    public function testGetInstanceByPathEmptyImage()
    {
        $imagePath = $this->getTestDataPath('empty.gif');

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            "Couldn't read image at"
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests Image::getInstanceByPath() with a broken image file.
     */
    public function testGetInstanceByPathBrokenImage()
    {
        $imagePath = $this->getTestDataPath('broken.gif');

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            "Couldn't read image at"
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests Image::getInstanceByPath() with a GIF image.
     */
    public function testGetInstanceByPathCache()
    {
        $imageName = 'test.png';

        // create temporary image copy so we can do whatever we want with it
        $originalPath = $this->getTestDataPath($imageName);
        $dummyPath = dirname($originalPath).DIRECTORY_SEPARATOR.
            'tmp_'.$imageName;
        copy($originalPath, $dummyPath);
        $this->assertTrue(
            is_file($dummyPath),
            'Test dummy file not found'
        );
        $this->assertTrue(
            is_readable($dummyPath),
            'Test dummy file not readable'
        );

        // prepare cache directory
        $cacheDir = sys_get_temp_dir();
        $cachePath = $cacheDir.DIRECTORY_SEPARATOR.'tmp_'.$imageName;
        if (is_file($cachePath) && is_writable($cachePath)) {
            unlink($cachePath);
        }
        $this->assertFalse(
            is_file($cachePath),
            'Cached test dummy file was not deleted'
        );

        // build image from test dummy
        $image = Image::getInstanceByPath($dummyPath, $cacheDir);
        $this->assertInstanceOf('randomhost\\Image\\Image', $image);
        $this->assertIsResource($image->image);
        unset($image);

        // ensure image is cached
        $this->assertTrue(
            is_file($cachePath),
            'Cached test dummy file not found'
        );
        $this->assertTrue(
            is_readable($cachePath),
            'Cached test dummy file not readable'
        );

        // delete test dummy
        unlink($dummyPath);
        $this->assertFalse(
            is_file($dummyPath),
            'Test dummy file was not deleted'
        );

        // build image from cache
        $image = Image::getInstanceByPath($dummyPath, $cacheDir);
        $this->assertInstanceOf('randomhost\\Image\\Image', $image);
        $this->assertIsResource($image->image);
        unset($image);

        // delete cache file
        if (is_file($cachePath) && is_writable($cachePath)) {
            unlink($cachePath);
        }
        $this->assertFalse(
            is_file($cachePath),
            'Cached test dummy file was not deleted'
        );
    }

    /**
     * Tests Image::getInstanceByPath() with an invalid cache path.
     */
    public function testGetInstanceByPathInvalidCachePath()
    {
        $cacheDir = 'doesNotExists';

        $imagePath = $this->getTestDataPath('test.png');

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage(
            'Cache directory at '.$cacheDir.' could not be found'
        );

        Image::getInstanceByPath($imagePath, $cacheDir);
    }

    /**
     * Tests Image::getInstanceByCreate().
     */
    public function testGetInstanceByCreate()
    {
        $image = Image::getInstanceByCreate(10, 10);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);
    }

    /**
     * Tests Image::merge().
     *
     * @param string $firstImageName  First image name.
     * @param string $secondImageName Second image name.
     * @param int    $strategy        Merge strategy.
     *
     * @dataProvider providerMerge
     */
    public function testMerge($firstImageName, $secondImageName, $strategy)
    {
        $firstImagePath = $this->getTestDataPath($firstImageName);
        $secondImagePath = $this->getTestDataPath($secondImageName);

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertIsResource($firstImage->image);
        $this->assertIsResource($secondImage->image);

        $this->assertSame(
            $firstImage,
            $firstImage->merge(
                $secondImage,
                0,
                0,
                $strategy
            )
        );
    }

    /**
     * Tests Image::merge() with an unset first image resource.
     */
    public function testMergeInvalidFirstResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertIsResource($firstImage->image);
        $this->assertIsResource($secondImage->image);

        $firstImage->image = null;
        $this->assertNull($firstImage->image);

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->merge($secondImage, 0, 0);
    }

    /**
     * Tests Image::merge() with an unset second image resource.
     */
    public function testMergeInvalidSecondResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertIsResource($firstImage->image);
        $this->assertIsResource($secondImage->image);

        $secondImage->image = null;
        $this->assertNull($secondImage->image);

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->merge($secondImage, 0, 0);
    }

    /**
     * Tests Image::mergeAlpha().
     *
     * @param string $firstImageName  First image name.
     * @param string $secondImageName Second image name.
     * @param int    $alpha           Alpha value.
     *
     * @dataProvider providerMergeAlpha
     */
    public function testMergeAlpha($firstImageName, $secondImageName, $alpha)
    {
        $firstImagePath = $this->getTestDataPath($firstImageName);
        $secondImagePath = $this->getTestDataPath($secondImageName);

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertIsResource($firstImage->image);
        $this->assertIsResource($secondImage->image);

        $this->assertSame(
            $firstImage,
            $firstImage->mergeAlpha(
                $secondImage,
                0,
                0,
                $alpha
            )
        );
    }

    /**
     * Tests Image::mergeAlpha() with an unset first image resource.
     */
    public function testMergeAlphaInvalidFirstResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertIsResource($firstImage->image);
        $this->assertIsResource($secondImage->image);

        $firstImage->image = null;
        $this->assertNull($firstImage->image);

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->mergeAlpha($secondImage, 0, 0);
    }

    /**
     * Tests Image::mergeAlpha() with an unset second image resource.
     */
    public function testMergeAlphaInvalidSecondResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertIsResource($firstImage->image);
        $this->assertIsResource($secondImage->image);

        $secondImage->image = null;
        $this->assertNull($secondImage->image);

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->mergeAlpha($secondImage, 0, 0);
    }

    /**
     * Tests Image::render().
     *
     * @runInSeparateProcess
     */
    public function testRender()
    {
        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);

        ob_start();

        $result = $image->render();
        ob_get_contents();
        ob_end_clean();

        $this->assertSame($image, $result);
    }

    /**
     * Tests Image::render() with an unset image resource.
     *
     * @runInSeparateProcess
     */
    public function testRenderInvalidResource()
    {
        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);

        $image->image = null;

        $this->assertNull($image->image);

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(
            'Attempt to render invalid resource as image.'
        );

        $image->render();
    }

    /**
     * Tests Image::getMimetype().
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     *
     * @dataProvider providerImageData
     */
    public function testGetMimeType($imageName, $mimeType)
    {
        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);

        $this->assertSame($mimeType, $image->getMimeType());
    }

    /**
     * Tests Image::getModified().
     *
     * @param string $imageName Test image name.
     *
     * @dataProvider providerImageData
     */
    public function testGetModified($imageName)
    {
        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);

        $this->assertSame(filemtime($imagePath), $image->getModified());
    }

    /**
     * Tests Image::getWidth().
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     * @param int    $width     Expected image width.
     * @param int    $height    Expected image height.
     *
     * @dataProvider providerImageData
     */
    public function testGetWidth($imageName, $mimeType, $width, $height)
    {
        /*
         * This is done to shut up IDEs which complain about the parameters
         * not being used so we can re-use the data provider.
         */
        unset($mimeType, $height);

        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);

        $this->assertSame($width, $image->getWidth());
    }

    /**
     * Tests Image::getHeight().
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     * @param int    $width     Expected image width.
     * @param int    $height    Expected image height.
     *
     * @dataProvider providerImageData
     */
    public function testGetHeight($imageName, $mimeType, $width, $height)
    {
        /*
         * This is done to shut up IDEs which complain about the parameters
         * not being used so we can re-use the data provider.
         */
        unset($mimeType, $width);

        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertIsResource($image->image);

        $this->assertSame($height, $image->getHeight());
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
