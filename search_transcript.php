<?php
$transcript = 'C:/Users/quiro/.gemini/antigravity-ide/brain/0d9792aa-6716-4e1a-8d77-7240892b5ca3/.system_generated/logs/transcript_full.jsonl';
$handle = fopen($transcript, "r");

$bestContent = '';
while (($line = fgets($handle)) !== false) {
    if (strpos($line, 'cargarDelivery') !== false || strpos($line, 'admin.js') !== false) {
        $data = json_decode($line, true);
        if ($data && isset($data['content'])) {
            $len = strlen($data['content']);
            if ($len > strlen($bestContent) && strpos($data['content'], 'cargarDelivery') !== false) {
                $bestContent = $data['content'];
            }
        }
        if ($data && isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                // If it's a tool response maybe it has content? No, tool response is in 'content' of another step.
            }
        }
    }
}
fclose($handle);

echo "Best content length: " . strlen($bestContent) . "\n";
if (strlen($bestContent) > 0) {
    file_put_contents('C:/xampp/htdocs/TecW_PAF/transcript_extract.txt', $bestContent);
    echo "Saved to transcript_extract.txt\n";
}
