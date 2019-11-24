<?php
use FPDF;
use DPDF\DPDF;
use Exception;
use DPDF\Concerns\ImgTrait;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testIsInterlaced()
    {
        $g = new class { use ImgTrait; }; // anonymous class
        $res=$g->isInterlaced(__DIR__.'/assets/image_png.png');
        $this->assertEquals(false,$res);
    }
    public function testIsInterlacedTrue()
    {
        $g = new class { use ImgTrait; }; // anonymous class
        $res=$g->isInterlaced(__DIR__.'/assets/interlaced.png');
        $this->assertEquals(true,$res);
    }

    public function testImageInterlacedPngFPDF(){
        $pdf= new FPDF();
        $pdf->AddPage();
        $this->expectException(Exception::class);
        $pdf->Image(__DIR__.'/assets/interlaced.png',10,10,200,200);
        $pdf->Output('f','image.pdf');
    }
    public function testImagePng(){
        $pdf= new DPDF();
        $pdf->AddPage();
        $pdf->Image(__DIR__.'/assets/interlaced.png',10,10,200,200);
        $pdf->Output('f','image.pdf');
    }
   
}
