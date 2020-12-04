<?php


namespace Codeception\Lib\WFramework\WebObjects\Primitive;


use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\Exceptions\WFileLink\FileDownloadException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WLocator\WLocator;

class WFileLink extends WElement
{
    protected function initTypeName() : string
    {
        return 'Ссылка на файл';
    }

    public function download() : string
    {
        WLogger::logInfo($this . " -> скачиваем файл по ссылке");

        /**
         * TODO очень много всего нужно учесть:
         * что если ссылка лежит в JavaScript?
         * что если имени файла нет в URL?
         * ...
         */

        $innerHtml = $this->returnSeleniumElement()->get()->attribute('innerHTML');

        $matchResult = preg_match_all('%<a\s+\X*\s+href=\X*<\/a>%iUu', $innerHtml, $matches);

        $element = $this;

        if ($matchResult === 1)
        {
            WLogger::logDebug('Элемент не является <a> - ищем <a> внутри него');

            $element = WFileLink::fromLocator('//a', WLocator::xpath('.//a'));
            $element->setParent($this);
        }

        $url = $element
                    ->returnSeleniumElement()
                    ->get()
                    ->attribute('href')
                    ;

        if ($url === null)
        {
            throw new UsageException($this . " -> не содержит href");
        }

        $filename = basename(parse_url($url, PHP_URL_PATH));
        $filename = str_replace('%20', ' ', $filename);
        $outputFileName = codecept_output_dir() . $filename;

        $fp = fopen($outputFileName, 'wb+');

        if ($fp === false)
        {
            throw new FileDownloadException("Не получилось открыть файл: $outputFileName - на запись");
        }

        $curlHandler = curl_init(str_replace(' ','%20', $url));

        if ($curlHandler === false)
        {
            fclose($fp);
            throw new FileDownloadException("Не получилось инициализировать cURL");
        }

        curl_setopt($curlHandler, CURLOPT_TIMEOUT, 300);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandler, CURLOPT_FILE, $fp);

        $curlResult = curl_exec($curlHandler);
        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        curl_close($curlHandler);
        fclose($fp);

        if ($curlResult === false || $httpCode !== 200)
        {
            throw new FileDownloadException("Не получилось скачать файл");
        }

        return $outputFileName;
    }


}
