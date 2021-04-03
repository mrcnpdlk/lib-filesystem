<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 02.04.2021
 * Time: 15:22
 */

namespace Mrcnpdlk\Lib\Filesystem;

interface NodeInterface
{
    public function delete(): bool;

    /**
     * Return full path for directory or file
     *
     * @return string
     */
    public function getFullPath(): string;

    /**
     * Check if node exists on filesystem
     *
     * @return bool
     */
    public function isExists(): bool;
}
