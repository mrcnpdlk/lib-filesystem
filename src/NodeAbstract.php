<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 02.04.2021
 * Time: 15:34
 */

namespace Mrcnpdlk\Lib\Filesystem;

abstract class NodeAbstract
{
    /**
     * @param string $directoryName
     *
     * @return bool
     */
    protected static function isValidDirectoryName(string $directoryName): bool
    {
        return false === strpbrk($directoryName, '\\/?%*:|"<>');
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    protected static function isValidFileName(string $fileName): bool
    {
        return false === strpbrk($fileName, '\\/?%*:|"<>');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected static function sanitizePath(string $path): string
    {
        $path = rtrim($path);

        return rtrim($path, DIRECTORY_SEPARATOR);
    }
}
