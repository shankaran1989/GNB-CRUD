<?php
class Property {
    private $conn;
    private $table = "properties";
    public function __construct($db){ $this->conn=$db; }

    // Create - property_photos expected as JSON string (array of filenames)
    public function create($data){
        $sql="INSERT INTO $this->table (pro_title, pro_add, bedroom_count, bath_room_count, resp_count, property_photos, short_desc, status, propname, ref_no) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stmt=$this->conn->prepare($sql);
        $stmt->bind_param("ssiiississ",$data['pro_title'],$data['pro_add'],$data['bedroom_count'],$data['bath_room_count'],$data['resp_count'],$data['property_photos'],$data['short_desc'],$data['status'],$data['propname'],$data['ref_no']);
        return $stmt->execute();
    }

    // Get paginated with optional search
    public function getAll($limit=10, $offset=0, $search='') {
        $search_sql = '';
        if(!empty($search)) {
            $s = $this->conn->real_escape_string($search);
            $search_sql = " WHERE pro_title LIKE '%$s%' OR propname LIKE '%$s%' OR ref_no LIKE '%$s%' ";
        }
        $sql = "SELECT * FROM $this->table $search_sql ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getCount($search='') {
        $search_sql = '';
        if(!empty($search)) {
            $s = $this->conn->real_escape_string($search);
            $search_sql = " WHERE pro_title LIKE '%$s%' OR propname LIKE '%$s%' OR ref_no LIKE '%$s%' ";
        }
        $sql = "SELECT COUNT(*) as cnt FROM $this->table $search_sql";
        $res = $this->conn->query($sql)->fetch_assoc();
        return (int)$res['cnt'];
    }

    public function getById($id){ 
        $id = (int)$id;
        return $this->conn->query("SELECT * FROM $this->table WHERE id=$id")->fetch_assoc(); 
    }

    // Update - property_photos is JSON string (existing+new)
    public function update($id,$data){
        $sql="UPDATE $this->table SET pro_title=?, pro_add=?, bedroom_count=?, bath_room_count=?, resp_count=?, property_photos=?, short_desc=?, status=?, propname=?, ref_no=? WHERE id=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->bind_param("ssiiississi",$data['pro_title'],$data['pro_add'],$data['bedroom_count'],$data['bath_room_count'],$data['resp_count'],$data['property_photos'],$data['short_desc'],$data['status'],$data['propname'],$data['ref_no'],$id);
        return $stmt->execute();
    }

    public function delete($id){ 
        $id = (int)$id;
        return $this->conn->query("DELETE FROM $this->table WHERE id=$id"); 
    }
}
?>