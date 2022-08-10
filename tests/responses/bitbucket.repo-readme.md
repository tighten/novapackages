# Nova Uploadcare Image Field

An image field using the UploadCare API.

This is a vue-wrapper around uploadcare-vue (https://github.com/tipeio/uploadcare-vue).

You can upload an image through drag-and-drop, url or google drive. The image is stored on Uploadcare and saved as a url in the model.

## Screenshots

![image1](https://i.imgur.com/ulpx4yK.png)
![image2](https://i.imgur.com/1vOlAec.png)
![image3](https://i.imgur.com/uNMDmHZ.png)

### Installing

~~~~
composer require adnanchowdhury/uploadcare-image
~~~~

### Getting Started

To publish the config file to config/uploadcare.php, run:

~~~~
php artisan vendor:publish --provider="Adnanchowdhury\UploadcareImage\FieldServiceProvider" 
~~~~

Add Uploadcare Public Key to .env:

~~~~
UPLOADCARE_PUBLIC_KEY=yourpublickey
~~~~

Register the field in the Nova resource:

~~~~
use Adnanchowdhury\UploadcareImage\UploadcareImage;
~~~~

Use the field:

~~~~
UploadcareImage::make('Image')
~~~~



## Authors

* **Adnan Chowdhury**

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

