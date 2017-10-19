<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use linslin\yii2\curl\Curl;
use Megaplan\SimpleClient\Client;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MegaplanController extends Controller
{
    public $url = 'https://espanarusa.bitrix24.ru/rest/49/yzmwq2ftvomdnpre/';

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
//        $list = $this->get('/BumsTimeApiV01/Event/list.api', ['OnlyActual'=>false]);
//        print_r($list);die;
//        foreach ($list as $client)
//            $this->addClient($client);

        $list = $this->get('/BumsCrmApiV01/Contractor/list.api');

        foreach ($list as $client)
            $this->addClient($client);
    }

    public function addClient($data)
    {
        $list = $this->get('/BumsTradeApiV01/Deal/list.api', ['FilterFields' => ['Contractor' => $data['Id']]]);
        $list = array_filter($list, function ($row) use ($data) {
            return $row['Contractor']['Id'] == $data['Id'];
        });

        if (empty($list) && empty($data['ParentCompany'])) $object = 'lead';
        elseif ($data['PersonType'] == 'human') $object = 'contact';
        else $object = 'company';

        $params = [
            "ORIGINATOR_ID" => "megaplan", // Идентификатор внешней информационной базы
            "ORIGIN_ID" => $data['Id'], // Внешний ключ
        ];

        $result = $this->exist('crm.' . $object . '.list', $params);
        if (!$result) {
            $data = ArrayHelper::merge($data, $this->get('/BumsCrmApiV01/Contractor/card.api', ['Id' => $data['Id'], 'RequestedFields' => array_map(function ($row) {
                return $row['Name'];
            }, $this->get('/BumsCrmApiV01/Contractor/listFields.api'))]));
            $params += [
                'TITLE' => $data['Name'], // Название. Обязательное поле.
                "POST" => "", // Должность
                "COMMENTS" => nl2br(trim($data['Description'])), // Комментарии
                "EMAIL" => [["VALUE" => $data['Email'], "VALUE_TYPE" => "WORK"]], // e-mail
                "PHONE" => array_map(function ($row) {
                    return ["VALUE" => $row, "VALUE_TYPE" => "WORK"];
                }, array_unique($data['Phones'])), // Телефон
                "ADDRESS" => preg_replace('#<br[^>]*>#', "\n", implode(",\n ", array_map(function ($row) {
                    return $row['Address'];
                }, array_diff_key($data['Locations'], ['', 'Адрес не указан'])))), // Адрес
                "ASSIGNED_BY_ID" => $this->employeesMap[$data['Responsibles'][0]['Id']] ?: 41, // Ответственный
                "SOURCE_ID" => 'OTHER', // Источник
                "BIRTHDATE" => $data['Birthday'] ?: $data['Category183CustomFieldDenRozhdeniya'], // Дата рождения
                "WEB" => $data['Site'], // веб-сайт
                "OPENED" => 'Y', // Доступен всем

                "STATUS_ID" => "1", // Идентификатор статуса лида

                "NAME" => $data['FirstName'], // Имя
                "SECOND_NAME" => $data['MiddleName'], // Отчество
                "LAST_NAME" => $data['LastName'], // Фамилия
                "COMPANY_TITLE" => $data['CompanyName'], // Название компании
                "TYPE_ID" => $data['Type']['Name'] == 'Партнеры' ? 'PARTNER' : 'CLIENT', // Тип контакта
                "HONORIFIC" => $data['Gender'] ? $data['Gender'] == 'male' ? 'HNR_RU_1' : 'HNR_RU_2' : '', // Обращение

                "COMPANY_TYPE" => $data['Type']['Name'] == 'Партнеры' ? 'PARTNER' : 'CUSTOMER', // Тип компании

//                "CREATED_BY_ID" => $this->employeesMap[$data['Responsibles'][0]['Id']], // Создан
//                "MODIFY_BY_ID" => $this->employeesMap[$data['Responsibles'][0]['Id']], // Изменен
//                "DATE_CREATE" => $data['TimeCreated'], // Дата создания
//                "DATE_MODIFY" => $data['TimeUpdated'], // Дата изменения
            ];
            foreach (['Icq', 'Facebook', 'Jabber', 'Skype', 'Twitter'] as $im) if ($data[$im]) {
                $params['IM'][] = ["VALUE" => $data[$im], "VALUE_TYPE" => $im];
            }
            if ($data['ParentCompany']) {
                $company = $this->exist('crm.company.list', [
                    "ORIGINATOR_ID" => "megaplan", // Идентификатор внешней информационной базы
                    "ORIGIN_ID" => $data['ParentCompany']['Id'],// Внешний ключ
                ]);
                $params['COMPANY_ID'] = $company;
            }

            $result = $this->add('crm.' . $object . '.add', $params);
        }

        if ($data['Attaches']) {
//            print_r($data);
//            print_r($params);
//            die;
        }

//        foreach ($list as $lead) $this->addLead($result, $lead);

//        print_r($params);
//        print_r($result);
//        die;

//        if (!empty($result)) return;
//
//        print_r($data);
//        die;
    }

    public function addLead($client_id, $data)
    {
        $data = $this->get('/BumsTradeApiV01/Deal/card.api', ['Id' => $data['Id']]);

        print_r($client_id);
        print_r($data);
        die;
    }

    public function get($method, $params = null)
    {
        $cacheId = $method . serialize($params ?: []);
        $data = \Yii::$app->cache->get($cacheId);
        if (1 || $data === false) {
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

            \Yii::$app->cache->set($cacheId, $data, 3600);
        }

        return $data;
    }

    public function auth()
    {
        return $this->auth ?: ($this->auth = (new Client('espanarusa.megaplan.ru'))->auth('info@espanarusa.com', 'pylypenko1984'));
    }

    public function exist($method, $data)
    {
        if (!$data['ORIGIN_ID']) return false;

        $curl = new Curl();
        $result = $curl->get($this->url . str_replace(['.add', '.update'], '.list', $method) . '/?' . http_build_query(['filter' => [
                'ORIGINATOR_ID' => $data['ORIGINATOR_ID'],
                'ORIGIN_ID' => $data['ORIGIN_ID']
            ]]));
        $result = JSON::decode($result);

        if ($result['total']) return $result['result'][0]['ID'];

        return false;
    }

    public function add($method, $data)
    {
        $curl = new Curl();
        $curl->setPostParams(['fields' => $data, 'params' => ['REGISTER_SONET_EVENT' => 'N']]);
        $result = $curl->post($this->url . '' . $method . '/', true);
        $result = JSON::decode($result);

        if (!$result['result']) {
            print_r($data);
            print_r($result);
            die;
        }
        return $result['result'];
    }
}
