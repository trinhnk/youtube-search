<?php

namespace Trinhnk\YoutubeSearch;

use KubAT\PhpSimple\HtmlDomParser;

class YoutubeSearch
{
    private $_urlQuery;
    private $_urlEmbed;
    private $_urlWatch;

    public function __construct()
    {
        $this->_urlQuery = env('YOUTUBE_URL_QUERY', 'https://www.youtube.com/results?search_query=');
        $this->_urlEmbed = env('YOUTUBE_URL_EMBED', 'https://www.youtube.com/embed/');
        $this->_urlWatch = env('YOUTUBE_URL_WATCH', 'https://www.youtube.com/watch?v=');
    }

    /**
     * @param string|null $keyword
     * @param integer $quantity
     * @return array|null
     */
    public function search(?string $keyword = null, int $quantity = 1): ?array
    {
        if (!empty($keyword)) {
            $url = $this->_urlQuery . urlencode($keyword);
            $dom = HtmlDomParser::file_get_html($url);
            $listOfVideo = $this->getVideosList($dom);
            if (!empty($quantity)) {
                array_splice($listOfVideo, $quantity);
            }
            return $listOfVideo;
        }
        return null;
    }

    /**
     * @param object $dom
     * @return array|null
     */
    private function getVideosList(object $dom): ?array
    {
        $scripts = $dom->find('script');
        $content = '';
        foreach ($scripts as $script) {
            if (strpos($script->innertext, 'var ytInitialData') !== false) {
                $content = $script->innertext;
                break;
            }
        }
        $content = $this->decodeJSContent($content);

        $videosList = $this->getManyVideoInfo($content);

        $arrVideoInfo = [];
        foreach ($videosList as $video) {
            $videoInfo = $this->getOneVideoInfo($video);
            if (!empty($videoInfo)) {
                $videoInfo['embed'] = $this->_urlEmbed . $videoInfo['id'];
                $videoInfo['watch'] = $this->_urlWatch . $videoInfo['id'];
                array_push($arrVideoInfo, $videoInfo);
            }
        }
        return $arrVideoInfo;
    }

    /**
     * @param string $text
     * @return string
     */
    private function decodeJSContent(string $text): string
    {
        return preg_replace_callback(
            "(\\\\x([0-9a-f]{2}))i",
            function ($a) {
                return chr(hexdec($a[1]));
            },
            $text
        );
    }

    /**
     * @param string $content
     * @return array|null
     */
    private function getManyVideoInfo(string $content): ?array
    {
        $check2Column = str_contains($content, ',"secondaryContents"');
        if ($check2Column) {
            $arrContent = explode('{"primaryContents":', $content);
            $arrContent = explode(',"secondaryContents"', $arrContent[1]);
            $content = $arrContent[0];
            $content = json_decode($content);
        } else {
            $content = str_replace('var ytInitialData = ', '', $content);
            $content = str_replace(']};', ']}', $content);
            $content = json_decode($content);
            $content = $content->contents->twoColumnSearchResultsRenderer->primaryContents;
        }
        $videosList = $content->sectionListRenderer->contents[0]->itemSectionRenderer->contents ?? null;
        return $videosList;
    }

    /**
     * @param object $video
     * @return array|null
     */
    private function getOneVideoInfo(object $video): ?array
    {
        $videoId = $video->videoRenderer->videoId ?? null;
        $videoTitle = $video->videoRenderer->title->runs[0]->text ?? null;
        if (!empty($videoId) && !empty($videoTitle)) {
            return [
                'id' => $videoId,
                'title' => $videoTitle,
            ];
        }
        return null;
    }
}
