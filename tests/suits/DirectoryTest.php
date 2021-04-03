<?php
/**
 * Created by Marcin.
 * Date: 03.04.2021
 * Time: 21:25
 */

namespace suits;

use Mrcnpdlk\Lib\Filesystem\Directory;
use Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    /**
     * @var string
     */
    protected static $pathDir;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$pathDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'lib-dir-test';
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        @rmdir(static::$pathDir);
    }

    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException
     */
    public function testCreate(): void
    {
        $dir = new Directory(static::$pathDir);
        self::assertFalse($dir->isExists());
        $dir->create();
        self::assertTrue($dir->isExists());
        self::assertSame(static::$pathDir, $dir->getFullPath());
        self::assertDirectoryExists(static::$pathDir);
        self::assertDirectoryIsReadable(static::$pathDir);
        self::assertDirectoryIsWritable(static::$pathDir);

        $file = $dir->file('aaa.txt');
        self::assertSame(static::$pathDir . DIRECTORY_SEPARATOR . 'aaa.txt', $file->getFullPath());

        $dir->delete();
        self::assertDirectoryNotExists(static::$pathDir);
    }

    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException
     */
    public function testInvalidSubDir(): void
    {
        $this->expectException(InvalidNameException::class);
        $dir = Directory::temp()->getSubDir('$%^&*');
        $dir->create();
    }

    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException
     */
    public function testSubDir(): void
    {
        $dir = new Directory(static::$pathDir);

        $subDirEmpty = $dir->getSubDir();
        self::assertSame(static::$pathDir, $subDirEmpty->getFullPath());

        $subDir = $dir->getSubDir('subdir');

        $path = static::$pathDir . DIRECTORY_SEPARATOR . 'subdir';

        self::assertSame($path, $subDir->getFullPath());
        self::assertDirectoryExists($path);
        self::assertDirectoryIsReadable($path);
        self::assertDirectoryIsWritable($path);
        $dir->delete();
        self::assertDirectoryNotExists($path);
        self::assertDirectoryNotExists(static::$pathDir);
    }

    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException
     */
    public function testTempDir(): void
    {
        $dir = Directory::temp();
        self::assertTrue($dir->isExists());
        self::assertDirectoryExists($dir->getFullPath());
        self::assertSame(sys_get_temp_dir(), $dir->getFullPath());
    }
}
