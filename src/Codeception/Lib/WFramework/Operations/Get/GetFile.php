<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Exceptions\FileDownloadException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Ds\Sequence;

class GetFile extends AbstractOperation
{
    public function getName() : string
    {
        return "скачиваем файл по ссылке";
    }

    /**
     * Для элемента содержащего ссылку - скачивает файл по этой ссылке
     *
     * Возвращает путь к скачанному файлу.
     */
    public function __construct(){}

    public function acceptWBlock($block) : string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : string
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : string
    {
        /**
         * TODO очень много всего нужно учесть:
         * что если ссылка лежит в JavaScript?
         * что если имени файла нет в URL?
         * ...
         */

        $innerHtml = $pageObject->accept(new GetAttributeValue('innerHTML'));

        if (preg_match('%<a\s+\X*\s+href=\X*<\/a>%iUu', $innerHtml, $matches))
        {
            WLogger::logDebug('Элемент не является <a> - ищем <a> внутри него');

            /** @var WPageObject $poClass */
            $poClass = get_class($pageObject);

            /** @var WPageObject $button */
            $button = $poClass::fromLocator('//a', WLocator::xpath('.//a'));
            $button->setParent($pageObject);

            $pageObject = $button;
        }

        $url = $pageObject->accept(new GetAttributeValue('href'));

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