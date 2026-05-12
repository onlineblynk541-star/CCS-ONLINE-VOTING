<?php
// Set headers for JSON response and cross-origin access
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Include the separated database connection
require_once 'db_connection.php';

// --- Input Handling ---
$method = $_SERVER['REQUEST_METHOD'];
$data = [];

if ($method === 'POST') {
    // Read JSON body for POST/PUT/DELETE logic
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if ($data === null) {
        // Fallback for form data
        $data = $_POST;
    }
} elseif ($method === 'GET') {
    $data = $_GET;
}

$action = $data['action'] ?? null;
$collection = $data['collection'] ?? null;

// Allow actions that don't require collections
if (!$action || (!$collection && !in_array($action, ['reset_votes', 'get_settings', 'save_settings', 'register_voter', 'approve_voter']))) {
    echo json_encode(['success' => false, 'message' => 'Missing action or collection parameter.']);
    exit();
}

// --- Core API Logic ---
switch ($action) {
    case 'get':
        handleGet($pdo, $collection);
        break;
    case 'add':
        handleAdd($pdo, $collection, $data);
        break;
    case 'update':
        handleUpdate($pdo, $collection, $data);
        break;
    case 'delete':
        handleDelete($pdo, $collection, $data);
        break;
    case 'results':
        handleGetResults($pdo);
        break;
    case 'reset_votes':
        handleResetVotes($pdo);
        break;
    case 'get_settings':
        handleGetSettings($pdo); 
        break;
    case 'save_settings':
        handleSaveSettings($pdo, $data); 
        break;
    case 'register_voter':
        handleRegisterVoter($pdo, $data);
        break;
    case 'approve_voter':
        handleApproveVoter($pdo, $data);
        break;
    default:
        echo json_encode(['success' => false, 'message' => "Unknown action: $action"]);
        break;
}

// --- Handler Functions ---

function handleGet($pdo, $collection) {
    switch ($collection) {
        case 'voters':
            $stmt = $pdo->query("SELECT * FROM voters ORDER BY name ASC");
            $data = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'candidates':
            $stmt = $pdo->query("SELECT * FROM candidates ORDER BY name ASC");
            $data = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'positions':
            $stmt = $pdo->query("SELECT * FROM positions ORDER BY name ASC");
            $data = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'votes':
            $stmt = $pdo->query("SELECT id, voter_id, position_id, candidate_id, created_at FROM votes ORDER BY created_at DESC");
            $data = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => "Collection '$collection' not found."]);
            break;
    }
}

function handleAdd($pdo, $collection, $data) {
    try {
        if ($collection === 'voters') {
            // Admin manual adds are automatically approved (is_approved = 1)
            $sql = "INSERT INTO voters (student_id, name, course, is_approved) VALUES (:student_id, :name, :course, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':student_id' => $data['studentId'],
                ':name' => $data['name'],
                ':course' => $data['course']
            ]);
        } elseif ($collection === 'candidates') {
            $sql = "INSERT INTO candidates (name, party_list, position_id, image) VALUES (:name, :party_list, :position_id, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':party_list' => $data['partyList'],
                ':position_id' => $data['positionId'],
                ':image' => $data['image'] ?? null
            ]);
        } elseif ($collection === 'positions') {
            $sql = "INSERT INTO positions (name, max_votes) VALUES (:name, :max_votes)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':max_votes' => $data['maxVotes']
            ]);
        } else {
            throw new Exception("Invalid collection for add operation.");
        }
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "SQL Error: " . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleUpdate($pdo, $collection, $data) {
    try {
        $id = $data['id'];
        if ($collection === 'voters') {
            $sql = "UPDATE voters SET student_id = :student_id, name = :name, course = :course WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':student_id' => $data['studentId'],
                ':name' => $data['name'],
                ':course' => $data['course'],
                ':id' => $id
            ]);
        } elseif ($collection === 'candidates') {
            $sql = "UPDATE candidates SET name = :name, party_list = :party_list, position_id = :position_id, image = :image WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':party_list' => $data['partyList'],
                ':position_id' => $data['positionId'],
                ':image' => $data['image'] ?? null,
                ':id' => $id
            ]);
        } elseif ($collection === 'positions') {
            $sql = "UPDATE positions SET name = :name, max_votes = :max_votes WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':max_votes' => $data['maxVotes'],
                ':id' => $id
            ]);
        } else {
            throw new Exception("Invalid collection for update operation.");
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "SQL Error: " . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleDelete($pdo, $collection, $data) {
    try {
        $id = $data['id'];
        $validCollections = ['voters', 'candidates', 'positions'];
        if (!in_array($collection, $validCollections)) {
            throw new Exception("Invalid collection for delete operation.");
        }
        
        $sql = "DELETE FROM $collection WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "SQL Error: " . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleGetResults($pdo) {
    $sql = "
        SELECT 
            c.id AS candidateId,
            c.name AS candidateName,
            c.party_list AS partyList,
            c.image AS image,
            p.id AS positionId,
            p.name AS positionName,
            p.max_votes AS maxVotes,
            COUNT(v.id) AS voteCount
        FROM candidates c
        JOIN positions p ON c.position_id = p.id
        LEFT JOIN votes v ON c.id = v.candidate_id AND p.id = v.position_id
        GROUP BY c.id, c.name, c.party_list, c.image, p.id, p.name, p.max_votes
        ORDER BY p.id ASC, voteCount DESC
    ";
    
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $results]);
}

function handleResetVotes($pdo) {
    try {
        $pdo->beginTransaction(); 

        $pdo->exec("DELETE FROM votes");
        $pdo->exec("UPDATE voters SET has_voted = 0");

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'All votes reset successfully.']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => "Error resetting votes: " . $e->getMessage()]);
    }
}

function handleGetSettings($pdo) {
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        echo json_encode(['success' => true, 'data' => $data ?: []]);
    } catch (Exception $e) {
         echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleSaveSettings($pdo, $data) {
    try {
        $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES (:key, :value)");
        
        if (isset($data['start_time'])) {
            $stmt->execute([':key' => 'start_time', ':value' => $data['start_time']]);
        }
        if (isset($data['end_time'])) {
            $stmt->execute([':key' => 'end_time', ':value' => $data['end_time']]);
        }
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleRegisterVoter($pdo, $data) {
    try {
        // Students registering themselves start with is_approved = 0
        $sql = "INSERT INTO voters (student_id, name, course, is_approved) VALUES (:student_id, :name, :course, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':student_id' => $data['studentId'],
            ':name' => $data['name'],
            ':course' => $data['course']
        ]);
        echo json_encode(['success' => true, 'message' => 'Registration submitted. Awaiting approval.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "SQL Error: " . $e->getMessage()]);
    }
}

function handleApproveVoter($pdo, $data) {
    try {
        $sql = "UPDATE voters SET is_approved = 1 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $data['id']]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "SQL Error: " . $e->getMessage()]);
    }
}
?>
