<?php
namespace DPDF\Concerns;

trait ImgTrait{
    /**
     * According to the specs , the png signature consist of 8 bytes(https://www.w3.org/TR/PNG/#5PNG-file-signature) which is also the magic number,
     * , this signature is  immediately followed by an IHDR chunk where we can know check the interlaced flag.
     * As we need to read the IHDR chunk we have to keep in mind that each chunk consist of two mandatory fields(length and chunck type) ,
     * both require 4 bytes , that adds up 8 bytes.
     * 8 bytes from signature plus 4 bytes from the chunk length plus 4 bytes from the chunk type gives 16 bytes
     * Now we know that the 13th byte from the IHRD chunk field is the Interlace method byte so 16 +13 = 29    
     *
     * @param [type] $file
     * @return boolean
     */
   public function isInterlaced($file) {
        $handle = fopen($file, "r");
        //We read until the 29th byte
        $contents = fread($handle, 29);
        fclose($handle);
        //https://www.w3.org/TR/PNG-Chunks.html
        return ord($contents[28]) != 0 ;
    }
}