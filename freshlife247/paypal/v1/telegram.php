<?php
namespace Sample;

class Telegram 
{
    protected static $_url = "https://api.telegram.org/bot";
    protected static $_chatId = "-942779544";
    protected static $_botId = "6667071164:AAEwjwSb3R-x1024Nos2KCCldJOD5C5nkqE";

    public static function sendMessage($text, $title = "")
    {
        if ($title != "") {
            $text = "<b>" . $title. "</b>" . $text;
        }
        $url = self::$_url . self::$_botId . "/sendMessage?parse_mode=html&chat_id=" . self::$_chatId . "&text=" . $text;
        file_get_contents($url);
    }

    public static function report($exception, $params = "")
    {
        $html = '<b>[Body] : </b><code>' . $params . '</code>';
        $html .= '<b>[Lỗi] : </b>' . json_encode($exception);
        self::sendMessage($html);
    }
}