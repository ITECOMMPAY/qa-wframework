<?php


namespace Codeception\Lib\WFramework\WOperations\Field;


use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class FieldUploadFile extends AbstractOperation
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * Загружает файл
     *
     * @param string $filename - полное имя файла относительно codecept_data_dir
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Загружаем файл: ' . $this->filename);

        $element = $pageObject->getProxyWebElement();

        $element->setFileDetector(new \Facebook\WebDriver\Remote\LocalFileDetector());

        $rawFilePath = codecept_data_dir() . '/' . $this->filename;

        $filePath = realpath($rawFilePath);

        if ($filePath === false)
        {
            throw new UsageException('Не получается найти файл: ' . $rawFilePath);
        }

        $element->sendKeys($filePath);

        return $this;
    }
}
