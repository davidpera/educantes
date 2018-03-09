------------------------------
-- Archivo de base de datos --
------------------------------

DROP TABLE IF EXISTS alumnos CASCADE;

CREATE TABLE alumnos
(
    id      bigserial       PRIMARY KEY
  , nombre  varchar(255)    NOT NULL
);

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id          bigserial       PRIMARY KEY
  , nombre      varchar(255)    NOT NULL UNIQUE
  , password    varchar(255)    NOT NULL
  , email       varchar(255)
  , tel_movil   numeric(9)      NOT NULL UNIQUE
  , rol         char(1)         NOT NULL
  , alumno_id   bigint          REFERENCES alumnos (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
);
