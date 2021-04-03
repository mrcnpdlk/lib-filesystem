<?php
/**
 * Created by Marcin.
 * Date: 03.04.2021
 * Time: 21:28
 */

namespace suits;

use Mrcnpdlk\Lib\Filesystem\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException
     */
    public function testCreateFile(): void
    {
        $file = new File(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt');
        $file->putContents('xxx');
        self::assertTrue($file->isExists());
        self::assertSame('xxx', $file->getContents());
        $file->delete();
        self::assertFalse($file->isExists());
    }

    public function testFile(): void
    {
        $file = new File(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt');
        self::assertSame('test.txt', $file->getBasename());
        self::assertSame('test', $file->getFilename());
        self::assertSame('txt', $file->getExtension());
        self::assertSame(__DIR__, $file->getDirname());
        self::assertSame(__DIR__, $file->getDir()->getFullPath());
    }
}
