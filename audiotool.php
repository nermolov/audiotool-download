<?php
  error_reporting(E_ALL & ~E_WARNING);
  $dom = new DomDocument();
  $dom->loadHTML(file_get_contents($_POST['url']));
  $finder = new DomXPath($dom);
  $classname="_track-name";
  $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
  $i = 0;
  $muu = '';
  foreach ($nodes as $item) {
      //echo $item->nodeValue . "\n";
      $links[$i] = 'https://api.audiotool.com' . $item->getAttribute('href') . 'mixdown.mp3';
      if (isset($_POST['mstate'])) {
        $raw = get_headers($links[$i], 1)['Content-Disposition'];
        $almost = preg_replace("/attachment; filename=\"/", "", $raw, 1);
        $alalmost = preg_replace("/\"/", "", $almost, 1) . "\n";
        $muu .= preg_replace("/\//", "-", $alalmost, 1);
      }
      $i++;
  }
  if (isset($_POST['mstate'])) {
    $links['m3u8'] = base64_encode($muu);
    if ($_POST['url'] == 'https://www.audiotool.com/') {
      $date = getdate();
      $links['title'] = 'charts'.$date['mon'].'-'.$date['mday'].'-'.$date['year'];
    } else {
      $classname="_album-name";
      $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
      $array = array();
      foreach($nodes as $node){
        $array[] = $node;
      }
      $links['title'] = $array[0]->nodeValue;
    }
  }
  echo(json_encode($links, JSON_UNESCAPED_SLASHES));
?>
