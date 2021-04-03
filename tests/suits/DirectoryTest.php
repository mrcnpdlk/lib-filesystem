<?php
/**
 * Created by Marcin.
 * Date: 03.04.2021
 * Time: 21:25
 */

namespace suits;

use Mrcnpdlk\Lib\Filesystem\Directory;
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
     */
    public function testCreate(): void
    {
        $dir = new Directory(static::$pathDir);
        $dir->create();
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
    public function testSubDir(): void
    {
        $dir    = new Directory(static::$pathDir);
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
}
