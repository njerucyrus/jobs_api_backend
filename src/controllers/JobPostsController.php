<?php
/**
 * Created by PhpStorm.
 * User: njerucyrus
 * Date: 2/1/18
 * Time: 2:49 PM
 */

namespace src\controllers;


use src\db\DB;

class JobPostsController implements CrudInterface
{
    private $_db;
    private $_conn;

    public function __construct()
    {
        $this->_db = DB::getInstance();
        $this->_conn = DB::getInstance()->connect();
    }

    public function create($data)
    {
        try{
            $stmt = $this->_conn
                ->prepare("INSERT INTO jobs(posted_by, category, description, location, lat, lng, price, status, posted_on, deadline)
                         VALUES (:posted_by, :category, :description, :location, :lat, :lng, :price, :status, :posted_on, :deadline)");
            $stmt->bindValue(":posted_by", $data['posted_by']);
            $stmt->bindValue(":category", $data['category']);
            $stmt->bindValue(":description", $data['description']);
            $stmt->bindValue(":location", $data['location']);
            $stmt->bindValue(":lat", $data['lat']);
            $stmt->bindValue(":lng", $data['lng']);
            $stmt->bindValue(":price", $data['price']);
            $stmt->bindValue(":status", $data['status']);
            $stmt->bindValue(":posted_on", $data['posted_on']);
            $stmt->bindValue(":deadline", $data['deadline']);
            if ($stmt->execute()){
                return [
                    "status_code"=>200,
                    "message" => "Job posted successfully",
                    "data" => self::getId($this->_conn->lastInsertId())["data"]
                ];
            }else{
                return [
                    "status_code"=>500,
                    "message" => "Error occurred {$stmt->errorInfo()[2]}",
                ];
            }

        }catch (\PDOException $e){
            return [
                "status_code"=>500,
                "message" => "Error occurred {$e->getMessage()}",
            ];
        }
    }

    public function update($data)
    {
        try{
            $stmt = $this->_conn
                ->prepare("UPDATE jobs SET posted_by=:posted_by, 
                             category=:category, description=:description,
                             location=:location, lat=:lat, lng=:lng, price=:price,
                             status=:status, posted_on=:posted_on, deadline=:deadline
                             WHERE id=:id
                             ");

            $stmt->bindValue(":id", $data['id']);
            $stmt->bindValue(":posted_by", $data['posted_by']);
            $stmt->bindValue(":category", $data['category']);
            $stmt->bindValue(":description", $data['description']);
            $stmt->bindValue(":location", $data['location']);
            $stmt->bindValue(":lat", $data['lat']);
            $stmt->bindValue(":lng", $data['lng']);
            $stmt->bindValue(":price", $data['price']);
            $stmt->bindValue(":status", $data['status']);
            $stmt->bindValue(":posted_on", $data['posted_on']);
            $stmt->bindValue(":deadline", $data['deadline']);
            if ($stmt->execute()){
                return [
                    "status_code"=>200,
                    "message" => "Job updated successfully",
                ];
            }else{
                return [
                    "status_code"=>500,
                    "message" => "Error occurred {$stmt->errorInfo()[2]}",
                ];
            }

        }catch (\PDOException $e){
            return [
                "status_code"=>500,
                "message" => "Error occurred {$e->getMessage()}",
            ];
        }
    }

    public static function delete($id)
    {
        try{
            $stmt = (new self)->_conn
                ->prepare("DELETE FROM jobs WHERE id=:id");
            $stmt->execute();
            $stmt->bindParam(":id", $id);
            if($stmt->execute()){
                return [
                    "status_code"=>200,
                    "message" => "Job deleted",
                ];
            }else{
                return [
                    "status_code"=>500,
                    "message" => "Error occurred {$stmt->errorInfo()[2]}",
                ];
            }
        }catch (\PDOException $e){
            return [
                "status_code"=>500,
                "message" => "Error occurred {$e->getMessage()}",
            ];
        }
    }

    public static function getId($id)
    {

        try{
           $stmt = (new self)->_conn
           ->prepare("SELECT * FROM jobs WHERE id=:id");
           $stmt->bindParam(":id", $id);
           if ($stmt->execute() && $stmt->rowCount()>0){
               return [
                   "status_code"=>200,
                   "data"=>$stmt->fetch(\PDO::FETCH_ASSOC)
               ];
           }else{
               return [
                   "status_code"=>500,
                   "message" => "Error {$stmt->errorInfo()[2]}",
               ];
           }
        }catch (\PDOException $e){
            return [
                "status_code"=>200,
                "message" => "Error {$e->getMessage()}",
            ];
        }

    }

    public static function all()
    {
        try{
            $stmt = (new self)->_conn
                ->prepare("SELECT * FROM jobs WHERE 1 ORDER BY posted_on DESC");

            if ($stmt->execute() && $stmt->rowCount() > 0){
                return [
                    "status_code"=>200,
                    "data"=>$stmt->fetchAll(\PDO::FETCH_ASSOC)
                ];
            }else{
                return [
                    "status_code"=>500,
                    "message" => "Error {$stmt->errorInfo()[2]}",
                ];
            }
        }catch (\PDOException $e){
            return [
                "status_code"=>500,
                "message" => "Error {$e->getMessage()}",
            ];
        }
    }

}