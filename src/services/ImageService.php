<?php

declare(strict_types=1);

namespace Service;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Repo\ImageRepo;
use Service\Service;
use Util\ImageUtil;

class ImageService extends Service
{

    public ImageRepo $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo =  new ImageRepo();
    }


    public function create(string $name, string $description, mixed $imageFile): mixed
    {
        $uploadResult = ImageUtil::upload($imageFile);
        if (!$uploadResult['error']) {
            $url = $uploadResult['url'];
            $fileName = $uploadResult['fileName'];
            $created = $this->repo->create([
                'name' => $name,
                'fileName' => $fileName,
                'description' => $description,
                'url' => $url
            ]);

            if ($created) {
                $repoResult = $this->repo->getWithUrl($url);
                return $this->responseData(201, false, "Image has been uploaded successfully", $repoResult);
            }

            return $this->responseData(500, true, "Something went wrong");
        }

        return $this->responseData(500, true, "Something went wrong");
    }

    private function isTheSame(array $columns, int $id, array $columnDatas)
    {
        $item = $this->repo->getWithId($id);
        $areTheSame = [];
        foreach ($columns as $columnName) {
            array_push($areTheSame, $item[$columnName] == $columnDatas[$columnName]);
        }
        return [
            'data' => $item,
            'isTheSame' => in_array(false, $areTheSame)
        ];
    }

    private function wasUpdated(bool $result)
    {
        if ($result) {
            return $this->responseData(200, false, "user was updated");
        }

        return $this->responseData(500, true, "Something went wrong");
    }


    public function readWithUrl(string $url)
    {
        $repoResult = $this->repo->getWithUrl($url);
        if ($repoResult) return $this->responseData(200, false, "Image has been retrieved successfully", $repoResult);
        return $this->responseData(404, true, "Image was not found");
    }

    public function readWithFileName(string $fileName)
    {
        $repoResult = $this->repo->getWithFileName($fileName);
        if ($repoResult) return $this->responseData(200, false, "Image has been retrieved successfully", $repoResult);
        return $this->responseData(404, true, "Image was not found");
    }

    public function readWithId(int $id)
    {
        $repoResult = $this->repo->getWithId($id);
        if ($repoResult) return $this->responseData(200, false, "Image has been retrieved successfully", $repoResult);
        return $this->responseData(404, true, "Image was not found");
    }

    public function updateName(string $name, int $id)
    {
        $theSame = $this->isTheSame(['name'], $id, ['name' => $name]);
        if ($theSame['data'] && $theSame['isTheSame']) {
            return $this->responseData(200, false, "Image name was updated", $theSame['data']);
        }
        $updated = $this->repo->updateName($name, $id);
        if ($updated) return $this->readWithId($id);
        return $this->responseData(500, true, "Something went wrong");
    }

    public function updateDescription(string $description, int $id)
    {
        $theSame = $this->isTheSame(['description'], $id, ['description' => $description]);
        if ($theSame['data'] && $theSame['isTheSame']) {
            return $this->responseData(200, false, "Image description was updated", $theSame['data']);
        }
        $updated = $this->repo->updateDescription($description, $id);
        if ($updated) return $this->readWithId($id);
        return $this->responseData(500, true, "Something went wrong");
    }

    public function update(string $name, string $description, int $id)
    {
        $theSame = $this->isTheSame(['description', 'name'], $id, ['description' => $description, 'name' => $name]);
        if ($theSame['data'] && !$theSame['isTheSame']) {
            return $this->responseData(200, false, "Image was updated", $theSame['data']);
        }
        $updated = $this->repo->update($name, $description, $id);
        if ($updated) return $this->readWithId($id);
        return $this->responseData(500, true, "Something went wrongs", $theSame);
    }

    public function delete(int $id)
    {
        $repoResult = $this->repo->getWithId($id);
        if ($repoResult && ImageUtil::delete($repoResult['url'])) {
            $this->repo->delete($id);
            return $this->responseData(200, false, "Image was deleted");
        }
        return $this->responseData(404, true, "Image was not found", $repoResult);
    }

    public function getPagination(int $page, int $limit)
    {
        $repoResult = $this->repo->getPagination([
            'page' => $page,
            'limit' => $limit
        ]);
        return $this->responseData(200, false, "Images were retrieved successfully", $repoResult);
    }

    public function searchPagination(string $keyword, int $page, int $limit)
    {
        $repoResult = $this->repo->searchPagination(
            $keyword,
            ['name'],
            [
                'page' => $page,
                'limit' => $limit
            ]
        );
        return $this->responseData(200, false, "Images were retrieved successfully", $repoResult);
    }
}
