<?php

#require 'vendor/autoload.php';
require './fpdf/fpdf.php';
require 'DPDF.php';
#use Fpdf\Fpdf;
use DPDF\DPDF;

$pdf = new DPDF();
$pdf->SetFont('Arial','B',12);
$pdf->setHeader(function (DPDF $pdf){

    $pdf->column([
        DPDF::Label(
            [
                'label'=>[
                    'text'=>'name:',
                    'align'=>'L',
                    'weight'=>28,
                    'border'=>false,
                    'style'=>'B',
                    'height'=>3
                ],
                'text'=>[
                    'text'=>'daniel',
                    'style'=>'N',
                    'align'=>'R'

                ]
            ]
        ),       
        [
            'text'=>'email simple:',
            'align'=>'L',
            'weight'=>28,
            'border'=>false,
            'style'=>'N',
            'height'=>3

        ],
        [
            "text"=>"hdhsadsa",
            "align"=>"L",
            "size"=>7,
            'style'=>'N',
            'weight'=>10,
            'height'=>5,
            'border'=>false,
            'fill'=>'#FF5733',
        ]
    ]);
    
    $pdf->SetXY(($pdf->GetWithWithoutMargin()/3)*1,10);
    

    $pdf->column([
        [
            'text'=>'name:',
            'align'=>'L',
            'weight'=>30

        ],
        [
            'text'=>'email:',
            'align'=>'L',
            'weight'=>30

        ],
        [
            "text"=>"hdhsadsa",
            "align"=>"L",
            "size"=>10,
            'weight'=>10,
            'height'=>5,
            'border'=>false,
            'fill'=>'#FF5733',
        ]
    ]);
    $heigh=$pdf->GetY()-5;#this five is because the last height(cell adds height to y when ln>0)
    $pdf->SetXY(($pdf->GetWithWithoutMargin()/3)*2,10);
    try{
        #https://stackoverflow.com/questions/18484632/print-base64-coded-image-into-a-fpdf-document
        #https://www.adobe.com/content/dam/acom/en/devnet/acrobat/pdfs/PDF32000_2008.pdf
        #https://www.adobe.com/content/dam/acom/en/devnet/pdf/pdfs/pdf_reference_archives/PDFReference.pdf
    $pdf->AddImageFromBase64(
        base64_encode(file_get_contents('logo.jpeg')),
        $pdf->GetX(),
        $pdf->GetY(),
        ($pdf->GetWithWithoutMargin()/3),
        $heigh,
        'jpeg'
    );
}catch(Exception $e){
    print_r($e);
}

   # $pdf->draw();
    $pdf->Ln(20);


});
$pdf->setFooter(function(DPDF $pdf){
    // Position at 1.5 cm from bottom
    $pdf->SetY(-15);
    $pdf->SetFont('Arial','I',8);
    $pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}',0,0,'C');

   // $pdf->Text(0,10,"Page".$pdf->PageNo().'/{nb}',0,0,'C');
});
$pdf->AddPage();


$header=[
    [
        "text"=>"hdhsadsa",
        "align"=>"C",
        "size"=>15,
        'weight'=>10,
        'fill'=>'#FF5733', # cuando tiene este valor se rellena de este color
    ],
    [
        "text"=>"dsadsa",
        "align"=>"C",
        "size"=>'20',
        "height"=>5,
        "border"=>1,
        'textColor'=>'#FFFFFF',# allows to change the color for this item, 
        'fill'=>'#5C1708',
        'weight'=>30 # relativo es el porcentaje!!
    ],
    'heheh'
];

$pdf->CellRow('Table with XXXXXXX ');
#or
$pdf->draw([[
    'text'=>'titulo',
    'weight'=>100,
    'border'=>false
]],1);
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

