<?php

#require 'vendor/autoload.php';
require './fpdf/fpdf.php';
#use Fpdf\Fpdf;

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
}

$pdf = new DPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);


//aqui iniciiaria la tabla:
$w=$pdf->GetPageWidth();
$w=$w-($pdf->GetLMargin()+$pdf->GetRMargin());
#$w=$w-15;
$header=["","hahaha","sda","dsadsa","dsasad","dsadsads","dsadsa","dsadsa"];
$header=[[
    "text"=>"",
    "align"=>"C",
    "size"=>'20'

]];
$wc=$w/count($header);
#print_r($pdf);
foreach ($header as  $value) {
    $pdf->Cell($wc,10,$value,1,
    0,# con esto hace que sea una linea seguida
    'C');    
}

$pdf->Output();

