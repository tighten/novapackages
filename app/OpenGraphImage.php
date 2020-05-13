<?php

namespace App;

use GDText\Box;
use GDText\Color;
use Illuminate\Support\Facades\File as Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OpenGraphImage
{
    const GUTTER = 15;
    const IMAGE_HEIGHT = 630;
    const IMAGE_WIDTH = 1200;
    const PADDING = 50;

    protected $fileName;
    protected $subtitle;
    protected $title;

    /**
     * __construct
     *
     * @param  string $title Main text for the image.
     * @param  string $subtitle Subtext for the image.
     * @param  string $fileName Image filename (e.g. my-image.png)
     * @return void
     */
    public function __construct($title, $subtitle, $fileName)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->fileName = $fileName;
    }

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

    public function generate()
    {
        Filesystem::ensureDirectoryExists(Storage::disk('public')->path('') . $this->storageDirectory());
        $this->deleteImagesForProject();
        $this->save($this->make());
    }

    protected function storageDirectory(): string
    {
        return config('opengraph.image_directory_name') . '/';
    }

    protected function storagePath(): string
    {
        return Storage::disk('public')->path('') . $this->storageDirectory() . "{$this->fileName}";
    }

    protected function deleteImagesForProject()
    {
        $files = Storage::files($this->storageDirectory());
        $id = explode('_', $this->fileName)[0];
        $matches = preg_grep("/{$id}\_/", $files);

        Storage::delete($matches);
    }

    protected function save($image)
    {
        imagepng($image, $this->storagePath($this->fileName));
        imagedestroy($image);
    }

    protected function make()
    {
        if (public_path('images/package-opengraph-base.png')) {
            $image = imagecreatefrompng(public_path('images/package-opengraph-base.png'));
        } else {
            $image = imagecreatetruecolor(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);

            imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
            imagesetthickness($image, 5);
            imagerectangle(
                $image,
                self::PADDING,
                self::PADDING,
                (self::IMAGE_WIDTH - self::PADDING),
                (self::IMAGE_HEIGHT - self::PADDING),
                imagecolorallocate($image, 44, 49, 88)
            );
        }

        $box = new Box($image);
        $box->setBox(
            self::PADDING * 2,
            0,
            self::IMAGE_WIDTH - (self::PADDING * 4),
            self::IMAGE_HEIGHT - (self::PADDING * 2)
        );
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Bold.ttf'));
        $box->setTextAlign('left', 'center');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(75);
        $box->draw(Str::limit($this->title, 70, '...'));

        $box = new Box($image);
        $box->setBox(
            self::PADDING * 2,
            0,
            self::IMAGE_WIDTH,
            self::IMAGE_HEIGHT - (self::PADDING * 2)
        );
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Italic.ttf'));
        $box->setTextAlign('left', 'bottom');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(30);
        $box->draw("By {$this->subtitle}");

        return $image;
    }
}
