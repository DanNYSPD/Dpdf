<?php 
namespace DPDF\Concerns;
/**
 * The code in this trait was taken from http://www.fpdf.org/en/script/script35.php
 * The author is Christophe Prugnaud which work  is based on  Maxime Delorme.
 */
trait RoundedRectTrait{
    /**
     * This value is the default cellRound used when CellRound is called.
     *
     * @var integer
     */
    public $cellRound=1;
    function CellRound($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){
        #now I wll draw the rect
         
        $this->RoundedRect($this->GetX(),$this->GetY(),$w,$h,$this->cellRound);
        
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
        
        /*
        $this->RoundedRect($this->getX() + $this->cellspacing / 2,
            $this->getY() + $this->cellspacing / 2,
            $w - $this->cellspacing, $h, 1, 'DF');
        $this->Cell($w, $h + $this->cellspacing, $txt, $ln, 0, $align,
            $fill, $link);*/
    }

    function RoundedRectCoords($x,$y,$x2,$y2,$r, $corners = '1234', $style = ''){
        $drawColor=$this->defaultHexDrawColor??$this->lastHexDrawColor;
        $lastDrawColor=!empty($this->lastHexDrawColor)?$this->lastHexDrawColor:'#000000';
        $this->SetDrawHexColor($drawColor);
        $this->RoundedRect(
            $x,
            $y,
          #  $x2-$this->GetLMargin(),
          #  $x2-$this->GetLMargin(),
            $x2-$x,
            ($y2   - $y) ,
            $r,
            $corners,
            $style
        );
        $this->SetDrawHexColor($lastDrawColor);
    }
    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f'; #fill the path
        elseif($style=='FD' || $style=='DF')
            $op='B';#fill and stroke the path
        else
            $op='S'; #stroke the path
        $MyArc = 4/3 * (sqrt(2) - 1);
        #m begin a new subpath.
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            #path construction operators, l operator appends a straight line segment from the current point to the point x y
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        #acording to pdf documentation the c operator appends a cubic bezier curve to the current path
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

}