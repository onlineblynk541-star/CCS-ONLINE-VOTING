<?php
session_start();

// Set the correct timezone for accurate schedule checking
date_default_timezone_set('Asia/Manila');

// Include the separate database connection file
require_once 'db_connection.php'; 

// --- Fetch Election Settings for Time Window ---
$electionSettings = ['start_time' => null, 'end_time' => null];
try {
    $stmtSettings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('start_time', 'end_time')");
    while ($rowSettings = $stmtSettings->fetch()) {
        $electionSettings[$rowSettings['setting_key']] = $rowSettings['setting_value'];
    }
} catch (Exception $e) {
    // Handle quietly if table doesn't exist yet
}

$now = time();
$start_ts = !empty($electionSettings['start_time']) ? strtotime($electionSettings['start_time']) : 0;
$end_ts = !empty($electionSettings['end_time']) ? strtotime($electionSettings['end_time']) : 0;

$isVotingOpen = false;
$votingMessage = "";

if (!$start_ts || !$end_ts) {
    $votingMessage = "The election schedule has not been set by the administrator yet.";
} elseif ($now < $start_ts) {
    $votingMessage = "Voting has not started yet. The election will begin at " . date('F j, Y, g:i A', $start_ts) . ".";
} elseif ($now > $end_ts) {
    $votingMessage = "Voting is officially closed. The election ended on " . date('F j, Y, g:i A', $end_ts) . ".";
} else {
    $isVotingOpen = true;
}

// --- Backend Logic ---

$error = '';
$success = '';

// 1. Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: voter_dashboard.php");
    exit();
}

// 2. Handle Login / Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $studentId = trim($_POST['student_id']);
    $password = trim($_POST['password']);

    // Check if voter exists
    $stmt = $pdo->prepare("SELECT * FROM voters WHERE student_id = :sid");
    $stmt->execute([':sid' => $studentId]);
    $voter = $stmt->fetch();

    if ($voter) {
        if ($voter['password'] === NULL) {
            // First time login: Set Password
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE voters SET password = :pass WHERE id = :id");
                $update->execute([':pass' => $hash, ':id' => $voter['id']]);
                
                // Login User
                $_SESSION['voter_id'] = $voter['id'];
                $_SESSION['voter_name'] = $voter['name'];
                header("Location: voter_dashboard.php");
                exit();
            } else {
                $error = "Please create a password to secure your account.";
            }
        } else {
            // Existing user: Verify Password
            if (password_verify($password, $voter['password'])) {
                $_SESSION['voter_id'] = $voter['id'];
                $_SESSION['voter_name'] = $voter['name'];
                header("Location: voter_dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        }
    } else {
        $error = "Student ID not found in the official voter list.";
    }
}

// 3. Handle Voting Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_vote') {
    if (!isset($_SESSION['voter_id'])) { die("Unauthorized"); }
    
    // BACKEND CHECK: Prevent voting if outside the allowed time window
    if (!$isVotingOpen) {
        die("Voting is currently closed. You cannot submit a ballot.");
    }

    $voterId = $_SESSION['voter_id'];
    $votes = $_POST['votes'] ?? []; 

    // Check if already voted
    $stmt = $pdo->prepare("SELECT has_voted FROM voters WHERE id = ?");
    $stmt->execute([$voterId]);
    if ($stmt->fetchColumn() == 1) {
        die("You have already voted.");
    }

    // --- JRMSU ANTI-OVERVOTE BACKEND CHECK ---
    $stmtLimit = $pdo->query("SELECT id, max_votes FROM positions");
    $posLimits = [];
    while($rowLimit = $stmtLimit->fetch()) {
        $posLimits[$rowLimit['id']] = $rowLimit['max_votes'];
    }

    foreach ($votes as $positionId => $candidateData) {
        $maxAllowed = 1; 
        if (is_array($candidateData) && count($candidateData) > $maxAllowed) {
            die("Overvoting detected! You exceeded the maximum allowed votes. The ballot is void.");
        }
    }
    // ------------------------------------------

    $pdo->beginTransaction();
    try {
        foreach ($votes as $positionId => $candidateData) {
            if (is_array($candidateData)) {
                foreach ($candidateData as $cId) {
                    $stmt = $pdo->prepare("INSERT INTO votes (voter_id, position_id, candidate_id) VALUES (?, ?, ?)");
                    $stmt->execute([$voterId, $positionId, $cId]);
                }
            } else {
                $stmt = $pdo->prepare("INSERT INTO votes (voter_id, position_id, candidate_id) VALUES (?, ?, ?)");
                $stmt->execute([$voterId, $positionId, $candidateData]);
            }
        }

        // Mark voter as voted
        $update = $pdo->prepare("UPDATE voters SET has_voted = 1 WHERE id = ?");
        $update->execute([$voterId]);

        $pdo->commit();
        $success = "Vote cast successfully!";
        header("Location: voter_dashboard.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to submit vote: " . $e->getMessage();
    }
}

// --- Fetch Data for Dashboard ---
$voterData = null;
$positions = [];
$candidates = [];
$voteCounts = [];

if (isset($_SESSION['voter_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM voters WHERE id = ?");
    $stmt->execute([$_SESSION['voter_id']]);
    $voterData = $stmt->fetch();

    if ($voterData) {
        $stmt = $pdo->query("SELECT * FROM positions ORDER BY id ASC");
        $positions = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT * FROM candidates ORDER BY name ASC");
        $allCandidates = $stmt->fetchAll();
        
        foreach ($allCandidates as $c) {
            $candidates[$c['position_id']][] = $c;
        }

        if ($voterData['has_voted'] == 1) {
            $stmt = $pdo->query("SELECT candidate_id, COUNT(*) as count FROM votes GROUP BY candidate_id");
            while ($row = $stmt->fetch()) {
                $voteCounts[$row['candidate_id']] = $row['count'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Voter Dashboard - JRMSU E-Voting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                colors: {
                    'primary': '#001f3f', 
                    'secondary': '#DAA520', 
                    'bg-slate': '#f1f5f9',
                    'accent': '#FFD700',
                },
            },
        },
    };
    </script>
    <style>
    /* CRITICAL FIX: Lock the html and body so it NEVER scrolls completely */
    html, body {
        height: 100%;
        width: 100%;
        overflow: hidden; /* Pinipigilan ang buong page na mag scroll pataas */
        margin: 0;
        padding: 0;
        background-color: #f1f5f9;
        font-family: 'Inter', sans-serif;
    }

    .candidate-option {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
    }
    
    .candidate-option:hover {
        transform: translateY(-3px);
        border-color: #DAA520;
        background-color: #fffdf0;
    }

    input[type="radio"]:checked + div, 
    input[type="checkbox"]:checked + div {
        border-color: #DAA520;
        background-color: #fffdf0;
        box-shadow: 0 0 15px rgba(218, 165, 32, 0.2);
    }

    input[type="radio"]:checked + div .check-icon,
    input[type="checkbox"]:checked + div .check-icon {
        opacity: 1;
        transform: scale(1);
    }

    /* Custom Scrollbar for smoother look */
    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    </style>
</head>
<body class="text-gray-800 antialiased">

    <?php if (!isset($_SESSION['voter_id'])): ?>
    <div class="h-full w-full flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-xl border-t-4 border-primary">
            <div class="text-center mb-8">
                <img src="OIP.jpg" alt="JRMSU Logo" class="h-24 w-24 rounded-full object-cover mx-auto drop-shadow-md border-2 border-secondary">
            </div>
           <div class="flex items-center mb-6">
                <div class="bg-secondary p-2 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h1 class="text-primary font-black text-xl tracking-tight leading-none">JRMSU VOTER PORTAL</h1>
                    <p class="text-[10px] text-secondary font-bold uppercase tracking-[0.2em]">Siocon Campus</p>
                </div>
            </div>

            <?php if($error): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" id="loginForm" class="space-y-5">
                <input type="hidden" name="action" value="login">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                    <input type="text" name="student_id" id="student_id" required 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all"
                        placeholder="Enter your ID (e.g., 2023-0001)">
                </div>

                <div id="password-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1" id="pass-label">Password</label>
                    <input type="password" name="password" id="password" required disabled
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all"
                        placeholder="Enter password">
                    <p class="text-xs text-gray-500 mt-1" id="pass-help"></p>
                </div>

                <button type="button" id="check-id-btn" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-secondary transition-colors shadow-lg">
                    Next
                </button>

                <button type="submit" id="submit-btn" class="hidden w-full bg-secondary text-white font-bold py-3 rounded-lg hover:bg-primary transition-colors shadow-lg">
                    Login
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('check-id-btn').addEventListener('click', async () => {
            const idInput = document.getElementById('student_id');
            const passContainer = document.getElementById('password-container');
            const passInput = document.getElementById('password');
            const checkBtn = document.getElementById('check-id-btn');
            const submitBtn = document.getElementById('submit-btn');
            const passHelp = document.getElementById('pass-help');

            if(!idInput.value) {
                alert("Please enter your Student ID");
                return;
            }
            
            idInput.readOnly = true;
            idInput.classList.add('bg-gray-50', 'text-gray-500');
            
            passContainer.classList.remove('hidden');
            passInput.disabled = false;
            passInput.focus();
            
            checkBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            
            passHelp.innerText = "If this is your first time, create a new password. If you have logged in before, enter your existing password.";
        });
    </script>

    <?php else: ?>
    
    <div class="fixed inset-0 w-full h-full flex bg-gray-50">
        
        <aside class="w-64 bg-primary text-white flex-shrink-0 hidden md:flex flex-col z-30 shadow-2xl h-full">
            <div class="p-6 flex items-center border-b border-white/10 shrink-0">
                <svg class="h-8 w-8 text-accent mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <span class="text-lg font-bold">Voter Portal</span>
            </div>
            <div class="p-6 flex-1 overflow-hidden">
                <div class="mb-6">
                    <p class="text-xs text-gray-300 uppercase tracking-wider mb-1">Student Name</p>
                    <p class="font-semibold text-lg break-words"><?php echo htmlspecialchars($_SESSION['voter_name']); ?></p>
                </div>
                <div class="mb-6">
                    <p class="text-xs text-gray-300 uppercase tracking-wider mb-1">Status</p>
                    <?php if($voterData['has_voted']): ?>
                        <span class="inline-block bg-green-500 text-white text-xs px-2 py-1 rounded-full shadow">Voted</span>
                    <?php elseif(!$isVotingOpen): ?>
                        <span class="inline-block bg-red-500 text-white text-xs px-2 py-1 rounded-full font-bold shadow">Closed</span>
                    <?php else: ?>
                        <span class="inline-block bg-accent text-primary text-xs px-2 py-1 rounded-full font-bold shadow">Ready to Vote</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-auto p-6 border-t border-white/10 shrink-0 bg-black/20">
                <a href="?logout=true" class="flex items-center text-gray-300 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-full min-w-0 relative">
            
            <header class="bg-primary text-white p-4 md:hidden flex justify-between items-center shadow-md z-20 shrink-0">
                <div class="font-bold flex items-center">
                    <svg class="h-6 w-6 text-accent mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    JRMSU Voting
                </div>
                <a href="?logout=true" class="text-sm bg-white/10 px-3 py-1 rounded hover:bg-white/20 transition-colors">Logout</a>
            </header>

            <main class="flex-1 overflow-y-auto w-full relative z-10 scroll-smooth">
                <div class="p-6 md:p-10 max-w-5xl mx-auto pb-40"> <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800">Election Day <?php echo date('Y'); ?></h1>
                        <?php if(!$voterData['has_voted']): ?>
                            <?php if($isVotingOpen): ?>
                                <p class="text-gray-500 mt-1">Please select your preferred candidates below. This action cannot be undone.</p>
                            <?php else: ?>
                                <p class="text-gray-500 mt-1">Voting is currently not available.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-gray-500 mt-1">Thank you for participating. Below are the live results.</p>
                        <?php endif; ?>
                    </div>

                    <?php if($voterData['has_voted']): ?>
                        <div class="bg-white rounded-2xl shadow-xl p-8 text-center border-t-4 border-green-500 mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Vote Submitted Successfully</h2>
                            <p class="text-gray-600">Your ballot has been securely recorded.</p>
                        </div>

                        <div class="mt-8">
                            <h2 class="text-xl font-bold text-primary mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Live Election Standings
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php foreach($positions as $position): ?>
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 flex flex-col h-full">
                                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-100 font-bold text-primary flex justify-between">
                                            <span><?php echo htmlspecialchars($position['name']); ?></span>
                                            <span class="text-xs font-normal text-gray-500 bg-white px-2 py-1 rounded border">Top 1</span>
                                        </div>
                                        <div class="p-5 space-y-5 flex-1">
                                            <?php 
                                            $posCandidates = $candidates[$position['id']] ?? [];
                                            
                                            $totalPosVotes = 0;
                                            foreach($posCandidates as $c) {
                                                $totalPosVotes += ($voteCounts[$c['id']] ?? 0);
                                            }

                                            usort($posCandidates, function($a, $b) use ($voteCounts) {
                                                return ($voteCounts[$b['id']] ?? 0) - ($voteCounts[$a['id']] ?? 0);
                                            });
                                            
                                            if(empty($posCandidates)):
                                            ?>
                                                <p class="text-gray-400 text-sm italic">No candidates.</p>
                                            <?php else: ?>
                                                <?php foreach($posCandidates as $index => $candidate): 
                                                    $votes = $voteCounts[$candidate['id']] ?? 0;
                                                    $percent = ($totalPosVotes > 0) ? round(($votes / $totalPosVotes) * 100) : 0;
                                                    $isLeading = ($index < 1) && ($votes > 0);
                                                ?>
                                                    <div class="relative">
                                                        <div class="flex justify-between items-end mb-1">
                                                            <div class="flex items-center">
                                                                <?php if (!empty($candidate['image'])): ?>
                                                                    <img src="<?php echo htmlspecialchars($candidate['image']); ?>" alt="Profile" class="h-16 w-16 rounded-full object-cover mr-4 border shadow-sm shrink-0">
                                                                <?php else: ?>
                                                                    <div class="h-16 w-16 rounded-full bg-gray-200 border flex items-center justify-center text-gray-500 font-bold mr-4 shadow-sm shrink-0 text-xl">
                                                                        <?php echo substr(htmlspecialchars($candidate['name']), 0, 1); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <div class="font-semibold text-sm text-gray-800 flex items-center">
                                                                        <?php if($isLeading): ?>
                                                                            <span class="text-yellow-500 mr-1">★</span>
                                                                        <?php endif; ?>
                                                                        <?php echo htmlspecialchars($candidate['name']); ?>
                                                                    </div>
                                                                    <div class="text-xs text-gray-400"><?php echo htmlspecialchars($candidate['party_list'] ?? 'Ind.'); ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="font-bold text-primary text-sm"><?php echo $votes; ?></div>
                                                                <div class="text-xs text-gray-400"><?php echo $percent; ?>%</div>
                                                            </div>
                                                        </div>
                                                        <div class="w-full bg-gray-100 rounded-full h-2">
                                                            <div class="bg-secondary h-2 rounded-full transition-all duration-500" style="width: <?php echo $percent; ?>%"></div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <?php if ($isVotingOpen): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="submit_vote">
                                
                                <div class="space-y-8">
                                    <?php foreach($positions as $position): ?>
                                        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 relative">
                                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
                                                <h3 class="text-lg font-bold text-primary"><?php echo htmlspecialchars($position['name']); ?></h3>
                                                <span class="text-xs bg-secondary text-white px-2 py-1 rounded shadow-sm">
                                                    Select 1
                                                </span>
                                            </div>
                                            
                                            <div class="p-6 grid grid-cols-1 xl:grid-cols-2 gap-4">
                                                <?php 
                                                $posCandidates = $candidates[$position['id']] ?? [];
                                                if(empty($posCandidates)): 
                                                ?>
                                                    <p class="text-gray-400 italic col-span-2">No candidates for this position.</p>
                                                <?php else: ?>
                                                    <?php foreach($posCandidates as $candidate): ?>
                                                        <label class="cursor-pointer group flex h-full">
                                                            
                                                            <input type="radio" 
                                                                name="votes[<?php echo $position['id']; ?>]" 
                                                                value="<?php echo $candidate['id']; ?>" 
                                                                class="radio-card sr-only jrmsu-checkbox"
                                                                data-position="<?php echo $position['id']; ?>"
                                                                data-max="1"
                                                                required>
                                                            
                                                            <div class="w-full p-4 rounded-lg border-2 border-gray-200 group-hover:border-secondary transition-all flex items-center bg-white shadow-sm hover:shadow-md">
                                                                
                                                                <?php if (!empty($candidate['image'])): ?>
                                                                    <img src="<?php echo htmlspecialchars($candidate['image']); ?>" alt="Profile" class="h-16 w-16 md:h-20 md:w-20 rounded-full object-cover mr-4 shrink-0 border border-gray-200 group-hover:border-secondary transition-colors shadow-sm">
                                                                <?php else: ?>
                                                                    <div class="h-16 w-16 md:h-20 md:w-20 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold mr-4 shrink-0 group-hover:bg-secondary group-hover:text-white transition-colors shadow-sm text-2xl">
                                                                        <?php echo substr(htmlspecialchars($candidate['name']), 0, 1); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="font-bold text-gray-800 text-md md:text-lg truncate"><?php echo htmlspecialchars($candidate['name']); ?></div>
                                                                    <div class="text-sm text-gray-500 truncate"><?php echo htmlspecialchars($candidate['party_list'] ?? 'Independent'); ?></div>
                                                                </div>
                                                                <div class="check-icon opacity-0 transform scale-50 transition-all duration-200 text-primary ml-2 shrink-0">
                                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mt-12 flex justify-end">
                                    <button type="submit" onclick="return confirm('Are you sure you want to submit your vote? This cannot be changed.');" class="bg-gradient-to-r from-primary to-secondary text-white text-lg font-bold py-4 px-10 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-200 w-full sm:w-auto text-center">
                                        Submit Official Ballot
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="bg-white rounded-2xl shadow-xl p-8 text-center border-t-4 border-red-500 mb-8">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Voting is Unavailable</h2>
                                <p class="text-gray-600"><?php echo htmlspecialchars($votingMessage); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </main>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.jrmsu-checkbox');
        
        checkboxes.forEach(box => {
            box.addEventListener('change', function() {
                const posId = this.getAttribute('data-position');
                const maxAllowed = parseInt(this.getAttribute('data-max'), 10);
                
                const checkedBoxes = document.querySelectorAll(`.jrmsu-checkbox[data-position="${posId}"]:checked`);
                
                if (checkedBoxes.length > maxAllowed) {
                    this.checked = false; 
                    alert(`Overvoting Alert! Hanggang ${maxAllowed} lang ang pwedeng iboto para sa posisyong ito. (JRMSU Rule)`);
                }
            });
        });
    });
    </script>
    <?php endif; ?>

</body>
</html>
