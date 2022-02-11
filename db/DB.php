<?php

namespace app\db;

use Yii;

class DB
{
    private string $db = '/db/users.txt';

    protected function connect(): string
    {
        return Yii::getAlias('@app').$this->db;
    }

    public function getAll()
    {
        $result = file_get_contents(Yii::getAlias('@app').$this->db);

        return json_decode($result, true);
    }

    public function insert($data)
    {
        $result = $this->getAll();

        foreach ($result as $value) {
            if ($value['username'] == $data['username']) {
                $key = array_search($value, $result);
                unset($result[$key]);
            }
        }

        $data['id'] = $this->getID();
        $result[] = $data;
        $fp = fopen(Yii::getAlias('@app').$this->db, 'w+');
        fwrite($fp, json_encode($result));
        fclose($fp);
    }

    public function getID()
    {
        $result = $this->getAll();

        return empty($result) ? 1 : end($result)['id'] + 1;
    }

    public static function getByUsername($username)
    {
        $result = (new DB)->getAll();

        foreach ($result as $value) {
            if ($value['username'] == $username)
                return $value;
        }

        return null;
    }

    public static function getById($id)
    {
        $result = (new DB)->getAll();

        foreach ($result as $value) {
            if ($value['id'] == $id)
                return $value;
        }

        return null;
    }
}
