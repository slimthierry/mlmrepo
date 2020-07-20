<?php

namespace Drewlabs\Contracts\FileSystem;

interface Imageable
{
    /**
     * Resize an image based on provided width and height
     *
     * @param string|null $file_path
     * @param integer $width
     * @param integer $height
     * @return mixed
     */
    public function resize(?string $file_path, int $width = null, int $height = null);

    /**
     * Crop an image based on provided width and height
     *
     * @param string|null $file_path
     * @param integer $width
     * @param integer $height
     * @param boolean $same_size
     * @return mixed
     */
    public function crop(?string $file_path, int $width = null, int $height = null, bool $same_size = false);
}
