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
        'textColor'=>'#FFFFFF',# allows to change the color for this item, 
        'fill'=>'#5C1708',
        'weight'=>30 # relativo es el porcentaje!!
    ],
    'heheh'
];


$pdf->draw($header);
$pdf->Ln();

$arrayData=[['jaja','hehehe',100],['jaja','hehehe',100],['jaja','hehehe',100],['jaja','hehehe',100],['jaja','hehehe',100]];
foreach ($arrayData as $value) {
    $tbody=[
        [
            "text"=>$value[0],
            "weight"=>10,
            'font'=>'',
            'style'=>'I' #modifies the style (the font is maintained)
        ],
        [
            "text"=>$value[1],
            "weight"=>30,
        ],
        [
            "text"=>$value[2],
            #"weight"=>10,
        ]
    ];
    $pdf->draw($tbody);
    $pdf->Ln();
}

$pdf->Output();

