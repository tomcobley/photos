<?php

/**
 * SQLite connnection
 */
class SQLiteConnection {
    /**
     * PDO instance
     * @var type
     */
    private $pdo;


    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect($dbPath = "db/phpsqlite.db") {
        if ($this->pdo == null) {
          $this->pdo = new \PDO("sqlite:".$dbPath);
          $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $this->pdo;
    }

}


class SQLiteInsert {

    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;

    /**
     * Initialize the object with a specified PDO object
     * @param \PDO $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    /**
     * Insert a new content item into the content_items table
     */
    public function insertContentItem($contentType, $foreignId, $timestamp) {

      if ($contentType === 'image') {
        $imageId = $foreignId;
        $dividerId = NULL;
      } else {
        $imageId = NULL;
        $dividerId = $foreignId;
      }

      $sql = 'INSERT INTO content_items (content_type, image_id, divider_id, hidden, timestamp)
              VALUES (:content_type, :image_id, :divider_id, :hidden, :timestamp)';

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
          ':content_type' => $contentType,
          ':image_id' => $imageId,
          ':divider_id' => $dividerId,
          ':hidden' => 0,
          ':timestamp' => $timestamp
      ]);

      return $this->pdo->lastInsertId();
    }


    public function insertImage($imageCode, $title, $coords, $src, $thumbnailSrc) {

      // generate random ID for image (and check random ID doesn't already exist)
      $uniqueIdFound = false;
      while (!$uniqueIdFound) {
        $newId = rand(1000000000,9999999999);
        $checkSql = "SELECT * FROM images WHERE image_id = " . $newId . ";";
        $stmt = $this->pdo->query($checkSql);
        if (!($stmt->fetch(\PDO::FETCH_ASSOC))) {
          $uniqueIdFound = true;
        }
      }

      $sql = 'INSERT INTO images (image_id, image_code, title, coords, src, thumbnail_src)
              VALUES (:image_id, :image_code, :title, :coords, :src, :thumbnail_src)';

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
          ':image_id' => $newId,
          ':image_code' => $imageCode,
          ':title' => $title,
          ':coords' => $coords,
          ':src' => $src,
          ':thumbnail_src' => $thumbnailSrc
      ]);

      return $this->pdo->lastInsertId();
    }


    public function insertDivider($city = "", $country = "") {

      $sql = 'INSERT INTO dividers (city, country)
              VALUES (:city, :country)';

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
          ':city' => $city,
          ':country' => $country
      ]);

      return $this->pdo->lastInsertId();
    }

}



class SQLiteUpdate {

  /**
   * PDO object
   * @var \PDO
   */
  private $pdo;

  /**
   * Initialize the object with a specified PDO object
   * @param \PDO $pdo
   */
  public function __construct($pdo) {
      $this->pdo = $pdo;
  }


  public function updateRecord($table, $key_column, $key, $dataArray) {

    $sql = 'UPDATE ' . $table . ' SET';
    $counter = 1;
    foreach ($dataArray as $column => $value) {
      if (gettype($value) === "string") {
        $value = '"'.$value.'"';
      }
      $sql .= ' ' . $column . ' = ' . $value;

      $sql .= ($counter < count($dataArray) ? ',' : ' ');
      $counter++;
    }
    if (gettype($key) === "string") {
      $key = '"'.$key.'"';
    }
    $sql .= "WHERE $key_column = $key;";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute();

  }
}


class SQLiteFindRecord {

  /**
   * PDO object
   * @var \PDO
   */
  private $pdo;

  /**
   * Initialize the object with a specified PDO object
   * @param \PDO $pdo
   */
  public function __construct($pdo) {
      $this->pdo = $pdo;
  }

  public function search($table, $orderQuery, $searchColumn=1, $searchKey=1) {
    // returns array of matching data, or false
    // if no $searchColumn or $searchKey is defined, all data from table is returned

    if ($orderQuery === "default") {
      $orderQuery = "";
    }

    if (gettype($searchKey) === "string") {
      $searchKey = '"'.$searchKey.'"';
    }
    $checkSql = "SELECT * FROM $table WHERE $searchColumn = $searchKey " . $orderQuery . ";" ;

    try {
      $stmt = $this->pdo->query($checkSql);
      $result = [];
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $result[] = $row;
      }
      return $result;

    } catch (\PDOException $e) {
      error_log($e);
      throw $e;
    }

  }
}
