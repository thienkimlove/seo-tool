<?php
use Aws\Common\Aws;
use Aws\Common\Enum\Region;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Exception\S3Exception;

class UploadController extends AppController {
    public $autoRender = false;        
    public function index() {
       
        $post = $this->request->data;
        $response = '';
        $contentType = 'png';
        if (!empty($post['rawImage'])) {
            $contentType = 'png';
            $matches = array ();
            $content = $post ['rawImage'];
            if (preg_match ( '/data:.*;base64,/', $content, $matches )) {
                $contentType = str_replace ( 'data:', '', str_replace ( ';base64,', '', $matches [0] ) );
            }
            $ext = str_replace ( 'image/', '', $contentType );
            $contentStart = preg_replace ( '/^data:.*;base64,/', '', substr ( $content, 0, 30 ) );
            $content = base64_decode ( preg_replace ( '/^data:.*;base64,/', '', $contentStart . substr ( $content, 30 ) ) );
            $response = $this->_uploadToS3($content, $contentType); 
        } else {
           
            if (!empty($this->request->params['form']['files'])) {
                $files = $this->request->params['form']['files'];  
                if (is_uploaded_file($files['tmp_name'][0])) {
                  $filename =  TMP . time(). $files['name'][0];
                  move_uploaded_file($files['tmp_name'][0], $filename);
                  //for testing resize , correct must be 1000 1000
                  $this->_resize($filename, 1000, 1000);
                  $response = json_encode(array('response' => $this->_uploadToS3(file_get_contents($filename), $files['type'][0])));
                  unlink($filename);
                }
            }
        } 
        echo $response;  
    }
    private function _uploadToS3($content, $contentType) {
        $s3 = Aws::factory ( Configure::read ( 'AWS.S3' ) )->get ( 's3' );
                 $return = $s3->putObject ( array (
                'Bucket' => Configure::read ( 'AWS.S3.bucket' ),
                'Key' => str_replace ( '-', '', String::uuid () ),
                'Body' => $content,
                'ACL' => CannedAcl::PUBLIC_READ,
                'ContentType' => $contentType 
            ));
         return  $return ['ObjectURL'];  
    } 
    
    private  function _resize($path, $width, $height, $aspect = true) {
        
        $types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp"); // used to determine image type  
        
        if (!($size = getimagesize($path))) {
          return; // image doesn't exist  
        }  
        if ($aspect) { // adjust to aspect.
            if (($size[1]/$height) > ($size[0]/$width))  // $size[0]:width, [1]:height, [2]:type
                $width = ceil(($size[0]/$size[1]) * $height);
            else 
                $height = ceil($width / ($size[0]/$size[1]));
        }        
        
        $resize = ($size[0] > $width || $size[1] > $height) || ($size[0] < $width || $size[1] < $height);  
        
        if ($resize) {
            $image = call_user_func('imagecreatefrom'.$types[$size[2]], $path);
            if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {
                imagecopyresampled ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
              } else {
                $temp = imagecreate ($width, $height);
                imagecopyresized ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
            }
            call_user_func("image".$types[$size[2]], $temp, $path);
            imagedestroy ($image);
            imagedestroy ($temp);
        }  
    }
}
?>
