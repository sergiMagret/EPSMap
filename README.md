# EPSMap - Català
EPSMap – Aplicació web de navegació interna pels edificis de la EPS

## Instal·lar l'aplicació
1. Crear la base de dades, en el meu cas anomenada `eps_map`.  
Per a fer això pots utilitzar un dels dos fitxers que trobaràs a la carpeta [`mysql`](./mysql/).  
Aquí hi ha un fitxer per a importar només l’estructura: [`eps_map_structure.sql`](./mysql/eps_map_structure.sql).  
O per a importar l’estructura i introduir les dades per a l’edifici Politècnica 2, és a dir, amb els resultats d’aquest projecte: [`eps_map_populate_p2.sql`](./mysql/eps_map_populate_p2.sql).
2. Un cop hagis creat la base de dades, has de configurar l’aplicació.  
Per això fes una còpia del fitxer [`conf/settings.fill.php`](./conf/settings.fill.php) i canvia el nom a `conf/settings.php`.  
Aquest últim fitxer és el que es s’ha de modificar per a canviar la configuració de l’aplicació. No està trackejat per Git, per tant, res del que modifiquis aquí es guardarà al repositori.  
En aquest mateix fitxer trobaràs les instruccions i recomanacions a l’hora de configurar l’aplicació.

-----------------

# EPSMap - English
EPSMap – Internal navigation system in the EPS buildings web application

## Installing
1. Create the database, in my case named `eps_map`.  
To do so, you can use one of the files you can find in the directory [`mysql`](./mysql/).  
Here you will find two files, one to load only the structure for the application: [`eps_map_structure.sql`](./mysql/eps_map_structure.sql).  
And another to load the structure and data from the P-II building, in other words, the full results for this project: [`eps_map_populate_p2.sql`](./mysql/eps_map_populate_p2.sql).
2. Once you have created the database, you need to configure the application.  
To do so, copy and rename the file [`conf/settings.fill.php`](./conf/settings.fill.php) to `conf/settings.php`.  
This new file is the one you need to modify in order to update the application configuration. It is not tracked by Git, so nothing you put in this file will be saved in the repo.  
In this same file you will find further instructions and recomendations to configure the application.

-----------------

# EPSMap - Castellano
EPSMap – Aplicación web Sistema de navegación interno por los edificios de la EPS

## Instalar la aplicación
1. Crear la base de datos, en mi caso `eps_map`.  
Para esto puedes utilitzar uno de los dos ficheros que encontraras en la carpeta [`mysql`](./mysql/).  
Aquí hay un fichero para importar solo la estructura: [`eps_map_structure.sql`](./mysql/eps_map_structure.sql).  
O para importar la estructura juntamente con los datos del edificio Politécnica 2, es decir, con los resultados de este proyecto: [`eps_map_populate_p2.sql`](./mysql/eps_map_populate_p2.sql).
2. Creada la base de datos, tienes que configurar la aplicación.  
Para esto haz una copia del fichero [`conf/settings.fill.php`](./conf/settings.fill.php) y renombralo a `conf/settings.php`.  
Este último fichero es el que tienes que modificar para actualitzar la configuración de la aplicación. No està trackeado por Git, por tanto, nada de lo que modifiques aquí se guardará en el repositorio.  
En este mismo fichero encontrarás instrucciones y recomendaciones para configurar la aplicación.
