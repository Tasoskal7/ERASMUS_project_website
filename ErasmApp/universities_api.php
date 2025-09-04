<?php
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "erasmapp");
if($conn->connect_error) 
{
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

function getJsonBody()
{
    return json_decode(file_get_contents('php://input'), true);
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if($method === 'GET') 
{
    if($id) 
    {
        $stmt = $conn->prepare("SELECT id, uni_name, country, city, active FROM universities WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()) 
        {
            echo json_encode($row);
        } 
        else 
        {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
        }
        $stmt->close();
    } 
    else 
    {
        $result = $conn->query("SELECT id, uni_name, country, city, active FROM universities");
        $data = [];
        while($row = $result->fetch_assoc()) 
        {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    exit();
}

if($method === 'POST') 
{
    $data = getJsonBody();
    if(!isset($data['uni_name']) || !isset($data['country']) || !isset($data['city']) || !isset($data['active'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "uni_name, country, city, active required"]);
        exit();
    }
    $stmt = $conn->prepare("INSERT INTO universities (uni_name, country, city, active) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $data['uni_name'], $data['country'], $data['city'], $data['active']);
    if($stmt->execute()) 
    {
        echo json_encode([
            "id" => $stmt->insert_id,
            "uni_name" => $data['uni_name'],
            "country" => $data['country'],
            "city" => $data['city'],
            "active" => $data['active']
        ]);
    }
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Insert failed"]);
    }
    $stmt->close();
    exit();
}

if($method === 'PUT' && $id)
{
    $data = getJsonBody();
    if(!isset($data['uni_name']) || !isset($data['country']) || !isset($data['city']) || !isset($data['active'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "uni_name, country, city, active required"]);
        exit();
    }
    $stmt = $conn->prepare("UPDATE universities SET uni_name=?, country=?, city=?, active=? WHERE id=?");
    $stmt->bind_param("sssii", $data['uni_name'], $data['country'], $data['city'], $data['active'], $id);
    if($stmt->execute())
    {
        echo json_encode([
            "id" => $id,
            "uni_name" => $data['uni_name'],
            "country" => $data['country'],
            "city" => $data['city'],
            "active" => $data['active']
        ]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Update failed"]);
    }
    $stmt->close();
    exit();
}

if($method === 'DELETE' && $id)
{
    $stmt = $conn->prepare("DELETE FROM universities WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) 
    {
        echo json_encode(["deleted" => $id]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Delete failed"]);
    }
    $stmt->close();
    exit();
}

http_response_code(405);
echo json_encode(["error" => "Method Not Allowed"]);
?>
