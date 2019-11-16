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
    function CellRound($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false,$corners='1234', $link=''){
        #now I wll draw the rect
        $style='D';
         if($fill===true){
            $style='DF';
         }
        $this->RoundedRect($this->GetX(),$this->GetY(),$w,$h,$this->cellRound,'1234',$style);
        
        $this->Cell($w,$h,$txt,$border,$ln,$align,0,$link);
        
        /*
        $this->RoundedRect($this->getX() + $this->cellspacing / 2,
            $this->getY() + $this->cellspacing / 2,
            $w - $this->cellspacing, $h, 1, 'DF');
        $this->Cell($w, $h + $this->cellspacing, $txt, $ln, 0, $align,
            $fill, $link);*/
    }

    function MultiCellRound($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    $XYPrevous=$this->GetXY();
	// Output text with automatic or explicit line breaks
	if(!isset($this->CurrentFont))
		$this->Error('No font has been set');
	$cw = &$this->CurrentFont['cw'];
	if($w==0)
		$w = $this->w-$this->rMargin-$this->x;
	$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
	$s = str_replace("\r",'',$txt);
	$nb = strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$b = 0;
	if($border)
	{
		if($border==1)
		{
			$border = 'LTRB';
			$b = 'LRT';
			$b2 = 'LR';
		}
		else
		{
			$b2 = '';
			if(strpos($border,'L')!==false)
				$b2 .= 'L';
			if(strpos($border,'R')!==false)
				$b2 .= 'R';
			$b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
		}
	}
	$sep = -1;
	$i = 0;
	$j = 0;
	$l = 0;
	$ns = 0;
	$nl = 1;
	while($i<$nb)
	{
		// Get next character
		$c = $s[$i];
		if($c=="\n")
		{
			// Explicit line break
			if($this->ws>0)
			{
				$this->ws = 0;
				$this->_out('0 Tw');
			}
			$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,$align,false);
			$i++;
			$sep = -1;
			$j = $i;
			$l = 0;
			$ns = 0;
			$nl++;
			if($border && $nl==2)
				$b = $b2;
			continue;
		}
		if($c==' ')
		{
			$sep = $i;
			$ls = $l;
			$ns++;
		}
		$l += $cw[$c];
		if($l>$wmax)
		{
			// Automatic line break
			if($sep==-1)
			{
				if($i==$j)
					$i++;
				if($this->ws>0)
				{
					$this->ws = 0;
					$this->_out('0 Tw');
				}
				$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,false);
			}
			else
			{
				if($align=='J')
				{
					$this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
					$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
				}
				$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,false);
				$i = $sep+1;
			}
			$sep = -1;
			$j = $i;
			$l = 0;
			$ns = 0;
			$nl++;
			if($border && $nl==2)
				$b = $b2;
		}
		else
			$i++;
	}
	// Last chunk
	if($this->ws>0)
	{
		$this->ws = 0;
		$this->_out('0 Tw');
	}
	if($border && strpos($border,'B')!==false)
		$b .= 'B';
	$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,false);
    $this->x = $this->lMargin;
    $this->RoundedRectCoords(
        $XYPrevous['x'],
        $XYPrevous['y'],
        $this->GetX(),$this->GetY(),$this->cellRound,'1234',$style);
    
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