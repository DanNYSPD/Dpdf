<?php

#require 'vendor/autoload.php';
require './fpdf/fpdf.php';
#use Fpdf\Fpdf;
/**
 * @author Daniel Hernandez <daniel.hernanandez.job@gmail.com>
 * 
 * This Library is under MIT License.
 * 
 */
class DPDF extends FPDF{
    /**
     * Convierte un color hexadecimal a RGB
     *
     * @param [type] $hexColor
     * @return void
     */
    public function HextoRGB($hexColor){
        $len=strlen($hexColor);

        if($len==3||$len==4){
            list($r, $g, $b) = sscanf($hexColor, "#%1x%1x%1x");
        }elseif(in_array($len,[6,7])){
            
            list($r, $g, $b)   = sscanf($hexColor, "#%02x%02x%02x");
        }else{
            die("error");
        }   
        return [$r,$g,$b];
    }
    public function SetFillHexadecimalColor($hexcolor){
       $this->SetFillColor(...$this->HextoRGB($hexcolor));
    }
    public function SetDrawHexColor($hexcolor){
        $this->SetDrawColor(...$this->HextoRGB($hexcolor));
    }
    public function SetTextHexColor($hexcolor){
        $this->SetTextColor(...$this->HextoRGB($hexcolor));;
    }
    public function GetRMargin(){
        return $this->rMargin;
    }
    public function GetLMargin(){
        return $this->lMargin;
    }
    public function GetWithWithoutMargin(){
        return $this->GetPageWidth()-($this->GetLMargin()+$this->GetRMargin());
    }
}

$pdf = new DPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);


//aqui iniciiaria la tabla:
$w=$pdf->GetPageWidth();
$w=$pdf->GetWithWithoutMargin();
#$w=$w-15;
$header=["","hahaha","sda","dsadsa","dsasad","dsadsads","dsadsa","dsadsa"];
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

$pdf->Output();

