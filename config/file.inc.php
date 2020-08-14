<?php

namespace Aturbosms\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;

/**
 * Класс конфигурации модуля
 */
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append(array(
            'smsToken' => new Type\Varchar(array(
                'maxLength' => 255,
                'description' => t('Token'),
                'hint' => t('https://turbosms.ua/route.html')

            )),
            'smsSender' => new Type\Varchar(array(
                'maxLength' => 255,
                'description' => t('Имя отпраавителя'),
                'hint' => t('Отправитель должен быть активирован в Вашем аккаунте')

            )),
        ));

    }
}
