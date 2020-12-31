<?php


namespace Codeception\Lib\WFramework\WebObjects\Verifier;


use Codeception\Lib\WFramework\Operations\Get\GetScreenshotRaw;
use function array_filter;
use Codeception\Actor;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use function get_declared_classes;
use function implode;
use function in_array;
use function mb_substr;
use PHPUnit\Framework\AssertionFailedError;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use ReflectionObject;
use RegexIterator;
use function reset;

class PageObjectsVerifier
{
    /**
     * @var Actor
     */
    protected $mainActor;

    /**
     * @var string
     */
    protected $pageObjectsDir;

    /**
     * @var array
     */
    protected $ignoredPageObjects;

    /**
     * @var bool
     */
    protected $takeScreenshots;

    /**
     * @var array
     */
    protected $result = [];

    const CANT_OPEN = 'cant_open';
    const HAVE_MAIN_LOCATOR_BROKEN = 'have_main_locator_broken';
    const HAVE_BROKEN_ELEMENTS = 'have_broken_elements';

    /**
     * PageObjectsVerifier constructor.
     *
     * @param Actor $mainActor - основной актор проекта ($I)
     * @param string $pageObjectsDir - полный путь к каталогу с PageObject'ами
     * @param array $ignoredPageObjects - массив полных имён классов PageObject'ов, валидность которых проверять не надо
     * @param bool $takeScreenshots - сохранять ли скриншот валидного PageObject'а в одном каталоге рядом с ним
     */
    public function __construct(Actor $mainActor, string $pageObjectsDir, array $ignoredPageObjects, bool $takeScreenshots)
    {
        $this->mainActor = $mainActor;
        $this->pageObjectsDir = $pageObjectsDir;
        $this->ignoredPageObjects = $ignoredPageObjects;
        $this->takeScreenshots = $takeScreenshots;
    }

    protected function getPageObjectClasses() : array
    {
        $directory = new RecursiveDirectoryIterator($this->pageObjectsDir);
        $iterator = new RecursiveIteratorIterator($directory);
        $pageObjectFiles = new RegexIterator($iterator, '/^.+\.php/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($pageObjectFiles as $pageObjectFile)
        {
            include_once reset($pageObjectFile);
        }

        $declaredClasses = get_declared_classes();

        $pageObjectClasses = array_filter($declaredClasses,
            function($value, $key) {
                if (in_array($value, $this->ignoredPageObjects, true))
                {
                    return false;
                }

                $reflect = new ReflectionClass($value);

                if ($reflect->isAbstract() || $reflect->isTrait() || $reflect->isInterface())
                {
                    return false;
                }

                if (!$reflect->isSubclassOf(WBlock::class))
                {
                    return false;
                }

                return true;
            }, ARRAY_FILTER_USE_BOTH);

        sort($pageObjectClasses);

        return $pageObjectClasses;
    }

    /**
     * Проверяет, что все локаторы PageObject'ов - валидны.
     *
     * Для этого каждый PageObject будет открыт с помощью его метода openPage().
     *
     * Результатывалидации PageObject'ов будет сохранён внутри объекта данного класса.
     * Его можно получить с помощью метода getResult() и распечатать в консоль с помощью
     * статического метода printResult().
     *
     * @param int $totalThreads
     * @param int $currentThreadNumber
     * @throws \Codeception\Lib\WFramework\Exceptions\UsageException
     * @throws \ImagickException
     * @throws \ReflectionException
     */
    public function checkPageObjects(int $totalThreads = 1, int $currentThreadNumber = 1)
    {
        $classes = $this->getPageObjectClasses();

        $chunks = array_chunk($classes, (int) ceil(count($classes) / $totalThreads));
        array_unshift($chunks, []);

        $classes = $chunks[$currentThreadNumber];

        $this->result = [];

        foreach ($classes as $pageObjectClass)
        {
            $this->checkPageObject($pageObjectClass);
        }
    }

    /**
     * Проверяет, что локаторы PageObject'а - валидны.
     *
     * Для этого PageObject будет открыт с помощью его метода openPage().
     *
     * Результат валидации PageObject'а будет сохранён внутри объекта данного класса.
     * Его можно получить с помощью метода getResult() и распечатать в консоль с помощью
     * статического метода printResult().
     *
     * @param string $className - полное имя класса PageObject'а
     * @throws \Codeception\Lib\WFramework\Exceptions\UsageException
     * @throws \ImagickException
     * @throws \ReflectionException
     */
    public function checkPageObject(string $className)
    {
        $this->mainActor->restartWebDriver();

        /** @var WBlock $pageObject */
        $pageObject = new $className($this->mainActor);

        $refObject = new ReflectionObject($pageObject);
        $openPageMethod = 'openPage';
        $refMethod = $refObject->getMethod($openPageMethod);
        $refMethod->setAccessible(true);

        try
        {
            $refMethod->invoke($pageObject);
        }
        catch (\Exception $e)
        {
            echo PHP_EOL . $e->getMessage() . PHP_EOL;

            $this->result[static::CANT_OPEN][] = $className;
            return;
        }

        try
        {
            $pageObject->shouldExist(false);
        }
        catch (AssertionFailedError $e)
        {
            $this->result[static::HAVE_MAIN_LOCATOR_BROKEN][] = $className;
            return;
        }

        foreach ($pageObject->getChildren() as $child)
        {
            try
            {
                $child->shouldExist(true);
            }
            catch (AssertionFailedError $e)
            {
                $this->result[static::HAVE_BROKEN_ELEMENTS][$className][] = $child->getName();
            }
        }

        if (isset($this->result[static::HAVE_BROKEN_ELEMENTS][$className]))
        {
            return;
        }

        if (!$this->takeScreenshots)
        {
            return;
        }

        $filename = $refObject->getFileName();

        if (!$filename)
        {
            return;
        }

        $screenshotName = mb_substr($filename, 0, -4) . '__.png';

        $pageObject->accept(new GetScreenshotRaw($screenshotName));
    }

    /**
     * Возвращает результат валидации PageObject'ов в виде ассоциативного массива:
     *
     * [
     *      'cant_open' => [PO которые не получилось открыть с помощью метода openPage()],
     *      'have_main_locator_broken' => [PO у которых сломан основной локатор],
     *      'have_broken_elements' => [PO у которых сломаны локаторы детей]
     * ]
     *
     * @return array
     */
    public function getResult() : array
    {
        return $this->result;
    }

    public static function printResult(array $result = [])
    {
        if (empty($result))
        {
            return;
        }

        $blankLine = PHP_EOL . PHP_EOL;

        if (isset($result[PageObjectsVerifier::CANT_OPEN]))
        {
            echo $blankLine;
            echo "Следующие WBlock не удалось открыть (не отработал openPage):";
            echo $blankLine;
            echo implode(PHP_EOL, $result[PageObjectsVerifier::CANT_OPEN]);
        }

        if (isset($result[PageObjectsVerifier::HAVE_MAIN_LOCATOR_BROKEN]))
        {
            echo $blankLine;
            echo "Следующие WBlock не удалось найти на странице (не найден локатор из initPageLocator):";
            echo $blankLine;
            echo implode(PHP_EOL, $result[PageObjectsVerifier::HAVE_MAIN_LOCATOR_BROKEN]);
        }

        if (isset($result[PageObjectsVerifier::HAVE_BROKEN_ELEMENTS]))
        {
            echo $blankLine;
            echo "Следующие WBlock содержат битые элементы:";
            echo $blankLine;

            foreach ($result[PageObjectsVerifier::HAVE_BROKEN_ELEMENTS] as $pageObject => $errors)
            {
                echo "$pageObject:" . PHP_EOL;
                echo implode(PHP_EOL, $errors);
                echo $blankLine;
            }
        }

        echo $blankLine;
    }
}
