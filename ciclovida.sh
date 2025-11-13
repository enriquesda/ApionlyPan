cd htdocs/cicloVidaApp								# Movemos al directorio
if [ -s www.zip ] 								# Preguntamos si existe el fichero www.zip
then										#
	sudo mv www.zip /home/bitnami/backup/www.zip # Lo copiamos a la carpeta de backup
	sudo rm -R *.* 								# Borramos el contenido
	sudo rm -R assets 							#
	sudo rm -R svg 								#
	sudo mv /home/bitnami/backup/www.zip www.zip # Lo restauramos desde la carpeta de backup
	unzip www.zip 								# Extraemos el zip con la aplicación actualizada
	rm www.zip								# Eliminamos el fichero zip
else 										#
	sudo rm -R *.* 								# En caso de que no, simplemente borramos todos los ficheros
	sudo rm -R assets 							#
	sudo rm -R svg 								#
fi 										#

#Copiamos el .htaccess a la carpeta de Administración
cp /home/bitnami/backup/.htaccess /opt/bitnami/apache2/htdocs/cicloVidaApp/.htaccess
