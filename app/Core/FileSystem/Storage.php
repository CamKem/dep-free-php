<?php

namespace App\Core\FileSystem;

class Storage
{

    public function __construct(
        // set the path to the public directory.
        // TODO: add in symlinks later
        private string $path
    )
    {
        // add a trailing slash if it doesn't exist
        $this->path = rtrim($this->path, '/') . '/';
        // add the base path to the storage path
        $this->path = BASE_PATH . $this->path;
    }

    public function get(string $path): false|string
    {
        return file_get_contents($this->path . $path);
    }

    public function put(string $path, array $contents): false|string
    {
        // if error is not 0, then we have an error
        if ($contents['error'] !== 0) {
            return false;
        }

        // we need to get the contents of the file
        $file = file_get_contents($contents['tmp_name']);

        // now we need to remove the file from the temp location
        unlink($contents['tmp_name']);

        // we need to ensure the name is web safe, no spaces, etc
        $name = str_replace(' ', '-', $contents['name']);

        // we need to handle non-breaking spaces & control characters
        // there is a regex for all control characters, it;s $regex = '/[\p{Cf}]/u';
        // we also need to handle non-breaking spaces & narrow non-breaking spaces
        // U+202F & U+00A0, respectively - we can add it to the regex
        $name = preg_replace('/[\p{Cf}\x{00A0}\x{202F}]/u', '-', $name);

        // we need to handle the case where the file already exists
        if (file_exists($this->path . $path . $name)) {
            $name = time() . '-' . $name;
        }

        // we need to move the file from the temp location to the storage location
        $uploaded = file_put_contents($this->path . $path . $name, $file);
        if ($uploaded === false) {
            return false;
        }

        // if $uploaded === $contents['size'], then we have a successful upload
        if ($uploaded === $contents['size']) {
            return $path . $name;
        }

        return false;
    }

    public function delete(string $path): bool
    {
        // we need to handle the case where the file doesn't exist
        if ($this->exists($path) === false) {
            return false;
        }
        return unlink($this->path . $path);
    }

    public function exists(string $path): bool
    {
        return file_exists($this->path . $path);
    }

    public function size(string $path): int
    {
        return filesize($this->path . $path);
    }

    public function lastModified(string $path): int
    {
        return filemtime($this->path . $path);
    }

    public function copy(string $from, string $to): bool
    {
        return copy($this->path . $from, $this->path . $to);
    }

    public function move(string $from, string $to): bool
    {
        return rename($this->path . $from, $this->path . $to);
    }

    public function files(string $directory): array
    {
        return array_values(array_filter(scandir($this->path . $directory), function ($file) use ($directory) {
            return !in_array($file, ['.', '..']) && is_file($this->path . $directory . '/' . $file);
        }));
    }

}