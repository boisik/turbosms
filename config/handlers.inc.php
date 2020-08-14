<?php

namespace Aturbosms\Config;

use RS\Event\HandlerAbstract;
use RS\Router\Route as RouterRoute;

/**
 * Класс содержит обработчики событий, на которые подписан модуль
 */
class Handlers extends HandlerAbstract
{
    function init()
    {
        $this
            ->bind('alerts.getsmssenders');
    }


    public static function alertsGetSmsSenders($list)
    {
        $list[] = new \Aturbosms\Model\SMS\turbosms\Sender();
        return $list;
    }
}
