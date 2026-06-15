<?php
/**
 * Sprava Osztály
 * Felelős a kontakt űrlapon keresztüli üzenetek kezeléséért.
 */
class Sprava {
    private $db;

    // Megkapja az adatbázis kapcsolatot
    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * Új üzenet elmentése az adatbázisba (kontakt.php használja)
     */
    public function ulozitSpravu($meno, $email, $textSpravy) {
        $sql = "INSERT INTO spravy (meno, email, sprava) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("sss", $meno, $email, $textSpravy);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Lekéri az összes beérkezett üzenetet az admin számára (admin.php használja)
     */
    public function getAllSpravy() {
        $sql = "SELECT * FROM spravy ORDER BY datum_odoslania DESC";
        $result = $this->db->query($sql);
        
        $spravy = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $spravy[] = $row;
            }
        }
        return $spravy;
    }
}