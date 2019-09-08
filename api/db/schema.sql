CREATE TABLE content_items (
  content_item_id INTEGER PRIMARY KEY,
  content_type TEXT NOT NULL,
  position INTEGER NOT NULL,
  image_id INTEGER,
  divider_id INTEGER
);

INSERT INTO content_items
(
  content_item_id,
  content_type,
  position,
  image_id,
  divider_id
)
VALUES (
  0,
  "image",
  4,
  3,
  NULL
);

SELECT * FROM content_items;

CREATE TABLE images (
  image_id INTEGER PRIMARY KEY,
  image_code VARCHAR (255),
  title VARCHAR (255),
  coords VARCHAR (255),
  src TEXT NOT NULL,
  thumbnail_src TEXT NOT NULL
);
