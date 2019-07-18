# The PDF engine.
 This library boost the speed for creating and defining pdf reports with defined structures. Look at down.

 ```php
 $pdf= new DFPDF();
 $pdf->Table(DFPDF::Horizontal([
     'weigth',[
         DFPDF::Cello(['text'=>'Id']),
         DFPDF::Cello(['text'=>'1010'])
     ]
    ])
 );

$pdf->Output();

 ```



#Install via composer
> composer require xarenisoft/dpdf

