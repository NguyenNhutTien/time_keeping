<?php

namespace Application\Model;

use RuntimeException;
// use Laminas\Db\TableGateway\TableGatewayInterface;
use Zend\Db\TableGateway\TableGatewayInterface;

class SettingDayTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getSettingDay($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveSettingDay(SettingDay $settingDay)
    {
        $data = [
            'type' => $settingDay->type,
            'from' =>  $settingDay->from,
            'to' =>  $settingDay->to,
            'off_date' => $settingDay->off_date,
            'half_day' =>  $settingDay->half_day,
            'note' => $settingDay->note,
            'created_at' => $settingDay->created_at,
            'created_by' => $settingDay->created_by,
        ];

        $id = (int) $settingDay->id;   

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getSettingDay($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update setting-day with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteSettingDay($id)
    {   
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}