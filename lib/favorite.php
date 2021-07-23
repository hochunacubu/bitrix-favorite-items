<?php
\Bitrix\Main\Loader::IncludeModule("highloadblock");
/*
 * Избранное
 */
class Favorite
{
    public $userId;

    private $hbId = FAVORITE_HB; //id highloadblock в котором хранятся избранные товары

    public $favId;

    public function __construct($userId)
    {
        $this->setUserId($userId);
    }

    public function setHbId($hbId)
    {
        $this->hbId = $hbId;
    }

    public function getHbId()
    {
        return $this->hbId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getFavoriteObj()
    {
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($this->hbId)->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }

    /**
     * @return array items id
     */
    public function getListByIds($ids = [])
    {
        $list = [];
        $favoriteObg = $this->getFavoriteObj();
        $userId = $this->getUserId();
        $result = $favoriteObg::getList([
            "select" => ["*"],
            "order"  => ["ID" => "DESC"],
            "filter" => [
                "UF_USER_ID" => $userId,
                "UF_ITEM_ID" => $ids,
            ],

        ]);

        while ($arRow = $result->Fetch()) {
            $list[] = $arRow['UF_ITEM_ID'];
        }

        return $list;
    }

    /**
     * @return array items id
     */
    public function getList()
    {
        $list = [];
        $favoriteObg = $this->getFavoriteObj();
        $userId = $this->getUserId();
        $result = $favoriteObg::getList([
            "select" => ["*"],
            "order"  => ["ID" => "DESC"],
            "filter" => ["UF_USER_ID" => $userId],

        ]);

        while ($arRow = $result->Fetch()) {
            $list[] = $arRow['UF_ITEM_ID'];
        }

        return $list;
    }

    public function setFavoriteId($id)
    {
        $this->favId = $id;
    }

    public function getFavoriteId()
    {
        return $this->favId;
    }

    public function save()
    {
        $favId = $this->getFavoriteId();
        $userId = $this->getUserId();
        $favoriteObg = $this->getFavoriteObj();
        $arFields = array (
            'UF_USER_ID' => $userId,
            'UF_ITEM_ID' => $favId
        );

        $result = $favoriteObg::add($arFields);
        if($result->isSuccess()) {
            return true;
        } else {
            return false;
        }
    }

    public function getRowId()
    {
        $favoriteObg = $this->getFavoriteObj();
        $userId = $this->getUserId();
        $favId = $this->getFavoriteId();
        $result = $favoriteObg::getList([
            "select" => ["ID"],
            "filter" => [
                "UF_USER_ID" => $userId,
                'UF_ITEM_ID' => $favId
            ],

        ]);

        if ($row = $result->Fetch()) {
            return $row['ID'];
        } else {
            return false;
        }
    }

    public function delete()
    {
        $favoriteObg = $this->getFavoriteObj();
        $rowId = $this->getRowId();
        if (empty($rowId)) {
            return false;
        }
      
        if ($favoriteObg::delete($rowId)) {
            return true;
        } else {
            return false;
        }
    }

    public function isFavorite()
    {
        $rowId = $this->getRowId();

        return (!empty($rowId)) ? true : false;
    }
}
