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
    public function connect() {
        if ($this->pdo == null) {
          $this->pdo = new \PDO("sqlite:db/phpsqlite.db");
          $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $this->pdo;
    }

}


/**
 * SQLite Create Table Demo
 */
class SQLiteCreateTable {

    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;

    /**
     * connect to the SQLite database
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * create tables
     */
    public function createTables() {
        $commands = ['CREATE TABLE IF NOT EXISTS projects (
                        project_id   INTEGER PRIMARY KEY,
                        project_name TEXT NOT NULL
                      )',
            'CREATE TABLE IF NOT EXISTS tasks (
                    task_id INTEGER PRIMARY KEY,
                    task_name  VARCHAR (255) NOT NULL,
                    completed  INTEGER NOT NULL,
                    start_date TEXT,
                    completed_date TEXT,
                    project_id VARCHAR (255),
                    FOREIGN KEY (project_id)
                    REFERENCES projects(project_id) ON UPDATE CASCADE
                                                    ON DELETE CASCADE)'];
        // execute the sql commands to create new tables
        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }
    }

    /**
     * get the table list in the database
     */
    public function getTableList() {

        $stmt = $this->pdo->query("SELECT name
                                   FROM sqlite_master
                                   WHERE type = 'table'
                                   ORDER BY name");
        $tables = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tables[] = $row['name'];
        }

        return $tables;
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
     * Insert a new project into the projects table
     * @param string $projectName
     * @return the id of the new project
     */
    public function insertProject($projectName) {
        $sql = 'INSERT INTO projects(project_name) VALUES(:project_name)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':project_name', $projectName);
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * Insert a new task into the tasks table
     * @param type $taskName
     * @param type $startDate
     * @param type $completedDate
     * @param type $completed
     * @param type $projectId
     * @return int id of the inserted task
     */
    public function insertTask($taskName, $startDate, $completedDate, $completed, $projectId) {
        $sql = 'INSERT INTO tasks(task_name,start_date,completed_date,completed,project_id) '
                . 'VALUES(:task_name,:start_date,:completed_date,:completed,:project_id)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':task_name' => $taskName,
            ':start_date' => $startDate,
            ':completed_date' => $completedDate,
            ':completed' => $completed,
            ':project_id' => $projectId,
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Insert a new content item into the content_items table
     */
    public function insertContentItem($contentType, $postion, $foreignId) {

      if ($contentType === 'image') {
        $imageId = $foreignId;
        $dividerId = "NULL";
      } else {
        $imageId = "NULL";
        $dividerId = $foreignId;
      }

      $sql = 'INSERT INTO content_items (content_type, position, image_id, divider_id)
              VALUES (:content_type, :position, :image_id, :divider_id)';

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
          ':content_type' => "image",
          ':position' => $postion,
          ':image_id' => $imageId,
          ':divider_id' => $dividerId
      ]);

      return $this->pdo->lastInsertId();
    }


    /**
     * Insert a new image item into the images table
     */
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

}

//
// INSERT INTO content_items
// (
//   content_item_id,
//   content_type,
//   position,
//   image_id,
//   divider_id
// )
// VALUES (
//   0,
//   "image",
//   4,
//   3,
//   NULL
// );


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
    echo $sql;
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

  public function search($table, $searchColumn=1, $searchKey=1) {
    // returns array of matching data, or false
    // if no $searchColumn or $searchKey is defined, all data from table is returned
    if (gettype($searchKey) === "string") {
      $searchKey = '"'.$searchKey.'"';
    }
    $checkSql = "SELECT * FROM $table WHERE $searchColumn = $searchKey;";

    try {
      $stmt = $this->pdo->query($checkSql);
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
