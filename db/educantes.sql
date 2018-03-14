------------------------------
-- Archivo de base de datos --
------------------------------

DROP TABLE IF EXISTS uniformes CASCADE;

CREATE TABLE uniformes
(
    id          bigserial       PRIMARY KEY
  , codigo      varchar(255)    NOT NULL UNIQUE
  , descripcion varchar(255)    NOT NULL
  , talla       varchar(255)    NOT NULL
  , precio      numeric(5,2)    NOT NULL
  , IVA         numeric(3)      NOT NULL
  , ubicacion   varchar(255)
  , cantidad    numeric(10)     NOT NULL
);

DROP TABLE IF EXISTS secstocks CASCADE;

CREATE TABLE secstocks
(
    id          bigserial       PRIMARY KEY
  , CD          numeric(10)     NOT NULL --consumo diario
  , PE          numeric(10)     NOT NULL --numero de dias que tarda en llegar del proveedor
  , SS          numeric(10)     NOT NULL --Security Stock, determinado por el usuario
  , MP          numeric(10)     NOT NULL --Momento de pedido, calculado de los datos anteriores
  , uniforme_id bigint          NOT NULL REFERENCES uniformes (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
);

DROP TABLE IF EXISTS colegios CASCADE;

CREATE TABLE colegios
(
    id          bigserial       PRIMARY KEY
  , cif         varchar(255)    NOT NULL UNIQUE
  , nombre      varchar(255)    NOT NULL UNIQUE
  , email       varchar(255)    NOT NULL UNIQUE
  , cod_postal  numeric(5)      NOT NULL
  , direccion   varchar(255)    NOT NULL
);

DROP TABLE IF EXISTS libros CASCADE;

CREATE TABLE libros
(
    id          bigserial       PRIMARY KEY
  , ISBN        numeric(13)     NOT NULL
  , titulo      varchar(255)    NOT NULL
  , curso       varchar(255)    NOT NULL
  , precio      numeric(5,2)    NOT NULL
  , colegio_id  bigint          NOT NULL REFERENCES colegios (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
);

DROP TABLE IF EXISTS alumnos CASCADE;

CREATE TABLE alumnos
(
    id          bigserial       PRIMARY KEY
  , codigo      varchar(255)    NOT NULL UNIQUE
  , nombre      varchar(255)    NOT NULL
  , apellidos   varchar(255)    NOT NULL
  , fech_nac    date            NOT NULL
  , nom_padre   varchar(255)    NOT NULL
  , nom_madre   varchar(255)    NOT NULL
  , colegio_id  bigint          NOT NULL REFERENCES colegios (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
);

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id          bigserial       PRIMARY KEY
  , nom_usuario varchar(255)    NOT NULL UNIQUE
  , password    varchar(255)    NOT NULL
  , nombre      varchar(255)
  , apellidos   varchar(255)
  , nif         char(10)        UNIQUE
  , direccion   varchar(255)
  , email       varchar(255)    UNIQUE
  , tel_movil   numeric(9)      UNIQUE
  , rol         char(1)         NOT NULL
);

DROP TABLE IF EXISTS sms CASCADE;

CREATE TABLE sms
(
    id          bigserial       PRIMARY KEY
  , emisario_id bigint          NOT NULL REFERENCES colegios (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
  , receptor_id bigint          NOT NULL REFERENCES usuarios (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
  , mensaje     varchar(255)    NOT NULL
);

DROP TABLE IF EXISTS correos CASCADE;

CREATE TABLE correos
(
    id          bigserial       PRIMARY KEY
  , emisario_id bigint          NOT NULL REFERENCES colegios (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
  , receptor_id bigint          NOT NULL REFERENCES usuarios (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
  , mensaje     varchar(255)    NOT NULL
);

DROP TABLE IF EXISTS facturas CASCADE;

CREATE TABLE facturas
(
    id              bigserial   PRIMARY KEY
  , usuario_id      bigint      NOT NULL REFERENCES usuarios (id)
                                ON DELETE NO ACTION ON UPDATE CASCADE
  , fecha           date        DEFAULT current_date
  , descuento       numeric(3)  DEFAULT 0
);

DROP TABLE IF EXISTS detalles CASCADE;

CREATE TABLE detalles
(
    num_detalle     bigserial
  , factura_id      bigint
  , uniformes_id    bigint          NOT NULL REFERENCES uniformes (id)
                                    ON DELETE NO ACTION ON UPdATE CASCADE
  , cantidad        numeric(10)     NOT NULL
  , PRIMARY KEY (num_detalle, factura_id)
);
