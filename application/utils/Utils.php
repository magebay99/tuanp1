<?php
class Utils{
    public function DownloadImage($imgUrl, $imgName){
        $ch = curl_init($imgUrl);
        $filepath = "public/image/".$imgName.".png";
        $fp = fopen($filepath, "wb");
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $filepath;
    }
}
?>