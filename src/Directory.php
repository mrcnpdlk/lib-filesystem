<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 02.04.2021
 * Time: 15:26
 */

namespace Mrcnpdlk\Lib\Filesystem;

use FilesystemIterator;
use RuntimeException;

class Directory extends NodeAbstract implements NodeInterface
{
    /**
     * @var string
     */
    private $location;
    /**
     * @var string
     */
    private $name;

    /**
     * Directory constructor.
     *
     * @param string $location
     */
    public function __construct(string $location = '')
    {
        $this->location = self::sanitizePath($location);
    }

    /**
     * @return $this
     */
    public function create(): self
    {
        if (empty($this->location)) {
            $this->location = self::getSystemTemporaryDirectory();
        }

        if (empty($this->name)) {
            $this->name = mt_rand() . '-' . str_replace([' ', '.'], '', microtime());
        }
        if (!file_exists($this->getFullPath()) && !mkdir($concurrentDirectory = $this->getFullPath(), 0777, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
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
     * @param string $subDirPath
     *
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
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
     * @param string $location
     *
     * @return $this
     */
    public function location(string $location): self
    {
        $this->location = self::sanitizePath($location);

        return $this;
    }

    /**
     * @param string $name
     *
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
     *
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $this->sanitizeName($name);

        return $this;
    }

    /**
     * @param string $pathOrFilename
     *
     * @return string
     */
    public function path(string $pathOrFilename = ''): string
    {
        if (empty($pathOrFilename)) {
            return $this->getFullPath();
        }

        $path = $this->getFullPath() . DIRECTORY_SEPARATOR . trim($pathOrFilename, '/');

        $directoryPath = $this->removeFilenameFromPath($path);

        if (!file_exists($directoryPath) && !mkdir($directoryPath, 0777, true) && !is_dir($directoryPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $directoryPath));
        }

        return $path;
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
     * @return string
     */
    protected function getFullPath(): string
    {
        return $this->location . ($this->name ? DIRECTORY_SEPARATOR . $this->name : '');
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
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
     *
     * @return string
     */
    protected function sanitizeName(string $name): string
    {
        if (!self::isValidDirectoryName($name)) {
            throw new Exception("The directory name `$name` contains invalid characters.");
        }

        return trim($name);
    }
}
