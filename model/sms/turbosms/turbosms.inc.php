<?php

/**
 * Класс для работы с сервисом sms.ru
 */
namespace Aturbosms\Model\SMS\Turbosms;

class Turbosms
{
    const CODE_SUCCESS = 0;


    protected

        $response   = null;


    function __construct()
    {
      $this->log_file = \RS\Helper\Log::file(\Setup::$PATH.\Setup::$STORAGE_DIR.'/logs/tusbosms.log');
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
    public function sendSMS($recipients, $message, $sender)
    {
        if (!is_array($recipients)) {
            $recipients = array('0' => $recipients);
        }


        $params = array(
            'recipients'   => $recipients,
            'sms'          => array('sender'=>$sender,'text'=>$message)

        );

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
        $config = \RS\Config\Loader::byModule($this);

        $params = array_merge(array('token' => $config->smsToken), $params);

        $url = 'https://api.turbosms.ua/'. $function;
        $post = http_build_query($params);

        if ($config['write_log']) {
            $log_str = "request: method = $function, params = ".serialize($params);
            $this->log_file->append($log_str);
        }


            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $post,
                    'timeout' => 10,
                ),
            ));


         $response = file_get_contents($url, false, $context);

var_dump($params);
        if ($config['write_log']) {
            $log_str = serialize($response);
            $this->log_file->append($log_str);
        }

        return $this->response = $this->parseResponse($response, true);
    }

    private function parseResponse($response){
        $code = substr($response,0,3);
        switch ($code){
            case '0':$error = 'Запрос обработан успешно.';break;
            case '1':$error = 'Успешный результат вызова метода ping.';break;
            case '103':$error = 'Отсутствует токен аутентификации.';break;
            case '104':$error = 'Отсутствуют данные запроса.';break;
            case '105':$error = 'Аутентификация не пройдена, не верный токен.';break;
            case '106':$error = 'Пользователь заблокирован, работа с API невозможна до разблокировки.';break;
            case '200':$error = 'Отсутствует или пустой параметр отправителя сообщения.';break;
            case '201':$error = 'Отсутствует или пустой параметр текста сообщения.';break;
            case '202':$error = 'Отсутствует или пустой список получателей сообщения.';break;
            case '203':$error = 'Не достаточно кредитов на балансе для создания рассылки.';break;
            case '204':$error = 'Отсутствуют или пустые параметры кнопки в сообщении, когда она обязательна.';break;
            case '205':$error = 'Отсутствует или пустой параметр текста на кнопке в сообщении.';break;
            case '206':$error = 'Отсутствует или пустой параметр URL адреса, куда перейдёт получатель сообщения при нажатии на кнопку.';break;

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
