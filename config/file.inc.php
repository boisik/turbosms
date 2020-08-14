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
                'description' => t('Имя отправителя'),
                'hint' => t('Отправитель должен быть активирован в Вашем аккаунте')

            )),
            'write_log' => new Type\Integer(array(
                'description' => t('Записывать лог запросов'),
                'checkboxView' => array(1,0),
            )),
        ));

    }
}
