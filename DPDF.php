<?php

namespace DPDF;
use FPDF;
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
    /**
     * En ocasiones solo se quiere cambiar el font style y no toda la fuente
     *
     * @return void
     */
    public function SetFontStyle($style){
        $this->FontStyle = $style;
       // die($this->lastSize);
       #print_r([$this->FontFamily,$style,$this->lastSize,$this->lastFont]);
       #die();
        $this->SetFont($this->lastFont,$style,$this->lastSize);
        
    }
    public $lastSize=0;
    public $lastFont='';
    public function SetFont($family, $style='', $size=0){
        
        $this->lastSize=$size;
        $this->lastFont=$family;
        parent::SetFont($family, $style, $size);
    }
    
    public function draw($header){
        $w=$this->GetWithWithoutMargin();
        $wc=$w/count($header);
    #print_r($pdf);
    #$currentFont=$this->SetFontSize
    foreach ($header as  $value) {
        if(!is_array($value)){
            $value=['text'=>$value];
        }
        $align=$value['align']??'C';
        $heigth=$value['heigth']??10;
        $border=$value['border']??1;
        $fill=$value['fill']??false;

        if(!empty($value['fill'])){
            $this->SetFillHexadecimalColor($fill);
            $fill=true;
        }

        if(!empty($value['weight'])){
            $w=$value['weight'];
            $pw=$this->GetPageWidth();
            $wc=($w/100)*$pw; #sacamos el valor relaivo
        }

        if (!empty($value['style'])) {
           # $style=
             $this->SetFontStyle($value['style']);
          # $this->SetFont('Arial',$value['style'],18);
        }
        
    
        $this->Cell($wc,$heigth,$value['text'],
        $border,#border
        0,# con esto hace que sea una linea seguida
        $align,
        $fill
        );
        if(!empty($value['style'])){
            #$this->SetFontStyle($value['style']);
            $this->SetFontStyle('');
        }
    }
    }
}
