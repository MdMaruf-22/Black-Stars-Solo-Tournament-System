<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Approve or reject logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $requestId = $_POST['request_id'];
    $action = $_POST['action'];

    // Fetch the request details
    $stmt = $pdo->prepare("SELECT * FROM tournament_join_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();

    if ($request && $request['status'] === 'pending') {
        if ($action === 'approve') {
            // Insert player into tournament_players
            $stmt = $pdo->prepare("INSERT IGNORE INTO tournament_players (tournament_id, player_id) VALUES (?, ?)");
            $stmt->execute([$request['tournament_id'], $request['player_id']]);

            // Update request status
            $stmt = $pdo->prepare("UPDATE tournament_join_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$requestId]);
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE tournament_join_requests SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$requestId]);
        }
    }

    // Redirect to avoid resubmission
    header("Location: admin_tournament_requests.php");
    exit;
}

// Fetch pending requests
$stmt = $pdo->query("
    SELECT r.id, r.fee_code, r.requested_at, 
           p.username AS player_name, 
           t.name AS tournament_name
    FROM tournament_join_requests r
    JOIN users p ON r.player_id = p.id
    JOIN tournaments t ON r.tournament_id = t.id
    WHERE r.status = 'pending'
    ORDER BY r.requested_at ASC
");
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Join Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">⏳ Pending Tournament Join Requests</h1>

        <?php if (count($requests) > 0): ?>
            <table class="w-full text-sm table-auto border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">Player</th>
                        <th class="px-4 py-2">Tournament</th>
                        <th class="px-4 py-2">Last Three Digits</th>
                        <th class="px-4 py-2">Requested At</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($r['player_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($r['tournament_name']); ?></td>
                            <td class="px-4 py-2 font-mono text-blue-600"><?php echo htmlspecialchars($r['fee_code']); ?></td>
                            <td class="px-4 py-2"><?php echo $r['requested_at']; ?></td>
                            <td class="px-4 py-2 space-x-2">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600" onclick="return confirm('Approve this request?')">Approve</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Reject this request?')">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-gray-600">No pending requests at the moment.</div>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="tournaments_list.php" class="text-blue-600 hover:underline">⬅ Back to Tournament List</a>
        </div>
    </div>
</body>
</html>
