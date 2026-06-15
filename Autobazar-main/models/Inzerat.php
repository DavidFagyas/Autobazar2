<?php
/**
 * Inzerat Osztály
 * Felelős az autóhirdetések adatbázisból való lekéréséért és módosításáért.
 */
class Inzerat {
    private $db;

    // Az osztály létrehozásakor megkapja a működő adatbázis kapcsolatot
    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * Lekéri a legfrissebb hirdetéseket a slideshow-hoz (index.php használja)
     */
    public function getLatestSlides($limit = 3) {
        $limit = (int)$limit;
        $sql = "SELECT id, Nazov, obraz FROM inzerati ORDER BY id DESC LIMIT $limit";
        $result = $this->db->query($sql);

        $slides = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $slides[] = $row;
            }
        }
        return $slides;
    }

    /**
     * Lekéri az összes hirdetést a hozzá tartozó felhasználónévvel együtt (Admin használja)
     */
    public function getAllInzeratiWithUsers() {
        $sql = "SELECT inzerati.*, users.username FROM inzerati 
                LEFT JOIN users ON inzerati.pouzivatel_id = users.id 
                ORDER BY inzerati.id DESC";
        $result = $this->db->query($sql);
        
        $inzeraty = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $inzeraty[] = $row;
            }
        }
        return $inzeraty;
    }

    /**
     * Egy konkrét autó lekérése ID alapján a részletekhez és az admin szerkesztőhöz.
     * OOP módon, prepared statement-tel lekéri az autót ÉS a hirdető nevét is!
     */
    public function getInzeratById($id) {
        $id = (int)$id;
        $sql = "SELECT i.*, u.username 
                FROM inzerati i 
                LEFT JOIN users u ON i.pouzivatel_id = u.id 
                WHERE i.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Autó adatainak frissítése – EZT FOGJA MEGHÍVNI AZ admin_edit_car.php OLDALAD!
     * Figyelembe veszi az adatbázisod pontos oszlopneveit (Nazov, Popis, obraz).
     */
    public function update($id, $nazov, $cena, $popis, $obrazok) {
        $id = (int)$id;
        $cena = floatval($cena);

        $stmt = $this->db->prepare("UPDATE inzerati SET Nazov = ?, cena = ?, Popis = ?, obraz = ? WHERE id = ?");
        $stmt->bind_param("sdssi", $nazov, $cena, $popis, $obrazok, $id);
        
        return $stmt->execute();
    }

    /**
     * Lekér egy konkrét hirdetést, de csak akkor, ha az a megadott felhasználóé
     */
    public function getUserInzeratById($id, $user_id) {
        $id = (int)$id;
        $user_id = (int)$user_id;

        $stmt = $this->db->prepare("SELECT * FROM inzerati WHERE id = ? AND pouzivatel_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Frissíti a hirdetés összes részletes adatát (A felhasználó saját szerkesztője használja)
     */
    public function updateDetailedInzerat($id, $user_id, $nazov, $cena, $popis, $palivo, $prevodovka, $km, $pohon) {
        $id = (int)$id;
        $user_id = (int)$user_id;
        $km = (int)$km;

        $stmt = $this->db->prepare("UPDATE inzerati SET Nazov=?, cena=?, Popis=?, Palivo=?, Prevodovka=?, KM=?, Pohon=? WHERE id=? AND pouzivatel_id=?");
        $stmt->bind_param("sdsssisii", $nazov, $cena, $popis, $palivo, $prevodovka, $km, $pohon, $id, $user_id);
        
        return $stmt->execute();
    }

    /**
     * ÚJ METÓDUS: Lekéri a galéria képeit egy konkrét hirdetéshez
     */
    public function getGaleriaByInzeratId($id) {
        $id = (int)$id;
        $sql = "SELECT cesta_k_obrazku FROM galeria WHERE inzerat_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $kepek = [];
        while ($row = $result->fetch_assoc()) {
            $kepek[] = $row['cesta_k_obrazku'];
        }
        return $kepek;
    }
}