# Convite -- QR Guest Book App
 
## Demo
MP4 demo version can be accessed [here](https://github.com/HagaiRaja/qrGuestBook/blob/main/resources/video/demo.mp4)

![Video Demo](https://github.com/HagaiRaja/qrGuestBook/blob/main/resources/video/demo.gif)

## Built Using
- PHP 7.4
- Laravel 8 (PHP ^7.3.0)
- Node v14.18.2
- [AdminLTE 3](https://adminlte.io/)

## Installation

1. Clone the repository with ``git clone`` and cd into the directory
```bash
git clone https://github.com/HagaiRaja/qrGuestBook.git
cd qrGuestBook
```
2. Copy ``.env.example`` file to ``.env``
3. Edit database credentials in ``.env``
4. Run ``composer install``
5. Run ``npm install``
6. Run ``npm run dev`` or ``npm run prod`` for production
7. Run ``npm run watch`` *optional, only to auto update UI when development
8. On new tab, run ``php artisan key:generate``
10. Run ``php artisan migrate``
11. Run ``php artisan db:seed``
12. Run ``php artisan storage:link``
13. Run ``php artisan serve`` (if you want to use other port add ``--port=90``)

## How to Use
Will write as soon as needed, let me know if you want it :)

## Credits
Thanks to [nimiq/qr-scanner](https://github.com/nimiq/qr-scanner) for the awesome QR-Scanner Library
