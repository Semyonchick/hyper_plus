<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Megaplan\SimpleClient\Client;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MegaplanController extends Controller
{
    public $employeesMap = [
        1000002 => 69,
        1000015 => 55,
        1000007 => 75,
        1000040 => 37,
        1000005 => 73,
        1000006 => 61,
        1000030 => 57,
        1000041 => 63,
        1000001 => 41,
        1000017 => 51,
        1000033 => 59,
    ];

    private $log = [];
    private $auth = [];

    public function actionIndex()
    {
        $list = $this->get('/BumsCrmApiV01/Contractor/list.api');

        foreach ($list as $client)
            $this->addClient($client);
    }

    public function addClient($data)
    {
        $data = $this->get('/BumsCrmApiV01/Contractor/card.api', ['Id' => $data['Id']]);

        $list = $this->get('/BumsTradeApiV01/Deal/list.api', ['Contractor' => $data['Id']]);
        foreach ($list as $lead)
            $this->addLead($lead);

        print_r($data);
        die;
    }

    public function addLead($data)
    {
        $data = $this->get('/BumsTradeApiV01/Deal/card.api', ['Id' => $data['Id']]);

        print_r($data);
        die;
    }

    public function get($method, $params = null)
    {
        $ll = count($this->log);
        if (($ll > 3000 && ($spend = time() - $this->log[$ll - 3000]) && $spend < ($max = 3600)) ||
            ($ll >= 3 && ($spend = microtime(true) - $this->log[$ll - 3]) && $spend < ($max = 1))
        ) {
            sleep($max - $spend);
            return $this->get($method, $params);
        }

        $this->log[] = microtime(true);
        $result = $this->auth()->get($method, $params);
        if (is_string($result)) $result = json_decode($result);
        if ($result->status->code != 'ok') {
            throw new Exception($result->status->message);
        }

        $data = current(json_decode(json_encode($result->data), 1));

        return $data;
    }

    public function auth()
    {
        return $this->auth ?: ($this->auth = (new Client('espanarusa.megaplan.ru'))->auth('info@espanarusa.com', 'pylypenko1984'));
    }
}
