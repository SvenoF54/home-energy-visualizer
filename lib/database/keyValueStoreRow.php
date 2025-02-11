<?php

class KeyValueStoreRow
{
    private $scope;
    private $storeKey;
    private $value;
    private $jsonData;
    private $notice;
    private $updated;
    private $inserted;

    public function __construct($scope = null, $storeKey = null, $value = null, $jsonData = null, $notice = null, $updated = null, $inserted = null)
    {
        $this->scope = $scope;
        $this->storeKey = $storeKey;
        $this->value = $value;
        $this->jsonData = $jsonData;
        $this->notice = $notice;
        $this->updated = $updated;
        $this->inserted = $inserted;
    }

    public static function createFromRow(array $row)
    {
        return new self(
            $row['scope'] ?? null,
            $row['store_key'] ?? null,
            $row['value'] ?? null,
            $row['json_data'] ?? null,
            $row['notice'] ?? null,
            $row['updated'] ?? null,
            $row['inserted'] ?? null
        );
    }

    public function getScope() { return $this->scope; }
    public function getStoreKey() { return $this->storeKey; }
    public function getValue() { return $this->value; }
    public function getJsonData() { return $this->jsonData; }
    public function getNotice() { return $this->notice; }
    public function getUpdated() { return $this->updated; }
    public function getInserted() { return $this->inserted; }

    public function setScope($scope) { $this->scope = $scope; }
    public function setStoreKey($storeKey) { $this->storeKey = $storeKey; }
    public function setValue($value) { $this->value = $value; }
    public function setJsonData($jsonData) { $this->jsonData = $jsonData; }
    public function setNotice($notice) { $this->notice = $notice; }
    public function setUpdated($updated) { $this->updated = $updated; }
    public function setInserted($inserted) { $this->inserted = $inserted; }
}
