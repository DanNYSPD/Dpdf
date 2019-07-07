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
function draw($header,$pdf){
    $w=$pdf->GetWithWithoutMargin();
    $wc=$w/count($header);
#print_r($pdf);
foreach ($header as  $value) {
    if(!is_array($value)){
        $value=['text'=>$value];
    }
        $align=$value['align']??'C';
        $heigth=$value['heigth']??10;
        $border=$value['border']??1;
        $fill=$value['fill']??false;

        if(!empty($value['fill'])){
            $pdf->SetFillHexadecimalColor($fill);
            $fill=true;
        }

        if(!empty($value['weight'])){
        $w=$value['weight'];
        $pw=$pdf->GetPageWidth();
        $wc=($w/100)*$pw; #sacamos el valor relaivo
        }
    

    $pdf->Cell($wc,$heigth,$value['text'],
    $border,#border
    0,# con esto hace que sea una linea seguida
    $align,
    $fill
    );    
}
}
draw($header,$pdf);
$pdf->Output();

