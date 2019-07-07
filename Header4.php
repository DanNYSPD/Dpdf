<?php

#require 'vendor/autoload.php';
require './fpdf/fpdf.php';
require 'DPDF.php';
#use Fpdf\Fpdf;
use DPDF\DPDF;

$pdf = new DPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);

$header=[
    [
        "text"=>"hdhsadsa",
        "align"=>"C",
        "size"=>'20',
        'weight'=>10,
        'fill'=>'#FF5733', # cuando tiene este valor se rellena de este color
    ],
    [
        "text"=>"dsadsa",
        "align"=>"C",
        "size"=>'20',
        "heigth"=>5,
        "border"=>1,
        'weight'=>30 # relativo es el porcentaje!!
    ],
    'heheh'
];

$pdf->draw($header);
$pdf->Output();

