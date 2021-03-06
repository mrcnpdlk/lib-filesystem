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

    /**
     * /www/htdocs/inc/lib.inc.php -> lib.inc.php
     *
     * @return string
     */
    public function getBasename(): string
    {
        return pathinfo($this->getFullPath(), PATHINFO_BASENAME);
    }

    /**
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
     *
     * @return string
     */
    public function getContents(): string
    {
        $res = file_get_contents($this->getFullPath());
        if (false === $res) {
            throw new Exception('Nie udało się pobrać zawartości do pliku');
        }

        return $res;
    }

    /**
     * @return \Mrcnpdlk\Lib\Filesystem\Directory
     */
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
     * /www/htdocs/inc/lib.inc.php -> php
     *
     * @return string
     */
    public function getExtension(): string
    {
        return pathinfo($this->getFullPath(), PATHINFO_EXTENSION);
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
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception
     *
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
     * @throws \Mrcnpdlk\Lib\Filesystem\Exception\CannotCreateException
     *
     * @return $this
     */
    public function putContents(string $contents): self
    {
        $this->getDir()->create();
        $res = file_put_contents($this->getFullPath(), $contents);
        if (false === $res) {
            throw new Exception('Nie udało się zapisać zawartości do pliku');
        }

        return $this;
    }
}
