# CRUD and ETL Test

The test should be done in Laravel.

Feel free to install any libraries or plugins you want to facilitate the tasks.
Commit all code to the repository when you're done. 

## Test 1
Implement a console command to import a file with a list of hotels and store them in a local database. 
The cities should be added in its own database table and the name should be unique.

You can use any database engine of your choice.

The format of the data can be either CSV or Json.
In this repository, there are two files with samples of the data to import.

To help with the parsing, you're free to install via composer any existing import library if you want.
The script should be run via a console command by accepting the file to import as a parameter and automatically detect the file format.
Note the columns in the CSV file are separated by semicolon ';' as some of the content might have commas in it, like the Hotel Name or the Description.

## Test 2
Implement a Restful API to manage CRUD operations for the hotels stored in the database from the Test 1.

The API should have an endpoint for each of the following actions:
- Return the list of all hotels. It should return only the id, name, image, stars and city fields of each hotel.
- Return all the details of a single hotel given its ID.
- Add a new hotel in the database.
- Update the details of a single hotel.
- Delete a single hotel from the database. Please use soft deletes.

You can add any validation you see fit on the endpoints.
Extra points for unit tests.
No authentication required.
