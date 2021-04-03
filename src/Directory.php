<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 02.04.2021
 * Time: 15:26
 */

namespace Mrcnpdlk\Lib\Filesystem;

use FilesystemIterator;
use Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException;
use Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException;
use RuntimeException;

class Directory extends NodeAbstract implements NodeInterface
{
    /**
     * @var string
     */
    private $location;

    /**
     * Directory constructor.
     *
     * @param string $location
     */
    public function __construct(string $location = '')
    {
        $this->setLocation($location);
    }

    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException
     *
     * @return $this
     */
    public function create(): self
    {
        $concurrentDirectory = $this->getFullPath();
        if (!file_exists($this->getFullPath()) && !mkdir($concurrentDirectory, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new CannotCreateException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->deleteDirectory($this->getFullPath());
    }

    /**
     * @return $this
     */
    public function empty(): self
    {
        $this->deleteDirectory($this->getFullPath());
        if (!mkdir($concurrentDirectory = $this->getFullPath(), 0777, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return $this;
    }

    /**
     * @param string $fileName
     *
     * @return \Mrcnpdlk\Lib\Filesystem\File
     */
    public function file(string $fileName): File
    {
        return new File($this->getFullPath() . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        $realPath = realpath($this->location);

        return false === $realPath ? $this->location : $realPath;
    }

    /**
     * @param string $subDirPath
     *
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException
     *
     * @return \Mrcnpdlk\Lib\Filesystem\Directory
     */
    public function getSubDir(string $subDirPath = ''): Directory
    {
        if (empty($subDirPath)) {
            return $this->create();
        }
        $directoryPath = $this->getFullPath() . DIRECTORY_SEPARATOR . $this->sanitizeName($subDirPath);

        return (new self($directoryPath))->create();
    }

    /**
     * @return bool
     */
    public function isExists(): bool
    {
        return is_dir($this->getFullPath());
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function deleteDirectory(string $path): bool
    {
        if (is_link($path)) {
            return unlink($path);
        }

        if (!file_exists($path)) {
            return true;
        }

        if (!is_dir($path)) {
            return unlink($path);
        }

        foreach (new FilesystemIterator($path) as $item) {
            if (!$this->deleteDirectory($item)) {
                return false;
            }
        }

        /*
         * By forcing a php garbage collection cycle using gc_collect_cycles() we can ensure
         * that the rmdir does not fail due to files still being reserved in memory.
         */
        gc_collect_cycles();

        return rmdir($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isFilePath(string $path): bool
    {
        return false !== strpos($path, '.');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function removeFilenameFromPath(string $path): string
    {
        if (!$this->isFilePath($path)) {
            return $path;
        }

        return substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
    }

    /**
     * @param string $name
     *
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\InvalidNameException
     *
     * @return string
     */
    protected function sanitizeName(string $name): string
    {
        if (!self::isValidDirectoryName($name)) {
            throw new InvalidNameException("The directory name `$name` contains invalid characters.");
        }

        return trim($name);
    }

    /**
     * @param string $location
     *
     * @return $this
     */
    protected function setLocation(string $location): self
    {
        $this->location = empty($location) ? self::getSystemTemporaryDirectory() : self::sanitizePath($location);

        return $this;
    }
}
