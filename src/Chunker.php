<?php
namespace GPT_Toolkit;

use TikToken\Encoder;

class Chunker {

    public $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    public function chunk(int $max_tokens) {
        $chunks = [];
        $words = explode(' ', $this->text);
        $chunk = '';
        $chunk_words_estimation = intval($max_tokens / 8);
        $words_offset = 0;

        while ($words_offset < count($words)) {
            
            $chunk_words_size = $chunk_words_estimation;
            $chunk_words = array_slice($words, $words_offset, $chunk_words_size);
            $chunk = implode(' ', $chunk_words);
            $token_count = $this->count_tokens($chunk);

            // If the chunk is too small, add words until it's full or there are no more words
            while ($words_offset + $chunk_words_size < count($words) && $token_count < $max_tokens) {
                $chunk_words_size ++;
                $chunk_words = array_slice($words, $words_offset, $chunk_words_size);
                $chunk = implode(' ', $chunk_words);
                $token_count = $this->count_tokens($chunk);
            }

            // If the chunk is too large or ends with an incomplete tag, remove words until it fits
            while ($token_count > $max_tokens || preg_match('/<[^>]*$/', end($chunk_words))) {
                $chunk_words_size --;
                array_pop($chunk_words);
                $chunk = implode(' ', $chunk_words);
                $token_count = $this->count_tokens($chunk);
            }

            $chunks[] = [
                'content' => trim($chunk),
                'token_count' => $token_count
            ];

            $words_offset += $chunk_words_size;
        }
        return $chunks;
    }

    public function count_tokens(string $text) {
        $encoder = new Encoder();
        $encoded = $encoder->encode($text);
        return count($encoded);
    }

}