<?php declare(strict_types=1);
namespace DPDF;

use FPDF;
use tFPDF;
/**
 * @author Daniel Hernandez <daniel.hernanandez.job@gmail.com>
 *
 * This Library is under MIT License.
 *
 */
#class DPDF extends tFPDF{
class DPDF extends FPDF{

    public $autoUTF8=true;

    /**
     * Convierte un color hexadecimal a RGB
     *
     * @param [type] $hexColor
     * @return void
     */
    public function HextoRGB($hexColor)
    {
        $len = strlen($hexColor);

        if ($len == 3 || $len == 4) {
            list($r, $g, $b) = sscanf($hexColor, "#%1x%1x%1x");
        } elseif (in_array($len, [6, 7])) {

            list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
        } else {
            die("error");
        }
        return [$r, $g, $b];
    }
    public function SetFillHexadecimalColor($hexcolor)
    {
        $this->SetFillColor(...$this->HextoRGB($hexcolor));
    }
    public $lastHexDrawColor='';
    public function SetDrawHexColor($hexcolor)
    {
        $this->lastHexDrawColor=$hexcolor;
        $this->SetDrawColor(...$this->HextoRGB($hexcolor));
    }
    public function SetTextHexColor($hexcolor)
    {

        $this->SetTextColor(...$this->HextoRGB($hexcolor));
    }
    public function SetTextColor($r, $g = null, $b = null)
    {
        $this->lastTextColor = [$r, $g, $b];
        parent::SetTextColor($r, $g, $b);
    }
    /**
     * Last RGB color used by SetTextColor method and SetTextHexColor
     *
     * @var array
     */
    public $lastTextColor = [];
    public function GetLastTextColor()
    {
        return $this->lastTextColor;
    }
    public function HasLastTextColor(): bool
    {
        return !empty($this->lastTextColor);
    }
    /**
     * Predominant and default color
     *
     * @var array
     */
    public $defaultTextColor = [0, 0, 0];
    public function GetRMargin()
    {
        return $this->rMargin;
    }
    public function GetLMargin()
    {
        return $this->lMargin;
    }
    public function GetWithWithoutMargin()
    {
        return $this->GetPageWidth() - ($this->GetLMargin() + $this->GetRMargin());
    }

    /**
     * En ocasiones solo se quiere cambiar el font style y no toda la fuente
     *
     * @return void
     */
    public function SetFontStyle($style)
    {
        $this->FontStyle = $style;

        // die($this->lastSize);
        #print_r([$this->FontFamily,$style,$this->lastSize,$this->lastFont]);
        #die();

        #$this->SetFont($this->lastFont,$style,$this->lastSize);
        $this->SetFont($this->lastFont['family'], $style, $this->lastFont['size']);

    }
    #public $lastSize=0;
    public $lastFont = [];
    public function GetLastFont()
    {
        return $this->lastFont;
    }
    public function SetFont($family, $style = '', $size = 0)
    {
        $style = $style == 'N' ? '' : $style;
        #$this->lastSize=$size;
        #$this->lastFont=$family;
        $this->lastFont = ['family' => $family, 'style' => $style, 'size' => $size];
        parent::SetFont($family, $style, $size);
    }

    public $defaultBorder = 0;
    public function setDefaultBorder($value)
    {
        $this->defaultBorder = $value;
    }
    public $defaultHeight = 8;
    public function setDefaultHeight($value)
    {
        $this->defaultHeight = $value;
    }
    public $defaultAlign = 'L';
    public function setDefaultAlign($value)
    {
        $this->defaultAlign = $value;
    }
    public $defaultHexDrawColor='#000000';
    public function setDefaultHexDrawColor($value):void{
        $this->defaultHexDrawColor=$value;
    }
    /**
     * Undocumented function
     *
     * @param [type] $header
     * @param integer $ln
     * @param integer $autoLines 0= none, L;B,T,R o combinaciones
     * @return void
     */
    public function draw($header, $ln = 0,string $autoLines='')
    {
        $w = $this->GetWithWithoutMargin();
        $wc = $w / count($header);

        $lastFont = $this->GetLastFont();
        if ($this->HasLastTextColor()) {
            $lastTextColor = $this->GetLastTextColor();
        } else {
            $lastTextColor = $this->defaultTextColor;
        }
        $lastSize = $this->FontSizePt;

        $lastXMulticell = null; #solo util cuando se desean columnas,ln=2
        $lastYMulticell = null; #solo util cuando se desean filas,ln=0
        $startY = $maxY=$this->GetY();
        $startX =$maxX=$this->GetX();

        $lastDrawColor=$this->lastHexDrawColor;

        $arrayXYRigthPositions=[];
        foreach ($header as $value) {
            $fontModified = false;
            $currentFont = $lastFont;
            /**
             * @var  LabelAndText $specialObject
             */
            $specialObject = null;
            if (!is_array($value)) {
                if (\is_string($value)) {
                    $value = ['text' => $value]; #if it is text just tranform it to an array
                } else if (self::isLabel($value)) {
                    #if it is a label ..
                    /**
                     * @var LabelAndText $specialObject
                     */
                    $specialObject = $value;
                    $specialObject->label;
                    $value = $specialObject->label;
                }
            }
            $align = $value['align'] ?? $this->defaultAlign;
            $height = $value['height'] ?? $this->defaultHeight;
            $border = $value['border'] ?? $this->defaultBorder;
            $fill = $value['fill'] ?? false;
            $auto = $value['auto'] ?? false;
            $drawColor=$value['drawColor']??$this->defaultHexDrawColor;

            if($this->autoUTF8&&isset($value['text'])){
                $value['text']=utf8_decode($value['text']);
            }

            if (!empty($value['fill'])) {
                $this->SetFillHexadecimalColor($fill);
                $fill = true;
            }
            if (!empty($value['drawColor'])) {
                $this->SetDrawHexColor($drawColor);
                
            }

            if (!empty($value['weight'])) {
                $w = $value['weight'];
                $pw = $this->GetWithWithoutMargin(); # must not consider margin
                $wc = ($w / 100) * $pw; #sacamos el valor relaivo
            }

            if (!empty($value['style'])) {
                $value['style'] = $value['style'] == 'N' ? '' : $value['style']; # fpdf doesn't have N , it's '' which is normal, so I use N to represent this style, the reason is because I found more  intuitive use N , and because '' gives true in emtpy.
                $currentFont['style'] = $value['style'];
                #$this->SetFontStyle($value['style']);# something wrong happen here so my new idea is just to copy the last font and modify it according to this values.

                $this->SetFont($currentFont['family'], $currentFont['style'], $currentFont['size']);
                $fontModified = true; # I set this flag to indicate that a new font was setted up.
                # $this->SetFont('Arial',$value['style'],18);
            }
            if (!empty($value['textColor'])) {
                $this->SetTextHexColor($value['textColor']);
            }
            if (!empty($value['size'])) {
                $this->SetFontSize($value['size']);
                $fontModified = true;
            }
             

            #$lnItem=$specialObject==null?$ln:0;
            #$y=$this->GetY();
            if (!$auto) {
                #si hubo un multicell atras de este celda y esta en modo columnas, alinea en el eje X.
                if ($ln == 2 && $lastXMulticell != null) #si desea que sean columnas(debe manetenerse el mismo X)
                {
                    $this->SetX($lastXMulticell);
                    $lastXMulticell = null;
                }else if($ln==0 && $lastXMulticell!=null){ #si dese que sean rows(debe mantenerse el mismo Y y dexplazarse X)
                    #$this->SetY($lastYMulticell+50);
                    #$lastYMulticell=null;
                    #$this->SetX($lastXMulticell);
                    $this->SetXY($lastXMulticell,$lastYMulticell);

                    $lastXMulticell = null;
                    $lastYMulticell=null;
                }
                if(!isset($value['text'])){
                    $value['text']='';
                }
                if(isset($value['imageB64'])){
                    $pdf->AddImageFromBase64($value['imageB64'],
                        $pdf->GetX()+10,
                        $pdf->GetY(),
                        $wc,
                        $height,
                        'jpeg'
                    );

                }else{
                    $this->Cell($wc, $height, $value['text'],
                        $border, #border
                        $ln, # 0=con esto hace que sea una linea seguida
                        #$lnItem,# 0=con esto hace que sea una linea seguida
                        $align,
                        $fill
                    );
                }
            } else {
                if(isset($value['offsety'])){
                     
                    $this->setY($this->getY()+$value['offsety'],false);
                }
                #auto
                if ($ln == 2) #si desea que sean columnas
                {
                    //quiero conservar x para que esten alineadas como columnas:
                    $lastXMulticell = $this->getX();
                    $lastYMulticell=$this->GetY();

                }else if($ln==0){
                    # I save X and Y for the next cell
                    $lastXMulticell = $this->getX()+$wc; 
                    $lastYMulticell=$this->GetY();
                }
                #divido el largo del string por el cnago para obtener cuandos "row tendra", se umuitiñplca por height y se obtiene el heigut que tendra
                #$finalProxHeight = (ceil(($this->GetStringWidth($value['text']) / $wc)) * $height);
                $rows=$this->GetStringWidth($value['text']) / $wc;
                $rows+=substr_count( $value['text'], "\n" ); # se le suman los salto de linea
                #sino llega a 1, entonces por defecto ocupara una row
                $finalProxHeight = (floor(($rows>1?$rows:1)) * $height); #calculo el height
               # $this->Line($this->GetX(),$this->GetY(),$this->GetX()+50,$this->GetY()+$finalProxHeight);
                if($height==$finalProxHeight){
                    $this->MultiCell($wc, $height/1, $value['text'],
                    $border,
                    $align,
                    $fill
                );
                }else{
                    $this->MultiCell($wc, ($height/2)+.5, $value['text'],
                    $border,
                    $align,
                    $fill
                    );
                    $currentY=$this->getY();
                    $value['lastY']= $currentY;
                }

               
                 
            }
            if ($specialObject != null) {
                #debo tratar de que se mantenga en la misma linea, necesito el withd del text y no el de la celda
                #note, for some reason I cannot put the text with Text function in a precisely way, the coordenades fail, so I have to use cell again.
                #
                $currentFont['style'] = $specialObject->text['style'] ?? $currentFont['style'];
                $currentFont['size'] = $specialObject->text['size'] ?? $currentFont['size'];
                $alignSpecial = $specialObject->text['align'] ?? 'C';
                $strWith = $this->GetStringWidth($value['text']);

                $this->SetFont($currentFont['family'], $currentFont['style'], $currentFont['size']);
                #$this->Text($this->GetX()+$wc,$this->GetY()-$height/2,$specialObject->text['text']);
                #$this->Text($this->GetX()+($wc-$strWith)/2,$y,$specialObject->text['text']);
                #I take the X coordenade before to put the text because  the cell function modifies this value, and it's necesary
                #to restore it as if none cell function was called it.
                $x = $this->GetX();
                $this->SetXY(
                    $this->GetX() + $strWith + 10, # falta implmentar left, rigth
                    $this->GetY() - $height
                );
                /**
                 * I calculate the cell width (is the same less the label width , less the this text width)
                 */
                $cWidth = $wc - ($strWith + $this->GetStringWidth($specialObject->text['text']));


                if(isset($value['imageB64'])){
                    $pdf->AddImageFromBase64($value['imageB64'],
                        $pdf->GetX()+10,
                        $pdf->GetY(),
                        $cWidth,
                        $height,
                        'jpeg'
                    );

                }else{
                     $this->Cell(
                    $cWidth, # the with must not include the wtih
                    $height, $specialObject->text['text'],
                    $border, #border
                    $ln,
                    $alignSpecial
                    );
                }
                $this->SetX($x); # restore the X coordenate
            }

            if ($fontModified) {
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
            if(!empty($value['drawColor'])){
                
                $this->SetDrawHexColor($lastDrawColor);
            }
            $maxY=($maxY<$this->GetY()?$this->GetY():$maxY);
            $maxX=($maxX<$this->GetX()?$this->GetX():$maxX);
            // I save the last positions of evey cell
            $arrayXYRigthPositions[]=[$this->GetX(),$this->GetY()];
        }
        if($ln==0){
            ##bottom
            if(stripos($autoLines,'B')!==false){
                $this->Line($startX,$maxY,$maxX,$maxY);
            }
            /*
            if(stripos($autoLines,'L')!==false){
                $this->Line($startX,$startY,$startX,$maxY);
            }
            if(stripos($autoLines,'R')!==false){
                $this->Line($maxX,$startY,$maxX,$maxY);
            }*/
           
            foreach ($arrayXYRigthPositions as $coord) {
                 # due to multicell heigth change to much 
                if(stripos($autoLines,'L')!==false){
                    $this->Line($coord[0],$startY,$coord[0],$maxY);
                }
                if(stripos($autoLines,'R')!==false){
                   // $this->Line($maxX,$startY,$maxX,$maxY);
                }
            }
        }
    }
    public function inColumn($column, $mode = 2)
    {
        $this->draw($column, $mode); #2= above (default) or 1, nextLine
    }

    public $headerCallback = null;
    public $footerCallback = null;
    /**
     * Sets header callback.
     * This must be setted before calling AddPage, otherwise this callback will not be taken  for the page
     * @param [type] $callback
     * @return void
     */
    public function setHeader($callback)
    {
        //die();
        # echo "yo";
        $this->headerCallback = $callback;
    }

    public function setFooter($callback)
    {
        $this->footerCallback = $callback;
    }

    public function Header()
    {
        #echo "me";
        // \var_dump($this->headerCallback);
        if ($this->headerCallback !== null) {
            # die();
            $callback = $this->headerCallback;
            $callback($this);
        }
    }

    public function Footer()
    {
        if ($this->footerCallback !== null) {
            $callback = $this->footerCallback;
            $callback($this);
        }
    }
    public static function Label($config): LabelAndText
    {
        $label = new LabelAndText($config['label'], $config['text']);
        return $label;
    }
    public static function isLabel($v): bool
    {
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
    public function CellRow($text, $height = 7, $border = 0)
    {

        $this->Cell($this->GetWithWithoutMargin(), $height, $text, $border, 1);
    }

    public function AddImageFromBase64(string $base64, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '')
    {
        #https://stackoverflow.com/questions/2269363/put-png-over-a-jpg-in-php
        #$base = "data:image/jpeg;base64,";
        /*
        $base = "data:image/$type;base64,";
        #need to add a condition to detect the mime type
        $this->Image($base . $base64, $x, $y, $w, $h, $type, $link);
        */

        $dataUrlStart='data:image';#this is how a dataUri image start
        $base = "data:image/jpeg;base64,";
        $base = "data:image/png;base64,";
        if(\substr($base64,0,\strlen($dataUrlStart))!=$dataUrlStart){
            $base64= $base . $base64;
        }

        #detect type base on dataUri
        if($type==''){
            $start=\strlen($dataUrlStart)+1;
            $resPosition=strpos($base64,';',$start); 
            if($resPosition===FALSE){
                throw new Exception("Couldn't find the type of the image", 1);            
            }
            $type=  substr($base64,$start,$resPosition-$start);
        }

        #need to add a condition to detect the mime type
        $this->Image($base64, $x, $y, $w, $h, $type, $link);
    }

    public function AddX(int $xRelative)
    {
        $this->SetX($this->GetX() + $xRelative);
    }
    public function AddY(int $yRelative)
    {
        $this->SetY($this->GetY() + $yRelative);
    }
    public function GetPageWidth()
    {
        // Get current page width
        return $this->w;
    }
    public function CalculateXOffSet($px) {
        $pw = $this->GetWithWithoutMargin();       
        $newX=(($px/100)*$pw) +$this->lMargin;
        return $newX;
    }
    public function SetXOffSet($px){
        $newX=$this->CalculateXOffSet($px);
        $this->SetX(  $newX);        
    }
    /**
     * Recibe un offset y lo traduce a px
     * nno testetado
     * @param integer $offset
     * @return void
     */
    public function GetXOffSet($offset=0){
        
        $pw = $this->GetWithWithoutMargin();       
       # $newX=(($px/100)*$pw) +$this->lMargin;
       # $newX-$this->lMargin=(($px/100)*$pw) ;
        $px=100*(($offset-$this->lMargin)/$pw);
        return $px;
                
    }
    /**
     * px wegith 
     *
     * @param [type] $px
     * @param integer $calcualed
     * @return float
     */
    public function CalculateRealSize(float $px,$calcualed=-1):float{
        $pw = $calcualed>0?$calcualed:$this->GetWithWithoutMargin();       
        return $newX=(($px/100)*$pw);
    }
    public static function Vertical($config,$children):Container{
        return new Vertical($config,$children);
    }
    public static function Horizontal($config,$children):Container{
        return new Horizontal($config,$children);
    }
    public static function Cello($config):Cell{
        return new Cell($config);
    }
    public static function Imageo($config):Image{
        return new Image($config);
    }

    public  function Table( $container){
        //print_r($container);
        if($container instanceof Vertical||$container instanceof Horizontal){ //direct child will have the this form
            #antes de que inice respaldo
            $x=$this->GetX();
            #$withdParent= $this->CalculateRealSize($container->config['weight']);
            $withdParent= ($container->config['weight']);
            $this->draw( ## this will draw the borders!!
                [
                   $container->config
                ]
            );
            $numChildren=count($container->children);
            $this->SetX($x);
            $previousSibling=null;
            ##foreach ($container->children as $child) {
              #antes de la iteracion recobro el default  
              for($i=0;$i<$numChildren;$i++){
                $child=$container->children[$i];

                if(isset($child->config['weight'])){
                    $child->config['weight']= $child->RecalculateWeightFromParent();
                }else{
                   #when there is not a weight and its parent is Horizontal , then calculate it from its parent's tWeight divided into the children number
                   if($child->IsParentHorizontal()){
                        $child->config['weight']= $child->parent->getWeight()/$child->parent->CountChildren();
                    }else{ #when is parent is vertical we want to fill by default all its parent weight
                        $child->config['weight']=$child->parent->getWeight();
                    
                    }  

                }
                $y=$this->GetY();
                $x=$this->GetX();
              
                if($child instanceof Container){
                    $currentX=$this->GetX();
                    $currentY=$this->GetY();
                    $heightCellBeforeContainer=null;
                    if(isset($child->config['cell_height'])){
                        $heightCellBeforeContainer =$this->defaultHeight;
                        $this->defaultHeight=$child->config['cell_height'];
                    }
                    $this->Table($child);

                    #restauramos
                    if(isset($child->config['cell_height'])){
                        $this->defaultHeight=$heightCellBeforeContainer;
                    }

                    #dependiendo de si es horizontal o vertical, restaurare sus valores
                    if($child->IsParentHorizontal()){#se mueve solo en X
                        #calculo el width que pinto para ese chiild y hago el desplazamiento en X
                         $this->SetX($currentX+$this->CalculateRealSize($child->getWeight()));
                        #if($child->IsHorizontal()){ #solo si es horizontal y su padre es horizontal resstablece en el eje Y. 
                            $this->SetXY($currentX+$this->CalculateRealSize($child->getWeight()),
                            $currentY);
                        #}
                    }else if ($child->IsParentVertical()){ #se mueve en Y                       
                        #si tiene hermano next(aun sin renderizar) y es diferente de vertical, agregale salto de linea
                        if($i+1<$numChildren){                       
                            $next=$container->children[$i+1];
                            if(!$next->IsVertical()){
                                $this->Ln();
                            }
                        }
                        
                        $this->SetX($currentX); //we wish that this keeps its X coordenate in order to form a vertical.
                        
                        
                        
                    }else{
                        echo "doesn't have parent";
                    }
                    ##$this->SetY( $y);
                    $previousSibling=$child;
                    continue;
                }
                
                $this->SetY( $y);
                $this->SetX( $x);
               #echo json_encode($child->children);
               if($child instanceof Cell){
                   $autox=$this->GetX();
                   $autoy=$this->GetY();
                   
                   $this->Table($child);
                   if($child->IsAuto() ){ ##hay que detemerninar tambien si ess horizontal o vertical
                    //$tWeight= $child->getWeight();
                    $tWeight_= $child->config['weight'];
                    if(!$tWeight_){
                        #echo "error";
                    }
                    #$realWidht= $this->CalculateRealSize($child->getWeight());
                    #echo  $realWidht;
                    $r=20;
                    #$autox=$autox+$realWidht+$this->lMargin+2;
                     #$autox=$autox+$realWidht; //le sumo a X el withd que se supone cubrira

                    if($child->IsParentVertical()){

                    }else{
                      #  $this->SetXY($autox,$autoy); #se comento esta linea porque lo deseado con un auto, es que se incie en el eje Y donde se quedo, ya que si se restarura Y y le sigue un elemento, colisionaran
                        $this->SetX($autox);
                    }
                  }else if($child->IsParentVertical()){
                     # $this->Ln();
                      #echo "NO";
                    #  $this->AddY($this->defaultHeight);
                      #   $this->AddX($this->CalculateRealSize($realWidht));
                       #  
                    if(false){ #aqui hay un detalle porque no se de donde tomar X, 
                         # $this->AddX((int) \ceil($this->CalculateRealSize($child->getWeight()) ));
                    }else{
                        #$x
                    }
                  }else if($child->IsParentHorizontal() ){
                       #echo ";";
                       
                  }

                    continue;
               }
            }
        } else{ //si es celda
            if($container->IsParentVertical()){
                $this->draw([$container->config],2);
            }else{
                $this->draw([$container->config],0);
            }
        }
    }

    public function LineTo(int $x1,$y1){
        $this->Line($this->GetX(),$this->GetY(),$x1,$y1); 
    }
    public function getXY(){
        return ['x'=>$this->GetX(),'y'=>$this->GetY()];
    }
    public function createCoord($beggining){
        return ['start'=>$beggining,'end'=>$this->GetXY()];
    }
}

class LabelAndText
{
    public $label;
    public $text;
    public function __construct($label, $text)
    {
        $this->label = $label;
        $this->text = $text;
    }
}
class Container {
    use Common;
    public $children;
    public $config;
    
    public function __construct($config,$children){
         $this->children=$children;
         $this->config=$config;
    
         foreach ($children as $key => $child) {
             $child->parent=$this;
         }
    }
   
    public function RecalculateWeightFromParent(){
        if($this->parent==null){
            return -1;
        }
        return ($this->parent->getWeight()/100)*$this->getWeight();
    }
    public function CountChildren():int{
        return count($this->children);
    }
}   
class Vertical  extends Container{

}

class Horizontal  extends Container{

}

trait Common {
    public $parent;

    
    public function getWeight():float{
        return $this->config['weight'];
    }
    public function IsAuto(){
        return $this->config['auto']??false;
    }
    public function HasParent(){
        return $this->parent!==null;
    }
    public function IsParentVertical(){
        return $this->parent instanceof Vertical;
    }
    public function IsParentHorizontal(){
        return $this->parent instanceof Horizontal;
    }

    public function IsVertical(){
        return $this instanceof Vertical;
    }
    public function IsHorizontal(){
        return $this instanceof Horizontal;
    }
}
class Cell {
    use Common;
    public $config;
    

    
    public function __construct($config){
        if(\is_string($config)){
            $this->config=['text'=>$config];
        }else{
            $this->config=$config;
        }
      
    }
    public function RecalculateWeightFromParent(){
        if($this->parent==null){
            return -1;
        }
        return ($this->parent->getWeight()/100)*$this->getWeight();
    }
}
class Image{
    use Common;
    public $config;
    public function RecalculateWeightFromParent(){
        if($this->parent==null){
            return -1;
        }
        return ($this->parent->getWeight()/100)*$this->getWeight();
    }
}