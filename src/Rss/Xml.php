<?php


namespace App\Rss;


class Xml
{
    public function generate($posts){
        $xml = <<<xml
<?xml version="1.0" encoding="UTF-8" ?> 
<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
<channel>
<title>Примеры работ в Санкт-Петербурге. «Автосервис ПИК»</title>
<link>https://piksp.ru</link>
<description>Примеры работ в Санкт-Петербурге.✔ Ежедневно 9:00 - 21:00 ✔ Ремонт в день обращения ✔ Стоимость и цены. ✔ Гарантия 1 год! Профильный автосервис «ПИК» - ☎</description>
<language>ru</language>
xml;
        foreach ($posts as $post) {

            $title = self::xmlEscape($post->getName());
            $url = self::xmlEscape($post->getAlias());
            $slug = self::xmlEscape($post->getAlias());
            $pubDate = $post->getModifyDate()->format('D, d M Y H:i:s O');
            $image = $post->getBlogImg();
            /*$short_text = str_ireplace(array('<','>','&','\'','"'),array('&lt;','&gt;','&amp;','&apos;','&quot;'),$post->getShortText());*/
            $short_text = str_ireplace(array('<div>','</div>','&nbsp;','<','>','&','\'','"'),array(''),$post->getShortText());

            if(is_null($image)){
                $image = "zaglushka.jpg";
            }
            $xml .= <<<xml
<item turbo="true">
<title>{$title}</title>
<link>https://piksp.ru/blog/{$url}</link>
<description>{$slug}</description>
<pubDate>$pubDate</pubDate>
<guid isPermaLink="true">https://piksp.ru/blog/{$url}</guid>
<media:content medium="image" url="https://piksp.ru/img/nashiraboty_small/{$image}" width="227" height="135"/>
<turbo:content>

<h1>{$title}</h1>
<figure>
<img src="https://piksp.ru/img/nashiraboty_small/{$image}" type="image/jpeg"/>
</figure>

{$short_text}
</turbo:content>
</item>
xml;
        }
        $xml .= "</channel></rss>";

        return $xml;
    }

    private static function xmlEscape($string){
        return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);

    }

}