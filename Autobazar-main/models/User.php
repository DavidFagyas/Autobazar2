<?php
/**
 * User Osztály
 * Felelős a felhasználók adatbázisból való lekéréséért és kezeléséért.
 */
class User {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * Lekéri az összes regisztrált felhasználót, kivéve a fő admint
     */
    public function getAllUsersExceptAdmin() {
        $sql = "SELECT id, username, is_admin FROM users WHERE username != 'admin'";
        $result = $this->db->query($sql);
        
        $users = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }
}