<?php

namespace App\Jobs;

use GDText\Box;
use GDText\Color;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeneratePackageOpenGraphImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $packageName;
    protected $packageAuthor;
    protected $packageOgImageName;

    private $storageDir = 'ogimage/';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $packageName, string $packageAuthor, string $packageOgImageName)
    {
        $this->packageName = $packageName;
        $this->packageAuthor = $packageAuthor;
        $this->packageOgImageName = $packageOgImageName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->createStorageDirectory()) {
            $this->removeExistingImagesForPackage();
        }

        $image = $this->createImage();

        $this->saveImageToStorage($image);
    }

    private function createStorageDirectory(): bool
    {
        if (Storage::exists($this->storageDir)) {
            return false;
        }

        Storage::makeDirectory($this->storageDir);
        return true;
    }

    private function removeExistingImagesForPackage()
    {
        $files = Storage::files($this->storageDir);
        $packageId = explode('_', $this->packageOgImageName)[0];
        $matches = preg_grep("/{$packageId}\_/", $files);

        Storage::delete($matches);
    }

    private function saveImageToStorage($image)
    {
        imagepng($image, storage_path($this->getImageStoragePath()));

        imagedestroy($image);
    }

    private function getImageStoragePath()
    {
        return "app/public/{$this->storageDir}{$this->packageOgImageName}";
    }

    private function createImage()
    {
        $basePadding = 50;
        $gutter = 15;
        $height = 630;
        $width = 1200;

        $image = imagecreatefrompng(public_path('images/package-opengraph-base.png'));

        $indigoDarkest = imagecolorallocate($image, 44, 49, 88);

        imagesetthickness($image, 5);
        imagerectangle(
            $image,
            $basePadding,
            $basePadding,
            ($width - $basePadding),
            ($height - $basePadding),
            $indigoDarkest
        );

        $box = new Box($image);
        $box->setBox(($basePadding + ($gutter * 3)), 0, $width - ($basePadding * 4), $height - ($basePadding * 2));
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Bold.ttf'));
        $box->setTextAlign('left', 'center');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(75);
        $box->draw(Str::limit($this->packageName, 70, '...'));

        $box = new Box($image);
        $box->setBox(($basePadding + ($gutter * 3)), 0, $width, $height - ($basePadding + ($gutter * 3)));
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Italic.ttf'));
        $box->setTextAlign('left', 'bottom');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(30);
        $box->draw('By ' . $this->packageAuthor);

        return $image;
    }
}
