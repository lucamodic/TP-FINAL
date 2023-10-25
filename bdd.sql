CREATE SCHEMA IF NOT EXISTS questionario;
USE questionario;
CREATE TABLE user(
                     username VARCHAR(255) PRIMARY KEY,
                     name VARCHAR(255),
                     spawn VARCHAR(255),
                     sex VARCHAR(255),
                     mail VARCHAR(255),
                     password VARCHAR(255),
                     image VARCHAR(255),
                     puntaje INT,
                     partidasRealizadas INT,
                     qr VARCHAR(255)
);

CREATE TABLE partida (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         username VARCHAR(255),
                         puntaje INT,
                         FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE pregunta(
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         categoria VARCHAR(255),
                         enunciado VARCHAR(255),
                         dificultad VARCHAR(255)
);

CREATE TABLE respuesta (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           texto VARCHAR(255),
                           id_pregunta INT,
                           es_correcta BOOLEAN
);

CREATE TABLE preguntas_usadas (
                                  username VARCHAR(255),
                                  pregunta_id INT,
                                  FOREIGN KEY (username) REFERENCES user(username),
                                  FOREIGN KEY (pregunta_id) REFERENCES pregunta(id)
);

CREATE TABLE preguntas_reportadas (
                                  pregunta_id INT,
                                  FOREIGN KEY (pregunta_id) REFERENCES pregunta(id)
);


INSERT INTO pregunta(categoria, enunciado, dificultad)values('Cultura', '¿Cuál es la moneda oficial de Japón?', 'facil');
INSERT INTO pregunta(categoria, enunciado, dificultad)values('Cultura', '¿Qué famosa pintura de Leonardo da Vinci representa a una mujer con una enigmática sonrisa?', 'facil');
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Dólar japonés', 1, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Yen japonés', 1, true);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Rublo japonés', 1, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Euro japonés', 1, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('La última cena', 2, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('La creación de Adán', 2, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('La Venus de Milo', 2, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('La Mona Lisa', 2, true);

INSERT INTO pregunta(categoria, enunciado, dificultad)values('Ciencia', '¿Cuál es el elemento químico más abundante en la Tierra?', 'facil');
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Hierro', 3, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Oxígeno', 3, true);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Carbono', 3, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Hidrógeno', 3, false);

INSERT INTO pregunta(categoria, enunciado, dificultad)values('Cultura', '¿En qué año comenzó la Primera Guerra Mundial?', 'facil');
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('1901', 4, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('1914', 4, true);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('1939', 4, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('1945', 4, false);

INSERT INTO pregunta(categoria, enunciado, dificultad)values('Cultura', '¿Cuál es la capital de Japón?', 'facil');
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Pekín', 5, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Seúl', 5, false);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Tokio', 5, true);
INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('Bankok', 5, false);

ALTER TABLE partida
    ADD esta_activa BOOLEAN;

ALTER TABLE preguntas_usadas
    ADD tiempo TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE partida
    ADD tiempo TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE user
    ADD COLUMN latitud DECIMAL(10, 6),
    ADD COLUMN longitud DECIMAL(10, 6);

ALTER TABLE user
    ADD COLUMN esEditor BOOLEAN,
    ADD COLUMN esAdmin BOOLEAN;

ALTER TABLE user
    ADD COLUMN token_verificacion VARCHAR(100) NOT NULL,
    ADD COLUMN esta_verificado BOOLEAN DEFAULT 0;

CREATE TABLE preguntas_reportadas (
     pregunta_id INT,
     FOREIGN KEY (pregunta_id) REFERENCES pregunta(id)
)