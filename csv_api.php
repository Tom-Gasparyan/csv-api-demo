<?php

class API_csv {

    //Подключение к абстрактному файлу базы данных.
    //Создание в базе данных таблицы "Машины" с указанными полями. Предполагается использование sql-команды CREATE. Предполагается передача CREATE команды в качестве аргумента sql, следовательно, проверки на соответствие аргумента ожидаемому целенаправленно опущены во избежание усложнения анализа принципов работы метода, который представлен в демонстрационных целях.

    public function connect_to_database($dns, $path, $sql) {
        try {
            $db = new PDO($dns. $path);
            $db->setAttribute(PDO::ATTR_REMOTE, PDO::ERRMODE_EXCEPTION);
            $query = $db->exec($sql);
            return $db;
        } catch(Exception $e) {
            print "Unable to connect to database: " . $e->getMessage();
            exit();
        }
    }

    //Открытие некоторого соответствующего файла csv из абстрактной директории и подготовка запроса в таблицу cars базы данных $db для вставки данных. Предполагается, что файл позволяет и предоставил полномчия для чтения или записи.

    public function insert_data($source_file, $db, $table, $query) {
        if ( file_exists($source_file) ) {
            $fh = fopen($source_file, 'r+');
            $stmt = $db->prepare('INSERT INTO'. ' '. $table. ' '. $query);

            while ( ( !feof($fh) ) && ( $info = fgetcsv($fh) ) ) {
                $stmt->execute($info);
            }    
            fclose($fh);
        }
    }

    // При возникновении необходимости правок и изменений в исходном файле csv следует воспользоваться функцией fputcsv()

    public function update_csv($dns, $path, $table_name, $source_file, $query) {
        try {
            $db = new PDO($dns. $path);
        } catch(Exception $e) {
            print 'Unable to connect to database:'. ' '. $e->getMessage();
            exit();
        }

        if ( file_exists($source_file) ) {
            $fh = fopen($source_file, 'w+');

        // Отправка запроса на выборку указанных полей и дальнейшая запись данных в виде строки.

            $table = $db->query($query. ' '. $table_name);

            while ( $table_row = $table->fetch(PDO::FETCH_NUM) ) {
                fputcsv($fh, $table_row);
            }

            fclose($fh);
        }
    }

};

//Абстрактная вставка данных из представленнго в экспериментальных целях несуществующего файла directory/cars.csv в базу данных:

// $api_csv = new API_csv();
// $api_csv->insert_data('directory/cars.csv',  $api_csv->connect_to_database('mysql:', '/some_path/data_base.db', 'CREATE TABLE cars (
//     factory_year INT,
//     car_name VARCHAR(255),
//     suspension_system VARCHAR(255),
//     price DECIMAL(5, 2)
//     )'), 'cars', '(factory_year, car_name, suspension_system, price) VALUES (?, ?, ?, ?)');


//При изменении информации в базе данных и необходимости их внесения в исходный csv-файл, такого рода обновление выполняет метод $api_csv->update_csv:  

// $api_csv->update_csv('mysql:', '/some_path/data_base.db', 'cars', 'directory/cars.csv', 'SELECT factory_year, car_name, suspension_system, price FROM');

?>
