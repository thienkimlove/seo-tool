<?php

class SeoController extends AppController {
    
    public $helpers = array('Cache');
    
    public $cacheAction = array('sitemap' => 50000);
    
    public function robots() {
        $this->autoRender = false;
        if(Configure::read('debug')) {
            echo "User-agent: *  \n";
            echo "Disallow: /";
        } else {
            echo "User-agent: * \n";
            echo "Allow: / \n";
            echo "Disallow: /wp-login.php \n";
            echo "Disallow: /wp-admin/ \n";
            echo "Disallow: /wp-content/ \n";
            echo "Disallow: /wp-includes/ \n";
        }
    }
    
    public function sitemap() {
        $this->autoRender = false;
        if($this->RequestHandler->ext == 'xml') {
            $result = Cache::read('sitemap', '_seo_');
            if(!$result) {
                $reviews = $this->Scout2GoApi2->resource('Review')->query(array('indexable' => true));
                $surveyIds = array_filter(from($reviews['review'])->select('$v["Review"]["survey_id"]')->toList());
                // as $surveyIds can be very long so we have to paginate
                $shortkeys = array();
                foreach (array_chunk($surveyIds, 100) as $chunk) {
                    // first get the surveys with status = active and public_result = 1
                    $surveysChunk = $this->Scout2GoApi2->resource('Surveys')->query(array(
                        'id' => $chunk, 
                        'status' => 'active',
                        'public_result' => '1'
                    ));
                    $surveyIdsChunk = array_filter(from($surveysChunk['surveys'])->select('$v["Survey"]["id"]')->toList());
                    $shortkeysChunk = $this->Scout2GoApi2->resource('Shortkey')->query(array('survey_id' => $surveyIdsChunk));
                    $shortkeys = array_merge($shortkeys, from($shortkeysChunk['shortkeys'])->select('$v["Shortkey"]["shortkey"]')->toList());
                    
                }
                $this->set('shortkeys', $shortkeys);
//                 $wordPressContent = @file_get_contents('http://10.0.1.22/sitemap.xml');
//                 if($wordPressContent) {
//                     $this->set('wordPressContent', new SimpleXMLElement($wordPressContent));
//                 }
                $response = $this->render();
                Cache::write('sitemap', $response, '_seo_');
                return $response;
            }
            return $result;
        }
    }
}
?>
