<?php


    class Dao{

        //connect to heroku database

        private $host = "mysql:dbname=heroku_9c75f41139d02b3;host=us-cluster-east-01.k8s.cleardb.net";
        private $user = "b966495833aeb9";
        private $pass = "50d8dab7";

        

        

        public function getConection(){
            
            try{
               $conn = new PDO($this->host, $this->user, $this->pass);
               $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
               return $conn;
            }
            catch (PDOException $e) {
               echo 'Connection failed: ' . $e->getMessage();
            }
         }

         public function userExist($email, $password){
            $conn = $this->getConection();
            try {
                $query = "SELECT password FROM userLogin WHERE email = :email";
                $q = $conn->prepare($query);
                $q->bindParam(":email", $email);
                $q->execute();
                
                $result = $q->fetch(PDO::FETCH_ASSOC);
                
                // If we found a user with that email, verify the password
                if ($result && password_verify($password, $result['password'])) {
                    return true;
                } else {
                    return false;
                }
    
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
                return false;
            }
        }


         public function createUser($email, $password){
            $conn = $this->getConection();
            try {

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO userLogin (email, password) VALUES (:email, :password)";
                $q = $conn->prepare($query);
                $q->bindParam(":email", $email);
                $q->bindParam(":password", $hashedPassword);
                $q->execute();
        
                return true;
            }
            catch (PDOException $e) {
                // If it's a known PDO error (like unique constraint violation), provide a user-friendly message
                if ($e->getCode() == 23000) {  // 23000 is a common code for unique constraint violations
                    return "Email already exists!";
                }
                
                // Log the technical error message for debugging
                error_log('Database error: ' . $e->getMessage());
        
                // Provide a generic message to the user
                return "An error occurred while processing your request. Please try again later.";
            } 
            catch (Exception $e) {
                // Log the technical error message for debugging
                error_log('General error: ' . $e->getMessage());
        
                // Provide a generic message to the user
                return "An error occurred while processing your request. Please try again later.";
            }
        }
        
        
        
    }