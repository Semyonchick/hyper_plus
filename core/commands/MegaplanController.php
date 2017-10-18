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
        $fields = $this->get('/BumsCrmApiV01/Contractor/listFields.api');

//        print_r($fields);

        foreach($list as $client)
            $this->addClient($client, $fields);
    }

    public function addClient($data, $fields){
        print_r($data);
        $data = $this->get('/BumsCrmApiV01/Contractor/card.api', ['Id'=>$data['Id'], 'RequestedFields', array_map(function($row){
            return $row['Name'];
        }, $fields)]);
        print_r($data);
        die;
    }

    public function get($method, $params = null)
    {
        $logLength = count($this->log);
        if ($logLength > 3000) {
            if ($this->log[$logLength - 3000] > time() - 3600) {
                sleep(1);
                return $this->get($method, $params);
            }
        }
        sleep(0.3);

        $this->log[] = time();
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
