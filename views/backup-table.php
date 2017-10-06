<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 07.06.2017
 * Time: 18:16
 *
 * @var $backup array
 */

?>
<html lang="ru-RU">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        table {
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .negative {
            background-color: rgba(255, 0, 0, 0.4);
        }

        .positive {
            background-color: rgba(50, 200, 0, 0.5);
        }
    </style>
</head>
<body>

<? if ($data = $backup[$_GET['date']]['data']): ?>
    <table border="1" cellpadding="10" cellspacing="10">
        <? foreach ($data['steps'] as $ask): ?>
            <tr>
                <td id="<?= $ask['id'] ?>">
                    <h3><?= $ask['title'] ?></h3>
                    <div class="description"><?= $ask['text'] ?></div>
                    <div class="buttons"><?= implode(' ', array_map(function ($row) {
                            return '<a href="#' . $row['target'] . '"><button class="' . $row['status'] . '">' . $row['condition'] . '</button></a>';
                        }, array_filter($data['connections'], function ($row) use ($ask) {
                            return $ask['id'] == $row['source'];
                        }))); ?></div>
                </td>
            </tr>
        <? endforeach ?>
    </table>
<? endif ?>

</body>
</html>