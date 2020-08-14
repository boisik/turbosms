<?php

/**
 * Класс для работы с сервисом sms.ru
 */
namespace Aturbosms\Model\SMS\Turbosms;

class Turbosms
{
    const CODE_SUCCESS = 100;


    protected
        $appid    = null,
        $response   = null;

    /**
     * Конструктор
     *
     * @param string $project
     * @param string $key
     * @param string $useSSL
     * @param integer $testMode
     */
    public function __construct($appid)
    {
        $this->appid = $appid;
    }


    /**
     * Отправить SMS
     *
     * @param string|array $recipients
     * @param string $message
     * @param string $sender
     * @param string $run_at
     *
     * @return boolean|integer
     */
    public function sendSMS($recipients, $message, $sender, $translit)
    {
        if (is_array($recipients)){
            $recipients = implode(',', $recipients);
        }

        $params = array(
            'to'            => $recipients,
            'text'          => $message,

        );

        if ($sender != '') {
            $params['from'] = $sender;
        }




        $response = $this->makeRequest('message/send.json', $params);

        return $response['code'] == self::CODE_SUCCESS;
    }


    /**
     * Отправить запрос
     *
     * @param string $function
     * @param array $params
     *
     * @return stdClass
     */
    protected function makeRequest($function, array $params = array())
    {

        $params = array_merge(array('api_id' => $this->appid), $params);

        $url = ' https://api.turbosms.ua/'. $function;
        $post = http_build_query($params);

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $post,
                    'timeout' => 10,
                ),
            ));
            $response = file_get_contents($url, false, $context);
        }
        return $this->response = $this->parseResponse($response, true);
    }

    private function parseResponse($response){
        $code = substr($response,0,3);
        switch ($code){
            case '200':$error = 'Неправильный api_id';break;
            case '201':$error = 'Не хватает средств на лицевом счету';break;
            case '202':$error = 'Неправильно указан получатель';break;
            case '203':$error = 'Нет текста сообщения';break;
            case '204':$error = 'Имя отправителя не согласовано с администрацией';break;
            case '205':$error = 'Сообщение слишком длинное (превышает 8 СМС)';break;
            case '206':$error = 'Будет превышен или уже превышен дневной лимит на отправку сообщений';break;
            case '207':$error = 'На этот номер (или один из номеров) нельзя отправлять сообщения, либо указано более 100 номеров в списке получателей';break;
            case '208':$error = 'Параметр time указан неправильно';break;
            case '209':$error = 'Вы добавили этот номер (или один из номеров) в стоп-лист';break;
            case '210':$error = 'Используется GET, где необходимо использовать POST';break;
            case '211':$error = 'Метод не найден';break;
            case '212':$error = 'Текст сообщения необходимо передать в кодировке UTF-8 (вы передали в другой кодировке)';break;
            case '220':$error = 'Сервис временно недоступен, попробуйте чуть позже.';break;
            case '230':$error = 'Превышен общий лимит количества сообщений на этот номер в день.';break;
            case '231':$error = 'Превышен лимит одинаковых сообщений на этот номер в минуту.';break;
            case '232':$error = 'Превышен лимит одинаковых сообщений на этот номер в день.';break;
            case '300':$error = 'Неправильный token (возможно истек срок действия, либо ваш IP изменился)';break;
            case '301':$error = 'Неправильный пароль, либо пользователь не найден';break;
            case '302':$error = 'Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)';break;
            default: $error = '';
        }

        return array(
                'code'  => $code,
                'msg'   => $error
            );
    }


    /**
     * Возвращает ответ сервера последнего запроса
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

}
