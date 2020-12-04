<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 22.04.19
 * Time: 15:03
 */

namespace dodge\tests;

use dodge\DodgeTester;

class storeShotsCest
{
    /**
     * Перемещает все скриншоты из каталога tests/dodge/_data/shots/temp в каталог tests/dodge/_data/shots
     *
     * Руками это сделать сложнее т.к. каждый скриншот содержит в названии MD5 сумму своего содержимого.
     *
     * @param DodgeTester $I
     */
    public function acceptTemp(DodgeTester $I)
    {
        $I->wantTo('Принять скриншоты из каталога temp');

        $I->acceptTempShots();
    }

    /**
     * Загружает все скриншоты из каталога tests/dodge/_data/shots в S3
     *
     * @param DodgeTester $I
     */
    public function uploadShots(DodgeTester $I)
    {
        $I->wantTo('Загрузить скриншоты в S3');

        $I->uploadShots();
    }
}
