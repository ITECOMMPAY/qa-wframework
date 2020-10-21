<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 22.04.19
 * Time: 15:03
 */

namespace dodge\cases;

use dodge\DodgeTester;
use dodge\Helper\TestSteps\DodgeSteps;
use function codecept_output_dir;
use Common\Module\WFramework\WebObjects\Verifier\PageObjectsVerifier;
use function codecept_root_dir;
use function file_exists;
use function file_put_contents;
use function json_encode;
use function unlink;

class selfCheckCest
{
    protected $pageObjectsSubDir = '_support/Helper/Blocks/';

    protected $ignoredPageObjects = [
        'dodge\Helper\Blocks\DodgeBlock',
    ];

    protected $takeScreenshots = true; // Если в true - то для каждого успешно открытого PO, будет сохранён скриншот

    /**
     * Этот тест проверяет, что все локаторы всех PageObject'ов (кроме тех, что указаны в $ignoredPageObjects) - валидные
     * Т.к. он гоняет проверку в один поток - то лучше вместо него использовать параллельный прогон через Robo:
     *
     * ./vendor/bin/robo --load-from ./tests/dodge/cases/robo/RoboFile.php parallel:self-check
     *
     * @param DodgeTester $I
     * @param DodgeSteps $steps
     * @throws \Common\Module\WFramework\Exceptions\Common\UsageException
     * @throws \ImagickException
     * @throws \ReflectionException
     */
    public function selfCheckAll(DodgeTester $I, DodgeSteps $steps)
    {
        $I->wantToTest('Все PageObject\'ы имеют валидные локаторы');

        $pageObjectDir = codecept_root_dir() . $this->pageObjectsSubDir;

        $verifier = new PageObjectsVerifier($I, $pageObjectDir, $this->ignoredPageObjects, $this->takeScreenshots);
        $verifier->checkPageObjects();
        $verifier::printResult($verifier->getResult());

        $I->assertEmpty($verifier->getResult());
    }

    /**
     * Этот тест проверяет, что локаторы заданного PageObject'а - валидные
     *
     * ./vendor/bin/codecept run cases selfCheckCest:checkPageObject -c ./tests/dodge --env dodge-loc,dodge-loc-chrome,dodge-loc-1920
     *
     * @param DodgeTester $I
     * @param DodgeSteps $steps
     * @throws \Common\Module\WFramework\Exceptions\Common\UsageException
     * @throws \ImagickException
     * @throws \ReflectionException
     */
    public function checkPageObject(DodgeTester $I, DodgeSteps $steps)
    {
        $I->wantToTest('Указанный PageObject имеет валидные локаторы');

        $pageObject = '\dodge\Helper\Blocks\SelectModelPage\SelectModelBlock'; // Полное имя класса PO, который нужно валидировать
                                                                      // (правой кнопкой по имени класса -> Copy/Paste Special -> Copy Reference)



        $pageObjectDir = codecept_root_dir() . $this->pageObjectsSubDir;

        $verifier = new PageObjectsVerifier($I, $pageObjectDir, $this->ignoredPageObjects, $this->takeScreenshots);
        $verifier->checkPageObject($pageObject);
        $verifier::printResult($verifier->getResult());

        $I->assertEmpty($verifier->getResult());
    }


    /**
     * Методы ниже нужны для параллельного прогона валидации - руками их запускать не надо.
     * Параллельный прогон запускается следующей командой:
     * ./vendor/bin/robo --load-from ./tests/dodge/cases/robo/RoboFile.php parallel:self-check
     */


    protected function _deleteOutputFile(string $outputFile)
    {
        if (file_exists($outputFile))
        {
            unlink(codecept_output_dir() . $outputFile);
        }
    }

    protected function _saveOutputFile(string $outputFile, array $output)
    {
        file_put_contents(codecept_output_dir() . $outputFile, json_encode($output));
    }

    protected function selfCheckRobo(DodgeTester $I, int $totalThreads = 1, int $currentThreadNumber = 1)
    {
        $I->wantToTest('Все PageObject\'ы имеют валидные локаторы');

        $pageObjectDir = codecept_root_dir() . $this->pageObjectsSubDir;

        $outputFile = "self_check_thread_$currentThreadNumber.json";

        $this->_deleteOutputFile($outputFile);

        $verifier = new PageObjectsVerifier($I, $pageObjectDir, $this->ignoredPageObjects, $this->takeScreenshots);
        $verifier->checkPageObjects($totalThreads, $currentThreadNumber);

        $this->_saveOutputFile($outputFile, $verifier->getResult());
    }

    /** @group self_check_thread_1 */
    public function selfCheckRoboThread1(DodgeTester $I, DodgeSteps $steps)
    {
        $this->selfCheckRobo($I, 4, 1);
    }

    /** @group self_check_thread_2 */
    public function selfCheckRoboThread2(DodgeTester $I, DodgeSteps $steps)
    {
        $this->selfCheckRobo($I, 4, 2);
    }

    /** @group self_check_thread_3 */
    public function selfCheckRoboThread3(DodgeTester $I, DodgeSteps $steps)
    {
        $this->selfCheckRobo($I, 4, 3);
    }

    /** @group self_check_thread_4 */
    public function selfCheckRoboThread4(DodgeTester $I, DodgeSteps $steps)
    {
        $this->selfCheckRobo($I, 4, 4);
    }
}
