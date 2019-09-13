
FORMAT

.headers on
.mode column




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
  0,
  2570015846,
  NULL
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
  5,
  "divider",
  3,
  NULL,
  1);

SELECT * FROM content_items;

CREATE TABLE images (
  image_id INTEGER PRIMARY KEY,
  image_code VARCHAR (255),
  title VARCHAR (255),
  coords VARCHAR (255),
  src TEXT NOT NULL,
  thumbnail_src TEXT NOT NULL
);

ALTER TABLE images
ADD image_timestamp DATETIME;


CREATE TABLE dividers (
  divider_id INTEGER PRIMARY KEY,
  city VARCHAR (255),
  country VARCHAR (255)
);

INSERT INTO dividers
(
  divider_id,
  city,
  country
)
VALUES (
  1,
  "Sao Paulo",
  "Brasil"
);


update images set src = "http://localhost:80/public/images/9606362007.jpg",
  thumbnail_src = "http://localhost:80/public/thumbnails/9606362007.jpg"
  where src = "http://localhost:80/public/images/9606362007";


CREATE TABLE images_ (
image_id INTEGER PRIMARY KEY,
image_code VARCHAR (255),
title VARCHAR (255),
coords VARCHAR (255),
src TEXT NOT NULL,
thumbnail_src TEXT NOT NULL
, altitude VARCHAR (255), image_timestamp DATETIME);


// REMOVE COLUMN FROM TABLE
