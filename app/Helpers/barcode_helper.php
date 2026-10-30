<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorSVG;

if (!function_exists('barcode_svg')) {
    /**
     * Barcode Code128 dalam bentuk SVG inline, untuk kode pendek seperti NIS/NIP di kartu anggota.
     */
    function barcode_svg(string $code, float $widthFactor = 1.4, float $height = 32): string
    {
        if ($code === '') {
            return '';
        }

        return (new BarcodeGeneratorSVG())->getBarcode($code, BarcodeGeneratorSVG::TYPE_CODE_128, $widthFactor, $height);
    }
}

if (!function_exists('qrcode_svg')) {
    /**
     * QR code dalam bentuk SVG inline, untuk kode panjang seperti kode inventaris buku.
     */
    function qrcode_svg(string $code): string
    {
        if ($code === '') {
            return '';
        }

        $options = new QROptions([
            'outputBase64'    => false,
            'svgAddXmlHeader' => false,
            'connectPaths'    => true,
            'quietzoneSize'   => 1,
        ]);

        return (new QRCode($options))->render($code);
    }
}
