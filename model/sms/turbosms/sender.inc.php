<?php

namespace Aturbosms\Model\SMS\Turbosms;

class Sender extends \Alerts\Model\SMS\AbstractSender
{
    /**
    * Возвращает сокращенное название провайдера (только латинские буквы)
    * @return string
    */
    public function getShortName()
    {
        return 'turbosms';
    }

    /**
    * Возвращает отображаемое название провайдера
    * @return string
    */
    public function getTitle()
    {
        return t('Turbosms');
    }

    /**
    * Отправка SMS
    *
    * @param string $text
    * @param array $phone_numbers
    */
    public function send($text, $phone_numbers)
    {
        $config = \RS\Config\Loader::byModule($this);

        // Если не указан логин, отправка не выполняется
        if(!$config['smsToken']){
            return;
        }


        $api = new Turbosms();
        $res = $api->sendSMS ( $phone_numbers , $text , $config['smsSender']);
        if($res == false){
            $response = $api->getResponse ();
            throw new \Exception($response['msg'], $response['code']);
        }
    }

}