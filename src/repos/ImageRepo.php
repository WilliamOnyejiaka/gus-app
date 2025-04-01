<?php

declare(strict_types=1);

namespace Repo;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";

use Model\Image;
use Lib\Serializer;
use Lib\Database;
use Lib\Pagination;
use Lib\SearchPagination;

class ImageRepo
{

    protected array $neededRows = ["id", "fileName", "url", "name", "description", "created_at", "updated_at"];
    protected Image $model;
    protected array $allRows;

    public function __construct()
    {
        $this->allRows = [...$this->neededRows];
        $this->model = new Image();
    }

    public function create(array $params)
    {
        return $this->model->insert(...$params);
    }

    public function getWithUrl(string $url)
    {
        return Serializer::tuple(
            $this->model->getWithUrl($url),
            $this->neededRows
        );
    }

    public function getWithFileName(string $fileName)
    {
        return Serializer::tuple(
            $this->model->getWithFileName($fileName),
            $this->neededRows
        );
    }

    public function getWithId(int $id)
    {
        return Serializer::tuple(
            $this->model->getWithId($id),
            $this->neededRows
        );
    }

    public function updateName(string $name, int $id)
    {
        return $this->model->updateName($name, $id);
    }

    public function updateDescription(string $description, int $id)
    {
        return $this->model->updateDescription($description, $id);
    }

    public function update(string $name, string $description, int $id)
    {
        return $this->model->updateDetails($name, $description, $id);
    }

    public function delete(int $id)
    {
        return $this->model->delete($id);
    }

    public function getPagination(mixed $pageParams)
    {
        $connection = $this->model->connection;
        $sql = "SELECT * FROM images";
        $pagination = new Pagination($connection, $sql, $this->neededRows, [
            'page' => $pageParams['page'],
            'results_per_page' => $pageParams['limit']
        ]);

        return $pagination->meta_data();
    }

    public function searchPagination(string $keyword, array $searchParams, array $pageParams)
    {
        $connection = $this->model->connection;
        $sql = "SELECT * FROM images";
        $keyword = htmlentities(strip_tags($keyword));

        $pagination = new SearchPagination($connection, $sql, $this->neededRows, $keyword, $searchParams, [
            'page' => $pageParams['page'],
            'results_per_page' => $pageParams['limit']
        ]);

        return $pagination->meta_data();
    }
}
