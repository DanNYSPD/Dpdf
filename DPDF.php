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
    public function SetTextColor($r, $g=null, $b=null){
        $this->lastTextColor=[$r,$g,$b];
        parent::SetTextColor($r,$g,$b);
    }
    /**
     * Last RGB color used by SetTextColor method and SetTextHexColor
     *
     * @var array
     */
    public $lastTextColor=[];
    public function GetLastTextColor(){
        return $this->lastTextColor;
    }
    public function HasLastTextColor():bool{
        return !empty($this->lastTextColor);
    }
    /**
     * Predominant and default color
     *
     * @var array
     */
    public $defaultTextColor=[0,0,0];
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
       
        #$this->SetFont($this->lastFont,$style,$this->lastSize);
        $this->SetFont($this->lastFont['family'],$style,$this->lastFont['size']);
        
    }
    #public $lastSize=0;
    public $lastFont=[];
    public function GetLastFont(){
        return $this->lastFont;
    }
    public function SetFont($family, $style='', $size=0){
        
        #$this->lastSize=$size;
        #$this->lastFont=$family;
        $this->lastFont=['family'=>$family,'style'=>$style,'size'=>$size];
        parent::SetFont($family, $style, $size);
    }
    
    public function draw($header,$ln=0){
        $w=$this->GetWithWithoutMargin();
        $wc=$w/count($header);
    
    $lastFont=$this->GetLastFont();
    if($this->HasLastTextColor()){
        $lastTextColor=$this->GetLastTextColor();
    }else{
        $lastTextColor=$this->defaultTextColor;
    }
    $lastSize=$this->FontSizePt;
    
    foreach ($header as  $value) {
        if(!is_array($value)){
            $value=['text'=>$value];
        }
        $align=$value['align']??'C';
        $height=$value['height']??10;
        $border=$value['border']??1;
        $fill=$value['fill']??false;

        if(!empty($value['fill'])){
            $this->SetFillHexadecimalColor($fill);
            $fill=true;
        }

        if(!empty($value['weight'])){
            $w=$value['weight'];
            $pw=$this->GetWithWithoutMargin();# must not consider margin
            $wc=($w/100)*$pw; #sacamos el valor relaivo
        }

        if (!empty($value['style'])) {
           # $style=
             $this->SetFontStyle($value['style']);
          # $this->SetFont('Arial',$value['style'],18);
        }
        if(!empty($value['textColor'])){
            $this->SetTextHexColor($value['textColor']);
        }
        if(!empty($value['size'])){
            $this->SetFontSize($value['size']);
        }
        
    
        $this->Cell($wc,$height,$value['text'],
        $border,#border
        $ln,# 0=con esto hace que sea una linea seguida
        $align,
        $fill
        );
        if(!empty($value['style'])){
            #$this->SetFontStyle($value['style']);
            #$this->SetFontStyle('');
            $this->SetFont(
                $lastFont['family'],
                $lastFont['style'],
                $lastFont['size']
            );
        }
        if (!empty($value['textColor'])) {
          #  \print_r($lastTextColor);
            $this->SetTextColor(
                $lastTextColor[0],
                $lastTextColor[1],
                $lastTextColor[2]
            );
        }
        if (!empty($value['size'])) {
            $this->SetFontSize($lastSize);
        }
    }
    }
    public function column($column,$mode=2){
        $this->draw($column,$mode);#2= above (default) or 1, nextLine
    }

    public $headerCallback=null;
    public $footerCallback=null;
    /**
     * Sets header callback.
     * This must be setted before calling AddPage, otherwise this callback will not be taken  for the page
     * @param [type] $callback
     * @return void
     */
    public function setHeader($callback){
        //die();
       # echo "yo";
        $this->headerCallback=$callback;
    }
    
    public function setFooter($callback){
        $this->footerCallback=$callback;
    }
    
    public function Header()
    {
        #echo "me";
       // \var_dump($this->headerCallback);
        if($this->headerCallback!==null){
           # die();
           $callback=$this->headerCallback;
            $callback($this);
        }
    }

    public   function Footer()
    {
        if($this->footerCallback!==null){
            $callback=$this->footerCallback;
            $callback($this);
        }
    }
    
}
