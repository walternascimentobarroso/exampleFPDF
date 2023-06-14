<?php

require __DIR__ . '/../vendor/autoload.php';

use Fpdf\Fpdf;

define("DOWNLOAD_FOLDER", "download");

function makeCertificate($name, $imagePath = "certificate.jpg", $font = "Greating.ttf")
{
    $image = imagecreatefromjpeg($imagePath);
    $color = imagecolorallocate($image, 19, 21, 22);
    $fontSize = 60;
    $textAngle = 0;
    $imageWidth = imagesx($image); // Gets the width of the image
    $textBoundingBox = imagettfbbox($fontSize, $textAngle, $font, $name); // Gets the coordinates of the text's bounding box
    $textWidth = $textBoundingBox[2] - $textBoundingBox[0]; // Calculate the width of the text's bounding box
    $textX = intval(($imageWidth - $textWidth) / 2); // Calculate the horizontal position to center the text
    imagettftext($image, $fontSize, $textAngle, $textX, 800, $color, $font, $name);
    imagejpeg($image, DOWNLOAD_FOLDER . "/$name.jpg");
    imagedestroy($image);
    return DOWNLOAD_FOLDER . "/$name.jpg";
}

function convertJPGtoPDF($imageFile, $pdfFileOutput = 'imagem', $landscape = false)
{
    $orientation = $landscape ? 'L' : 'P'; // Set the page orientation based on the $landscape parameter
    $pdf = new Fpdf($orientation);
    $pdf->AddPage();

    // Set the maximum width and height for the image in the PDF
    $maxWidth = $pdf->GetPageWidth() - 20; // 20 is a margin to prevent the image from getting too close to the edges
    $maxHeight = $pdf->GetPageHeight() - 20;

    list($imageWidth, $imageHeight) = getimagesize($imageFile); // Get the dimensions of the image
    $scale = min($maxWidth / $imageWidth, $maxHeight / $imageHeight); // Calculates the scale needed to fit the image to the maximum size allowed

    // Calculate the new width and height of the image
    $newWidth = $imageWidth * $scale;
    $newHeight = $imageHeight * $scale;

    // Calculates the position to center the image in the PDF
    $x = ($pdf->GetPageWidth() - $newWidth) / 2;
    $y = ($pdf->GetPageHeight() - $newHeight) / 2;

    $pdf->SetXY($x, $y);
    $pdf->Image($imageFile, $x, $y, $newWidth, $newHeight);
    $pdf->Output(DOWNLOAD_FOLDER . "/$pdfFileOutput.pdf", 'F');
    return DOWNLOAD_FOLDER . "/$pdfFileOutput.pdf";
}

$nameCertificate = "Walter";
$certificatePathJPG = makeCertificate($nameCertificate);
$certificatePathPDF = convertJPGtoPDF($certificatePathJPG, $nameCertificate, true);
