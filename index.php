<?php
if (isset($_GET['url']) && !empty($_GET['url'])) {
    $target_url = $_GET['url'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    curl_close($ch);
    
    if ($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        
        $nodes = $xpath->query("//a[.//span[contains(translate(text(), 'WATCH', 'watch'), 'watch')] or contains(translate(text(), 'WATCH', 'watch'), 'watch')]");
        
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if (!empty($href) && strpos($href, 'http') === 0) {
                header('Location: ' . $href);
                exit;
            }
        }
    }
    
    header('Location: ' . $target_url);
    exit;
}

header('Content-Type: audio/x-mpegurl; charset=utf-8');

$hub_url = 'https://project-lc4mz.vercel.app/api/hub';
$hub_data = @file_get_contents($hub_url);

if (!$hub_data) {
    exit("#EXTM3U\n");
}

$lines = explode("\n", $hub_data);
echo "#EXTM3U\n";

for ($i = 0; $i < count($lines); $i++) {
    $line = trim($lines[$i]);
    if (strpos($line, '#EXTINF:') === 0) {
        $extinf = $line;
        
        if (preg_match('/group-title="[^"]*"/i', $extinf)) {
            $extinf = preg_replace('/group-title="[^"]*"/i', 'group-title="✨ Lastest Hub Movies"', $extinf);
        } else {
            $extinf = str_replace('#EXTINF:-1', '#EXTINF:-1 group-title="✨ Lastest Hub Movies"', $extinf);
        }
        
        $url = isset($lines[$i + 1]) ? trim($lines[$i + 1]) : '';
        
        if (!empty($url)) {
            $proxy_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?url=' . urlencode($url);
            echo $extinf . "\n" . $proxy_url . "\n";
        }
    }
}
?>
