# Instrucciones de instalación y despliegue

## En local

#### Requisitos previos
* PHP 7.1
* Postgresql 10.3
* Servidor Web
* Composer
* Git (opcional)

*Para instalar Educantes en local*

1. Crear un sitio virtual
2. Descargar repositorio git o clonar
3. Instalar composer del repositorio
4. El documentroot debe ser la carpeta web del respositorio git
5. Configurar variables de entorno en Apache con  SetEnv VARIABLE_NAME variable_value



## En la nube

*Para instalar Educantes en la nube heroku*
1. Crear nueva app
2. Conectamos con github
3. Añadir el addon heroku-postgresql
4. Enlazar tu repositorio git con heroku (en nuestro repositorio git)
	heroku apps
	heroku git:remote --app nombre aplicacion
5. Configurar variables de entorno
6. Cargar base de datos (heroku psql < db/educantes.sql)
