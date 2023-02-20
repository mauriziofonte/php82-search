<?php

namespace MFonte\Search\Services\FAL;

class Directory
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var File[]
     */
    private $files;

    private $keepOpen;

    /**
     * Directory constructor.
     *
     * @param string $path
     *
     * @throws \Exception
     */
    public function __construct($path = '', $keepFilesOpenned = true)
    {
        if ($path !== '') {
            if (mb_substr($path, -1) != \DIRECTORY_SEPARATOR) {
                $path .= \DIRECTORY_SEPARATOR;
            }
            $this->path = $path;
            $this->createDirectoryIfNotExist();
        }
        $this->keepOpen = $keepFilesOpenned;
    }

    /**
     * @param $directory
     *
     * @throws \Exception
     */
    private function createDirectoryIfNotExist()
    {
        if (!file_exists($this->path)) {
            mkdir($this->path, 0775, true);
        } elseif (!is_dir($this->path)) {
            throw new \Exception("The file at path $this->path is not a directory !");
        }
    }

    /**
     * opens a file.
     *
     * @param string $filename
     * @param bool   $createIfNotExist
     *
     * @return File|null
     */
    public function open($filename, $createIfNotExist = true) : ?File
    {
        if (!isset($this->files[$filename])) {
            if (file_exists($this->path.$filename) || $createIfNotExist) {
                $this->files[$filename] = new File($this->path, $filename, $this->keepOpen);
            } else {
                return null;
            }
        }

        return $this->files[$filename] ?? null;
    }

    /**
     * get a Directory based on the provided $name, creates the directory if it doesn't exist.
     *
     * @param bool $keepFilesOpened
     *
     * @throws \Exception
     *
     * @return Directory|null
     */
    public function getOrCreateDirectory($name, $keepFilesOpened = false) : ?Directory
    {
        if (file_exists($this->path.$name)) {
            if (is_dir($this->path.$name)) {
                return new self($this->path.$name, $keepFilesOpened);
            } else {
                return null;
            }
        }

        return new self($this->path.$name, $keepFilesOpened);
    }

    /**
     * deletes a file.
     */
    public function delete($file) : void
    {
        $file = $this->open($file);
        if ($file) {
            $file->delete();
        }
    }

    /**
     * gives all file names contained into the directory.
     *
     * @return array
     */
    public function scan() : array
    {
        $all = [];
        foreach (scandir($this->path) as $file) {
            if (is_file($this->path.$file)) {
                $all[] = $file;
            }
        }

        return $all;
    }

    /**
     * Open all files and returns them.
     *
     * @return File[]
     */
    public function openAll()
    {
        $all = scandir($this->path);
        foreach ($all as $file) {
            if (is_file($this->path.$file)) {
                $this->open($file, false);
            }
        }

        return $this->files;
    }

    /**
     * Unload all opened files.
     */
    public function free()
    {
        $this->files = [];
    }

    /**
     * Deletes all files contained into the directory.
     *
     * @param bool $softDelete
     *
     * @throws \Exception
     */
    public function deleteAll($softDelete = true) : void
    {
        if ($softDelete) {
            $all = $this->openAll();
            if (\count($all) > 0) {
                foreach ($all as $file) {
                    $file->delete();
                }
            }
        } else {
            $files = $this->files;
            if (\count($files) > 0) {
                foreach ($files as $file) {
                    $file->delete();
                }
            }
            $this->hardDelete(mb_substr($this->path, 0, -1));
            $this->createDirectoryIfNotExist();
        }
        $this->files = [];
    }

    /**
     * Deletes the folder and all its content.
     *
     * @return bool
     */
    private function hardDelete($dir) : bool
    {
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!$this->hardDelete($dir.\DIRECTORY_SEPARATOR.$file)) {
                chmod($dir.\DIRECTORY_SEPARATOR.$file, 0777);
                if (!$this->hardDelete($dir.\DIRECTORY_SEPARATOR.$file)) {
                    return false;
                }
            }
        }

        return rmdir($dir);
    }
}
