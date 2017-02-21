<?php
require_once('Classes/PHPExcel/IOFactory.php');
require_once('db_connect.php');

Logger::log("start script");

$start_time = time();

$xls = PHPExcel_IOFactory::load('parts_2017.xlsx');
// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
//Узнаем колличество строк в документе
$count = $sheet->getHighestRow();

$start = 2;

$res = array();
try{
    // соединяемся с базой данных
    $db = Database_connect::getInstance();
    $db->beginTransaction();
    for ($rows=1,$i=$start; $i <= $count; $i++)
    {
        if (($rows % 1000) == 0)
        {
            try {
                $db->commit();
                var_dump($db->errorCode());
            }catch (Exception $e)
            {
                Logger::log("SQL ошибка: ". $db->errorCode() .' '. $e->getMessage());
                $db->rollBack();
            }
            Logger::log('COMMIT' . $rows);
        }
        $row = new stdClass();

        $row->name = $xls->getActiveSheet()->getCell('A'.$i )->getValue();
        $row->article = $xls->getActiveSheet()->getCell('F'.$i )->getValue();

        if ($row->name == null) continue;
        $db->prepare("INSERT INTO `products` (`article`,`name`) VALUES('".$row->article."', '".$row->name."') ON DUPLICATE KEY UPDATE `name` = '". $row->name ."'");

        #$sth =$db->prepare("INSERT INTO `products` (`article`,`name`) VALUES(':article', ':name') ON DUPLICATE KEY UPDATE `name` = ':name'");
        #$sth->bindValue(':article', $row->article, PDO::PARAM_STR );
        #$sth->bindValue(':name', $row->name, PDO::PARAM_STR );
        $db->execute();
        var_dump($db->errorCode());
        $rows++;
    }
    $db->commit();
    Logger::log('COMMIT' . $rows);
}
catch (Exception $e)
{
    Logger::log("SQL ошибка: ". $db->errorCode() .' '. $e->getMessage());
    $db->rollBack();

}


if($rows) Logger::log("Количество затронутых строк: " . $rows . 'из ' . $count);

$end_time = time();
Logger::log("End Script ". date("Y-m-d H:i:s", ($start_time)) . ' ' .  date("Y-m-d H:i:s",($end_time)));
