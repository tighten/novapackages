<?php

namespace App;

class ReadmeFormatter
{
    protected $source;

    protected $readmeIsHtml;

    protected $url;

    protected $latestVersion;

    protected $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'svg'];

    public function __construct($package)
    {
        $this->source = $package->readme_source;
        $this->readmeIsHtml = $package->readmeIsHtml();
        $this->url = $package->repo_url;
        $this->latestVersion = $package->latest_version;
    }

    public function format($text)
    {
        $text = $this->removeAnchors($text);

        if ($this->readmeIsHtml) {
            return $this->formatHtml($text);
        }

        // We normalize all of the image url's first. Then we assume the remaining url's are
        // non-image url's and normalize those
        return markdown($this->replaceNonImageUrls($this->replaceImageUrls($text)));
    }

    public function formatHtml($html)
    {
        // Replace relative URLs
        $formattedHtml = $this->replaceNonImageUrlsHtml($this->replaceImageUrlsHtml($html));

        // wrap with .markdown
        return '<div class="markdown">'.$formattedHtml.'</div>';
    }

    private function removeAnchors($text)
    {
        return preg_replace('/<a .+class=\"anchor\".+>.+<\/a>/i', '', $text);
    }

    public function replaceImageUrls($markdown)
    {
        // Look for all of relative url's with valid image types in the markdown and noamalize them with fully
        // qualified url's.
        return preg_replace($this->imageRegexPatterns(), '[$1]('.$this->url.'/raw/'.$this->latestVersion.'/'.'$2'.')', $markdown);
    }

    public function replaceNonImageUrls($markdown)
    {
        // Look for all of relative url's in the markdown and repalce them with fully qualified url's base on the
        // markdown source
        return preg_replace('/\[(.*?)\]\(((?!http|mailto:).*?)\)/i', '[$1]('.$this->url.'/'.$this->nonImageFormat().'/'.$this->latestVersion.'/'.'$2'.')', $markdown);
    }

    public function replaceImageUrlsHtml($html)
    {
        $patterns = array_map(function ($extension) {
            return '/<img src="((?!http).*?\.'.$extension.')"/i';
        }, $this->imageExtensions);

        $baseUrl = $this->url.'/raw/'.$this->latestVersion.'/';

        // Look for all of relative URLs with valid image types in img elements and give them full URLs
        return preg_replace(
            $patterns,
            '<img src="'.$baseUrl.'$1'.'"',
            $html
        );
    }

    public function replaceNonImageUrlsHtml($html)
    {
        $pattern = '/<a(.+)href="((?!http)(?!mailto).*?)"/i';

        $baseUrl = $this->url.'/'.$this->nonImageFormat().'/'.$this->latestVersion.'/';

        return preg_replace(
            $pattern,
            '<a$1href="'.$baseUrl.'$2"',
            $html
        );
    }

    public function imageRegexPatterns()
    {
        // Build an array of image patterns to look for in the markdown
        return array_map(function ($extension) {
            return '/\[(.*?)\]\(((?!http).*?\.'.$extension.')\)/i';
        }, $this->imageExtensions);
    }

    protected function nonImageFormat()
    {
        // Bitbucket's fully qualified url is different than github and gitlab
        return ($this->source == 'github' || $this->source == 'gitlab')
            ? 'blob'
            : 'src';
    }
}
