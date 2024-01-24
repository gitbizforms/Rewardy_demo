<?

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$url = "http://bizforms.co.kr/smartblock/view.asp?sm_idx=3";
function file_get_contents_curl($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
 
 
$html = file_get_contents_curl($url);
$doc = new DOMDocument();
$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8')); // important!
$nodes = $doc->getElementsByTagName('title');
$title = $nodes->item(0)->nodeValue;
$metas = $doc->getElementsByTagName('meta');
$image=array();
 
for ($p = 0; $p < $metas->length; $p++){
  $meta = $metas->item($p);
  if($image == NULL){
    if($meta->getAttribute('property') == 'og:image')
      $image = $meta->getAttribute('content');
  }
}


print "<pre>";
print_r($image);
print "</pre>";
?>