# The PDF engine.
 This library boost the speed for creating and defining pdf reports with defined structures. Look at down.

 ```php
$pdf = new DPDF();
$pdf->AddPage();
$pdf->SetFont("Arial", "", 9);
//header
$pdf->Table(DPDF::Horizontal(['weight'=>100,'border'=>'B'],
    [
        DPDF::Cello(['text'=>'Id']),
        DPDF::Cello(['text'=>'Name']),
        DPDF::Cello(['text'=>'Age']),
        DPDF::Cello(['text'=>'Amount'])
    ]
   )
);
$pdf->Ln();

$pdf->Table(DPDF::Horizontal(['weight'=>100],
    [
        DPDF::Cello(['text'=>'1']),
        DPDF::Cello(['text'=>'JAN']),
        DPDF::Cello(['text'=>'10']),
        DPDF::Cello(['text'=>'$1010'])
    ]
   )
);


$pdf->Output(rand()."pdf.pdf",'F');

 ```



#Install via composer
> composer require xarenisoft/dpdf

