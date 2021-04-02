<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 02.04.2021
 * Time: 15:27
 */

namespace Mrcnpdlk\Lib\Filesystem;

use SplFileObject;

class File extends NodeAbstract implements NodeInterface
{
    /**
     * @var string
     */
    private $location;

    /**
     * File constructor.
     *
     * @param string $location
     */
    public function __construct(string $location)
    {
        $this->location = self::sanitizePath($location);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->isExists()) {
            unlink($this->getFullPath());
        }

        return !$this->isExists();
    }

    public function getDir(): Directory
    {
        return new Directory($this->getDirname());
    }

    /**
     * /www/htdocs/inc/lib.inc.php -> /www/htdocs/inc
     *
     * @return string
     */
    public function getDirname(): string
    {
        return pathinfo($this->getFullPath(), PATHINFO_DIRNAME);
    }

    /**
     * /www/htdocs/inc/lib.inc.php -> lib.inc
     *
     * @return string
     */
    public function getFilename(): string
    {
        return pathinfo($this->getFullPath(), PATHINFO_FILENAME);
    }

    /**
     * Returns path without slash on teh end
     *
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getMime(): string
    {
        $res = mime_content_type($this->getFullPath());
        if (false === $res) {
            throw new Exception(sprintf('Cannot get Mime type for `%s`', $this->getFullPath()));
        }

        return $res;
    }

    /**
     * @return \SplFileObject
     */
    public function getSplFileObject(): SplFileObject
    {
        return new SplFileObject($this->getFullPath());
    }

    /**
     * @return bool
     */
    public function isExists(): bool
    {
        return file_exists($this->getFullPath());
    }

    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
     *
     * @return string
     */
    public function md5(): string
    {
        if ($this->isExists()) {
            $res = md5_file($this->getFullPath());
            if (false === $res) {
                throw new Exception('Nie udało się pobrać sumy kontrolnej pliku');
            }

            return $res;
        }
        throw new Exception(sprintf('Plik %s nie istnieje', $this->getFullPath()));
    }

    /**
     * @param string $contents
     *
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
     *
     * @return $this
     */
    public function putContents(string $contents): self
    {
        $res = file_put_contents($this->getFullPath(), $contents);
        if (false === $res) {
            throw new Exception('Nie udało się zapisać zawartości do pliku');
        }

        return $this;
    }
}
