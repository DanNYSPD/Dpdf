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
        $style=$style=='N'?'':$style;
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
        $fontModified=false;
        $currentFont=$lastFont;
        /**
         * @var  LabelAndText $specialObject
         */
        $specialObject=null;
        if(!is_array($value)){
            if(\is_string($value)){
               $value=['text'=>$value]; #if it is text just tranform it to an array
            }else if(self::isLabel($value)){
                #if it is a label ..
                /**
                 * @var LabelAndText $specialObject
                 */
                $specialObject=$value;
                $specialObject->label;
                $value=$specialObject->label;
            }
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
            $value['style']=$value['style']=='N'?'':$value['style']; # fpdf doesn't have N , it's '' which is normal, so I use N to represent this style, the reason is because I found more  intuitive use N , and because '' gives true in emtpy.
            $currentFont['style']=$value['style'];
             #$this->SetFontStyle($value['style']);# something wrong happen here so my new idea is just to copy the last font and modify it according to this values.
             
          $this->SetFont($currentFont['family'],$currentFont['style'],$currentFont['size']);
          $fontModified=true;# I set this flag to indicate that a new font was setted up.
          # $this->SetFont('Arial',$value['style'],18);
        }
        if(!empty($value['textColor'])){
            $this->SetTextHexColor($value['textColor']);
        }
        if(!empty($value['size'])){
            $this->SetFontSize($value['size']);
            $fontModified=true;
        }
        
        #$lnItem=$specialObject==null?$ln:0;
        #$y=$this->GetY();
        $this->Cell($wc,$height,$value['text'],
            $border,#border
            $ln,# 0=con esto hace que sea una linea seguida
            #$lnItem,# 0=con esto hace que sea una linea seguida
            $align,
            $fill
        );
        if($specialObject!=null){
            #debo tratar de que se mantenga en la misma linea, necesito el withd del text y no el de la celda
            #note, for some reason I cannot put the text with Text function in a precisely way, the coordenades fail, so I have to use cell again.
            #
            $currentFont['style']=$specialObject->text['style']??$currentFont['style'];
            $alignSpecial=$specialObject->text['align']??'C';
            $strWith=$this->GetStringWidth($value['text']);

            $this->SetFont($currentFont['family'],$currentFont['style'],$currentFont['size']);
            #$this->Text($this->GetX()+$wc,$this->GetY()-$height/2,$specialObject->text['text']);
            #$this->Text($this->GetX()+($wc-$strWith)/2,$y,$specialObject->text['text']);
            #I take the X coordenade before to put the text because  the cell function modifies this value, and it's necesary
            #to restore it as if none cell function was called it.
            $x= $this->GetX();
            $this->SetXY(
                $this->GetX()+$strWith+10,# falta implmentar left, rigth
                $this->GetY()-$height
            );
            /**
             * I calculate the cell width (is the same less the label width , less the this text width)
             */
            $cWidth=$wc-($strWith+$this->GetStringWidth($specialObject->text['text']));
            $this->Cell(
                $cWidth,# the with must not include the wtih
            $height,$specialObject->text['text'],
            $border,#border
            $ln,
            $alignSpecial
        );
            $this->SetX($x);# restore the X coordenate
        }
        
        if($fontModified){            
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
    public static function Label($config):LabelAndText{
        $label= new LabelAndText($config['label'],$config['text']);
        return $label;
    }
    public static function isLabel($v):bool{
        return $v instanceof LabelAndText;
    }
    /**
     * Adds a new cell with the Page Width as width (so it fill the whole row)
     *
     * @param [type] $text
     * @param integer $height
     * @param integer $border
     * @return void
     */
    public function CellRow($text,$height=7,$border=0){
        
        $this->Cell($this->GetWithWithoutMargin(),$height,$text,$border,1);
    }

    public function AddImageFromBase64(string $base64,$x=null, $y=null, $w=0, $h=0, $type='', $link=''){
        $base="data:image/jpeg;base64,";      
        #need to add a condition to detect the mime type  
        $this->Image($base.$base64,$x,$y,$w,$h,$type,$link);
    }
}

class LabelAndText{
    public $label;
    public $text;
    public function __construct($label,$text){
        $this->label=$label;
        $this->text=$text;
    }
}