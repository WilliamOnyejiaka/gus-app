<?php

declare(strict_types=1);

namespace Model;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
include_once __DIR__ . "/../config/config.php";


use Lib\Model;

class Image extends Model
{

    public function __construct()
    {
        parent::__construct(...config('connectVars'));
        $this->tblName = "images";
        $this->createQuery = "CREATE TABLE IF NOT EXISTS $this->tblName (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            url Text NOT NULL,
            fileName Text NOT NULL,
            name VARCHAR(50),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
    }

    public function insert(string $url, string $fileName, string $name, string $description)
    {
        $sql = "INSERT INTO $this->tblName(url,fileName,name,description) VALUES(?,?,?,?)";

        $url = parent::sanitize($url);
        $name = parent::sanitize($name);
        $description = parent::sanitize($description);
        $fileName = parent::sanitize($fileName);

        return parent::affectRowQuery($sql, [$url, $fileName, $name, $description]);
    }

    public function getWithUrl(string $url)
    {
        $sql = "SELECT * FROM $this->tblName WHERE url = ?";
        $url = parent::sanitize($url);

        return parent::queryWithParams($sql, [$url]);
    }

    public function getWithFileName(string $fileName)
    {
        $sql = "SELECT * FROM $this->tblName WHERE fileName = ?";
        $fileName = parent::sanitize($fileName);

        return parent::queryWithParams($sql, [$fileName]);
    }

    public function getWithId(int $id)
    {
        $sql = "SELECT * FROM $this->tblName WHERE id = ?";
        return parent::queryWithParams($sql, [$id]);
    }

    private function update(string $sql, array $params)
    {
        $sql = "UPDATE $this->tblName SET " . $sql;
        return parent::affectRowQuery($sql, $params, true);
    }

    public function updateName(string $name, int $id)
    {
        $name = parent::sanitize($name);
        return $this->update("name = ? WHERE id = ?", [$name, $id]);
    }

    public function updateDescription(string $description, int $id)
    {
        $description = parent::sanitize($description);
        return $this->update("description = ? WHERE id = ?", [$description, $id]);
    }

    public function updateDetails(string $name,string $description, int $id)
    {
        $description = parent::sanitize($description);
        $name = parent::sanitize($name);
        return $this->update("name = ? ,description = ? WHERE id = ?", [$name,$description, $id]);
    }

    public function delete(int $id)
    {
        $sql = "DELETE FROM $this->tblName WHERE id = ?";
        return parent::affectRowQuery($sql, [$id]);
    }
}
