<?php

namespace App;

use GDText\Box;
use GDText\Color;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OpenGraphImage
{
    const PADDING = 50;
    const GUTTER = 15;

    protected $fileName;

    /**
     * Generate an open graph image file name.
     *
     * @param string $uniqueId A unique key to identify the image by.
     * @param string $name Name of the file.
     */
    public static function makeFileName($uniqueId, $name): string
    {
        return "{$uniqueId}_" . Str::slug($name, '-') . '.png';
    }

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function storageDirectory(): string
    {
        return config('opengraph.image_directory_name') . '/';
    }

    public function storagePath(): string
    {
        return Storage::disk('public')->path('') . $this->storageDirectory() . "{$this->fileName}";
    }

    public function makeStorageDirectory(): bool
    {
        if (Storage::exists($this->storageDirectory())) {
            return false;
        }

        Storage::makeDirectory($this->storageDirectory());
        return true;
    }

    public function removeDuplicates()
    {
        $files = Storage::files($this->storageDirectory());
        $id = explode('_', $this->fileName)[0];
        $matches = preg_grep("/{$id}\_/", $files);

        Storage::delete($matches);
    }

    public function save($image)
    {
        imagepng($image, $this->storagePath($this->fileName));

        imagedestroy($image);
    }

    /**
     * Create a new image
     *
     * @param  string $title Main text for the image.
     * @param  string $subtitle Subtext for the image.
     * @param  string $baseImagePath Path to a pre-designed png image base.
     * @param  int $width Width of the image.
     * @param  int $height Height of the image.
     */
    public function make($title, $subtitle, $baseImagePath = null, $width = 1200, $height = 630)
    {
        if ($baseImagePath) {
            $image = imagecreatefrompng($baseImagePath);
        } else {
            $image = imagecreatetruecolor($width, $height);

            imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
            imagesetthickness($image, 5);
            imagerectangle(
                $image,
                self::PADDING,
                self::PADDING,
                ($width - self::PADDING),
                ($height - self::PADDING),
                imagecolorallocate($image, 44, 49, 88)
            );
        }

        $box = new Box($image);
        $box->setBox(
            self::PADDING * 2,
            0,
            $width - (self::PADDING * 4),
            $height - (self::PADDING * 2)
        );
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Bold.ttf'));
        $box->setTextAlign('left', 'center');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(75);
        $box->draw(Str::limit($title, 70, '...'));

        $box = new Box($image);
        $box->setBox(
            self::PADDING * 2,
            0,
            $width,
            $height - (self::PADDING * 2)
        );
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Italic.ttf'));
        $box->setTextAlign('left', 'bottom');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(30);
        $box->draw($subtitle);

        return $image;
    }
}
