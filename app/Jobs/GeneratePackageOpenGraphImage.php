<?php

namespace App\Jobs;

use GDText\Box;
use GDText\Color;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GeneratePackageOpenGraphImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $packageName;
    protected $packageAuthor;
    protected $packageOgImageName;

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
        $path = 'app/public/og/';
        $file = $this->packageOgImageName;

        if (! Storage::exists('og/')) {
            Storage::makeDirectory('og/');
        } else {
            if (Storage::disk('public')->exists('og/' . $file)) {
                return;
            }
        }

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
        $box->setBox(($basePadding + ($gutter * 2)), 0, $width - ($basePadding * 2), $height - ($basePadding * 2));
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Bold.ttf'));
        $box->setTextAlign('left', 'center');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(80);
        $box->draw($this->packageName);

        $box = new Box($image);
        $box->setBox(($basePadding + ($gutter * 2)), 0, $width, $height - ($basePadding + ($gutter * 2)));
        $box->setFontFace(resource_path('fonts/Roboto/Roboto-Italic.ttf'));
        $box->setTextAlign('left', 'bottom');
        $box->setFontColor(new Color(44, 49, 88));
        $box->setFontSize(30);
        $box->draw('By ' . $this->packageAuthor);

        header('Content-type: image/png');

        imagepng($image, storage_path($path . $file));
        imagedestroy($image);
    }
}
